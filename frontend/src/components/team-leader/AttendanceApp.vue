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
                        />
                    </svg>
                    Back to Dashboard
                </button>
                <div class="flex items-center justify-between">
                    <div>
                        <h1
                            class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700"
                        >
                            Staff Attendance
                        </h1>
                    </div>
                </div>
            </div>
            <!-- Action Button -->
            <div class="mb-6">
                <button
                    @click="openCreateModal"
                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-purple-700"
                >
                    <svg
                        class="w-5 h-5 inline-block mr-2 -mt-1"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4"
                        ></path>
                    </svg>
                    Record Attendance
                </button>
            </div>

            <!-- Attendance List -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
            >
                <div
                    class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50"
                >
                    <h2 class="text-xl font-bold text-gray-900">
                        All Staff Attendance Records
                    </h2>
                </div>

                <div class="p-6">
                    <!-- Filter Date -->
                    <div class="mb-4 flex items-center gap-3">
                        <label class="text-sm font-medium text-gray-700"
                            >Filter tanggal (Report Day):</label
                        >
                        <input
                            type="date"
                            v-model="filterDate"
                            @change="fetchAttendances(1)"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        />
                    </div>

                    <!-- Loading State -->
                    <div v-if="loading" class="text-center py-12">
                        <div
                            class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"
                        ></div>
                        <p class="mt-4 text-gray-600">
                            Loading attendance records...
                        </p>
                    </div>

                    <!-- Error State -->
                    <div v-else-if="error" class="text-center py-12">
                        <svg
                            class="w-16 h-16 text-red-400 mx-auto mb-4"
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
                        <p class="text-gray-600">{{ error }}</p>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-else-if="attendances.length === 0"
                        class="text-center py-12"
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
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                            ></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            No Attendance Records
                        </h3>
                        <p class="text-gray-600">
                            Start by creating a new attendance record.
                        </p>
                    </div>

                    <!-- Attendance Table -->
                    <div v-else>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Employee
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Position
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Department
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            WFH/Onsite
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Device
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Report Day
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr
                                        v-for="attendance in attendances"
                                        :key="attendance.id"
                                        class="hover:bg-gray-50"
                                    >
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ attendance.name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ attendance.staff_id }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{ attendance.position }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{ attendance.department }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="[
                                                    'px-2 py-1 text-xs font-semibold rounded-full',
                                                    attendance.work_status ===
                                                    'WFH'
                                                        ? 'bg-blue-100 text-blue-800'
                                                        : 'bg-green-100 text-green-800',
                                                ]"
                                            >
                                                {{ attendance.work_status }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{ attendance.device }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{
                                                formatDate(
                                                    attendance.report_day
                                                )
                                            }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{ attendance.status_code }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2"
                                        >
                                            <button
                                                @click="
                                                    viewAttendance(attendance)
                                                "
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div
                            v-if="pagination.last_page > 1"
                            class="mt-4 flex items-center justify-between border-t border-gray-200 pt-4"
                        >
                            <div class="text-sm text-gray-700">
                                Showing {{ pagination.from }} to
                                {{ pagination.to }} of
                                {{ pagination.total }} results
                            </div>
                            <div class="flex space-x-2">
                                <button
                                    @click="
                                        goToPage(pagination.current_page - 1)
                                    "
                                    :disabled="pagination.current_page === 1"
                                    class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Previous
                                </button>
                                <button
                                    v-for="page in visiblePages"
                                    :key="page"
                                    @click="goToPage(page)"
                                    :class="[
                                        'px-3 py-1 border rounded-md text-sm font-medium',
                                        page === pagination.current_page
                                            ? 'bg-indigo-600 text-white border-indigo-600'
                                            : 'border-gray-300 text-gray-700 hover:bg-gray-50',
                                    ]"
                                >
                                    {{ page }}
                                </button>
                                <button
                                    @click="
                                        goToPage(pagination.current_page + 1)
                                    "
                                    :disabled="
                                        pagination.current_page ===
                                        pagination.last_page
                                    "
                                    class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full my-8">
                <div
                    class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50"
                >
                    <h3 class="text-xl font-bold text-gray-900">
                        Record Attendance
                    </h3>
                </div>

                <div class="p-6 max-h-[calc(90vh-200px)] overflow-y-auto">
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Employee Selection -->
                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Employee *</label
                                >
                                <select
                                    v-model="formData.staff_id"
                                    @change="onStaffChange"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">Select Employee</option>
                                    <option
                                        v-for="staff in staffList"
                                        :key="staff.staff_id"
                                        :value="staff.staff_id"
                                    >
                                        {{ staff.name }} - {{ staff.staff_id }}
                                    </option>
                                </select>
                            </div>

                            <!-- Hidden fields (auto-filled from staff data) -->
                            <input v-model="formData.position" type="hidden" />
                            <input v-model="formData.superior" type="hidden" />
                            <input
                                v-model="formData.department"
                                type="hidden"
                            />
                            <input v-model="formData.hire_date" type="hidden" />
                            <input v-model="formData.rank" type="hidden" />
                            <input v-model="formData.device" type="hidden" />
                            <input
                                v-model="formData.work_status"
                                type="hidden"
                            />
                            <input v-model="formData.group" type="hidden" />

                            <!-- Report Day -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Report Day *</label
                                >
                                <input
                                    v-model="formData.report_day"
                                    type="date"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>

                            <!-- Ranking Intervals -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Ranking/Intervals *</label
                                >
                                <select
                                    v-model="formData.ranking_intervals"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">Select Ranking</option>
                                    <option value="Top 5%">Top 5%</option>
                                    <option value="5% ~ 25%">5% ~ 25%</option>
                                    <option value="25% ~ 50%">25% ~ 50%</option>
                                    <option value="50% ~ 70%">50% ~ 70%</option>
                                    <option value="70% ~ 90%">70% ~ 90%</option>
                                    <option value="Bottom 10%">
                                        Bottom 10%
                                    </option>
                                </select>
                            </div>

                            <!-- Status & Code -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Status & Code *</label
                                >
                                <select
                                    v-model="formData.status_code"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">Select Status</option>
                                    <option value="AFPI CERTIFICATION">
                                        AFPI CERTIFICATION
                                    </option>
                                    <option value="Absent (AB4)">
                                        Absent (AB4)
                                    </option>
                                    <option value="Absent (AB8)">
                                        Absent (AB8)
                                    </option>
                                    <option value="Annual Leave (A8)">
                                        Annual Leave (A8)
                                    </option>
                                    <option value="FAILED IT CONFIG">
                                        FAILED IT CONFIG
                                    </option>
                                    <option value="Half Day - Sick (S4)">
                                        Half Day - Sick (S4)
                                    </option>
                                    <option value="Late">Late</option>
                                    <option value="Leave (C4)">
                                        Leave (C4)
                                    </option>
                                    <option value="Leave (C8)">
                                        Leave (C8)
                                    </option>
                                    <option value="Paid Leave (P)">
                                        Paid Leave (P)
                                    </option>
                                    <option value="Resignation">
                                        Resignation
                                    </option>
                                    <option value="Sick (S8)">Sick (S8)</option>
                                </select>
                            </div>

                            <!-- Proof Upload -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Proof (Image)</label
                                >
                                <input
                                    type="file"
                                    @change="onFileChange"
                                    accept="image/jpeg,image/jpg,image/png"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                />
                                <p class="text-xs text-gray-500 mt-1">
                                    Upload proof of attendance (optional, max
                                    10MB, will be compressed to 200KB)
                                </p>
                            </div>

                            <!-- Reason for Resign -->
                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Reason for Resign *</label
                                >
                                <textarea
                                    v-model="formData.reason_for_resign"
                                    rows="3"
                                    required
                                    placeholder="Enter reason"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                ></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <div
                    class="p-6 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3"
                >
                    <button
                        @click="closeModal"
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="submitForm"
                        :disabled="submitting"
                        class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg hover:from-indigo-700 hover:to-purple-700 disabled:opacity-50"
                    >
                        {{ submitting ? "Submitting..." : "Submit" }}
                    </button>
                </div>
            </div>
        </div>

        <!-- View Modal -->
        <div
            v-if="showViewModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full my-8">
                <div
                    class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50"
                >
                    <h3 class="text-xl font-bold text-gray-900">
                        Attendance Details
                    </h3>
                </div>

                <div class="p-6 max-h-[calc(90vh-200px)] overflow-y-auto">
                    <div v-if="viewData" class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="font-semibold">Employee:</span>
                            {{ viewData.name }} ({{ viewData.staff_id }})
                        </div>
                        <div>
                            <span class="font-semibold">Position:</span>
                            {{ viewData.position }}
                        </div>
                        <div>
                            <span class="font-semibold">Department:</span>
                            {{ viewData.department }}
                        </div>
                        <div>
                            <span class="font-semibold">Superior:</span>
                            {{ viewData.superior || "-" }}
                        </div>
                        <div>
                            <span class="font-semibold">Hire Date:</span>
                            {{ formatDate(viewData.hire_date) }}
                        </div>
                        <div>
                            <span class="font-semibold">Rank:</span>
                            {{ viewData.rank || "-" }}
                        </div>
                        <div>
                            <span class="font-semibold">Device:</span>
                            {{ viewData.device }}
                        </div>
                        <div>
                            <span class="font-semibold">WFH/Onsite:</span>
                            {{ viewData.work_status }}
                        </div>
                        <div>
                            <span class="font-semibold">Report Day:</span>
                            {{ formatDate(viewData.report_day) }}
                        </div>
                        <div>
                            <span class="font-semibold"
                                >Ranking/Intervals:</span
                            >
                            {{ viewData.ranking_intervals || "-" }}
                        </div>
                        <div>
                            <span class="font-semibold">Group:</span>
                            {{ viewData.group || "-" }}
                        </div>
                        <div>
                            <span class="font-semibold">Status & Code:</span>
                            {{ viewData.status_code }}
                        </div>
                        <div v-if="viewData.proof" class="col-span-2">
                            <span class="font-semibold">Proof:</span><br />
                            <img
                                :src="`/${viewData.proof}`"
                                alt="Proof"
                                class="mt-2 max-w-md rounded-lg border"
                            />
                        </div>
                        <div class="col-span-2">
                            <span class="font-semibold"
                                >Reason for Resign:</span
                            >
                            {{ viewData.reason_for_resign || "-" }}
                        </div>
                    </div>
                </div>

                <div
                    class="p-6 border-t border-gray-200 bg-gray-50 flex justify-end"
                >
                    <button
                        @click="closeViewModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast -->
        <div
            v-if="toast.show"
            :class="[
                'fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3',
                toast.type === 'success' ? 'bg-green-500' : 'bg-red-500',
                'text-white',
            ]"
        >
            <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    v-if="toast.type === 'success'"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                ></path>
                <path
                    v-else
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                ></path>
            </svg>
            <span>{{ toast.message }}</span>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import {
    getAttendances,
    getStaffByTeamLeader,
    getStaffDetail,
    createAttendance,
    deleteAttendance as deleteAttendanceApi,
} from "../../utils/api";

const attendances = ref([]);
const staffList = ref([]);
const loading = ref(true);
const error = ref("");
const showModal = ref(false);
const showViewModal = ref(false);
const submitting = ref(false);
const viewData = ref(null);
const toast = ref({ show: false, message: "", type: "success" });
const proofFile = ref(null);
const filterDate = ref(new Date().toISOString().split("T")[0]);

const pagination = ref({
    total: 0,
    per_page: 15,
    current_page: 1,
    last_page: 1,
    from: 0,
    to: 0,
});

const formData = ref({
    staff_id: "",
    work_status: "",
    name: "",
    position: "",
    superior: "",
    department: "",
    hire_date: "",
    rank: "",
    device: "",
    group: "",
    report_day: new Date().toISOString().split("T")[0],
    ranking_intervals: "",
    reason_for_resign: "",
    status_code: "",
});

const visiblePages = computed(() => {
    const pages = [];
    const current = pagination.value.current_page;
    const last = pagination.value.last_page;
    const delta = 2;

    for (
        let i = Math.max(2, current - delta);
        i <= Math.min(last - 1, current + delta);
        i++
    ) {
        pages.push(i);
    }

    if (current - delta > 2) {
        pages.unshift("...");
    }
    if (current + delta < last - 1) {
        pages.push("...");
    }

    pages.unshift(1);
    if (last > 1) {
        pages.push(last);
    }

    return pages.filter((v, i, a) => a.indexOf(v) === i);
});

onMounted(() => {
    fetchAttendances();
    fetchStaffList();
});

const fetchAttendances = async (page = 1) => {
    loading.value = true;
    error.value = "";

    try {
        const data = await getAttendances(page, filterDate.value || null);
        if (data.success) {
            attendances.value = data.data || [];
            pagination.value = data.pagination;
        } else {
            error.value = data.message || "Failed to load attendances";
        }
    } catch (err) {
        error.value = "Failed to load attendances";
        console.error("Error:", err);
    } finally {
        loading.value = false;
    }
};

const fetchStaffList = async () => {
    try {
        const data = await getStaffByTeamLeader();
        if (data.success) {
            staffList.value = data.data || [];
        }
    } catch (err) {
        console.error("Error fetching staff:", err);
    }
};

const onStaffChange = async () => {
    if (!formData.value.staff_id) return;

    try {
        const data = await getStaffDetail(formData.value.staff_id);
        if (data.success && data.data) {
            const staff = data.data;
            formData.value.name = staff.name;
            formData.value.position = staff.position;
            formData.value.superior = staff.superior || "";
            formData.value.department = staff.department;
            formData.value.hire_date = staff.hire_date;
            formData.value.rank = staff.rank || "";
            formData.value.group = staff.group || "";
            formData.value.device = staff.device || "Mobile";
            const wl = (staff.work_location || "").toUpperCase();
            formData.value.work_status = wl === "WFH" ? "WFH" : "Onsite";
        }
    } catch (err) {
        console.error("Error fetching staff detail:", err);
    }
};

const onFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        proofFile.value = file;
    }
};

const openCreateModal = () => {
    resetForm();
    showModal.value = true;
};

const viewAttendance = (attendance) => {
    viewData.value = attendance;
    showViewModal.value = true;
};

const closeViewModal = () => {
    showViewModal.value = false;
    viewData.value = null;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const resetForm = () => {
    formData.value = {
        staff_id: "",
        work_status: "",
        name: "",
        position: "",
        superior: "",
        department: "",
        hire_date: "",
        rank: "",
        device: "",
        group: "",
        report_day: new Date().toISOString().split("T")[0],
        ranking_intervals: "",
        reason_for_resign: "",
        status_code: "",
    };
    proofFile.value = null;
};

const submitForm = async () => {
    submitting.value = true;

    try {
        const result = await createAttendance(formData.value, proofFile.value);

        if (result.success) {
            showToast(
                result.message || "Attendance created successfully",
                "success"
            );
            closeModal();
            fetchAttendances(pagination.value.current_page);
        } else {
            showToast(result.message || "Operation failed", "error");
        }
    } catch (err) {
        showToast("An error occurred", "error");
        console.error("Error:", err);
    } finally {
        submitting.value = false;
    }
};

const deleteAttendance = async (id) => {
    if (!confirm("Are you sure you want to delete this attendance record?")) {
        return;
    }

    try {
        const result = await deleteAttendanceApi(id);
        if (result.success) {
            showToast("Attendance deleted successfully", "success");
            fetchAttendances(pagination.value.current_page);
        } else {
            showToast(result.message || "Delete failed", "error");
        }
    } catch (err) {
        showToast("An error occurred", "error");
        console.error("Error:", err);
    }
};

const goToPage = (page) => {
    if (page === "..." || page < 1 || page > pagination.value.last_page) return;
    fetchAttendances(page);
};

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const showToast = (message, type = "success") => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

const goBack = () => {
    window.location.href = "/team-leader/dashboard";
};

const logout = () => {
    localStorage.removeItem("tl_auth_token");
    localStorage.removeItem("tl_user");
    window.location.href = "/";
};
</script>
