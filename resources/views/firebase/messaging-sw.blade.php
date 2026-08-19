// Activa inmediatamente una nueva version del worker sin esperar a que se cierren todas las pestanas.
self.addEventListener('install', (event) => {
    event.waitUntil(self.skipWaiting());
});

// Hace que la version recien activada controle las pestanas abiertas del sistema.
self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

const firebaseConfig = {
    apiKey: @json(config('services.firebase.web.api_key')),
    authDomain: @json(config('services.firebase.web.auth_domain')),
    projectId: @json(config('services.firebase.web.project_id')),
    storageBucket: @json(config('services.firebase.web.storage_bucket')),
    messagingSenderId: @json(config('services.firebase.web.messaging_sender_id')),
    appId: @json(config('services.firebase.web.app_id')),
};

const hasFirebaseMessagingConfig = Object.values(firebaseConfig).every(Boolean);

if (hasFirebaseMessagingConfig) {
    importScripts('https://www.gstatic.com/firebasejs/12.14.0/firebase-app-compat.js');
    importScripts('https://www.gstatic.com/firebasejs/12.14.0/firebase-messaging-compat.js');

    firebase.initializeApp(firebaseConfig);

    const messaging = firebase.messaging();

    // Escucha mensajes cuando la pagina esta cerrada, oculta o en segundo plano.
    messaging.onBackgroundMessage((payload) => {
        const notification = payload?.notification ?? {};
        const title = notification.title || 'Nueva notificación';
        const options = {
            body: notification.body || '',
            icon: notification.icon || '{{ asset('images/mma/brand/combate-real-icon.png') }}',
            data: {
                link:
                    payload?.fcmOptions?.link
                    || payload?.data?.link
                    || '/',
            },
        };

        // Solicita al sistema operativo que muestre la notificacion recibida.
        self.registration.showNotification(title, options);
    });

    // Escucha el clic del usuario sobre una notificacion mostrada por el service worker.
    self.addEventListener('notificationclick', (event) => {
        event.notification.close();

        const link = event.notification?.data?.link || '/';

        // Mantiene activo el service worker hasta enfocar una ventana o abrir una nueva.
        event.waitUntil(
            clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
                for (const client of clientList) {
                    if ('focus' in client) {
                        // Reutiliza una ventana existente de la plataforma y navega al enlace.
                        client.navigate(link);
                        return client.focus();
                    }
                }

                if (clients.openWindow) {
                    // Abre una ventana nueva cuando la plataforma no esta abierta.
                    return clients.openWindow(link);
                }

                return null;
            })
        );
    });
}
