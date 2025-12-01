<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <AdminHeader title="Change Password" subtitle="Admin Portal" />
        <main class="p-4 sm:p-6 lg:p-8">
            <div class="max-w-2xl mx-auto">
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

                <!-- Change Password Form -->
                <div
                    class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl p-6"
                >
                    <form @submit.prevent="updatePassword">
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-slate-300 mb-2"
                            >
                                Current Password
                            </label>
                            <input
                                v-model="passwordForm.current_password"
                                type="password"
                                required
                                class="w-full px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-blue-500 transition"
                                placeholder="Enter current password"
                            />
                        </div>
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-slate-300 mb-2"
                            >
                                New Password
                            </label>
                            <input
                                v-model="passwordForm.new_password"
                                type="password"
                                required
                                minlength="6"
                                class="w-full px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-blue-500 transition"
                                placeholder="Enter new password (min 6 characters)"
                            />
                        </div>
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-slate-300 mb-2"
                            >
                                Confirm New Password
                            </label>
                            <input
                                v-model="passwordForm.confirm_password"
                                type="password"
                                required
                                minlength="6"
                                class="w-full px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-blue-500 transition"
                                placeholder="Confirm new password"
                            />
                        </div>
                        <button
                            type="submit"
                            :disabled="loadingPassword"
                            class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-700 text-white font-medium rounded-lg transition"
                        >
                            {{
                                loadingPassword
                                    ? "Updating..."
                                    : "Change Password"
                            }}
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { API_BASE_URL } from "@/config/api";
import AdminHeader from "./AdminHeader.vue";

const user = ref({
    name: "",
    phone_number: "",
    role: "",
});

const dropdownOpen = ref(false);
const successMessage = ref("");
const errorMessage = ref("");
const loadingPassword = ref(false);

const passwordForm = ref({
    current_password: "",
    new_password: "",
    confirm_password: "",
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

    // Close dropdown when clicking outside
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".relative")) {
            dropdownOpen.value = false;
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
        const response = await fetch(`${API_BASE_URL}/api/account/password`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                current_password: passwordForm.value.current_password,
                new_password: passwordForm.value.new_password,
                phone_number: user.value.phone_number,
            }),
        });

        const data = await response.json();

        if (response.ok && data.success) {
            successMessage.value = "Password changed successfully!";

            // Clear password form
            passwordForm.value = {
                current_password: "",
                new_password: "",
                confirm_password: "",
            };

            setTimeout(() => {
                successMessage.value = "";
            }, 3000);
        } else {
            errorMessage.value = data.message || "Failed to change password";
        }
    } catch (error) {
        console.error("Change password error:", error);
        errorMessage.value = "An error occurred: " + error.message;
    } finally {
        loadingPassword.value = false;
    }
};

const goBack = () => {
    window.location.href = "/admin/dashboard";
};

const goToDashboard = () => {
    window.location.href = "/admin/dashboard";
};

const goToAccount = () => {
    window.location.href = "/admin/account";
};

const logout = () => {
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user");
    window.location.href = "/admin";
};
</script>
