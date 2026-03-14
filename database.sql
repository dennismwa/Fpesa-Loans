SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+03:00";

CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('super_admin','admin','manager') DEFAULT 'admin',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(20) NOT NULL,
  `national_id` VARCHAR(30) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `employment_status` VARCHAR(50) DEFAULT NULL,
  `monthly_income` DECIMAL(12,2) DEFAULT 0,
  `address` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `email_verified` TINYINT(1) DEFAULT 0,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_phone` (`phone`),
  INDEX `idx_national_id` (`national_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `loan_types` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `icon` VARCHAR(50) DEFAULT 'briefcase',
  `min_amount` DECIMAL(12,2) DEFAULT 1000,
  `max_amount` DECIMAL(12,2) DEFAULT 1000000,
  `interest_rate` DECIMAL(5,2) DEFAULT 12.00,
  `min_term` INT DEFAULT 1,
  `max_term` INT DEFAULT 36,
  `required_documents` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `loan_applications` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `reference` VARCHAR(30) NOT NULL UNIQUE,
  `user_id` INT UNSIGNED NOT NULL,
  `loan_type_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `term_months` INT NOT NULL,
  `interest_rate` DECIMAL(5,2) NOT NULL,
  `monthly_payment` DECIMAL(12,2) NOT NULL,
  `total_repayment` DECIMAL(12,2) NOT NULL,
  `total_interest` DECIMAL(12,2) NOT NULL,
  `purpose` TEXT DEFAULT NULL,
  `employment_status` VARCHAR(50) DEFAULT NULL,
  `monthly_income` DECIMAL(12,2) DEFAULT 0,
  `status` ENUM('pending','fee_paid','under_review','approved','rejected','cancelled') DEFAULT 'pending',
  `fee_paid` TINYINT(1) DEFAULT 0,
  `fee_amount` DECIMAL(10,2) DEFAULT 200,
  `fee_payment_ref` VARCHAR(100) DEFAULT NULL,
  `admin_comment` TEXT DEFAULT NULL,
  `reviewed_by` INT UNSIGNED DEFAULT NULL,
  `reviewed_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_user` (`user_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`loan_type_id`) REFERENCES `loan_types`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `loans` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `loan_number` VARCHAR(30) NOT NULL UNIQUE,
  `application_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `loan_type_id` INT UNSIGNED NOT NULL,
  `principal` DECIMAL(12,2) NOT NULL,
  `interest_rate` DECIMAL(5,2) NOT NULL,
  `term_months` INT NOT NULL,
  `monthly_payment` DECIMAL(12,2) NOT NULL,
  `total_repayment` DECIMAL(12,2) NOT NULL,
  `total_interest` DECIMAL(12,2) NOT NULL,
  `amount_paid` DECIMAL(12,2) DEFAULT 0,
  `balance` DECIMAL(12,2) NOT NULL,
  `status` ENUM('active','completed','defaulted','closed') DEFAULT 'active',
  `disbursed_at` DATETIME DEFAULT NULL,
  `next_due_date` DATE DEFAULT NULL,
  `completed_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_user` (`user_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`application_id`) REFERENCES `loan_applications`(`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`loan_type_id`) REFERENCES `loan_types`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `loan_installments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `loan_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `installment_no` INT NOT NULL,
  `due_date` DATE NOT NULL,
  `principal` DECIMAL(12,2) NOT NULL,
  `interest` DECIMAL(12,2) NOT NULL,
  `emi` DECIMAL(12,2) NOT NULL,
  `balance` DECIMAL(12,2) DEFAULT 0,
  `amount_paid` DECIMAL(12,2) DEFAULT 0,
  `status` ENUM('pending','paid','partial','overdue') DEFAULT 'pending',
  `paid_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_loan` (`loan_id`),
  INDEX `idx_due` (`due_date`),
  FOREIGN KEY (`loan_id`) REFERENCES `loans`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `reference` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT UNSIGNED NOT NULL,
  `loan_id` INT UNSIGNED DEFAULT NULL,
  `installment_id` INT UNSIGNED DEFAULT NULL,
  `application_id` INT UNSIGNED DEFAULT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'mpesa',
  `payment_type` ENUM('application_fee','repayment','deposit','withdrawal') DEFAULT 'repayment',
  `transaction_ref` VARCHAR(100) DEFAULT NULL,
  `screenshot` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('pending','confirmed','rejected') DEFAULT 'pending',
  `confirmed_by` INT UNSIGNED DEFAULT NULL,
  `confirmed_at` DATETIME DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user` (`user_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `wallets` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL UNIQUE,
  `balance` DECIMAL(12,2) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `wallet_transactions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `type` ENUM('credit','debit') NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `type` ENUM('mpesa_api','paybill','till','bank','cash','manual') DEFAULT 'manual',
  `account_number` VARCHAR(100) DEFAULT NULL,
  `account_name` VARCHAR(100) DEFAULT NULL,
  `instructions` TEXT DEFAULT NULL,
  `api_key` VARCHAR(255) DEFAULT NULL,
  `api_secret` VARCHAR(255) DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `documents` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `loan_id` INT UNSIGNED DEFAULT NULL,
  `application_id` INT UNSIGNED DEFAULT NULL,
  `type` ENUM('agreement','signed_agreement','id_copy','payslip','other') DEFAULT 'other',
  `title` VARCHAR(200) DEFAULT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `file_size` INT DEFAULT 0,
  `uploaded_by` ENUM('user','admin','system') DEFAULT 'system',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT DEFAULT NULL,
  `setting_group` VARCHAR(50) DEFAULT 'general',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `type` ENUM('info','success','warning','error') DEFAULT 'info',
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_read` (`user_id`, `is_read`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED DEFAULT 0,
  `action` VARCHAR(100) NOT NULL,
  `details` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_action` (`action`),
  INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ═══════════════════════════════════════
-- DEFAULT DATA
-- ═══════════════════════════════════════

-- password = 'password' hashed with password_hash
INSERT INTO `admins` (`name`, `email`, `password`, `role`) VALUES
('Super Admin', 'admin@fpesa.co.ke', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');

INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
('site_name', 'Fpesa', 'general'),
('site_tagline', 'Your Trusted Loan Partner', 'general'),
('site_description', 'Fast, affordable loans for everyone. Apply online and get funded within 24 hours.', 'general'),
('primary_color', '#0D6B3F', 'appearance'),
('secondary_color', '#F59E0B', 'appearance'),
('logo', '', 'appearance'),
('contact_email', 'info@fpesa.co.ke', 'contact'),
('contact_phone', '+254 700 000 000', 'contact'),
('contact_address', 'Nairobi, Kenya', 'contact'),
('application_fee', '200', 'loans'),
('currency', 'KSH', 'general'),
('facebook_url', '#', 'social'),
('twitter_url', '#', 'social'),
('instagram_url', '#', 'social'),
('linkedin_url', '#', 'social'),
('meta_title', 'Fpesa - Fast & Affordable Loans in Kenya', 'seo'),
('meta_description', 'Apply for personal, business, car, school and emergency loans online. Fast approval, low interest rates.', 'seo'),
('meta_keywords', 'loans kenya, personal loan, business loan, fpesa, quick loans', 'seo');

INSERT INTO `loan_types` (`name`, `slug`, `description`, `icon`, `min_amount`, `max_amount`, `interest_rate`, `min_term`, `max_term`, `sort_order`) VALUES
('Personal Loan', 'personal-loan', 'Quick personal loans for your immediate needs. Flexible terms and competitive rates to help you handle personal expenses, medical bills, or any urgent financial need.', 'user', 5000, 500000, 14.00, 1, 24, 1),
('School Loan', 'school-loan', 'Invest in education with our affordable school loans. Cover tuition fees, books, accommodation and other education expenses with flexible repayment plans.', 'book-open', 10000, 1000000, 12.00, 3, 36, 2),
('Car Loan', 'car-loan', 'Drive your dream car with our auto financing options. Finance new or used vehicles with competitive rates and terms up to 60 months.', 'car', 100000, 5000000, 13.00, 6, 60, 3),
('Real Estate Loan', 'real-estate-loan', 'Finance your property dreams with competitive rates. Purchase land, build your home, or invest in commercial property with long-term financing.', 'home', 500000, 20000000, 11.00, 12, 120, 4),
('Business Loan', 'business-loan', 'Grow your business with our flexible financing solutions. Working capital, equipment purchase, inventory, or business expansion — we have you covered.', 'briefcase', 50000, 5000000, 15.00, 3, 36, 5),
('Emergency Loan', 'emergency-loan', 'Instant emergency loans when you need them most. Quick disbursement for medical emergencies, urgent repairs, or unexpected expenses.', 'zap', 1000, 100000, 16.00, 1, 12, 6),
('Salary Advance', 'salary-advance', 'Get an advance on your next salary. Easy qualification for employed individuals with quick processing and minimal documentation.', 'wallet', 1000, 200000, 10.00, 1, 3, 7);

INSERT INTO `payment_methods` (`name`, `type`, `account_number`, `account_name`, `instructions`, `is_active`, `sort_order`) VALUES
('M-Pesa Paybill', 'paybill', '247247', 'Fpesa Limited', 'Go to M-Pesa → Lipa na M-Pesa → Paybill → Business Number: 247247 → Account: Your Loan Reference Number', 1, 1),
('M-Pesa Till', 'till', '5678901', 'Fpesa Loans', 'Go to M-Pesa → Lipa na M-Pesa → Buy Goods → Till Number: 5678901', 1, 2),
('Bank Transfer', 'bank', '0123456789', 'Fpesa Limited - KCB Bank', 'Transfer to KCB Bank\nAccount: 0123456789\nName: Fpesa Limited\nBranch: Nairobi', 1, 3);
