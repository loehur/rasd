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
                            STAFF PERFORMANCE
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

                    <!-- Debug info - visible on mobile -->
                    <div class="mt-4 p-3 bg-slate-950/50 rounded-lg border border-slate-700/50 text-xs">
                        <p class="text-slate-400 mb-1">Debug Info:</p>
                        <p class="text-slate-300 font-mono break-all">
                            Hostname: {{ window.location.hostname }}
                        </p>
                        <p class="text-slate-300 font-mono break-all mt-1" id="apiUrl">
                            API: Loading...
                        </p>
                    </div>

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
                    © {{ new Date().getFullYear() }} Staff Performance. All
                    rights reserved.
                </p>
            </div>
        </div>

        <!-- Right: Illustration / Accent (hidden on small screens) -->
        <div
            class="hidden md:flex md:w-1/2 relative overflow-hidden items-center justify-center p-12"
        >
            <!-- Animated gradient mesh -->
            <div class="absolute inset-0">
                <div
                    class="absolute top-0 right-0 w-96 h-96 bg-blue-500/30 rounded-full blur-3xl animate-pulse"
                ></div>
                <div
                    class="absolute bottom-0 left-0 w-96 h-96 bg-emerald-500/30 rounded-full blur-3xl animate-pulse"
                    style="animation-delay: 1.5s"
                ></div>
                <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-purple-500/20 rounded-full blur-3xl animate-pulse"
                    style="animation-delay: 3s"
                ></div>
            </div>

            <div class="relative z-10 max-w-lg w-full space-y-8">
                <!-- Main feature card -->
                <div class="relative group">
                    <div
                        class="absolute -inset-1 bg-gradient-to-r from-blue-600/40 to-emerald-600/40 rounded-3xl blur-xl group-hover:blur-2xl transition-all duration-300"
                    ></div>
                    <div
                        class="relative bg-slate-900/70 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-8 shadow-2xl"
                    >
                        <div class="flex items-start gap-4 mb-6">
                            <div
                                class="p-3 bg-gradient-to-br from-blue-500 to-emerald-400 rounded-2xl shadow-lg"
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
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    ></path>
                                </svg>
                            </div>
                            <div>
                                <h2
                                    class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-300"
                                >
                                    Real-time Staff Monitoring
                                </h2>
                                <p class="text-sm text-slate-400 mt-1">
                                    Track performance easily
                                </p>
                            </div>
                        </div>

                        <p class="text-slate-300 leading-relaxed mb-6">
                            Powerful and user-friendly monitoring system to
                            track productivity, attendance, and staff
                            performance in real-time.
                        </p>

                        <div class="space-y-4">
                            <div
                                class="flex items-center gap-4 p-4 bg-slate-950/50 rounded-xl border border-slate-700/30 group hover:border-emerald-500/30 transition-all"
                            >
                                <div
                                    class="p-2 bg-emerald-500/10 rounded-lg group-hover:bg-emerald-500/20 transition-colors"
                                >
                                    <svg
                                        class="w-5 h-5 text-emerald-400"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-slate-200">
                                        Productivity Tracking
                                    </p>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        Monitor staff performance in real-time
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-4 p-4 bg-slate-950/50 rounded-xl border border-slate-700/30 group hover:border-blue-500/30 transition-all"
                            >
                                <div
                                    class="p-2 bg-blue-500/10 rounded-lg group-hover:bg-blue-500/20 transition-colors"
                                >
                                    <svg
                                        class="w-5 h-5 text-blue-400"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-slate-200">
                                        Attendance & Timesheet
                                    </p>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        Automatic attendance and work hours
                                        tracking
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-4 p-4 bg-slate-950/50 rounded-xl border border-slate-700/30 group hover:border-purple-500/30 transition-all"
                            >
                                <div
                                    class="p-2 bg-purple-500/10 rounded-lg group-hover:bg-purple-500/20 transition-colors"
                                >
                                    <svg
                                        class="w-5 h-5 text-purple-400"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-slate-200">
                                        Reports & Analytics
                                    </p>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        Comprehensive dashboard and detailed
                                        reports
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Status badge -->
                        <div
                            class="mt-6 flex items-center justify-between pt-6 border-t border-slate-700/30"
                        >
                            <div class="flex items-center gap-2">
                                <div class="relative">
                                    <div
                                        class="w-2 h-2 bg-green-400 rounded-full"
                                    ></div>
                                    <div
                                        class="absolute inset-0 w-2 h-2 bg-green-400 rounded-full animate-ping"
                                    ></div>
                                </div>
                                <span
                                    class="text-xs font-semibold text-green-400"
                                    >System Online</span
                                >
                            </div>
                            <span class="text-xs text-slate-500 font-mono"
                                >v2.0.0</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Stats cards -->
                <div class="grid grid-cols-3 gap-4">
                    <div
                        class="bg-slate-900/50 backdrop-blur border border-slate-700/30 rounded-2xl p-4 text-center hover:border-blue-500/30 transition-all"
                    >
                        <div class="text-2xl font-bold text-blue-400">
                            99.9%
                        </div>
                        <div class="text-xs text-slate-400 mt-1">Uptime</div>
                    </div>
                    <div
                        class="bg-slate-900/50 backdrop-blur border border-slate-700/30 rounded-2xl p-4 text-center hover:border-emerald-500/30 transition-all"
                    >
                        <div class="text-2xl font-bold text-emerald-400">
                            24/7
                        </div>
                        <div class="text-xs text-slate-400 mt-1">Support</div>
                    </div>
                    <div
                        class="bg-slate-900/50 backdrop-blur border border-slate-700/30 rounded-2xl p-4 text-center hover:border-purple-500/30 transition-all"
                    >
                        <div class="text-2xl font-bold text-purple-400">
                            Fast
                        </div>
                        <div class="text-xs text-slate-400 mt-1">Loading</div>
                    </div>
                </div>
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

onMounted(() => {
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
        error.value = `❌ ${e.message}\n\n🌐 Your hostname: ${hostname}\n📡 API URL: ${apiUrl}\n\n${e.name}: ${e.stack || 'No stack trace'}`;
        console.error("Login error:", e);
        console.error("API_BASE_URL:", API_BASE_URL);
    } finally {
        loading.value = false;
    }
};
</script>
