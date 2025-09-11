import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue2";
import path from "path";

export default defineConfig({

    server: {
          host: 'localhost',
    },

    plugins: [
        vue(),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    define: {
        "process.env.SOME_ENV": `"${process.env.SOME_ENV}"`,
    },
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "./resources/js"),
            "~": path.resolve(__dirname, "node_modules"),
            find: "@vue/runtime-core",
            replacement: "@vue/runtime-core/dist/runtime-core.esm-bundler.js",
        },
    },
    // build: {
    //     rollupOptions: {
    //         output: {
    //             manualChunks(id) {
    //                 if (id.includes("node_modules")) {
    //                     return id
    //                         .toString()
    //                         .split("node_modules/")[1]
    //                         .split("/")[0]
    //                         .toString();
    //                 }
    //             },
    //         },
    //     },
    // },
});
