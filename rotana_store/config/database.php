<?php
// إعدادات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_NAME', 'rotana_store');
define('DB_USER', 'root');
define('DB_PASS', '');

// إنشاء اتصال قاعدة البيانات
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
    }
}

// إنشاء قاعدة البيانات والجداول إذا لم تكن موجودة
function initializeDatabase() {
    try {
        // الاتصال بـ MySQL بدون تحديد قاعدة بيانات
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        
        // إنشاء قاعدة البيانات
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE " . DB_NAME);
        
        // إنشاء جدول المستخدمين
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(100),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // إنشاء جدول الفئات
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100) UNIQUE NOT NULL,
                icon VARCHAR(100),
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // إنشاء جدول المنتجات
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                old_price DECIMAL(10,2) NULL,
                icon VARCHAR(100),
                image_url VARCHAR(500),
                is_active BOOLEAN DEFAULT TRUE,
                is_featured BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // إنشاء جدول الإعدادات
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(100) UNIQUE NOT NULL,
                setting_value TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // إنشاء جدول النشاطات
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS activities (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                action VARCHAR(100) NOT NULL,
                description TEXT,
                image_url VARCHAR(500),
                ip_address VARCHAR(45),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // إضافة الحقول الجديدة إذا لم تكن موجودة
        try {
            $pdo->exec("ALTER TABLE products ADD COLUMN image_url VARCHAR(500) AFTER icon");
        } catch (PDOException $e) {
            // الحقل موجود بالفعل
        }
        
        try {
            $pdo->exec("ALTER TABLE products ADD COLUMN old_price DECIMAL(10,2) NULL AFTER price");
        } catch (PDOException $e) {
            // الحقل موجود بالفعل
        }
        
        try {
            $pdo->exec("ALTER TABLE products ADD COLUMN is_featured BOOLEAN DEFAULT FALSE AFTER is_active");
        } catch (PDOException $e) {
            // الحقل موجود بالفعل
        }
        
        // إنشاء مجلد الصور إذا لم يكن موجوداً
        if (!file_exists('uploads/')) {
            mkdir('uploads/', 0755, true);
        }
        if (!file_exists('uploads/products/')) {
            mkdir('uploads/products/', 0755, true);
        }
        
        // إنشاء ملف .htaccess لحماية مجلد الصور
        $htaccess_content = "# حماية مجلد الصور\n";
        $htaccess_content .= "<Files \"*.php\">\n";
        $htaccess_content .= "    Order Allow,Deny\n";
        $htaccess_content .= "    Deny from all\n";
        $htaccess_content .= "</Files>\n\n";
        $htaccess_content .= "# السماح فقط بملفات الصور\n";
        $htaccess_content .= "<FilesMatch \"\\.(jpg|jpeg|png|gif|webp)$\">\n";
        $htaccess_content .= "    Order Allow,Deny\n";
        $htaccess_content .= "    Allow from all\n";
        $htaccess_content .= "</FilesMatch>\n\n";
        $htaccess_content .= "# منع عرض محتويات المجلد\n";
        $htaccess_content .= "Options -Indexes";
        
        if (!file_exists('uploads/.htaccess')) {
            file_put_contents('uploads/.htaccess', $htaccess_content);
        }
        
        // إنشاء مجلد الصور للمنتجات
        if (!file_exists('uploads/products/')) {
            mkdir('uploads/products/', 0755, true);
        }
        
        // إنشاء صور افتراضية للمنتجات
        createDefaultProductImages();
        
        // إدخال البيانات الافتراضية
        insertDefaultData($pdo);
        
        return true;
    } catch (PDOException $e) {
        die("خطأ في إنشاء قاعدة البيانات: " . $e->getMessage());
    }
}

// إدخال البيانات الافتراضية
function insertDefaultData($pdo) {
    // إدخال المستخدم الافتراضي
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('rotana123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $hashedPassword, 'admin@rotana.com']);
    }
    
    // إدخال الفئات الافتراضية
    $categories = [
        ['name' => 'اللابتوبات', 'slug' => 'laptops', 'icon' => 'fas fa-laptop', 'description' => 'أحدث أجهزة الكمبيوتر المحمولة'],
        ['name' => 'الهواتف', 'slug' => 'phones', 'icon' => 'fas fa-mobile-alt', 'description' => 'أفضل الهواتف الحديثة'],
        ['name' => 'الاكسسوارات', 'slug' => 'accessories', 'icon' => 'fas fa-headphones', 'description' => 'ملحقات الأجهزة الإلكترونية']
    ];
    
    foreach ($categories as $category) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE slug = ?");
        $stmt->execute([$category['slug']]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, icon, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$category['name'], $category['slug'], $category['icon'], $category['description']]);
        }
    }
    
    // إدخال الإعدادات الافتراضية
    $settings = [
        ['store_name', 'روتانا'],
        ['store_description', 'متجر الأجهزة الإلكترونية الأول في العراق'],
        ['whatsapp_number', '+9647501234567'],
        ['phone_number', '+9647501234567'],
        ['address', 'بغداد، العراق'],
        ['facebook_url', '#'],
        ['instagram_url', '#'],
        ['telegram_url', '#'],
        ['currency', 'د.ع']
    ];
    
    foreach ($settings as $setting) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = ?");
        $stmt->execute([$setting[0]]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
            $stmt->execute([$setting[0], $setting[1]]);
        }
    }
    
    // إدخال المنتجات الافتراضية
    insertDefaultProducts($pdo);
}

// إدخال المنتجات الافتراضية
function insertDefaultProducts($pdo) {
    $products = [
        // اللابتوبات
      
    ];
    
    foreach ($products as $product) {
        // الحصول على category_id
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
        $stmt->execute([$product['category_slug']]);
        $categoryId = $stmt->fetchColumn();
        
        if ($categoryId) {
            // التحقق من وجود المنتج
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE title = ? AND category_id = ?");
            $stmt->execute([$product['title'], $categoryId]);
            if ($stmt->fetchColumn() == 0) {
                $stmt = $pdo->prepare("INSERT INTO products (category_id, title, description, price, icon, image_url, is_active, is_featured) VALUES (?, ?, ?, ?, ?, ?, 1, 1)");
                $stmt->execute([$categoryId, $product['title'], $product['description'], $product['price'], $product['icon'], $product['image_url']]);
            }
        }
    }
}

// تسجيل النشاط
function logActivity($action, $description = '', $userId = null, $imageUrl = null) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("INSERT INTO activities (user_id, action, description, image_url, ip_address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $action, $description, $imageUrl, $_SERVER['REMOTE_ADDR'] ?? '']);
    } catch (Exception $e) {
        // تجاهل أخطاء تسجيل النشاط
    }
}

// الحصول على إعداد
function getSetting($key, $default = '') {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetchColumn();
        return $result ?: $default;
    } catch (Exception $e) {
        return $default;
    }
}

// حفظ إعداد
function saveSetting($key, $value) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// رفع الصور
function uploadImage($file, $folder = 'products') {
    $uploadDir = "uploads/{$folder}/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['error' => 'نوع الملف غير مسموح به. الأنواع المسموحة: JPG, PNG, GIF, WEBP'];
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['error' => 'حجم الملف كبير جداً. الحد الأقصى: 5MB'];
    }
    $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = $originalName . '_' . uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filepath];
    } else {
        return ['error' => 'فشل في رفع الملف. تحقق من صلاحيات مجلد uploads/products/'];
    }
}

// حذف الصورة
function deleteImage($imagePath) {
    if (file_exists($imagePath) && is_file($imagePath)) {
        return unlink($imagePath);
    }
    return false;
}

// دالة لإنشاء صور افتراضية بسيطة (بدون مكتبة GD)
function createDefaultProductImages() {
    // إنشاء مجلد الصور إذا لم يكن موجوداً
    if (!file_exists('uploads/products/')) {
        mkdir('uploads/products/', 0755, true);
    }
    
    // قائمة الصور المطلوبة
    $default_images = [
        'laptop-hp.jpg', 'laptop-dell.jpg', 'laptop-lenovo.jpg', 'laptop-asus.jpg', 'laptop-acer.jpg', 'laptop-macbook.jpg',
        'iphone-15-pro-max.jpg', 'samsung-s24-ultra.jpg', 'huawei-p60-pro.jpg', 'xiaomi-redmi-note-13-pro.jpg',
        'oppo-find-x7-ultra.jpg', 'oneplus-12.jpg', 'iphone-14.jpg', 'samsung-a55.jpg',
        'airpods-pro.jpg', 'sony-wh-1000xm5.jpg', 'samsung-wireless-charger.jpg', 'iphone-leather-case.jpg',
        'apple-pencil.jpg', 'logitech-wireless-mouse.jpg', 'mechanical-keyboard.jpg', 'external-monitor.jpg',
        'external-hdd.jpg', 'webcam-hd.jpg', 'hp-laser-printer.jpg', 'bluetooth-speaker.jpg'
    ];
    
    // إنشاء ملف نصي بسيط لكل صورة (بديل مؤقت)
    foreach ($default_images as $filename) {
        $filepath = 'uploads/products/' . $filename;
        if (!file_exists($filepath)) {
            // إنشاء ملف نصي بسيط كبديل للصورة
            $content = "صورة المنتج: " . str_replace('.jpg', '', $filename);
            file_put_contents($filepath . '.txt', $content);
        }
    }
}

// تهيئة قاعدة البيانات عند تحميل الملف
initializeDatabase();
?> 