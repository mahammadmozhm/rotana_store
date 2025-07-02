<?php
session_start();

// حذف جميع متغيرات الجلسة
session_unset();

// تدمير الجلسة
session_destroy();

// إعادة التوجيه إلى صفحة تسجيل الدخول
header('Location: index.php');
exit();
?> 