/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./index.html",
    "./src/*.{js,ts,jsx,tsx}",
    "./src/**/*.{js,ts,jsx,tsx}",
    "./src/**/**/*.{js,ts,jsx,tsx}",
    "./src/**/**/**/*.{js,ts,jsx,tsx}",
    "./src/**/**/**/**/*.{js,ts,jsx,tsx}",
    "./src/**/**/**/**/**/*.{js,ts,jsx,tsx}",
    "./src/**/**/**/**/**/**/*.{js,ts,jsx,tsx}",
    "./src/**/**/**/**/**/**/**/*.{js,ts,jsx,tsx}",
    "./src/**/**/**/**/**/**/**/**/*.{js,ts,jsx,tsx}",
  ],

  theme: {
    extend: {
      colors: {
        softYellow: {
          500: 'rgba(250,236,188,0.87)',
        },
        primary: '#704425',
        onPrimary: '#ffe7d1',
        complementary: '#648ca9',
        analogous1: '#a9646a',
        analogous2: '#a9a364',
        triadic1: '#8ca964',
        triadic2: '#64a981',
      }
    },
  },
  plugins: [],
}
