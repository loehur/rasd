<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 p-6">
        <div class="max-w-7xl mx-auto">
            <AdminHeader title="Resign Staff" subtitle="Admin Portal" />

            <!-- Month Filter -->
            <div
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-xl p-4 mb-4"
            >
                <div class="flex items-center gap-2 mb-3">
                    <svg
                        class="w-5 h-5 text-slate-400"
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
                    <span class="text-sm font-medium text-slate-300"
                        >Filter by Month:</span
                    >
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="month in availableMonths"
                        :key="month"
                        @click="filterMonth = month"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition',
                            filterMonth === month
                                ? 'bg-blue-600/30 text-blue-300 border border-blue-500/50'
                                : 'bg-slate-800/50 text-slate-400 border border-slate-700 hover:bg-slate-700/50 hover:text-slate-200',
                        ]"
                    >
                        {{ formatMonthLabel(month) }}
                        <span class="ml-1 text-xs opacity-70"
                            >({{ getMonthCount(month) }})</span
                        >
                    </button>
                </div>
            </div>

            <!-- Search -->
            <div
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-xl p-3 md:p-4 mb-4"
            >
                <div class="flex flex-wrap gap-2 items-center">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by name, staff ID, or reason..."
                        class="flex-1 px-3 py-2 text-sm bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-blue-500 transition"
                    />
                    <button
                        @click="resetFilters"
                        class="shrink-0 px-3 py-2 text-sm bg-slate-800/50 border border-slate-700 rounded-lg text-slate-400 hover:text-slate-200 hover:border-slate-600 transition flex items-center justify-center"
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
                            />
                        </svg>
                    </button>
                    <button
                        @click="exportResignations"
                        class="shrink-0 px-3 py-2 text-sm bg-emerald-600 hover:bg-emerald-700 rounded-lg text-white transition"
                    >
                        Export
                    </button>
                    <a
                        :href="`${API_BASE_URL}/api/resignations/template`"
                        download
                        class="shrink-0 px-3 py-2 text-sm bg-slate-700 hover:bg-slate-600 rounded-lg text-white transition"
                    >
                        Download Template
                    </a>
                    <button
                        @click="$refs.fileInput.click()"
                        class="shrink-0 px-3 py-2 text-sm bg-blue-600 hover:bg-blue-700 rounded-lg text-white transition"
                    >
                        Import
                    </button>
                    <input
                        ref="fileInput"
                        type="file"
                        accept=".csv"
                        @change="handleFileSelect"
                        class="hidden"
                    />
                </div>
            </div>

            <div
                v-if="selectedFile"
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl p-6 mb-2"
            >
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm text-slate-400">Selected file</p>
                        <p class="text-slate-100 font-semibold">
                            {{ selectedFile.name }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ formatFileSize(selectedFile.size) }} •
                            {{ csvData.length }} rows detected
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a
                            :href="`${API_BASE_URL}/api/resignations/template`"
                            download
                            class="text-sm font-medium text-blue-400 hover:text-blue-300 underline"
                        >
                            Download Template
                        </a>
                        <button
                            @click="clearFile"
                            class="px-3 py-2 text-sm bg-red-600/20 text-red-400 border border-red-600/30 rounded-lg hover:bg-red-600/30 transition"
                        >
                            Remove
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table
                        class="w-full text-xs border-collapse border border-slate-700 rounded-lg"
                    >
                        <thead class="bg-slate-800/50">
                            <tr>
                                <th
                                    v-for="h in csvHeaders"
                                    :key="h"
                                    class="border border-slate-700 px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase"
                                >
                                    {{ h }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(row, idx) in csvData.slice(0, 5)"
                                :key="idx"
                                class="hover:bg-slate-800/30"
                            >
                                <td
                                    v-for="h in csvHeaders"
                                    :key="h"
                                    class="border border-slate-700 px-3 py-2 text-slate-300"
                                >
                                    {{ row[h] || "-" }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p
                        v-if="csvData.length > 5"
                        class="text-xs text-slate-500 mt-2"
                    >
                        Showing first 5 of {{ csvData.length }} rows
                    </p>
                </div>

                <div class="mt-4">
                    <button
                        @click="importResignations"
                        :disabled="uploading"
                        class="py-2 px-4 bg-gradient-to-r from-blue-600 to-emerald-500 text-white font-semibold rounded-lg hover:from-blue-500 hover:to-emerald-400 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span
                            v-if="uploading"
                            class="w-4 h-4 mr-2 inline-block border-2 border-white/70 border-t-transparent rounded-full animate-spin"
                        ></span>
                        <span>{{
                            uploading ? "Importing..." : "Import Resignations"
                        }}</span>
                    </button>
                </div>
            </div>

            <div
                v-if="resultMessage"
                :class="[
                    'mb-4 p-4 rounded-xl border',
                    resultSuccess
                        ? 'bg-emerald-500/10 border-emerald-500/30'
                        : 'bg-red-500/10 border-red-500/30',
                ]"
            >
                <p class="text-slate-100 font-medium">{{ resultMessage }}</p>
                <div
                    v-if="importErrors.length"
                    class="bg-slate-950/50 rounded-lg p-3 max-h-40 overflow-y-auto mt-2"
                >
                    <ul class="text-xs space-y-1">
                        <li
                            v-for="(error, index) in importErrors"
                            :key="index"
                            :class="[
                                resultSuccess
                                    ? 'text-orange-200'
                                    : 'text-red-200',
                            ]"
                        >
                            • {{ error }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Loading -->
            <div
                v-if="loading"
                class="text-center py-12 bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl"
            >
                <div
                    class="inline-block w-12 h-12 border-4 border-blue-500/30 border-t-blue-500 rounded-full animate-spin"
                ></div>
                <p class="text-slate-400 mt-4">Loading resign staff data...</p>
            </div>

            <!-- Error -->
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
                    />
                </svg>
                <p class="text-red-300 font-semibold">{{ error }}</p>
                <button
                    @click="loadResignations"
                    class="mt-4 px-4 py-2 bg-red-600/20 text-red-400 border border-red-600/30 rounded-lg hover:bg-red-600/30 transition"
                >
                    Retry
                </button>
            </div>

            <!-- Table -->
            <div
                v-else-if="filteredResignations.length > 0"
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl overflow-x-auto"
            >
                <table class="min-w-full text-[11px] md:text-xs">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th
                                v-for="col in columnsOrdered"
                                :key="col"
                                class="px-2 md:px-3 py-2 text-left text-[10px] md:text-[11px] font-semibold text-slate-300 uppercase tracking-wider whitespace-nowrap"
                            >
                                {{ col }}
                            </th>
                            <th
                                class="px-2 md:px-3 py-2 text-left text-[10px] md:text-[11px] font-semibold text-slate-300 uppercase tracking-wider whitespace-nowrap"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(resignation, i) in filteredResignations"
                            :key="resignation.id"
                            class="hover:bg-slate-800/30 transition border-t border-slate-800/50"
                        >
                            <td
                                v-for="col in columnsOrdered"
                                :key="col"
                                class="px-2 md:px-3 py-2 whitespace-nowrap"
                            >
                                <template v-if="col === 'Proof'">
                                    <a
                                        v-if="resignation.proof"
                                        :href="proofUrl(resignation.proof)"
                                        target="_blank"
                                        class="text-blue-300 underline"
                                    >
                                        Open
                                    </a>
                                    <span v-else>-</span>
                                </template>
                                <template v-else>
                                    {{ colValue(col, resignation, i) }}
                                </template>
                            </td>
                            <td class="px-2 md:px-3 py-2">
                                <button
                                    @click="reactivate(resignation)"
                                    :disabled="
                                        reactivatingId === resignation.id
                                    "
                                    class="px-3 py-1.5 text-[11px] md:text-xs rounded-lg border transition"
                                    :class="[
                                        reactivatingId === resignation.id
                                            ? 'bg-emerald-600/20 text-emerald-300 border-emerald-500/30 opacity-70 cursor-not-allowed'
                                            : 'bg-emerald-600/20 text-emerald-300 border-emerald-500/30 hover:bg-emerald-600/30',
                                    ]"
                                >
                                    {{
                                        reactivatingId === resignation.id
                                            ? "Activating..."
                                            : "Activate"
                                    }}
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
                    />
                </svg>
                <p class="text-slate-400 text-lg">
                    No resignation records found
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { API_BASE_URL } from "@/config/api";
import AdminHeader from "./AdminHeader.vue";

const resignations = ref([]);
const months = ref([]);
const loading = ref(false);
const error = ref("");
const errorDetail = ref("");
const searchQuery = ref("");
const currentDate = new Date();
const filterMonth = ref(
    `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(
        2,
        "0"
    )}`
);
const reactivatingId = ref(null);
const selectedFile = ref(null);
const csvHeaders = ref([]);
const csvData = ref([]);
const uploading = ref(false);
const resultMessage = ref("");
const resultSuccess = ref(false);
const importErrors = ref([]);

const availableMonths = computed(() => {
    return (months.value || [])
        .map((m) => m.month)
        .filter(Boolean)
        .sort((a, b) => b.localeCompare(a));
});

const loadMonths = async () => {
    try {
        const token =
            localStorage.getItem("auth_token") ||
            localStorage.getItem("tl_auth_token");
        if (!token) {
            window.location.href = "/admin";
            return;
        }
        const url = `${API_BASE_URL}/api/resignations/months`;
        const res = await fetch(url, {
            method: "GET",
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
                "X-Role":
                    JSON.parse(localStorage.getItem("user") || "{}").role ||
                    "admin",
            },
        });
        const data = await res.json();
        if (data && data.success) {
            months.value = Array.isArray(data.data) ? data.data : [];
        }
    } catch (e) {}
};

const loadResignations = async () => {
    loading.value = true;
    error.value = "";

    try {
        const token =
            localStorage.getItem("auth_token") ||
            localStorage.getItem("tl_auth_token");
        if (!token) {
            window.location.href = "/admin";
            return;
        }

        const monthParam = filterMonth.value;
        const url = `${API_BASE_URL}/api/resignations?month=${monthParam}&per_page=1000`;

        let items = [];
        try {
            const res = await fetch(url, {
                method: "GET",
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                    "X-Role":
                        JSON.parse(localStorage.getItem("user") || "{}").role ||
                        "admin",
                },
            });
            let data;
            try {
                data = await res.json();
            } catch (jsonErr) {
                const text = await res.text();
                errorDetail.value = `Status: ${res.status} ${
                    res.statusText
                }\nURL: ${url}\nResponse: ${text.substring(0, 500)}`;
            }
            if (data && data.success === true) {
                items = Array.isArray(data.data)
                    ? data.data
                    : data.results || [];
            } else if (Array.isArray(data)) {
                items = data;
            } else if (data && Array.isArray(data.data)) {
                items = data.data;
            }
        } catch (e) {
            errorDetail.value = String(e?.message || e);
        }

        resignations.value = (items || [])
            .filter((r) => r && (r.report_day || r.last_working_day))
            .sort(
                (a, b) =>
                    new Date(b.report_day || b.last_working_day) -
                    new Date(a.report_day || a.last_working_day)
            );
        // Backend enriches staff fields; avoid per-row fetches
    } catch (err) {
        error.value = "Connection error. Please make sure you're logged in.";
        console.error("Load error:", err);
        errorDetail.value = String(err?.message || err);
    } finally {
        loading.value = false;
    }
};

const filteredResignations = computed(() => {
    let result = resignations.value;

    if (filterMonth.value) {
        result = result.filter((r) => {
            const date = new Date(r.report_day);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const resignationMonth = `${year}-${month}`;
            return resignationMonth === filterMonth.value;
        });
    }

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        result = result.filter(
            (r) =>
                (r.staff_name || "").toLowerCase().includes(q) ||
                (r.staff_id || "").toLowerCase().includes(q) ||
                (r.reason || "").toLowerCase().includes(q)
        );
    }

    return result;
});

const columnsOrdered = [
    "SN",
    "Area",
    "WFH/Onsite",
    "ID Staff",
    "Name Staff",
    "Position",
    "Superior",
    "Department",
    "Hiredate",
    "Rank",
    "Device",
    "Report day",
    "Last working day",
    "Ranking Intervals",
    "Group",
    "Voluntary/Involuntary",
    "Subtype of Resignation",
    "Reason for resignation",
    "Proof",
];

// Backend enriches staff fields; avoid per-row fetches

const toTitle = (s) => {
    const v = String(s || "").replace(/_/g, " ");
    return v
        .split(" ")
        .map((w) => (w ? w[0].toUpperCase() + w.slice(1) : ""))
        .join(" ");
};

const normalizeWorkMode = (v) => {
    const s = String(v || "")
        .trim()
        .toUpperCase();
    if (!s) return "";
    const groupCodes = new Set([
        "M2",
        "B2",
        "B1",
        "A2",
        "A1-3",
        "A1-2",
        "A1-1",
        "P",
        "P-1",
    ]);
    if (groupCodes.has(s)) return "";
    if (s === "WFO" || s === "ONSITE") return "Onsite";
    if (s === "WFH") return "WFH";
    return v || "";
};

const colValue = (col, r, i) => {
    switch (col) {
        case "SN":
            return i + 1;
        case "Area":
            return r.area || "";
        case "WFH/Oniste":
            return normalizeWorkMode(r.work_status || r.work_location);
        case "WFH/Onsite":
            return normalizeWorkMode(r.work_status || r.work_location);
        case "ID Staff":
            return r.staff_id || "";
        case "Name Staff":
            return r.staff_name || "";
        case "Position":
            return r.staff_position || "";
        case "Superior":
            return r.staff_superior || "";
        case "Department":
            return r.department || "";
        case "Hiredate":
            return r.hire_date
                ? new Date(r.hire_date).toLocaleDateString()
                : "";
        case "Rank":
            return r.rank || "";
        case "Device":
            return r.device || "";
        case "Report day":
            return r.report_day
                ? new Date(r.report_day).toLocaleDateString()
                : "";
        case "Last working day":
            return r.last_working_day
                ? new Date(r.last_working_day).toLocaleDateString()
                : "";
        case "Ranking Intervals":
            return r.ranking_intervals || "";
        case "Group":
            return r.group || "";
        case "Voluntary/Involuntary":
            return r.resignation_type === "voluntary"
                ? "Voluntary"
                : r.resignation_type === "involuntary"
                ? "Involuntary"
                : "";
        case "Subtype of Resignation":
            return toTitle(r.resignation_subtype);
        case "Reason for resignation":
            return r.reason || "";
        case "Proof":
            return proofUrl(r.proof || "");
        default:
            return "";
    }
};

// Removed loadStaffProfiles to avoid N HTTP requests

const displayColumns = computed(() => {
    const omit = new Set([
        "password",
        "role",
        "submitted_by",
        "created_at",
        "updated_at",
    ]);
    const cols = new Set();
    filteredResignations.value.forEach((r) => {
        Object.keys(r || {}).forEach((k) => {
            if (!omit.has(k)) cols.add(k);
        });
    });
    return Array.from(cols);
});

const getMonthCount = (month) => {
    const found = (months.value || []).find((m) => m.month === month);
    return found ? found.count : 0;
};

const formatMonthLabel = (month) => {
    if (!month) return "";
    const [year, m] = month.split("-");
    const date = new Date(year, parseInt(m) - 1);
    return date.toLocaleDateString("en-US", {
        month: "short",
        year: "numeric",
    });
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
    window.location.href = "/admin/dashboard";
};
const resetFilters = () => {
    searchQuery.value = "";
    filterMonth.value = "";
};

const handleFileSelect = (event) => {
    const file = event.target.files[0];
    if (file && file.type === "text/csv") {
        selectedFile.value = file;
        parseCSV(file);
    } else {
        alert("Please select a valid CSV file");
    }
};

const parseCSV = (file) => {
    const reader = new FileReader();
    reader.onload = (e) => {
        const text = e.target.result;
        const lines = String(text)
            .split(/\r?\n/)
            .filter((line) => line.trim());
        if (lines.length > 0) {
            const first = lines[0].replace(/^\ufeff/, "");
            csvHeaders.value = first.split(",").map((h) => h.trim());
            csvData.value = lines.slice(1).map((line) => {
                const values = line.split(",");
                const row = {};
                csvHeaders.value.forEach((header, index) => {
                    row[header] = values[index]?.trim() || "";
                });
                return row;
            });
        }
    };
    reader.readAsText(file);
};

const clearFile = () => {
    selectedFile.value = null;
    csvHeaders.value = [];
    csvData.value = [];
};

const importResignations = async () => {
    if (!selectedFile.value) return;
    uploading.value = true;
    resultMessage.value = "";
    importErrors.value = [];
    resultSuccess.value = false;

    try {
        const token = localStorage.getItem("auth_token");
        const role =
            JSON.parse(localStorage.getItem("user") || "{}").role || "admin";
        const formData = new FormData();
        formData.append("file", selectedFile.value);

        const response = await fetch(
            `${API_BASE_URL}/api/resignations/import`,
            {
                method: "POST",
                headers: { Authorization: `Bearer ${token}`, "X-Role": role },
                body: formData,
            }
        );

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            resultSuccess.value = false;
            resultMessage.value =
                "Server error: unexpected response (not JSON).";
            console.error("Import response (non-JSON):", text);
            return;
        }

        if (data.success) {
            resultSuccess.value = true;
            resultMessage.value = data.message;
            importErrors.value = data.data?.errors || [];
            // Refresh table after import
            await loadResignations();
        } else {
            resultSuccess.value = false;
            resultMessage.value = data.message || "Import failed";
            importErrors.value = data.data?.errors || [];
        }
    } catch (error) {
        resultSuccess.value = false;
        resultMessage.value = "Error: " + error.message;
    } finally {
        uploading.value = false;
    }
};

const formatFileSize = (bytes) => {
    if (bytes < 1024) return bytes + " B";
    if (bytes < 1048576) return (bytes / 1024).toFixed(2) + " KB";
    return (bytes / 1048576).toFixed(2) + " MB";
};

const exportResignations = async () => {
    const headers = columnsOrdered;
    const rows = [
        headers,
        ...filteredResignations.value.map((r, i) =>
            headers.map((h) => colValue(h, r, i))
        ),
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
    a.download = `resign_staff_${filterMonth.value || "all"}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
};

const proofUrl = (p) => {
    if (!p) return "";
    if (/^https?:\/\//i.test(p)) return p;
    // Use full domain URL for export and display
    const origin = window.location.origin;
    const apiPath = String(API_BASE_URL || "").replace(/\/$/, "");
    // Build full base URL: if API_BASE_URL is relative, prepend origin
    const base = /^https?:\/\//i.test(apiPath) ? apiPath : `${origin}${apiPath.startsWith('/') ? '' : '/'}${apiPath}`;
    const normalized = String(p).startsWith("/") ? p.slice(1) : p;
    return `${base}/${normalized}`;
};

const formatValue = (v) => {
    if (v === null || v === undefined) return "-";
    if (typeof v === "string" && v.length === 0) return "-";
    return v;
};

onMounted(async () => {
    const token = localStorage.getItem("auth_token");
    if (!token) {
        window.location.href = "/admin";
        return;
    }
    await loadMonths();
    await loadResignations();
});

watch(filterMonth, () => {
    loadResignations();
});

const reactivate = async (r) => {
    try {
        reactivatingId.value = r.id;
        const token = localStorage.getItem("auth_token");
        const role =
            JSON.parse(localStorage.getItem("user") || "{}").role || "admin";
        const res = await fetch(`${API_BASE_URL}/api/resignations/reactivate`, {
            method: "POST",
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-Role": role,
            },
            body: JSON.stringify({ staff_id: r.staff_id }),
        });
        const data = await res.json();
        if (data && data.success) {
            resignations.value = resignations.value.filter(
                (x) => x.staff_id !== r.staff_id
            );
        } else {
            error.value = data?.message || "Failed to reactivate staff";
        }
    } catch (e) {
        error.value = "Connection error. Please make sure you're logged in.";
    } finally {
        reactivatingId.value = null;
    }
};
</script>
