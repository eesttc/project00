<?php
session_start();
require_once '../includes/config.php'; // Sử dụng file config.php đã cung cấp

$errors = [];
$success = '';

// Kiểm tra xem $conn có phải là đối tượng PDO hợp lệ hay không
if (!$conn instanceof PDO) {
    die('Cannot connect to database. Please try again later.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validate dữ liệu
    if (empty($email)) {
        $email = null; // Đặt email thành null nếu không nhập
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email.';
    } else {
        // Kiểm tra email có trùng không
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Email has already been used.';
        }
    }

    if (empty($username)) {
        $errors[] = 'Username is required.';
    } else {
        // Kiểm tra username có trùng không
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Username has already been used.';
        }
    }

    if (empty($password)) {
        $errors[] = 'Password is required.';
    }

    if (empty($phone)) {
        $errors[] = 'Phone number is required.';
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors[] = 'Invalid phone number.';
    } else {
        // Kiểm tra phone có trùng không
        $stmt = $conn->prepare("SELECT id FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Phone number has already been used.';
        }
    }

    // Nếu không có lỗi, tiến hành lưu người dùng
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, phone, email, is_registered) VALUES (?, ?, ?, ?, 1)");
        $result = $stmt->execute([$username, $hashed_password, $phone, $email]);

        if ($result) {
            // Chuyển hướng đến trang đăng ký thành công
            header('Location: ../includes/sign-up-success.php');
            exit;
        } else {
            $errors[] = 'An error occurred during registration. Please try again.';
        }
    }
}
?>
<?php include("../includes/check.php"); ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php include ("../public/assets/css/styles.php"); ?>
</head>
<body class="bg-gray-100">
    <?php include ("../includes/header.php"); ?>
    
    <div class="container-sign-up mx-auto mt-8 max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Sign up</h2>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 text-red-700 p-4 mb-4 rounded">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 label-sign-up">Username<span class="text-red-500">*</span></label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                       class="mt-1 p-2 w-full border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 input-sign-up" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 label-sign-up">Password<span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" 
                       class="mt-1 p-2 w-full border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 input-sign-up" required>
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700 label-sign-up">Phone number<span class="text-red-500">*</span></label>
                <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" 
                       class="mt-1 p-2 w-full border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 input-sign-up" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 label-sign-up">Email (optional)</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                       class="mt-1 p-2 w-full border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 input-sign-up">
            </div>
            <div class="mb-4">
                <label class="label-sign-up"></label>
                <button type="submit" class="btn btn-primary submit-sign-up">Sign up</button>
            </div>
        </form>

        <p class="mt-4 text-center">
            Have an account? <a href="../includes/sign-in.php" class="text-blue-500 hover:underline">Sign in</a>
        </p>
    </div>

    <?php include ("../includes/footer.php"); ?>
</body>
</html>

<?php $conn = null; // Đóng kết nối PDO ?>