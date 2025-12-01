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
                                class="text-sm font-bold tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-amber-300 to-orange-300"
                            >
                                Team Leader Resign
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
            <!-- Info Banner -->
            <div
                class="bg-amber-900/20 border border-amber-500/40 rounded-xl p-4 mb-6"
            >
                <div class="flex items-start gap-3">
                    <svg
                        class="w-6 h-6 text-amber-400 flex-shrink-0 mt-0.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-amber-300 mb-1">
                            Team Leader Resignation
                        </h3>
                        <p class="text-xs text-amber-200/80">
                            When a team leader resigns, all their staff members will be transferred to the replacement team leader. This action will be logged in the staff change history.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl overflow-hidden"
            >
                <div class="p-6 border-b border-slate-800/80">
                    <h2 class="text-xl font-bold text-slate-100 mb-2">
                        Process Team Leader Resignation
                    </h2>
                    <p class="text-sm text-slate-400">
                        Select the team leader who is resigning and their replacement
                    </p>
                </div>

                <div class="p-6">
                    <!-- Error/Success Messages -->
                    <div
                        v-if="errorMessage"
                        class="mb-4 p-4 bg-red-500/10 border border-red-500/30 rounded-lg"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 flex-1">
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
                                <p class="text-sm font-medium text-red-300">{{ errorMessage }}</p>
                            </div>
                            <button
                                @click="errorMessage = ''"
                                class="text-red-400 hover:text-red-300 transition flex-shrink-0"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="successMessage"
                        class="mb-4 p-4 bg-green-500/10 border border-green-500/30 rounded-lg"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 flex-1">
                                <svg
                                    class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                <p class="text-sm font-medium text-green-300">{{ successMessage }}</p>
                            </div>
                            <button
                                @click="successMessage = ''"
                                class="text-green-400 hover:text-green-300 transition flex-shrink-0"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Form -->
                    <div class="space-y-6">
                        <!-- Resigning TL -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Team Leader Resigning <span class="text-red-400">*</span>
                            </label>
                            <select
                                v-model="resigningTL"
                                class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:border-amber-500 transition"
                                :disabled="loading || submitting"
                            >
                                <option value="">-- Select Team Leader --</option>
                                <option
                                    v-for="tl in teamLeaders"
                                    :key="tl.staff_id"
                                    :value="tl.staff_id"
                                >
                                    {{ formatName(tl.name) }} ({{ tl.staff_id }})
                                </option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">
                                Select the team leader who will be leaving
                            </p>
                        </div>

                        <!-- Replacement TL -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Replacement Team Leader <span class="text-red-400">*</span>
                            </label>
                            <select
                                v-model="replacementTL"
                                class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:border-amber-500 transition"
                                :disabled="loading || submitting || !resigningTL"
                            >
                                <option value="">-- Select Replacement --</option>
                                <option
                                    v-for="tl in availableReplacements"
                                    :key="tl.staff_id"
                                    :value="tl.staff_id"
                                >
                                    {{ formatName(tl.name) }} ({{ tl.staff_id }})
                                </option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">
                                All staff members will be transferred to this team leader
                            </p>
                        </div>

                        <!-- Staff Count Info -->
                        <div
                            v-if="resigningTL && staffCount !== null"
                            class="bg-slate-800/50 border border-slate-700 rounded-lg p-4"
                        >
                            <div class="flex items-center gap-3">
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
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0"
                                    />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-slate-200">
                                        <span class="text-blue-400 font-bold">{{ staffCount }}</span> staff member(s) will be transferred
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        from {{ getTeamLeaderName(resigningTL) }} to {{ getTeamLeaderName(replacementTL) || 'selected replacement' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-3">
                            <button
                                @click="goBack"
                                :disabled="submitting"
                                class="flex-1 px-4 py-3 bg-slate-800 text-slate-300 border border-slate-700 rounded-lg hover:bg-slate-700 transition disabled:opacity-50"
                            >
                                Cancel
                            </button>
                            <button
                                @click="submitResignation"
                                :disabled="!canSubmit"
                                class="flex-1 px-4 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                <svg
                                    v-if="submitting"
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
                                {{ submitting ? 'Processing...' : 'Process Resignation' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || "";

const user = ref({
    name: "",
    role: "",
});

const teamLeaders = ref([]);
const resigningTL = ref("");
const replacementTL = ref("");
const staffCount = ref(null);
const loading = ref(false);
const submitting = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

const goBack = () => {
    window.location.href = "/admin/dashboard";
};

const loadTeamLeaders = async () => {
    loading.value = true;
    errorMessage.value = "";

    try {
        const token = localStorage.getItem("auth_token");
        const userData = localStorage.getItem("user");
        const parsedUser = userData ? JSON.parse(userData) : {};

        const res = await fetch(`${API_BASE_URL}/api/team-leaders`, {
            method: "GET",
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "application/json",
                "X-Role": parsedUser.role || "admin"
            }
        });

        const data = await res.json();

        if (data.success && Array.isArray(data.data)) {
            teamLeaders.value = data.data;
        } else {
            errorMessage.value = "Failed to load team leaders";
        }
    } catch (e) {
        console.error("Load error:", e);
        errorMessage.value = "Connection error. Please try again.";
    } finally {
        loading.value = false;
    }
};

const loadStaffCount = async () => {
    if (!resigningTL.value) {
        staffCount.value = null;
        return;
    }

    try {
        const token = localStorage.getItem("auth_token");
        const userData = localStorage.getItem("user");
        const parsedUser = userData ? JSON.parse(userData) : {};

        const res = await fetch(`${API_BASE_URL}/api/staff?team_leader_id=${resigningTL.value}`, {
            method: "GET",
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "application/json",
                "X-Role": parsedUser.role || "admin"
            }
        });

        const data = await res.json();

        if (data.success && Array.isArray(data.data)) {
            staffCount.value = data.data.filter(s => s.status === 'active').length;
        } else {
            staffCount.value = 0;
        }
    } catch (e) {
        console.error("Staff count error:", e);
        staffCount.value = 0;
    }
};

const formatName = (name) => {
    if (!name) return '';
    // Convert to title case (capitalize first letter of each word)
    return name
        .toLowerCase()
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const availableReplacements = computed(() => {
    if (!resigningTL.value) return [];
    return teamLeaders.value.filter(tl => tl.staff_id !== resigningTL.value);
});

const canSubmit = computed(() => {
    return resigningTL.value && replacementTL.value && !submitting.value;
});

const getTeamLeaderName = (id) => {
    const tl = teamLeaders.value.find(t => t.staff_id === id);
    return tl ? formatName(tl.name) : '';
};

const submitResignation = async () => {
    if (!canSubmit.value) return;

    submitting.value = true;
    errorMessage.value = "";
    successMessage.value = "";

    try {
        const token = localStorage.getItem("auth_token");
        const userData = localStorage.getItem("user");
        const parsedUser = userData ? JSON.parse(userData) : {};

        const res = await fetch(`${API_BASE_URL}/api/team-leaders/resign`, {
            method: "POST",
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "application/json",
                "X-Role": parsedUser.role || "admin"
            },
            body: JSON.stringify({
                resigning_tl_id: resigningTL.value,
                replacement_tl_id: replacementTL.value
            })
        });

        const data = await res.json();

        if (data.success) {
            successMessage.value = data.message || `Successfully transferred ${data.transferred_count || 0} staff members`;
            // Reload the team leaders list to reflect changes
            loadTeamLeaders();
        } else {
            errorMessage.value = data.message || "Failed to process resignation";
        }
    } catch (e) {
        console.error("Submit error:", e);
        errorMessage.value = "Connection error. Please try again.";
    } finally {
        submitting.value = false;
    }
};

watch(resigningTL, () => {
    loadStaffCount();
    if (resigningTL.value === replacementTL.value) {
        replacementTL.value = "";
    }
});

onMounted(() => {
    // Check if user is logged in and is admin
    const authToken = localStorage.getItem("auth_token");
    const userData = localStorage.getItem("user");

    if (!authToken || !userData) {
        window.location.href = "/admin";
        return;
    }

    const parsedUser = JSON.parse(userData);
    if (parsedUser.role !== "admin" && parsedUser.role !== "super-admin") {
        alert("Access denied. Admin only.");
        window.location.href = "/admin/dashboard";
        return;
    }

    user.value = parsedUser;
    loadTeamLeaders();
});
</script>
