<template>
    <div
        class="min-h-screen bg-slate-900 text-white flex items-center justify-center"
    >
        <div class="bg-slate-800 px-6 py-4 rounded-lg shadow">
            <h1 class="text-2xl font-bold mb-2">Tailwind OK</h1>
            <p class="mb-4">Counter: {{ count }}</p>
            <button
                class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700"
                @click="count++"
            >
                +1
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
const count = ref(0);
</script>
