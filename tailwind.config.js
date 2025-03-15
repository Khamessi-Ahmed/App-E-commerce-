/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./assets/**/*.js", "./templates/**/*.html.twig"],
  theme: {
    extend: {
      colors: {
        "primary": "#0d9488",
      },
    },
  },
  plugins: [require("@tailwindcss/forms")],
};
