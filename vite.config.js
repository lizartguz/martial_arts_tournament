import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/auth-common.css',
                'resources/css/auth-buttons.css',
                'resources/css/auth-forms.css',
                'resources/css/auth-alerts.css',
                'resources/js/app.js',
                'resources/js/analytics.js',
                'resources/js/push-notifications.js'
            ],
            refresh: true,
        }),
    ],
});
