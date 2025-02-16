<?php
// إعدادات الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alsafwa_database";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// التحقق من أن الطلب هو POST ووجود المعرف
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    if ($id > 0) {
        // استخدام استعلام مُحضّر للحذف
        $stmt = $conn->prepare("DELETE FROM parts WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<h2>تم حذف القطعة بنجاح!</h2>";
        } else {
            echo "<h2>خطأ أثناء الحذف:</h2> " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "<h2>طلب غير صالح: المعرف غير صالح.</h2>";
    }
} else {
    echo "<h2>طلب غير صالح: الطريقة غير مدعومة أو البيانات مفقودة.</h2>";
}

// إغلاق الاتصال
$conn->close();
?>

<link rel="stylesheet" href="styles.css">
<title>جاري الحذف</title>
<meta http-equiv="refresh" content="4;url=alearabia_sr.php?search=">
