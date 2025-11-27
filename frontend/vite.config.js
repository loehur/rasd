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
                    // Public pages
                    index: resolve(__dirname, "pages/public/index.html"),
                    // Admin pages
                    adminLogin: resolve(__dirname, "pages/admin/login.html"),
                    dashboard: resolve(__dirname, "pages/admin/dashboard.html"),
                    importStaff: resolve(
                        __dirname,
                        "pages/admin/import-staff.html"
                    ),
                    staffList: resolve(
                        __dirname,
                        "pages/admin/staff-list.html"
                    ),
                    account: resolve(__dirname, "pages/admin/account.html"),
                    changePassword: resolve(
                        __dirname,
                        "pages/admin/change-password.html"
                    ),
                },
            },
        },
    };
});
