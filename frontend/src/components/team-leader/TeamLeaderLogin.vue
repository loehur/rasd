<template>
    <div
        class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 flex items-center justify-center p-4"
    >
        <div class="w-full max-w-md">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <div
                    class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-blue-500 to-emerald-400 flex items-center justify-center shadow-2xl"
                >
                    <svg
                        class="w-10 h-10 text-white"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                    </svg>
                </div>
                <h1
                    class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-emerald-400"
                >
                    Team Leader Portal
                </h1>
                <p class="text-slate-400 mt-2">Sign in to access your dashboard</p>
            </div>

            <!-- Login Form -->
            <div
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl p-8 shadow-2xl"
            >
                <form @submit.prevent="handleLogin">
                    <!-- Employee ID Input -->
                    <div class="mb-6">
                        <label
                            for="employee_id"
                            class="block text-sm font-semibold text-slate-300 mb-2"
                        >
                            Employee ID
                        </label>
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                                <svg
                                    class="w-5 h-5 text-slate-500"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"
                                    ></path>
                                </svg>
                            </div>
                            <input
                                id="employee_id"
                                v-model="credentials.employee_id"
                                type="text"
                                required
                                placeholder="Enter your employee ID"
                                class="w-full pl-10 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition"
                                :disabled="loading"
                            />
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="mb-6">
                        <label
                            for="password"
                            class="block text-sm font-semibold text-slate-300 mb-2"
                        >
                            Password
                        </label>
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                                <svg
                                    class="w-5 h-5 text-slate-500"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    ></path>
                                </svg>
                            </div>
                            <input
                                id="password"
                                v-model="credentials.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                placeholder="Enter your password"
                                class="w-full pl-10 pr-12 py-3 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition"
                                :disabled="loading"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300 transition"
                            >
                                <svg
                                    v-if="!showPassword"
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    ></path>
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                    ></path>
                                </svg>
                                <svg
                                    v-else
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                                    ></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div
                        v-if="errorMessage"
                        class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-lg flex items-start gap-3"
                    >
                        <svg
                            class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5"
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
                        <p class="text-sm text-red-300">{{ errorMessage }}</p>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-3 px-6 bg-gradient-to-r from-blue-600 to-emerald-500 text-white font-semibold rounded-lg hover:from-blue-500 hover:to-emerald-400 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-lg"
                    >
                        <span
                            v-if="loading"
                            class="w-5 h-5 border-2 border-white/70 border-t-transparent rounded-full animate-spin"
                        ></span>
                        <span>{{ loading ? "Signing in..." : "Sign In" }}</span>
                    </button>
                </form>

                <!-- Footer -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-slate-500">
                        Default password: <span class="font-mono text-emerald-400">tl1230</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { API_BASE_URL } from "@/config/api";

const credentials = ref({
    employee_id: "",
    password: "",
});

const loading = ref(false);
const errorMessage = ref("");
const showPassword = ref(false);

const handleLogin = async () => {
    loading.value = true;
    errorMessage.value = "";

    try {
        const response = await fetch(`${API_BASE_URL}/api/team-leader/login`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify(credentials.value),
        });

        const data = await response.json();

        if (data.success) {
            // Store auth data
            localStorage.setItem("tl_auth_token", data.token);
            localStorage.setItem("tl_user", JSON.stringify(data.user));

            // Store default password flag
            if (data.is_default_password) {
                localStorage.setItem("tl_must_change_password", "true");
                // Redirect to change password page
                window.location.href = "/team-leader/change-password";
            } else {
                localStorage.removeItem("tl_must_change_password");
                // Redirect to Team Leader dashboard
                window.location.href = "/team-leader/dashboard";
            }
        } else {
            errorMessage.value =
                data.message || "Invalid employee ID or password";
        }
    } catch (error) {
        errorMessage.value =
            "Connection error. Please make sure the server is running.";
        console.error("Login error:", error);
    } finally {
        loading.value = false;
    }
};
</script>
