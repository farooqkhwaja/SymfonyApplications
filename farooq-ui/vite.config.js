import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [react(), tailwindcss()],

  build: {
    lib: {
      entry: "src/index.js",
      name: "FarooqUI",
      formats: ["es", "cjs"],
      fileName: (format) =>
        format === "es" ? "farooq-ui.mjs" : "farooq-ui.cjs",
    },
  },
});
