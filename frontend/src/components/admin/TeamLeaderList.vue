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
                    <table class="w-full">
                        <thead class="bg-slate-800/50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Employee ID
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Name
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Position
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Team
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Department
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Area
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Hire Date
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            <tr
                                v-for="tl in paginatedTeamLeaders"
                                :key="tl.id"
                                class="hover:bg-slate-800/30 transition"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="text-sm font-mono text-blue-400"
                                        >{{ tl.employee_id }}</span
                                    >
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-emerald-500 flex items-center justify-center text-white font-bold"
                                        >
                                            {{ getInitials(tl.name) }}
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-semibold text-slate-100"
                                            >
                                                {{ tl.name }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-300">{{
                                        tl.position
                                    }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 text-xs font-medium rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30"
                                    >
                                        {{ tl.team || "-" }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-400">{{
                                        tl.department
                                    }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-400">{{
                                        tl.area
                                    }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-400">{{
                                        tl.hire_date
                                    }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="viewTeamLeader(tl)"
                                            class="text-blue-400 hover:text-blue-300 transition"
                                            title="View Details"
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
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                ></path>
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                ></path>
                                            </svg>
                                        </button>
                                        <button
                                            @click="editTeamLeader(tl)"
                                            class="text-emerald-400 hover:text-emerald-300 transition"
                                            title="Edit Team Leader"
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
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                ></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden divide-y divide-slate-800/50">
                    <div
                        v-for="tl in paginatedTeamLeaders"
                        :key="tl.id"
                        class="p-4 hover:bg-slate-800/30 transition"
                    >
                        <div class="flex items-start gap-3 mb-3">
                            <div
                                class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-emerald-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                            >
                                {{ getInitials(tl.name) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-sm font-semibold text-slate-100 truncate"
                                >
                                    {{ tl.name }}
                                </h3>
                                <p class="text-xs text-slate-400 truncate">
                                    {{ tl.position }}
                                </p>
                                <p class="text-xs font-mono text-blue-400 mt-1">
                                    {{ tl.employee_id }}
                                </p>
                            </div>
                            <div>
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-full bg-slate-800/30 text-slate-300 border border-slate-700"
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
                        <!-- Action Buttons for Mobile -->
                        <div
                            class="flex gap-2 pt-3 border-t border-slate-800/50"
                        >
                            <button
                                @click="viewTeamLeader(tl)"
                                class="flex-1 px-3 py-2 bg-blue-600/20 text-blue-400 border border-blue-600/30 rounded-lg hover:bg-blue-600/30 transition flex items-center justify-center gap-2 text-sm"
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
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    ></path>
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                    ></path>
                                </svg>
                                View
                            </button>
                            <button
                                @click="editTeamLeader(tl)"
                                class="flex-1 px-3 py-2 bg-emerald-600/20 text-emerald-400 border border-emerald-600/30 rounded-lg hover:bg-emerald-600/30 transition flex items-center justify-center gap-2 text-sm"
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
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                    ></path>
                                </svg>
                                Edit
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div
                    v-if="totalPages > 1"
                    class="px-6 py-4 bg-slate-800/30 flex items-center justify-between"
                >
                    <p class="text-sm text-slate-400">
                        Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to
                        {{
                            Math.min(
                                currentPage * itemsPerPage,
                                filteredTeamLeaders.length
                            )
                        }}
                        of {{ filteredTeamLeaders.length }} team leaders
                    </p>
                    <div class="flex gap-2">
                        <button
                            @click="currentPage--"
                            :disabled="currentPage === 1"
                            class="px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-300 hover:bg-slate-700/50 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Previous
                        </button>
                        <button
                            v-for="page in visiblePages"
                            :key="page"
                            @click="currentPage = page"
                            :class="[
                                'px-4 py-2 border rounded-lg transition',
                                currentPage === page
                                    ? 'bg-blue-600/30 border-blue-500 text-blue-300'
                                    : 'bg-slate-800/50 border-slate-700 text-slate-300 hover:bg-slate-700/50',
                            ]"
                        >
                            {{ page }}
                        </button>
                        <button
                            @click="currentPage++"
                            :disabled="currentPage === totalPages"
                            class="px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-300 hover:bg-slate-700/50 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Next
                        </button>
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
                                {{ selectedTeamLeader.employee_id }}
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

// UI: search & pagination
const searchQuery = ref("");
const currentPage = ref(1);
const itemsPerPage = ref(5);
const selectedTeamLeader = ref(null);

const filteredTeamLeaders = computed(() => {
    if (!searchQuery.value) return teamLeaders.value;
    const q = searchQuery.value.toLowerCase();
    return teamLeaders.value.filter((tl) => {
        return (
            (tl.name || "").toLowerCase().includes(q) ||
            (tl.employee_id || "").toLowerCase().includes(q) ||
            (tl.team || "").toLowerCase().includes(q)
        );
    });
});

const totalPages = computed(() => {
    return Math.max(
        1,
        Math.ceil(filteredTeamLeaders.value.length / itemsPerPage.value)
    );
});

const paginatedTeamLeaders = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    return filteredTeamLeaders.value.slice(start, start + itemsPerPage.value);
});

const visiblePages = computed(() => {
    const pages = [];
    const maxVisible = 5;
    let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
    let end = Math.min(totalPages.value, start + maxVisible - 1);
    if (end - start < maxVisible - 1) {
        start = Math.max(1, end - maxVisible + 1);
    }
    for (let i = start; i <= end; i++) pages.push(i);
    return pages;
});

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
    currentPage.value = 1;
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

const editTeamLeader = (teamLeader) => {
    window.location.href = `/admin/team-leader/edit/${teamLeader.employee_id}`;
};

onMounted(fetchTeamLeaders);

const goBack = () => {
    window.location.href = "/admin/dashboard";
};

const goToImport = () => {
    window.location.href = "/admin/import-team-leader";
};
</script>
