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

// استعلام SQL لاسترجاع جميع قطع الغيار
$query = "SELECT * FROM parts";
$result = $conn->query($query);

// التحقق من وجود نتائج
if ($result === false) {
    die("فشل الاستعلام: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض قطع الغيار</title>
    <link rel="stylesheet" href="styles_stelam.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        button, a {
            display: inline-block;
            margin: 5px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover, a:hover {
            background-color: #45a049;
        }
    </style>
    <script>
        function printPage() {
            window.print();
        }

        function confirmDelete(partId) {
            if (confirm("هل أنت متأكد من حذف هذه القطعة؟")) {
                window.location.href = 'alearabin_delete.php?id=' + partId;
            }
        }
    </script>
</head>
<body>
    <h2>جميع قطع الغيار</h2>

    <!-- التحقق من وجود قطع غيار لعرضها -->
    <?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>اسم المورد</th>
                <th>التاريخ</th>
                <th>اسم القطعة</th>
                <th>رقم القطعة</th>
                <th>الكمية</th>
                <th>سعر القطعة الواحدة</th>
                <th>الإجمالي</th>
                <th>الملاحظات</th>
                <th>طالب القطعة</th> <!-- تمت الإضافة هنا -->
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['fail']); ?></td>
                <td><?= htmlspecialchars($row['date']); ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['part_number']); ?></td>
                <td><?= htmlspecialchars($row['quantity']); ?></td>
                <td><?= htmlspecialchars($row['price2']); ?></td>
                <td><?= htmlspecialchars($row['price']); ?></td>
                <td><?= htmlspecialchars($row['comm']); ?></td>
                <td><?= htmlspecialchars($row['requester']); ?></td> <!-- تمت الإضافة هنا -->
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>لا توجد قطع غيار مدخلة.</p>
    <?php endif; ?>

    <button onclick="printPage()">طباعة</button>
    <a href="alearabia_mg.html">رجوع إلى لوحة التحكم</a>
</body>
</html>
