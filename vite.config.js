import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { resolve } from "path";
import inject from "@rollup/plugin-inject";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/scss/app.scss",
                "resources/scss/admin.scss",
                "resources/js/app.js",
                "resources/js/admin.js",
            ],
            refresh: true,
        }),
        inject({
            $: "jquery",
            jQuery: "jquery",
        }),
    ],
    resolve: {
        alias: {
            "@": "/resources",
            "~bootstrap": resolve(__dirname, "node_modules/bootstrap"),
            "~@fortawesome": resolve(__dirname, "node_modules/@fortawesome"),
            "~datatables.net-bs5": resolve(
                __dirname,
                "node_modules/datatables.net-bs5"
            ),
        },
    },
    // css: {
    //     preprocessorOptions: {
    //         scss: {
    //             additionalData: `@import "scss/abstracts/variables";`,
    //         },
    //     },
    // },
});
