// Configuration for API endpoints
// Update this based on your environment

const API_CONFIG = {
    // Development - Laragon path
    development: "http://localhost/sd_pro/public",

    // Production - follow current origin (protocol + host + optional port)
    // This avoids mixed-content and SSL issues on deployments without HTTPS
    production: `${window.location.origin}`,
};

// Automatically detect environment
const isDevelopment =
    window.location.hostname === "localhost" ||
    window.location.hostname === "127.0.0.1";

export const API_BASE_URL = isDevelopment
    ? API_CONFIG.development
    : API_CONFIG.production;

// API Endpoints
export const API_ENDPOINTS = {
    login: `${API_BASE_URL}/api/login`,
    staff: `${API_BASE_URL}/api/staff`,
    staffImport: `${API_BASE_URL}/api/staff/import`,
    staffTemplate: `${API_BASE_URL}/api/staff/template`,
    accountName: `${API_BASE_URL}/api/account/name`,
    accountPassword: `${API_BASE_URL}/api/account/password`,
};
