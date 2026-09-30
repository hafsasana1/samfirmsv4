/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    "./app/Views/**/*.php",
    "./public/assets/js/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        canvas: '#F8FAF9',
        surface: '#FFFFFF',
        ink: {
          DEFAULT: '#1A1D21',
          muted: '#5A5D63',
          faint: '#9A9DA3',
        },
        navy: {
          900: '#4a154b',  // 🎨 Deep Purple (from samfirms-main)
          800: '#611f6a',  // 🎨 Medium Purple
          700: '#7d2b86',  // 🎨 Light Purple
        },
        accent: {
          DEFAULT: '#ff767a',  // 🎨 Coral/Pink (NEW accent color)
          hover: '#ff5a5f',    // 🎨 Coral Hover
          soft: '#fff0f0',     // 🎨 Light Coral Background
        },
        highlight: {
          DEFAULT: '#ff767a',  // 🎨 Coral/Pink
          soft: '#fff0f0',
        },
        danger: {
          DEFAULT: '#e01e5b',  // 🎨 Danger Pink (from samfirms-main)
          soft: '#FDE8E8',
        },
        success: {
          DEFAULT: '#2eb67d',  // 🎨 Success Green (from samfirms-main)
          dark: '#007a5b',
          soft: '#E3F9F1',
        },
        info: {
          DEFAULT: '#167895',  // 🎨 Info Blue (from samfirms-main)
          dark: '#1264a4',
          soft: '#E3F3F9',
        },
      },
      fontFamily: {
        sans: ['Inter', 'Roboto', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
        'card': '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)',
      },
      animation: {
        'slide-in': 'slideIn 0.3s ease-out',
        'fade-in': 'fadeIn 0.3s ease-out',
        'spin-slow': 'spin 2s linear infinite',
        'rotate-bubble': 'rotateBubble 10s linear infinite',
        'rotate-circle': 'rotateCircle 5s linear infinite',
      },
      keyframes: {
        slideIn: {
          '0%': { transform: 'translateY(-10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        rotateBubble: {
          '0%': { transform: 'rotate(0deg) translate(-10px) rotate(0deg)' },
          '100%': { transform: 'rotate(360deg) translate(-10px) rotate(-360deg)' },
        },
        rotateCircle: {
          '0%': { transform: 'rotate(0deg) translate(-10px) rotate(0deg)' },
          '100%': { transform: 'rotate(360deg) translate(-10px) rotate(-360deg)' },
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
