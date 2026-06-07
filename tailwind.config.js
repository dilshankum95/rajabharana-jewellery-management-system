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
            fontFamily: {
                sans: ['Lato', ...defaultTheme.fontFamily.sans],
                display: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                jewel: {
                    gold: '#C5A059',
                    'gold-light': '#E8D5A3',
                    'gold-dark': '#9A7B3A',
                    dark: '#1A1814',
                    charcoal: '#2D2A26',
                    cream: '#FAF7F2',
                    ivory: '#F5F0E8',
                    burgundy: '#722F37',
                },
            },
            backgroundImage: {
                'jewel-gradient': 'linear-gradient(135deg, #1A1814 0%, #2D2A26 50%, #1A1814 100%)',
                'gold-shimmer': 'linear-gradient(90deg, #9A7B3A 0%, #E8D5A3 50%, #C5A059 100%)',
            },
        },
    },

    plugins: [forms],
};
