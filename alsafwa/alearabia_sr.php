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

$results = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // استخدام العبارات المجهزة لتحسين الأمان
    $stmt = $conn->prepare("SELECT * FROM parts WHERE part_number LIKE ? OR name LIKE ? OR fail LIKE ?");
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // إنشاء الجدول
        $results = "<h3>نتائج البحث:</h3>";
        $results .= "<table border='1' cellpadding='10' style='width: 100%; text-align: center;'><tr>
            <th>اسم المورد</th>
            <th>التاريخ</th>
            <th>اسم القطعة</th>
            <th>رقم القطعة</th>
            <th>السعر</th>
            <th>الكمية</th>
            <th>الملاحظات</th>
            <th>إجراءات</th>
        </tr>";
        while ($row = $result->fetch_assoc()) {
            $results .= "<tr>
                <td>{$row["fail"]}</td>
                <td>{$row["date"]}</td>
                <td>{$row["name"]}</td>
                <td>{$row["part_number"]}</td>
                <td>{$row["price"]}</td>
                <td>{$row["quantity"]}</td>
                <td>{$row["comm"]}</td>
                <td>
                    <form action='alearabin_delete.php' method='POST' style='display:inline;' onsubmit='return confirm(\"هل أنت متأكد من الحذف؟\")'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <input type='submit' value='حذف'>
                    </form>
                  
                </td>
            </tr>";
        }
        $results .= "</table>";
    } else {
        $results = "<p>لم يتم العثور على نتائج.</p>";
    }
    $stmt->close();
} else {
    $results = "<p>يرجى إدخال كلمة البحث.</p>";
}

// إغلاق الاتصال
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بحث قطع غيار</title>
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
            max-width: 800px;
            margin: 2rem auto;
            padding: 1.5rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            color: #333;
        }
        .form-group {
            margin: 1rem 0;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
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
        a {
            display: inline-block;
            margin-top: 1rem;
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 0.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>بحث قطع غيار</h2>
        <form action="alearabia_sr.php" method="GET">
            <div class="form-group">
                <label for="search">بحث برقم أو اسم القطعة أو اسم المورد:</label>
                <input type="text" id="search" name="search" required>
            </div>
            <input type="submit" value="بحث">
            <a href="alearabia_mg.html">رجوع</a>
        </form>

        <!-- عرض النتائج -->
        <div id="results">
            <?php echo $results; ?>
        </div>
    </div>
</body>
</html>
