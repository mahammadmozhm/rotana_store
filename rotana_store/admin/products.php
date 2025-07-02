<?php
session_start();
require_once '../config/database.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

$category = $_GET['category'] ?? 'laptops';
$action = $_GET['action'] ?? 'list';
$admin_id = $_SESSION['admin_id'] ?? null;

// معالجة إضافة/تعديل/حذف المنتجات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add':
                    // إضافة منتج جديد
                    $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
                    $stmt->execute([$category]);
                    $categoryId = $stmt->fetchColumn();
                    
                    if ($categoryId) {
                        $imageUrl = '';
                        
                        // معالجة رفع الصورة
                        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                            $uploadResult = uploadImage($_FILES['product_image']);
                            if (isset($uploadResult['success'])) {
                                $imageUrl = $uploadResult['filename'];
                            } else {
                                $error_message = $uploadResult['error'];
                                break;
                            }
                        }
                        
                        $stmt = $pdo->prepare("INSERT INTO products (category_id, title, description, price, icon, image_url, is_active, is_featured) VALUES (?, ?, ?, ?, ?, ?, 1, 0)");
                        $stmt->execute([
                            $categoryId,
                            $_POST['title'],
                            $_POST['description'],
                            $_POST['price'],
                            $_POST['icon'],
                            $imageUrl
                        ]);
                        
                        logActivity('add_product', 'تم إضافة منتج جديد: ' . $_POST['title'], $admin_id, $imageUrl);
                        // إعادة التوجيه بعد الإضافة لمنع إعادة الإرسال عند التحديث
                        header('Location: products.php?category=' . urlencode($category) . '&action=add&success=1');
                        exit();
                    }
                    break;
                    
                case 'edit':
                    // تعديل منتج موجود
                    $imageUrl = $_POST['current_image'] ?? '';
                    
                    // معالجة رفع الصورة الجديدة
                    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                        $uploadResult = uploadImage($_FILES['product_image']);
                        if (isset($uploadResult['success'])) {
                            // حذف الصورة القديمة إذا كانت موجودة
                            if (!empty($imageUrl) && file_exists($imageUrl)) {
                                deleteImage($imageUrl);
                            }
                            $imageUrl = $uploadResult['filename'];
                        } else {
                            $error_message = $uploadResult['error'];
                            break;
                        }
                    }
                    
                    $stmt = $pdo->prepare("UPDATE products SET title = ?, description = ?, price = ?, icon = ?, image_url = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['description'],
                        $_POST['price'],
                        $_POST['icon'],
                        $imageUrl,
                        $_POST['product_id']
                    ]);
                    
                    logActivity('edit_product', 'تم تعديل منتج: ' . $_POST['title'], $admin_id);
                    $success_message = 'تم تحديث المنتج بنجاح';
                    break;
                    
                case 'delete':
                    // حذف منتج
                    // الحصول على مسار الصورة قبل الحذف
                    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
                    $stmt->execute([$_POST['product_id']]);
                    $imageUrl = $stmt->fetchColumn();
                    
                    // حذف المنتج من قاعدة البيانات
                    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
                    $stmt->execute([$_POST['product_id']]);
                    
                    // حذف الصورة من الخادم
                    if (!empty($imageUrl) && file_exists($imageUrl)) {
                        deleteImage($imageUrl);
                    }
                    
                    logActivity('delete_product', 'تم حذف منتج: ' . $_POST['product_title'], $admin_id);
                    $success_message = 'تم حذف المنتج بنجاح';
                    break;
                    
                case 'delete_test_products':
                    // حذف كل المنتجات التجريبية حسب الاسم
                    $stmt = $pdo->prepare("DELETE FROM products WHERE title LIKE ? OR title LIKE ? OR title LIKE ? OR title LIKE ? OR title LIKE ? OR title LIKE ? OR title LIKE ? OR title LIKE ?");
                    $stmt->execute([
                        '%تجريبي%',
                        '%tttt%',
                        '%ببببببببب%',
                        '%قققققققققق%',
                        '%تمنتمتنتمنت%',
                        '%سسسسسسسسس%',
                        '%ييييييييييييييييييييي%',
                        '%3333333333333%'
                    ]);
                    $success_message = 'تم حذف كل المنتجات التجريبية بنجاح';
                    break;
                    
                case 'delete_selected_real_laptops':
                    // حذف اللابتوبات الحقيقية المذكورة بالاسم
                    $stmt = $pdo->prepare("DELETE FROM products WHERE title IN (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        'لابتوب Lenovo ThinkPad',
                        'لابتوب HP Pavilion',
                        'لابتوب Dell Inspiron',
                        'لابتوب ASUS VivoBook',
                        'لابتوب Acer Aspire',
                        'لابتوب MacBook Air'
                    ]);
                    $success_message = 'تم حذف اللابتوبات الحقيقية المحددة بنجاح';
                    break;
            }
        }
    } catch (Exception $e) {
        $error_message = 'خطأ في قاعدة البيانات: ' . $e->getMessage();
    }
}

// تحميل المنتجات من قاعدة البيانات
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE c.slug = ? AND p.is_active = 1 
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$category]);
    $products = $stmt->fetchAll();
    
    // الحصول على معلومات الفئة
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$category]);
    $categoryInfo = $stmt->fetch();
    
} catch (Exception $e) {
    $products = [];
    $categoryInfo = null;
}

$category_names = [
    'laptops' => 'اللابتوبات',
    'phones' => 'الهواتف',
    'accessories' => 'الاكسسوارات'
];

$category_icons = [
    'laptops' => 'fas fa-laptop',
    'phones' => 'fas fa-mobile-alt',
    'accessories' => 'fas fa-headphones'
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة <?php echo $category_names[$category]; ?> - روتانا</title>
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
            <a href="products.php?category=laptops" class="nav-item <?php echo $category === 'laptops' ? 'active' : ''; ?>">
                <i class="fas fa-laptop"></i>
                <span>إدارة اللابتوبات</span>
            </a>
            <a href="products.php?category=phones" class="nav-item <?php echo $category === 'phones' ? 'active' : ''; ?>">
                <i class="fas fa-mobile-alt"></i>
                <span>إدارة الهواتف</span>
            </a>
            <a href="products.php?category=accessories" class="nav-item <?php echo $category === 'accessories' ? 'active' : ''; ?>">
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
                <div class="page-title">
                    <h2><i class="<?php echo $category_icons[$category]; ?>"></i> إدارة <?php echo $category_names[$category]; ?></h2>
                    <p>إضافة وتعديل وحذف منتجات <?php echo $category_names[$category]; ?></p>
                </div>
                <div class="page-actions">
                    <a href="products.php?category=<?php echo $category; ?>&action=add" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        إضافة منتج جديد
                    </a>
                </div>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    تم إضافة المنتج بنجاح
                </div>
            <?php endif; ?>

            <?php if ($action === 'add'): ?>
                <!-- Add Product Form -->
                <div class="form-container">
                    <h3>إضافة منتج جديد</h3>
                    <form method="POST" class="product-form" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="form-group">
                            <label for="title">اسم المنتج</label>
                            <input type="text" id="title" name="title" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">وصف المنتج</label>
                            <textarea id="description" name="description" rows="4" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="price">السعر (د.ع)</label>
                            <input type="number" id="price" name="price" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="icon">أيقونة المنتج</label>
                            <select id="icon" name="icon" required>
                                <option value="fas fa-laptop">لابتوب</option>
                                <option value="fas fa-mobile-alt">هاتف</option>
                                <option value="fas fa-headphones">سماعات</option>
                                <option value="fas fa-charging-station">شاحن</option>
                                <option value="fas fa-mouse">ماوس</option>
                                <option value="fas fa-keyboard">لوحة مفاتيح</option>
                                <option value="fas fa-tv">شاشة</option>
                                <option value="fas fa-hdd">قرص صلب</option>
                                <option value="fas fa-video">كاميرا</option>
                                <option value="fas fa-print">طابعة</option>
                                <option value="fas fa-volume-up">مكبر صوت</option>
                                <option value="fab fa-apple">آبل</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="product_image">صورة المنتج</label>
                            <input type="file" id="product_image" name="product_image" accept="image/*">
                            <small class="form-help">الأنواع المسموحة: JPG, PNG, GIF, WEBP. الحد الأقصى: 5MB</small>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                حفظ المنتج
                            </button>
                            <a href="products.php?category=<?php echo $category; ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            <?php elseif ($action === 'edit' && isset($_GET['id'])): ?>
                <div class="form-container">
                    <h3>تعديل المنتج</h3>
                    <form method="POST" class="product-form" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                        
                        <div class="form-group">
                            <label for="title">اسم المنتج</label>
                            <input type="text" id="title" name="title" value="<?php echo isset($product['title']) ? htmlspecialchars($product['title']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">وصف المنتج</label>
                            <textarea id="description" name="description" rows="4" required><?php echo isset($product['description']) ? htmlspecialchars($product['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="price">السعر (د.ع)</label>
                            <input type="number" id="price" name="price" value="<?php echo isset($product['price']) ? htmlspecialchars($product['price']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="icon">أيقونة المنتج</label>
                            <select id="icon" name="icon" required>
                                <option value="fas fa-laptop" <?php echo isset($product['icon']) && $product['icon'] === 'fas fa-laptop' ? 'selected' : ''; ?>>لابتوب</option>
                                <option value="fas fa-mobile-alt" <?php echo isset($product['icon']) && $product['icon'] === 'fas fa-mobile-alt' ? 'selected' : ''; ?>>هاتف</option>
                                <option value="fas fa-headphones" <?php echo isset($product['icon']) && $product['icon'] === 'fas fa-headphones' ? 'selected' : ''; ?>>سماعات</option>
                                <option value="fas fa-charging-station" <?php echo isset($product['icon']) && $product['icon'] === 'fas fa-charging-station' ? 'selected' : ''; ?>>شاحن</option>
                                <option value="fas fa-mouse" <?php echo isset($product['icon']) && $product['icon'] === 'fas fa-mouse' ? 'selected' : ''; ?>>ماوس</option>
                                <option value="fas fa-keyboard" <?php echo isset($product['icon']) && $product['icon'] === 'fas fa-keyboard' ? 'selected' : ''; ?>>لوحة مفاتيح</option>
                                <option value="fas fa-tv" <?php echo isset($product['icon']) && $product['icon'] === 'fas fa-tv' ? 'selected' : ''; ?>>شاشة</option>
                                <option value="fas fa-hdd" <?php echo isset($product['icon']) && $product['icon'] === 'fas fa-hdd' ? 'selected' : ''; ?>>قرص صلب</option>
                                <option value="fas fa-video" <?php echo isset($product['icon']) && $product['icon'] === 'fas fa-video' ? 'selected' : ''; ?>>كاميرا</option>
                                <option value="fas fa-print" <?php echo isset($product['icon']) && $product['icon'] === 'fas fa-print' ? 'selected' : ''; ?>>طابعة</option>
                                <option value="fas fa-volume-up" <?php echo isset($product['icon']) && $product['icon'] === 'fas fa-volume-up' ? 'selected' : ''; ?>>مكبر صوت</option>
                                <option value="fab fa-apple" <?php echo isset($product['icon']) && $product['icon'] === 'fab fa-apple' ? 'selected' : ''; ?>>آبل</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="product_image">صورة المنتج</label>
                            <input type="file" id="product_image" name="product_image" accept="image/*">
                            <small class="form-help">الأنواع المسموحة: JPG, PNG, GIF, WEBP. الحد الأقصى: 5MB</small>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                حفظ التعديلات
                            </button>
                            <a href="products.php?category=<?php echo $category; ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                إلغاء
                            </a>
                        </div>
                    </form>
                    <form method="POST" style="margin-top:20px;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                        <input type="hidden" name="product_title" value="<?php echo isset($product['title']) ? htmlspecialchars($product['title']) : ''; ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> حذف المنتج نهائياً
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <!-- Products List -->
                <div class="products-table-container">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>الوصف</th>
                                <th>السعر</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 2rem;">
                                        <p>لا توجد منتجات في هذه الفئة</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <?php if (!empty($product['image_url']) && file_exists($product['image_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="product-thumbnail">
                                            <?php else: ?>
                                                <i class="<?php echo htmlspecialchars($product['icon']); ?>"></i>
                                            <?php endif; ?>
                                            <span><?php echo htmlspecialchars($product['title']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                                    <td><?php echo number_format($product['price']); ?> د.ع</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="products.php?category=<?php echo $category; ?>&action=edit&id=<?php echo $product['id']; ?>" class="btn btn-small btn-edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <input type="hidden" name="product_title" value="<?php echo htmlspecialchars($product['title']); ?>">
                                                <button type="submit" class="btn btn-small btn-delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html> 