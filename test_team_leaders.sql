-- Insert test Team Leaders
-- Password default: tl1230 (hashed)

INSERT INTO `staff` (`staff_id`, `name`, `phone_number`, `role`, `password`, `position`, `group`, `department`, `hire_date`, `status`, `created_at`, `updated_at`) VALUES
('TL001', 'JOHN DOE', '081234567890', 'tl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'DC TL', 'A', 'Department A', '2024-01-01', 'active', NOW(), NOW()),
('TL002', 'JANE SMITH', '081234567891', 'tl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'DC TL', 'B', 'Department B', '2024-01-01', 'active', NOW(), NOW()),
('TL003', 'BOB JOHNSON', '081234567892', 'tl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'DC TL', 'C', 'Department C', '2024-01-01', 'active', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    phone_number = VALUES(phone_number),
    role = 'tl',
    updated_at = NOW();

-- Insert test Staff members under TL001
INSERT INTO `staff` (`staff_id`, `name`, `phone_number`, `role`, `password`, `position`, `team_leader_id`, `group`, `department`, `hire_date`, `status`, `created_at`, `updated_at`) VALUES
('STAFF001', 'Alice Wonder', '081234567893', 'staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'DC', 'TL001', 'A', 'Department A', '2024-02-01', 'active', NOW(), NOW()),
('STAFF002', 'Charlie Brown', '081234567894', 'staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'DC', 'TL001', 'A', 'Department A', '2024-02-01', 'active', NOW(), NOW()),
('STAFF003', 'David Lee', '081234567895', 'staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'DC', 'TL001', 'A', 'Department A', '2024-02-01', 'active', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    team_leader_id = VALUES(team_leader_id),
    updated_at = NOW();

-- Show results
SELECT 'Team Leaders:' as Info;
SELECT staff_id, name, role FROM staff WHERE role = 'tl';

SELECT '' as '';
SELECT 'Staff under TL001:' as Info;
SELECT staff_id, name, team_leader_id FROM staff WHERE team_leader_id = 'TL001' AND status = 'active';
