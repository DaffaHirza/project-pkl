import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#FFF4E5',
                    100: '#FFE4C2',
                    200: '#FFC98A',
                    300: '#F5AD55',
                    400: '#EE9B36',
                    500: '#EA8F24',
                    600: '#D57F20',
                    700: '#B66C1B',
                    800: '#8F5515',
                    900: '#6B3F0F',
                },
                ocean: {
                    50: '#E6F1F4',
                    100: '#CBE1E7',
                    200: '#9FC7D1',
                    300: '#72ADBA',
                    400: '#3D8AA0',
                    500: '#014E62',
                    600: '#014553',
                    700: '#013A46',
                    800: '#012F38',
                    900: '#01242B',
                },
                charcoal: '#111111',
                gray: {
                    dark: '#014E62',
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

    plugins: [forms],
};
