<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <button
                    @click="goBack"
                    class="mb-4 text-slate-400 hover:text-slate-200 flex items-center gap-2 transition"
                >
                    <svg
                        class="w-5 h-5"
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
                    Back to Dashboard
                </button>
                <div class="flex items-center justify-between">
                    <div>
                        <h1
                            class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-100 to-slate-300"
                        >
                            Team Leader
                        </h1>
                    </div>
                    <div class="flex gap-3">
                        <button
                            @click="goToImport"
                            class="px-4 py-2 bg-blue-600/20 text-blue-400 border border-blue-600/30 rounded-lg hover:bg-blue-600/30 transition flex items-center gap-2"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                ></path>
                            </svg>
                            Import
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-xl p-4 mb-4"
            >
                <div class="flex gap-2">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by name, employee ID, or team..."
                        class="flex-1 px-3 py-2 text-sm bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-blue-500 transition"
                    />
                    <button
                        @click="resetFilters"
                        class="px-3 py-2 text-sm bg-slate-800/50 border border-slate-700 rounded-lg text-slate-400 hover:text-slate-200 hover:border-slate-600 transition flex items-center justify-center"
                        title="Reset search"
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
                                d="M6 18L18 6M6 6l12 12"
                            ></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div
                v-if="loading"
                class="text-center py-12 bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl"
            >
                <div
                    class="inline-block w-12 h-12 border-4 border-blue-500/30 border-t-blue-500 rounded-full animate-spin"
                ></div>
                <p class="text-slate-400 mt-4">Loading team leader data...</p>
            </div>

            <!-- Error State -->
            <div
                v-else-if="error"
                class="bg-red-500/10 border border-red-500/30 rounded-2xl p-6 text-center"
            >
                <svg
                    class="w-12 h-12 text-red-400 mx-auto mb-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                </svg>
                <p class="text-red-300 font-semibold">{{ error }}</p>
                <button
                    @click="fetchTeamLeaders"
                    class="mt-4 px-4 py-2 bg-red-600/20 text-red-400 border border-red-600/30 rounded-lg hover:bg-red-600/30 transition"
                >
                    Retry
                </button>
            </div>

            <!-- Team Leader Table -->
            <div
                v-else-if="filteredTeamLeaders.length > 0"
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl overflow-hidden"
            >
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-800/50">
                            <tr>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Employee ID
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Name
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Position
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Team
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Department
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Area
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Hire Date
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            <tr
                                v-for="tl in filteredTeamLeaders"
                                :key="tl.id"
                                class="hover:bg-slate-800/30 transition"
                            >
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span
                                        class="text-sm font-mono text-blue-400"
                                        >{{ tl.staff_id }}</span
                                    >
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-emerald-500 flex items-center justify-center text-white font-bold text-[11px]"
                                        >
                                            {{ getInitials(tl.name) }}
                                        </div>
                                        <div>
                                            <p
                                                class="text-xs font-semibold text-slate-100"
                                            >
                                                {{ tl.name }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="text-xs text-slate-300">{{
                                        tl.position
                                    }}</span>
                                </td>
                                <td class="px-3 py-2">
                                    <span
                                        class="px-2 py-1 text-[10px] font-medium rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30"
                                    >
                                        {{ tl.team || "-" }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="text-xs text-slate-400">{{
                                        tl.department
                                    }}</span>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="text-xs text-slate-400">{{
                                        tl.area
                                    }}</span>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="text-xs text-slate-400">{{
                                        tl.hire_date
                                    }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden divide-y divide-slate-800/50">
                    <div
                        v-for="tl in filteredTeamLeaders"
                        :key="tl.id"
                        class="p-3 hover:bg-slate-800/30 transition"
                    >
                        <div class="flex items-start gap-3 mb-3">
                            <div
                                class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-emerald-500 flex items-center justify-center text-white font-bold text-xs flex-shrink-0"
                            >
                                {{ getInitials(tl.name) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-xs font-semibold text-slate-100 truncate"
                                >
                                    {{ tl.name }}
                                </h3>
                                <p class="text-xs text-slate-400 truncate">
                                    {{ tl.position }}
                                </p>
                                <p class="text-xs font-mono text-blue-400 mt-1">
                                    {{ tl.staff_id }}
                                </p>
                            </div>
                            <div>
                                <span
                                    class="px-2 py-1 text-[10px] font-medium rounded-full bg-slate-800/30 text-slate-300 border border-slate-700"
                                    >{{ tl.team || "-" }}</span
                                >
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                            <div>
                                <p class="text-slate-500">Department</p>
                                <p class="text-slate-300 font-medium truncate">
                                    {{ tl.department }}
                                </p>
                            </div>
                            <div>
                                <p class="text-slate-500">Area</p>
                                <p class="text-slate-300 font-medium truncate">
                                    {{ tl.area }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="text-center py-12 bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl"
            >
                <svg
                    class="w-16 h-16 text-slate-600 mx-auto mb-4"
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
                <p class="text-slate-400 text-lg">No team leaders found</p>
                <button
                    @click="goToImport"
                    class="mt-4 px-6 py-3 bg-blue-600/20 text-blue-400 border border-blue-600/30 rounded-lg hover:bg-blue-600/30 transition"
                >
                    Import Team Leader Data
                </button>
            </div>

            <!-- Detail Modal -->
            <div
                v-if="selectedTeamLeader"
                @click="selectedTeamLeader = null"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50"
            >
                <div
                    @click.stop
                    class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                >
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-emerald-500 flex items-center justify-center text-white text-2xl font-bold"
                            >
                                {{ getInitials(selectedTeamLeader.name) }}
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-slate-100">
                                    {{ selectedTeamLeader.name }}
                                </h2>
                                <p class="text-slate-400">
                                    {{ selectedTeamLeader.position }}
                                </p>
                            </div>
                        </div>
                        <button
                            @click="selectedTeamLeader = null"
                            class="text-slate-400 hover:text-slate-200 transition"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                ></path>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Employee ID
                            </p>
                            <p class="text-sm text-slate-100 font-mono">
                                {{ selectedTeamLeader.staff_id }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Team
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedTeamLeader.team || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Department
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedTeamLeader.department || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Area
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedTeamLeader.area || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Hire Date
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedTeamLeader.hire_date || "-" }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { API_BASE_URL } from "@/config/api";

const teamLeaders = ref([]);
const loading = ref(false);
const error = ref("");

// UI: search
const searchQuery = ref("");
const selectedTeamLeader = ref(null);

const filteredTeamLeaders = computed(() => {
    if (!searchQuery.value) return teamLeaders.value;
    const q = searchQuery.value.toLowerCase();
    return teamLeaders.value.filter((tl) => {
        return (
            (tl.name || "").toLowerCase().includes(q) ||
            (tl.staff_id || "").toLowerCase().includes(q) ||
            (tl.team || "").toLowerCase().includes(q)
        );
    });
});

// No pagination: show all filtered items

const getInitials = (name) => {
    if (!name) return "TL";
    return name
        .split(" ")
        .map((n) => n[0] || "")
        .join("")
        .toUpperCase()
        .substring(0, 2);
};

const viewTeamLeader = (tl) => {
    selectedTeamLeader.value = tl;
};

const resetFilters = () => {
    searchQuery.value = "";
};

const fetchTeamLeaders = async () => {
    loading.value = true;
    error.value = "";

    try {
        const token = localStorage.getItem("auth_token");
        const res = await fetch(`${API_BASE_URL}/api/team-leaders`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        const data = await res.json();
        if (data.success && Array.isArray(data.data)) {
            teamLeaders.value = data.data;
        } else if (Array.isArray(data)) {
            teamLeaders.value = data;
        } else {
            teamLeaders.value = [];
        }
    } catch (e) {
        error.value =
            "Connection error. Please make sure the server is running.";
        console.error("Load error:", e);
        teamLeaders.value = [];
    } finally {
        loading.value = false;
    }
};

onMounted(fetchTeamLeaders);

const goBack = () => {
    window.location.href = "/admin/dashboard";
};

const goToImport = () => {
    window.location.href = "/admin/import-team-leader";
};
</script>
