<?php
session_start();
require_once '../config/database.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$admin_id = $_SESSION['admin_id'] ?? null;

// الحصول على الإحصائيات
try {
    $pdo = getDBConnection();
    
    // عدد اللابتوبات
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id = c.id WHERE c.slug = 'laptops' AND p.is_active = 1");
    $stmt->execute();
    $laptops_count = $stmt->fetchColumn();
    
    // عدد الهواتف
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id = c.id WHERE c.slug = 'phones' AND p.is_active = 1");
    $stmt->execute();
    $phones_count = $stmt->fetchColumn();
    
    // عدد الاكسسوارات
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id = c.id WHERE c.slug = 'accessories' AND p.is_active = 1");
    $stmt->execute();
    $accessories_count = $stmt->fetchColumn();
    
    // إجمالي المنتجات
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE is_active = 1");
    $stmt->execute();
    $total_products = $stmt->fetchColumn();
    
    // النشاطات الأخيرة
    $stmt = $pdo->prepare("SELECT a.*, u.username FROM activities a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 5");
    $stmt->execute();
    $recent_activities = $stmt->fetchAll();
    
} catch (Exception $e) {
    $laptops_count = 0;
    $phones_count = 0;
    $accessories_count = 0;
    $total_products = 0;
    $recent_activities = [];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - روتانا</title>
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
                <span>مرحباً، <?php echo htmlspecialchars($admin_username); ?></span>
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
            <a href="dashboard.php" class="nav-item active">
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
            <a href="settings.php" class="nav-item">
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
                <h2>لوحة التحكم</h2>
                <p>مرحباً بك في لوحة إدارة متجر روتانا</p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <div class="stat-info">
                        <h3>اللابتوبات</h3>
                        <p class="stat-number"><?php echo $laptops_count; ?></p>
                        <span class="stat-label">منتج متوفر</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>الهواتف</h3>
                        <p class="stat-number"><?php echo $phones_count; ?></p>
                        <span class="stat-label">منتج متوفر</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-headphones"></i>
                    </div>
                    <div class="stat-info">
                        <h3>الاكسسوارات</h3>
                        <p class="stat-number"><?php echo $accessories_count; ?></p>
                        <span class="stat-label">منتج متوفر</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3>إجمالي المنتجات</h3>
                        <p class="stat-number"><?php echo $total_products; ?></p>
                        <span class="stat-label">منتج</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3>إجراءات سريعة</h3>
                <div class="actions-grid">
                    <a href="products.php?category=laptops&action=add" class="action-card">
                        <i class="fas fa-plus"></i>
                        <span>إضافة لابتوب جديد</span>
                    </a>
                    <a href="products.php?category=phones&action=add" class="action-card">
                        <i class="fas fa-plus"></i>
                        <span>إضافة هاتف جديد</span>
                    </a>
                    <a href="products.php?category=accessories&action=add" class="action-card">
                        <i class="fas fa-plus"></i>
                        <span>إضافة اكسسوار جديد</span>
                    </a>
                    <a href="settings.php" class="action-card">
                        <i class="fas fa-cog"></i>
                        <span>تعديل إعدادات المتجر</span>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="recent-activity">
                <h3>النشاطات الأخيرة</h3>
                <div class="activity-list">
                    <?php if (empty($recent_activities)): ?>
                        <div class="activity-item">
                            <div class="activity-content">
                                <p>لا توجد نشاطات حديثة</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <?php
                                    $icon = 'fas fa-info-circle';
                                    switch ($activity['action']) {
                                        case 'login':
                                            $icon = 'fas fa-sign-in-alt';
                                            break;
                                        case 'add_product':
                                            $icon = 'fas fa-plus-circle';
                                            break;
                                        case 'edit_product':
                                            $icon = 'fas fa-edit';
                                            break;
                                        case 'delete_product':
                                            $icon = 'fas fa-trash';
                                            break;
                                        case 'update_settings':
                                            $icon = 'fas fa-cog';
                                            break;
                                    }
                                    ?>
                                    <i class="<?php echo $icon; ?>"></i>
                                </div>
                                <div class="activity-content">
                                    <p><?php echo htmlspecialchars($activity['description']); ?></p>
                                    <span class="activity-time">
                                        <?php 
                                        $time = new DateTime($activity['created_at']);
                                        $now = new DateTime();
                                        $diff = $time->diff($now);
                                        
                                        if ($diff->days > 0) {
                                            echo 'منذ ' . $diff->days . ' يوم';
                                        } elseif ($diff->h > 0) {
                                            echo 'منذ ' . $diff->h . ' ساعة';
                                        } elseif ($diff->i > 0) {
                                            echo 'منذ ' . $diff->i . ' دقيقة';
                                        } else {
                                            echo 'الآن';
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 