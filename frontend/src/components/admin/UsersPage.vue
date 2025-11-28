<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <nav
            class="bg-slate-900/80 backdrop-blur border-b border-slate-800/80 sticky top-0 z-50"
        >
            <div
                class="px-3 sm:px-6 lg:px-8 h-16 flex items-center justify-between"
            >
                <div class="flex items-center gap-3">
                    <button
                        @click="goBack"
                        class="text-slate-300 hover:text-slate-100 text-sm flex items-center gap-2"
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
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                        Back to Dashboard
                    </button>
                    <span
                        class="hidden sm:inline text-sm font-bold tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400"
                        >Admin Users</span
                    >
                </div>
            </div>
        </nav>

        <main class="px-3 sm:px-6 lg:px-8 py-4">
            <div class="max-w-4xl mx-auto space-y-6">
                <div
                    class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl p-6"
                >
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold">Manage Users</h2>
                        <span
                            class="text-xs px-2 py-1 rounded bg-slate-800/60 border border-slate-700"
                            >Role: {{ currentUser.role }}</span
                        >
                    </div>

                    <div
                        v-if="currentUser.role !== 'super-admin'"
                        class="p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-xl text-yellow-400"
                    >
                        Hanya super-admin yang dapat mengakses halaman Users.
                    </div>

                    <div v-else>
                        <!-- Add New Admin -->
                        <form
                            @submit.prevent="createAdmin"
                            class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-6"
                        >
                            <div>
                                <label class="block text-sm text-slate-300 mb-2"
                                    >Name *</label
                                >
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full px-3 sm:px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg"
                                />
                            </div>
                            <div>
                                <label class="block text-sm text-slate-300 mb-2"
                                    >Phone Number *</label
                                >
                                <input
                                    v-model="form.phone_number"
                                    type="text"
                                    required
                                    class="w-full px-3 sm:px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg"
                                />
                            </div>
                            <div class="sm:col-span-2 flex justify-end">
                                <button
                                    :disabled="creating"
                                    class="w-full sm:w-auto px-5 sm:px-6 py-2 bg-emerald-600 hover:bg-emerald-700 rounded-lg text-white disabled:opacity-50"
                                >
                                    {{ creating ? "Saving..." : "Add Admin" }}
                                </button>
                            </div>
                        </form>

                        <!-- Users Table -->
                        <div class="overflow-x-auto -mx-3 sm:mx-0 hidden md:block">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr
                                        class="text-left text-slate-300 border-b border-slate-800"
                                    >
                                        <th class="py-2 px-3 sm:px-0">Name</th>
                                        <th class="py-2 px-3 sm:px-0">Phone</th>
                                        <th class="py-2">Role</th>
                                        <th class="py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="u in users"
                                        :key="u.id"
                                        class="border-b border-slate-800"
                                    >
                                        <td
                                            class="py-2 break-words px-3 sm:px-0"
                                        >
                                            {{ u.name }}
                                        </td>
                                        <td
                                            class="py-2 break-words px-3 sm:px-0"
                                        >
                                            {{ u.phone_number }}
                                        </td>
                                        <td class="py-2">{{ u.role }}</td>
                                        <td class="py-2 flex items-center gap-2">
                                            <button
                                                v-if="u.role !== 'super-admin' && currentUser.role === 'super-admin'"
                                                @click="resetPassword(u.id)"
                                                class="w-full sm:w-auto px-3 py-1 text-sm bg-blue-600 hover:bg-blue-700 rounded text-white"
                                            >
                                                Reset
                                            </button>
                                            <button
                                                v-if="u.role !== 'super-admin' && currentUser.role === 'super-admin'"
                                                @click="removeUser(u.id)"
                                                class="w-full sm:w-auto px-3 py-1 text-sm bg-red-600 hover:bg-red-700 rounded text-white"
                                            >
                                                Delete
                                            </button>
                                        </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile list -->
                <div class="md:hidden space-y-3">
                    <div v-for="u in users" :key="u.id" class="p-4 bg-slate-800/50 border border-slate-700 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-100">{{ u.name }}</p>
                                <p class="text-xs text-slate-400">{{ u.phone_number }}</p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded bg-slate-700 text-slate-300">{{ u.role }}</span>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button
                                v-if="u.role !== 'super-admin' && currentUser.role === 'super-admin'"
                                @click="resetPassword(u.id)"
                                class="flex-1 px-3 py-2 text-xs bg-blue-600 hover:bg-blue-700 rounded text-white"
                            >Reset</button>
                            <button
                                v-if="u.role !== 'super-admin' && currentUser.role === 'super-admin'"
                                @click="removeUser(u.id)"
                                class="flex-1 px-3 py-2 text-xs bg-red-600 hover:bg-red-700 rounded text-white"
                            >Delete</button>
                        </div>
                    </div>
                </div>
                    </div>
                </div>

                <!-- Toast -->
                <div
                    v-if="toast.show"
                    :class="[
                        'fixed top-4 left-1/2 -translate-x-1/2 sm:left-auto sm:translate-x-0 sm:right-4 px-4 sm:px-6 py-3 sm:py-4 rounded-lg shadow-lg z-50',
                        toast.type === 'success'
                            ? 'bg-green-600'
                            : 'bg-red-600',
                    ]"
                >
                    <span class="text-white">{{ toast.message }}</span>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { API_BASE_URL } from "../../config/api";

const currentUser = ref({ role: "" });
const users = ref([]);
const form = ref({ name: "", phone_number: "" });
const creating = ref(false);
const toast = ref({ show: false, message: "", type: "success" });

onMounted(async () => {
    const userData = localStorage.getItem("user");
    if (!userData) {
        window.location.href = "/admin";
        return;
    }
    currentUser.value = JSON.parse(userData);

    await loadUsers();
});

const loadUsers = async () => {
    try {
        const token = localStorage.getItem("auth_token");
        const role = JSON.parse(localStorage.getItem("user") || "{}").role;
        const res = await fetch(`${API_BASE_URL}/api/users`, {
            method: "GET",
            headers: { Authorization: `Bearer ${token}`, "X-Role": role },
        });
        const data = await res.json();
        if (data.success) {
            users.value = data.data;
        }
    } catch (e) {
        console.error("Load users error:", e);
    }
};

const createAdmin = async () => {
    creating.value = true;
    try {
        const token = localStorage.getItem("auth_token");
        const role = JSON.parse(localStorage.getItem("user") || "{}").role;
        const res = await fetch(`${API_BASE_URL}/api/users`, {
            method: "POST",
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "application/json",
                "X-Role": role,
            },
            body: JSON.stringify({
                name: form.value.name,
                phone_number: form.value.phone_number,
            }),
        });
        const data = await res.json();
        if (data.success) {
            showToast("Admin user created");
            form.value = { name: "", phone_number: "" };
            await loadUsers();
        } else {
            showToast(data.message || "Failed to create", "error");
        }
    } catch (e) {
        showToast("Error creating user", "error");
    } finally {
        creating.value = false;
    }
};

const resetPassword = async (id) => {
    try {
        const token = localStorage.getItem("auth_token");
        const role = JSON.parse(localStorage.getItem("user") || "{}").role;
        const res = await fetch(
            `${API_BASE_URL}/api/users/${id}/reset-password`,
            {
                method: "POST",
                headers: { Authorization: `Bearer ${token}`, "X-Role": role },
            }
        );
        const data = await res.json();
        if (data.success) {
            showToast('Password reset to "Admin123!"');
        } else {
            showToast(data.message || "Reset failed", "error");
        }
    } catch (e) {
        showToast("Error resetting password", "error");
    }
};

const removeUser = async (id) => {
    try {
        if (currentUser.value.role !== 'super-admin') {
            showToast('Forbidden', 'error');
            return;
        }
        const { deleteUser } = await import('@/utils/api');
        const res = await deleteUser(id);
        if (res.success) {
            showToast('User deleted');
            await loadUsers();
        } else {
            showToast(res.message || 'Delete failed', 'error');
        }
    } catch (e) {
        showToast('Error deleting user', 'error');
    }
};

const showToast = (message, type = "success") => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

const goBack = () => {
    window.location.href = "/admin/dashboard";
};
</script>
