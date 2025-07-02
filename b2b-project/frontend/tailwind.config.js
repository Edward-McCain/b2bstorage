/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    fontFamily: {
      sans: ['"Nunito Sans"', 'sans-serif'],
    },
    extend: {
      colors: {
        primary: '#1677ff',
      },
    },
  },
  plugins: [],
} 