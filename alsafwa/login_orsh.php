<?php
session_start(); // بدء جلسة جديدة

// إعداد اتصال قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alsafwa_database";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// تحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // تحقق من أن الحقول ليست فارغة
    if (!empty($selected_username) && !empty($password)) {
        // استعلام عن اسم المستخدم
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $selected_username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            // تحقق من كلمة المرور
            if (password_verify($password, $hashed_password)) {
                $_SESSION['username'] = $selected_username; // تخزين اسم المستخدم في الجلسة
                header("Location: alearabia_mg.html"); // توجيه المستخدم
                exit();
            } else {
                $error_message = "اسم المستخدم أو كلمة المرور غير صحيحة.";
            }
        } else {
            $error_message = "اسم المستخدم أو كلمة المرور غير صحيحة.";
        }
        $stmt->close();
    } else {
        $error_message = "الرجاء إدخال اسم المستخدم وكلمة المرور.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="login.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 50px;
            text-align: center;
        }

        form {
            display: inline-block;
            max-width: 400px;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        select, input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <form method="POST" action="">
        <h2>تسجيل الدخول</h2>

        <!-- قائمة منسدلة لاختيار اسم المستخدم -->
        <label for="username">اختر اسم المستخدم:</label>
        <select name="username" id="username" required>
            <?php
            // جلب أسماء المستخدمين من قاعدة البيانات
            $conn = new mysqli($servername, $username, $password, $dbname);
            $sql = "SELECT username FROM users";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['username'] . "'>" . $row['username'] . "</option>";
                }
            } else {
                echo "<option value=''>لا توجد أسماء مستخدمين</option>";
            }
            ?>
        </select>

        <!-- حقل كلمة المرور -->
        <input type="password" id="password" name="password" required placeholder="كلمة المرور">

        <input type="submit" value="تسجيل الدخول">

        <?php if (!empty($error_message)): ?>
            <p class="error"><?= $error_message; ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
