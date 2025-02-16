<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alsafwa_database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<p style='color:red;'>فشل الاتصال بقاعدة البيانات</p>");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $part_id = intval($_POST['part_id']);
    $quantity_added = intval($_POST['quantity']);

    if ($part_id > 0 && $quantity_added > 0) {
        $stmt = $conn->prepare("UPDATE parts SET quantity = quantity + ? WHERE id = ?");
        $stmt->bind_param("ii", $quantity_added, $part_id);
        
        if ($stmt->execute()) {
            echo "<p style='color:green;'>تمت إضافة الكمية بنجاح!</p>";
        } else {
            echo "<p style='color:red;'>حدث خطأ أثناء التحديث.</p>";
        }
        $stmt->close();
    }
}

$conn->close();
?>
