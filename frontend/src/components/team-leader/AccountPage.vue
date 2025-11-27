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
                                href="/team-leader/dashboard"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition"
                            >
                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Dashboard
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
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Back Button -->
            <button
                @click="goBack"
                class="mb-6 flex items-center gap-2 text-indigo-600 hover:text-indigo-700 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </button>

            <!-- Page Header -->
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Account Settings</h2>
                <p class="text-gray-600 mt-2">Update your account information</p>
            </div>

            <!-- Success Message -->
            <div
                v-if="successMessage"
                class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700"
            >
                {{ successMessage }}
            </div>

            <!-- Error Message -->
            <div
                v-if="errorMessage"
                class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700"
            >
                {{ errorMessage }}
            </div>

            <!-- Update Profile Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <h3 class="text-xl font-bold text-gray-900">Update Profile</h3>
                </div>

                <form @submit.prevent="updateProfile" class="p-6 space-y-6">
                    <!-- Employee ID (Read-only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Employee ID
                        </label>
                        <input
                            v-model="user.employee_id"
                            type="text"
                            readonly
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500"
                        />
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name *
                        </label>
                        <input
                            v-model="profileForm.name"
                            type="text"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            placeholder="Enter your full name"
                        />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="loadingProfile"
                            class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-purple-700 disabled:opacity-50 transition"
                        >
                            {{ loadingProfile ? 'Updating...' : 'Update Profile' }}
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { API_BASE_URL } from '../../config/api';

const userName = ref('');
const showDropdown = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const loadingProfile = ref(false);

const user = ref({
    employee_id: '',
    name: '',
});

const profileForm = ref({
    name: '',
});

onMounted(() => {
    // Check if user is logged in
    const authToken = localStorage.getItem('tl_auth_token');
    const userData = localStorage.getItem('tl_user');

    if (!authToken || !userData) {
        window.location.href = '/';
        return;
    }

    // Load user data
    const parsedUser = JSON.parse(userData);
    user.value = parsedUser;
    userName.value = parsedUser.name;
    profileForm.value.name = parsedUser.name;

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.relative')) {
            showDropdown.value = false;
        }
    });
});

const updateProfile = async () => {
    successMessage.value = '';
    errorMessage.value = '';
    loadingProfile.value = true;

    try {
        const token = localStorage.getItem('tl_auth_token');
        const response = await fetch(`${API_BASE_URL}/api/team-leader/account/name`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: profileForm.value.name,
            }),
        });

        const data = await response.json();

        if (data.success) {
            // Update local storage
            user.value.name = profileForm.value.name;
            userName.value = profileForm.value.name;
            localStorage.setItem('tl_user', JSON.stringify(user.value));

            successMessage.value = 'Profile updated successfully!';
            setTimeout(() => {
                successMessage.value = '';
            }, 3000);
        } else {
            errorMessage.value = data.message || 'Failed to update profile';
        }
    } catch (error) {
        errorMessage.value = 'An error occurred. Please try again.';
        console.error('Error:', error);
    } finally {
        loadingProfile.value = false;
    }
};

const goBack = () => {
    window.location.href = '/team-leader/dashboard';
};

const logout = () => {
    localStorage.removeItem('tl_auth_token');
    localStorage.removeItem('tl_user');
    window.location.href = '/';
};
</script>
