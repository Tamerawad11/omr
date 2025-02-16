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
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// جلب بيانات القطع
$sql = "SELECT id, name, part_number, quantity FROM parts";
$result = $conn->query($sql);

// إغلاق الاتصال
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أخذ قطعة من المخزون</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        select, input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            display: block;
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 0.7rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            text-decoration: none;
            color: #4CAF50;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>أخذ قطعة من المخزون</h2>
        <form action="alearabia_take1.php" method="POST">
            <div class="form-group">
                <label for="part_id">اختر القطعة:</label>
                <select name="part_id" id="part_id" required>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" 
                                . $row['name'] . " (رقم: " . $row['part_number'] . " - متوفر: " . $row['quantity'] . ")</option>";
                        }
                    } else {
                        echo "<option value=''>لا توجد قطع متاحة</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">الكمية المأخوذة:</label>
                <input type="number" id="quantity" name="quantity" min="1" required>
            </div>
            <button type="submit">تأكيد</button>
        </form>
        <a href="alearabia_mg.html" class="back-link">رجوع</a>
    </div>
</body>
</html>
