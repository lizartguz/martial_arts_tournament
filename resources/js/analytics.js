import { getAnalytics, isSupported, logEvent } from 'firebase/analytics';
import { firebaseApp, hasAnalyticsConfig } from './firebase-app';
let analyticsInstance = null;
let sidebarTrackingInitialized = false;

// Envía un evento a Firebase Analytics solo cuando el SDK ya está listo.
function trackAnalyticsEvent(eventName, params = {}) {
    if (!analyticsInstance) {
        return;
    }

    logEvent(analyticsInstance, eventName, params);
}

// Limpia el texto del menú para que los reportes usen etiquetas legibles.
function normalizeMenuLabel(text) {
    return text.replace(/\s+/g, ' ').trim();
}

// Captura clics del menú lateral de AdminLTE para medir qué módulos usan más los usuarios.
function trackSidebarMenuClicks() {
    if (sidebarTrackingInitialized) {
        return;
    }

    sidebarTrackingInitialized = true;

    document.addEventListener('click', (event) => {
        const sidebarLink = event.target.closest('.main-sidebar .nav-link');

        if (!sidebarLink) {
            return;
        }

        const menuLabel = normalizeMenuLabel(sidebarLink.textContent || '');
        const menuHref = sidebarLink.getAttribute('href') || '';
        const isSubmenuToggle = sidebarLink.classList.contains('nav-dropdown-toggle')
            || menuHref === '#';

        const treeview = sidebarLink.closest('.nav-treeview');
        const parentLink = treeview
            ? treeview.closest('.nav-item')?.querySelector(':scope > .nav-link')
            : null;
        const parentLabel = parentLink
            ? normalizeMenuLabel(parentLink.textContent || '')
            : '';

        trackAnalyticsEvent('sidebar_menu_click', {
            menu_label: menuLabel || 'unknown',
            menu_href: menuHref || 'no_href',
            parent_label: parentLabel || 'root',
            interaction_type: isSubmenuToggle ? 'toggle' : 'navigation',
        });
    });
}

// Inicializa Analytics al final de la carga para no competir con scripts heredados.
function initAnalytics() {
    trackSidebarMenuClicks();

    if (!hasAnalyticsConfig) {
        console.warn('Firebase Analytics was skipped because VITE_FIREBASE_* variables are missing.');
        return;
    }

    isSupported()
        .then((supported) => {
            if (!supported) {
                return;
            }

            analyticsInstance = getAnalytics(firebaseApp);
            window.appAnalytics = analyticsInstance;
            trackAnalyticsEvent('app_open');
        })
        .catch((error) => {
            console.warn('Firebase Analytics could not be initialized.', error);
        });
}

if (document.readyState === 'complete') {
    initAnalytics();
} else {
    window.addEventListener('load', initAnalytics, { once: true });
}
