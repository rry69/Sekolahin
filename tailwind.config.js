import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    safelist: [
        'dp-mono',
        'dp-picker',
        'dp-animate-in',
        'dp-animate-out',
        'dp-grid-animate',
        'dp-header-animate',
        'dp-pop-select',
        'dp-chevron',
        'dp-chevron-open',
        'native-date-hidden',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [forms],
};
