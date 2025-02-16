<?php
session_start();

// إعداد اتصال قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alsafwa_database";

$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// التحقق من القيم المدخلة
$part_id = intval($_POST['part_id']);
$quantity_taken = intval($_POST['quantity']);

if ($part_id > 0 && $quantity_taken > 0) {
    // بدء معاملة لضمان التزامن
    $conn->begin_transaction();

    try {
        // جلب الكمية الحالية والتحقق من القطعة
        $sql = "SELECT quantity FROM parts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $part_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_quantity = intval($row['quantity']);

            if ($quantity_taken <= $current_quantity) {
                $new_quantity = $current_quantity - $quantity_taken;

                // تحديث الكمية
                $update_sql = "UPDATE parts SET quantity = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ii", $new_quantity, $part_id);
                $update_stmt->execute();

                // التزام المعاملة
                $conn->commit();

                echo "<h2>تم أخذ $quantity_taken من القطعة بنجاح!</h2>";
                echo "<h2>الكمية الجديدة: $new_quantity</h2>";
            } else {
                echo "<h2>خطأ: الكمية المأخوذة أكبر من الكمية المتاحة!</h2>";
            }
        } else {
            echo "<h2>خطأ: القطعة غير موجودة!</h2>";
        }
    } catch (Exception $e) {
        $conn->rollback(); // إلغاء المعاملة عند حدوث خطأ
        echo "<h2>حدث خطأ أثناء معالجة الطلب: " . $e->getMessage() . "</h2>";
    }

    $stmt->close();
} else {
    echo "<h2>الرجاء إدخال بيانات صحيحة.</h2>";
}

$conn->close();
?>

<!-- زر للعودة إلى الصفحة السابقة -->
<br>
<a href="alearabia_take.php" style="text-decoration: none; font-size: 16px; color: blue;">العودة</a>
