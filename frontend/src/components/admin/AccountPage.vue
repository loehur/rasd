<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <!-- Main Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            <div class="max-w-2xl mx-auto">
                <div class="mb-6">
                    <button
                        @click="goBack"
                        class="text-slate-400 hover:text-slate-200 flex items-center gap-2 transition"
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
import { API_BASE_URL } from "@/config/api";

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
