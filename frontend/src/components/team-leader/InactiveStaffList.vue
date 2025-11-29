<template>
    <div
        class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-gray-100 p-6"
    >
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <button
                    @click="goBack"
                    class="mb-4 text-gray-600 hover:text-gray-800 flex items-center gap-2 transition"
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
                            class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-gray-600 via-slate-600 to-gray-700"
                        >
                            Inactive Staff
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Staff members who are no longer active
                        </p>
                    </div>
                </div>
            </div>

            <!-- Month Filter -->
            <div
                class="bg-white shadow-sm border border-gray-200 rounded-xl p-4 mb-4"
            >
                <div class="flex items-center gap-2 mb-3">
                    <svg
                        class="w-5 h-5 text-gray-600"
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
                    <span class="text-sm font-medium text-gray-700"
                        >Filter by Month:</span
                    >
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="filterMonth = ''"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition',
                            filterMonth === ''
                                ? 'bg-gray-600 text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                        ]"
                    >
                        All Months
                        <span class="ml-1 text-xs opacity-70"
                            >({{ resignations.length }})</span
                        >
                    </button>
                    <button
                        v-for="month in availableMonths"
                        :key="month"
                        @click="filterMonth = month"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition',
                            filterMonth === month
                                ? 'bg-slate-600 text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                        ]"
                    >
                        {{ formatMonthLabel(month) }}
                        <span class="ml-1 text-xs opacity-70"
                            >({{ getMonthCount(month) }})</span
                        >
                    </button>
                </div>
            </div>

            <!-- Search and Filter -->
            <div
                class="bg-white shadow-sm border border-gray-200 rounded-xl p-4 mb-4"
            >
                <div class="flex gap-2">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by name, staff ID, or phone..."
                        class="flex-1 px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
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
                class="text-center py-12 bg-white shadow-sm border border-gray-200 rounded-2xl"
            >
                <div
                    class="inline-block w-12 h-12 border-4 border-gray-200 border-t-gray-600 rounded-full animate-spin"
                ></div>
                <p class="text-gray-600 mt-4">Loading inactive staff data...</p>
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
                v-else-if="filteredResignations.length > 0"
                class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden"
            >
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead
                            class="bg-gradient-to-r from-gray-50 to-slate-50"
                        >
                            <tr>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    Report Day
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    ID Staff
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    Name Staff
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    Position
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    Superior
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    Last Working Day
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    Type
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-gray-700 uppercase tracking-wider"
                                >
                                    Reason
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="resignation in filteredResignations"
                                :key="resignation.id"
                                class="hover:bg-gray-50/50 transition border-t border-gray-200"
                            >
                                <td
                                    class="px-3 py-2 text-gray-600 font-semibold"
                                >
                                    {{ formatDate(resignation.report_day) }}
                                </td>
                                <td
                                    class="px-3 py-2 text-gray-600 font-mono font-semibold"
                                >
                                    {{ resignation.staff_id }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ resignation.staff_name }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ resignation.staff_position || "-" }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ resignation.staff_superior || "-" }}
                                </td>
                                <td class="px-3 py-2">
                                    {{
                                        formatDate(resignation.last_working_day)
                                    }}
                                </td>
                                <td class="px-3 py-2">
                                    <span
                                        :class="[
                                            'px-2 py-1 text-xs font-medium rounded-full',
                                            resignation.resignation_type ===
                                            'voluntary'
                                                ? 'bg-blue-100 text-blue-700 border border-blue-300'
                                                : 'bg-orange-100 text-orange-700 border border-orange-300',
                                        ]"
                                    >
                                        {{
                                            resignation.resignation_type ===
                                            "voluntary"
                                                ? "Voluntary"
                                                : "Involuntary"
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="px-3 py-2 max-w-xs truncate"
                                    :title="resignation.reason"
                                >
                                    {{ resignation.reason }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden divide-y divide-gray-200">
                    <div
                        v-for="resignation in filteredResignations"
                        :key="resignation.id"
                        class="p-4 hover:bg-gray-50/50 transition"
                    >
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-gray-900">
                                    {{ resignation.staff_name }}
                                </h3>
                                <p class="text-xs font-mono text-gray-600 mt-1">
                                    {{ resignation.staff_id }}
                                </p>
                            </div>
                            <span
                                :class="[
                                    'px-2 py-1 text-xs font-medium rounded-full',
                                    resignation.resignation_type === 'voluntary'
                                        ? 'bg-blue-100 text-blue-700 border border-blue-300'
                                        : 'bg-orange-100 text-orange-700 border border-orange-300',
                                ]"
                            >
                                {{
                                    resignation.resignation_type === "voluntary"
                                        ? "Voluntary"
                                        : "Involuntary"
                                }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <p class="text-gray-500">Report Day</p>
                                <p class="text-gray-700 font-medium">
                                    {{ formatDate(resignation.report_day) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500">Last Working Day</p>
                                <p class="text-gray-700 font-medium">
                                    {{
                                        formatDate(resignation.last_working_day)
                                    }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500">Position</p>
                                <p class="text-gray-700 font-medium truncate">
                                    {{ resignation.staff_position || "-" }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500">Superior</p>
                                <p class="text-gray-700 font-medium truncate">
                                    {{ resignation.staff_superior || "-" }}
                                </p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-500">Reason</p>
                                <p
                                    class="text-gray-700 text-xs mt-1 line-clamp-2"
                                >
                                    {{ resignation.reason }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="text-center py-12 bg-white shadow-sm border border-gray-200 rounded-2xl"
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
                <p class="text-gray-600 text-lg">
                    No resignation records found
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { API_BASE_URL } from "@/config/api";

const resignations = ref([]);
const loading = ref(false);
const error = ref("");
const searchQuery = ref("");
const currentDate = new Date();
const filterMonth = ref(`${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}`);

// Get available months from resignations
const availableMonths = computed(() => {
    const months = resignations.value.map((r) => {
        const date = new Date(r.report_day);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        return `${year}-${month}`;
    });
    return [...new Set(months)].sort((a, b) => b.localeCompare(a)); // Descending order
});

const loadResignations = async () => {
    loading.value = true;
    error.value = "";

    try {
        const token = localStorage.getItem("tl_auth_token");
        if (!token) {
            window.location.href = "/";
            return;
        }

        // Fetch all resignations - backend will handle pagination, we get all for now
        const response = await fetch(
            `${API_BASE_URL}/api/resignations?per_page=1000`,
            {
                method: "GET",
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                },
            }
        );

        const data = await response.json();

        if (data.success) {
            // Get resignations and enrich with staff details from backend
            resignations.value = data.data || [];

            // Sort by report_day descending
            resignations.value.sort((a, b) => {
                return new Date(b.report_day) - new Date(a.report_day);
            });
        } else {
            error.value = "Failed to load resignation data";
        }
    } catch (err) {
        error.value = "Connection error. Please make sure you're logged in.";
        console.error("Load error:", err);
    } finally {
        loading.value = false;
    }
};

const filteredResignations = computed(() => {
    let result = resignations.value;

    // Month filter
    if (filterMonth.value) {
        result = result.filter((resignation) => {
            const date = new Date(resignation.report_day);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const resignationMonth = `${year}-${month}`;
            return resignationMonth === filterMonth.value;
        });
    }

    // Search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (resignation) =>
                resignation.staff_name?.toLowerCase().includes(query) ||
                resignation.staff_id?.toLowerCase().includes(query) ||
                resignation.reason?.toLowerCase().includes(query)
        );
    }

    return result;
});

const getMonthCount = (month) => {
    return resignations.value.filter((resignation) => {
        const date = new Date(resignation.report_day);
        const year = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const resignationMonth = `${year}-${m}`;
        return resignationMonth === month;
    }).length;
};

const formatMonthLabel = (month) => {
    if (!month) return '';
    const [year, m] = month.split('-');
    const date = new Date(year, parseInt(m) - 1);
    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
};

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const goBack = () => {
    window.location.href = "/team-leader/dashboard";
};

const resetFilters = () => {
    searchQuery.value = "";
    filterMonth.value = "";
};

onMounted(() => {
    // Check auth
    const token = localStorage.getItem("tl_auth_token");
    if (!token) {
        window.location.href = "/";
        return;
    }

    loadResignations();
});
</script>
