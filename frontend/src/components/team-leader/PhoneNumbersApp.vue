<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
        <!-- Header -->
        <header class="bg-gradient-to-r from-slate-50 via-indigo-50 to-sky-50 text-slate-800 border-b border-slate-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">Phone Numbers</h1>
                        <p class="text-sm text-slate-600 mt-1">Manage phone number entries</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="/team-leader/dashboard" class="text-sm text-slate-600 hover:text-slate-900">
                            ← Back to Dashboard
                        </a>
                        <button
                            @click="logout"
                            class="px-4 py-2 text-sm font-medium bg-red-600 text-white rounded-lg hover:bg-red-700"
                        >
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Add Form (Staff & Team Leader) -->
            <div v-if="userRole === 'staff' || userRole === 'tl' || userRole === 'team_leader'" class="mb-8 bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Add New Phone Number</h2>
                
                <form @submit.prevent="submitForm" class="space-y-4">
                    <!-- Auto-filled fields (read-only) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Employee ID
                            </label>
                            <input 
                                v-model="formData.employeeId"
                                type="text"
                                readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Employee Name
                            </label>
                            <input 
                                v-model="formData.employeeName"
                                type="text"
                                readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                            />
                        </div>
                    </div>

                    <!-- Manual input fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input 
                                v-model="formData.phoneNumber"
                                type="tel"
                                required
                                placeholder="Enter phone number"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Remarks <span class="text-red-500">*</span>
                            </label>
                            <input 
                                v-model="formData.remarks"
                                type="text"
                                required
                                placeholder="Enter remarks (required)"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50"
                        >
                            {{ submitting ? 'Adding...' : 'Add Phone Number' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info message for Team Leaders -->
            <div v-if="userRole === 'tl' || userRole === 'team_leader'" class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-blue-800">
                        Anda dapat menambahkan phone number dan melihat data dari tim Anda. TL ID akan otomatis diisi dengan Employee ID Anda. Hanya admin yang dapat edit atau delete.
                    </p>
                </div>
            </div>

            <!-- Info message for Staff -->
            <div v-if="userRole === 'staff'" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-green-800">
                        You can add new phone numbers using the form above. Only admin can edit or delete entries.
                    </p>
                </div>
            </div>

            <!-- Phone Numbers List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900">Phone Numbers List</h2>
                        <div class="text-sm text-gray-600">
                            Showing {{ pagination.total > 0 ? ((pagination.current_page - 1) * pagination.per_page) + 1 : 0 }} - {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} of {{ pagination.total }} entries
                        </div>
                    </div>
                    
                    <!-- Search Input -->
                    <div class="mt-4">
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search phone number (min. 8 digits)"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    @input="onSearchInput"
                                />
                                <p v-if="searchError" class="mt-1 text-sm text-red-600">
                                    {{ searchError }}
                                </p>
                            </div>
                            <button
                                @click="performSearch"
                                :disabled="searchQuery.length > 0 && searchQuery.length < 8"
                                class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Search
                            </button>
                            <button
                                v-if="searchQuery"
                                @click="clearSearch"
                                class="px-6 py-2 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="p-8 text-center">
                    <div class="inline-block h-8 w-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                    <p class="mt-2 text-gray-600">Loading...</p>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="p-8 text-center text-red-600">
                    {{ error }}
                </div>

                <!-- Data Table -->
                <div v-else-if="phoneNumbers.length > 0" class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Employee ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Employee Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Team Leader
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Phone Number
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Remarks
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date Added
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in phoneNumbers" :key="item.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ item.staff_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ item.employee_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ item.team_leader_name || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                                    {{ item.phone_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <div v-if="item.remarks" class="relative group cursor-help">
                                        <span class="text-gray-700">
                                            {{ item.remarks.length > 20 ? item.remarks.substring(0, 20) + '...' : item.remarks }}
                                        </span>
                                        <div v-if="item.remarks.length > 20" class="absolute z-50 invisible group-hover:visible bg-gray-800 text-white text-xs rounded-lg py-2 px-3 bottom-full left-0 mb-2 w-auto max-w-xs shadow-xl whitespace-normal">
                                            {{ item.remarks }}
                                            <div class="absolute top-full left-4 transform border-4 border-transparent border-t-gray-800"></div>
                                        </div>
                                    </div>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ formatDate(item.created_at) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-else class="p-8 text-center text-gray-500">
                    No phone numbers found.{{ userRole === 'staff' ? ' Add your first entry above!' : '' }}
                </div>

                <!-- Pagination Controls -->
                <div v-if="phoneNumbers.length > 0" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Page {{ pagination.current_page }} of {{ pagination.total_pages }}
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="goToPage(pagination.current_page - 1)"
                            :disabled="!pagination.has_prev || loading"
                            class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                        >
                            ← Previous
                        </button>
                        <button
                            @click="goToPage(pagination.current_page + 1)"
                            :disabled="!pagination.has_next || loading"
                            class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                        >
                            Next →
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Edit Phone Number</h3>
                
                <form @submit.prevent="submitEdit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input 
                            v-model="editData.phone_number"
                            type="tel"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Remarks <span class="text-red-500">*</span>
                        </label>
                        <input 
                            v-model="editData.remarks"
                            type="text"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    
                    <div class="flex gap-3 justify-end">
                        <button
                            type="button"
                            @click="closeEditModal"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{ submitting ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Toast Notification -->
        <div
            v-if="toast.show"
            :class="[
                'fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3',
                toast.type === 'success' ? 'bg-green-500' : 'bg-red-500',
                'text-white'
            ]"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
import { ref, onMounted } from 'vue';
import { API_BASE_URL } from '@/config/api';

const phoneNumbers = ref([]);
const loading = ref(true);
const error = ref('');
const submitting = ref(false);
const userRole = ref('');
const toast = ref({ show: false, message: '', type: 'success' });
const searchQuery = ref('');
const searchError = ref('');
const showEditModal = ref(false);
const editData = ref({
    id: null,
    phone_number: '',
    remarks: ''
});
const pagination = ref({
    current_page: 1,
    per_page: 20,
    total: 0,
    total_pages: 0,
    has_prev: false,
    has_next: false
});

const formData = ref({
    employeeId: '',
    employeeName: '',
    teamLeaderName: '',
    phoneNumber: '',
    remarks: ''
});

onMounted(() => {
    const user = JSON.parse(localStorage.getItem('tl_user') || '{}');
    userRole.value = user.role || '';
    
    // Pre-fill form data for staff and team leader
    if (userRole.value === 'staff' || userRole.value === 'tl' || userRole.value === 'team_leader') {
        formData.value.employeeId = user.employee_id || '';
        formData.value.employeeName = user.name || '';
        // Team leader name will be filled from backend
        // For TL, the team_leader_id will automatically be their own ID (handled by backend)
    }
    
    fetchPhoneNumbers();
});

const fetchPhoneNumbers = async (page = 1) => {
    loading.value = true;
    error.value = '';
    
    try {
        const token = localStorage.getItem('tl_auth_token');
        
        // Build URL with search and pagination parameters
        const params = new URLSearchParams();
        params.append('page', page);
        params.append('per_page', 20);
        if (searchQuery.value && searchQuery.value.length >= 8) {
            params.append('search', searchQuery.value);
        }
        
        const response = await fetch(`${API_BASE_URL}/api/phone-numbers?${params.toString()}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            phoneNumbers.value = data.data;
            if (data.pagination) {
                pagination.value = data.pagination;
            }
        } else {
            error.value = data.message || 'Failed to fetch phone numbers';
        }
    } catch (err) {
        error.value = 'Failed to connect to server';
        console.error('Error:', err);
    } finally {
        loading.value = false;
    }
};

const goToPage = (page) => {
    if (page >= 1 && page <= pagination.value.total_pages) {
        fetchPhoneNumbers(page);
    }
};

const submitForm = async () => {
    if (!formData.value.phoneNumber.trim()) {
        showToast('Please enter a phone number', 'error');
        return;
    }
    
    if (!formData.value.remarks.trim()) {
        showToast('Please enter remarks', 'error');
        return;
    }
    
    submitting.value = true;
    
    try {
        const token = localStorage.getItem('tl_auth_token');
        const response = await fetch(`${API_BASE_URL}/api/phone-numbers`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                phone_number: formData.value.phoneNumber,
                remarks: formData.value.remarks
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Phone number added successfully!', 'success');
            // Reset manual fields
            formData.value.phoneNumber = '';
            formData.value.remarks = '';
            // Refresh list
            fetchPhoneNumbers();
        } else {
            showToast(data.message || 'Failed to add phone number', 'error');
        }
    } catch (err) {
        showToast('Failed to connect to server', 'error');
        console.error('Error:', err);
    } finally {
        submitting.value = false;
    }
};

const deletePhoneNumber = async (id) => {
    if (!confirm('Are you sure you want to delete this phone number?')) {
        return;
    }
    
    try {
        const token = localStorage.getItem('tl_auth_token');
        const response = await fetch(`${API_BASE_URL}/api/phone-numbers/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Phone number deleted successfully', 'success');
            fetchPhoneNumbers();
        } else {
            showToast(data.message || 'Failed to delete phone number', 'error');
        }
    } catch (err) {
        showToast('Failed to connect to server', 'error');
        console.error('Error:', err);
    }
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const showToast = (message, type = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

const onSearchInput = () => {
    // Clear error when user types
    searchError.value = '';
    
    // Show error if length is between 1-7
    if (searchQuery.value.length > 0 && searchQuery.value.length < 8) {
        searchError.value = 'Please enter at least 8 digits';
    }
};

const performSearch = async () => {
    if (searchQuery.value.length > 0 && searchQuery.value.length < 8) {
        searchError.value = 'Please enter at least 8 digits';
        return;
    }
    
    fetchPhoneNumbers();
};

const clearSearch = () => {
    searchQuery.value = '';
    searchError.value = '';
    fetchPhoneNumbers();
};

const editPhoneNumber = (item) => {
    editData.value = {
        id: item.id,
        phone_number: item.phone_number,
        remarks: item.remarks
    };
    showEditModal.value = true;
};

const submitEdit = async () => {
    if (!editData.value.phone_number.trim()) {
        showToast('Please enter a phone number', 'error');
        return;
    }
    
    if (!editData.value.remarks.trim()) {
        showToast('Please enter remarks', 'error');
        return;
    }
    
    submitting.value = true;
    
    try {
        const token = localStorage.getItem('tl_auth_token');
        const response = await fetch(`${API_BASE_URL}/api/phone-numbers/${editData.value.id}`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                phone_number: editData.value.phone_number,
                remarks: editData.value.remarks
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Phone number updated successfully!', 'success');
            closeEditModal();
            fetchPhoneNumbers();
        } else {
            showToast(data.message || 'Failed to update phone number', 'error');
        }
    } catch (err) {
        showToast('Failed to connect to server', 'error');
        console.error('Error:', err);
    } finally {
        submitting.value = false;
    }
};

const closeEditModal = () => {
    showEditModal.value = false;
    editData.value = {
        id: null,
        phone_number: '',
        remarks: ''
    };
};

const logout = () => {
    localStorage.removeItem('tl_auth_token');
    localStorage.removeItem('tl_user');
    window.location.href = '/';
};
</script>
