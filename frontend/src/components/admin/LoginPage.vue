<template>
    <div
        class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100 flex flex-col md:flex-row relative overflow-hidden"
    >
        <!-- Animated background elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div
                class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl animate-pulse"
            ></div>
            <div
                class="absolute -bottom-40 -left-40 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl animate-pulse"
                style="animation-delay: 1s"
            ></div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-purple-500/5 rounded-full blur-3xl animate-pulse"
                style="animation-delay: 2s"
            ></div>
        </div>

        <!-- Left: Form -->
        <div
            class="relative z-10 w-full md:w-1/2 flex items-center justify-center px-6 py-12 sm:px-8 lg:px-16"
        >
            <div class="w-full max-w-md">
                <!-- Logo / Brand -->
                <div class="mb-10 flex items-center gap-4">
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-blue-500 to-emerald-400 rounded-2xl blur-md opacity-50 animate-pulse"
                        ></div>
                        <div
                            class="relative h-14 w-14 rounded-2xl bg-gradient-to-br from-blue-500 to-emerald-400 flex items-center justify-center shadow-2xl shadow-blue-500/50 transform hover:scale-110 transition-transform duration-300"
                        >
                            <svg
                                class="w-8 h-8 text-white"
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
                    </div>
                    <div>
                        <p
                            class="text-sm font-bold tracking-[0.15em] text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400"
                        >
                            STAFF DETAILS
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Performance Monitoring System
                        </p>
                    </div>
                </div>

                <!-- Heading -->
                <div class="mb-8">
                    <h1
                        class="text-3xl sm:text-4xl font-black tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-100 to-slate-300"
                    >
                        Welcome! 👋
                    </h1>
                    <p
                        class="mt-3 text-sm sm:text-base text-slate-400 leading-relaxed"
                    >
                        Please login using your
                        <span class="font-semibold text-emerald-400"
                            >phone number</span
                        >
                        and password to access the staff monitoring system.
                    </p>
                </div>

                <!-- Card -->
                <div
                    class="bg-slate-900/70 backdrop-blur rounded-2xl border border-slate-800/80 shadow-xl shadow-blue-500/5 p-5 sm:p-6"
                >
                    <form @submit.prevent="onSubmit" class="space-y-4">
                        <!-- Phone number -->
                        <div class="space-y-1.5">
                            <label
                                for="phone_number"
                                class="block text-xs font-semibold tracking-wide uppercase text-slate-400"
                            >
                                Phone Number
                            </label>
                            <div class="relative">
                                <span
                                    class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-xs text-slate-500"
                                >
                                    +62
                                </span>
                                <input
                                    id="phone_number"
                                    v-model="form.phone_number"
                                    type="tel"
                                    inputmode="tel"
                                    placeholder="81234567890"
                                    class="w-full rounded-lg border border-slate-700 bg-slate-900/70 pl-11 pr-3 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                />
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="space-y-1.5">
                            <div
                                class="flex items-center justify-between text-xs font-semibold tracking-wide uppercase text-slate-400"
                            >
                                <label for="password">Password</label>
                                <button
                                    type="button"
                                    class="text-[0.7rem] font-normal normal-case text-slate-400 hover:text-slate-200"
                                >
                                    Forgot password?
                                </button>
                            </div>
                            <div class="relative">
                                <input
                                    id="password"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    placeholder="••••••••"
                                    class="w-full rounded-lg border border-slate-700 bg-slate-900/70 px-3 pr-10 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center text-xs text-slate-500 hover:text-slate-200"
                                >
                                    {{ showPassword ? "Hide" : "Show" }}
                                </button>
                            </div>
                        </div>

                        <!-- Error message -->
                        <p
                            v-if="error"
                            class="text-xs sm:text-sm text-red-400 bg-red-950/60 border border-red-800/80 rounded-lg px-3 py-2 flex gap-2 items-start"
                        >
                            <span
                                class="mt-[2px] inline-flex h-3 w-3 rounded-full bg-red-500"
                            ></span>
                            <span>{{ error }}</span>
                        </p>

                        <!-- Submit -->
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 py-2.5 text-sm font-semibold rounded-lg bg-gradient-to-r from-blue-600 to-emerald-500 text-slate-50 hover:from-blue-500 hover:to-emerald-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950 transition disabled:opacity-70 disabled:cursor-not-allowed"
                            :disabled="loading"
                        >
                            <span
                                v-if="loading"
                                class="h-4 w-4 border-2 border-slate-100/70 border-t-transparent rounded-full animate-spin"
                            ></span>
                            <span>{{
                                loading ? "Processing..." : "Sign In"
                            }}</span>
                        </button>
                    </form>

                    <!-- Info kecil -->
                    <p
                        class="mt-4 text-[0.7rem] text-slate-500 text-center leading-relaxed"
                    >
                        By signing in, you agree to our
                        <a href="#" class="underline hover:text-slate-300"
                            >privacy policy</a
                        >
                        and
                        <a href="#" class="underline hover:text-slate-300"
                            >terms & conditions</a
                        >.
                    </p>
                </div>

                <!-- Footer text -->
                <p class="mt-6 text-[0.7rem] text-slate-500 text-center">
                    © {{ new Date().getFullYear() }} Staff Details. All
                    rights reserved.
                </p>
            </div>
        </div>

    </div>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import { API_BASE_URL } from "@/config/api";

const form = reactive({
    phone_number: "",
    password: "",
});

const loading = ref(false);
const error = ref("");
const showPassword = ref(false);
const hostname = ref("");

onMounted(() => {
    // Set hostname
    hostname.value = window.location.hostname;

    // Display API URL on page load
    const apiElement = document.getElementById("apiUrl");
    if (apiElement) {
        apiElement.textContent = `API: ${API_BASE_URL}/api/login`;
    }
});

const onSubmit = async () => {
    error.value = "";

    if (!form.phone_number || !form.password) {
        error.value = "Phone number and password are required.";
        return;
    }

    loading.value = true;
    try {
        // Call login API
        console.log("API URL:", `${API_BASE_URL}/api/login`);
        const response = await fetch(`${API_BASE_URL}/api/login`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({
                phone_number: form.phone_number,
                password: form.password,
            }),
        });

        console.log("Response status:", response.status);
        const data = await response.json();
        console.log("Response data:", data);

        if (data.success) {
            // Store token and user data
            localStorage.setItem("auth_token", data.data.token);
            localStorage.setItem("user", JSON.stringify(data.data.user));

            // Redirect to dashboard immediately
            window.location.href = "/admin/dashboard";
        } else {
            error.value = data.message || "Login failed. Please try again.";
        }
    } catch (e) {
        // Show detailed error for debugging on mobile
        const hostname = window.location.hostname;
        const apiUrl = `${API_BASE_URL}/api/login`;
        error.value = `❌ ${
            e.message
        }\n\n🌐 Your hostname: ${hostname}\n📡 API URL: ${apiUrl}\n\n${
            e.name
        }: ${e.stack || "No stack trace"}`;
        console.error("Login error:", e);
        console.error("API_BASE_URL:", API_BASE_URL);
    } finally {
        loading.value = false;
    }
};
</script>
