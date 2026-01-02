<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 p-6">
        <div class="max-w-7xl mx-auto">
            <AdminHeader title="Staff List" subtitle="Admin Portal" />

            <!-- Group Filter Tabs -->
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
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                        ></path>
                    </svg>
                    <span class="text-sm font-medium text-slate-300"
                        >Filter</span
                    >
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="setGroupFilter('')"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition',
                            filterGroup === ''
                                ? 'bg-blue-600/30 text-blue-300 border border-blue-500/50'
                                : 'bg-slate-800/50 text-slate-400 border border-slate-700 hover:bg-slate-700/50 hover:text-slate-200',
                        ]"
                    >
                        All
                        <span class="ml-1 text-xs opacity-70"
                            >({{ activeStaffList.length }})</span
                        >
                    </button>
                    <button
                        v-for="group in availableGroups"
                        :key="group"
                        @click="setGroupFilter(group)"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition',
                            filterGroup === group
                                ? 'bg-emerald-600/30 text-emerald-300 border border-emerald-500/50'
                                : 'bg-slate-800/50 text-slate-400 border border-slate-700 hover:bg-slate-700/50 hover:text-slate-200',
                        ]"
                    >
                        {{ group }}
                        <span class="ml-1 text-xs opacity-70"
                            >({{ getGroupCount(group) }})</span
                        >
                    </button>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <button
                        @click="showInsight('tl')"
                        :class="insightBtnClass('tl')"
                    >
                        TL
                    </button>
                    <button
                        @click="showInsight('resignation')"
                        :class="insightBtnClass('resignation')"
                    >
                        Resignation
                    </button>
                    <button
                        @click="showInsight('attendance')"
                        :class="insightBtnClass('attendance')"
                    >
                        Attendance
                    </button>
                    <button
                        @click="showInsight('summary')"
                        :class="insightBtnClass('summary')"
                    >
                        Summary
                    </button>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search..."
                        class="flex-1 px-3 py-2 text-sm bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-blue-500 transition"
                    />
                    <button
                        @click="searchQuery = ''"
                        class="px-3 py-2 text-sm bg-slate-800/50 border border-slate-700 rounded-lg text-slate-400 hover:text-slate-200 hover:border-slate-600 transition flex items-center justify-center"
                        title="Clear search"
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
                    <button
                        @click="goToImport"
                        class="p-2 bg-blue-600/20 text-blue-400 border border-blue-600/30 rounded-lg hover:bg-blue-600/30 transition flex items-center justify-center"
                        title="Import staff"
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
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                            ></path>
                        </svg>
                    </button>
                    <button
                        @click="exportInsightsXlsx"
                        class="p-2 bg-emerald-600/20 text-emerald-400 border border-emerald-600/30 rounded-lg hover:bg-emerald-600/30 transition flex items-center justify-center"
                        title="Export XLSX"
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
                                d="M4 4v16h16M12 12v6m0 0l-3-3m3 3l3-3M8 4h8"
                            ></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div
                v-if="insightMode"
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-xl p-4 mb-4"
            >
                <div v-if="insightLoading" class="text-center py-6">
                    <div
                        class="inline-block w-10 h-10 border-4 border-blue-500/30 border-t-blue-500 rounded-full animate-spin"
                    ></div>
                </div>
                <div v-else>
                    <div v-if="insightMode === 'tl'" class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-800/50">
                                <tr>
                                    <th
                                        class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                    >
                                        Employee ID
                                    </th>
                                    <th
                                        class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                    >
                                        Name
                                    </th>
                                    <th
                                        class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                    >
                                        Position
                                    </th>
                                    <th
                                        class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                    >
                                        Group
                                    </th>
                                    <th
                                        class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                    >
                                        Department
                                    </th>
                                    <th
                                        class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                    >
                                        Area
                                    </th>
                                    <th
                                        class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                    >
                                        Hire Date
                                    </th>
                                    <th
                                        class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                    >
                                        Team Qty
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/50">
                                <tr
                                    v-for="tl in filteredTeamLeadersInsight"
                                    :key="tl.id"
                                    class="hover:bg-slate-800/30 transition"
                                >
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span
                                            class="text-sm font-mono text-blue-400"
                                            >{{ tl.staff_id }}</span
                                        >
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-emerald-500 flex items-center justify-center text-white font-bold text-[11px]"
                                            >
                                                {{ getInitials(tl.name) }}
                                            </div>
                                            <div>
                                                <p
                                                    class="text-xs font-semibold text-slate-100"
                                                >
                                                    {{ tl.name }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="text-xs text-slate-300">{{
                                            tl.position
                                        }}</span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span
                                            class="px-2 py-1 text-[10px] font-medium rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30"
                                            >{{ tl.group || "-" }}</span
                                        >
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="text-xs text-slate-400">{{
                                            tl.department
                                        }}</span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="text-xs text-slate-400">{{
                                            tl.area
                                        }}</span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="text-xs text-slate-400">{{
                                            tl.hire_date
                                        }}</span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span
                                            class="text-xs font-semibold text-emerald-300"
                                            >{{
                                                teamQtyByTlId[tl.staff_id] || 0
                                            }}</span
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        v-else-if="insightMode === 'resignation'"
                        class="overflow-x-auto"
                    >
                        <table class="min-w-full text-xs">
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
                                        Department
                                    </th>
                                    <th
                                        class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                    >
                                        Hiredate
                                    </th>
                                    <th
                                        class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                    >
                                        Rank
                                    </th>
                                    <th
                                        class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                    >
                                        Device
                                    </th>
                                    <th
                                        class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                    >
                                        WFH/Oniste
                                    </th>
                                    <th
                                        class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                    >
                                        Group
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="r in filteredResignationsInsight"
                                    :key="r.id"
                                    class="hover:bg-slate-800/30 transition border-t border-slate-800/50"
                                >
                                    <td class="px-3 py-2">
                                        {{
                                            formatDate(
                                                r.report_day ||
                                                    r.last_working_day
                                            )
                                        }}
                                    </td>
                                    <td
                                        class="px-3 py-2 font-mono text-blue-400"
                                    >
                                        {{ r.staff_id }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ r.staff_name }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ r.staff_position || "-" }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ r.staff_superior || "-" }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ r.department || "-" }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{
                                            r.hire_date
                                                ? formatDate(r.hire_date)
                                                : "-"
                                        }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ r.rank || "-" }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ r.device || "-" }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ r.work_location || "-" }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ r.group || "-" }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        v-else-if="insightMode === 'attendance'"
                        class="overflow-x-auto"
                    >
                        <table class="min-w-full text-xs">
                            <thead>
                                <tr
                                    class="text-left text-slate-300 border-b border-slate-800"
                                >
                                    <th class="py-2 px-3">Employee</th>
                                    <th class="py-2 px-3">Position</th>
                                    <th class="py-2 px-3">Department</th>
                                    <th class="py-2 px-3">WFH/Onsite</th>
                                    <th class="py-2 px-3">Device</th>
                                    <th class="py-2 px-3">Report Day</th>
                                    <th class="py-2 px-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="a in filteredAttendancesInsight"
                                    :key="a.id"
                                    class="border-b border-slate-800"
                                >
                                    <td class="py-2 px-3">
                                        <div class="font-medium text-slate-100">
                                            {{ a.name }}
                                        </div>
                                        <div class="text-xs text-slate-400">
                                            {{ a.staff_id }}
                                        </div>
                                    </td>
                                    <td class="py-2">{{ a.position }}</td>
                                    <td class="py-2">{{ a.department }}</td>
                                    <td class="py-2">
                                        {{ a.work_status }}
                                    </td>
                                    <td class="py-2">{{ a.device }}</td>
                                    <td class="py-2">
                                        {{ formatDate(a.report_day) }}
                                    </td>
                                    <td class="py-2">
                                        {{ a.status_code }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        v-else-if="insightMode === 'summary'"
                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                    >
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead>
                                    <tr
                                        class="text-left text-slate-300 border-b border-slate-800"
                                    >
                                        <th class="py-2 px-3">Team</th>
                                        <th class="py-2 px-3">Team Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="row in filteredGroupSummary"
                                        :key="row.group"
                                        class="border-b border-slate-800"
                                    >
                                        <td class="py-2 px-3">
                                            {{ row.group }}
                                        </td>
                                        <td class="py-2 px-3">
                                            {{ row.count }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead>
                                    <tr
                                        class="text-left text-slate-300 border-b border-slate-800"
                                    >
                                        <th class="py-2 px-3">Area-Location</th>
                                        <th class="py-2 px-3">Team Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="py-2 px-3">Total</td>
                                        <td class="py-2 px-3">
                                            {{ activeStaffList.length }}
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="row in filteredAreaLocationSummary"
                                        :key="row.label"
                                        class="border-b border-slate-800"
                                    >
                                        <td class="py-2 px-3">
                                            {{ row.label }}
                                        </td>
                                        <td class="py-2 px-3">
                                            {{ row.count }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search -->

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
                v-else-if="!insightMode && filteredStaff.length > 0"
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl overflow-hidden"
            >
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-800/50">
                            <tr>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    SN
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Area
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    WFH/Onsite
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
                                    Department
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Hiredate
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Rank
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    Device
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-[11px] font-semibold text-slate-300 uppercase tracking-wider"
                                >
                                    WL
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template
                                v-for="(
                                    staffGroup, teamLeader
                                ) in groupedByTeamLeader"
                                :key="teamLeader"
                            >
                                <!-- Team Leader Header Row -->
                                <tr class="bg-slate-800/70">
                                    <td
                                        colspan="12"
                                        class="px-3 py-3 text-left font-bold text-emerald-400 text-sm"
                                    >
                                        Team Leader: {{ teamLeader }} ({{
                                            staffGroup.length
                                        }}
                                        staff)
                                    </td>
                                </tr>
                                <!-- Staff Rows -->
                                <tr
                                    v-for="(staff, index) in staffGroup"
                                    :key="staff.id"
                                    class="hover:bg-slate-800/30 transition border-t border-slate-800/50"
                                >
                                    <td
                                        class="px-3 py-2 text-blue-300 font-semibold"
                                    >
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ staff.area || "-" }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ staff.work_location || "-" }}
                                    </td>
                                    <td
                                        class="px-3 py-2 text-blue-400 font-mono"
                                    >
                                        {{ staff.staff_id }}
                                    </td>
                                    <td class="px-3 py-2">{{ staff.name }}</td>
                                    <td class="px-3 py-2">
                                        {{ staff.position || "-" }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ getSuperiorName(staff) }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ staff.department || "-" }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ staff.hire_date }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ staff.rank || "-" }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ staff.device || "-" }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ staff.warning_letter ? "WL" : "-" }}
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden">
                    <template
                        v-for="(staffGroup, teamLeader) in groupedByTeamLeader"
                        :key="teamLeader"
                    >
                        <!-- Team Leader Header -->
                        <div
                            class="bg-slate-800/70 px-4 py-3 border-t border-slate-800/50"
                        >
                            <h3 class="font-bold text-emerald-400 text-sm">
                                Team Leader: {{ teamLeader }}
                            </h3>
                            <p class="text-xs text-slate-400 mt-1">
                                {{ staffGroup.length }} staff members
                            </p>
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
                                        <span
                                            class="text-blue-300 font-bold text-sm"
                                            >{{ index + 1 }}.</span
                                        >
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
                                        <p
                                            class="text-xs text-slate-400 truncate"
                                        >
                                            {{ staff.position }}
                                        </p>
                                        <p
                                            class="text-xs font-mono text-blue-400 mt-1"
                                        >
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
                                        <p
                                            class="text-slate-300 font-medium truncate"
                                        >
                                            {{ staff.department }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-slate-500">Phone</p>
                                        <p
                                            class="text-slate-300 font-medium truncate"
                                        >
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
                v-else-if="!insightMode"
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
                                {{ getSuperiorName(selectedStaff) }}
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
                    
                    <!-- ADMIN ACTIONS -->
                    <div class="mt-6 pt-4 border-t border-slate-800 flex justify-end">
                        <button
                            @click="resetStaffPassword(selectedStaff.staff_id)"
                            class="px-4 py-2 bg-red-500/10 text-red-400 border border-red-500/30 rounded-lg hover:bg-red-500/20 transition text-sm font-medium flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            Reset Password to Default
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import * as XLSX from "xlsx";
import { API_BASE_URL } from "@/config/api";
import AdminHeader from "./AdminHeader.vue";
import { adminGetAttendancesByMonth } from "@/utils/api";

const staffList = ref([]);
const currentUser = ref({ role: "" });
const loading = ref(false);
const error = ref("");
const searchQuery = ref("");
const filterDepartment = ref("");
const filterPosition = ref("");
const filterGroup = ref(""); // Group filter
const currentPage = ref(1);
const itemsPerPage = ref(5);
const selectedStaff = ref(null);
const insightMode = ref("");
const insightLoading = ref(false);
const resignationsMonth = ref([]);
const attendancesMonth = ref([]);
const currentMonth = ref(
    `${new Date().getFullYear()}-${String(new Date().getMonth() + 1).padStart(
        2,
        "0"
    )}`
);

// Available groups in order
const availableGroups = [
    "M2",
    "B2",
    "B1",
    "A2",
    "A1-3",
    "A1-2",
    "A1-1",
    "P",
    "P-1",
];

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

const resetStaffPassword = async (staffId) => {
    if (!confirm("Are you sure you want to reset this staff's password to the default 'staff1230'?")) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE_URL}/api/staff/${staffId}/reset-password`, {
            method: "POST",
            headers: {
                "Accept": "application/json",
            }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            alert("Password Reset Successfully!\n\nNew Password: staff1230");
        } else {
            alert("Failed to reset password: " + (data.message || "Unknown error"));
        }
    } catch (err) {
        console.error(err);
        alert("An error occurred while communicating with the server.");
    }
};

const activeStaffList = computed(() => {
    return staffList.value.filter(
        (s) =>
            (s.staff_status || "active").toLowerCase() === "active" &&
            (s.role || "").toLowerCase() !== "tl"
    );
});

const filteredStaff = computed(() => {
    let result = activeStaffList.value;

    // Group filter (priority filter)
    if (filterGroup.value) {
        result = result.filter((staff) => staff.group === filterGroup.value);
    }

    // Search filter (global search across visible columns)
    if (searchQuery.value) {
        const q = (searchQuery.value || "").toLowerCase();
        result = result.filter(
            (staff) =>
                includesQ(staff.name, q) ||
                includesQ(staff.staff_id, q) ||
                includesQ(staff.phone_number, q) ||
                includesQ(staff.area, q) ||
                includesQ(staff.work_location, q) ||
                includesQ(staff.position, q) ||
                includesQ(getSuperiorName(staff), q) ||
                includesQ(staff.department, q) ||
                includesQ(staff.hire_date, q) ||
                includesQ(staff.rank, q) ||
                includesQ(staff.device, q) ||
                ((staff.warning_letter || false) && includesQ("wl", q)) ||
                ((staff.ojk_case || 0) > 0 && includesQ("ojk", q))
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

    filteredStaff.value.forEach((staff) => {
        const teamLeader = getSuperiorName(staff) || "No Team Leader";
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

// Build map of staff_id -> name and helper to resolve superior name
const tlNameById = computed(() => {
    const map = {};
    staffList.value.forEach((s) => {
        map[s.staff_id] = s.name;
    });
    return map;
});

const tlSummary = computed(() => {
    const list = Object.entries(groupedByTeamLeader.value).map(
        ([name, arr]) => ({ name, count: arr.length })
    );
    return list.sort((a, b) => b.count - a.count);
});

const groupSummary = computed(() => {
    const map = {};
    activeStaffList.value.forEach((s) => {
        const g = s.group || "-";
        map[g] = (map[g] || 0) + 1;
    });
    const ordered = [];
    availableGroups.forEach((g) => {
        if (map[g]) ordered.push({ group: g, count: map[g] });
    });
    Object.keys(map)
        .filter((g) => !availableGroups.includes(g))
        .sort()
        .forEach((g) => ordered.push({ group: g, count: map[g] }));
    return ordered;
});

const locationSummary = computed(() => {
    const map = {};
    activeStaffList.value.forEach((s) => {
        const loc = s.work_location || "-";
        map[loc] = (map[loc] || 0) + 1;
    });
    return Object.keys(map)
        .sort()
        .map((k) => ({ location: k, count: map[k] }));
});

// Summary by Area + Work Location (e.g., Depok-Onsite)
const areaLocationSummary = computed(() => {
    const map = {};
    activeStaffList.value.forEach((s) => {
        const area = s.area || "-";
        const loc = s.work_location || "-";
        const key = `${area}-${loc}`;
        map[key] = (map[key] || 0) + 1;
    });
    return Object.keys(map)
        .sort()
        .map((k) => ({ label: k, count: map[k] }));
});

// Team leaders and team quantity mapping for TL insight
const teamLeaders = ref([]);
const teamQtyByTlId = computed(() => {
    const map = {};
    activeStaffList.value.forEach((s) => {
        const tlId = s.team_leader_id;
        if (tlId) {
            map[tlId] = (map[tlId] || 0) + 1;
        }
    });
    return map;
});

// Generic helper for case-insensitive includes
const includesQ = (val, q) =>
    String(val || "")
        .toLowerCase()
        .includes(q);

// Filtered datasets for current insight tables
const filteredTeamLeadersInsight = computed(() => {
    const q = (searchQuery.value || "").toLowerCase();
    const base = q
        ? teamLeaders.value.filter(
              (tl) =>
                  includesQ(tl.name, q) ||
                  includesQ(tl.staff_id, q) ||
                  includesQ(tl.position, q) ||
                  includesQ(tl.group, q) ||
                  includesQ(tl.department, q) ||
                  includesQ(tl.area, q) ||
                  includesQ(tl.hire_date, q)
          )
        : teamLeaders.value.slice();
    return base.sort((a, b) => {
        const ai = availableGroups.indexOf(a.group || "");
        const bi = availableGroups.indexOf(b.group || "");
        const pa = ai === -1 ? 999 : ai;
        const pb = bi === -1 ? 999 : bi;
        if (pa !== pb) return pa - pb;
        return String(a.group || "").localeCompare(String(b.group || ""));
    });
});

const filteredResignationsInsight = computed(() => {
    const q = (searchQuery.value || "").toLowerCase();
    if (!q) return resignationsMonth.value;
    return resignationsMonth.value.filter(
        (r) =>
            includesQ(r.staff_name, q) ||
            includesQ(r.staff_id, q) ||
            includesQ(r.staff_position, q) ||
            includesQ(r.staff_superior, q) ||
            includesQ(r.department, q) ||
            includesQ(r.rank, q) ||
            includesQ(r.device, q) ||
            includesQ(r.work_location, q) ||
            includesQ(r.group, q) ||
            includesQ(r.hire_date, q) ||
            includesQ(r.report_day, q) ||
            includesQ(r.last_working_day, q)
    );
});

const filteredAttendancesInsight = computed(() => {
    const q = (searchQuery.value || "").toLowerCase();
    if (!q) return attendancesMonth.value;
    return attendancesMonth.value.filter(
        (a) =>
            includesQ(a.name, q) ||
            includesQ(a.staff_id, q) ||
            includesQ(a.position, q) ||
            includesQ(a.department, q) ||
            includesQ(a.work_status, q) ||
            includesQ(a.device, q) ||
            includesQ(a.status_code, q) ||
            includesQ(a.report_day, q)
    );
});

const filteredGroupSummary = computed(() => {
    const q = (searchQuery.value || "").toLowerCase();
    if (!q) return groupSummary.value;
    return groupSummary.value.filter((row) => includesQ(row.group, q));
});

const filteredLocationSummary = computed(() => {
    const q = (searchQuery.value || "").toLowerCase();
    if (!q) return locationSummary.value;
    return locationSummary.value.filter((row) => includesQ(row.location, q));
});

const filteredAreaLocationSummary = computed(() => {
    const q = (searchQuery.value || "").toLowerCase();
    if (!q) return areaLocationSummary.value;
    return areaLocationSummary.value.filter((row) => includesQ(row.label, q));
});

const insightBtnClass = (mode) => {
    const active = insightMode.value === mode;
    return [
        "px-4 py-2 text-sm font-medium rounded-lg transition",
        active
            ? "bg-blue-600/30 text-blue-300 border border-blue-500/50"
            : "bg-slate-800/50 text-slate-400 border border-slate-700 hover:bg-slate-700/50 hover:text-slate-200",
    ];
};

const formatDate = (d) => {
    if (!d) return "-";
    return new Date(d).toLocaleDateString();
};

const showInsight = async (mode) => {
    insightMode.value = mode;
    if (mode === "attendance") {
        if (!attendancesMonth.value.length) {
            insightLoading.value = true;
            try {
                const data = await adminGetAttendancesByMonth(
                    currentMonth.value,
                    1000
                );
                attendancesMonth.value = data.success ? data.data || [] : [];
            } catch (e) {
                attendancesMonth.value = [];
            } finally {
                insightLoading.value = false;
            }
        }
    } else if (mode === "resignation") {
        if (!resignationsMonth.value.length) {
            insightLoading.value = true;
            try {
                const token = localStorage.getItem("auth_token");
                const role = JSON.parse(
                    localStorage.getItem("user") || "{}"
                ).role;
                const params = new URLSearchParams();
                params.set("month", currentMonth.value);
                const res = await fetch(
                    `${API_BASE_URL}/api/resignations?${params.toString()}`,
                    {
                        method: "GET",
                        headers: {
                            Authorization: `Bearer ${token}`,
                            "Content-Type": "application/json",
                            "X-Role": role,
                        },
                    }
                );
                const json = await res.json();
                resignationsMonth.value =
                    json && json.success ? json.data || [] : [];
            } catch (e) {
                resignationsMonth.value = [];
            } finally {
                insightLoading.value = false;
            }
        }
    } else if (mode === "tl") {
        if (!teamLeaders.value.length) {
            insightLoading.value = true;
            try {
                const token = localStorage.getItem("auth_token");
                const res = await fetch(`${API_BASE_URL}/api/team-leaders`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                const data = await res.json();
                if (data && data.success && Array.isArray(data.data)) {
                    teamLeaders.value = data.data;
                } else if (Array.isArray(data)) {
                    teamLeaders.value = data;
                } else {
                    teamLeaders.value = [];
                }
            } catch (e) {
                teamLeaders.value = [];
            } finally {
                insightLoading.value = false;
            }
        }
    }
};

const ensureInsightsLoaded = async () => {
    if (!attendancesMonth.value.length) {
        try {
            const data = await adminGetAttendancesByMonth(
                currentMonth.value,
                1000
            );
            attendancesMonth.value = data.success ? data.data || [] : [];
        } catch (e) {
            attendancesMonth.value = [];
        }
    }
    if (!resignationsMonth.value.length) {
        try {
            const token = localStorage.getItem("auth_token");
            const role = JSON.parse(localStorage.getItem("user") || "{}").role;
            const params = new URLSearchParams();
            params.set("month", currentMonth.value);
            const res = await fetch(
                `${API_BASE_URL}/api/resignations?${params.toString()}`,
                {
                    method: "GET",
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json",
                        "X-Role": role,
                    },
                }
            );
            const json = await res.json();
            resignationsMonth.value =
                json && json.success ? json.data || [] : [];
        } catch (e) {
            resignationsMonth.value = [];
        }
    }
    if (!teamLeaders.value.length) {
        try {
            const token = localStorage.getItem("auth_token");
            const res = await fetch(`${API_BASE_URL}/api/team-leaders`, {
                headers: { Authorization: `Bearer ${token}` },
            });
            const data = await res.json();
            if (data && data.success && Array.isArray(data.data)) {
                teamLeaders.value = data.data;
            } else if (Array.isArray(data)) {
                teamLeaders.value = data;
            } else {
                teamLeaders.value = [];
            }
        } catch (e) {
            teamLeaders.value = [];
        }
    }
};

const exportInsightsXlsx = async () => {
    await ensureInsightsLoaded();

    const fmtDate = (d) => (d ? new Date(d).toLocaleDateString() : "-");

    const tlHeaders = [
        "Employee ID",
        "Name",
        "Position",
        "Group",
        "Department",
        "Area",
        "Hire Date",
        "Team Qty",
    ];
    const tlRows = teamLeaders.value.map((tl) => [
        tl.staff_id || "",
        tl.name || "",
        tl.position || "",
        tl.group || "",
        tl.department || "",
        tl.area || "",
        tl.hire_date ? fmtDate(tl.hire_date) : "",
        teamQtyByTlId.value[tl.staff_id] || 0,
    ]);
    const tlSheet = XLSX.utils.aoa_to_sheet([tlHeaders, ...tlRows]);

    const rHeaders = [
        "Report Day",
        "ID Staff",
        "Name Staff",
        "Position",
        "Superior",
        "Department",
        "Hiredate",
        "Rank",
        "Device",
        "WFH/Onsite",
        "Group",
        "Last Working Day",
    ];
    const rRows = resignationsMonth.value.map((r) => [
        fmtDate(r.report_day || r.last_working_day),
        r.staff_id || "",
        r.staff_name || "",
        r.staff_position || "",
        r.staff_superior || "",
        r.department || "",
        r.hire_date ? fmtDate(r.hire_date) : "",
        r.rank || "",
        r.device || "",
        r.work_location || "",
        r.group || "",
        r.last_working_day ? fmtDate(r.last_working_day) : "",
    ]);
    const rSheet = XLSX.utils.aoa_to_sheet([rHeaders, ...rRows]);

    const aHeaders = [
        "Employee",
        "Position",
        "Department",
        "WFH/Onsite",
        "Device",
        "Report Day",
        "Status",
        "Staff ID",
    ];
    const aRows = attendancesMonth.value.map((a) => [
        a.name || "",
        a.position || "",
        a.department || "",
        a.work_status || "",
        a.device || "",
        fmtDate(a.report_day),
        a.status_code || "",
        a.staff_id || "",
    ]);
    const aSheet = XLSX.utils.aoa_to_sheet([aHeaders, ...aRows]);

    const gHeaders = ["Team", "Team Quantity"];
    const gRows = groupSummary.value.map((row) => [row.group, row.count]);

    const alHeaders = ["Area-Location", "Team Quantity"];
    const alRows = areaLocationSummary.value.map((row) => [
        row.label,
        row.count,
    ]);

    const gtHeaders = ["Group", "Quantity"];
    const gtRows = [
        ["All", activeStaffList.value.length],
        ...availableGroups.map((g) => [g, getGroupCount(g)]),
    ];

    const summarySheet = XLSX.utils.aoa_to_sheet([]);
    const add = (aoa, c) =>
        XLSX.utils.sheet_add_aoa(summarySheet, aoa, { origin: { r: 0, c } });
    let c = 0;
    add([["Team Summary"], gHeaders, ...gRows], c);
    c += gHeaders.length + 1;
    add([["Area-Location Summary"], alHeaders, ...alRows], c);
    c += alHeaders.length + 1;
    add([["Groups Tabs"], gtHeaders, ...gtRows], c);

    const wb = XLSX.utils.book_new();

    const staffHeaders = [
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
        "WL",
    ];
    const buildStaffRows = (list) =>
        list.map((s, i) => [
            i + 1,
            s.area || "-",
            s.work_location || "-",
            s.staff_id || "",
            s.name || "",
            s.position || "",
            getSuperiorName(s),
            s.department || "",
            s.hire_date ? fmtDate(s.hire_date) : "",
            s.rank || "",
            s.device || "",
            s.warning_letter ? "WL" : "-",
        ]);

    const allSheet = XLSX.utils.aoa_to_sheet([
        staffHeaders,
        ...buildStaffRows(activeStaffList.value),
    ]);
    XLSX.utils.book_append_sheet(wb, allSheet, "All");

    availableGroups.forEach((g) => {
        const rows = buildStaffRows(
            activeStaffList.value.filter((s) => (s.group || "") === g)
        );
        const sheet = XLSX.utils.aoa_to_sheet([staffHeaders, ...rows]);
        XLSX.utils.book_append_sheet(wb, sheet, g);
    });

    XLSX.utils.book_append_sheet(wb, tlSheet, "TL");
    XLSX.utils.book_append_sheet(wb, rSheet, "Resignation");
    XLSX.utils.book_append_sheet(wb, aSheet, "Attendance");
    XLSX.utils.book_append_sheet(wb, summarySheet, "Summary");

    const fname = `staff_insights_${currentMonth.value}.xlsx`;
    XLSX.writeFile(wb, fname);
};

const getSuperiorName = (staff) => {
    const id = staff?.team_leader_id || "";
    if (!id) return "-";
    return tlNameById.value[id] || "-";
};

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
    return activeStaffList.value.filter((staff) => staff.group === group)
        .length;
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

const setGroupFilter = (group) => {
    filterGroup.value = group;
    insightMode.value = null;
    currentPage.value = 1;
};

onMounted(() => {
    const userData = localStorage.getItem("user");
    if (!userData) {
        window.location.href = "/admin";
        return;
    }
    currentUser.value = JSON.parse(userData);
    loadStaff();
});
</script>
