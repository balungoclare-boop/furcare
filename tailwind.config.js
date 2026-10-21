import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                indigo: {
                    50: '#efedf8',
                    100: '#dad7f0',
                    200: '#b7b1e1',
                    300: '#8b81cf',
                    400: '#685cbc',
                    500: '#4d439f',
                    600: '#3B3281',
                    700: '#322b6c',
                    800: '#2b2459',
                    900: '#201d47',
                },
                jade: {
                    50: '#edfdf9',
                    100: '#d6f9f0',
                    200: '#adefdc',
                    300: '#78e0c9',
                    400: '#3fc8af',
                    500: '#02BF95',
                    600: '#00aa83',
                    700: '#008a6b',
                    800: '#006b52',
                    900: '#004f3d',
                },
            },
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
