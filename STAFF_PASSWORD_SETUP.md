# Staff User Setup - Default Password Migration

## Overview
Semua user dengan role `staff` sekarang memiliki password default: **`staff1230`**

## File yang Dibuat

### 1. Migration File
**File:** `database/migrations/2026_01_02_000001_set_default_password_for_staff.php`

**Deskripsi:** Migration ini akan mengupdate password semua existing users dengan role='staff' menjadi 'staff1230'

**Cara Jalankan:**
```bash
php artisan migrate
```

### 2. Seeder File
**File:** `database/seeders/StaffUserSeeder.php`

**Deskripsi:** Seeder untuk membuat test staff users dengan password default

**Cara Jalankan:**
```bash
php artisan db:seed --class=StaffUserSeeder
```

**Staff Users yang Dibuat:**
1. **Staff Test 1** - Phone: `08123456781`
2. **Staff Test 2** - Phone: `08123456782`
3. **Staff Test 3** - Phone: `08123456783`
4. **Budi Santoso** - Phone: `08567890123`
5. **Siti Nurhaliza** - Phone: `08567890124`

Semua menggunakan password: **`staff1230`**

### 3. SQL Script (backup option)
**File:** `database/test_staff_users.sql`

Manual SQL script jika ingin insert via database client.

## Cara Testing Staff Login

### Login Credentials
Gunakan salah satu dari kombinasi berikut:

| Name | Phone Number | Password |
|------|-------------|----------|
| Staff Test 1 | 08123456781 | staff1230 |
| Staff Test 2 | 08123456782 | staff1230 |
| Staff Test 3 | 08123456783 | staff1230 |
| Budi Santoso | 08567890123 | staff1230 |
| Siti Nurhaliza | 08567890124 | staff1230 |

### Testing Steps:

1. **Buka halaman login:** http://localhost/jobs/sd_pro/public/

2. **Masukkan kredensial:**
   - Phone Number: `08123456781` (atau nomor lainnya)
   - Password: `staff1230`

3. **Klik Sign In**

4. **Verifikasi hasil:**
   - Staff berhasil login ✅
   - Redirect ke dashboard team leader ✅
   - Muncul welcome message untuk staff ✅
   - Semua menu cards disembunyikan ✅
   - Hanya dropdown Account/Change Password/Logout yang tersedia ✅

## Migration untuk Existing Staff Users

Jika Anda sudah memiliki staff users di database dan ingin memberikan mereka password default:

```bash
# Jalankan migration
php artisan migrate

# Ini akan update semua user dengan role='staff' 
# dan set password mereka menjadi 'staff1230'
```

## Catatan Penting

- Password **`staff1230`** adalah password **DEFAULT**
- User sebaiknya diminta untuk **mengganti password** setelah login pertama kali
- Password di-hash menggunakan **bcrypt** (Laravel default)
- Migration sudah dijalankan dan berhasil
- Seeder sudah membuat 5 test staff users

## Quick Commands

```bash
# Jalankan seeder untuk membuat test users
php artisan db:seed --class=StaffUserSeeder

# Cek jumlah staff users
php -r "require 'bootstrap/app.php'; echo DB::table('users')->where('role', 'staff')->count();"

# Lihat semua staff users
php artisan tinker
>>> DB::table('users')->where('role', 'staff')->get()
```

## Next Steps untuk Production

1. **Ubah password default** setelah deploy production
2. **Implementasi force password change** di login pertama
3. **Tambahkan validasi password complexity**
4. **Setup email notification** untuk password reset
