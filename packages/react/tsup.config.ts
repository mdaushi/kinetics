import { defineConfig } from "tsup";

export default defineConfig({
  entry: ["src/index.ts"],
  format: ["cjs", "esm"],
  dts: true,
  clean: true,
  sourcemap: true,
  treeshake: true,
  banner: {
    js: 'import "./index.css";',
  },

  // Rule: setiap package di peerDependencies WAJIB ada di sini.
  external: ["react", "react-dom", "@inertiajs/react", "@tanstack/react-table"],
});
