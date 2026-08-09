import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: ['selector', '[data-theme="dark"]'],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                sidebar: {
                    bg:     '#0d4f1e',
                    brand:  '#0a3d18',
                    hover:  'rgba(255,255,255,0.12)',
                    active: 'rgba(255,255,255,0.20)',
                    text:   '#ffffff',
                    icon:   '#a7f3d0',
                },
            },
        },
    },

    plugins: [forms, typography],
};
