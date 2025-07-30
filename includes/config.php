<?php
// Đảm bảo không có khoảng trắng hoặc ký tự trước thẻ <?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wireless_world";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES 'utf8mb4'");
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
?>