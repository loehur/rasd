<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <nav
            class="bg-slate-900/80 backdrop-blur border-b border-slate-800/80 sticky top-0 z-50"
        >
            <div
                class="px-3 sm:px-6 lg:px-8 h-16 flex items-center justify-between"
            >
                <div class="flex items-center gap-3">
                    <button
                        @click="goBack"
                        class="text-slate-300 hover:text-slate-100 text-sm flex items-center gap-2"
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
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                        Back to Dashboard
                    </button>
                    <span
                        class="hidden sm:inline text-sm font-bold tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400"
                        >Attendance</span
                    >
                </div>
            </div>
        </nav>

        <main class="px-3 sm:px-6 lg:px-8 py-4">
            <div class="max-w-6xl mx-auto space-y-6">
                <div
                    class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl p-6"
                >
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4"
                    >
                        <h2 class="text-lg sm:text-xl font-bold">
                            Attendance Records
                        </h2>
                        <div class="flex items-center gap-3">
                            <label class="text-sm text-slate-300"
                                >Filter tanggal:</label
                            >
                            <input
                                type="date"
                                v-model="filterDate"
                                @change="loadAttendances"
                                class="px-3 py-2 bg-slate-800/50 border border-slate-700 rounded text-slate-100"
                            />
                        </div>
                    </div>

                    <div v-if="loading" class="p-6 text-slate-300">
                        Loading...
                    </div>
                    <div v-else>
                        <div class="overflow-x-auto -mx-3 sm:mx-0">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr
                                        class="text-left text-slate-300 border-b border-slate-800"
                                    >
                                        <th class="py-2 px-3 sm:px-0">
                                            Employee
                                        </th>
                                        <th class="py-2">Position</th>
                                        <th class="py-2">Department</th>
                                        <th class="py-2">WFH/Onsite</th>
                                        <th class="py-2">Device</th>
                                        <th class="py-2">Report Day</th>
                                        <th class="py-2">Status</th>
                                        <th class="py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="att in attendances"
                                        :key="att.id"
                                        class="border-b border-slate-800"
                                    >
                                        <td class="py-2 px-3 sm:px-0">
                                            <div
                                                class="font-medium text-slate-100"
                                            >
                                                {{ att.name }}
                                            </div>
                                            <div class="text-xs text-slate-400">
                                                {{ att.staff_id }}
                                            </div>
                                        </td>
                                        <td class="py-2">{{ att.position }}</td>
                                        <td class="py-2">
                                            {{ att.department }}
                                        </td>
                                        <td class="py-2">
                                            <span
                                                :class="[
                                                    'px-2 py-1 text-xs rounded-full border',
                                                    att.work_status === 'WFH'
                                                        ? 'bg-blue-500/10 text-blue-300 border-blue-500/30'
                                                        : 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30',
                                                ]"
                                                >{{ att.work_status }}</span
                                            >
                                        </td>
                                        <td class="py-2">{{ att.device }}</td>
                                        <td class="py-2">
                                            {{ formatDate(att.report_day) }}
                                        </td>
                                        <td class="py-2">
                                            {{ att.status_code }}
                                        </td>
                                        <td class="py-2">
                                            <button
                                                @click="remove(att.id)"
                                                class="px-3 py-1 text-xs bg-red-600 hover:bg-red-700 rounded text-white"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div
                    v-if="toast.show"
                    :class="[
                        'fixed top-4 left-1/2 -translate-x-1/2 sm:left-auto sm:translate-x-0 sm:right-4 px-4 sm:px-6 py-3 sm:py-4 rounded-lg shadow-lg z-50',
                        toast.type === 'success'
                            ? 'bg-green-600'
                            : 'bg-red-600',
                    ]"
                >
                    <span class="text-white">{{ toast.message }}</span>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { adminGetAttendances, adminDeleteAttendance } from "@/utils/api";

const loading = ref(false);
const attendances = ref([]);
const filterDate = ref(new Date().toISOString().split("T")[0]);
const toast = ref({ show: false, message: "", type: "success" });

onMounted(async () => {
    await loadAttendances();
});

const loadAttendances = async () => {
    try {
        loading.value = true;
        const data = await adminGetAttendances(filterDate.value, 1000);
        if (data.success) {
            attendances.value = data.data || [];
        } else {
            showToast(data.message || "Failed to load attendances", "error");
        }
    } catch (e) {
        showToast("Error loading attendances", "error");
    } finally {
        loading.value = false;
    }
};

const remove = async (id) => {
    try {
        const res = await adminDeleteAttendance(id);
        if (res.success) {
            showToast("Attendance deleted");
            await loadAttendances();
        } else {
            showToast(res.message || "Delete failed", "error");
        }
    } catch (e) {
        showToast("Error deleting attendance", "error");
    }
};

const formatDate = (d) => {
    const dt = new Date(d);
    return dt.toLocaleDateString();
};

const showToast = (message, type = "success") => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

const goBack = () => {
    window.location.href = "/admin/dashboard";
};
</script>
script
