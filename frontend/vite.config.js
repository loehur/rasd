// FILE: vite.config.js
import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import { fileURLToPath } from "node:url";
import { resolve } from "node:path";

const __dirname = fileURLToPath(new URL(".", import.meta.url));

export default defineConfig(({ command }) => {
    if (command === "serve") {
        return {
            root: ".", // gunakan root proyek agar /src/... ter-resolve
            publicDir: "public",
            plugins: [vue()],
            resolve: { alias: { "@": resolve(__dirname, "src") } },
            server: {
                open: true, // buka browser otomatis ke root (/)
                port: 5173,
                fs: { allow: [resolve(__dirname)] },
            },
        };
    }

    return {
        root: ".",
        publicDir: "public",
        plugins: [vue()],
        resolve: { alias: { "@": resolve(__dirname, "src") } },
        build: {
            outDir: "dist",
            emptyOutDir: true,
            rollupOptions: {
                input: {
                    main: resolve(__dirname, "index.html"),
                    admin: resolve(__dirname, "admin.html"),
                    dashboard: resolve(__dirname, "dashboard.html"),
                    importStaff: resolve(__dirname, "import-staff.html"),
                    staffList: resolve(__dirname, "staff-list.html"),
                    account: resolve(__dirname, "account.html"),
                    changePassword: resolve(__dirname, "change-password.html"),
                },
            },
        },
    };
});
