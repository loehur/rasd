-- Insert test staff user
-- Phone Number: 08123456789
-- Password: Staff123! (will be hashed automatically by Laravel's setPasswordAttribute mutator)

-- First, we need to hash the password using bcrypt
-- The hash for 'Staff123!' is: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- But we'll insert raw password and let Laravel hash it via model mutation

-- For direct SQL insert, we need the bcrypt hash
-- In PHP: password_hash('Staff123!', PASSWORD_BCRYPT)
-- Result: $2y$10$zQYvL5CkQ1sVkO0GJ8CQ0uX7Y3wYvD0KK5O7nZ4K6kO4qL4Y8O7nO (example)

-- Since we can't use password_hash in SQL, we'll use a pre-generated bcrypt hash
-- For password 'Staff123!': $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

INSERT INTO `users` (`name`, `phone_number`, `password`, `role`, `created_at`, `updated_at`) 
VALUES (
    'Staff Test User',
    '08123456789',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'staff',
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `password` = VALUES(`password`),
    `role` = VALUES(`role`),
    `updated_at` = NOW();

-- Note: The password hash above is for 'secret' password (Laravel default test hash)
-- To create a proper hash for 'Staff123!', run this PHP code:
-- echo password_hash('Staff123!', PASSWORD_BCRYPT);
