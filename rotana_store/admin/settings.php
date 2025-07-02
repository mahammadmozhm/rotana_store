<?php
session_start();
require_once '../config/database.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

$admin_id = $_SESSION['admin_id'] ?? null;

// معالجة حفظ الإعدادات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        
        // حفظ إعدادات المتجر
        $settings = [
            'store_name' => $_POST['store_name'] ?? 'روتانا',
            'store_description' => $_POST['store_description'] ?? 'متجر الأجهزة الإلكترونية الأول في العراق',
            'whatsapp_number' => $_POST['whatsapp_number'] ?? '+9647501234567',
            'phone_number' => $_POST['phone_number'] ?? '+9647501234567',
            'address' => $_POST['address'] ?? 'بغداد، العراق',
            'facebook_url' => $_POST['facebook_url'] ?? '#',
            'instagram_url' => $_POST['instagram_url'] ?? '#',
            'telegram_url' => $_POST['telegram_url'] ?? '#'
        ];
        
        foreach ($settings as $key => $value) {
            saveSetting($key, $value);
        }
        
        // تحديث بيانات المستخدم إذا تم تغييرها
        if (!empty($_POST['admin_username']) || !empty($_POST['admin_password'])) {
            $updateFields = [];
            $updateValues = [];
            
            if (!empty($_POST['admin_username'])) {
                $updateFields[] = "username = ?";
                $updateValues[] = $_POST['admin_username'];
            }
            
            if (!empty($_POST['admin_password'])) {
                $updateFields[] = "password = ?";
                $updateValues[] = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
            }
            
            if (!empty($updateFields)) {
                $updateValues[] = $admin_id;
                $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($updateValues);
            }
        }
        
        logActivity('update_settings', 'تم تحديث إعدادات المتجر', $admin_id);
        $success_message = 'تم حفظ الإعدادات بنجاح';
        
    } catch (Exception $e) {
        $error_message = 'خطأ في حفظ الإعدادات: ' . $e->getMessage();
    }
}

// تحميل الإعدادات الحالية
$current_settings = [
    'store_name' => getSetting('store_name', 'روتانا'),
    'store_description' => getSetting('store_description', 'متجر الأجهزة الإلكترونية الأول في العراق'),
    'whatsapp_number' => getSetting('whatsapp_number', '+9647501234567'),
    'phone_number' => getSetting('phone_number', '+9647501234567'),
    'address' => getSetting('address', 'بغداد، العراق'),
    'facebook_url' => getSetting('facebook_url', '#'),
    'instagram_url' => getSetting('instagram_url', '#'),
    'telegram_url' => getSetting('telegram_url', '#')
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الإعدادات - روتانا</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <!-- Header -->
    <header class="admin-header">
        <div class="admin-nav">
            <div class="admin-logo">
                <h1><i class="fas fa-mobile-alt"></i> روتانا</h1>
                <span>لوحة الإدارة</span>
            </div>
            <div class="admin-user">
                <span>مرحباً، <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    تسجيل الخروج
                </a>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <nav class="admin-nav-menu">
            <a href="dashboard.php" class="nav-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>لوحة التحكم</span>
            </a>
            <a href="products.php?category=laptops" class="nav-item">
                <i class="fas fa-laptop"></i>
                <span>إدارة اللابتوبات</span>
            </a>
            <a href="products.php?category=phones" class="nav-item">
                <i class="fas fa-mobile-alt"></i>
                <span>إدارة الهواتف</span>
            </a>
            <a href="products.php?category=accessories" class="nav-item">
                <i class="fas fa-headphones"></i>
                <span>إدارة الاكسسوارات</span>
            </a>
            <a href="settings.php" class="nav-item active">
                <i class="fas fa-cog"></i>
                <span>الإعدادات</span>
            </a>
            <a href="../index.php" class="nav-item">
                <i class="fas fa-external-link-alt"></i>
                <span>عرض المتجر</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-content">
            <div class="page-header">
                <h2><i class="fas fa-cog"></i> إعدادات المتجر</h2>
                <p>تعديل معلومات المتجر وبيانات الاتصال</p>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <div class="settings-container">
                <form method="POST" class="settings-form">
                    <!-- Store Information -->
                    <div class="settings-section">
                        <h3><i class="fas fa-store"></i> معلومات المتجر</h3>
                        
                        <div class="form-group">
                            <label for="store_name">اسم المتجر</label>
                            <input type="text" id="store_name" name="store_name" value="<?php echo htmlspecialchars($current_settings['store_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="store_description">وصف المتجر</label>
                            <textarea id="store_description" name="store_description" rows="3"><?php echo htmlspecialchars($current_settings['store_description']); ?></textarea>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="settings-section">
                        <h3><i class="fas fa-phone"></i> معلومات الاتصال</h3>
                        
                        <div class="form-group">
                            <label for="whatsapp_number">رقم الواتساب</label>
                            <input type="text" id="whatsapp_number" name="whatsapp_number" value="<?php echo htmlspecialchars($current_settings['whatsapp_number']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone_number">رقم الهاتف</label>
                            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($current_settings['phone_number']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">العنوان</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($current_settings['address']); ?>" required>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="settings-section">
                        <h3><i class="fas fa-share-alt"></i> وسائل التواصل الاجتماعي</h3>
                        
                        <div class="form-group">
                            <label for="facebook_url">رابط فيسبوك</label>
                            <input type="url" id="facebook_url" name="facebook_url" value="<?php echo htmlspecialchars($current_settings['facebook_url']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="instagram_url">رابط انستغرام</label>
                            <input type="url" id="instagram_url" name="instagram_url" value="<?php echo htmlspecialchars($current_settings['instagram_url']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="telegram_url">رابط تيليجرام</label>
                            <input type="url" id="telegram_url" name="telegram_url" value="<?php echo htmlspecialchars($current_settings['telegram_url']); ?>">
                        </div>
                    </div>

                    <!-- Admin Settings -->
                    <div class="settings-section">
                        <h3><i class="fas fa-user-shield"></i> إعدادات المدير</h3>
                        
                        <div class="form-group">
                            <label for="admin_username">اسم المستخدم</label>
                            <input type="text" id="admin_username" name="admin_username" value="admin" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="admin_password">كلمة المرور الجديدة (اتركها فارغة إذا لم ترد تغييرها)</label>
                            <input type="password" id="admin_password" name="admin_password">
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            حفظ الإعدادات
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html> 