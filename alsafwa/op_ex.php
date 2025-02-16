<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alsafwa_database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// التأكد من استلام البيانات
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['part_id'], $_POST['quantity'], $_POST['person_name'], $_POST['car_number'], $_POST['location'])) {
        die("<h2 style='color: red;'>خطأ: بعض البيانات غير مُرسلة. تأكد من إدخال جميع الحقول.</h2>");
    }

    $part_id = intval($_POST['part_id']);
    $quantity_taken = intval($_POST['quantity']);
    $person_name = $conn->real_escape_string(trim($_POST['person_name']));
    $car_number = $conn->real_escape_string(trim($_POST['car_number']));
    $location = $conn->real_escape_string(trim($_POST['location']));

    // التحقق من صحة البيانات
    if ($part_id > 0 && $quantity_taken > 0 && !empty($person_name) && !empty($car_number) && !empty($location)) {
        // جلب الكمية الحالية للقطعة باستخدام prepared statement
        $stmt = $conn->prepare("SELECT quantity FROM parts WHERE id = ?");
        $stmt->bind_param("i", $part_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_quantity = $row['quantity'];

            if ($current_quantity >= $quantity_taken) {
                // حساب الكمية الجديدة
                $new_quantity = $current_quantity - $quantity_taken;

                // تحديث الكمية في قاعدة البيانات باستخدام prepared statement
                $update_stmt = $conn->prepare("UPDATE parts SET quantity = ? WHERE id = ?");
                $update_stmt->bind_param("ii", $new_quantity, $part_id);

                // تسجيل العملية في جدول transactions
                $insert_stmt = $conn->prepare("INSERT INTO transactions (part_id, person_name, quantity_taken, car_number, location) 
                                               VALUES (?, ?, ?, ?, ?)");
                $insert_stmt->bind_param("isiss", $part_id, $person_name, $quantity_taken, $car_number, $location);

                if ($update_stmt->execute() && $insert_stmt->execute()) {
                    echo "<h2 style='color: green;'>تم تسجيل العملية بنجاح!</h2>";
                    echo "<p>الكمية الجديدة للقطعة: $new_quantity</p>";

                    // إشعار عند انخفاض الكمية
                    if ($new_quantity < 5) {
                        echo "<p style='color: orange; font-weight: bold;'>تحذير: المخزون منخفض! تبقى فقط $new_quantity قطع.</p>";
                    }
                } else {
                    echo "<h2 style='color: red;'>خطأ أثناء تحديث البيانات: " . $conn->error . "</h2>";
                }
                $update_stmt->close();
                $insert_stmt->close();
            } else {
                echo "<h2 style='color: red;'>لا توجد كمية كافية.</h2>";
            }
        } else {
            echo "<h2 style='color: red;'>القطعة غير موجودة.</h2>";
        }
        $stmt->close();
    } else {
        echo "<h2 style='color: red;'>بيانات غير صحيحة. الرجاء المحاولة مرة أخرى.</h2>";
    }
}
$conn->close();
?>
