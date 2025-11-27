<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <!-- Top Navigation Bar -->
        <nav
            class="bg-slate-900/80 backdrop-blur border-b border-slate-800/80 sticky top-0 z-50"
        >
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <button
                        @click="refreshPage"
                        class="flex items-center gap-3 hover:opacity-80 transition cursor-pointer text-left"
                    >
                        <div
                            class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-emerald-400 flex items-center justify-center shadow-lg"
                        >
                            <svg
                                class="w-6 h-6 text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                ></path>
                            </svg>
                        </div>
                        <div class="select-none text-left">
                            <p
                                class="text-sm font-bold tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400"
                            >
                                STAFF PERFORMANCE
                            </p>
                            <p class="text-[0.65rem] text-slate-500">
                                Performance System
                            </p>
                        </div>
                    </button>

                    <!-- User Dropdown -->
                    <div class="relative">
                        <button
                            @click="toggleDropdown"
                            class="flex items-center gap-2 text-emerald-400 hover:text-emerald-300 transition cursor-pointer"
                        >
                            <div class="text-right">
                                <p class="text-sm font-semibold">
                                    {{ user.name }}
                                </p>
                                <p class="text-xs text-emerald-300/70">
                                    {{ user.role }}
                                </p>
                            </div>
                            <svg
                                class="w-4 h-4 transition-transform"
                                :class="{ 'rotate-180': dropdownOpen }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                ></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            v-if="dropdownOpen"
                            class="absolute right-0 mt-2 w-48 bg-slate-900 border border-slate-800 rounded-lg shadow-lg overflow-hidden z-50"
                        >
                            <button
                                @click="goToAccount"
                                class="w-full px-4 py-2 text-left text-sm text-slate-300 hover:bg-slate-800 transition flex items-center gap-2"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    ></path>
                                </svg>
                                Account
                            </button>
                            <button
                                @click="goToChangePassword"
                                class="w-full px-4 py-2 text-left text-sm text-slate-300 hover:bg-slate-800 transition flex items-center gap-2"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                                    ></path>
                                </svg>
                                Change Password
                            </button>
                            <button
                                @click="logout"
                                class="w-full px-4 py-2 text-left text-sm text-red-400 hover:bg-slate-800 transition flex items-center gap-2"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                    ></path>
                                </svg>
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Dashboard Section -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-slate-100 mb-4">
                        Dashboard
                    </h2>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Card 1 -->
                        <div
                            class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl p-4 hover:border-blue-500/30 transition-all"
                        >
                            <div
                                class="w-12 h-12 mx-auto mb-3 bg-blue-500/10 rounded-xl border border-blue-500/20 flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-blue-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                    ></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-slate-100">
                                    47
                                </p>
                                <p class="text-sm text-slate-400 mt-1">
                                    Total Staff
                                </p>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div
                            class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl p-4 hover:border-emerald-500/30 transition-all"
                        >
                            <div
                                class="w-12 h-12 mx-auto mb-3 bg-emerald-500/10 rounded-xl border border-emerald-500/20 flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-emerald-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                    ></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-slate-100">
                                    5
                                </p>
                                <p class="text-sm text-slate-400 mt-1">
                                    On Leave
                                </p>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div
                            class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl p-4 hover:border-purple-500/30 transition-all"
                        >
                            <div
                                class="w-12 h-12 mx-auto mb-3 bg-purple-500/10 rounded-xl border border-purple-500/20 flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-purple-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                    ></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-slate-100">
                                    42
                                </p>
                                <p class="text-sm text-slate-400 mt-1">
                                    Present Today
                                </p>
                            </div>
                        </div>

                        <!-- Card 4 -->
                        <div
                            class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl p-4 hover:border-orange-500/30 transition-all"
                        >
                            <div
                                class="w-12 h-12 mx-auto mb-3 bg-orange-500/10 rounded-xl border border-orange-500/20 flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-orange-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    ></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-slate-100">
                                    89%
                                </p>
                                <p class="text-sm text-slate-400 mt-1">
                                    Productivity
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-slate-100 mb-4">
                        Quick Actions
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <button
                            @click="goToStaffList"
                            class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-xl p-4 hover:border-purple-500/50 hover:bg-slate-800/50 transition-all text-center group"
                        >
                            <div
                                class="w-12 h-12 mx-auto mb-3 bg-purple-500/10 rounded-lg flex items-center justify-center group-hover:bg-purple-500/20 transition"
                            >
                                <svg
                                    class="w-6 h-6 text-purple-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                    ></path>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-slate-200">
                                View Staff
                            </p>
                        </button>

                        <button
                            @click="goToTeamLeaderList"
                            class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-xl p-4 hover:border-emerald-500/50 hover:bg-slate-800/50 transition-all text-center group"
                        >
                            <div
                                class="w-12 h-12 mx-auto mb-3 bg-emerald-500/10 rounded-lg flex items-center justify-center group-hover:bg-emerald-500/20 transition"
                            >
                                <svg
                                    class="w-6 h-6 text-emerald-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-slate-200">
                                View Team Leader
                            </p>
                        </button>

                        <button
                            class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-xl p-4 hover:border-blue-500/50 hover:bg-slate-800/50 transition-all text-center group"
                        >
                            <div
                                class="w-12 h-12 mx-auto mb-3 bg-blue-500/10 rounded-lg flex items-center justify-center group-hover:bg-blue-500/20 transition"
                            >
                                <svg
                                    class="w-6 h-6 text-blue-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    ></path>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-slate-200">
                                Timesheet
                            </p>
                        </button>

                        <button
                            class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-xl p-4 hover:border-orange-500/50 hover:bg-slate-800/50 transition-all text-center group"
                        >
                            <div
                                class="w-12 h-12 mx-auto mb-3 bg-orange-500/10 rounded-lg flex items-center justify-center group-hover:bg-orange-500/20 transition"
                            >
                                <svg
                                    class="w-6 h-6 text-orange-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    ></path>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-slate-200">
                                Reports
                            </p>
                        </button>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div>
                    <h2 class="text-xl font-bold text-slate-100 mb-4">
                        Recent Activity
                    </h2>
                    <div
                        class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl p-6"
                    >
                        <div class="space-y-4">
                            <div
                                class="flex items-start gap-4 pb-4 border-b border-slate-800/50"
                            >
                                <div
                                    class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center flex-shrink-0"
                                >
                                    <svg
                                        class="w-5 h-5 text-blue-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-slate-200">
                                        <span class="font-semibold"
                                            >Budi Santoso</span
                                        >
                                        checked in at 08:15 AM
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        2 hours ago
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex items-start gap-4 pb-4 border-b border-slate-800/50"
                            >
                                <div
                                    class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center flex-shrink-0"
                                >
                                    <svg
                                        class="w-5 h-5 text-emerald-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-slate-200">
                                        Task "Monthly Report" completed by
                                        Marketing Team
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        3 hours ago
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div
                                    class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center flex-shrink-0"
                                >
                                    <svg
                                        class="w-5 h-5 text-purple-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-slate-200">
                                        Siti Aminah submitted leave request for
                                        3 days
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        5 hours ago
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const user = ref({
    name: "",
    phone_number: "",
    role: "",
});

const dropdownOpen = ref(false);

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

onMounted(() => {
    // Check if user is logged in
    const authToken = localStorage.getItem("auth_token");
    const userData = localStorage.getItem("user");

    if (!authToken || !userData) {
        // Not logged in, redirect to login
        window.location.href = "/admin";
        return;
    }

    // Load user data
    user.value = JSON.parse(userData);

    // Close dropdown when clicking outside
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".relative")) {
            dropdownOpen.value = false;
        }
    });
});

const logout = () => {
    // Clear auth data
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user");

    // Redirect to login
    window.location.href = "/admin";
};

const goToAccount = () => {
    window.location.href = "/admin/account";
};

const goToChangePassword = () => {
    window.location.href = "/admin/change-password";
};

const goToImportStaff = () => {
    window.location.href = "/admin/import-staff";
};

const goToStaffList = () => {
    window.location.href = "/admin/staff-list";
};

const refreshPage = () => {
    window.location.reload();
};
const goToTeamLeaderList = () => {
    window.location.href = "/admin/team-leader-list";
};
</script>
