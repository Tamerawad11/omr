<?php
session_start();
session_destroy(); // دمر الجلسة
header("Location: login.php"); // ارجع إلى صفحة تسجيل الدخول
exit();
?>
