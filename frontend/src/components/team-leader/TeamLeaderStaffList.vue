<template>
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-6"
    >
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <button
                    @click="goBack"
                    class="mb-4 text-indigo-600 hover:text-indigo-800 flex items-center gap-2 transition"
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
                            class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700"
                        >
                            All Staff
                        </h1>
                    </div>
                </div>
            </div>

            <!-- Group Filter Tabs -->
            <div
                class="bg-white shadow-sm border border-indigo-100 rounded-xl p-4 mb-4"
            >
                <div class="flex items-center gap-2 mb-3">
                    <svg
                        class="w-5 h-5 text-indigo-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                        ></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-700"
                        >Filter by Group:</span
                    >
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="filterGroup = ''"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition',
                            filterGroup === ''
                                ? 'bg-indigo-600 text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                        ]"
                    >
                        All
                        <span class="ml-1 text-xs opacity-70"
                            >({{ staffList.length }})</span
                        >
                    </button>
                    <button
                        v-for="group in availableGroups"
                        :key="group"
                        @click="filterGroup = group"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition',
                            filterGroup === group
                                ? 'bg-purple-600 text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                        ]"
                    >
                        {{ group }}
                        <span class="ml-1 text-xs opacity-70"
                            >({{ getGroupCount(group) }})</span
                        >
                    </button>
                </div>
            </div>

            <!-- Search and Filter -->
            <div
                class="bg-white shadow-sm border border-indigo-100 rounded-xl p-4 mb-4"
            >
                <div class="flex gap-2">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by name, staff ID, or phone..."
                        class="flex-1 px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                    />
                    <button
                        @click="resetFilters"
                        class="px-3 py-2 text-sm bg-gray-100 border border-gray-300 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-200 transition flex items-center justify-center"
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
                class="text-center py-12 bg-white shadow-sm border border-indigo-100 rounded-2xl"
            >
                <div
                    class="inline-block w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"
                ></div>
                <p class="text-gray-600 mt-4">Loading staff data...</p>
            </div>

            <!-- Error State -->
            <div
                v-else-if="error"
                class="bg-red-50 border border-red-200 rounded-2xl p-6 text-center"
            >
                <svg
                    class="w-12 h-12 text-red-500 mx-auto mb-4"
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
                <p class="text-red-700 font-semibold">{{ error }}</p>
                <button
                    @click="loadStaff"
                    class="mt-4 px-4 py-2 bg-red-100 text-red-700 border border-red-300 rounded-lg hover:bg-red-200 transition"
                >
                    Retry
                </button>
            </div>

            <!-- Staff Table -->
            <div
                v-else-if="filteredStaff.length > 0"
                class="bg-white shadow-sm border border-indigo-100 rounded-2xl overflow-hidden"
            >
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead
                            class="bg-gradient-to-r from-indigo-50 to-purple-50"
                        >
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    Staff ID
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    Name
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    Position
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    Department
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    Phone
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    Work Location
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr
                                v-for="staff in paginatedStaff"
                                :key="staff.id"
                                class="hover:bg-indigo-50/50 transition"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="text-sm font-mono text-indigo-600 font-semibold"
                                        >{{ staff.staff_id }}</span
                                    >
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold"
                                        >
                                            {{ getInitials(staff.name) }}
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-semibold text-gray-900"
                                            >
                                                {{ staff.name }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ staff.email || "No email" }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-700">{{
                                        staff.position
                                    }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700 border border-purple-300"
                                    >
                                        {{ staff.department }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600">{{
                                        staff.phone_number
                                    }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600">{{
                                        staff.work_location || "-"
                                    }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        v-if="staff.ojk_case > 0"
                                        class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700 border border-red-300"
                                    >
                                        OJK Case
                                    </span>
                                    <span
                                        v-else-if="staff.warning_letter"
                                        class="px-3 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-700 border border-orange-300"
                                    >
                                        Warning
                                    </span>
                                    <span
                                        v-else
                                        class="px-3 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700 border border-emerald-300"
                                    >
                                        Active
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button
                                        @click="viewStaff(staff)"
                                        class="text-indigo-600 hover:text-indigo-800 transition"
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
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden divide-y divide-gray-200">
                    <div
                        v-for="staff in paginatedStaff"
                        :key="staff.id"
                        @click="viewStaff(staff)"
                        class="p-4 hover:bg-indigo-50/50 transition cursor-pointer"
                    >
                        <div class="flex items-start gap-3 mb-3">
                            <div
                                class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                            >
                                {{ getInitials(staff.name) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-sm font-semibold text-gray-900 truncate"
                                >
                                    {{ staff.name }}
                                </h3>
                                <p class="text-xs text-gray-600 truncate">
                                    {{ staff.position }}
                                </p>
                                <p
                                    class="text-xs font-mono text-indigo-600 mt-1 font-semibold"
                                >
                                    {{ staff.staff_id }}
                                </p>
                            </div>
                            <div>
                                <span
                                    v-if="staff.ojk_case > 0"
                                    class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700 border border-red-300"
                                >
                                    OJK
                                </span>
                                <span
                                    v-else-if="staff.warning_letter"
                                    class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-700 border border-orange-300"
                                >
                                    Warning
                                </span>
                                <span
                                    v-else
                                    class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700 border border-emerald-300"
                                >
                                    Active
                                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <p class="text-gray-500">Department</p>
                                <p class="text-gray-700 font-medium truncate">
                                    {{ staff.department }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500">Phone</p>
                                <p class="text-gray-700 font-medium truncate">
                                    {{ staff.phone_number }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div
                    v-if="totalPages > 1"
                    class="px-6 py-4 bg-gray-50 flex items-center justify-between border-t border-gray-200"
                >
                    <p class="text-sm text-gray-600">
                        Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to
                        {{
                            Math.min(
                                currentPage * itemsPerPage,
                                filteredStaff.length
                            )
                        }}
                        of {{ filteredStaff.length }} staff
                    </p>
                    <div class="flex gap-2">
                        <button
                            @click="currentPage--"
                            :disabled="currentPage === 1"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed"
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
                                    ? 'bg-indigo-600 border-indigo-600 text-white'
                                    : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50',
                            ]"
                        >
                            {{ page }}
                        </button>
                        <button
                            @click="currentPage++"
                            :disabled="currentPage === totalPages"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="text-center py-12 bg-white shadow-sm border border-indigo-100 rounded-2xl"
            >
                <svg
                    class="w-16 h-16 text-gray-400 mx-auto mb-4"
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
                <p class="text-gray-600 text-lg">No staff found</p>
            </div>

            <!-- Staff Detail Modal -->
            <div
                v-if="selectedStaff"
                @click="selectedStaff = null"
                class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50"
            >
                <div
                    @click.stop
                    class="bg-white border border-indigo-100 rounded-2xl p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-xl"
                >
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-2xl font-bold"
                            >
                                {{ getInitials(selectedStaff.name) }}
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">
                                    {{ selectedStaff.name }}
                                </h2>
                                <p class="text-gray-600">
                                    {{ selectedStaff.position }}
                                </p>
                            </div>
                        </div>
                        <button
                            @click="selectedStaff = null"
                            class="text-gray-400 hover:text-gray-700 transition"
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
                            <p class="text-xs text-gray-500 uppercase mb-1">
                                Staff ID
                            </p>
                            <p class="text-sm text-gray-900 font-mono">
                                {{ selectedStaff.staff_id }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">
                                Department
                            </p>
                            <p class="text-sm text-gray-900">
                                {{ selectedStaff.department }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">
                                Phone Number
                            </p>
                            <p class="text-sm text-gray-900">
                                {{ selectedStaff.phone_number }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">
                                Email
                            </p>
                            <p class="text-sm text-gray-900">
                                {{ selectedStaff.email || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">
                                Superior
                            </p>
                            <p class="text-sm text-gray-900">
                                {{ selectedStaff.superior || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">
                                Group
                            </p>
                            <p class="text-sm text-gray-900">
                                {{ selectedStaff.group || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">
                                Area
                            </p>
                            <p class="text-sm text-gray-900">
                                {{ selectedStaff.area || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">
                                Work Location
                            </p>
                            <p class="text-sm text-gray-900">
                                {{ selectedStaff.work_location || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">
                                Hire Date
                            </p>
                            <p class="text-sm text-gray-900">
                                {{ selectedStaff.hire_date }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">
                                Rank
                            </p>
                            <p class="text-sm text-gray-900">
                                {{ selectedStaff.rank || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">
                                Device
                            </p>
                            <p class="text-sm text-gray-900">
                                {{ selectedStaff.device || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">
                                Team Leader ID
                            </p>
                            <p class="text-sm text-gray-900">
                                {{ selectedStaff.team_leader_id || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">
                                Warning Letter
                            </p>
                            <p class="text-sm text-gray-900">
                                {{ selectedStaff.warning_letter || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">
                                OJK Case
                            </p>
                            <p
                                :class="[
                                    'text-sm font-semibold',
                                    selectedStaff.ojk_case > 0
                                        ? 'text-red-600'
                                        : 'text-emerald-600',
                                ]"
                            >
                                {{ selectedStaff.ojk_case }}
                            </p>
                        </div>
                        <div class="col-span-2" v-if="selectedStaff.notes">
                            <p class="text-xs text-gray-500 uppercase mb-1">
                                Notes
                            </p>
                            <p class="text-sm text-gray-900">
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
const availableGroups = [
    "P-1",
    "P",
    "A1-1",
    "A1-2",
    "A1-3",
    "A2",
    "B1",
    "B2",
    "M2",
];

const loadStaff = async () => {
    loading.value = true;
    error.value = "";

    try {
        const token = localStorage.getItem("tl_auth_token");
        if (!token) {
            window.location.href = "/";
            return;
        }

        // Fetch all staff data (same as Admin)
        const response = await fetch(`${API_BASE_URL}/api/staff`, {
            method: "GET",
            headers: {
                Accept: "application/json",
            },
        });

        const data = await response.json();

        if (data.success) {
            staffList.value = data.data || [];
        } else {
            error.value = "Failed to load staff data";
        }
    } catch (err) {
        error.value = "Connection error. Please make sure you're logged in.";
        console.error("Load error:", err);
    } finally {
        loading.value = false;
    }
};

const filteredStaff = computed(() => {
    let result = staffList.value;

    // Group filter (priority filter)
    if (filterGroup.value) {
        result = result.filter((staff) => staff.group === filterGroup.value);
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
    return staffList.value.filter((staff) => staff.group === group).length;
};

const viewStaff = (staff) => {
    selectedStaff.value = staff;
};

const goBack = () => {
    window.location.href = "/team-leader/dashboard";
};

const resetFilters = () => {
    searchQuery.value = "";
    filterDepartment.value = "";
    filterPosition.value = "";
    filterGroup.value = "";
    currentPage.value = 1;
};

onMounted(() => {
    // Check auth
    const token = localStorage.getItem("tl_auth_token");
    if (!token) {
        window.location.href = "/";
        return;
    }

    loadStaff();
});
</script>
