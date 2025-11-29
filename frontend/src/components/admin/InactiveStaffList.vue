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
                            class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-slate-100 via-slate-200 to-slate-300"
                        >
                            Inactive Staff
                        </h1>
                        <p class="text-slate-400 mt-2">
                            Staff members who are no longer active
                        </p>
                    </div>
                </div>
            </div>

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
                        @click="filterMonth = ''"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition',
                            filterMonth === ''
                                ? 'bg-emerald-600/30 text-emerald-300 border border-emerald-500/50'
                                : 'bg-slate-800/50 text-slate-400 border border-slate-700 hover:bg-slate-700/50 hover:text-slate-200',
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
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-xl p-4 mb-4"
            >
                <div class="flex gap-2 items-center">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by name, staff ID, or reason..."
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
                            />
                        </svg>
                    </button>
                    <button
                        @click="exportResignations"
                        class="px-3 py-2 text-sm bg-emerald-600 hover:bg-emerald-700 rounded-lg text-white transition"
                    >
                        Export
                    </button>
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
                <p class="text-slate-400 mt-4">
                    Loading inactive staff data...
                </p>
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
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl overflow-hidden"
            >
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-800/50">
                            <tr>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Report Day
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    ID Staff
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Name Staff
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Position
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Superior
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Last Working Day
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Type
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Reason
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="resignation in filteredResignations"
                                :key="resignation.id"
                                class="hover:bg-slate-800/30 transition border-t border-slate-800/50"
                            >
                                <td
                                    class="px-3 py-2 text-slate-300 font-semibold"
                                >
                                    {{ formatDate(resignation.report_day) }}
                                </td>
                                <td
                                    class="px-3 py-2 text-blue-300 font-mono font-semibold"
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
                                                ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30'
                                                : 'bg-orange-500/20 text-orange-300 border border-orange-500/30',
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
                                <td class="px-3 py-2">
                                    <button
                                        @click="reactivate(resignation)"
                                        :disabled="
                                            reactivatingId === resignation.id
                                        "
                                        class="px-3 py-1.5 text-xs rounded-lg border transition"
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

                <!-- Mobile List -->
                <div class="md:hidden divide-y divide-slate-800/50">
                    <div
                        v-for="resignation in filteredResignations"
                        :key="resignation.id"
                        class="p-4 hover:bg-slate-800/30 transition"
                    >
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-sm font-semibold text-slate-100"
                                >
                                    {{ resignation.staff_name }}
                                </h3>
                                <p class="text-xs font-mono text-blue-300 mt-1">
                                    {{ resignation.staff_id }}
                                </p>
                            </div>
                            <span
                                :class="[
                                    'px-2 py-1 text-xs font-medium rounded-full',
                                    resignation.resignation_type === 'voluntary'
                                        ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30'
                                        : 'bg-orange-500/20 text-orange-300 border border-orange-500/30',
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
                                <p class="text-slate-500">Report Day</p>
                                <p class="text-slate-300 font-medium">
                                    {{ formatDate(resignation.report_day) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-slate-500">Last Working Day</p>
                                <p class="text-slate-300 font-medium">
                                    {{
                                        formatDate(resignation.last_working_day)
                                    }}
                                </p>
                            </div>
                            <div>
                                <p class="text-slate-500">Position</p>
                                <p class="text-slate-300 font-medium truncate">
                                    {{ resignation.staff_position || "-" }}
                                </p>
                            </div>
                            <div>
                                <p class="text-slate-500">Superior</p>
                                <p class="text-slate-300 font-medium truncate">
                                    {{ resignation.staff_superior || "-" }}
                                </p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-slate-500">Reason</p>
                                <p
                                    class="text-slate-300 text-xs mt-1 line-clamp-2"
                                >
                                    {{ resignation.reason }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button
                                @click="reactivate(resignation)"
                                :disabled="reactivatingId === resignation.id"
                                class="w-full px-3 py-2 text-xs rounded-lg border transition"
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
import { ref, computed, onMounted } from "vue";
import { API_BASE_URL } from "@/config/api";

const resignations = ref([]);
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

const availableMonths = computed(() => {
    const months = resignations.value.map((r) => {
        const date = new Date(r.report_day);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        return `${year}-${month}`;
    });
    return [...new Set(months)].sort((a, b) => b.localeCompare(a));
});

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

        const endpoints = [
            `${API_BASE_URL}/api/resignations?per_page=1000`,
            `${API_BASE_URL}/api/resignations`,
        ];

        let items = [];
        for (const url of endpoints) {
            try {
                const res = await fetch(url, {
                    method: "GET",
                    headers: {
                        Authorization: `Bearer ${token}`,
                        Accept: "application/json",
                        "X-Role":
                            JSON.parse(localStorage.getItem("user") || "{}")
                                .role || "admin",
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
                    continue;
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
                if (items.length) break;
            } catch (e) {
                // try next endpoint
                errorDetail.value = String(e?.message || e);
            }
        }

        resignations.value = (items || [])
            .filter((r) => r && (r.report_day || r.last_working_day))
            .sort(
                (a, b) =>
                    new Date(b.report_day || b.last_working_day) -
                    new Date(a.report_day || a.last_working_day)
            );
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

const getMonthCount = (month) => {
    return resignations.value.filter((r) => {
        const date = new Date(r.report_day);
        const year = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, "0");
        const resignationMonth = `${year}-${m}`;
        return resignationMonth === month;
    }).length;
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

const exportResignations = async () => {
    const headers = [
        "SN",
        "Area",
        "WFH/Oniste",
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

    const token =
        localStorage.getItem("auth_token") ||
        localStorage.getItem("tl_auth_token");
    const base = String(API_BASE_URL || "").replace(/\/$/, "");

    const uniqueIds = [
        ...new Set(
            filteredResignations.value.map((r) => r.staff_id).filter(Boolean)
        ),
    ];
    const staffMap = {};
    for (const id of uniqueIds) {
        try {
            const res = await fetch(
                `${API_BASE_URL}/api/attendances/staff/${id}`,
                {
                    method: "GET",
                    headers: {
                        Authorization: token ? `Bearer ${token}` : undefined,
                        Accept: "application/json",
                    },
                }
            );
            const data = await res.json();
            if (data && data.success && data.data) {
                staffMap[id] = data.data;
            }
        } catch (e) {}
    }

    const toTitle = (s) => {
        const v = String(s || "").replace(/_/g, " ");
        return v
            .split(" ")
            .map((w) => (w ? w[0].toUpperCase() + w.slice(1) : ""))
            .join(" ");
    };

    const rows = [
        headers,
        ...filteredResignations.value.map((r, i) => {
            const s = staffMap[r.staff_id] || {};
            const proofPath = r.proof || "";
            const normalized = proofPath.startsWith("/")
                ? proofPath.slice(1)
                : proofPath;
            const proofUrl = /^https?:\/\//i.test(proofPath)
                ? proofPath
                : normalized
                ? `${base}/${normalized}`
                : "";
            return [
                i + 1,
                s.area || "",
                s.work_location || "",
                r.staff_id || "",
                r.staff_name || s.name || "",
                r.staff_position || s.position || "",
                r.staff_superior || "",
                s.department || "",
                s.hire_date ? new Date(s.hire_date).toLocaleDateString() : "",
                s.rank || "",
                s.device || "",
                r.report_day ? new Date(r.report_day).toLocaleDateString() : "",
                r.last_working_day
                    ? new Date(r.last_working_day).toLocaleDateString()
                    : "",
                r.ranking_intervals || "",
                s.group || "",
                r.resignation_type === "voluntary"
                    ? "Voluntary"
                    : r.resignation_type === "involuntary"
                    ? "Involuntary"
                    : "",
                toTitle(r.resignation_subtype),
                r.reason || "",
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
    a.download = `inactive_staff_${filterMonth.value || "all"}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
};

onMounted(() => {
    const token = localStorage.getItem("auth_token");
    if (!token) {
        window.location.href = "/admin";
        return;
    }
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
