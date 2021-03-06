module.exports = {
  future: {
    removeDeprecatedGapUtilities: true,
    purgeLayersByDefault: true,
  },
  purge: [],
  theme: {
    extend: {},
  },
  variants: {},
  plugins: [
    require('tailwindcss'),
    require('@tailwindcss/ui'),
    require('autoprefixer'),
    require('@tailwindcss/custom-forms')
  ],
}