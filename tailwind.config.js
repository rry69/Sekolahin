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
            colors: {
                eggplore: {
                    primary: {
                        DEFAULT: '#6C78F5',
                        50:  '#F1F3FE',
                        100: '#E5E9FC',
                        200: '#C9CEF3',
                        400: '#8B96F5',
                        450: '#7482F2',
                        500: '#6C78F5',
                        600: '#5A64E8',
                        700: '#4A54C9',
                    },
                    success: { DEFAULT: '#2DC99C', soft: '#E1F5F1' },
                    danger:  { DEFAULT: '#F27389', soft: '#FEEBEE' },
                    warning: { DEFAULT: '#F5CC66', soft: '#FBF3D9' },
                    info:    { DEFAULT: '#248FE6', soft: '#E3F0FC' },
                    neutral: {
                        50:  '#F8F9FB',
                        100: '#F5F6FA',
                        150: '#EEF0F4',
                        200: '#E0E2E8',
                        300: '#C9CDD6',
                        400: '#9AA0AE',
                        500: '#6B7280',
                        700: '#3F4451',
                        900: '#1A1A2E',
                    },
                },
            },
            borderRadius: {
                'btn': '6px',
                'input': '4px',
                'card': '8px',
            },
            boxShadow: {
                xs: '0 1px 2px rgba(16,24,40,.06)',
            },
        },
    },
    plugins: [forms],
};
