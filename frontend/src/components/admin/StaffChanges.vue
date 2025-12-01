<template>
    <div
        class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950"
    >
        <AdminHeader
            title="Staff Changes Management"
            subtitle="Manage staff transfers, promotions, and updates"
        />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Error/Success Messages -->
            <div
                v-if="error"
                class="mb-6 p-4 bg-red-900/20 border border-red-800/50 rounded-lg text-red-400"
            >
                {{ error }}
            </div>
            <div
                v-if="successMessage"
                class="mb-6 p-4 bg-green-900/20 border border-green-800/50 rounded-lg text-green-400"
            >
                {{ successMessage }}
            </div>

            <!-- Tab Navigation -->
            <div
                class="mb-8 bg-slate-900/50 border border-slate-800/50 rounded-xl overflow-hidden"
            >
                <div class="flex overflow-x-auto">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="[
                            'flex-1 min-w-[200px] px-6 py-4 font-medium transition whitespace-nowrap',
                            activeTab === tab.id
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-400 hover:text-slate-300 hover:bg-slate-800/30',
                        ]"
                    >
                        {{ tab.name }}
                    </button>
                </div>
            </div>

            <!-- Division Transfer Tab -->
            <div
                v-if="activeTab === 'transfer'"
                class="bg-slate-900/50 border border-slate-800/50 rounded-xl p-6"
            >
                <h2 class="text-xl font-semibold text-slate-100 mb-6">
                    Transfer Division
                </h2>
                <form
                    @submit.prevent="submitTransferDivision"
                    class="space-y-4"
                >
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >Select Staff</label
                        >
                        <v-select
                            v-model="transferForm.staff_id"
                            :options="staffOptions"
                            :reduce="(option) => option.code"
                            placeholder="Search staff by ID or name..."
                            class="vue-select-dark"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >New Group</label
                        >
                        <v-select
                            v-model="transferForm.new_group"
                            :options="groupOptions"
                            :reduce="(option) => option.code"
                            placeholder="Select group..."
                            class="vue-select-dark"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >New Team Leader</label
                        >
                        <v-select
                            v-model="transferForm.new_team_leader_id"
                            :options="filteredTeamLeaderOptions"
                            :reduce="(option) => option.code"
                            placeholder="Search team leader..."
                            class="vue-select-dark"
                            :disabled="!transferForm.new_group"
                            @update:modelValue="onTeamLeaderSelected"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >Remarks (Optional)</label
                        >
                        <textarea
                            v-model="transferForm.remarks"
                            rows="3"
                            placeholder="Add any notes about this transfer..."
                            class="w-full px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        ></textarea>
                    </div>

                    <button
                        type="submit"
                        :disabled="submitting"
                        class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-700 disabled:cursor-not-allowed text-white font-medium rounded-lg transition"
                    >
                        {{ submitting ? "Processing..." : "Transfer Division" }}
                    </button>
                </form>
            </div>

            <!-- Promotion Tab -->
            <div
                v-if="activeTab === 'promotion'"
                class="bg-slate-900/50 border border-slate-800/50 rounded-xl p-6"
            >
                <h2 class="text-xl font-semibold text-slate-100 mb-6">
                    Promote to Team Leader
                </h2>
                <form @submit.prevent="submitPromotion" class="space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >Select Staff</label
                        >
                        <v-select
                            v-model="promotionForm.staff_id"
                            :options="staffOnlyOptions"
                            :reduce="(option) => option.code"
                            placeholder="Search staff (non-TL only)..."
                            class="vue-select-dark"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >New Group</label
                        >
                        <v-select
                            v-model="promotionForm.group"
                            :options="groupOptions"
                            :reduce="(option) => option.code"
                            placeholder="Select group..."
                            class="vue-select-dark"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >First Day as TL</label
                        >
                        <input
                            v-model="promotionForm.first_day_tl"
                            type="date"
                            required
                            class="w-full px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >Remarks (Optional)</label
                        >
                        <textarea
                            v-model="promotionForm.remarks"
                            rows="3"
                            placeholder="Add any notes about this promotion..."
                            class="w-full px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        ></textarea>
                    </div>

                    <button
                        type="submit"
                        :disabled="submitting"
                        class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-700 disabled:cursor-not-allowed text-white font-medium rounded-lg transition"
                    >
                        {{
                            submitting
                                ? "Processing..."
                                : "Promote to Team Leader"
                        }}
                    </button>
                </form>
            </div>

            <!-- Rank Change Tab -->
            <div
                v-if="activeTab === 'rank'"
                class="bg-slate-900/50 border border-slate-800/50 rounded-xl p-6"
            >
                <h2 class="text-xl font-semibold text-slate-100 mb-6">
                    Change Rank
                </h2>
                <form @submit.prevent="submitRankChange" class="space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >Select Staff</label
                        >
                        <v-select
                            v-model="rankForm.staff_id"
                            :options="staffOptions"
                            :reduce="(option) => option.code"
                            placeholder="Search staff by ID or name..."
                            class="vue-select-dark"
                            @update:modelValue="onStaffSelectForRank"
                        />
                    </div>

                    <div v-if="rankForm.staff_id">
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >Current Rank</label
                        >
                        <input
                            :value="currentRank"
                            type="text"
                            disabled
                            class="w-full px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-400"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >New Rank</label
                        >
                        <select
                            v-model="rankForm.new_rank"
                            required
                            class="w-full px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">-- Select Rank --</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                            <option value="KPI">KPI</option>
                            <option value="CPI">CPI</option>
                            <option value="C0">C0</option>
                            <option value="C1">C1</option>
                            <option value="C2">C2</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >Remarks (Optional)</label
                        >
                        <textarea
                            v-model="rankForm.remarks"
                            rows="3"
                            placeholder="Add any notes about this rank change..."
                            class="w-full px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        ></textarea>
                    </div>

                    <button
                        type="submit"
                        :disabled="submitting"
                        class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-700 disabled:cursor-not-allowed text-white font-medium rounded-lg transition"
                    >
                        {{ submitting ? "Processing..." : "Change Rank" }}
                    </button>
                </form>
            </div>

            <!-- Warning Letter Tab -->
            <div
                v-if="activeTab === 'warning'"
                class="bg-slate-900/50 border border-slate-800/50 rounded-xl p-6"
            >
                <h2 class="text-xl font-semibold text-slate-100 mb-6">
                    Update Warning Letter
                </h2>
                <form @submit.prevent="submitWarningLetter" class="space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >Select Staff</label
                        >
                        <v-select
                            v-model="warningForm.staff_id"
                            :options="staffOptions"
                            :reduce="(option) => option.code"
                            placeholder="Search staff by ID or name..."
                            class="vue-select-dark"
                            @update:modelValue="onStaffSelectForWarning"
                        />
                    </div>

                    <div v-if="warningForm.staff_id">
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >Current Warning Letter</label
                        >
                        <input
                            :value="currentWarningLetter"
                            type="text"
                            disabled
                            class="w-full px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-400"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >New Warning Letter</label
                        >
                        <select
                            v-model="warningForm.warning_letter"
                            required
                            class="w-full px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">
                                -- Select Warning Letter --
                            </option>
                            <option value="1st">1st Warning Letter</option>
                            <option value="2nd">2nd Warning Letter</option>
                            <option value="3rd">3rd Warning Letter</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >Remarks (Optional)</label
                        >
                        <textarea
                            v-model="warningForm.remarks"
                            rows="3"
                            placeholder="Add any notes about this warning letter..."
                            class="w-full px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        ></textarea>
                    </div>

                    <button
                        type="submit"
                        :disabled="submitting"
                        class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-700 disabled:cursor-not-allowed text-white font-medium rounded-lg transition"
                    >
                        {{
                            submitting
                                ? "Processing..."
                                : "Update Warning Letter"
                        }}
                    </button>
                </form>
            </div>

            <!-- Logs Tab -->
            <div
                v-if="activeTab === 'logs'"
                class="bg-slate-900/50 border border-slate-800/50 rounded-xl overflow-hidden"
            >
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-slate-100 mb-4">
                        Change History
                    </h2>

                    <!-- Filter by Staff -->
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-slate-300 mb-2"
                            >Filter by Staff (Optional)</label
                        >
                        <div class="w-full md:w-96">
                            <v-select
                                v-model="selectedStaffForLogs"
                                :options="staffOptions"
                                :reduce="(option) => option.code"
                                placeholder="All Staff (or search to filter)"
                                class="vue-select-dark"
                                :clearable="true"
                                @update:modelValue="loadLogs"
                            />
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-800/50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase"
                                >
                                    Date
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase"
                                >
                                    Staff
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase"
                                >
                                    Change Type
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase"
                                >
                                    Details
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase"
                                >
                                    Changed By
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            <tr v-if="logs.length === 0">
                                <td
                                    colspan="5"
                                    class="px-6 py-8 text-center text-slate-400"
                                >
                                    No change history found
                                </td>
                            </tr>
                            <tr
                                v-for="log in logs"
                                :key="log.id"
                                class="hover:bg-slate-800/30 transition"
                            >
                                <td class="px-6 py-4 text-sm text-slate-300">
                                    {{ formatDate(log.created_at) }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-100">
                                        {{ log.staff?.name }}
                                    </div>
                                    <div
                                        class="text-xs text-slate-400 font-mono"
                                    >
                                        {{ log.staff_id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="
                                            getChangeTypeClass(log.change_type)
                                        "
                                    >
                                        {{
                                            getChangeTypeLabel(log.change_type)
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-300">
                                    <div class="space-y-1">
                                        <div v-if="log.old_value">
                                            <span class="text-slate-500"
                                                >From:</span
                                            >
                                            <span class="text-slate-400">{{
                                                formatLogValue(
                                                    log.change_type,
                                                    log.old_value
                                                )
                                            }}</span>
                                        </div>
                                        <div v-if="log.new_value">
                                            <span class="text-slate-500"
                                                >To:</span
                                            >
                                            <span class="text-green-400">{{
                                                formatLogValue(
                                                    log.change_type,
                                                    log.new_value
                                                )
                                            }}</span>
                                        </div>
                                        <div
                                            v-if="log.remarks"
                                            class="text-xs text-slate-500 italic"
                                        >
                                            {{ log.remarks }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-400">
                                    {{ log.changed_by }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { API_BASE_URL } from "@/config/api";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import AdminHeader from "./AdminHeader.vue";

const API_BASE = `${API_BASE_URL}/api`;

// State
const activeTab = ref("transfer");
const allStaff = ref([]);
const teamLeaders = ref([]);
const logs = ref([]);
const error = ref("");
const successMessage = ref("");
const submitting = ref(false);
const selectedStaffForLogs = ref("");
const currentRank = ref("");
const currentWarningLetter = ref("");

// Tabs
const tabs = [
    { id: "transfer", name: "Division Transfer" },
    { id: "promotion", name: "Promote to TL" },
    { id: "rank", name: "Change Rank" },
    { id: "warning", name: "Warning Letter" },
    { id: "logs", name: "Change History" },
];

// Forms
const transferForm = ref({
    staff_id: "",
    new_group: "",
    new_department: "",
    new_team_leader_id: "",
    remarks: "",
});

const promotionForm = ref({
    staff_id: "",
    group: "",
    first_day_tl: "",
    remarks: "",
});

const rankForm = ref({
    staff_id: "",
    new_rank: "",
    remarks: "",
});

const warningForm = ref({
    staff_id: "",
    warning_letter: "",
    remarks: "",
});

// Computed
const staffOnly = computed(() =>
    allStaff.value.filter((s) => s.role === "staff")
);

// Formatted options for vue-select
const staffOptions = computed(() =>
    allStaff.value.map((staff) => ({
        label: `${staff.staff_id} - ${staff.name} (${staff.position})`,
        code: staff.staff_id,
        ...staff,
    }))
);

const staffOnlyOptions = computed(() =>
    staffOnly.value.map((staff) => ({
        label: `${staff.staff_id} - ${staff.name} (${staff.position})`,
        code: staff.staff_id,
        ...staff,
    }))
);

const teamLeaderOptions = computed(() =>
    teamLeaders.value.map((tl) => ({
        label: `${tl.staff_id} - ${tl.name} (Group: ${tl.group || "N/A"})`,
        code: tl.staff_id,
        ...tl,
    }))
);

const groupOptions = computed(() => {
    const set = new Set(
        allStaff.value
            .map((s) => s.group)
            .filter((g) => g && typeof g === "string" && g.trim() !== "")
    );
    return Array.from(set)
        .sort()
        .map((g) => ({ label: g, code: g }));
});

const filteredTeamLeaderOptions = computed(() => {
    const selectedGroup = transferForm.value.new_group;
    if (!selectedGroup) return teamLeaderOptions.value;
    return teamLeaders.value
        .filter((tl) => (tl.group || "") === selectedGroup)
        .map((tl) => ({
            label: `${tl.staff_id} - ${tl.name} (Group: ${tl.group || "N/A"})`,
            code: tl.staff_id,
            ...tl,
        }));
});

// Methods
const loadAllStaff = async () => {
    try {
        const response = await fetch(`${API_BASE}/staff-changes/all-staff`);
        if (!response.ok) throw new Error("Failed to load staff");
        allStaff.value = await response.json();
    } catch (e) {
        error.value = e.message;
    }
};

const loadTeamLeaders = async () => {
    try {
        const response = await fetch(`${API_BASE}/staff-changes/team-leaders`);
        if (!response.ok) throw new Error("Failed to load team leaders");
        teamLeaders.value = await response.json();
    } catch (e) {
        error.value = e.message;
    }
};

const loadLogs = async () => {
    try {
        const url = selectedStaffForLogs.value
            ? `${API_BASE}/staff-changes/logs/${selectedStaffForLogs.value}`
            : `${API_BASE}/staff-changes/logs`;

        const response = await fetch(url);
        if (!response.ok) throw new Error("Failed to load logs");
        logs.value = await response.json();
    } catch (e) {
        error.value = e.message;
    }
};

const submitTransferDivision = async () => {
    error.value = "";
    successMessage.value = "";
    submitting.value = true;

    try {
        // Ensure backend-required department is set; derive from selected TL if available
        if (
            !transferForm.value.new_department &&
            transferForm.value.new_team_leader_id
        ) {
            const tl = teamLeaders.value.find(
                (t) => t.staff_id === transferForm.value.new_team_leader_id
            );
            transferForm.value.new_department = tl?.department || "";
        }

        const response = await fetch(
            `${API_BASE}/staff-changes/transfer-division`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(transferForm.value),
            }
        );

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || "Transfer failed");
        }

        successMessage.value = data.message;
        transferForm.value = {
            staff_id: "",
            new_group: "",
            new_department: "",
            new_team_leader_id: "",
            remarks: "",
        };
        await loadAllStaff();
        await loadLogs();
    } catch (e) {
        error.value = e.message;
    } finally {
        submitting.value = false;
    }
};

const onTeamLeaderSelected = () => {
    const tl = teamLeaders.value.find(
        (t) => t.staff_id === transferForm.value.new_team_leader_id
    );
    if (tl) {
        transferForm.value.new_department = tl.department || "";
        if (!transferForm.value.new_group && tl.group) {
            transferForm.value.new_group = tl.group;
        }
    }
};

const submitPromotion = async () => {
    error.value = "";
    successMessage.value = "";
    submitting.value = true;

    try {
        const response = await fetch(`${API_BASE}/staff-changes/promote-tl`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(promotionForm.value),
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || "Promotion failed");
        }

        successMessage.value = data.message;
        promotionForm.value = {
            staff_id: "",
            group: "",
            first_day_tl: "",
            remarks: "",
        };
        await loadAllStaff();
        await loadTeamLeaders();
        await loadLogs();
    } catch (e) {
        error.value = e.message;
    } finally {
        submitting.value = false;
    }
};

const submitRankChange = async () => {
    error.value = "";
    successMessage.value = "";
    submitting.value = true;

    try {
        const response = await fetch(`${API_BASE}/staff-changes/change-rank`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(rankForm.value),
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || "Rank change failed");
        }

        successMessage.value = data.message;
        rankForm.value = { staff_id: "", new_rank: "", remarks: "" };
        currentRank.value = "";
        await loadAllStaff();
        await loadLogs();
    } catch (e) {
        error.value = e.message;
    } finally {
        submitting.value = false;
    }
};

const submitWarningLetter = async () => {
    error.value = "";
    successMessage.value = "";
    submitting.value = true;

    try {
        const response = await fetch(
            `${API_BASE}/staff-changes/warning-letter`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(warningForm.value),
            }
        );

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || "Warning letter update failed");
        }

        successMessage.value = data.message;
        warningForm.value = { staff_id: "", warning_letter: "", remarks: "" };
        currentWarningLetter.value = "";
        await loadAllStaff();
        await loadLogs();
    } catch (e) {
        error.value = e.message;
    } finally {
        submitting.value = false;
    }
};

const onStaffSelectForRank = () => {
    const selected = allStaff.value.find(
        (s) => s.staff_id === rankForm.value.staff_id
    );
    currentRank.value = selected?.rank || "N/A";
};

const onStaffSelectForWarning = () => {
    const selected = allStaff.value.find(
        (s) => s.staff_id === warningForm.value.staff_id
    );
    currentWarningLetter.value = selected?.warning_letter || "None";
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const getChangeTypeLabel = (type) => {
    const labels = {
        division_transfer: "Division Transfer",
        promotion: "Promotion to TL",
        rank_change: "Rank Change",
        warning_letter: "Warning Letter",
    };
    return labels[type] || type;
};

const getChangeTypeClass = (type) => {
    const classes = {
        division_transfer:
            "px-2 py-1 text-xs font-medium rounded-full bg-blue-900/30 text-blue-400 border border-blue-800/50",
        promotion:
            "px-2 py-1 text-xs font-medium rounded-full bg-green-900/30 text-green-400 border border-green-800/50",
        rank_change:
            "px-2 py-1 text-xs font-medium rounded-full bg-purple-900/30 text-purple-400 border border-purple-800/50",
        warning_letter:
            "px-2 py-1 text-xs font-medium rounded-full bg-red-900/30 text-red-400 border border-red-800/50",
    };
    return classes[type] || "";
};

const formatLogValue = (type, value) => {
    if (!value) return "N/A";

    if (type === "division_transfer") {
        return `Group: ${value.group || "N/A"}, Dept: ${
            value.department || "N/A"
        }, TL: ${value.team_leader_id || "N/A"}`;
    } else if (type === "promotion") {
        return `Role: ${value.role || "N/A"}, Group: ${value.group || "N/A"}`;
    } else if (type === "rank_change") {
        return value.rank || "N/A";
    } else if (type === "warning_letter") {
        return value.warning_letter || "N/A";
    }

    return JSON.stringify(value);
};

onMounted(async () => {
    await loadAllStaff();
    await loadTeamLeaders();
    await loadLogs();
});
</script>

<style>
/* Vue Select Dark Theme Customization */
.vue-select-dark .vs__dropdown-toggle {
    background-color: rgba(30, 41, 59, 0.5);
    border: 1px solid rgb(51, 65, 85);
    border-radius: 0.5rem;
    padding: 0.25rem 0.5rem;
}

.vue-select-dark .vs__dropdown-toggle:hover {
    border-color: rgb(71, 85, 105);
}

.vue-select-dark .vs__selected {
    color: rgb(241, 245, 249);
    margin: 0.25rem;
    padding: 0.25rem 0.5rem;
}

.vue-select-dark .vs__search,
.vue-select-dark .vs__search:focus {
    color: rgb(241, 245, 249);
    background-color: transparent;
    border: none;
}

.vue-select-dark .vs__search::placeholder {
    color: rgb(148, 163, 184);
}

.vue-select-dark .vs__actions {
    padding: 0.25rem 0.5rem;
}

.vue-select-dark .vs__clear,
.vue-select-dark .vs__open-indicator {
    fill: rgb(148, 163, 184);
}

.vue-select-dark .vs__clear:hover,
.vue-select-dark .vs__open-indicator:hover {
    fill: rgb(203, 213, 225);
}

.vue-select-dark .vs__dropdown-menu {
    background-color: rgb(30, 41, 59);
    border: 1px solid rgb(51, 65, 85);
    border-radius: 0.5rem;
    margin-top: 0.25rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
}

.vue-select-dark .vs__dropdown-option {
    color: rgb(203, 213, 225);
    padding: 0.5rem 1rem;
}

.vue-select-dark .vs__dropdown-option--highlight {
    background-color: rgb(59, 130, 246);
    color: white;
}

.vue-select-dark .vs__dropdown-option--selected {
    background-color: rgba(59, 130, 246, 0.2);
    color: rgb(147, 197, 253);
}

.vue-select-dark .vs__no-options {
    color: rgb(148, 163, 184);
    padding: 0.75rem 1rem;
}

/* Focus state */
.vue-select-dark.vs--open .vs__dropdown-toggle {
    border-color: rgb(59, 130, 246);
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
}
</style>
