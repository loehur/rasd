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
                        ></path>
                    </svg>
                    Back to Dashboard
                </button>
                <div class="flex items-center justify-between">
                    <div>
                        <h1
                            class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-100 to-slate-300"
                        >
                            Staff List
                        </h1>
                        <p class="text-slate-400 mt-2">
                            View and manage all staff members
                        </p>
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

            <!-- Group Filter Tabs -->
            <div class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-xl p-4 mb-4">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <span class="text-sm font-medium text-slate-300">Filter by Group:</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="filterGroup = ''"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition',
                            filterGroup === ''
                                ? 'bg-blue-600/30 text-blue-300 border border-blue-500/50'
                                : 'bg-slate-800/50 text-slate-400 border border-slate-700 hover:bg-slate-700/50 hover:text-slate-200'
                        ]"
                    >
                        All
                        <span class="ml-1 text-xs opacity-70">({{ staffList.length }})</span>
                    </button>
                    <button
                        v-for="group in availableGroups"
                        :key="group"
                        @click="filterGroup = group"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition',
                            filterGroup === group
                                ? 'bg-emerald-600/30 text-emerald-300 border border-emerald-500/50'
                                : 'bg-slate-800/50 text-slate-400 border border-slate-700 hover:bg-slate-700/50 hover:text-slate-200'
                        ]"
                    >
                        {{ group }}
                        <span class="ml-1 text-xs opacity-70">({{ getGroupCount(group) }})</span>
                    </button>
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
                        placeholder="Search by name, staff ID, or phone..."
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
                <p class="text-slate-400 mt-4">Loading staff data...</p>
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
                    @click="loadStaff"
                    class="mt-4 px-4 py-2 bg-red-600/20 text-red-400 border border-red-600/30 rounded-lg hover:bg-red-600/30 transition"
                >
                    Retry
                </button>
            </div>

            <!-- Staff Table -->
            <div
                v-else-if="filteredStaff.length > 0"
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl overflow-hidden"
            >
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-800/50">
                            <tr>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider">SN</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Area</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider">WFH/Onsite</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider">ID Staff</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Name Staff</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Position</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Superior</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Department</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Hiredate</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Rank</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Device</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider">WL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(staffGroup, teamLeader) in groupedByTeamLeader" :key="teamLeader">
                                <!-- Team Leader Header Row -->
                                <tr class="bg-slate-800/70">
                                    <td colspan="12" class="px-3 py-3 text-left font-bold text-emerald-400 text-sm">
                                        Team Leader: {{ teamLeader }} ({{ staffGroup.length }} staff)
                                    </td>
                                </tr>
                                <!-- Staff Rows -->
                                <tr v-for="(staff, index) in staffGroup" :key="staff.id" class="hover:bg-slate-800/30 transition border-t border-slate-800/50">
                                    <td class="px-3 py-2 text-blue-300 font-semibold">{{ index + 1 }}</td>
                                    <td class="px-3 py-2">{{ staff.area || '-' }}</td>
                                    <td class="px-3 py-2">{{ staff.work_location || '-' }}</td>
                                    <td class="px-3 py-2 text-blue-400 font-mono">{{ staff.staff_id }}</td>
                                    <td class="px-3 py-2">{{ staff.name }}</td>
                                    <td class="px-3 py-2">{{ staff.position || '-' }}</td>
                                    <td class="px-3 py-2">{{ staff.superior || '-' }}</td>
                                    <td class="px-3 py-2">{{ staff.department || '-' }}</td>
                                    <td class="px-3 py-2">{{ staff.hire_date }}</td>
                                    <td class="px-3 py-2">{{ staff.rank || '-' }}</td>
                                    <td class="px-3 py-2">{{ staff.device || '-' }}</td>
                                    <td class="px-3 py-2">{{ staff.warning_letter ? 'WL' : '-' }}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden">
                    <template v-for="(staffGroup, teamLeader) in groupedByTeamLeader" :key="teamLeader">
                        <!-- Team Leader Header -->
                        <div class="bg-slate-800/70 px-4 py-3 border-t border-slate-800/50">
                            <h3 class="font-bold text-emerald-400 text-sm">
                                Team Leader: {{ teamLeader }}
                            </h3>
                            <p class="text-xs text-slate-400 mt-1">{{ staffGroup.length }} staff members</p>
                        </div>
                        <!-- Staff Cards -->
                        <div class="divide-y divide-slate-800/50">
                            <div
                                v-for="(staff, index) in staffGroup"
                                :key="staff.id"
                                @click="viewStaff(staff)"
                                class="p-4 hover:bg-slate-800/30 transition cursor-pointer"
                            >
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-blue-300 font-bold text-sm">{{ index + 1 }}.</span>
                                        <div
                                            class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-emerald-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                        >
                                            {{ getInitials(staff.name) }}
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3
                                            class="text-sm font-semibold text-slate-100 truncate"
                                        >
                                            {{ staff.name }}
                                        </h3>
                                        <p class="text-xs text-slate-400 truncate">
                                            {{ staff.position }}
                                        </p>
                                        <p class="text-xs font-mono text-blue-400 mt-1">
                                            {{ staff.staff_id }}
                                        </p>
                                    </div>
                                    <div>
                                        <span
                                            v-if="staff.ojk_case > 0"
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-300 border border-red-500/30"
                                        >
                                            OJK
                                        </span>
                                        <span
                                            v-else-if="staff.warning_letter"
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-orange-500/20 text-orange-300 border border-orange-500/30"
                                        >
                                            Warning
                                        </span>
                                        <span
                                            v-else
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30"
                                        >
                                            Active
                                        </span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div>
                                        <p class="text-slate-500">Department</p>
                                        <p class="text-slate-300 font-medium truncate">
                                            {{ staff.department }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-slate-500">Phone</p>
                                        <p class="text-slate-300 font-medium truncate">
                                            {{ staff.phone_number }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
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
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                    ></path>
                </svg>
                <p class="text-slate-400 text-lg">No staff found</p>
                <button
                    @click="goToImport"
                    class="mt-4 px-6 py-3 bg-blue-600/20 text-blue-400 border border-blue-600/30 rounded-lg hover:bg-blue-600/30 transition"
                >
                    Import Staff Data
                </button>
            </div>

            <!-- Staff Detail Modal -->
            <div
                v-if="selectedStaff"
                @click="selectedStaff = null"
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
                                {{ getInitials(selectedStaff.name) }}
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-slate-100">
                                    {{ selectedStaff.name }}
                                </h2>
                                <p class="text-slate-400">
                                    {{ selectedStaff.position }}
                                </p>
                            </div>
                        </div>
                        <button
                            @click="selectedStaff = null"
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
                                Staff ID
                            </p>
                            <p class="text-sm text-slate-100 font-mono">
                                {{ selectedStaff.staff_id }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Department
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedStaff.department }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Phone Number
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedStaff.phone_number }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Email
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedStaff.email || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Superior
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedStaff.superior || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Group
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedStaff.group || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Area
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedStaff.area || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Work Location
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedStaff.work_location || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Hire Date
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedStaff.hire_date }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Rank
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedStaff.rank || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Device
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedStaff.device || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Team Leader ID
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedStaff.team_leader_id || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Warning Letter
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedStaff.warning_letter || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                OJK Case
                            </p>
                            <p
                                :class="[
                                    'text-sm font-semibold',
                                    selectedStaff.ojk_case > 0
                                        ? 'text-red-400'
                                        : 'text-emerald-400',
                                ]"
                            >
                                {{ selectedStaff.ojk_case }}
                            </p>
                        </div>
                        <div class="col-span-2" v-if="selectedStaff.notes">
                            <p class="text-xs text-slate-500 uppercase mb-1">
                                Notes
                            </p>
                            <p class="text-sm text-slate-100">
                                {{ selectedStaff.notes }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { API_BASE_URL } from "@/config/api";

const staffList = ref([]);
const loading = ref(false);
const error = ref("");
const searchQuery = ref("");
const filterDepartment = ref("");
const filterPosition = ref("");
const filterGroup = ref(""); // Group filter
const currentPage = ref(1);
const itemsPerPage = ref(5);
const selectedStaff = ref(null);

// Available groups in order
const availableGroups = ['P-1', 'P', 'A1-1', 'A1-2', 'A1-3', 'A2', 'B1', 'B2', 'M2'];

const loadStaff = async () => {
    loading.value = true;
    error.value = "";

    try {
        const response = await fetch(`${API_BASE_URL}/api/staff`, {
            method: "GET",
            headers: {
                Accept: "application/json",
            },
        });

        const data = await response.json();

        if (data.success) {
            staffList.value = data.data;
        } else {
            error.value = "Failed to load staff data";
        }
    } catch (err) {
        error.value =
            "Connection error. Please make sure the server is running.";
        console.error("Load error:", err);
    } finally {
        loading.value = false;
    }
};

const filteredStaff = computed(() => {
    let result = staffList.value;

    // Group filter (priority filter)
    if (filterGroup.value) {
        result = result.filter(
            (staff) => staff.group === filterGroup.value
        );
    }

    // Search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (staff) =>
                staff.name.toLowerCase().includes(query) ||
                staff.staff_id.toLowerCase().includes(query) ||
                staff.phone_number.includes(query)
        );
    }

    // Department filter
    if (filterDepartment.value) {
        result = result.filter(
            (staff) => staff.department === filterDepartment.value
        );
    }

    // Position filter
    if (filterPosition.value) {
        result = result.filter(
            (staff) => staff.position === filterPosition.value
        );
    }

    return result;
});

// Group staff by team leader
const groupedByTeamLeader = computed(() => {
    const groups = {};

    filteredStaff.value.forEach(staff => {
        const teamLeader = staff.superior || 'No Team Leader';
        if (!groups[teamLeader]) {
            groups[teamLeader] = [];
        }
        groups[teamLeader].push(staff);
    });

    return groups;
});

const departments = computed(() => {
    return [...new Set(staffList.value.map((s) => s.department))].filter(
        Boolean
    );
});

const positions = computed(() => {
    return [...new Set(staffList.value.map((s) => s.position))].filter(Boolean);
});

const totalOjkCases = computed(() => {
    return staffList.value.reduce(
        (sum, staff) => sum + (staff.ojk_case || 0),
        0
    );
});

const totalPages = computed(() => {
    return Math.ceil(filteredStaff.value.length / itemsPerPage.value);
});

const paginatedStaff = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredStaff.value.slice(start, end);
});

const visiblePages = computed(() => {
    const pages = [];
    const maxVisible = 5;
    let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
    let end = Math.min(totalPages.value, start + maxVisible - 1);

    if (end - start < maxVisible - 1) {
        start = Math.max(1, end - maxVisible + 1);
    }

    for (let i = start; i <= end; i++) {
        pages.push(i);
    }

    return pages;
});

const getInitials = (name) => {
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .substring(0, 2);
};

const getGroupCount = (group) => {
    return staffList.value.filter(staff => staff.group === group).length;
};

const viewStaff = (staff) => {
    selectedStaff.value = staff;
};

const goBack = () => {
    window.location.href = "/admin/dashboard";
};

const goToImport = () => {
    window.location.href = "/admin/import-staff";
};

const resetFilters = () => {
    searchQuery.value = "";
    filterDepartment.value = "";
    filterPosition.value = "";
    filterGroup.value = "";
    currentPage.value = 1;
};

onMounted(() => {
    loadStaff();
});
</script>
