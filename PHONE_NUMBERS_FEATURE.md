# Phone Numbers Feature - Documentation

## 📞 Overview

Fitur **Phone Numbers** memungkinkan staff untuk menambahkan dan mengelola nomor telepon, sementara Team Leader dapat melihat data phone numbers dari tim mereka.

## 🎯 Features

### For Staff (role='staff'):
- ✅ **Add Phone Number** - Tambah entry nomor telepon baru
- ✅ **View Own Data** - Lihat semua phone numbers yang mereka input
- ✅ **Delete Own Data** - Hapus phone number yang mereka input
- auto-fill: Employee ID, Employee Name, Team Leader Name
- manual input: Phone Number, Remarks

### For Team Leader (role='tl'):
- ✅ **View Team Data** - Lihat semua phone numbers dari staff di tim mereka
- ❌ **Cannot Add** - Team Leader tidak bisa menambah data
- ❌ **Cannot Delete** - Team Leader tidak bisa menghapus data

## 📊 Database Structure

### Table: `phone_numbers`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary Key (auto increment) |
| staff_id | varchar | Employee ID dari staff |
| employee_name | varchar | Nama employee (auto-filled) |
| team_leader_name | varchar | Nama team leader (auto-filled) |
| phone_number | varchar | Nomor telepon (manual input) |
| remarks | text | Catatan/Keterangan (manual input) |
| created_at | timestamp | Tanggal dibuat |
| updated_at | timestamp | Tanggal diupdate |

## 🛠️ Files Created

### Backend:
1. **Migration:** `database/migrations/2026_01_02_000003_create_phone_numbers_table.php`
2. **Model:** `app/Models/PhoneNumber.php`
3. **Controller:** `app/Http/Controllers/PhoneNumberController.php`
4. **Routes:** Updated in `routes/web.php`

### Frontend:
1. **HTML Page:** `frontend/pages/team-leader/phone-numbers.html`
2. **JS Entry:** `frontend/src/pages/team-leader/phoneNumbers.js`
3. **Vue Component:** `frontend/src/components/team-leader/PhoneNumbersApp.vue`
4. **Dashboard:** Updated `DashboardApp.vue` with Phone Numbers menu card for staff

## 🔌 API Endpoints

### GET `/api/phone-numbers`
**Description:** Get all phone numbers based on user role
- **Staff:** Returns only their own data
- **Team Leader:** Returns data from their team

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "staff_id": "IDNA200001",
            "employee_name": "John Doe",
            "team_leader_name": "Jane Smith",
            "phone_number": "08123456789",
            "remarks": "Primary contact",
            "created_at": "2026-01-02T10:30:00.000000Z",
            "updated_at": "2026-01-02T10:30:00.000000Z"
        }
    ],
    "user_role": "staff"
}
```

### POST `/api/phone-numbers`
**Description:** Add new phone number (Staff only)

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
    "phone_number": "08123456789",
    "remarks": "Optional notes"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Phone number added successfully",
    "data": {
        "staff_id": "IDNA200001",
        "employee_name": "John Doe",
        "team_leader_name": "Jane Smith",
        "phone_number": "08123456789",
        "remarks": "Optional notes",
        "updated_at": "2026-01-02T10:30:00.000000Z",
        "created_at": "2026-01-02T10:30:00.000000Z",
        "id": 1
    }
}
```

### DELETE `/api/phone-numbers/{id}`
**Description:** Delete phone number (Staff can only delete their own data)

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response:**
```json
{
    "success": true,
    "message": "Phone number deleted successfully"
}
```

## 🚀 Access

### For Staff:
1. Login dengan staff_id dan password `staff1230`
2. Di dashboard, klik menu **"Phone Numbers - Manage Contacts"**
3. Form untuk add phone number akan tampil di bagian atas
4. Isi Phone Number dan Remarks (optional)
5. Klik **"Add Phone Number"**
6. Data akan muncul di tabel di bawahnya

### For Team Leader:
1. Login sebagai Team Leader
2. Access URL: `/team-leader/phone-numbers`
3. Akan melihat semua phone numbers dari staff di tim mereka
4. Tidak ada form untuk add (view-only)

## 📝 Usage Examples

### Add Phone Number (Staff):
```javascript
// Auto-filled from logged in user:
Employee ID: IDNA200001
Employee Name: Budi Santoso
Team Leader: Jane Smith

// Manual input:
Phone Number: 08123456789
Remarks: Primary contact number
```

### View Data (Team Leader):
Team Leader akan melihat tabel dengan semua data phone numbers dari:
- Staff 1: 3 phone numbers
- Staff 2: 5 phone numbers
- Staff 3: 2 phone numbers
- dll.

## 🔐 Security Features

1. **Authentication Required:** Semua endpoint memerlukan valid auth token
2. **Role-Based Access:**
   - Staff hanya bisa CRUD data mereka sendiri
   - Team Leader hanya bisa VIEW data dari tim mereka
3. **Auto-fill Protection:** Employee ID, Name, TL Name otomatis dari user login (tidak bisa dimanipulasi)
4. **Ownership Check:** Staff hanya bisa delete phone number yang mereka sendiri yang buat

## 🎨 UI Features

### For Staff:
- **Add Form:** Clean form dengan auto-filled fields (read-only)
- **Manual Input:** Clear indication untuk Phone Number dan Remarks
- **Data Table:** Lihat semua phone numbers yang sudah diinput
- **Delete Action:** Button untuk delete per row
- **Toast Notifications:** Success/error feedback

### For Team Leader:
- **Info Banner:** Penjelasan bahwa mereka view-only
- **Data Table:** Lihat semua data dari tim
- **No Form:** Tidak ada form add
- **No Actions:** Tidak ada button delete

## 🧪 Testing Steps

### Test as Staff:
1. Login dengan staff_id dari database
2. Password: `staff1230`
3. Klik menu "Phone Numbers"
4. Verify form tampil dengan auto-filled fields
5. Input phone number dan remarks
6. Submit dan verify data muncul di table
7. Try delete dan verify berhasil

### Test as Team Leader:
1. Login sebagai Team Leader
2. Navigate ke `/team-leader/phone-numbers`
3. Verify tidak ada form (view-only)
4. Verify bisa lihat data dari staff di tim mereka
5. Verify tidak ada delete button

## 📈 Future Enhancements

Potential improvements:
- Export to Excel
- Search/Filter functionality
- Bulk import phone numbers
- Phone number validation
- Duplicate detection
- Edit phone number (currently delete + add new)

## ✅ Status

- [x] Database migration created and run
- [x] Model created
- [x] Controller with role-based access
- [x] API routes configured
- [x] Frontend HTML page
- [x] Vue component with dual interface (staff/TL)
- [x] Menu added to staff dashboard
- [x] Frontend built successfully
- [ ] Testing with real data
- [ ] Documentation reviewed

---

**Created:** 2026-01-02
**Feature Type:** Staff-specific feature with TL view access
**Priority:** Medium
**Status:** Ready for testing
