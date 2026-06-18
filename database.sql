-- Jeevansathi Gym Website Database Schema
-- MySQL 5.7+ / MariaDB 10.3+

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+05:30";

CREATE DATABASE IF NOT EXISTS `gym_website` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gym_website`;

-- =====================================================
-- Table: admin_users
-- =====================================================
CREATE TABLE `admin_users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin user (password: admin123)
INSERT INTO `admin_users` (`username`, `password_hash`) VALUES
('admin', '$2y$12$bGUG5ZCtdUruGe1CDc2KMeXFvUJoprfuJTN/mi39fFrbhNuWuff9W');

-- =====================================================
-- Table: site_settings
-- =====================================================
CREATE TABLE `site_settings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default site settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('gym_name', 'Iron Pulse Fitness'),
('logo', ''),
('favicon', 'assets/images/favicon.png'),
('tagline', 'Transform Your Body, Transform Your Life'),
('hero_heading', 'Build Your Dream Body With Expert Training'),
('hero_subheading', 'Join the most premium fitness community in your city. State-of-the-art equipment, certified trainers, and a results-driven approach to help you achieve your fitness goals.'),
('hero_image', 'assets/images/hero-bg.jpg'),
('hero_image_fit', 'cover'),
('about_content', 'Iron Pulse Fitness is more than just a gym - it is a community dedicated to helping you achieve your fitness goals. Founded in 2015, we have helped over 5000 members transform their bodies and lives. Our 10,000 sq ft facility features cutting-edge equipment from Life Fitness and Hammer Strength, dedicated zones for cardio, strength training, functional fitness, and group classes. Whether you are a beginner taking your first steps toward fitness or an experienced athlete looking to push your limits, our team of certified trainers is here to guide you every step of the way.'),
('primary_color', '#22C55E'),
('secondary_color', '#111827'),
('button_color', '#22C55E'),
('whatsapp_number', '919876543210'),
('phone_number', '+91 98765 43210'),
('email', 'info@ironpulsefitness.com'),
('address', '123 Fitness Street, Sector 15, Gurugram, Haryana 122001'),
('google_map_embed', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3507.2!2d77.0!3d28.4!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjjCsDI0JzAwLjAiTiA3N8KwMDAnMDAuMCJF!5e0!3m2!1sen!2sin!4v1" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'),
('business_hours', 'Monday - Saturday: 5:00 AM - 11:00 PM | Sunday: 6:00 AM - 10:00 PM'),
('instagram_link', 'https://www.instagram.com/ironpulsefitness'),
('facebook_link', 'https://www.facebook.com/ironpulsefitness'),
('youtube_link', 'https://www.youtube.com/ironpulsefitness'),
('footer_text', 'Iron Pulse Fitness - Your transformation starts here. We believe that fitness is not just about building muscles, it is about building confidence, discipline, and a healthier lifestyle.'),
('google_analytics', ''),
('search_console', ''),
('facebook_pixel', ''),
('default_meta_title', 'Iron Pulse Fitness - Premium Gym & Fitness Center'),
('default_meta_description', 'Join Iron Pulse Fitness, the premium gym and fitness center offering expert personal training, group classes, and state-of-the-art equipment. Transform your body today.'),
('default_meta_keywords', 'gym, fitness center, personal training, weight loss, muscle building, group classes, yoga, CrossFit, Zumba, kickboxing'),
('services', '[{"title":"Personal Training","description":"One-on-one sessions with certified trainers tailored to your specific goals, body type, and fitness level. Get personalized workout plans and nutrition guidance.","icon":"fa-dumbbell"},{"title":"Group Classes","description":"High-energy group fitness classes including Zumba, Yoga, CrossFit, HIIT, Kickboxing, and Spinning. Motivate each other and have fun while getting fit.","icon":"fa-users"},{"title":"Nutrition Counseling","description":"Expert nutritionists create customized meal plans based on your body composition, metabolism, and fitness objectives. Track your macros and see real results.","icon":"fa-apple-alt"},{"title":"Cardio Zone","description":"State-of-the-art cardio equipment including treadmills, ellipticals, rowing machines, and stationary bikes with personal entertainment screens.","icon":"fa-heartbeat"},{"title":"Strength Training","description":"Fully equipped strength training area with free weights, cable machines, Smith machines, and dedicated powerlifting platforms for all levels.","icon":"fa-fire"},{"title":"Spa & Recovery","description":"Post-workout recovery with steam room, sauna, foam rolling area, and sports massage services to help your muscles recover faster.","icon":"fa-spa"}]'),
('faqs', '[{"question":"What are the gym timings?","answer":"We are open Monday to Saturday from 5:00 AM to 11:00 PM and Sunday from 6:00 AM to 10:00 PM. Our peak hours are 6-9 AM and 5-9 PM."},{"question":"Do you offer a free trial?","answer":"Yes! We offer a complimentary 1-day trial pass so you can experience our facilities, equipment, and training atmosphere before committing to a membership."},{"question":"Is there parking available?","answer":"Yes, we have ample free parking space for both two-wheelers and four-wheelers right in front of our facility."},{"question":"Do you have separate sections for women?","answer":"Yes, we have a dedicated women-only workout area with all essential equipment for members who prefer a private training environment."},{"question":"Can I freeze my membership?","answer":"Yes, you can freeze your membership for up to 30 days per year in case of medical emergencies, travel, or other valid reasons. Contact our front desk for the process."},{"question":"What should I bring on my first day?","answer":"Bring comfortable workout clothes, a pair of clean gym shoes, a water bottle, and a small towel. We provide lockers to store your belongings safely."},{"question":"Do you provide diet plans?","answer":"Yes, all Standard and Premium members receive a customized diet plan from our certified nutritionist, tailored to their fitness goals and dietary preferences."},{"question":"Is there an age limit to join?","answer":"Members must be at least 16 years old to join. Those between 16-18 require parental consent and are limited to certain equipment for safety."}]'),
('trust_badges', '[{"number":"5000+","label":"Happy Members"},{"number":"15+","label":"Expert Trainers"},{"number":"8+","label":"Years Experience"},{"number":"50+","label":"Daily Classes"}]');

-- =====================================================
-- Table: seo_settings
-- =====================================================
CREATE TABLE `seo_settings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_key` VARCHAR(50) NOT NULL,
  `page_name` VARCHAR(100) NOT NULL,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `meta_keywords` VARCHAR(500) DEFAULT NULL,
  `slug` VARCHAR(100) DEFAULT NULL,
  `canonical_url` VARCHAR(255) DEFAULT NULL,
  `robots_meta` VARCHAR(100) DEFAULT 'index, follow',
  `og_title` VARCHAR(255) DEFAULT NULL,
  `og_description` TEXT DEFAULT NULL,
  `og_image` VARCHAR(255) DEFAULT NULL,
  `twitter_title` VARCHAR(255) DEFAULT NULL,
  `twitter_description` TEXT DEFAULT NULL,
  `twitter_image` VARCHAR(255) DEFAULT NULL,
  `schema_json` TEXT DEFAULT NULL,
  `focus_keyword` VARCHAR(100) DEFAULT NULL,
  `custom_header_scripts` TEXT DEFAULT NULL,
  `custom_footer_scripts` TEXT DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_key` (`page_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default SEO settings for all pages
INSERT INTO `seo_settings` (`page_key`, `page_name`, `meta_title`, `meta_description`, `meta_keywords`, `slug`, `canonical_url`, `robots_meta`, `og_title`, `og_description`, `schema_json`, `focus_keyword`) VALUES
('home', 'Home', 'Iron Pulse Fitness - Premium Gym & Fitness Center in Gurugram', 'Join Iron Pulse Fitness for expert personal training, group classes, and world-class equipment. Start your fitness transformation today with a free trial.', 'gym near me, fitness center Gurugram, personal training, weight loss gym, best gym, premium fitness', '/', NULL, 'index, follow', 'Iron Pulse Fitness - Transform Your Body Today', 'Join the most premium fitness community. State-of-the-art equipment and certified trainers.', '{"@context":"https://schema.org","@type":"Gym","name":"Iron Pulse Fitness","description":"Premium gym and fitness center","address":{"@type":"PostalAddress","streetAddress":"123 Fitness Street, Sector 15","addressLocality":"Gurugram","addressRegion":"Haryana","postalCode":"122001","addressCountry":"IN"},"telephone":"+919876543210","openingHours":"Mo-Sa 05:00-23:00, Su 06:00-22:00"}', 'premium gym Gurugram'),
('about', 'About Us', 'About Iron Pulse Fitness - Our Story & Mission', 'Learn about Iron Pulse Fitness journey, our mission to transform lives through fitness, our world-class facilities, and our team of certified expert trainers.', 'about iron pulse fitness, gym story, fitness mission, gym facilities', 'about', NULL, 'index, follow', 'About Iron Pulse Fitness - Our Fitness Journey', 'Discover our story and mission to transform lives through expert fitness training.', '{"@context":"https://schema.org","@type":"AboutPage","name":"About Iron Pulse Fitness","description":"Our story and mission"}', 'about gym'),
('services', 'Services', 'Our Services - Personal Training, Group Classes & More', 'Explore our comprehensive fitness services including personal training, group classes, nutrition counseling, cardio zone, strength training, and spa recovery.', 'gym services, personal training, group classes, nutrition counseling, strength training, cardio', 'services', NULL, 'index, follow', 'Fitness Services at Iron Pulse - Complete Training Solutions', 'From personal training to group classes, discover all the fitness services we offer.', '{"@context":"https://schema.org","@type":"Service","provider":{"@type":"Gym","name":"Iron Pulse Fitness"},"serviceType":"Fitness Training"}', 'fitness services'),
('plans', 'Membership Plans', 'Membership Plans & Pricing - Affordable Fitness Packages', 'Choose from our flexible membership plans - Basic, Standard, and Premium. Affordable pricing with monthly, quarterly, and yearly options available.', 'gym membership, fitness plans, gym pricing, monthly membership, affordable gym', 'plans', NULL, 'index, follow', 'Gym Membership Plans - Find Your Perfect Fit', 'Flexible membership options starting from Rs 999. Choose the plan that suits your fitness goals.', '{"@context":"https://schema.org","@type":"Product","name":"Gym Membership","offers":{"@type":"AggregateOffer","lowPrice":"999","highPrice":"2499","priceCurrency":"INR"}}', 'gym membership plans'),
('trainers', 'Our Trainers', 'Expert Certified Personal Trainers - Meet Our Team', 'Meet our team of certified fitness professionals with years of experience in bodybuilding, CrossFit, yoga, nutrition, and sports training.', 'personal trainers, certified fitness trainers, gym trainers, expert coaches', 'trainers', NULL, 'index, follow', 'Meet Our Expert Trainers at Iron Pulse Fitness', 'Certified trainers with years of experience ready to guide your fitness journey.', '{"@context":"https://schema.org","@type":"SportsTeam","name":"Iron Pulse Trainers","description":"Our team of certified fitness professionals"}', 'personal trainers'),
('classes', 'Classes Schedule', 'Group Fitness Classes - Yoga, CrossFit, Zumba & More', 'Join our energizing group fitness classes including Yoga, CrossFit, Zumba, HIIT, Kickboxing, and Spinning. Check schedule and book your spot today.', 'group fitness classes, yoga class, crossfit, zumba, HIIT, kickboxing, spinning', 'classes', NULL, 'index, follow', 'Group Fitness Classes at Iron Pulse', 'High-energy group classes to keep you motivated. From yoga to CrossFit, find your fit.', '{"@context":"https://schema.org","@type":"Event","name":"Group Fitness Classes","location":{"@type":"Gym","name":"Iron Pulse Fitness"}}', 'group fitness classes'),
('gallery', 'Gallery', 'Photo Gallery - Our Gym Facilities & Equipment', 'Browse photos of our state-of-the-art gym facilities, training areas, equipment, group classes, and member transformations at Iron Pulse Fitness.', 'gym photos, fitness gallery, gym facilities, equipment photos, gym interior', 'gallery', NULL, 'index, follow', 'Iron Pulse Fitness Gallery - See Our Facilities', 'Take a virtual tour of our premium facilities through our photo gallery.', NULL, 'gym gallery'),
('blog', 'Blog', 'Fitness Blog - Tips, Workouts & Nutrition Advice', 'Read expert fitness articles, workout tips, nutrition advice, and transformation stories on the Iron Pulse Fitness blog.', 'fitness blog, workout tips, nutrition advice, exercise guides, health articles', 'blog', NULL, 'index, follow', 'Iron Pulse Fitness Blog - Expert Fitness Insights', 'Stay updated with expert fitness tips, workout routines, and nutrition guidance.', NULL, 'fitness blog'),
('contact', 'Contact Us', 'Contact Iron Pulse Fitness - Get in Touch Today', 'Get in touch with Iron Pulse Fitness. Visit us, call us, or send us a message. We are here to answer all your fitness queries and help you get started.', 'contact gym, gym address, gym phone number, gym location, fitness enquiry', 'contact', NULL, 'index, follow', 'Contact Iron Pulse Fitness - We Are Here For You', 'Have questions? Contact us via phone, email, or visit our gym. We would love to hear from you.', '{"@context":"https://schema.org","@type":"ContactPage","name":"Contact Iron Pulse Fitness"}', 'contact gym'),
('transformations', 'Transformations', 'Member Transformations - Real Results, Real People', 'See inspiring before and after transformations from our members. Real results achieved through dedicated training and expert guidance at Iron Pulse Fitness.', 'body transformation, before after results, fitness transformation, weight loss results', 'transformations', NULL, 'index, follow', 'Amazing Body Transformations at Iron Pulse Fitness', 'Real members, real results. See the incredible transformations achieved at our gym.', NULL, 'body transformation');

-- =====================================================
-- Table: membership_plans
-- =====================================================
CREATE TABLE `membership_plans` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_name` VARCHAR(100) NOT NULL,
  `monthly_price` DECIMAL(10,2) NOT NULL,
  `quarterly_price` DECIMAL(10,2) NOT NULL,
  `yearly_price` DECIMAL(10,2) NOT NULL,
  `features` TEXT NOT NULL,
  `is_highlighted` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default membership plans
INSERT INTO `membership_plans` (`plan_name`, `monthly_price`, `quarterly_price`, `yearly_price`, `features`, `is_highlighted`) VALUES
('Basic', 999.00, 2699.00, 9999.00, '["Access to gym floor & equipment","Locker facility","Cardio zone access","Free fitness assessment","2 group classes per week","Access during all operating hours","Drinking water & towel service"]', 0),
('Standard', 1499.00, 3999.00, 14999.00, '["Everything in Basic plan","Unlimited group classes","Personal trainer (2 sessions/week)","Customized diet plan","Body composition analysis monthly","Steam & sauna access","Protein shake post-workout","Priority booking for classes"]', 1),
('Premium', 2499.00, 6999.00, 24999.00, '["Everything in Standard plan","Daily personal training session","Weekly nutritionist consultation","Spa & massage (2 sessions/month)","Premium locker with laundry","Guest passes (2 per month)","Priority equipment access","24/7 trainer support via WhatsApp","Free gym merchandise quarterly"]', 0);

-- =====================================================
-- Table: trainers
-- =====================================================
CREATE TABLE `trainers` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `specialty` VARCHAR(200) NOT NULL,
  `experience` VARCHAR(50) NOT NULL,
  `bio` TEXT DEFAULT NULL,
  `instagram_link` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: classes
-- =====================================================
CREATE TABLE `classes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `class_name` VARCHAR(100) NOT NULL,
  `trainer_name` VARCHAR(100) NOT NULL,
  `class_time` VARCHAR(50) NOT NULL,
  `class_days` VARCHAR(100) NOT NULL,
  `duration` VARCHAR(50) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default classes
INSERT INTO `classes` (`class_name`, `trainer_name`, `class_time`, `class_days`, `duration`, `description`) VALUES
('Power Yoga', 'Priya Sharma', '6:00 AM - 7:00 AM', 'Monday, Wednesday, Friday', '60 minutes', 'A dynamic and athletic form of yoga that builds strength, flexibility, and mental focus. Suitable for all levels, this class combines traditional yoga poses with strength-building sequences to create a complete mind-body workout.'),
('CrossFit WOD', 'Rahul Verma', '7:00 AM - 8:00 AM', 'Monday, Tuesday, Thursday, Saturday', '60 minutes', 'High-intensity functional training that combines weightlifting, gymnastics, and cardio. Each Workout of the Day is different, keeping your body challenged and preventing plateaus. Build functional strength and explosive power.'),
('Zumba Fitness', 'Neha Kapoor', '5:00 PM - 6:00 PM', 'Tuesday, Thursday, Saturday', '60 minutes', 'Dance your way to fitness with this high-energy Latin-inspired workout. Burn up to 800 calories per session while having fun with easy-to-follow choreography set to infectious music. No dance experience required.'),
('HIIT Circuit', 'Arjun Mehta', '6:00 PM - 6:45 PM', 'Monday, Wednesday, Friday, Saturday', '45 minutes', 'Maximum results in minimum time. This High-Intensity Interval Training circuit alternates between intense bursts of exercise and brief recovery periods. Torch fat, boost metabolism, and improve cardiovascular endurance.'),
('Kickboxing', 'Vikram Singh', '7:00 PM - 8:00 PM', 'Tuesday, Thursday, Saturday', '60 minutes', 'Combine martial arts techniques with fast-paced cardio in this empowering full-body workout. Learn proper punch and kick techniques while building strength, coordination, and self-defense skills in a fun, high-energy environment.'),
('Strength & Conditioning', 'Amit Patel', '8:00 AM - 9:00 AM', 'Monday, Wednesday, Friday', '60 minutes', 'Progressive strength training program designed to build lean muscle mass and functional strength. Focus on compound movements with proper form and technique. Includes periodized programming for continuous improvement.');

-- =====================================================
-- Table: gallery
-- =====================================================
CREATE TABLE `gallery` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `category` VARCHAR(50) DEFAULT 'general',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: transformations
-- =====================================================
CREATE TABLE `transformations` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_name` VARCHAR(100) NOT NULL,
  `before_image` VARCHAR(255) NOT NULL,
  `after_image` VARCHAR(255) NOT NULL,
  `duration` VARCHAR(50) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: testimonials
-- =====================================================
CREATE TABLE `testimonials` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_name` VARCHAR(100) NOT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `rating` TINYINT(1) NOT NULL DEFAULT 5,
  `review` TEXT NOT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: enquiries
-- =====================================================
CREATE TABLE `enquiries` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enquiry_type` VARCHAR(50) NOT NULL DEFAULT 'general',
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `whatsapp` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `age` INT(3) DEFAULT NULL,
  `gender` ENUM('male','female','other') DEFAULT NULL,
  `fitness_goal` VARCHAR(200) DEFAULT NULL,
  `preferred_time` VARCHAR(100) DEFAULT NULL,
  `message` TEXT DEFAULT NULL,
  `status` ENUM('new','contacted','converted','closed') NOT NULL DEFAULT 'new',
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_enquiry_type` (`enquiry_type`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: blog_posts
-- =====================================================
CREATE TABLE `blog_posts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `author` VARCHAR(100) DEFAULT 'Admin',
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `content` LONGTEXT NOT NULL,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `meta_keywords` VARCHAR(500) DEFAULT NULL,
  `canonical_url` VARCHAR(255) DEFAULT NULL,
  `schema_json` TEXT DEFAULT NULL,
  `status` ENUM('draft','published') NOT NULL DEFAULT 'draft',
  `published_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_status` (`status`),
  KEY `idx_category` (`category`),
  KEY `idx_published_at` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
