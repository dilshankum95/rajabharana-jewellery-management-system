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
                sans: ['DM Sans', 'Lato', ...defaultTheme.fontFamily.sans],
                display: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                jewel: {
                    gold: '#E9B44C',
                    'gold-light': '#FDF3DC',
                    'gold-dark': '#C8943A',
                    rose: '#E07A5F',
                    'rose-dark': '#C65D42',
                    'rose-light': '#FCEEEA',
                    dark: '#0F172A',
                    navy: '#1E293B',
                    charcoal: '#64748B',
                    cream: '#F8FAFC',
                    ivory: '#F1F5F9',
                    burgundy: '#BE185D',
                    muted: '#94A3B8',
                    teal: '#2DD4BF',
                },
            },
            backgroundImage: {
                'jewel-gradient': 'linear-gradient(135deg, #0F172A 0%, #1E293B 45%, #334155 100%)',
                'jewel-hero': 'linear-gradient(135deg, #0F172A 0%, #1E293B 60%, #0F172A 100%)',
                'gold-shimmer': 'linear-gradient(90deg, #C8943A 0%, #F5D485 50%, #E9B44C 100%)',
                'btn-gradient': 'linear-gradient(135deg, #E07A5F 0%, #E9B44C 100%)',
                'btn-gradient-hover': 'linear-gradient(135deg, #C65D42 0%, #C8943A 100%)',
                'cream-gradient': 'linear-gradient(180deg, #F8FAFC 0%, #EEF2FF 100%)',
            },
            boxShadow: {
                jewel: '0 4px 20px -4px rgba(224, 122, 95, 0.15)',
                'jewel-lg': '0 8px 32px -8px rgba(15, 23, 42, 0.12)',
                glow: '0 0 24px -4px rgba(233, 180, 76, 0.35)',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out',
                'slide-up': 'slideUp 0.45s ease-out',
                'shimmer': 'shimmer 3s ease-in-out infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                shimmer: {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                },
            },
        },
    },

    plugins: [forms],
};
