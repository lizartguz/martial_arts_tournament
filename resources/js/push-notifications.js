import { getMessaging, getToken, isSupported, onMessage } from 'firebase/messaging';
import { firebaseApp, hasFirebaseConfig } from './firebase-app';

const vapidKey = import.meta.env.VITE_FIREBASE_VAPID_KEY;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const authenticatedUserId = document.querySelector('meta[name="authenticated-user-id"]')?.getAttribute('content') || '';
const savedTokenKey = 'environmental_web_push_token';
const savedUserIdKey = 'environmental_web_push_user_id';
const lastTokenSyncKey = 'environmental_web_push_last_sync_at';
const tokenSyncInterval = 6 * 60 * 60 * 1000;
const permissionTimeout = 30000;
const serviceWorkerTimeout = 15000;
const firebaseTokenTimeout = 25000;
const backendTimeout = 15000;
let foregroundListenerRegistered = false;
let activationInProgress = false;

// Estados visuales de la campana de Firebase Web Push:
// gris pendiente, azul procesando, verde activo, amarillo por sincronizar y rojo bloqueado.
const bellStates = {
    pending: {
        icon: 'fas fa-bell',
        color: 'text-secondary',
        label: 'Activar notificaciones',
    },
    processing: {
        icon: 'fas fa-circle-notch fa-spin',
        color: 'text-info',
        label: 'Activando notificaciones...',
    },
    active: {
        icon: 'fas fa-bell',
        color: 'text-success',
        label: 'Notificaciones activadas',
    },
    synchronized: {
        icon: 'fas fa-check-circle',
        color: 'text-success',
        label: 'Dispositivo sincronizado correctamente',
    },
    warning: {
        icon: 'fas fa-exclamation-triangle',
        color: 'text-warning',
        label: 'Permiso concedido. Pulse para completar el registro.',
    },
    blocked: {
        icon: 'fas fa-bell-slash',
        color: 'text-danger',
        label: 'Notificaciones bloqueadas por el navegador',
    },
};

// Evita que una operacion externa deje la campana esperando indefinidamente.
async function withTimeout(promise, timeout, message) {
    let timeoutId;

    try {
        return await Promise.race([
            promise,
            new Promise((resolve, reject) => {
                timeoutId = window.setTimeout(() => {
                    const error = new Error(message);
                    error.name = 'WebPushTimeoutError';
                    reject(error);
                }, timeout);
            }),
        ]);
    } finally {
        window.clearTimeout(timeoutId);
    }
}

// Localiza el control de Firebase Web Push generado por AdminLTE en la barra superior.
function getBellElements() {
    const configuredElement = document.getElementById('web-push-toggle');
    const button = configuredElement?.matches('a')
        ? configuredElement
        : configuredElement?.querySelector('a')
            || document.querySelector('a.web-push-navbar-link, .web-push-navbar-link a');
    const container = button?.closest('li') || configuredElement;
    const icon = button?.querySelector('i');

    return { container, button, icon };
}

// Actualiza icono, color y ayuda accesible de la campana segun el estado de Web Push.
function setBellState(state, customLabel = null) {
    const { button, icon } = getBellElements();
    const definition = bellStates[state] || bellStates.warning;
    const label = customLabel || definition.label;

    if (!button || !icon) {
        return;
    }

    icon.className = `${definition.icon} ${definition.color}`;
    button.title = label;
    button.setAttribute('aria-label', label);
    button.setAttribute('aria-busy', state === 'processing' ? 'true' : 'false');
    button.classList.toggle('disabled', state === 'processing');
}

// Detecta Brave para explicar correctamente los errores propios de su servicio Push.
async function isBraveBrowser() {
    return Boolean(navigator.brave && await navigator.brave.isBrave?.());
}

// Resume el User-Agent a un nombre y version utiles para el registro.
async function getBrowserDescription() {
    const userAgent = navigator.userAgent || '';
    const browserMatchers = [
        { pattern: /Edg\/([\d.]+)/, name: 'Microsoft Edge' },
        { pattern: /OPR\/([\d.]+)/, name: 'Opera' },
        { pattern: /Firefox\/([\d.]+)/, name: 'Mozilla Firefox' },
        { pattern: /Chrome\/([\d.]+)/, name: 'Google Chrome' },
        { pattern: /Version\/([\d.]+).*Safari/, name: 'Safari' },
    ];

    if (await isBraveBrowser().catch(() => false)) {
        const chromiumVersion = userAgent.match(/Chrome\/([\d.]+)/)?.[1];

        return chromiumVersion ? `Brave ${chromiumVersion}` : 'Brave';
    }

    for (const browser of browserMatchers) {
        const version = userAgent.match(browser.pattern)?.[1];

        if (version) {
            return `${browser.name} ${version}`;
        }
    }

    return 'Navegador web';
}

// Ejecuta peticiones autenticadas al backend y valida su respuesta JSON.
async function requestJson(url, options) {
    const response = await withTimeout(
        fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                ...(options.headers || {}),
            },
            credentials: 'same-origin',
        }),
        backendTimeout,
        'El servidor no respondio a tiempo.'
    );
    const payload = await response.json().catch(() => null);

    if (!response.ok || payload?.success !== true) {
        const message = payload?.message || `Laravel respondio con estado ${response.status}`;
        throw new Error(message);
    }

    return payload;
}

// Registra el token web en Laravel y confirma que el servidor lo haya persistido.
async function persistWebToken(token) {
    const browser = await getBrowserDescription();

    return requestJson('/firebase/web-push/token', {
        method: 'POST',
        body: JSON.stringify({
            token,
            browser,
            device_name: `${navigator.platform || 'Web'} / ${navigator.language || 'es'}`,
            device_identifier: window.location.host,
        }),
    });
}

// Consulta si el token guardado localmente sigue activo para la sesion actual.
async function checkStoredTokenStatus(token) {
    return requestJson('/firebase/web-push/token/status', {
        method: 'POST',
        body: JSON.stringify({ token }),
    });
}

// Desactiva el token cuando el navegador informa que el permiso fue revocado.
async function unregisterStoredToken() {
    const savedToken = window.localStorage.getItem(savedTokenKey);

    if (savedToken) {
        await requestJson('/firebase/web-push/token', {
            method: 'DELETE',
            body: JSON.stringify({ token: savedToken }),
        }).catch(() => {});
    }

    window.localStorage.removeItem(savedTokenKey);
    window.localStorage.removeItem(savedUserIdKey);
    window.localStorage.removeItem(lastTokenSyncKey);
}

// Muestra una notificacion nativa cuando Firebase entrega un mensaje en primer plano.
function showForegroundNotification(payload) {
    if (Notification.permission !== 'granted') {
        return;
    }

    const notification = payload?.notification ?? {};
    const link = payload?.fcmOptions?.link || payload?.data?.link || '/';
    const browserNotification = new Notification(notification.title || 'Nueva notificacion', {
        body: notification.body || '',
        icon: notification.icon || '/frontend/images/logo_with_text.png',
    });

    browserNotification.onclick = () => {
        window.focus();
        window.location.href = link;
        browserNotification.close();
    };
}

// Registra una sola escucha de mensajes cuando el usuario ya concedio permiso.
async function ensureForegroundListener() {
    if (foregroundListenerRegistered || Notification.permission !== 'granted') {
        return;
    }

    const supported = await isSupported().catch(() => false);

    if (!supported) {
        return;
    }

    foregroundListenerRegistered = true;
    onMessage(getMessaging(firebaseApp), showForegroundNotification);
}

// Genera y registra el token FCM exclusivamente despues de una accion del usuario.
async function registerCurrentBrowser(onProgress = () => {}) {
    if (!hasFirebaseConfig || !firebaseApp || !vapidKey) {
        throw new Error('Firebase Web Push no esta configurado completamente.');
    }

    if (!authenticatedUserId || !csrfToken || !('serviceWorker' in navigator) || !('Notification' in window)) {
        throw new Error('Este navegador o la sesion actual no permiten activar Web Push.');
    }

    onProgress('Comprobando compatibilidad...');
    const supported = await withTimeout(
        isSupported().catch(() => false),
        5000,
        'El navegador no respondio al comprobar la compatibilidad Web Push.'
    );

    if (!supported) {
        throw new Error('Este navegador no es compatible con Firebase Web Push.');
    }

    onProgress('Preparando el navegador...');
    await withTimeout(
        navigator.serviceWorker.register('/firebase-messaging-sw.js', {
            updateViaCache: 'none',
        }),
        serviceWorkerTimeout,
        'No se pudo preparar el servicio de notificaciones.'
    );
    const registration = await withTimeout(
        navigator.serviceWorker.ready,
        serviceWorkerTimeout,
        'El servicio de notificaciones no termino de iniciar.'
    );

    onProgress('Conectando con Firebase...');
    const token = await withTimeout(
        getToken(getMessaging(firebaseApp), {
            vapidKey,
            serviceWorkerRegistration: registration,
        }),
        firebaseTokenTimeout,
        'Firebase no devolvio el token del navegador a tiempo.'
    );

    if (!token) {
        throw new Error('Firebase no devolvio un token para este navegador.');
    }

    onProgress('Registrando dispositivo...');
    await persistWebToken(token);
    window.localStorage.setItem(savedTokenKey, token);
    window.localStorage.setItem(savedUserIdKey, authenticatedUserId);
    window.localStorage.setItem(lastTokenSyncKey, String(Date.now()));
    await ensureForegroundListener();
}

// Explica errores mediante mensajes nativos y mantiene disponible el reintento.
async function handleWebPushError(error) {
    const braveBrowser = await isBraveBrowser().catch(() => false);
    const pushServiceFailure = error?.name === 'AbortError'
        && String(error?.message || '').toLowerCase().includes('push service error');

    setBellState('warning', 'No se pudo completar el registro. Pulse para reintentar.');

    if (braveBrowser && pushServiceFailure) {
        window.alert(
            'Brave tiene bloqueado el servicio Push. Abra brave://settings/privacy, active '
            + '"Usar los servicios de Google para los mensajes push", reinicie Brave y vuelva a intentarlo.'
        );

        return;
    }

    window.alert(`No se pudo activar Web Push: ${error?.message || 'Error desconocido.'}`);
    console.warn('Firebase Web Push could not be initialized.', error);
}

// Al pulsar la campana, solicita permiso si hace falta y registra o resincroniza el token FCM.
async function activateWebPush(event) {
    event.preventDefault();

    if (activationInProgress) {
        return;
    }

    if (!('Notification' in window) || !('serviceWorker' in navigator)) {
        setBellState('warning', 'Web Push no esta disponible en este navegador.');
        window.alert('Este navegador no permite activar notificaciones Web Push.');
        return;
    }

    if (Notification.permission === 'denied') {
        setBellState('blocked');
        window.alert(
            'Las notificaciones estan bloqueadas. Habilitelas desde el candado de la barra de direcciones '
            + 'y vuelva a pulsar la campana.'
        );

        return;
    }

    activationInProgress = true;
    setBellState('processing', 'Esperando permiso del navegador...');

    try {
        if (Notification.permission === 'default') {
            const permission = await withTimeout(
                Notification.requestPermission(),
                permissionTimeout,
                'El navegador no recibio una respuesta al solicitar el permiso.'
            );

            if (permission !== 'granted') {
                setBellState(permission === 'denied' ? 'blocked' : 'pending');
                return;
            }
        }

        await registerCurrentBrowser((message) => setBellState('processing', message));
        setBellState('synchronized');
        window.setTimeout(() => setBellState('active'), 1800);
    } catch (error) {
        await handleWebPushError(error);
    } finally {
        activationInProgress = false;
    }
}

// Consulta permiso, token local y confirmacion de Laravel para decidir el color inicial.
// Esta comprobacion nunca solicita permisos automaticamente al usuario.
async function refreshBellState() {
    if (!('Notification' in window) || !('serviceWorker' in navigator) || !hasFirebaseConfig || !firebaseApp || !vapidKey) {
        setBellState('warning', 'Web Push no esta disponible en este navegador.');
        return;
    }

    if (Notification.permission === 'denied') {
        await unregisterStoredToken();
        setBellState('blocked');
        return;
    }

    if (Notification.permission === 'default') {
        setBellState('pending');
        return;
    }

    await ensureForegroundListener();

    const savedToken = window.localStorage.getItem(savedTokenKey);
    const savedUserId = window.localStorage.getItem(savedUserIdKey);

    if (!savedToken || savedUserId !== authenticatedUserId) {
        setBellState('warning');
        return;
    }

    try {
        const status = await checkStoredTokenStatus(savedToken);
        const lastSyncAt = Number(window.localStorage.getItem(lastTokenSyncKey) || 0);
        const synchronizationExpired = Date.now() - lastSyncAt >= tokenSyncInterval;

        setBellState(status.registered && !synchronizationExpired ? 'active' : 'warning');
    } catch (error) {
        setBellState('warning', 'No se pudo comprobar el registro. Pulse para reintentar.');
        console.warn('No se pudo comprobar el estado de Firebase Web Push.', error);
    }
}

// Conecta una sola vez el clic de la campana y actualiza su estado al cargar la interfaz.
function bootWebPushBell() {
    const { button } = getBellElements();

    if (!button || !authenticatedUserId) {
        return;
    }

    button.setAttribute('href', '#');
    button.setAttribute('role', 'button');

    if (button.dataset.webPushInitialized !== 'true') {
        button.dataset.webPushInitialized = 'true';
        button.addEventListener('click', activateWebPush);
    }

    refreshBellState();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootWebPushBell, { once: true });
} else {
    bootWebPushBell();
}

// Recupera la campana cuando Livewire reemplaza el menu o el navegador restaura la pagina.
document.addEventListener('livewire:navigated', bootWebPushBell);
window.addEventListener('pageshow', bootWebPushBell);
