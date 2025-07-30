<?php
// Đảm bảo không có khoảng trắng hoặc ký tự trước thẻ <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>