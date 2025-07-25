<?php
// Đảm bảo không có khoảng trắng hoặc ký tự trước thẻ <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/config.php';

$errors = [];

// Kiểm tra xem $conn có phải là đối tượng PDO hợp lệ hay không
if (!$conn instanceof PDO) {
    die('Cannot connect to database. Please try again later.');
}

// Định nghĩa BASE_URL
define('BASE_URL', 'http://localhost:8080/project00/');

// Xử lý form đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate dữ liệu
    if (empty($username)) {
        $errors[] = 'Username is required.';
    }
    if (empty($password)) {
        $errors[] = 'Password is required.';
    }

    // Nếu không có lỗi validate, kiểm tra thông tin đăng nhập
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Lưu thông tin người dùng vào session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Chuyển hướng dựa trên vai trò
                if ($user['role'] === 'admin') {
                    $redirect_url = BASE_URL . 'includes/dashboard.php';
                } else {
                    $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . 'public/index.php';
                    // Tránh chuyển hướng lại về sign-in.php hoặc logout.php
                    if (strpos($redirect_url, 'sign-in.php') !== false || strpos($redirect_url, 'logout.php') !== false) {
                        $redirect_url = BASE_URL . 'public/index.php';
                    }
                }
                header("Location: $redirect_url");
                exit;
            } else {
                $errors[] = 'Incorrect username or password.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Query error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php include("../public/assets/css/styles.php"); ?>
</head>
<body class="bg-gray-100">
    <?php include("../includes/header.php"); ?>
    
    <div class="container-sign-up mx-auto mt-8 max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Sign in</h2>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 text-red-700 p-4 mb-4 rounded">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="#">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 label-sign-up">Username <span class="text-red-500">*</span></label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                       class="mt-1 p-2 w-full border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 input-sign-up" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 label-sign-up">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" 
                       class="mt-1 p-2 w-full border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 input-sign-up" required>
            </div>
            <div class="mb-4">
                <label class="label-sign-up"></label>
                <button type="submit" class="btn btn-primary submit-sign-up">Sign in</button>
            </div>
        </form>

        <p class="mt-4 text-center">
            Don't have an account? <a href="../includes/sign-up.php" class="text-blue-500 hover:underline">Sign up</a>
        </p>
    </div>

    <?php include("../includes/footer.php"); ?>
    <?php $conn = null; ?>
</body>
</html>