<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <AdminHeader title="Attendance Records" subtitle="Admin Portal" />
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
                                >Filter bulan:</label
                            >
                            <input
                                type="month"
                                v-model="filterMonth"
                                @change="loadAttendances"
                                class="px-3 py-2 bg-slate-800/50 border border-slate-700 rounded text-slate-100"
                            />
                            <button
                                @click="exportExcel"
                                class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 rounded text-white"
                            >
                                Export
                            </button>
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
import AdminHeader from "./AdminHeader.vue";
import { adminGetAttendancesByMonth, adminDeleteAttendance } from "@/utils/api";
import { API_BASE_URL } from "@/config/api";

const loading = ref(false);
const attendances = ref([]);
const filterMonth = ref(
    `${new Date().getFullYear()}-${String(new Date().getMonth() + 1).padStart(
        2,
        "0"
    )}`
);
const toast = ref({ show: false, message: "", type: "success" });

onMounted(async () => {
    await loadAttendances();
});

const loadAttendances = async () => {
    try {
        loading.value = true;
        const data = await adminGetAttendancesByMonth(filterMonth.value, 1000);
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

const exportExcel = () => {
    const headers = [
        "SN",
        "Status",
        "WFH/Oniste",
        "ID Staff",
        "Name Staff",
        "Position",
        "Superior",
        "Department",
        "Hiredate",
        "Rank",
        "Device",
        "Report Date",
        "Ranking Intervals",
        "Group",
        "Reason for resign",
        "Proof",
    ];

    const rows = [
        headers,
        ...attendances.value.map((a, i) => {
            const p = a.proof || "";
            const base = String(API_BASE_URL || "").replace(/\/$/, "");
            const normalized = p.startsWith("/") ? p.slice(1) : p;
            const proofUrl = /^https?:\/\//i.test(p)
                ? p
                : normalized
                ? `${base}/${normalized}`
                : "";
            const tl = a.team_leader || a.teamLeader;
            const superiorName = a.superior || (tl && tl.name) || "";
            return [
                i + 1,
                a.status_code || "",
                a.work_status || "",
                a.staff_id || "",
                a.name || "",
                a.position || "",
                superiorName,
                a.department || "",
                a.hire_date ? new Date(a.hire_date).toLocaleDateString() : "",
                a.rank || "",
                a.device || "",
                a.report_day ? new Date(a.report_day).toLocaleDateString() : "",
                a.ranking_intervals || "",
                a.group || "",
                a.reason_for_resign || "",
                proofUrl,
            ];
        }),
    ];

    const escape = (s) => {
        const v = String(s ?? "");
        return /[",\n]/.test(v) ? '"' + v.replace(/"/g, '""') + '"' : v;
    };

    const csv = rows.map((r) => r.map(escape).join(",")).join("\n");
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `attendance_${filterMonth.value}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
};
</script>
script
