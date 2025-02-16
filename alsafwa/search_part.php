<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alsafwa_database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "فشل الاتصال بقاعدة البيانات"]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = trim($_POST['search']);
    
    if (!empty($search)) {
        $stmt = $conn->prepare("SELECT id, name, part_number, quantity FROM parts WHERE name LIKE ? OR part_number LIKE ?");
        $searchParam = "%$search%";
        $stmt->bind_param("ss", $searchParam, $searchParam);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $part = $result->fetch_assoc();
            echo json_encode(["status" => "found", "id" => $part['id'], "name" => $part['name'], "part_number" => $part['part_number'], "quantity" => $part['quantity']]);
        } else {
            echo json_encode(["status" => "not_found"]);
        }
        
        $stmt->close();
    }
}

$conn->close();
?>
