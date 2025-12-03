<template>
    <nav class="bg-slate-900/80 backdrop-blur border-b border-slate-800/80 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3 select-none">
                    <button @click="goBack" class="p-2 hover:bg-slate-800/50 rounded-lg transition">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </button>
                    <div class="text-left">
                        <p class="text-sm font-bold tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-red-300 to-orange-300">
                            {{ title }}
                        </p>
                        <p class="text-[0.7rem] text-slate-500">{{ subtitle }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-emerald-400">{{ user.name }}</p>
                    <p class="text-xs text-emerald-300/70">{{ user.role }}</p>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    title: { type: String, default: '' },
    subtitle: { type: String, default: 'Admin Portal' },
});

const user = ref({ name: '', role: '' });

onMounted(() => {
    const userData = localStorage.getItem('user');
    if (userData) {
        try {
            const parsed = JSON.parse(userData);
            user.value.name = parsed.name || '';
            user.value.role = parsed.role || '';
        } catch {}
    }
});

const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '/admin/dashboard';
    }
};
</script>

<style scoped>
</style>
