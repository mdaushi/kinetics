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

  // Bundle kinetics-core langsung ke dalam output react.
  // Diperlukan karena workspace:* protocol tidak bekerja
  // saat package diinstall via local path dari vendor/.
  noExternal: ["@mdaushi/kinetics-core"],

  // Semua peerDependencies harus di-external.
  external: ["react", "react-dom", "@inertiajs/react", "@tanstack/react-table"],
});
