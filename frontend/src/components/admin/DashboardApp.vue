<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <!-- Top Navigation Bar -->
        <nav
            class="bg-slate-900/80 backdrop-blur border-b border-slate-800/80 sticky top-0 z-50"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-3 select-none">
                        <div
                            class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-sky-500 flex items-center justify-center shadow-lg"
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
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0"
                                />
                            </svg>
                        </div>
                        <div class="text-left">
                            <p
                                class="text-sm font-bold tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-sky-300"
                            >
                                Staff Details
                            </p>
                            <p class="text-[0.7rem] text-slate-500">
                                Admin Portal
                            </p>
                        </div>
                    </div>
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
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Quick Actions -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-slate-100 mb-4">
                    Quick Actions
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <!-- Card 1: Staff Lists - Active -->
                    <button
                        @click="goToStaffList"
                        class="bg-gradient-to-br from-emerald-600/20 to-emerald-500/10 border border-emerald-500/40 hover:border-emerald-400/60 hover:from-emerald-600/30 hover:to-emerald-500/20 rounded-xl p-4 transition-all text-left flex items-center gap-3"
                    >
                        <div
                            class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center"
                        >
                            <svg
                                class="w-6 h-6 text-emerald-300"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-300">Staff Lists</p>
                            <p class="text-lg font-semibold text-emerald-300">
                                Active
                            </p>
                        </div>
                    </button>

                    <!-- Card 2: Team Leader - List -->
                    <button
                        @click="goToTeamLeaderList"
                        class="bg-gradient-to-br from-fuchsia-600/20 to-fuchsia-500/10 border border-fuchsia-500/40 hover:border-fuchsia-400/60 hover:from-fuchsia-600/30 hover:to-fuchsia-500/20 rounded-xl p-4 transition-all text-left flex items-center gap-3"
                    >
                        <div
                            class="w-12 h-12 rounded-xl bg-fuchsia-500/10 border border-fuchsia-500/30 flex items-center justify-center"
                        >
                            <svg
                                class="w-6 h-6 text-fuchsia-300"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-300">Team Leader</p>
                            <p class="text-lg font-semibold text-fuchsia-300">
                                List
                            </p>
                        </div>
                    </button>

                    <!-- Card 3: Staff Lists - Resign -->
                    <button
                        @click="goToInactiveStaff"
                        class="bg-gradient-to-br from-blue-600/20 to-blue-500/10 border border-blue-500/40 hover:border-blue-400/60 hover:from-blue-600/30 hover:to-blue-500/20 rounded-xl p-4 transition-all text-left flex items-center gap-3"
                    >
                        <div
                            class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-center"
                        >
                            <svg
                                class="w-6 h-6 text-blue-300"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13M12 6.253C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-300">Staff Lists</p>
                            <p class="text-lg font-semibold text-blue-300">
                                Resign
                            </p>
                        </div>
                    </button>

                    <!-- Card 4: Staff - Attendance -->
                    <button
                        @click="goToAttendance"
                        class="bg-gradient-to-br from-orange-600/20 to-orange-500/10 border border-orange-500/40 hover:border-orange-400/60 hover:from-orange-600/30 hover:to-orange-500/20 rounded-xl p-4 transition-all text-left flex items-center gap-3"
                    >
                        <div
                            class="w-12 h-12 rounded-xl bg-orange-500/10 border border-orange-500/30 flex items-center justify-center"
                        >
                            <svg
                                class="w-6 h-6 text-orange-300"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-300">Staff</p>
                            <p class="text-lg font-semibold text-orange-300">
                                Attendance
                            </p>
                        </div>
                    </button>

                    <!-- Card 5: Staff - Changes -->
                    <button
                        @click="goToStaffChanges"
                        class="bg-gradient-to-br from-purple-600/20 to-purple-500/10 border border-purple-500/40 hover:border-purple-400/60 hover:from-purple-600/30 hover:to-purple-500/20 rounded-xl p-4 transition-all text-left flex items-center gap-3"
                    >
                        <div
                            class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center"
                        >
                            <svg
                                class="w-6 h-6 text-purple-300"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-300">Staff</p>
                            <p class="text-lg font-semibold text-purple-300">
                                Changes
                            </p>
                        </div>
                    </button>

                    <!-- Card 6: Reports - Performance -->
                    <a
                        href="https://excel.myportofolio98.xyz/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="bg-gradient-to-br from-rose-600/20 to-rose-500/10 border border-rose-500/40 hover:border-rose-400/60 hover:from-rose-600/30 hover:to-rose-500/20 rounded-xl p-4 transition-all text-left flex items-center gap-3"
                    >
                        <div
                            class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-center"
                        >
                            <svg
                                class="w-6 h-6 text-rose-300"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 17l6-6 4 4 8-8"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-300">Reports</p>
                            <p class="text-lg font-semibold text-rose-300">
                                Performance
                            </p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div v-if="false">
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
                                    Task "Monthly Report" completed by Marketing
                                    Team
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
                                    Siti Aminah submitted leave request for 3
                                    days
                                </p>
                                <p class="text-xs text-slate-500 mt-1">
                                    5 hours ago
                                </p>
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
const openStaffMenu = ref(false);

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
        const target = e.target;
        if (!(target && target.closest && target.closest(".relative"))) {
            dropdownOpen.value = false;
        }
        if (!(target && target.closest && target.closest(".staff-menu"))) {
            openStaffMenu.value = false;
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

const goToInactiveStaff = () => {
    window.location.href = "/admin/inactive-staff";
};

const refreshPage = () => {
    window.location.reload();
};
const goToTeamLeaderList = () => {
    window.location.href = "/admin/team-leader-list";
};

const goToUsers = () => {
    window.location.href = "/admin/users";
};

const goToAttendance = () => {
    window.location.href = "/admin/attendance";
};

const goToStaffChanges = () => {
    window.location.href = "/admin/staff-changes";
};
</script>
