<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <!-- Top Navigation Bar -->
        <nav
            class="bg-slate-900/80 backdrop-blur border-b border-slate-800/80 sticky top-0 z-50"
        >
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center gap-3">
                        <div
                            class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-emerald-400 flex items-center justify-center shadow-lg"
                        >
                            <svg
                                class="w-6 h-6 text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                ></path>
                            </svg>
                        </div>
                        <div>
                            <p
                                class="text-sm font-bold tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400"
                            >
                                STAFF PERFORMANCE
                            </p>
                            <p class="text-[0.65rem] text-slate-500">
                                Performance System
                            </p>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative">
                        <button
                            @click="toggleDropdown"
                            class="flex items-center gap-2 text-emerald-400 hover:text-emerald-300 transition cursor-pointer"
                        >
                            <div class="text-right">
                                <p class="text-sm font-semibold">
                                    {{ user.name }}
                                </p>
                                <p class="text-xs text-emerald-300/70">
                                    {{ user.role }}
                                </p>
                            </div>
                            <svg
                                class="w-4 h-4 transition-transform"
                                :class="{ 'rotate-180': dropdownOpen }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                ></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            v-if="dropdownOpen"
                            class="absolute right-0 mt-2 w-48 bg-slate-900 border border-slate-800 rounded-lg shadow-lg overflow-hidden z-50"
                        >
                            <button
                                @click="goToDashboard"
                                class="w-full px-4 py-2 text-left text-sm text-slate-300 hover:bg-slate-800 transition flex items-center gap-2"
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
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                    ></path>
                                </svg>
                                Dashboard
                            </button>
                            <button
                                @click="goToChangePassword"
                                class="w-full px-4 py-2 text-left text-sm text-slate-300 hover:bg-slate-800 transition flex items-center gap-2"
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
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                                    ></path>
                                </svg>
                                Change Password
                            </button>
                            <button
                                @click="logout"
                                class="w-full px-4 py-2 text-left text-sm text-red-400 hover:bg-slate-800 transition flex items-center gap-2"
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
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                    ></path>
                                </svg>
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            <div class="max-w-2xl mx-auto">
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
                            ></path>
                        </svg>
                        Back to Dashboard
                    </button>
                    <h1
                        class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-100 to-slate-300"
                    >
                        Account Settings
                    </h1>
                    <p class="text-slate-400 mt-2">
                        Update your account information and password
                    </p>
                </div>

                <!-- Success Message -->
                <div
                    v-if="successMessage"
                    class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-400"
                >
                    {{ successMessage }}
                </div>

                <!-- Error Message -->
                <div
                    v-if="errorMessage"
                    class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400"
                >
                    {{ errorMessage }}
                </div>

                <!-- Update Name Form -->
                <div
                    class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl p-6 mb-6"
                >
                    <h2 class="text-xl font-bold text-slate-100 mb-4">
                        Update Profile
                    </h2>
                    <form @submit.prevent="updateName">
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-slate-300 mb-2"
                            >
                                Full Name
                            </label>
                            <input
                                v-model="nameForm.name"
                                type="text"
                                required
                                class="w-full px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-blue-500 transition"
                                placeholder="Enter your name"
                            />
                        </div>
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-slate-300 mb-2"
                            >
                                Phone Number
                            </label>
                            <input
                                v-model="nameForm.phone_number"
                                type="text"
                                required
                                class="w-full px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-blue-500 transition"
                                placeholder="Enter your phone number"
                            />
                        </div>
                        <button
                            type="submit"
                            :disabled="loadingName"
                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-700 text-white font-medium rounded-lg transition"
                        >
                            {{ loadingName ? "Updating..." : "Update Profile" }}
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const user = ref({
    name: "",
    phone_number: "",
    role: "",
});

const dropdownOpen = ref(false);
const successMessage = ref("");
const errorMessage = ref("");
const loadingName = ref(false);

const nameForm = ref({
    name: "",
    phone_number: "",
});

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

onMounted(() => {
    // Check if user is logged in
    const authToken = localStorage.getItem("auth_token");
    const userData = localStorage.getItem("user");

    if (!authToken || !userData) {
        window.location.href = "/admin";
        return;
    }

    // Load user data
    user.value = JSON.parse(userData);
    nameForm.value.name = user.value.name;
    nameForm.value.phone_number = user.value.phone_number;

    // Close dropdown when clicking outside
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".relative")) {
            dropdownOpen.value = false;
        }
    });
});

const updateName = async () => {
    successMessage.value = "";
    errorMessage.value = "";
    loadingName.value = true;

    try {
        const response = await fetch(`${API_BASE_URL}/api/account/name`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                name: nameForm.value.name,
                phone_number: user.value.phone_number,
                new_phone_number: nameForm.value.phone_number,
            }),
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Update local storage
            user.value.name = nameForm.value.name;
            user.value.phone_number = nameForm.value.phone_number;
            localStorage.setItem("user", JSON.stringify(user.value));

            successMessage.value = "Profile updated successfully!";
            setTimeout(() => {
                successMessage.value = "";
            }, 3000);
        } else {
            errorMessage.value = data.message || "Failed to update name";
        }
    } catch (error) {
        errorMessage.value = "An error occurred. Please try again.";
    } finally {
        loadingName.value = false;
    }
};

const goBack = () => {
    window.location.href = "/admin/dashboard";
};

const goToDashboard = () => {
    window.location.href = "/admin/dashboard";
};

const goToChangePassword = () => {
    window.location.href = "/admin/change-password";
};

const logout = () => {
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user");
    window.location.href = "/admin";
};
</script>
