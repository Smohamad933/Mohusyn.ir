/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './**/*.{ts,tsx}', '!./node_modules/**'],
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: {
        sans: ['Poppins', 'sans-serif'],
        signature: ['Dancing Script', 'cursive'],
        bengali: ['Poppins', 'sans-serif'],
      },
      colors: {
        primary: '#2563EB', secondary: '#1E293B', accent: '#3B82F6', dark: '#000000',
        'color-1': 'hsl(var(--color-1))', 'color-2': 'hsl(var(--color-2))', 'color-3': 'hsl(var(--color-3))', 'color-4': 'hsl(var(--color-4))', 'color-5': 'hsl(var(--color-5))',
      },
      animation: {
        marquee: 'marquee 25s linear infinite',
        cursor: 'cursor .75s step-end infinite',
        spotlight: 'spotlight 2s ease .75s 1 forwards',
        'spin-slow': 'spin 3s linear infinite',
        'bounce-slow': 'bounce 3s infinite',
        rainbow: 'rainbow var(--speed, 2s) infinite linear',
      },
      keyframes: {
        marquee: { '0%': { transform: 'translateX(0)' }, '100%': { transform: 'translateX(-50%)' } },
        cursor: { '0%, 100%': { opacity: '1' }, '50%': { opacity: '0' } },
        rainbow: { '0%': { 'background-position': '0%' }, '100%': { 'background-position': '200%' } },
        spotlight: { '0%': { opacity: '0', transform: 'translate(-72%, -62%) scale(0.5)' }, '100%': { opacity: '1', transform: 'translate(-50%,-40%) scale(1)' } },
      },
    },
  },
  plugins: [],
};
