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

// التحقق من إرسال البيانات باستخدام POST
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['fail'], $_POST['date'], $_POST['name'], $_POST['part_number'], $_POST['price2'], $_POST['price'], $_POST['quantity'], $_POST['comm'], $_POST['requester'])) {
        // تنظيف البيانات المدخلة
        $fail = trim($conn->real_escape_string($_POST['fail']));
        $date = trim($conn->real_escape_string($_POST['date']));
        $name = trim($conn->real_escape_string($_POST['name']));
        $part_number = trim($conn->real_escape_string($_POST['part_number']));
        $price2 = floatval($_POST['price2']);
        $price = floatval($_POST['price']);
        $quantity = intval($_POST['quantity']);
        $comm = trim($conn->real_escape_string($_POST['comm']));
        $requester = trim($conn->real_escape_string($_POST['requester']));

        // التحقق من أن الحقول ليست فارغة
        if (!empty($fail) && !empty($date) && !empty($name) && !empty($part_number) && $price2 > 0 && $price > 0 && $quantity > 0 && !empty($comm) && !empty($requester)) {
            // استخدام العبارات المجهزة لإدخال البيانات
            $stmt = $conn->prepare("INSERT INTO parts (fail, date, name, part_number, price2, price, quantity, comm, requester) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssdids", $fail, $date, $name, $part_number, $price2, $price, $quantity, $comm, $requester);

            if ($stmt->execute()) {
                $message = "تم إضافة القطعة بنجاح! (طالب القطعة: $requester)";
            } else {
                $message = "حدث خطأ أثناء الإضافة: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $message = "يرجى التأكد من ملء جميع الحقول بشكل صحيح.";
        }
    } else {
        $message = "بعض الحقول مفقودة.";
    }
}

// إغلاق الاتصال
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة قطع غيار</title>
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
        <h2>إضافة قطع غيار</h2>
        
        <!-- عرض رسالة النجاح أو الخطأ -->
        <?php if (!empty($message)): ?>
            <div class="message <?php echo (strpos($message, 'خطأ') !== false) ? 'error' : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="fail">اسم المورد:</label>
                <input type="text" id="fail" name="fail" required>
            </div>
            <div class="form-group">
                <label for="date">تاريخ الإدخال:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="name">اسم القطعة:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="part_number">رقم القطعة:</label>
                <input type="text" id="part_number" name="part_number" required>
            </div>
            <div class="form-group">
                <label for="price2">سعر القطعة الواحدة:</label>
                <input type="number" id="price2" name="price2" required step="0.01">
            </div>
            <div class="form-group">
                <label for="price">الإجمالي:</label>
                <input type="number" id="price" name="price" required step="0.01">
            </div>
            <div class="form-group">
                <label for="quantity">الكمية:</label>
                <input type="number" id="quantity" name="quantity" required>
            </div>
            <div class="form-group">
                <label for="comm">الملاحظات:</label>
                <input type="text" id="comm" name="comm" required>
            </div>
            <div class="form-group">
                <label for="requester">اسم طالب القطعة:</label>
                <input type="text" id="requester" name="requester" required>
            </div>
            <input type="submit" value="إضافة">
            <a href="alearabia_mg.html">رجوع</a>
        </form>
    </div>
</body>
</html>
