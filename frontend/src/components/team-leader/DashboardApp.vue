<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">TL-Dashboard</h1>
                        <p class="text-sm text-gray-600 mt-1">Welcome, {{ userName }}</p>
                    </div>
                    <div class="relative">
                        <button
                            @click="showDropdown = !showDropdown"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>{{ userName }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            v-if="showDropdown"
                            @click.stop
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                        >
                            <a
                                href="/team-leader/account"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition"
                            >
                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Account
                            </a>
                            <a
                                href="/team-leader/change-password"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition"
                            >
                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                                Change Password
                            </a>
                            <div class="border-t border-gray-200 my-1"></div>
                            <button
                                @click="logout"
                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition"
                            >
                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-blue-100">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Staff</p>
                            <p class="text-2xl font-bold text-gray-900">{{ totalStaff }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-green-100">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Present Today</p>
                            <p class="text-2xl font-bold text-gray-900">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-purple-100">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Today's Date</p>
                            <p class="text-lg font-bold text-gray-900">{{ todayDate }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Staff Attendance
                            </h2>
                            <p class="text-sm text-gray-600 mt-1">Record attendance for your team members</p>
                        </div>
                        <button
                            @click="openAttendanceModal"
                            class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-purple-700"
                        >
                            <svg class="w-5 h-5 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Record Attendance
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Loading State -->
                    <div v-if="loading" class="text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                        <p class="mt-4 text-gray-600">Loading attendance records...</p>
                    </div>

                    <!-- Error State -->
                    <div v-else-if="error" class="text-center py-12">
                        <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-600">{{ error }}</p>
                    </div>

                    <!-- Empty State -->
                    <div v-else-if="attendances.length === 0" class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Attendance Records</h3>
                        <p class="text-gray-600">Start by creating a new attendance record.</p>
                    </div>

                    <!-- Attendance Table -->
                    <div v-else>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">WFH/Onsite</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Device</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Report Day</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="attendance in attendances" :key="attendance.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ attendance.name }}</div>
                                            <div class="text-xs text-gray-500">{{ attendance.staff_id }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ attendance.position }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ attendance.department }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'px-2 py-1 text-xs font-semibold rounded-full',
                                                attendance.work_status === 'WFH' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'
                                            ]">
                                                {{ attendance.work_status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ attendance.device }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(attendance.report_day) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ attendance.status_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <button
                                                @click="viewAttendance(attendance)"
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
                        <div v-if="pagination.last_page > 1" class="mt-4 flex items-center justify-between border-t border-gray-200 pt-4">
                            <div class="text-sm text-gray-700">
                                Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
                            </div>
                            <div class="flex space-x-2">
                                <button
                                    @click="goToPage(pagination.current_page - 1)"
                                    :disabled="pagination.current_page === 1"
                                    class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
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
                                            : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                                    ]"
                                >
                                    {{ page }}
                                </button>
                                <button
                                    @click="goToPage(pagination.current_page + 1)"
                                    :disabled="pagination.current_page === pagination.last_page"
                                    class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                                >
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Record Attendance Modal -->
        <div v-if="showAttendanceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full my-8">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <h3 class="text-xl font-bold text-gray-900">Record Attendance</h3>
                </div>

                <div class="p-6 max-h-[calc(90vh-200px)] overflow-y-auto">
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Employee Selection -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Employee *</label>
                                <select
                                    v-model="formData.staff_id"
                                    @change="onStaffChange($event)"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">Select Employee</option>
                                    <option v-for="staff in staffList" :key="staff.staff_id" :value="staff.staff_id">
                                        {{ staff.name }} - {{ staff.staff_id }}
                                    </option>
                                </select>
                            </div>

                            <!-- Position (auto-filled) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Position *</label>
                                <input
                                    v-model="formData.position"
                                    type="text"
                                    required
                                    readonly
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                >
                            </div>

                            <!-- Superior (auto-filled) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Superior</label>
                                <input
                                    v-model="formData.superior"
                                    type="text"
                                    readonly
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                >
                            </div>

                            <!-- Department (auto-filled) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                                <input
                                    v-model="formData.department"
                                    type="text"
                                    required
                                    readonly
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                >
                            </div>

                            <!-- Hire Date (auto-filled) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hire Date *</label>
                                <input
                                    v-model="formData.hire_date"
                                    type="date"
                                    required
                                    readonly
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                >
                            </div>

                            <!-- Rank (dropdown S1-S6) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rank *</label>
                                <select
                                    v-model="formData.rank"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">Select Rank</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                    <option value="S4">S4</option>
                                    <option value="S5">S5</option>
                                    <option value="S6">S6</option>
                                </select>
                            </div>

                            <!-- Device -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Device *</label>
                                <select
                                    v-model="formData.device"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">Select Device</option>
                                    <option value="Mobile">Mobile</option>
                                    <option value="PC">PC</option>
                                </select>
                            </div>

                            <!-- Report Day -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Report Day *</label>
                                <input
                                    v-model="formData.report_day"
                                    type="date"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                            </div>

                            <!-- Last Working Day -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Working Day</label>
                                <input
                                    v-model="formData.last_working_day"
                                    type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                            </div>

                            <!-- WFH/Onsite -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">WFH/Onsite *</label>
                                <select
                                    v-model="formData.work_status"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">Select Status</option>
                                    <option value="WFH">WFH</option>
                                    <option value="Onsite">Onsite</option>
                                </select>
                            </div>

                            <!-- Ranking Intervals -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ranking/Intervals *</label>
                                <input
                                    v-model="formData.ranking_intervals"
                                    type="text"
                                    required
                                    placeholder="e.g., 5%-25%"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                            </div>

                            <!-- Group (auto-filled, read-only) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Group</label>
                                <input
                                    v-model="formData.group"
                                    type="text"
                                    readonly
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                >
                            </div>

                            <!-- Status & Code -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status & Code *</label>
                                <select
                                    v-model="formData.status_code"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">Select Status</option>
                                    <option value="AFPI CERTIFICATION">AFPI CERTIFICATION</option>
                                    <option value="Absent (AB4)">Absent (AB4)</option>
                                    <option value="Absent (AB8)">Absent (AB8)</option>
                                    <option value="Annual Leave (A8)">Annual Leave (A8)</option>
                                    <option value="FAILED IT CONFIG">FAILED IT CONFIG</option>
                                    <option value="Half Day - Sick (S4)">Half Day - Sick (S4)</option>
                                    <option value="Late">Late</option>
                                    <option value="Leave (C4)">Leave (C4)</option>
                                    <option value="Leave (C8)">Leave (C8)</option>
                                    <option value="Paid Leave (P)">Paid Leave (P)</option>
                                    <option value="Resignation">Resignation</option>
                                    <option value="Sick (S8)">Sick (S8)</option>
                                </select>
                            </div>

                            <!-- Proof Upload -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Proof (Image) *</label>
                                <input
                                    type="file"
                                    @change="onFileChange"
                                    accept="image/jpeg,image/jpg,image/png"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                <p class="text-xs text-gray-500 mt-1">Upload proof of attendance (max 10MB, will be compressed to 200KB)</p>
                            </div>

                            <!-- Reason for Resign -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Resign</label>
                                <textarea
                                    v-model="formData.reason_for_resign"
                                    rows="3"
                                    placeholder="Enter reason if applicable"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                ></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="p-6 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3">
                    <button
                        @click="closeAttendanceModal"
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
                        {{ submitting ? 'Submitting...' : 'Submit' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- View Modal -->
        <div v-if="showViewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full my-8">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <h3 class="text-xl font-bold text-gray-900">Attendance Details</h3>
                </div>

                <div class="p-6 max-h-[calc(90vh-200px)] overflow-y-auto">
                    <div v-if="viewData" class="grid grid-cols-2 gap-4">
                        <div><span class="font-semibold">Employee:</span> {{ viewData.name }} ({{ viewData.staff_id }})</div>
                        <div><span class="font-semibold">Position:</span> {{ viewData.position }}</div>
                        <div><span class="font-semibold">Department:</span> {{ viewData.department }}</div>
                        <div><span class="font-semibold">Superior:</span> {{ viewData.superior || '-' }}</div>
                        <div><span class="font-semibold">Hire Date:</span> {{ formatDate(viewData.hire_date) }}</div>
                        <div><span class="font-semibold">Rank:</span> {{ viewData.rank || '-' }}</div>
                        <div><span class="font-semibold">Device:</span> {{ viewData.device }}</div>
                        <div><span class="font-semibold">WFH/Onsite:</span> {{ viewData.work_status }}</div>
                        <div><span class="font-semibold">Report Day:</span> {{ formatDate(viewData.report_day) }}</div>
                        <div><span class="font-semibold">Last Working Day:</span> {{ viewData.last_working_day ? formatDate(viewData.last_working_day) : '-' }}</div>
                        <div><span class="font-semibold">Ranking/Intervals:</span> {{ viewData.ranking_intervals || '-' }}</div>
                        <div><span class="font-semibold">Group:</span> {{ viewData.group || '-' }}</div>
                        <div><span class="font-semibold">Status & Code:</span> {{ viewData.status_code }}</div>
                        <div v-if="viewData.proof" class="col-span-2">
                            <span class="font-semibold">Proof:</span><br>
                            <img :src="`/${viewData.proof}`" alt="Proof" class="mt-2 max-w-md rounded-lg border">
                        </div>
                        <div class="col-span-2"><span class="font-semibold">Reason for Resign:</span> {{ viewData.reason_for_resign || '-' }}</div>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-200 bg-gray-50 flex justify-end">
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
        <div v-if="toast.show" :class="[
            'fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3',
            toast.type === 'success' ? 'bg-green-500' : 'bg-red-500',
            'text-white'
        ]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="toast.type === 'success'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ toast.message }}</span>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { getTeamLeaderStaff, getAttendances, getStaffDetail, createAttendance } from '../../utils/api';

const userName = ref('');
const staffList = ref([]);
const attendances = ref([]);
const loading = ref(true);
const error = ref('');
const showAttendanceModal = ref(false);
const showViewModal = ref(false);
const submitting = ref(false);
const viewData = ref(null);
const proofFile = ref(null);
const toast = ref({ show: false, message: '', type: 'success' });
const showDropdown = ref(false);

const pagination = ref({
    total: 0,
    per_page: 15,
    current_page: 1,
    last_page: 1,
    from: 0,
    to: 0
});

const formData = ref({
    staff_id: '',
    work_status: '',
    name: '',
    position: '',
    superior: '',
    department: '',
    hire_date: '',
    rank: '',
    device: '',
    group: '',
    report_day: new Date().toISOString().split('T')[0],
    last_working_day: '',
    ranking_intervals: '',
    reason_for_resign: '',
    status_code: ''
});

const totalStaff = computed(() => staffList.value.length);

const todayDate = computed(() => {
    const today = new Date();
    return today.toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
});

const visiblePages = computed(() => {
    const pages = [];
    const current = pagination.value.current_page;
    const last = pagination.value.last_page;
    const delta = 2;

    for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
        pages.push(i);
    }

    if (current - delta > 2) {
        pages.unshift('...');
    }
    if (current + delta < last - 1) {
        pages.push('...');
    }

    pages.unshift(1);
    if (last > 1) {
        pages.push(last);
    }

    return pages.filter((v, i, a) => a.indexOf(v) === i);
});

onMounted(() => {
    // Check if user must change password
    const mustChangePassword = localStorage.getItem('tl_must_change_password');
    if (mustChangePassword === 'true') {
        window.location.href = '/team-leader/change-password';
        return;
    }

    const user = JSON.parse(localStorage.getItem('tl_user') || '{}');
    userName.value = user.name || 'Team Leader';

    fetchStaffList();
    fetchAttendances();
});

const fetchStaffList = async () => {
    try {
        const token = localStorage.getItem('tl_auth_token');
        if (!token) {
            window.location.href = '/';
            return;
        }

        const data = await getTeamLeaderStaff();
        if (data.success) {
            staffList.value = data.data || [];
        }
    } catch (err) {
        console.error('Error fetching staff:', err);
    }
};

const fetchAttendances = async (page = 1) => {
    loading.value = true;
    error.value = '';

    try {
        const data = await getAttendances(page);
        if (data.success) {
            attendances.value = data.data || [];
            pagination.value = data.pagination;
        } else {
            error.value = data.message || 'Failed to load attendances';
        }
    } catch (err) {
        error.value = 'Failed to load attendances';
        console.error('Error:', err);
    } finally {
        loading.value = false;
    }
};

const onStaffChange = async (event) => {
    const staffId = event.target.value;

    console.log('Staff selection changed to:', staffId);

    if (!staffId) {
        console.log('No staff selected (empty value)');
        return;
    }

    console.log('Fetching staff detail for:', staffId);

    try {
        const data = await getStaffDetail(staffId);
        console.log('Staff detail response:', data);

        if (data.success && data.data) {
            const staff = data.data;
            console.log('Auto-filling form with staff data:', staff);

            formData.value.name = staff.name || '';
            formData.value.position = staff.position || '';
            formData.value.superior = staff.superior || '';
            formData.value.department = staff.department || '';
            formData.value.hire_date = staff.hire_date || '';
            formData.value.rank = staff.rank || '';
            formData.value.group = staff.group || '';

            console.log('Form data after auto-fill:', formData.value);
        } else {
            console.error('Failed to get staff detail:', data);
            showToast(data.message || 'Failed to load staff details', 'error');
        }
    } catch (err) {
        console.error('Error fetching staff detail:', err);
        showToast('Error loading staff details', 'error');
    }
};

const onFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        proofFile.value = file;
    }
};

const openAttendanceModal = () => {
    resetForm();
    showAttendanceModal.value = true;
};

const closeAttendanceModal = () => {
    showAttendanceModal.value = false;
    resetForm();
};

const resetForm = () => {
    formData.value = {
        staff_id: '',
        work_status: '',
        name: '',
        position: '',
        superior: '',
        department: '',
        hire_date: '',
        rank: '',
        device: '',
        group: '',
        report_day: new Date().toISOString().split('T')[0],
        last_working_day: '',
        ranking_intervals: '',
        reason_for_resign: '',
        status_code: ''
    };
    proofFile.value = null;
};

const submitForm = async () => {
    if (!proofFile.value) {
        showToast('Please upload proof image', 'error');
        return;
    }

    submitting.value = true;

    try {
        const result = await createAttendance(formData.value, proofFile.value);

        if (result.success) {
            showToast(result.message || 'Attendance created successfully', 'success');
            closeAttendanceModal();
            fetchAttendances(pagination.value.current_page);
        } else {
            // Show validation errors if available
            if (result.errors) {
                const errorMessages = Object.values(result.errors).flat();
                showToast(errorMessages.join(', '), 'error');
            } else {
                showToast(result.message || 'Operation failed', 'error');
            }
        }
    } catch (err) {
        showToast('An error occurred', 'error');
        console.error('Error:', err);
    } finally {
        submitting.value = false;
    }
};

const viewAttendance = (attendance) => {
    viewData.value = attendance;
    showViewModal.value = true;
};

const closeViewModal = () => {
    showViewModal.value = false;
    viewData.value = null;
};



const goToPage = (page) => {
    if (page === '...' || page < 1 || page > pagination.value.last_page) return;
    fetchAttendances(page);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const showToast = (message, type = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

const logout = () => {
    localStorage.removeItem('tl_auth_token');
    localStorage.removeItem('tl_user');
    window.location.href = '/';
};
</script>
