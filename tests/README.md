# Team Leader Testing Tools

Kumpulan script untuk testing dan troubleshooting Team Leader authentication.

## Scripts Available

### 1. Check Team Leader (`check_team_leader.php`)

Memeriksa informasi team leader dan verifikasi password.

**Usage:**
```bash
# Check specific team leader
php tests/check_team_leader.php IDNA102557

# List all team leaders
php tests/check_team_leader.php
```

**Output:**
- Employee ID, Name, Position, Department, Team
- Password hash information
- Password verification for common passwords (tl1230, Admin123!, tl3313)

---

### 2. Reset Team Leader Password (`reset_team_leader_password.php`)

Reset password team leader ke password baru.

**Usage:**
```bash
# Reset to specific password
php tests/reset_team_leader_password.php IDNA102557 tl3313

# Reset to default password (tl1230)
php tests/reset_team_leader_password.php IDNA102557
```

**Features:**
- Automatically handles password hashing via model mutator
- Verifies password after reset
- Shows success confirmation

**⚠️ Important:**
- Jangan gunakan `password_hash()` manual karena model sudah punya mutator
- Password akan otomatis di-hash oleh `TeamLeader::setPasswordAttribute()`

---

### 3. Test Login (`test_login.php`)

Test login flow untuk team leader.

**Usage:**
```bash
php tests/test_login.php IDNA102557 tl3313
```

**Test Steps:**
1. Find team leader by employee ID
2. Verify password
3. Check if using default password
4. Generate authentication token
5. Return login response (JSON)

**Output:**
- Step-by-step verification
- Login response in JSON format
- Note if using default password

---

## Common Issues & Solutions

### Issue: "Invalid employee ID or password" after changing password

**Cause:** Double hashing - password di-hash manual lalu di-hash lagi oleh model mutator

**Solution:**
```php
// ✗ WRONG - akan di-hash 2x
$teamLeader->password = password_hash($newPassword, PASSWORD_DEFAULT);

// ✓ CORRECT - biarkan mutator yang hash
$teamLeader->password = $newPassword;
```

**Fixed in:**
- `TeamLeaderController::updatePassword()` - line 506
- `TeamLeaderController::resetPassword()` - line 538

---

### Issue: Need to reset password for team leader

**Solution:**
```bash
# Reset ke password baru
php tests/reset_team_leader_password.php IDNA102557 newpassword123

# Reset ke default (tl1230)
php tests/reset_team_leader_password.php IDNA102557
```

---

### Issue: Check which password is currently set

**Solution:**
```bash
php tests/check_team_leader.php IDNA102557
```

Script akan otomatis test common passwords: tl1230, Admin123!, tl3313

---

## Technical Details

### Password Hashing

Model `TeamLeader` memiliki mutator yang otomatis hash password:

```php
// app/Models/TeamLeader.php
public function setPasswordAttribute($value)
{
    $this->attributes['password'] = Hash::make($value);
}
```

**Implication:**
- ✓ Pass **plain text** password saat update
- ✗ Jangan hash manual dengan `password_hash()`
- ✓ Verification menggunakan `password_verify()`

### Authentication Token

Token format: `base64_encode(employee_id:timestamp)`

**Example:**
```php
$token = base64_encode('IDNA102557:1764290278');
// Result: SUROQTEwMjU1NzoxNzY0MjkwMjc4
```

---

## Quick Reference

```bash
# List all team leaders
php tests/check_team_leader.php

# Check specific TL
php tests/check_team_leader.php IDNA102557

# Reset password
php tests/reset_team_leader_password.php IDNA102557 newpass123

# Test login
php tests/test_login.php IDNA102557 newpass123
```

---

## Notes

- All scripts require Laravel/Lumen to be properly configured
- Database connection must be working
- Scripts will output colored text in terminal for better readability
- Use these scripts for debugging and testing purposes only
