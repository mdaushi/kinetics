import { defineConfig } from "tsup";

export default defineConfig({
  entry: ["src/index.ts"],
  format: ["cjs", "esm"],
  dts: true,
  clean: true,
  sourcemap: true,
  treeshake: true,

  // Semua peerDependencies harus di-external.
  external: ["react", "react-dom", "@inertiajs/react", "@tanstack/react-table"],
});
