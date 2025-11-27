// FILE: src/main.js
import { createApp } from "vue";
import PublicApp from "./components/PublicApp.vue";

// import Tailwind CSS
import "./assets/tailwind.css";

console.log("DEBUG: src/main.js loaded");
try {
    const app = createApp(PublicApp);
    console.log("DEBUG: createApp OK, mounting...");
    app.mount("#app");
    console.log("DEBUG: mount called");
} catch (err) {
    console.error("ERROR mounting Vue app:", err);
    const el = document.getElementById("fallback");
    if (el) el.textContent = "Vue mount error: " + err.message;
}
