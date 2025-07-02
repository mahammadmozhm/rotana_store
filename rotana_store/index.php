<?php
require_once 'config/database.php';

// تحميل الإعدادات
$store_name = getSetting('store_name', 'روتانا');
$store_description = getSetting('store_description', 'متجر الأجهزة الإلكترونية الأول في العراق');
$whatsapp_number = getSetting('whatsapp_number', '+9647501234567');
$phone_number = getSetting('phone_number', '+9647501234567');
$address = getSetting('address', 'بغداد، العراق');
$facebook_url = getSetting('facebook_url', '#');
$instagram_url = getSetting('instagram_url', '#');
$telegram_url = getSetting('telegram_url', '#');
$currency = getSetting('currency', 'د.ع');

// تحميل المنتجات من قاعدة البيانات
try {
    $pdo = getDBConnection();
    
    // اللابتوبات
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name, c.slug as category_slug
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE c.slug = 'laptops' AND p.is_active = 1
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $laptops = $stmt->fetchAll();
    
    // الهواتف
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name, c.slug as category_slug
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE c.slug = 'phones' AND p.is_active = 1
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $phones = $stmt->fetchAll();
    
    // الاكسسوارات
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name, c.slug as category_slug
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE c.slug = 'accessories' AND p.is_active = 1
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $accessories = $stmt->fetchAll();
    
    // إحصائيات سريعة
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_products FROM products WHERE is_active = 1");
    $stmt->execute();
    $total_products = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_categories FROM categories WHERE is_active = 1");
    $stmt->execute();
    $total_categories = $stmt->fetchColumn();
    
} catch (Exception $e) {
    error_log("خطأ في جلب المنتجات: " . $e->getMessage());
    $laptops = [];
    $phones = [];
    $accessories = [];
    $total_products = 0;
    $total_categories = 0;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($store_name); ?> - متجر الأجهزة الإلكترونية</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="manifest" href="manifest.json">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="logo">
                    <h1><i class="fas fa-mobile-alt"></i> <?php echo htmlspecialchars($store_name); ?></h1>
                </div>
                <ul class="nav-menu">
                    <li><a href="#home" class="nav-link">الرئيسية</a></li>
                    <li><a href="#laptops" class="nav-link">اللابتوبات</a></li>
                    <li><a href="#phones" class="nav-link">الهواتف</a></li>
                    <li><a href="#accessories" class="nav-link">الاكسسوارات</a></li>
                    <li><a href="#contact" class="nav-link">اتصل بنا</a></li>
                </ul>
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <h1 class="hero-title">مرحباً بكم في متجر <?php echo htmlspecialchars($store_name); ?></h1>
            <div class="hero-quotes">
                <div class="quote-container">
                    <p class="quote-text" id="quoteText">أفضل الأجهزة الإلكترونية بأفضل الأسعار</p>
                </div>
            </div>
            <p class="hero-subtitle"><?php echo htmlspecialchars($store_description); ?></p>
            <div class="hero-buttons">
                <a href="#products" class="btn btn-primary">تصفح المنتجات</a>
                <a href="#contact" class="btn btn-secondary">تواصل معنا</a>
            </div>
        </div>
        <div class="hero-image">
            <div class="floating-devices">
                <i class="fas fa-laptop"></i>
                <i class="fas fa-mobile-alt"></i>
                <i class="fas fa-headphones"></i>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="statistics">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <i class="fas fa-box"></i>
                    <h3><?php echo number_format($total_products); ?></h3>
                    <p>منتج متوفر</p>
                </div>
                <div class="stat-item">
                    <i class="fas fa-tags"></i>
                    <h3><?php echo number_format($total_categories); ?></h3>
                    <p>فئة منتجات</p>
                </div>
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <h3>1000+</h3>
                    <p>عميل راضي</p>
                </div>
                <div class="stat-item">
                    <i class="fas fa-shipping-fast"></i>
                    <h3>24</h3>
                    <p>ساعة توصيل</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <h2 class="section-title">فئات المنتجات</h2>
            <div class="categories-grid">
                <div class="category-card" data-category="laptops">
                    <i class="fas fa-laptop"></i>
                    <h3>اللابتوبات</h3>
                    <p>أحدث أجهزة الكمبيوتر المحمولة</p>
                </div>
                <div class="category-card" data-category="phones">
                    <i class="fas fa-mobile-alt"></i>
                    <h3>الهواتف الذكية</h3>
                    <p>أفضل الهواتف الحديثة</p>
                </div>
                <div class="category-card" data-category="accessories">
                    <i class="fas fa-headphones"></i>
                    <h3>الاكسسوارات</h3>
                    <p>ملحقات الأجهزة الإلكترونية</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="products">
        <div class="container">
            <h2 class="section-title">منتجاتنا المميزة</h2>
            <div class="search-container">
                <input type="text" id="productSearch" placeholder="ابحث عن منتج...">
                <span class="search-icon"><i class="fas fa-search"></i></span>
            </div>
            
            <!-- Laptops -->
            <div id="laptops" class="product-section">
                <h3 class="product-category-title">اللابتوبات</h3>
                <div class="products-grid">
                    <?php if (empty($laptops)): ?>
                        <div class="no-products">
                            <p>لا توجد لابتوبات متوفرة حالياً</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($laptops as $laptop): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <?php
                                    $img = $laptop['image_url'];
                                    if (strpos($img, 'admin/uploads/products/') !== 0) {
                                        $img = 'admin/uploads/products/' . basename($img);
                                    }
                                    ?>
                                    <?php if (!empty($img) && pathinfo($img, PATHINFO_EXTENSION) !== 'txt'): ?>
                                        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($laptop['title']); ?>">
                                    <?php else: ?>
                                        <div class="product-icon">
                                            <i class="<?php echo htmlspecialchars($laptop['icon']); ?>"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3><?php echo htmlspecialchars($laptop['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($laptop['description']); ?></p>
                                    <div class="product-price">
                                        <span class="price"><?php echo number_format($laptop['price']); ?> <?php echo htmlspecialchars($currency); ?></span>
                                        <?php if (!empty($laptop['old_price']) && $laptop['old_price'] > $laptop['price']): ?>
                                            <span class="old-price"><?php echo number_format($laptop['old_price']); ?> <?php echo htmlspecialchars($currency); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                    $wa_message = "أريد طلب: {$laptop['title']}\nالسعر: " . number_format($laptop['price']) . " {$currency}\nالفئة: {$laptop['category_name']}";
                                    ?>
                                    <a href="https://wa.me/9647813681814?text=<?php echo urlencode($wa_message); ?>" class="order-btn" target="_blank">
                                        <i class="fab fa-whatsapp"></i> احجز الآن
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Phones -->
            <div id="phones" class="product-section">
                <h3 class="product-category-title">الهواتف الذكية</h3>
                <div class="products-grid">
                    <?php if (empty($phones)): ?>
                        <div class="no-products">
                            <p>لا توجد هواتف متوفرة حالياً</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($phones as $phone): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <?php
                                    $img = $phone['image_url'];
                                    if (strpos($img, 'admin/uploads/products/') !== 0) {
                                        $img = 'admin/uploads/products/' . basename($img);
                                    }
                                    ?>
                                    <?php if (!empty($img) && pathinfo($img, PATHINFO_EXTENSION) !== 'txt'): ?>
                                        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($phone['title']); ?>">
                                    <?php else: ?>
                                        <div class="product-icon">
                                            <i class="<?php echo htmlspecialchars($phone['icon']); ?>"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3><?php echo htmlspecialchars($phone['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($phone['description']); ?></p>
                                    <div class="product-price">
                                        <span class="price"><?php echo number_format($phone['price']); ?> <?php echo htmlspecialchars($currency); ?></span>
                                        <?php if (!empty($phone['old_price']) && $phone['old_price'] > $phone['price']): ?>
                                            <span class="old-price"><?php echo number_format($phone['old_price']); ?> <?php echo htmlspecialchars($currency); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                    $wa_message = "أريد طلب: {$phone['title']}\nالسعر: " . number_format($phone['price']) . " {$currency}\nالفئة: {$phone['category_name']}";
                                    ?>
                                    <a href="https://wa.me/9647813681814?text=<?php echo urlencode($wa_message); ?>" class="order-btn" target="_blank">
                                        <i class="fab fa-whatsapp"></i> احجز الآن
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Accessories -->
            <div id="accessories" class="product-section">
                <h3 class="product-category-title">الاكسسوارات</h3>
                <div class="products-grid">
                    <?php if (empty($accessories)): ?>
                        <div class="no-products">
                            <p>لا توجد اكسسوارات متوفرة حالياً</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($accessories as $accessory): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <?php
                                    $img = $accessory['image_url'];
                                    if (strpos($img, 'admin/uploads/products/') !== 0) {
                                        $img = 'admin/uploads/products/' . basename($img);
                                    }
                                    ?>
                                    <?php if (!empty($img) && pathinfo($img, PATHINFO_EXTENSION) !== 'txt'): ?>
                                        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($accessory['title']); ?>">
                                    <?php else: ?>
                                        <div class="product-icon">
                                            <i class="<?php echo htmlspecialchars($accessory['icon']); ?>"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3><?php echo htmlspecialchars($accessory['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($accessory['description']); ?></p>
                                    <div class="product-price">
                                        <span class="price"><?php echo number_format($accessory['price']); ?> <?php echo htmlspecialchars($currency); ?></span>
                                        <?php if (!empty($accessory['old_price']) && $accessory['old_price'] > $accessory['price']): ?>
                                            <span class="old-price"><?php echo number_format($accessory['old_price']); ?> <?php echo htmlspecialchars($currency); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                    $wa_message = "أريد طلب: {$accessory['title']}\nالسعر: " . number_format($accessory['price']) . " {$currency}\nالفئة: {$accessory['category_name']}";
                                    ?>
                                    <a href="https://wa.me/9647813681814?text=<?php echo urlencode($wa_message); ?>" class="order-btn" target="_blank">
                                        <i class="fab fa-whatsapp"></i> احجز الآن
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <h2 class="section-title">تواصل معنا</h2>
            <div class="contact-content">
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fab fa-whatsapp"></i>
                        <h3>الواتساب</h3>
                        <p>للحجز والاستفسار</p>
                        <a href="https://wa.me/9647813681814" class="whatsapp-btn" target="_blank">
                            <i class="fab fa-whatsapp"></i> احجز الآن
                        </a>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <h3>الهاتف</h3>
                        <p><?php echo htmlspecialchars($phone_number); ?></p>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>العنوان</h3>
                        <p><?php echo htmlspecialchars($address); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-mobile-alt"></i> <?php echo htmlspecialchars($store_name); ?></h3>
                    <p><?php echo htmlspecialchars($store_description); ?></p>
                </div>
                <div class="footer-section">
                    <h4>روابط سريعة</h4>
                    <ul>
                        <li><a href="#home">الرئيسية</a></li>
                        <li><a href="#laptops">اللابتوبات</a></li>
                        <li><a href="#phones">الهواتف</a></li>
                        <li><a href="#accessories">الاكسسوارات</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>تواصل معنا</h4>
                    <div class="social-links">
                        <a href="<?php echo htmlspecialchars($facebook_url); ?>"><i class="fab fa-facebook"></i></a>
                        <a href="<?php echo htmlspecialchars($instagram_url); ?>"><i class="fab fa-instagram"></i></a>
                        <a href="<?php echo htmlspecialchars($telegram_url); ?>"><i class="fab fa-telegram"></i></a>
                        <a href="https://wa.me/9647813681814"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 <?php echo htmlspecialchars($store_name); ?>. جميع الحقوق محفوظة</p>
            </div>
        </div>
    </footer>

    <script>
    const quotes = [
        "ابتسم للحياة وامتلك التقنية!",
        "جهازك الجديد في انتظارك.",
        "التقنية تصنع الفرق.",
        "استثمر في المستقبل مع روتانا.",
        "كل ما تحتاجه من أجهزة هنا.",
        "جودة عالية وأسعار منافسة.",
        "خدمة عملاء مميزة دائماً.",
        "أحدث اللابتوبات والهواتف.",
        "اكسسوارات أصلية 100%.",
        "توصيل سريع لكل العراق.",
        "عروضنا لا تفوت!",
        "منتجات أصلية ومضمونة.",
        "راحة بالك تبدأ من هنا.",
        "تسوق بثقة مع روتانا.",
        "كل جديد في عالم التقنية.",
        "أجهزة تناسب جميع الاحتياجات.",
        "دعم فني متواصل.",
        "تسوق الآن وادفع عند الاستلام.",
        "منتجاتنا تلبي طموحاتك.",
        "أجهزة أصلية بضمان.",
        "أسعارنا الأفضل دائماً.",
        "تجربة تسوق لا مثيل لها.",
        "كل ما تحتاجه في مكان واحد.",
        "منتجاتنا حديثة دائماً.",
        "خدمة ما بعد البيع مميزة.",
        "تسوق بأمان وسهولة.",
        "أجهزة ذكية لحياة أسهل.",
        "عروضنا مستمرة كل يوم.",
        "منتجاتنا أصلية 100%.",
        "توصيل سريع وآمن.",
        "جودة تليق بك.",
        "أحدث الإصدارات التقنية.",
        "خدمة عملاء على مدار الساعة.",
        "منتجاتنا تلبي جميع الأذواق.",
        "تسوق وكن مميزاً.",
        "أجهزة قوية لأداء أفضل.",
        "كل ما تبحث عنه لدينا.",
        "منتجاتنا بضمان حقيقي.",
        "تسوق بثقة وراحة بال.",
        "أجهزة أصلية بأسعار منافسة.",
        "خدمة توصيل سريعة.",
        "منتجاتنا تناسب الجميع.",
        "تسوق الآن وكن رابحاً.",
        "أجهزة حديثة كل يوم.",
        "عروضنا الأقوى في العراق.",
        "منتجاتنا تلبي احتياجاتك.",
        "تسوق بسهولة وأمان.",
        "أجهزة أصلية ومضمونة.",
        "خدمة عملاء مميزة.",
        "منتجاتنا الأفضل دائماً.",
        "تسوق وحقق أحلامك.",
        "أجهزة ذكية لحياة أفضل.",
        "منتجاتنا تلبي جميع الرغبات.",
        "تسوق بثقة مع روتانا.",
        "أحدث الأجهزة بين يديك.",
        "منتجاتنا بضمان حقيقي.",
        "تسوق وكن مميزاً.",
        "أجهزة قوية لأداء أفضل.",
        "كل ما تبحث عنه لدينا.",
        "منتجاتنا بضمان حقيقي.",
        "تسوق بثقة وراحة بال.",
        "أجهزة أصلية بأسعار منافسة.",
        "خدمة توصيل سريعة.",
        "منتجاتنا تناسب الجميع.",
        "تسوق الآن وكن رابحاً.",
        "أجهزة حديثة كل يوم.",
        "عروضنا الأقوى في العراق.",
        "منتجاتنا تلبي احتياجاتك.",
        "تسوق بسهولة وأمان.",
        "أجهزة أصلية ومضمونة.",
        "خدمة عملاء مميزة.",
        "منتجاتنا الأفضل دائماً.",
        "تسوق وحقق أحلامك.",
        "أجهزة ذكية لحياة أفضل."
    ];
    let quoteIndex = 0;
    function showNextQuote() {
        const quoteText = document.getElementById('quoteText');
        if (!quoteText) return;
        quoteText.style.opacity = 0;
        setTimeout(() => {
            quoteText.textContent = quotes[quoteIndex];
            quoteText.style.opacity = 1;
            quoteIndex = (quoteIndex + 1) % quotes.length;
        }, 400);
    }
    setInterval(showNextQuote, 2500);
    </script>
    <style>
    .hero-quotes { margin: 10px 0 0 0; text-align: center; }
    .quote-container { min-height: 32px; }
    .quote-text {
        font-size: 1.1rem;
        color:rgb(255, 255, 255);
        font-weight: 600;
        transition: opacity 0.4s;
        opacity: 1;
        direction: rtl;
        letter-spacing: 0.5px;
    }
    </style>
    <script src="assets/js/script.js"></script>
    <script>
    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      deferredPrompt = e;
      // إظهار بانر تثبيت مخصص
      if (!document.getElementById('installBanner')) {
        let installBanner = document.createElement('div');
        installBanner.id = 'installBanner';
        installBanner.innerHTML = `
          <div style="position:fixed;bottom:0;left:0;width:100vw;background:#fffbe9;border-top:2px solid #a855f7;box-shadow:0 -2px 16px rgba(139,92,246,0.10);padding:18px 12px 12px 12px;z-index:9999;display:flex;align-items:center;justify-content:center;gap:16px;direction:rtl;">
            <span style="font-size:1.1rem;color:#333;">هل تريد تثبيت الصفحة على الشاشة الرئيسية؟</span>
            <button id="installBtn" style="padding:8px 22px;background:#8b5cf6;color:#fff;border:none;border-radius:8px;font-size:1rem;font-weight:600;cursor:pointer;">تثبيت</button>
            <button id="closeInstall" style="padding:8px 18px;background:#eee;color:#333;border:none;border-radius:8px;font-size:1rem;cursor:pointer;">إغلاق</button>
          </div>
        `;
        document.body.appendChild(installBanner);

        document.getElementById('installBtn').onclick = function() {
          installBanner.remove();
          deferredPrompt.prompt();
          deferredPrompt.userChoice.then(() => { deferredPrompt = null; });
        };
        document.getElementById('closeInstall').onclick = function() {
          installBanner.remove();
        };
      }
    });
    </script>
    <script>
    document.getElementById('productSearch').addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const desc = card.querySelector('p').textContent.toLowerCase();
            if (title.includes(query) || desc.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
        // إظهار رسالة إذا لم يوجد أي منتج مطابق
        document.querySelectorAll('.product-section').forEach(section => {
            const visible = Array.from(section.querySelectorAll('.product-card')).some(card => card.style.display !== 'none');
            const noProducts = section.querySelector('.no-products');
            if (!visible) {
                if (noProducts) {
                    noProducts.style.display = '';
                }
            } else {
                if (noProducts) {
                    noProducts.style.display = 'none';
                }
            }
        });
    });
    </script>
</body>
</html> 