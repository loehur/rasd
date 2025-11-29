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
                    name: "global-favicon",
                    transformIndexHtml(html) {
                        return {
                            html,
                            tags: [
                                {
                                    tag: "link",
                                    attrs: {
                                        rel: "icon",
                                        href: "http://localhost/sd_pro/public/assets/staff.png",
                                    },
                                    injectTo: "head",
                                },
                            ],
                        };
                    },
                },
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
                            // Team Leader login routes
                            else if (
                                req.url === "/" ||
                                req.url === "/team-leader" ||
                                req.url === "/team-leader/"
                            ) {
                                req.url =
                                    "/pages/public/team-leader-login.html";
                            }
                            // Rewrite /team-leader/<page> -> /pages/team-leader/<page>.html
                            else if (req.url.startsWith("/team-leader/")) {
                                const page = req.url.replace(
                                    "/team-leader/",
                                    ""
                                );
                                req.url = `/pages/team-leader/${page}.html`;
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
        plugins: [
            vue(),
            {
                name: "global-favicon",
                transformIndexHtml(html) {
                    return {
                        html,
                        tags: [
                            {
                                tag: "link",
                                attrs: {
                                    rel: "icon",
                                    href: "/assets/staff.png",
                                },
                                injectTo: "head",
                            },
                        ],
                    };
                },
            },
        ],
        resolve: { alias: { "@": resolve(__dirname, "src") } },
        build: {
            outDir: "../public",
            emptyOutDir: false,
            rollupOptions: {
                input: {
                    // Public pages
                    teamLeaderLogin: resolve(
                        __dirname,
                        "pages/public/team-leader-login.html"
                    ),
                    // Team Leader pages
                    teamLeaderDashboard: resolve(
                        __dirname,
                        "pages/team-leader/dashboard.html"
                    ),
                    teamLeaderAttendance: resolve(
                        __dirname,
                        "pages/team-leader/attendance.html"
                    ),
                    teamLeaderAccount: resolve(
                        __dirname,
                        "pages/team-leader/account.html"
                    ),
                    teamLeaderChangePassword: resolve(
                        __dirname,
                        "pages/team-leader/change-password.html"
                    ),
                    teamLeaderStaffList: resolve(
                        __dirname,
                        "pages/team-leader/staff-list.html"
                    ),
                    teamLeaderInactiveStaff: resolve(
                        __dirname,
                        "pages/team-leader/inactive-staff.html"
                    ),
                    teamLeaderResignation: resolve(
                        __dirname,
                        "pages/team-leader/resignation.html"
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
                    editTeamLeader: resolve(
                        __dirname,
                        "pages/admin/edit-team-leader.html"
                    ),
                    staffList: resolve(
                        __dirname,
                        "pages/admin/staff-list.html"
                    ),
                    inactiveStaff: resolve(
                        __dirname,
                        "pages/admin/inactive-staff.html"
                    ),
                    account: resolve(__dirname, "pages/admin/account.html"),
                    changePassword: resolve(
                        __dirname,
                        "pages/admin/change-password.html"
                    ),
                    users: resolve(__dirname, "pages/admin/users.html"),
                    attendance: resolve(
                        __dirname,
                        "pages/admin/attendance.html"
                    ),
                    staffChanges: resolve(
                        __dirname,
                        "pages/admin/staff-changes.html"
                    ),
                },
            },
        },
    };
});
