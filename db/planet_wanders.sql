-- Database initialization
CREATE DATABASE IF NOT EXISTS `planet_wanders_db`
  DEFAULT CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `planet_wanders_db`;

-- ============================================================================
-- 1. SYSTEM SETTINGS & CURRENCY MANAGEMENT
-- ============================================================================

CREATE TABLE `exchange_rates` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `currency_code` CHAR(3) NOT NULL UNIQUE COMMENT 'e.g., USD, KES, EUR, GBP',
    `currency_name` VARCHAR(50) NOT NULL,
    `symbol` VARCHAR(10) NOT NULL COMMENT 'e.g., $, KSh, €',
    `rate_to_usd` DECIMAL(12, 4) NOT NULL DEFAULT 1.0000 COMMENT 'Value of 1 USD in this currency (e.g., 130.0000 for KES)',
    `is_base_currency` TINYINT(1) NOT NULL DEFAULT 0,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================================
-- 2. DESTINATIONS & REGIONS
-- ============================================================================

CREATE TABLE `destinations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL COMMENT 'e.g. Masai Mara Game Reserve, Amboseli National Park',
    `slug` VARCHAR(160) NOT NULL UNIQUE,
    `country` VARCHAR(60) NOT NULL DEFAULT 'Kenya',
    `region` VARCHAR(100) NULL COMMENT 'e.g. Rift Valley, Coast, Northern Tanzania',
    `daily_conservation_fee_usd` DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Used by Safari Calculator engine',
    `is_popular` TINYINT(1) NOT NULL DEFAULT 1,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================================
-- 3. FLEET & SAFARI VEHICLES
-- ============================================================================

CREATE TABLE `vehicles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(120) NOT NULL COMMENT 'e.g., Custom 4x4 Safari Land Cruiser, Safari Tour Minivan',
    `slug` VARCHAR(140) NOT NULL UNIQUE,
    `vehicle_code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'e.g., cruiser, van',
    `daily_rate_usd` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `passenger_capacity` TINYINT UNSIGNED NOT NULL DEFAULT 6,
    `features` JSON NULL COMMENT '["Pop-up Roof", "Long-Range Radio", "Cooler Box", "In-Car USB"]',
    `image_url` VARCHAR(255) NULL,
    `status` ENUM('available', 'maintenance', 'retired') NOT NULL DEFAULT 'available',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================================
-- 4. ACCOMMODATION TIERS & LODGES
-- ============================================================================

CREATE TABLE `accommodation_tiers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `tier_key` VARCHAR(50) NOT NULL UNIQUE COMMENT 'e.g., budget, midrange, luxury',
    `tier_name` VARCHAR(100) NOT NULL,
    `base_rate_usd_per_person` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `description` VARCHAR(255) NULL
) ENGINE=InnoDB;

-- ============================================================================
-- 5. SAFARI PACKAGES & CATEGORIES
-- ============================================================================

CREATE TABLE `package_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(80) NOT NULL COMMENT 'e.g., Maasai Mara, Amboseli & Rift, Serengeti (TZ), Beach & Coastal',
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `filter_tag` VARCHAR(50) NOT NULL UNIQUE COMMENT 'matches UI filter: mara, amboseli, tanzania, beach, honeymoon',
    `sort_order` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE `packages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(220) NOT NULL UNIQUE,
    `package_code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Matches JS key: mara3, amboseli4, etc.',
    `days` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `nights` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `base_price_usd` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `price_unit` VARCHAR(50) NOT NULL DEFAULT 'person' COMMENT 'person, couple, student',
    `ribbon_badge` VARCHAR(60) NULL COMMENT 'e.g., Most Popular, Mt. Kilimanjaro Views, Bush & Beach Combo',
    `featured_image` VARCHAR(255) NOT NULL,
    `overview` TEXT NOT NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('published', 'draft', 'archived') NOT NULL DEFAULT 'published',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `package_categories`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Daily Day-by-Day Itineraries
CREATE TABLE `package_itineraries` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `package_id` INT UNSIGNED NOT NULL,
    `day_number` TINYINT UNSIGNED NOT NULL,
    `day_label` VARCHAR(50) NOT NULL COMMENT 'e.g., Day 1, Day 2, Day 5-6',
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NOT NULL,
    FOREIGN KEY (`package_id`) REFERENCES `packages`(`id`) ON DELETE CASCADE,
    INDEX `idx_package_day` (`package_id`, `day_number`)
) ENGINE=InnoDB;

-- Inclusions and Exclusions
CREATE TABLE `package_inclusions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `package_id` INT UNSIGNED NOT NULL,
    `inclusion_type` ENUM('inclusion', 'exclusion') NOT NULL DEFAULT 'inclusion',
    `item_text` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`package_id`) REFERENCES `packages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Optional Add-ons (Calculators & Customizations)
CREATE TABLE `safari_addons` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `addon_key` VARCHAR(50) NOT NULL UNIQUE COMMENT 'e.g., balloon, maasai_village',
    `name` VARCHAR(150) NOT NULL,
    `price_usd` DECIMAL(10, 2) NOT NULL,
    `charge_type` ENUM('per_person', 'per_vehicle', 'per_group') NOT NULL DEFAULT 'per_person',
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB;

-- ============================================================================
-- 6. LEADS, CALCULATOR QUOTES & BOOKING INQUIRIES
-- ============================================================================

CREATE TABLE `travelers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(120) NOT NULL,
    `phone_whatsapp` VARCHAR(40) NOT NULL,
    `country` VARCHAR(80) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_traveler_email` (`email`),
    INDEX `idx_traveler_phone` (`phone_whatsapp`)
) ENGINE=InnoDB;

CREATE TABLE `inquiries` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `inquiry_reference` VARCHAR(30) NOT NULL UNIQUE COMMENT 'e.g., PW-2026-0042',
    `traveler_id` INT UNSIGNED NULL,
    `source` ENUM('quick_search_bar', 'package_book_now', 'itinerary_modal', 'cost_calculator', 'contact_form') NOT NULL,
    
    -- Specific package booking link (if applicable)
    `package_id` INT UNSIGNED NULL,
    
    -- Parameters captured
    `destination_name` VARCHAR(150) NULL,
    `target_travel_date` DATE NULL,
    `guests_description` VARCHAR(100) NULL COMMENT 'e.g., 2 Adults (Couple / Honeymoon)',
    `adults_count` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `children_count` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `duration_days` TINYINT UNSIGNED NULL,
    `preferred_vehicle` VARCHAR(100) NULL,
    `accommodation_tier` VARCHAR(60) NULL,
    
    -- Financial Quote Snapshot
    `quoted_amount_usd` DECIMAL(10, 2) NULL,
    `quoted_amount_kes` DECIMAL(12, 2) NULL,
    `currency_used` CHAR(3) NOT NULL DEFAULT 'KES',
    
    -- Selected Add-ons Snapshot (JSON format)
    `selected_addons` JSON NULL COMMENT '["addon_balloon", "addon_maasai"]',
    
    `special_requests` TEXT NULL,
    `status` ENUM('new', 'assigned', 'contacted', 'quoted', 'booked', 'closed') NOT NULL DEFAULT 'new',
    `assigned_office` ENUM('Narok', 'Nairobi') NOT NULL DEFAULT 'Narok',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`traveler_id`) REFERENCES `travelers`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`package_id`) REFERENCES `packages`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================================
-- 7. REVIEWS & TESTIMONIALS
-- ============================================================================

CREATE TABLE `testimonials` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `guest_name` VARCHAR(100) NOT NULL,
    `guest_origin` VARCHAR(100) NOT NULL COMMENT 'e.g., London, United Kingdom',
    `avatar_url` VARCHAR(255) NULL,
    `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
    `review_text` TEXT NOT NULL,
    `package_tag` VARCHAR(100) NULL COMMENT 'e.g., Bush to Beach 7-Day',
    `is_featured` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================================
-- 8. FAQS (FREQUENTLY ASKED QUESTIONS)
-- ============================================================================

CREATE TABLE `faqs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `question` VARCHAR(255) NOT NULL,
    `answer` TEXT NOT NULL,
    `sort_order` SMALLINT NOT NULL DEFAULT 0,
    `status` ENUM('active', 'hidden') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(60) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `role` ENUM('superadmin', 'manager') NOT NULL DEFAULT 'superadmin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- Default password is: admin123
INSERT INTO `admins` (`username`, `password_hash`, `full_name`, `role`)
VALUES ('admin', '$2y$10$wN9aC0PzJm5M7pX.0iW2eO1P/Ym0f4i901V4uQzH8H6E9X6R8S.rC', 'Planet Wanders Admin', 'superadmin')
ON DUPLICATE KEY UPDATE `username` = `username`;