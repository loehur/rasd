<template>
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50"
    >
        <!-- Main Content -->
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Back Button -->
            <button
                @click="goBack"
                class="mb-6 flex items-center gap-2 text-indigo-600 hover:text-indigo-700 transition"
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

            <!-- Page Header -->
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900">
                    Account Settings
                </h2>
                <p class="text-gray-600 mt-2">
                    Update your account information
                </p>
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
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
            >
                <div
                    class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50"
                >
                    <h3 class="text-xl font-bold text-gray-900">
                        Update Profile
                    </h3>
                </div>

                <form @submit.prevent="updateProfile" class="p-6 space-y-6">
                    <!-- Employee ID (Read-only) -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
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
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Full Name
                        </label>
                        <input
                            v-model="profileForm.name"
                            type="text"
                            readonly
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500"
                        />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button
                            type="button"
                            disabled
                            class="px-6 py-2 bg-gray-300 text-gray-600 font-semibold rounded-lg cursor-not-allowed"
                        >
                            Update Profile (Disabled)
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { API_BASE_URL } from "../../config/api";

const userName = ref("");
const showDropdown = ref(false);
const successMessage = ref("");
const errorMessage = ref("");
const loadingProfile = ref(false);

const user = ref({
    employee_id: "",
    name: "",
});

const profileForm = ref({
    name: "",
});

onMounted(() => {
    // Check if user is logged in
    const authToken = localStorage.getItem("tl_auth_token");
    const userData = localStorage.getItem("tl_user");

    if (!authToken || !userData) {
        window.location.href = "/";
        return;
    }

    // Load user data
    const parsedUser = JSON.parse(userData);
    user.value = parsedUser;
    userName.value = parsedUser.name;
    profileForm.value.name = parsedUser.name;

    // Close dropdown when clicking outside
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".relative")) {
            showDropdown.value = false;
        }
    });
});

const updateProfile = async () => {
    successMessage.value = "";
    errorMessage.value = "";
    loadingProfile.value = true;

    try {
        const token = localStorage.getItem("tl_auth_token");
        const response = await fetch(
            `${API_BASE_URL}/api/team-leader/account/name`,
            {
                method: "PUT",
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    name: profileForm.value.name,
                }),
            }
        );

        const data = await response.json();

        if (data.success) {
            // Update local storage
            user.value.name = profileForm.value.name;
            userName.value = profileForm.value.name;
            localStorage.setItem("tl_user", JSON.stringify(user.value));

            successMessage.value = "Profile updated successfully!";
            setTimeout(() => {
                successMessage.value = "";
            }, 3000);
        } else {
            errorMessage.value = data.message || "Failed to update profile";
        }
    } catch (error) {
        errorMessage.value = "An error occurred. Please try again.";
        console.error("Error:", error);
    } finally {
        loadingProfile.value = false;
    }
};

const goBack = () => {
    window.location.href = "/team-leader/dashboard";
};

const logout = () => {
    localStorage.removeItem("tl_auth_token");
    localStorage.removeItem("tl_user");
    window.location.href = "/";
};
</script>
