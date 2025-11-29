<template>
    <div
        class="min-h-screen bg-gradient-to-br from-red-50 via-orange-50 to-red-50 p-6"
    >
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <button
                    @click="goBack"
                    class="mb-4 text-red-600 hover:text-red-800 flex items-center gap-2 transition"
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
                            class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-red-600 via-orange-600 to-red-700"
                        >
                            Staff Resignation
                        </h1>
                    </div>
                </div>
            </div>
            <!-- Action Button -->
            <div class="mb-6">
                <button
                    @click="openCreateModal"
                    class="px-6 py-3 bg-gradient-to-r from-red-600 to-orange-600 text-white font-semibold rounded-lg hover:from-red-700 hover:to-orange-700"
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
                    Add Resignation Record
                </button>
            </div>

            <!-- Resignation List -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
            >
                <div
                    class="p-6 border-b border-gray-200 bg-gradient-to-r from-red-50 to-orange-50"
                >
                    <h2 class="text-xl font-bold text-gray-900">
                        All Resignation Records
                    </h2>
                </div>

                <div class="p-6">
                    <!-- Filter by Year -->
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
                                />
                            </svg>
                            <span class="text-sm font-medium text-gray-700"
                                >Filter by Year:</span
                            >
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button
                                @click="filterYear = ''"
                                :class="[
                                    'px-4 py-2 text-sm font-medium rounded-lg transition',
                                    filterYear === ''
                                        ? 'bg-gray-600 text-white'
                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                                ]"
                            >
                                All Years
                                <span class="ml-1 text-xs opacity-70"
                                    >({{ resignations.length }})</span
                                >
                            </button>
                            <button
                                v-for="year in availableYears"
                                :key="year"
                                @click="filterYear = year"
                                :class="[
                                    'px-4 py-2 text-sm font-medium rounded-lg transition',
                                    filterYear === year
                                        ? 'bg-red-600 text-white'
                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                                ]"
                            >
                                {{ year }}
                                <span class="ml-1 text-xs opacity-70"
                                    >({{ getYearCount(year) }})</span
                                >
                            </button>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div v-if="loading" class="text-center py-12">
                        <div
                            class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-red-600"
                        ></div>
                        <p class="mt-4 text-gray-600">
                            Loading resignation records...
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

                    <!-- Table -->
                    <div
                        v-else-if="displayResignations.length > 0"
                        class="overflow-x-auto"
                    >
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
                                        Last Working Day
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Type
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Subtype
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Report Day
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Reason
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="resignation in displayResignations"
                                    :key="resignation.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ resignation.staff_name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ resignation.staff_id }}
                                        </div>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{
                                            formatDate(
                                                resignation.last_working_day
                                            )
                                        }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'px-2 py-1 text-xs font-semibold rounded-full',
                                                resignation.resignation_type ===
                                                'voluntary'
                                                    ? 'bg-blue-100 text-blue-800'
                                                    : 'bg-orange-100 text-orange-800',
                                            ]"
                                        >
                                            {{ resignation.resignation_type }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{ resignation.resignation_subtype }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{ formatDate(resignation.report_day) }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-xs truncate"
                                        :title="resignation.reason"
                                    >
                                        {{ resignation.reason || "-" }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"
                                        >
                                            Resign
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Summary -->
                        <div class="mt-6 flex items-center justify-between">
                            <p class="text-sm text-gray-600">
                                Showing {{ displayResignations.length }} results
                            </p>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-12">
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
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            ></path>
                        </svg>
                        <p class="text-gray-600">
                            No resignation records found
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full my-8">
                <div
                    class="p-6 border-b border-gray-200 bg-gradient-to-r from-red-50 to-orange-50"
                >
                    <h3 class="text-xl font-bold text-gray-900">
                        Record Resignation
                    </h3>
                </div>

                <div class="p-6">
                    <form @submit.prevent="submitForm">
                        <!-- 2 Column Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Employee Selection -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Employee *</label
                                >
                                <select
                                    v-model="formData.staff_id"
                                    @change="onStaffSelect"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                >
                                    <option value="">Select Employee</option>
                                    <option
                                        v-for="staff in activeStaffList"
                                        :key="staff.staff_id"
                                        :value="staff.staff_id"
                                    >
                                        {{ staff.staff_id }} - {{ staff.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Last Working Day -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Last Working Day *</label
                                >
                                <input
                                    v-model="formData.last_working_day"
                                    type="date"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                />
                            </div>

                            <!-- Resignation Type -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Type *</label
                                >
                                <select
                                    v-model="formData.resignation_type"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                >
                                    <option value="">Select Type</option>
                                    <option value="voluntary">Voluntary</option>
                                    <option value="involuntary">
                                        Involuntary
                                    </option>
                                </select>
                            </div>

                            <!-- Resignation Subtype -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Subtype *</label
                                >
                                <select
                                    v-model="formData.resignation_subtype"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                >
                                    <option value="">Select Subtype</option>
                                    <option value="personal_reason">
                                        Personal Reason
                                    </option>
                                    <option value="management_reason">
                                        Management Reason
                                    </option>
                                </select>
                            </div>

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
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                />
                            </div>
                        </div>

                        <!-- Full Width Fields -->
                        <div class="mt-4 space-y-4">
                            <!-- Reason -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Reason *</label
                                >
                                <textarea
                                    v-model="formData.reason"
                                    required
                                    rows="3"
                                    maxlength="1000"
                                    placeholder="Enter resignation reason..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 resize-none"
                                ></textarea>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ formData.reason.length }}/1000 characters
                                </p>
                            </div>

                            <!-- Proof Upload -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Proof (Optional)</label
                                >
                                <div
                                    v-if="!proofPreview"
                                    class="flex items-center justify-center w-full"
                                >
                                    <label
                                        class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center pt-5 pb-6"
                                        >
                                            <svg
                                                class="w-8 h-8 mb-2 text-gray-500"
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
                                            <p
                                                class="mb-1 text-sm text-gray-500"
                                            >
                                                <span class="font-semibold"
                                                    >Click to upload</span
                                                >
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                PNG or JPG (MAX. 10MB, will be
                                                compressed to 200KB)
                                            </p>
                                        </div>
                                        <input
                                            type="file"
                                            class="hidden"
                                            @change="handleFileUpload"
                                            accept="image/jpeg,image/jpg,image/png"
                                        />
                                    </label>
                                </div>
                                <div v-else class="relative">
                                    <img
                                        :src="proofPreview"
                                        class="w-full h-48 object-cover rounded-lg border border-gray-300"
                                    />
                                    <button
                                        type="button"
                                        @click="removeProof"
                                        class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-2 hover:bg-red-700 transition"
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
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-3 pt-6">
                            <button
                                type="button"
                                @click="closeModal"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="submitting"
                                class="flex-1 px-4 py-2 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-lg hover:from-red-700 hover:to-orange-700 disabled:opacity-50 transition"
                            >
                                {{ submitting ? "Submitting..." : "Submit" }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div
            v-if="toast.show"
            class="fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-3"
            :class="
                toast.type === 'success'
                    ? 'bg-green-500 text-white'
                    : 'bg-red-500 text-white'
            "
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
import { API_BASE_URL } from "@/config/api";

const resignations = ref([]);
const staffList = ref([]);
const loading = ref(true);
const error = ref("");
const showModal = ref(false);
const submitting = ref(false);
const toast = ref({ show: false, message: "", type: "success" });
const filterYear = ref(new Date().getFullYear());

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
    last_working_day: "",
    resignation_type: "",
    resignation_subtype: "",
    report_day: new Date().toISOString().split("T")[0],
    reason: "",
    proof: null,
});

const proofPreview = ref(null);

// Filter only active staff
const activeStaffList = computed(() => {
    return staffList.value.filter((staff) => staff.staff_status === "active");
});

onMounted(() => {
    fetchResignations();
    fetchStaffList();
});

const fetchResignations = async (page = 1) => {
    loading.value = true;
    error.value = "";

    try {
        const token = localStorage.getItem("tl_auth_token");
        const params = new URLSearchParams();
        params.set("page", String(page));
        // Fetch all, filter by year client-side

        const response = await fetch(
            `${API_BASE_URL}/api/resignations?${params.toString()}`,
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "application/json",
                },
            }
        );

        const data = await response.json();
        if (data.success) {
            resignations.value = data.data || [];
            pagination.value = data.pagination;
        } else {
            error.value = data.message || "Failed to load resignations";
        }
    } catch (err) {
        error.value = "Failed to load resignations";
        console.error("Error:", err);
    } finally {
        loading.value = false;
    }
};

const fetchStaffList = async () => {
    try {
        const token = localStorage.getItem("tl_auth_token");
        const response = await fetch(
            `${API_BASE_URL}/api/attendances/staff/team-leader`,
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "application/json",
                },
            }
        );

        const data = await response.json();
        if (data.success) {
            staffList.value = data.data || [];
        }
    } catch (err) {
        console.error("Error fetching staff:", err);
    }
};

const onStaffSelect = () => {
    const selectedStaff = staffList.value.find(
        (s) => s.staff_id === formData.value.staff_id
    );
    if (selectedStaff) {
        // You can auto-fill additional fields if needed
    }
};

const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) {
        formData.value.proof = null;
        proofPreview.value = null;
        return;
    }

    // Validate file type
    if (!["image/jpeg", "image/jpg", "image/png"].includes(file.type)) {
        showToast("Please upload only JPG or PNG images", "error");
        event.target.value = "";
        return;
    }

    try {
        // Compress image
        const compressedFile = await compressImage(file);
        formData.value.proof = compressedFile;

        // Generate preview
        const reader = new FileReader();
        reader.onload = (e) => {
            proofPreview.value = e.target.result;
        };
        reader.readAsDataURL(compressedFile);
    } catch (err) {
        console.error("Error compressing image:", err);
        showToast("Failed to process image", "error");
    }
};

const compressImage = async (file) => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (event) => {
            const img = new Image();
            img.src = event.target.result;
            img.onload = () => {
                const canvas = document.createElement("canvas");
                let width = img.width;
                let height = img.height;

                // Resize if larger than 1920px
                if (width > 1920 || height > 1920) {
                    const ratio = Math.min(1920 / width, 1920 / height);
                    width = Math.floor(width * ratio);
                    height = Math.floor(height * ratio);
                }

                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0, width, height);

                // Compress to under 200KB
                let quality = 0.85;
                const tryCompress = () => {
                    canvas.toBlob(
                        (blob) => {
                            if (blob.size > 200000 && quality > 0.1) {
                                quality -= 0.05;
                                tryCompress();
                            } else {
                                const compressedFile = new File(
                                    [blob],
                                    file.name,
                                    {
                                        type: "image/jpeg",
                                        lastModified: Date.now(),
                                    }
                                );
                                resolve(compressedFile);
                            }
                        },
                        "image/jpeg",
                        quality
                    );
                };

                tryCompress();
            };
            img.onerror = reject;
        };
        reader.onerror = reject;
    });
};

const removeProof = () => {
    formData.value.proof = null;
    proofPreview.value = null;
};

const openCreateModal = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const resetForm = () => {
    formData.value = {
        staff_id: "",
        last_working_day: "",
        resignation_type: "",
        resignation_subtype: "",
        report_day: new Date().toISOString().split("T")[0],
        reason: "",
        proof: null,
    };
    proofPreview.value = null;
};

const submitForm = async () => {
    submitting.value = true;

    try {
        const token = localStorage.getItem("tl_auth_token");

        // Create FormData for file upload
        const formDataToSend = new FormData();
        formDataToSend.append("staff_id", formData.value.staff_id);
        formDataToSend.append(
            "last_working_day",
            formData.value.last_working_day
        );
        formDataToSend.append(
            "resignation_type",
            formData.value.resignation_type
        );
        formDataToSend.append(
            "resignation_subtype",
            formData.value.resignation_subtype
        );
        formDataToSend.append("report_day", formData.value.report_day);
        formDataToSend.append("reason", formData.value.reason);

        // Append proof file if exists
        if (formData.value.proof) {
            formDataToSend.append("proof", formData.value.proof);
        }

        const response = await fetch(`${API_BASE_URL}/api/resignations`, {
            method: "POST",
            headers: {
                Authorization: `Bearer ${token}`,
            },
            body: formDataToSend,
        });

        const data = await response.json();

        if (data.success) {
            showToast(
                data.message || "Resignation recorded successfully",
                "success"
            );
            closeModal();
            fetchResignations(pagination.value.current_page);
            fetchStaffList(); // Refresh staff list to update active/resign status
        } else {
            showToast(data.message || "Operation failed", "error");
        }
    } catch (err) {
        showToast("An error occurred: " + err.message, "error");
        console.error("Error:", err);
    } finally {
        submitting.value = false;
    }
};

const showToast = (message, type = "success") => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

const formatDate = (dateString) => {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

// Years available from resignation records
const availableYears = computed(() => {
    const years = new Set();
    resignations.value.forEach((r) => {
        const d = r.report_day || r.last_working_day;
        if (!d) return;
        const y = new Date(d).getFullYear();
        if (!isNaN(y)) years.add(y);
    });
    return Array.from(years).sort((a, b) => b - a);
});

const getYearCount = (year) => {
    return resignations.value.filter((r) => {
        const d = r.report_day || r.last_working_day;
        return d && new Date(d).getFullYear() === year;
    }).length;
};

// Limit to TL-supervised staff and selected year
const tlStaffIds = computed(
    () => new Set(staffList.value.map((s) => s.staff_id))
);
const displayResignations = computed(() => {
    return resignations.value.filter((r) => {
        if (!tlStaffIds.value.has(r.staff_id)) return false;
        if (!filterYear.value || filterYear.value === "") return true;
        const d = r.report_day || r.last_working_day;
        if (!d) return false;
        return new Date(d).getFullYear() === filterYear.value;
    });
});

const goBack = () => {
    window.location.href = "/team-leader/dashboard";
};
</script>
