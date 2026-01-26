import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import daisyui from 'daisyui';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./app/Models/*.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#ecf3ff',
                    100: '#dde9ff',
                    200: '#c2d6ff',
                    300: '#9cb9ff',
                    400: '#7592ff',
                    500: '#465fff',
                    600: '#3641f5',
                    700: '#2a31d8',
                    800: '#252dae',
                    900: '#262e89',
                },
                gray: {
                    dark: '#1a2231',
                },
                success: {
                    500: '#12b76a',
                },
                error: {
                    500: '#f04438',
                },
            },
            spacing: {
                '4.5': '1.125rem',
                '5.5': '1.375rem',
                '6.5': '1.625rem',
                '7.5': '1.875rem',
                '8.5': '2.125rem',
                '11.5': '2.875rem',
                '62.5': '15.625rem',
                '72.5': '18.125rem',
            },
            zIndex: {
                '1': '1',
                '999': '999',
                '9999': '9999',
                '99999': '99999',
            },
        },
    },

    plugins: [forms, daisyui],
    
    daisyui: {
        styled: true,
        themes: false,
        base: false,
        utils: true,
        logs: false,
    },
};
