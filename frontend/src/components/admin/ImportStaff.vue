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
                        ></path>
                    </svg>
                    Back to Dashboard
                </button>
                <h1
                    class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-100 to-slate-300"
                >
                    Import Staff Data
                </h1>
                <p class="text-slate-400 mt-2">
                    Upload CSV file to import multiple staff members at once
                </p>
            </div>

            <!-- Upload Section -->
            <div
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl p-6 mb-6"
            >
                <h2 class="text-lg font-bold text-slate-100 mb-4">
                    Upload CSV File
                </h2>

                <!-- File Input -->
                <div
                    class="border-2 border-dashed border-slate-700 rounded-xl p-8 text-center hover:border-blue-500/50 transition-all cursor-pointer"
                    @click="$refs.fileInput.click()"
                    @dragover.prevent="dragOver = true"
                    @dragleave="dragOver = false"
                    @drop.prevent="handleDrop"
                    :class="{
                        'border-blue-500 bg-blue-500/5': dragOver,
                    }"
                >
                    <input
                        ref="fileInput"
                        type="file"
                        accept=".csv"
                        @change="handleFileSelect"
                        class="hidden"
                    />

                    <svg
                        v-if="!selectedFile"
                        class="w-16 h-16 mx-auto mb-4 text-slate-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                        ></path>
                    </svg>

                    <div v-if="selectedFile" class="mb-4">
                        <div
                            class="w-16 h-16 mx-auto mb-4 bg-emerald-500/20 rounded-xl flex items-center justify-center"
                        >
                            <svg
                                class="w-8 h-8 text-emerald-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                ></path>
                            </svg>
                        </div>
                        <p class="text-lg font-semibold text-emerald-400">
                            {{ selectedFile.name }}
                        </p>
                        <p class="text-sm text-slate-400 mt-1">
                            {{ formatFileSize(selectedFile.size) }} •
                            {{ csvData.length }}
                            rows detected
                        </p>
                    </div>

                    <div v-else>
                        <p class="text-slate-400 mb-2">
                            Click to upload or drag and drop
                        </p>
                        <p class="text-xs text-slate-500">
                            CSV files only (max 2MB)
                        </p>
                    </div>

                    <button
                        v-if="selectedFile"
                        @click.stop="clearFile"
                        class="mt-4 px-4 py-2 text-sm bg-red-600/20 text-red-400 border border-red-600/30 rounded-lg hover:bg-red-600/30 transition"
                    >
                        Remove File
                    </button>
                </div>

                <!-- Download Template -->
                <div class="mt-4 flex items-center justify-between">
                    <p class="text-sm text-slate-400">Don't have a CSV file?</p>
                    <a
                        href="http://localhost:8000/api/staff/template"
                        download
                        class="text-sm font-medium text-blue-400 hover:text-blue-300 underline"
                    >
                        Download Template
                    </a>
                </div>
            </div>

            <!-- Preview Section -->
            <div
                v-if="csvData.length > 0"
                class="bg-slate-900/70 backdrop-blur border border-slate-800/80 rounded-2xl p-6 mb-6"
            >
                <h2 class="text-lg font-bold text-slate-100 mb-4">
                    Preview ({{ csvData.length }} rows)
                </h2>

                <div class="overflow-x-auto">
                    <table
                        class="w-full text-sm border-collapse border border-slate-700 rounded-lg"
                    >
                        <thead class="bg-slate-800/50">
                            <tr>
                                <th
                                    v-for="header in csvHeaders"
                                    :key="header"
                                    class="border border-slate-700 px-3 py-2 text-left text-xs font-semibold text-slate-300 uppercase"
                                >
                                    {{ header }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(row, index) in csvData.slice(0, 5)"
                                :key="index"
                                class="hover:bg-slate-800/30"
                            >
                                <td
                                    v-for="header in csvHeaders"
                                    :key="header"
                                    class="border border-slate-700 px-3 py-2 text-slate-300"
                                >
                                    {{ row[header] || "-" }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p
                    v-if="csvData.length > 5"
                    class="text-xs text-slate-500 mt-2"
                >
                    Showing first 5 of {{ csvData.length }} rows
                </p>
            </div>

            <!-- Import Button -->
            <div v-if="selectedFile" class="flex gap-4">
                <button
                    @click="importData"
                    :disabled="uploading"
                    class="flex-1 py-3 px-6 bg-gradient-to-r from-blue-600 to-emerald-500 text-white font-semibold rounded-xl hover:from-blue-500 hover:to-emerald-400 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <span
                        v-if="uploading"
                        class="w-5 h-5 border-2 border-white/70 border-t-transparent rounded-full animate-spin"
                    ></span>
                    <span>{{
                        uploading ? "Importing..." : "Import Staff Data"
                    }}</span>
                </button>
            </div>

            <!-- Result Message -->
            <div
                v-if="resultMessage"
                :class="[
                    'mt-6 p-4 rounded-xl border',
                    resultSuccess
                        ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300'
                        : 'bg-red-500/10 border-red-500/30 text-red-300',
                ]"
            >
                <p class="font-semibold mb-2">{{ resultMessage }}</p>
                <div v-if="importErrors.length > 0" class="mt-2">
                    <p class="text-sm font-semibold mb-1">Errors:</p>
                    <ul class="text-xs space-y-1 max-h-40 overflow-y-auto">
                        <li v-for="(error, index) in importErrors" :key="index">
                            • {{ error }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const selectedFile = ref(null);
const csvHeaders = ref([]);
const csvData = ref([]);
const dragOver = ref(false);
const uploading = ref(false);
const resultMessage = ref("");
const resultSuccess = ref(false);
const importErrors = ref([]);

const handleFileSelect = (event) => {
    const file = event.target.files[0];
    if (file && file.type === "text/csv") {
        selectedFile.value = file;
        parseCSV(file);
    } else {
        alert("Please select a valid CSV file");
    }
};

const handleDrop = (event) => {
    dragOver.value = false;
    const file = event.dataTransfer.files[0];
    if (file && file.type === "text/csv") {
        selectedFile.value = file;
        parseCSV(file);
    }
};

const parseCSV = (file) => {
    const reader = new FileReader();
    reader.onload = (e) => {
        const text = e.target.result;
        const lines = text.split("\n").filter((line) => line.trim());

        if (lines.length > 0) {
            csvHeaders.value = lines[0].split(",").map((h) => h.trim());
            csvData.value = lines.slice(1).map((line) => {
                const values = line.split(",");
                const row = {};
                csvHeaders.value.forEach((header, index) => {
                    row[header] = values[index]?.trim() || "";
                });
                return row;
            });
        }
    };
    reader.readAsText(file);
};

const clearFile = () => {
    selectedFile.value = null;
    csvHeaders.value = [];
    csvData.value = [];
    resultMessage.value = "";
};

const formatFileSize = (bytes) => {
    if (bytes < 1024) return bytes + " B";
    if (bytes < 1048576) return (bytes / 1024).toFixed(2) + " KB";
    return (bytes / 1048576).toFixed(2) + " MB";
};

const importData = async () => {
    if (!selectedFile.value) return;

    uploading.value = true;
    resultMessage.value = "";
    importErrors.value = [];

    try {
        const formData = new FormData();
        formData.append("file", selectedFile.value);

        const response = await fetch(
            "http://localhost/sd_pro/public/api/staff/import",
            {
                method: "POST",
                headers: {
                    Accept: "application/json",
                },
                body: formData,
            }
        );

        const data = await response.json();

        if (data.success) {
            resultSuccess.value = true;
            resultMessage.value = data.message;
            if (data.data.errors && data.data.errors.length > 0) {
                importErrors.value = data.data.errors;
            }
            // Message will stay visible until user clears or uploads new file
        } else {
            resultSuccess.value = false;
            resultMessage.value = data.message || "Import failed";
        }
    } catch (error) {
        resultSuccess.value = false;
        resultMessage.value =
            "Connection error. Please make sure the server is running.";
        console.error("Import error:", error);
    } finally {
        uploading.value = false;
    }
};

const goBack = () => {
    window.location.href = "/dashboard.html";
};
</script>
