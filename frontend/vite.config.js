// FILE: vite.config.js
import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import { fileURLToPath } from "node:url";
import { resolve } from "node:path";

const __dirname = fileURLToPath(new URL(".", import.meta.url));

export default defineConfig(({ command }) => {
    if (command === "serve") {
        return {
            root: ".",
            publicDir: "public",
            plugins: [
                vue(),
                {
                    name: "rewrite-middleware",
                    configureServer(server) {
                        server.middlewares.use((req, res, next) => {
                            // Rewrite /admin -> /pages/admin/login.html
                            if (req.url === "/admin" || req.url === "/admin/") {
                                req.url = "/pages/admin/login.html";
                            }
                            // Rewrite /admin/dashboard -> /pages/admin/dashboard.html
                            else if (req.url.startsWith("/admin/")) {
                                const page = req.url.replace("/admin/", "");
                                req.url = `/pages/admin/${page}.html`;
                            }
                            next();
                        });
                    },
                },
            ],
            resolve: { alias: { "@": resolve(__dirname, "src") } },
            server: {
                open: "/admin",
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
            outDir: "../public",
            emptyOutDir: false,
            rollupOptions: {
                input: {
                    // Public pages
                    index: resolve(__dirname, "pages/public/index.html"),
                    teamLeaderLogin: resolve(
                        __dirname,
                        "pages/public/team-leader-login.html"
                    ),
                    // Admin pages
                    adminLogin: resolve(__dirname, "pages/admin/login.html"),
                    dashboard: resolve(__dirname, "pages/admin/dashboard.html"),
                    importStaff: resolve(
                        __dirname,
                        "pages/admin/import-staff.html"
                    ),
                    importTeamLeader: resolve(
                        __dirname,
                        "pages/admin/import-team-leader.html"
                    ),
                    teamLeaderList: resolve(
                        __dirname,
                        "pages/admin/team-leader-list.html"
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
