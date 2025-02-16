<?php
// إعداد اتصال قاعدة البيانات
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

// إعداد المتغيرات للبحث
$search_query = "";
if (isset($_POST['search']) && !empty($_POST['search'])) {
    $search_query = $conn->real_escape_string($_POST['search']);
}

// جلب العمليات من جدول transactions مع معلومات إضافية بما في ذلك رقم القطعة
$sql = "SELECT t.id, p.name AS part_name, p.part_number, t.person_name, t.quantity_taken, 
               t.transaction_date, t.car_number, t.location 
        FROM transactions t 
        JOIN parts p ON t.part_id = p.id ";

if ($search_query != "") {
    $sql .= "WHERE t.person_name LIKE '%$search_query%' 
             OR t.car_number LIKE '%$search_query%' 
             OR t.location LIKE '%$search_query%' ";
}

$sql .= "ORDER BY t.transaction_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف المصروفات الورشة</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            text-align: center;
            padding: 20px;
        }
        
        h2 {
            color: #333;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #007BFF;
            color: #fff;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            width: 30%;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button {
            padding: 10px;
            font-size: 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background-color: #0056b3;
        }

        @media print {
            body {
                font-size: 14px;
                text-align: left;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th, td {
                padding: 8px;
                border: 1px solid #ddd;
            }

            button {
                display: none;
            }
        }
    </style>
</head>
<body>

    <h2>كشف المصروفات الورشة</h2>
    
    <!-- نموذج البحث -->
    <form method="POST" action="">
        <label for="search">البحث حسب المستلم، رقم السيارة أو الموقع:</label>
        <input type="text" id="search" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="أدخل اسم أو رقم سيارة أو موقع">
        <button type="submit">بحث</button>
    </form>

    <table>
        <tr>
            <th>#</th>
            <th>اسم القطعة</th>
            <th>رقم القطعة</th>
            <th>المستلم</th>
            <th>رقم السيارة</th>
            <th>الموقع</th>
            <th>الكمية المصروفة</th>
            <th>التاريخ</th>
        </tr>
        
        <?php
        $counter = 1;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$counter}</td>
                        <td>{$row['part_name']}</td>
                        <td>{$row['part_number']}</td>
                        <td>{$row['person_name']}</td>
                        <td>{$row['car_number']}</td>
                        <td>{$row['location']}</td>
                        <td>{$row['quantity_taken']}</td>
                        <td>{$row['transaction_date']}</td>
                      </tr>";
                $counter++;
            }
        } else {
            echo "<tr><td colspan='8'>لا توجد عمليات مسجلة</td></tr>";
        }
        ?>
    </table>

    <button onclick="window.history.back()">رجوع</button>
    <button onclick="window.print()">طباعة الفاتورة</button>

</body>
</html>

<?php
$conn->close();
?>
