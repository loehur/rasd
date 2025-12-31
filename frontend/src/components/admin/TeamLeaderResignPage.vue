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

            <!-- Recent Resignations Section -->
            <div
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl overflow-hidden mb-6"
            >
                <div class="p-6 border-b border-slate-800/80">
                    <h2 class="text-xl font-bold text-slate-100 mb-2">
                        Sudah Input (Resignations History)
                    </h2>
                    <p class="text-sm text-slate-400">
                        Team leader resignations that can be reverted
                    </p>
                </div>

                <div class="p-6">
                    <!-- Date Filter -->
                    <div class="mb-6 bg-slate-800/50 border border-slate-700 rounded-lg p-4">
                        <div class="flex flex-col sm:flex-row gap-4 items-end">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Start Date
                                </label>
                                <input
                                    type="date"
                                    v-model="filterStartDate"
                                    @change="onDateFilterChange"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:border-amber-500 transition"
                                />
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    End Date
                                </label>
                                <input
                                    type="date"
                                    v-model="filterEndDate"
                                    @change="onDateFilterChange"
                                    :max="maxEndDate"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:border-amber-500 transition"
                                />
                            </div>
                            <button
                                @click="resetDateFilter"
                                class="px-4 py-2 bg-slate-700 text-slate-300 border border-slate-600 rounded-lg hover:bg-slate-600 transition text-sm"
                            >
                                Reset
                            </button>
                        </div>
                        <p v-if="dateRangeError" class="text-xs text-red-400 mt-2">
                            {{ dateRangeError }}
                        </p>
                        <p v-else class="text-xs text-slate-500 mt-2">
                            Maksimal rentang filter adalah 1 minggu (7 hari)
                        </p>
                    </div>

                    <!-- Loading State -->
                    <div v-if="loadingResignations" class="text-center py-8">
                        <svg
                            class="w-8 h-8 animate-spin text-amber-500 mx-auto"
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
                        <p class="text-sm text-slate-400 mt-2">Loading...</p>
                    </div>

                    <!-- Empty State -->
                    <div v-else-if="recentResignations.length === 0" class="text-center py-8">
                        <svg
                            class="w-12 h-12 text-slate-600 mx-auto mb-3"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                        <p class="text-sm text-slate-400">Tidak ada data resignasi pada periode yang dipilih</p>
                    </div>

                    <!-- Resignations List -->
                    <div v-else class="space-y-3">
                        <div
                            v-for="resignation in recentResignations"
                            :key="resignation.id"
                            class="bg-slate-800/50 border border-slate-700 rounded-lg p-4"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg
                                            class="w-5 h-5 text-red-400"
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
                                        <span class="text-sm font-semibold text-slate-200">
                                            {{ formatName(resignation.resigning_tl_name) }} → {{ formatName(resignation.replacement_tl_name) || 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-slate-400 space-y-1">
                                        <p>
                                            <span class="text-slate-500">Resigning TL:</span>
                                            {{ formatName(resignation.resigning_tl_name) }} ({{ resignation.resigning_tl_id }})
                                        </p>
                                        <p>
                                            <span class="text-slate-500">Replacement TL:</span>
                                            {{ formatName(resignation.replacement_tl_name) || 'N/A' }} ({{ resignation.replacement_tl_id || 'N/A' }})
                                        </p>
                                        <p>
                                            <span class="text-slate-500">Transferred:</span>
                                            {{ resignation.transferred_count }} staff member(s)
                                        </p>
                                        <p>
                                            <span class="text-slate-500">Date:</span>
                                            {{ formatDateTime(resignation.created_at) }}
                                        </p>
                                    </div>
                                </div>
                                <button
                                    @click="revertResignation(resignation)"
                                    :disabled="reverting"
                                    class="px-4 py-2 bg-red-600/80 hover:bg-red-600 text-white text-sm rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                >
                                    <svg
                                        v-if="reverting && revertingResignationId === resignation.resigning_tl_id"
                                        class="w-4 h-4 animate-spin"
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
                                    <svg
                                        v-else
                                        class="w-4 h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"
                                        />
                                    </svg>
                                    Revert
                                </button>
                            </div>
                        </div>
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
const recentResignations = ref([]);
const reverting = ref(false);
const revertingResignationId = ref(null);
const loadingResignations = ref(false);
const dateRangeError = ref("");

// Initialize date filter with default 1 week ago
const getDefaultDateRange = () => {
    const today = new Date();
    const oneWeekAgo = new Date();
    oneWeekAgo.setDate(today.getDate() - 7);
    
    return {
        start: oneWeekAgo.toISOString().split('T')[0],
        end: today.toISOString().split('T')[0]
    };
};

const defaultDates = getDefaultDateRange();
const filterStartDate = ref(defaultDates.start);
const filterEndDate = ref(defaultDates.end);

const maxEndDate = computed(() => {
    if (!filterStartDate.value) return '';
    const start = new Date(filterStartDate.value);
    const maxEnd = new Date(start);
    maxEnd.setDate(start.getDate() + 7);
    return maxEnd.toISOString().split('T')[0];
});

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

        const list = data && data.success && Array.isArray(data.data)
            ? data.data
            : Array.isArray(data)
            ? data
            : [];
        staffCount.value = list.filter(
            (s) =>
                (String(s.staff_status || 'active').toLowerCase() === 'active') &&
                (String(s.role || '').toLowerCase() !== 'tl') &&
                (String(s.team_leader_id || '') === String(resigningTL.value || ''))
        ).length;
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
            // Reset form
            resigningTL.value = "";
            replacementTL.value = "";
            staffCount.value = null;
            // Reload the team leaders list and recent resignations
            loadTeamLeaders();
            loadRecentResignations();
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

const validateDateRange = () => {
    if (!filterStartDate.value || !filterEndDate.value) {
        dateRangeError.value = "";
        return true;
    }

    const start = new Date(filterStartDate.value);
    const end = new Date(filterEndDate.value);
    const diffTime = Math.abs(end - start);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays > 7) {
        dateRangeError.value = "Rentang tanggal maksimal 1 minggu (7 hari)";
        return false;
    }

    if (start > end) {
        dateRangeError.value = "Tanggal mulai harus sebelum atau sama dengan tanggal akhir";
        return false;
    }

    dateRangeError.value = "";
    return true;
};

const onDateFilterChange = () => {
    if (validateDateRange()) {
        loadRecentResignations();
    }
};

const resetDateFilter = () => {
    const defaultDates = getDefaultDateRange();
    filterStartDate.value = defaultDates.start;
    filterEndDate.value = defaultDates.end;
    dateRangeError.value = "";
    loadRecentResignations();
};

const loadRecentResignations = async () => {
    if (!validateDateRange()) {
        return;
    }

    loadingResignations.value = true;
    
    try {
        const token = localStorage.getItem("auth_token");
        const userData = localStorage.getItem("user");
        const parsedUser = userData ? JSON.parse(userData) : {};

        // Build query params
        const params = new URLSearchParams();
        params.append('limit', '100');
        if (filterStartDate.value) {
            params.append('start_date', filterStartDate.value);
        }
        if (filterEndDate.value) {
            params.append('end_date', filterEndDate.value);
        }

        const res = await fetch(`${API_BASE_URL}/api/team-leaders/resignations/recent?${params.toString()}`, {
            method: "GET",
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "application/json",
                "X-Role": parsedUser.role || "admin"
            }
        });

        const data = await res.json();

        if (data.success && Array.isArray(data.data)) {
            recentResignations.value = data.data;
        } else {
            recentResignations.value = [];
        }
    } catch (e) {
        console.error("Load recent resignations error:", e);
        recentResignations.value = [];
    } finally {
        loadingResignations.value = false;
    }
};

const revertResignation = async (resignation) => {
    if (!confirm(`Are you sure you want to revert this resignation?\n\nThis will:\n- Transfer ${resignation.transferred_count} staff member(s) back to ${resignation.resigning_tl_name}\n- Restore ${resignation.resigning_tl_name}'s status to active`)) {
        return;
    }

    reverting.value = true;
    revertingResignationId.value = resignation.resigning_tl_id;
    errorMessage.value = "";
    successMessage.value = "";

    try {
        const token = localStorage.getItem("auth_token");
        const userData = localStorage.getItem("user");
        const parsedUser = userData ? JSON.parse(userData) : {};

        const res = await fetch(`${API_BASE_URL}/api/team-leaders/resignations/revert`, {
            method: "POST",
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "application/json",
                "X-Role": parsedUser.role || "admin"
            },
            body: JSON.stringify({
                resigning_tl_id: resignation.resigning_tl_id
            })
        });

        const data = await res.json();

        if (data.success) {
            successMessage.value = data.message || "Successfully reverted resignation";
            // Reload data
            loadTeamLeaders();
            loadRecentResignations();
        } else {
            errorMessage.value = data.message || "Failed to revert resignation";
        }
    } catch (e) {
        console.error("Revert error:", e);
        errorMessage.value = "Connection error. Please try again.";
    } finally {
        reverting.value = false;
        revertingResignationId.value = null;
    }
};

const formatDateTime = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

watch(resigningTL, () => {
    loadStaffCount();
    if (resigningTL.value === replacementTL.value) {
        replacementTL.value = "";
    }
});

watch(filterStartDate, (newStartDate) => {
    if (newStartDate && filterEndDate.value) {
        const start = new Date(newStartDate);
        const end = new Date(filterEndDate.value);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        // If end date is more than 7 days from start, adjust it
        if (diffDays > 7) {
            const newEnd = new Date(start);
            newEnd.setDate(start.getDate() + 7);
            filterEndDate.value = newEnd.toISOString().split('T')[0];
        }
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
    // Load recent resignations will use default date range (1 week ago)
    loadRecentResignations();
});
</script>
