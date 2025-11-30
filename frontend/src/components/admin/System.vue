<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <!-- Top Navigation Bar -->
        <nav
            class="bg-slate-900/80 backdrop-blur border-b border-slate-800/80 sticky top-0 z-50"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-3">
                        <button
                            @click="goBack"
                            class="p-2 hover:bg-slate-800/50 rounded-lg transition"
                        >
                            <svg
                                class="w-6 h-6 text-slate-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"
                                />
                            </svg>
                        </button>
                        <div class="text-left">
                            <p
                                class="text-sm font-bold tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-red-300 to-orange-300"
                            >
                                System Settings
                            </p>
                            <p class="text-[0.7rem] text-slate-500">
                                Admin Portal
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-emerald-400">
                            {{ user.name }}
                        </p>
                        <p class="text-xs text-emerald-300/70">
                            {{ user.role }}
                        </p>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Warning Banner -->
            <div
                class="bg-red-900/20 border border-red-500/40 rounded-xl p-4 mb-6"
            >
                <div class="flex items-start gap-3">
                    <svg
                        class="w-6 h-6 text-red-400 flex-shrink-0 mt-0.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        />
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-red-300 mb-1">
                            Danger Zone
                        </h3>
                        <p class="text-xs text-red-200/80">
                            Actions in this area are irreversible. Please
                            proceed with extreme caution.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Reset Data Section -->
            <div
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl overflow-hidden"
            >
                <div class="p-6 border-b border-slate-800/80">
                    <h2 class="text-xl font-bold text-slate-100 mb-2">
                        Database Management
                    </h2>
                    <p class="text-sm text-slate-400">
                        Manage system data and perform maintenance operations
                    </p>
                </div>

                <!-- Reset Staff Data -->
                <div class="p-6 border-b border-slate-800/80">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-slate-200 mb-2">
                                Reset All Staff Data
                            </h3>
                            <p class="text-sm text-slate-400 mb-2">
                                Permanently delete all staff records from the
                                database. This action cannot be undone.
                            </p>
                            <div class="flex items-center gap-2 text-xs text-red-400">
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
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                    />
                                </svg>
                                <span>Warning: This will delete all staff data permanently</span>
                            </div>
                        </div>
                        <button
                            @click="showResetConfirmation"
                            :disabled="isResetting"
                            class="px-4 py-2 bg-red-600/20 text-red-300 border border-red-500/40 rounded-lg hover:bg-red-600/30 hover:border-red-400/60 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <svg
                                v-if="!isResetting"
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                />
                            </svg>
                            <svg
                                v-else
                                class="w-5 h-5 animate-spin"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            {{ isResetting ? "Resetting..." : "Reset Data" }}
                        </button>
                    </div>
                </div>

                <!-- System Info -->
                <div class="p-6 bg-slate-900/40">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-slate-800/30 rounded-lg">
                            <p class="text-xs text-slate-400 mb-1">Total Staff</p>
                            <p class="text-2xl font-bold text-slate-200">
                                {{ stats.totalStaff }}
                            </p>
                        </div>
                        <div class="text-center p-4 bg-slate-800/30 rounded-lg">
                            <p class="text-xs text-slate-400 mb-1">Active Staff</p>
                            <p class="text-2xl font-bold text-emerald-400">
                                {{ stats.activeStaff }}
                            </p>
                        </div>
                        <div class="text-center p-4 bg-slate-800/30 rounded-lg">
                            <p class="text-xs text-slate-400 mb-1">Team Leaders</p>
                            <p class="text-2xl font-bold text-purple-400">
                                {{ stats.teamLeaders }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div
                v-if="showModal"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4"
                @click="closeModal"
            >
                <div
                    @click.stop
                    class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full overflow-hidden"
                >
                    <div class="p-6 border-b border-slate-800">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 rounded-full bg-red-500/20 flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-red-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-100">
                                    Confirm Reset
                                </h3>
                                <p class="text-xs text-slate-400">
                                    This action cannot be undone
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Error Message Banner -->
                        <div
                            v-if="errorMessage"
                            class="mb-4 p-4 bg-red-500/10 border border-red-500/40 rounded-lg"
                        >
                            <div class="flex items-start gap-3 mb-3">
                                <svg
                                    class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-red-300 mb-1">{{ errorMessage }}</p>
                                    <p v-if="errorMessage.includes('session') || errorMessage.includes('Invalid password')" class="text-xs text-red-200/70">
                                        <strong>Tip:</strong> If this persists, try logging out and logging back in.
                                    </p>
                                </div>
                                <button
                                    @click="errorMessage = ''"
                                    class="text-red-400 hover:text-red-300 transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <button
                                v-if="errorMessage.includes('session') || errorMessage.includes('invalid') || errorMessage.includes('expired')"
                                @click="handleLogout"
                                class="w-full px-3 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-300 text-xs font-medium rounded-md transition flex items-center justify-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Logout and Login Again
                            </button>
                        </div>

                        <p class="text-sm text-slate-300 mb-4">
                            Are you absolutely sure you want to delete
                            <strong class="text-red-400">all staff data</strong>?
                            This will permanently remove:
                        </p>
                        <ul class="text-sm text-slate-400 space-y-2 mb-6 ml-4">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span>
                                All staff records ({{ stats.totalStaff }} total)
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span>
                                All team leader data ({{ stats.teamLeaders }} total)
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span>
                                Associated attendance records
                            </li>
                        </ul>
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Type <strong class="text-red-400">DELETE</strong> to confirm:
                                </label>
                                <input
                                    v-model="confirmText"
                                    type="text"
                                    placeholder="DELETE"
                                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-red-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Enter your password to confirm:
                                </label>
                                <input
                                    v-model="confirmPassword"
                                    type="password"
                                    placeholder="Your password"
                                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-red-500"
                                    @keyup.enter="confirmReset"
                                />
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button
                                @click="closeModal"
                                :disabled="isResetting"
                                class="flex-1 px-4 py-2 bg-slate-800 text-slate-300 border border-slate-700 rounded-lg hover:bg-slate-700 transition disabled:opacity-50"
                            >
                                Cancel
                            </button>
                            <button
                                @click="confirmReset"
                                :disabled="confirmText !== 'DELETE' || !confirmPassword || isResetting"
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ isResetting ? "Deleting..." : "Delete All Data" }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || "";

const user = ref({
    name: "",
    role: "",
});

const stats = ref({
    totalStaff: 0,
    activeStaff: 0,
    teamLeaders: 0,
});

const showModal = ref(false);
const confirmText = ref("");
const confirmPassword = ref("");
const isResetting = ref(false);
const errorMessage = ref("");

const goBack = () => {
    window.location.href = "/admin/dashboard";
};

const handleLogout = () => {
    // Clear auth data
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user");

    // Redirect to login
    window.location.href = "/admin";
};

const loadStats = async () => {
    try {
        const token = localStorage.getItem("auth_token");
        const userData = localStorage.getItem("user");
        const parsedUser = userData ? JSON.parse(userData) : {};

        const headers = {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            "X-Role": parsedUser.role || "admin"
        };

        // Load total staff count
        const staffRes = await fetch(`${API_BASE_URL}/api/staff`, { headers });

        if (!staffRes.ok) {
            console.error("Staff API error:", staffRes.status, await staffRes.text());
        } else {
            const staffData = await staffRes.json();
            console.log("Staff data:", staffData);

            if (staffData.success && Array.isArray(staffData.data)) {
                stats.value.totalStaff = staffData.data.length;
                stats.value.activeStaff = staffData.data.filter(s => s.status === 'active').length;
            }
        }

        // Load team leaders count
        const tlRes = await fetch(`${API_BASE_URL}/api/team-leaders`, { headers });

        if (!tlRes.ok) {
            console.error("Team Leaders API error:", tlRes.status, await tlRes.text());
        } else {
            const tlData = await tlRes.json();
            console.log("Team Leaders data:", tlData);

            if (tlData.success && Array.isArray(tlData.data)) {
                stats.value.teamLeaders = tlData.data.length;
            }
        }
    } catch (e) {
        console.error("Failed to load stats:", e);
    }
};

const showResetConfirmation = () => {
    confirmText.value = "";
    confirmPassword.value = "";
    errorMessage.value = "";
    showModal.value = true;
};

const closeModal = () => {
    if (!isResetting.value) {
        showModal.value = false;
        confirmText.value = "";
        confirmPassword.value = "";
        errorMessage.value = "";
    }
};

const confirmReset = async () => {
    if (confirmText.value !== "DELETE" || !confirmPassword.value || isResetting.value) {
        return;
    }

    isResetting.value = true;
    errorMessage.value = ""; // Clear previous errors

    try {
        const token = localStorage.getItem("auth_token");
        const userData = localStorage.getItem("user");
        const parsedUser = userData ? JSON.parse(userData) : {};

        const res = await fetch(`${API_BASE_URL}/api/staff/reset-all`, {
            method: "DELETE",
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "application/json",
                "X-Role": parsedUser.role || "super-admin"
            },
            body: JSON.stringify({
                password: confirmPassword.value
            })
        });

        const data = await res.json();

        if (data.success) {
            // Show success message and close modal
            errorMessage.value = "";
            closeModal();
            loadStats(); // Reload stats

            // Show success notification
            setTimeout(() => {
                alert(`Success! Deleted ${data.deleted} staff records.`);
            }, 100);
        } else {
            // Show error in modal
            errorMessage.value = data.message || "Failed to reset data";
        }
    } catch (e) {
        console.error("Reset error:", e);
        errorMessage.value = "Connection error. Please try again.";
    } finally {
        isResetting.value = false;
    }
};

onMounted(() => {
    // Check if user is logged in and is super-admin
    const authToken = localStorage.getItem("auth_token");
    const userData = localStorage.getItem("user");

    if (!authToken || !userData) {
        window.location.href = "/admin";
        return;
    }

    const parsedUser = JSON.parse(userData);
    if (parsedUser.role !== "super-admin") {
        alert("Access denied. Super-admin only.");
        window.location.href = "/admin/dashboard";
        return;
    }

    user.value = parsedUser;
    loadStats();
});
</script>
