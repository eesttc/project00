<?php
// Đảm bảo không có khoảng trắng hoặc ký tự trước thẻ <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_unset();
session_destroy();
header("Location: ../public/index.php");
exit;
?>