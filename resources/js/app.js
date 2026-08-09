import './bootstrap';
import Swal from 'sweetalert2';
import * as luxon from 'luxon';
import SignaturePad from 'signature_pad';
import Chart from 'chart.js/auto';
import 'steelseries';

window.Swal = Swal;
window.luxon = luxon;
window.SignaturePad = SignaturePad;
window.Chart = Chart;

const injectAppAlertStyles = () => {
    if (document.getElementById('app-alert-styles')) {
        return;
    }

    const style = document.createElement('style');
    style.id = 'app-alert-styles';
    style.textContent = `
        .swal2-popup {
            width: min(360px, calc(100vw - 2rem)) !important;
            padding: 1rem 1rem 1.125rem !important;
            border-radius: 0.5rem !important;
        }

        /*
         * El icono de SweetAlert2 (incluido el check de "success", que se dibuja
         * con un anillo y lineas posicionadas en em) debe escalarse con font-size,
         * no con width/height absolutos, para que su geometria interna no se rompa.
         */
        .swal2-icon {
            font-size: 0.6rem !important;
            margin: 0.75rem auto 1rem !important;
        }

        .swal2-title {
            padding: 0 !important;
            font-size: 1.05rem !important;
            font-weight: 700 !important;
            line-height: 1.35 !important;
        }

        .swal2-html-container {
            margin: 0.5rem 0 0 !important;
            padding: 0 !important;
            font-size: 0.875rem !important;
            line-height: 1.45 !important;
        }

        .swal2-actions {
            margin: 1rem auto 0 !important;
        }

        .swal2-styled.swal2-confirm,
        .swal2-styled.swal2-cancel,
        .swal2-styled.swal2-deny {
            min-width: 5rem !important;
            padding: 0.5rem 0.875rem !important;
            border-radius: 0.375rem !important;
            font-size: 0.875rem !important;
            font-weight: 700 !important;
        }

        .app-alert-meta {
            margin-top: 0.625rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            background: #f8fafc;
            color: #475569;
            text-align: left;
            font-size: 0.75rem;
        }

        .app-alert-meta-row {
            display: flex;
            gap: 0.375rem;
            justify-content: space-between;
        }
    `;
    document.head.appendChild(style);
};

const escapeHtml = (value) => String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

const alertColors = {
    success: '#16a34a',
    error: '#dc2626',
    warning: '#d97706',
    info: '#2563eb',
};

const buildAlertHtml = (description, metadata) => {
    const parts = [];

    if (description) {
        parts.push(`<div>${escapeHtml(description)}</div>`);
    }

    if (metadata && typeof metadata === 'object' && Object.keys(metadata).length > 0) {
        const rows = Object.entries(metadata)
            .filter(([, value]) => value !== null && value !== undefined && value !== '')
            .map(([key, value]) => `
                <div class="app-alert-meta-row">
                    <strong>${escapeHtml(key)}</strong>
                    <span>${escapeHtml(value)}</span>
                </div>
            `)
            .join('');

        if (rows) {
            parts.push(`<div class="app-alert-meta">${rows}</div>`);
        }
    }

    return parts.join('');
};

const normalizeAlertPayload = (payload, variant = 'info') => {
    const data = Array.isArray(payload) ? payload[0] : payload;

    if (typeof data === 'string') {
        return { title: data, type: variant };
    }

    const title = data?.title
        ?? data?.message
        ?? data?.success
        ?? data?.failed
        ?? data?.warning
        ?? data?.info
        ?? '';

    return {
        ...data,
        title,
        type: data?.type ?? variant,
        description: data?.description ?? data?.text ?? data?.body,
        metadata: data?.metadata ?? data?.meta,
    };
};

const fireAppAlert = (payload, variant = 'info') => {
    injectAppAlertStyles();

    const options = normalizeAlertPayload(payload, variant);

    if (!options.title) {
        throw new Error('AppAlert requires a title.');
    }

    const type = options.type === 'failed' ? 'error' : options.type;
    const icon = ['success', 'error', 'warning', 'info', 'question'].includes(type) ? type : variant;
    const html = buildAlertHtml(options.description, options.metadata);

    return Swal.fire({
        icon,
        title: escapeHtml(options.title),
        html: html || undefined,
        width: options.width ?? 360,
        confirmButtonColor: options.confirmButtonColor ?? alertColors[icon] ?? alertColors.info,
        confirmButtonText: options.confirmButtonText ?? window.AppAlertDefaults?.confirmButtonText ?? 'Cerrar',
        showConfirmButton: options.showConfirmButton ?? true,
        timer: options.timer,
        timerProgressBar: options.timerProgressBar ?? Boolean(options.timer),
        allowOutsideClick: options.allowOutsideClick ?? true,
        allowEscapeKey: options.allowEscapeKey ?? true,
        customClass: {
            popup: 'app-alert-popup',
            title: 'app-alert-title',
            htmlContainer: 'app-alert-description',
            confirmButton: 'app-alert-confirm',
            ...(options.customClass ?? {}),
        },
        ...options.swal,
    });
};

window.AppAlert = {
    fire: fireAppAlert,
    success: (title, description = null, metadata = null, options = {}) => fireAppAlert({ title, description, metadata, ...options }, 'success'),
    error: (title, description = null, metadata = null, options = {}) => fireAppAlert({ title, description, metadata, ...options }, 'error'),
    warning: (title, description = null, metadata = null, options = {}) => fireAppAlert({ title, description, metadata, ...options }, 'warning'),
    info: (title, description = null, metadata = null, options = {}) => fireAppAlert({ title, description, metadata, ...options }, 'info'),
};

const registerLivewireAlerts = () => {
    if (!window.Livewire || window.__appAlertLivewireRegistered) {
        return;
    }

    window.__appAlertLivewireRegistered = true;

    window.Livewire.on('successAlert', (event) => window.AppAlert.fire(event, 'success'));
    window.Livewire.on('failedAlert', (event) => window.AppAlert.fire(event, 'error'));
    window.Livewire.on('warningAlert', (event) => window.AppAlert.fire(event, 'warning'));
    window.Livewire.on('infoAlert', (event) => window.AppAlert.fire(event, 'info'));
};

injectAppAlertStyles();
document.addEventListener('livewire:init', registerLivewireAlerts);
document.addEventListener('livewire:load', registerLivewireAlerts);
document.addEventListener('DOMContentLoaded', registerLivewireAlerts);

document.addEventListener('DOMContentLoaded', () => {
    const compass = document.querySelector('steelseries-compass');
    if (compass) {
        compass.setAttribute('value', 75);
    }
});
