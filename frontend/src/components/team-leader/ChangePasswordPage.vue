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
                    Change Password
                </h2>
                <p class="text-gray-600 mt-2">
                    Update your password to keep your account secure
                </p>
            </div>

            <!-- Warning for Default Password -->
            <div
                v-if="mustChangePassword"
                class="mb-6 p-4 bg-yellow-50 border border-yellow-300 rounded-lg flex items-start gap-3"
            >
                <svg
                    class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                    ></path>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-yellow-800">
                        Default Password Detected
                    </p>
                    <p class="text-sm text-yellow-700 mt-1">
                        You are currently using the default password. For
                        security reasons, you must change your password before
                        you can access the dashboard and record attendance.
                    </p>
                </div>
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

            <!-- Change Password Form -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
            >
                <div
                    class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50"
                >
                    <h3 class="text-xl font-bold text-gray-900">
                        Update Password
                    </h3>
                </div>

                <form @submit.prevent="updatePassword" class="p-6 space-y-6">
                    <!-- Current Password -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Current Password *
                        </label>
                        <input
                            v-model="passwordForm.current_password"
                            type="password"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            placeholder="Enter current password"
                        />
                    </div>

                    <!-- New Password -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            New Password *
                        </label>
                        <input
                            v-model="passwordForm.new_password"
                            type="password"
                            required
                            minlength="6"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            placeholder="Enter new password (min 6 characters)"
                        />
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Confirm New Password *
                        </label>
                        <input
                            v-model="passwordForm.confirm_password"
                            type="password"
                            required
                            minlength="6"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            placeholder="Confirm new password"
                        />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="loadingPassword"
                            class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-purple-700 disabled:opacity-50 transition"
                        >
                            {{
                                loadingPassword
                                    ? "Changing Password..."
                                    : "Change Password"
                            }}
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
const loadingPassword = ref(false);
const mustChangePassword = ref(false);

const user = ref({
    employee_id: "",
    name: "",
});

const passwordForm = ref({
    current_password: "",
    new_password: "",
    confirm_password: "",
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

    // Check if user must change password
    mustChangePassword.value =
        localStorage.getItem("tl_must_change_password") === "true";

    // Close dropdown when clicking outside
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".relative")) {
            showDropdown.value = false;
        }
    });
});

const updatePassword = async () => {
    successMessage.value = "";
    errorMessage.value = "";

    // Validate passwords match
    if (
        passwordForm.value.new_password !== passwordForm.value.confirm_password
    ) {
        errorMessage.value = "New passwords do not match";
        return;
    }

    loadingPassword.value = true;

    try {
        const token = localStorage.getItem("tl_auth_token");
        const response = await fetch(
            `${API_BASE_URL}/api/team-leader/account/password`,
            {
                method: "PUT",
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    current_password: passwordForm.value.current_password,
                    new_password: passwordForm.value.new_password,
                }),
            }
        );

        const data = await response.json();

        if (data.success) {
            successMessage.value = "Password changed successfully!";

            // Clear the must change password flag
            localStorage.removeItem("tl_must_change_password");

            // Clear password form
            passwordForm.value = {
                current_password: "",
                new_password: "",
                confirm_password: "",
            };

            // Redirect to dashboard after 2 seconds
            setTimeout(() => {
                window.location.href = "/team-leader/dashboard";
            }, 2000);
        } else {
            errorMessage.value = data.message || "Failed to change password";
        }
    } catch (error) {
        errorMessage.value = "An error occurred. Please try again.";
        console.error("Error:", error);
    } finally {
        loadingPassword.value = false;
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
