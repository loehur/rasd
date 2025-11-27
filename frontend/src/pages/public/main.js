// FILE: src/pages/public/main.js
import { createApp } from "vue";
import PublicApp from "@/components/public/PublicApp.vue";
import "@/assets/tailwind.css";

const app = createApp(PublicApp);
app.mount("#app");
