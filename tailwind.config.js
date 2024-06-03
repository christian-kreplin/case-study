/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.css",
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: {
          10: '#FFFBFE',
          50: '#F6EDFF',
          100: '#EADDFF',
          200: '#D0BCFF',
          300: '#B69DF8',
          400: '#9A82DB',
          500: '#7F67BE',
          600: '#6750A4',
          700: '#4F378B',
          800: '#381E72',
          900: '#21005D',
        },
        secondary: {
          10: '#FFFBFE',
          50: '#F6EDFF',
          100: '#E8DEF8',
          200: '#CCC2DC',
          300: '#B0A7C0',
          400: '#958DA5',
          500: '#7A7289',
          600: '#625B71',
          700: '#4A4458',
          800: '#332D41',
          900: '#1D192B',
        },
        tertiary: {
          10: '#FFFBFA',
          50: '#FFECF1',
          100: '#FFD8E4',
          200: '#EFB8C8',
          300: '#D29DAC',
          400: '#B58392',
          500: '#986977',
          600: '#7D5260',
          700: '#633B48',
          800: '#492532',
          900: '#31111D',
        },
        error: {
          10: '#FFFBF9',
          50: '#FCEEEE',
          100: '#F9DEDC',
          200: '#F2B8B5',
          300: '#EC928E',
          400: '#E46962',
          500: '#DC362E',
          600: '#B3261E',
          700: '#8C1D18',
          800: '#601410',
          900: '#410E0B',
        },
        neutral: {
          10: '#FFFBFE',
          50: '#F4EFF4',
          100: '#E6E1E5',
          200: '#C9C5CA',
          300: '#AEAAAE',
          400: '#939094',
          500: '#787579',
          600: '#605D62',
          700: '#484649',
          800: '#313033',
          900: '#1C1B1F',
        },
        gray: {
          10: '#FFFBFE',
          50: '#F5EEFA',
          100: '#E7E0EC',
          200: '#CAC4D0',
          300: '#AEA9B4',
          400: '#938F99',
          500: '#79747E',
          600: '#605D66',
          700: '#49454F',
          800: '#322F37',
          900: '#1D1A22',
        },
        surface: {
          50: '#FFFBFE',
          100: '#f8f3f9',
          200: '#f3eef7',
          300: '#eee8f3',
          400: '#ede6f2',
          500: '#eae3f1',
        },
        surfacedark: {
          50: '#1c1b1f',
          100: '#26242a',
          200: '#2a2830',
          300: '#302d37',
          400: '#332f3a',
          500: '#36323e',
        }
      }
    },
    fontFamily: {
      sans: ['Roboto', 'sans-serif'],
      serif: ['Roboto Serif', 'serif'],
    },
    screens: {
      'sm': '640px',
      'md': '768px',
      'lg': '1024px',
      'xl': '1280px',
      'xxl': '1400px'
    }
  },
  plugins: [],
}

