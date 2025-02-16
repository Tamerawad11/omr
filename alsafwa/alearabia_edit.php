<?php
// إعدادات الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alsafwa_database";

// إنشاء الاتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// التحقق من استلام الـ ID عبر GET أو POST
$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);

// جلب بيانات القطعة في حالة الطلب عبر GET
$row = [];
if ($id && $_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT * FROM parts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        die("لم يتم العثور على القطعة المطلوبة.");
    }
    $stmt->close();
}

// التحقق من أن النموذج تم تقديمه عبر POST (عملية التحديث)
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && $id) {
    // التحقق من المدخلات
    $name = trim($_POST['name']);
    $comm = trim($_POST['comm']);  // الملاحظات
    $part_number = trim($_POST['part_number']);
    $fail = trim($_POST['fail']);
    $price2 = floatval($_POST['price2']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $date = $_POST['date'];  // التاريخ

    // التحقق من أن المدخلات ليست فارغة
    if (empty($name) || empty($comm) || empty($part_number) || empty($fail) || empty($date) || $price2 <= 0 || $price <= 0 || $quantity <= 0) {
        $message = "جميع الحقول يجب أن تكون مملوءة بشكل صحيح.";
    } else {
        // تأكد من أن التاريخ بتنسيق صحيح (YYYY-MM-DD)
        $date_format = date("Y-m-d", strtotime($date));
        if ($date_format != $date) {
            $message = "التاريخ المدخل غير صحيح.";
        } else {
            // تحديث بيانات القطعة في قاعدة البيانات
            $sql = "UPDATE parts SET 
                        name=?, part_number=?, price2=?, price=?, quantity=?, date=?, comm=?, fail=? 
                    WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssddiisi", $name, $part_number, $price2, $price, $quantity, $date_format, $comm, $fail, $id);

            if ($stmt->execute()) {
                $message = "تم تحديث البيانات بنجاح!";
                echo "<meta http-equiv='refresh' content='3;url=alearabia_sr.php?search=$part_number'>";
            } else {
                $message = "حدث خطأ أثناء التحديث: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل القطعة</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 1.5rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #333;
        }
        .form-group {
            margin: 1rem 0;
            text-align: right;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 0.5rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .message {
            margin-top: 1rem;
            padding: 0.5rem;
            color: white;
            background-color: #4CAF50;
            border-radius: 5px;
        }
        .error {
            background-color: #f44336;
        }
        a {
            display: block;
            margin-top: 1rem;
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>تعديل القطعة</h2>
        
        <!-- عرض رسالة النجاح أو الخطأ -->
        <?php if (!empty($message)): ?>
            <div class="message <?php echo (strpos($message, 'خطأ') !== false) ? 'error' : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="alearabia_edit.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="form-group">
                <label for="fail">اسم المورد:</label>
                <input type="text" id="fail" name="fail" value="<?php echo isset($row['fail']) ? htmlspecialchars($row['fail']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="date">تاريخ:</label>
                <input type="date" id="date" name="date" value="<?php echo isset($row['date']) ? htmlspecialchars($row['date']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="name">اسم القطعة:</label>
                <input type="text" id="name" name="name" value="<?php echo isset($row['name']) ? htmlspecialchars($row['name']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="part_number">رقم القطعة:</label>
                <input type="text" id="part_number" name="part_number" value="<?php echo isset($row['part_number']) ? htmlspecialchars($row['part_number']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="price2">سعر القطعة الواحدة:</label>
                <input type="number" id="price2" name="price2" value="<?php echo isset($row['price2']) ? htmlspecialchars($row['price2']) : ''; ?>" required step="0.01">
            </div>

            <div class="form-group">
                <label for="price">الاجمالي:</label>
                <input type="number" id="price" name="price" value="<?php echo isset($row['price']) ? htmlspecialchars($row['price']) : ''; ?>" required step="0.01">
            </div>

            <div class="form-group">
                <label for="quantity">الكمية:</label>
                <input type="number" id="quantity" name="quantity" value="<?php echo isset($row['quantity']) ? htmlspecialchars($row['quantity']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="comm">ملاحظات:</label>
                <input type="text" id="comm" name="comm" value="<?php echo isset($row['comm']) ? htmlspecialchars($row['comm']) : ''; ?>" required>
            </div>

            <input type="submit" value="تحديث">
        </form>
        <a href="alearabia_mg.html">رجوع</a>
    </div>
</body>
</html>
