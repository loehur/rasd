-- Insert multiple test staff users with default password 'staff1230'
-- Password hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi (Laravel default 'secret')
-- For proper 'staff1230' hash, run the PHP script below first

-- Test Staff User 1
INSERT INTO `users` (`name`, `phone_number`, `password`, `role`, `created_at`, `updated_at`) 
VALUES (
    'Staff Test 1',
    '08123456781',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'staff',
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `role` = VALUES(`role`),
    `updated_at` = NOW();

-- Test Staff User 2
INSERT INTO `users` (`name`, `phone_number`, `password`, `role`, `created_at`, `updated_at`) 
VALUES (
    'Staff Test 2',
    '08123456782',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'staff',
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `role` = VALUES(`role`),
    `updated_at` = NOW();

-- Test Staff User 3
INSERT INTO `users` (`name`, `phone_number`, `password`, `role`, `created_at`, `updated_at`) 
VALUES (
    'Staff Test 3',
    '08123456783',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'staff',
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `role` = VALUES(`role`),
    `updated_at` = NOW();

-- After inserting, run this to update their passwords to 'staff1230':
-- php artisan migrate (will run the migration we just created)
