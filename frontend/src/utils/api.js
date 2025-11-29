import { API_BASE_URL } from "../config/api";

/**
 * Fetch staff list for the logged-in team leader
 */
export async function getTeamLeaderStaff() {
    const token = localStorage.getItem("tl_auth_token");

    const response = await fetch(`${API_BASE_URL}/api/team-leader/staff`, {
        method: "GET",
        headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
        },
    });

    return await response.json();
}

/**
 * Submit attendance records for staff
 */
export async function submitAttendance(attendanceData) {
    const token = localStorage.getItem("tl_auth_token");

    const response = await fetch(`${API_BASE_URL}/api/attendance`, {
        method: "POST",
        headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
        },
        body: JSON.stringify(attendanceData),
    });

    return await response.json();
}

/**
 * Get all attendances with pagination
 */
export async function getAttendances(page = 1, reportDay = null, perPage = 15) {
    const token = localStorage.getItem("tl_auth_token");
    const params = new URLSearchParams();
    params.set("page", String(page));
    if (reportDay) params.set("report_day", reportDay);
    if (perPage) params.set("per_page", String(perPage));

    const response = await fetch(
        `${API_BASE_URL}/api/attendances?${params.toString()}`,
        {
            method: "GET",
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "application/json",
            },
        }
    );

    return await response.json();
}

/**
 * Get staff list for the team leader
 */
export async function getStaffByTeamLeader() {
    const token = localStorage.getItem("tl_auth_token");

    const response = await fetch(
        `${API_BASE_URL}/api/attendances/staff/team-leader`,
        {
            method: "GET",
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "application/json",
            },
        }
    );

    return await response.json();
}

/**
 * Get staff detail by ID
 */
export async function getStaffDetail(staffId) {
    const token = localStorage.getItem("tl_auth_token");

    const response = await fetch(
        `${API_BASE_URL}/api/attendances/staff/${staffId}`,
        {
            method: "GET",
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "application/json",
            },
        }
    );

    return await response.json();
}

/**
 * Create new attendance record with file upload
 */
export async function createAttendance(attendanceData, proofFile) {
    const token = localStorage.getItem("tl_auth_token");

    const formData = new FormData();

    // Append all form fields (including empty strings, skip only null/undefined)
    Object.keys(attendanceData).forEach((key) => {
        if (attendanceData[key] !== null && attendanceData[key] !== undefined) {
            formData.append(key, attendanceData[key]);
        }
    });

    // Append proof file
    if (proofFile) {
        formData.append("proof", proofFile);
    }

    const response = await fetch(`${API_BASE_URL}/api/attendances`, {
        method: "POST",
        headers: {
            Authorization: `Bearer ${token}`,
            // Don't set Content-Type, let browser set it with boundary for multipart/form-data
        },
        body: formData,
    });

    const text = await response.text();
    try {
        return JSON.parse(text);
    } catch (e) {
        console.error("Attendance create response (non-JSON):", text);
        return {
            success: false,
            message: "Server error: unexpected response (not JSON).",
        };
    }
}

/**
 * Update attendance record
 */
export async function updateAttendance(id, attendanceData) {
    const token = localStorage.getItem("tl_auth_token");

    const response = await fetch(`${API_BASE_URL}/api/attendances/${id}`, {
        method: "PUT",
        headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
        },
        body: JSON.stringify(attendanceData),
    });

    return await response.json();
}

/**
 * Delete attendance record
 */
export async function deleteAttendance(id) {
    const token = localStorage.getItem("tl_auth_token");

    const response = await fetch(`${API_BASE_URL}/api/attendances/${id}`, {
        method: "DELETE",
        headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
        },
    });

    return await response.json();
}

export async function adminGetAttendances(reportDay = null, perPage = 1000) {
    const token = localStorage.getItem("auth_token");
    const params = new URLSearchParams();
    if (reportDay) params.set("report_day", reportDay);
    if (perPage) params.set("per_page", String(perPage));
    const response = await fetch(
        `${API_BASE_URL}/api/attendances?${params.toString()}`,
        {
            method: "GET",
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "application/json",
            },
        }
    );
    return await response.json();
}

export async function adminDeleteAttendance(id) {
    const token = localStorage.getItem("auth_token");
    const role = JSON.parse(localStorage.getItem("user") || "{}").role;
    const response = await fetch(`${API_BASE_URL}/api/attendances/${id}`, {
        method: "DELETE",
        headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            "X-Role": role,
        },
    });
    return await response.json();
}

export async function getUsers() {
    const token = localStorage.getItem("auth_token");
    const role = JSON.parse(localStorage.getItem("user") || "{}").role;
    const response = await fetch(`${API_BASE_URL}/api/users`, {
        method: "GET",
        headers: { Authorization: `Bearer ${token}`, "X-Role": role },
    });
    return await response.json();
}

export async function createAdminUser(name, phone_number) {
    const token = localStorage.getItem("auth_token");
    const role = JSON.parse(localStorage.getItem("user") || "{}").role;
    const response = await fetch(`${API_BASE_URL}/api/users`, {
        method: "POST",
        headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            "X-Role": role,
        },
        body: JSON.stringify({ name, phone_number }),
    });
    return await response.json();
}

export async function resetUserPassword(id) {
    const token = localStorage.getItem("auth_token");
    const role = JSON.parse(localStorage.getItem("user") || "{}").role;
    const response = await fetch(
        `${API_BASE_URL}/api/users/${id}/reset-password`,
        {
            method: "POST",
            headers: { Authorization: `Bearer ${token}`, "X-Role": role },
        }
    );
    return await response.json();
}

export async function deleteUser(id) {
    const token = localStorage.getItem("auth_token");
    const role = JSON.parse(localStorage.getItem("user") || "{}").role;
    const response = await fetch(`${API_BASE_URL}/api/users/${id}`, {
        method: "DELETE",
        headers: { Authorization: `Bearer ${token}`, "X-Role": role },
    });
    return await response.json();
}

export async function adminDeleteStaff(staffId) {
    const token = localStorage.getItem("auth_token");
    const role = JSON.parse(localStorage.getItem("user") || "{}").role;
    const response = await fetch(`${API_BASE_URL}/api/staff/${staffId}`, {
        method: "DELETE",
        headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            "X-Role": role,
        },
    });
    return await response.json();
}
