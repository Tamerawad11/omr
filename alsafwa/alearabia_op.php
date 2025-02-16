<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alsafwa_database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>البحث عن القطع اخذ الكمية</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f9f9f9;
        }
        .container {
            margin: 50px auto;
            width: 50%;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        button {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        #quantitySection {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>البحث عن قطعة</h2>
        <input type="text" id="search" placeholder="أدخل اسم القطعة أو رقمها">
        <button id="searchBtn">بحث</button>

        <div id="result"></div>

        <div id="quantitySection">
            <h3>اخذ كمية</h3>
            <input type="number" id="quantity" min="1" placeholder="أدخل الكمية">
            <input type="text" id="person_name" min="1" placeholder="اسم المستلم ">
            <button id="addQuantityBtn">اخذ الكمية</button>
        </div>

 
    </div>

    <script>
        $(document).ready(function(){
            var selectedPartId = null;

            // البحث عن القطعة
            $("#searchBtn").click(function(){
                var searchQuery = $("#search").val().trim();

                if (searchQuery !== "") {
                    $.ajax({
                        url: "search_part.php",
                        type: "POST",
                        data: { search: searchQuery },
                        success: function(response) {
                            var data = JSON.parse(response);
                            if (data.status === "found") {
                                $("#result").html(`<p>القطعة: ${data.name} | رقم القطعة: ${data.part_number} | الكمية المتوفرة: ${data.quantity}</p>`);
                                selectedPartId = data.id;
                                $("#quantitySection").show();
                            } else {
                                $("#result").html("<p style='color:red;'>لم يتم العثور على القطعة.</p>");
                                $("#quantitySection").hide();
                            }
                        }
                    });
                } else {
                    alert("يرجى إدخال اسم القطعة أو رقمها للبحث.");
                }
            });

            // اخذ الكمية
            $("#addQuantityBtn").click(function(){
                var quantity = $("#quantity").val();

                if (selectedPartId && quantity > 0) {
                    $.ajax({
                        url: "op_ex.php",
                        type: "POST",
                        data: { part_id: selectedPartId, quantity: quantity },
                        success: function(response) {
                            $("#result").append(response);
                            $("#quantitySection").hide();
                            $("#search").val("");
                            $("#quantity").val("");
                        }
                    });
                } else {
                    alert("يرجى إدخال كمية صحيحة.");
                }
            });
        });
    </script>
</body>
</html>
