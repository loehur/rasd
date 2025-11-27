import { API_BASE_URL } from '../config/api';

/**
 * Fetch staff list for the logged-in team leader
 */
export async function getTeamLeaderStaff() {
    const token = localStorage.getItem('tl_auth_token');

    const response = await fetch(`${API_BASE_URL}/api/team-leader/staff`, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
        },
    });

    return await response.json();
}

/**
 * Submit attendance records for staff
 */
export async function submitAttendance(attendanceData) {
    const token = localStorage.getItem('tl_auth_token');

    const response = await fetch(`${API_BASE_URL}/api/attendance`, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(attendanceData),
    });

    return await response.json();
}
