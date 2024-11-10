import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import { resolve } from "path";

export default defineConfig({
	plugins: [react()],
	build: {
		outDir: "dist",
		emptyOutDir: true,
		rollupOptions: {
			input: resolve(__dirname, "assets/index.ts"),
			output: {
				assetFileNames: "[name][extname]",
				entryFileNames: "[name].js",
				chunkFileNames: "[name]-[hash].js",
			},
		},
	},
	resolve: {
		alias: {
			"@": resolve(__dirname, "assets"),
		},
	},
	css: {
		preprocessorOptions: {
			scss: {
				api: "modern-compiler",
			},
		},
	},
});
