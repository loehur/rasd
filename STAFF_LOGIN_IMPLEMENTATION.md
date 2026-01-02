# Staff Login Implementation

## Summary
Staff users can now login through the Team Leader login page at `/team-leader` (or `/`). Upon login, staff users will see a welcome message but no menu items will be accessible yet.

## Changes Made

### 1. Backend Changes

#### TeamLeaderController.php
- Modified `login()` method to support both Team Leader and Staff login
- The method now tries to authenticate users in this order:
  1. First checks if user exists in `team_leaders` table (via TeamLeader model)
  2. If not found, checks if user exists in `users` table with role='staff'
  3. Returns appropriate user data with role information

**Login Flow:**
- Team Leaders: Login with `staff_id` (employee ID) and password
- Staff: Login with `phone_number` and password
- Both receive a token and user data including their role

### 2. Frontend Changes

#### DashboardApp.vue
- Added `userRole` reactive variable to track user's role
- Added conditional rendering (`v-if="userRole !== 'staff'"`) to hide menu cards for staff users
- Added a welcome message section for staff users explaining that menu access is being configured
- User role is extracted from localStorage on component mount

## Testing

### Test Staff User Credentials
A test staff user has been prepared:

**Login Credentials:**
- Phone Number: `08123456789`
- Password: `Staff123!`

### To Create Test User in Database

**Option 1: Using PHP Script (Recommended)**
```bash
# The script has already been run and shown the SQL output
# Copy the SQL from the output and run it in your database client
```

**Option 2: Run the SQL directly**
```sql
INSERT INTO `users` (`name`, `phone_number`, `password`, `role`, `created_at`, `updated_at`) 
VALUES (
    'Staff Test User',
    '08123456789',
    '$2y$10$gPgMAefNvqPzIzwlOU0/3OekFwKKIct1ZNmpEQacjrxsly8Q9fe/6',
    'staff',
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    `name` = 'Staff Test User',
    `password` = '$2y$10$gPgMAefNvqPzIzwlOU0/3OekFwKKIct1ZNmpEQacjrxsly8Q9fe/6',
    `role` = 'staff',
    `updated_at` = NOW();
```

### Testing Steps

1. **Insert the test user** into your database using the SQL above
2. **Navigate** to the Team Leader login page (usually at `/` or `/team-leader`)
3. **Login** with phone number `08123456789` and password `Staff123!`
4. **Verify** that:
   - Login is successful
   - You see a welcome message
   - All menu cards are hidden
   - Only Account, Change Password, and Logout options are available in the dropdown

## Next Steps

Future enhancements can include:
- Creating staff-specific menu items
- Adding staff dashboard with relevant features
- Implementing role-based API access control for staff endpoints

## Files Modified

1. `app/Http/Controllers/TeamLeaderController.php` - Extended login method
2. `frontend/src/components/team-leader/DashboardApp.vue` - Added role-based UI hiding
3. `database/create_staff_user.php` - Helper script to generate staff user
4. `database/test_staff_user.sql` - SQL script for manual user creation

## Important Notes

- Staff users authenticate using their **phone number**, not employee ID
- The password uses Laravel's Hash facade (bcrypt)
- Staff users will have limited access - currently no menu items are shown
- Team Leader functionality remains unchanged
- The role information is stored in the JWT-like token and localStorage
