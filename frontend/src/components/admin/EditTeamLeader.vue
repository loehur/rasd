<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 p-6">
        <div class="max-w-4xl mx-auto">
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
                        />
                    </svg>
                    Back to Team Leader List
                </button>
                <h1
                    class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-100 to-slate-300"
                >
                    Edit Team Leader
                </h1>
                <p class="text-slate-400 mt-2">
                    Edit Team Leader information
                </p>
            </div>

            <!-- Loading State -->
            <div
                v-if="loading"
                class="text-center py-12 bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl"
            >
                <div
                    class="inline-block w-12 h-12 border-4 border-blue-500/30 border-t-blue-500 rounded-full animate-spin"
                ></div>
                <p class="text-slate-400 mt-4">Loading team leader data...</p>
            </div>

            <!-- Edit Form -->
            <div
                v-else-if="teamLeader"
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl p-6"
            >
                <form @submit.prevent="saveTeamLeader">
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Employee ID
                            </label>
                            <input
                                v-model="teamLeader.employee_id"
                                type="text"
                                disabled
                                class="w-full px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-400 cursor-not-allowed"
                            />
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Name *
                            </label>
                            <input
                                v-model="teamLeader.name"
                                type="text"
                                required
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 focus:border-blue-500 focus:outline-none"
                            />
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Position
                            </label>
                            <input
                                v-model="teamLeader.position"
                                type="text"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 focus:border-blue-500 focus:outline-none"
                            />
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Team
                            </label>
                            <input
                                v-model="teamLeader.team"
                                type="text"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 focus:border-blue-500 focus:outline-none"
                            />
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Department
                            </label>
                            <input
                                v-model="teamLeader.department"
                                type="text"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 focus:border-blue-500 focus:outline-none"
                            />
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Area
                            </label>
                            <input
                                v-model="teamLeader.area"
                                type="text"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 focus:border-blue-500 focus:outline-none"
                            />
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Hire Date
                            </label>
                            <input
                                v-model="teamLeader.hire_date"
                                type="date"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-100 focus:border-blue-500 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div class="flex gap-3 pt-6 border-t border-slate-800">
                        <button
                            type="button"
                            @click="openResetModal"
                            class="px-4 py-2 text-sm font-medium text-amber-300 bg-amber-500/20 border border-amber-500/30 rounded-lg hover:bg-amber-500/30 transition flex items-center gap-2"
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
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                                ></path>
                            </svg>
                            Reset Password
                        </button>
                        <div class="flex-1"></div>
                        <button
                            type="button"
                            @click="goBack"
                            class="px-6 py-2 text-sm font-medium text-slate-300 bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 transition"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="saving"
                            class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-emerald-600 rounded-lg hover:from-blue-700 hover:to-emerald-700 transition disabled:opacity-50"
                        >
                            {{ saving ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reset Password Confirmation Modal -->
        <div
            v-if="showResetModal"
            @click="closeResetModal"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50"
        >
            <div
                @click.stop
                class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-md w-full"
            >
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-amber-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg
                            class="w-8 h-8 text-amber-400"
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
                    </div>
                    <h3 class="text-xl font-bold text-slate-100 mb-2">
                        Reset Password
                    </h3>
                    <p class="text-slate-400 text-sm mb-4">
                        Anda akan mereset password untuk:
                    </p>
                    <div class="bg-slate-800/50 rounded-lg p-4 mb-4">
                        <p class="text-slate-100 font-semibold">
                            {{ teamLeader.name }}
                        </p>
                        <p class="text-slate-400 text-sm font-mono">
                            {{ teamLeader.employee_id }}
                        </p>
                    </div>
                    <div class="bg-amber-500/10 border border-amber-500/30 rounded-lg p-3 mb-4">
                        <p class="text-amber-300 text-sm">
                            Password akan direset ke: <span class="font-mono font-bold">"tl1230"</span>
                        </p>
                        <p class="text-amber-400/70 text-xs mt-1">
                            Team Leader harus mengganti password saat login pertama kali
                        </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button
                        @click="closeResetModal"
                        :disabled="resetting"
                        class="flex-1 px-4 py-2 text-sm font-medium text-slate-300 bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 transition disabled:opacity-50"
                    >
                        Batal
                    </button>
                    <button
                        @click="confirmReset"
                        :disabled="resetting"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-amber-600 to-orange-600 rounded-lg hover:from-amber-700 hover:to-orange-700 transition disabled:opacity-50"
                    >
                        {{ resetting ? 'Mereset...' : 'Reset Password' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div
            v-if="toast.show"
            class="fixed bottom-4 right-4 z-50 animate-slide-up"
        >
            <div
                class="px-6 py-4 rounded-lg shadow-xl border flex items-center gap-3 min-w-80"
                :class="{
                    'bg-emerald-500/20 border-emerald-500/30': toast.type === 'success',
                    'bg-red-500/20 border-red-500/30': toast.type === 'error',
                    'bg-blue-500/20 border-blue-500/30': toast.type === 'info',
                }"
            >
                <svg
                    v-if="toast.type === 'success'"
                    class="w-6 h-6 text-emerald-400 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                </svg>
                <svg
                    v-if="toast.type === 'error'"
                    class="w-6 h-6 text-red-400 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                </svg>
                <p
                    class="text-sm font-medium"
                    :class="{
                        'text-emerald-100': toast.type === 'success',
                        'text-red-100': toast.type === 'error',
                        'text-blue-100': toast.type === 'info',
                    }"
                >
                    {{ toast.message }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { API_BASE_URL } from '@/config/api';

const teamLeader = ref(null);
const loading = ref(false);
const saving = ref(false);
const showResetModal = ref(false);
const resetting = ref(false);
const toast = ref({ show: false, message: '', type: '' });

const employeeId = window.location.pathname.split('/').pop();

onMounted(() => {
    fetchTeamLeader();
});

const fetchTeamLeader = async () => {
    loading.value = true;
    try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${API_BASE_URL}/api/team-leaders/${employeeId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
            },
        });
        const data = await res.json();
        if (data.success) {
            teamLeader.value = data.data;
        } else {
            showToast('Failed to load team leader data', 'error');
        }
    } catch (e) {
        showToast('Connection error', 'error');
        console.error('Load error:', e);
    } finally {
        loading.value = false;
    }
};

const saveTeamLeader = async () => {
    saving.value = true;
    try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${API_BASE_URL}/api/team-leaders/${employeeId}`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(teamLeader.value),
        });
        const data = await res.json();
        if (data.success) {
            showToast('Team Leader data updated successfully', 'success');
        } else {
            showToast(data.message || 'Failed to update', 'error');
        }
    } catch (e) {
        showToast('Connection error', 'error');
        console.error('Save error:', e);
    } finally {
        saving.value = false;
    }
};

const openResetModal = () => {
    showResetModal.value = true;
};

const closeResetModal = () => {
    showResetModal.value = false;
};

const confirmReset = async () => {
    resetting.value = true;
    try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${API_BASE_URL}/api/team-leaders/${employeeId}/reset-password`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
        });
        const data = await res.json();
        if (data.success) {
            showToast('Password berhasil di-reset ke "tl1230"', 'success');
            closeResetModal();
        } else {
            showToast(data.message || 'Gagal reset password', 'error');
        }
    } catch (e) {
        showToast('Terjadi kesalahan saat reset password', 'error');
        console.error('Reset password error:', e);
    } finally {
        resetting.value = false;
    }
};

const showToast = (message, type = 'info') => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 4000);
};

const goBack = () => {
    window.location.href = '/admin/team-leader-list';
};
</script>
