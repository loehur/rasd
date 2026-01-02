# Staff Login - Menggunakan Tabel Staff

## ✅ Perubahan yang Dilakukan

### Sebelumnya (SALAH ❌):
- Staff login menggunakan tabel `users` dengan role='staff'
- Menggunakan phone_number sebagai username

### Sekarang (BENAR ✅):
- Staff login menggunakan tabel `staff` dengan role='staff'  
- Menggunakan `staff_id` sebagai employee ID (sama seperti Team Leader)
- Password default: **`staff1230`**

## 📊 Statistik Database

Total staff dengan role='staff' di database: **524 staff**

Semua staff sudah di-set password default: **`staff1230`**

## 🔐 Cara Login Staff

### Kredensial Login:
- **Employee ID**: `staff_id` dari tabel staff (contoh: IDNA200001, IDNB300002, dll)
- **Password**: `staff1230` (default untuk semua staff)

### Langkah Testing:

1. **Buka halaman login:** http://localhost/jobs/sd_pro/public/

2. **Cari staff_id** dari database:
   ```sql
   SELECT staff_id, name, position, department 
   FROM staff 
   WHERE role = 'staff' 
   LIMIT 10;
   ```

3. **Login dengan:**
   - Employee ID: (gunakan salah satu staff_id dari query di atas)
   - Password: `staff1230`

4. **Hasil yang diharapkan:**
   - ✅ Staff berhasil login
   - ✅ Redirect ke dashboard team leader
   - ✅ Muncul welcome message untuk staff
   - ✅ Semua menu cards tersembunyi (belum ada akses)
   - ✅ Hanya dropdown Account/Change Password/Logout yang tersedia

## 🛠️ File yang Dimodifikasi

### 1. TeamLeaderController.php
**Lokasi:** `app/Http/Controllers/TeamLeaderController.php`

**Perubahan:**
- Method `login()` sekarang mencari staff di tabel `staff` dengan role='staff'
- Tidak lagi menggunakan tabel `users`
- Menggunakan `staff_id` untuk autentikasi, bukan phone_number
- Verifikasi password menggunakan `password_verify()`
- Default password check untuk 'staff1230'

### 2. Migration File
**Lokasi:** `database/migrations/2026_01_02_000002_set_default_password_for_staff_table.php`

**Fungsi:**
- Set password default `staff1230` untuk semua staff dengan role='staff'
- Hanya update staff yang password-nya NULL atau kosong
- Total 524 staff sudah di-update

**Status:** ✅ Migration berhasil dijalankan

## 📝 Query Berguna

### Lihat semua staff:
```sql
SELECT staff_id, name, role, position, department 
FROM staff 
WHERE role = 'staff' 
ORDER BY name 
LIMIT 20;
```

### Cek password staff tertentu:
```sql
SELECT staff_id, name, 
       CASE 
           WHEN password IS NOT NULL THEN 'Has password' 
           ELSE 'No password' 
       END as password_status
FROM staff 
WHERE role = 'staff'
LIMIT 10;
```

### Count staff by role:
```sql
SELECT role, COUNT(*) as total 
FROM staff 
GROUP BY role;
```

## 🔧 Command Artisan

### Reset password semua staff ke default:
```bash
# Jika perlu reset ulang, jalankan migration fresh
php artisan migrate:fresh --seed

# Atau jalankan ulang migration spesifik
php artisan migrate:rollback --step=1
php artisan migrate
```

### Cek via Tinker:
```bash
php artisan tinker

# Lihat beberapa staff
>>> \App\Models\Staff::where('role', 'staff')->take(5)->get(['staff_id', 'name', 'position'])

# Count total staff
>>> \App\Models\Staff::where('role', 'staff')->count()
```

## 🎯 Flow Login

```
User mengisi form:
├─ Employee ID: [staff_id dari tabel staff]
├─ Password: staff1230
│
Controller checks:
├─ 1. Cari di tabel staff dengan role='tl' (Team Leader) ❌
├─ 2. Cari di tabel staff dengan role='staff' ✅
│   ├─ Verifikasi password dengan password_verify()
│   ├─ Generate token
│   └─ Return user data dengan role='staff'
│
Dashboard:
├─ Detect role='staff'
├─ Hide all menu cards
└─ Show welcome message
```

## ⚠️ Catatan Penting

1. **Tabel yang Digunakan:**
   - Team Leader: tabel `staff` dengan role='tl'
   - Staff: tabel `staff` dengan role='staff'
   - Admin/Super Admin: tabel `users` (tidak berubah)

2. **Password Default:**
   - Team Leader: `tl1230`
   - Staff: `staff1230`

3. **Field Login:**
   - Team Leader & Staff: `staff_id` (Employee ID)
   - Admin: `phone_number`

4. **Total Staff di Database:** 524 staff
   - Semua sudah memiliki password default `staff1230`

## 📱 Testing Checklist

- [x] Login controller updated ke tabel staff
- [x] Migration untuk set password default dibuat
- [x] Migration berhasil dijalankan (524 staff updated)
- [x] Password verification menggunakan password_verify()
- [x] Return data dengan role='staff'
- [ ] Test login dengan staff_id dari database
- [ ] Verify dashboard shows welcome message
- [ ] Verify all menu cards hidden for staff

## 🚀 Next Steps

1. **Test login dengan data real** dari database
2. **Verify staff dapat login** dengan staff_id mereka
3. **Confirm dashboard** menampilkan UI yang benar untuk staff
4. **Optional:** Implement force password change untuk first login
