<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #e5e7eb;
            color: #374151;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        tr:hover {
            background-color: #f1f5f9;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            width: 100%;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php
    define('BASE_URL', 'http://localhost:8080/project00/');
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: ' . BASE_URL . 'public/index.php');
        exit;
    }
    require_once '../includes/config.php';

    $message = '';
    try {
        // Xử lý tạo hóa đơn
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_bill'])) {
            $order_id = (int)$_POST['order_id'];
            $stmt_total = $conn->prepare("
                SELECT SUM(od.quantity * p.price) AS total_price
                FROM orderdetail od
                JOIN products p ON od.productid = p.id
                WHERE od.orderid = ?
            ");
            $stmt_total->execute([$order_id]);
            $total_price = $stmt_total->fetch(PDO::FETCH_ASSOC)['total_price'];

            if ($total_price !== null) {
                $stmt_bill = $conn->prepare("INSERT INTO bill (orderid, totalprice) VALUES (?, ?)");
                $stmt_bill->execute([$order_id, $total_price]);
                $message = '<p class="success">Create bill success!</p>';
            } else {
                $message = '<p class="error">Cannot create bill!</p>';
            }
        }

        // Xử lý cập nhật trạng thái đơn hàng (Hoàn thành)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_order'])) {
            $order_id = (int)$_POST['order_id'];
            $stmt_update = $conn->prepare("UPDATE `order` SET status = 'done' WHERE id = ?");
            $stmt_update->execute([$order_id]);
            $message = '<p class="success">Updated!</p>';
        }

        // Xử lý hủy đơn hàng
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
            $order_id = (int)$_POST['order_id'];
            $stmt_update = $conn->prepare("UPDATE `order` SET status = 'canceled' WHERE id = ?");
            $stmt_update->execute([$order_id]);
            $message = '<p class="success">Canceled order!</p>';
        }

        // Xử lý xóa tài khoản người dùng
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
            $user_id = (int)$_POST['user_id'];
            $stmt_delete = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt_delete->execute([$user_id]);
            $message = '<p class="success">Removed user!</p>';
        }

        // Xử lý thêm tài khoản người dùng
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
            $username = trim($_POST['username']);
            $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $role = in_array($_POST['role'], ['user', 'admin']) ? $_POST['role'] : 'user';

            $email = empty($email) ? null : $email;

            if (empty($username) || empty($password) || empty($phone)) {
                $message = '<p class="error">Please fill required information!</p>';
            } else {
                $stmt_check_username = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
                $stmt_check_username->execute([$username]);
                $username_exists = $stmt_check_username->fetchColumn();

                $stmt_check_phone = $conn->prepare("SELECT COUNT(*) FROM users WHERE phone = ?");
                $stmt_check_phone->execute([$phone]);
                $phone_exists = $stmt_check_phone->fetchColumn();

                $email_exists = false;
                if ($email !== null) {
                    $stmt_check_email = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
                    $stmt_check_email->execute([$email]);
                    $email_exists = $stmt_check_email->fetchColumn();
                }

                if ($username_exists) {
                    $message = '<p class="error">Username exists!</p>';
                } elseif ($phone_exists) {
                    $message = '<p class="error">Phone number exists!</p>';
                } elseif ($email_exists) {
                    $message = '<p class="error">Email exists!</p>';
                } else {
                    try {
                        $stmt_add_user = $conn->prepare("
                            INSERT INTO users (username, password, email, phone, is_registered, role)
                            VALUES (?, ?, ?, ?, 1, ?)
                        ");
                        $stmt_add_user->execute([$username, $password, $email, $phone, $role]);
                        $message = '<p class="success">Create account success!</p>';
                    } catch (PDOException $e) {
                        $message = '<p class="error">Cannot create account: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    }
                }
            }
        }

        // Lấy số liệu thống kê
        $stmt_products = $conn->query("SELECT COUNT(*) AS products FROM products");
        $products_count = $stmt_products->fetch(PDO::FETCH_ASSOC)['products'];

        $stmt_orders = $conn->query("SELECT COUNT(*) AS orders FROM `order`");
        $orders_count = $stmt_orders->fetch(PDO::FETCH_ASSOC)['orders'];

        // Debug: Kiểm tra dữ liệu đơn hàng
        $stmt_check_orders = $conn->query("SELECT id, userid, dateoforder, status FROM `order`");
        $debug_orders = $stmt_check_orders->fetchAll(PDO::FETCH_ASSOC);
        if (empty($debug_orders)) {
            $message = '<p class="error">Cannot find the order!</p>';
        }

        // Lấy danh sách đơn hàng gần đây (chưa hoàn thành)
        $stmt_orders_recent = $conn->prepare("
            SELECT o.id, o.userid, o.dateoforder, o.status, b.totalprice
            FROM `order` o
            LEFT JOIN bill b ON o.id = b.orderid
            WHERE o.status = 'pending'
            ORDER BY o.dateoforder DESC
            LIMIT 5
        ");
        $stmt_orders_recent->execute();
        $recent_orders = $stmt_orders_recent->fetchAll(PDO::FETCH_ASSOC);

        // Lấy toàn bộ đơn hàng
        $stmt_all_orders = $conn->prepare("
            SELECT o.id, o.userid, o.dateoforder, o.status, b.totalprice
            FROM `order` o
            LEFT JOIN bill b ON o.id = b.orderid
            ORDER BY o.dateoforder DESC
        ");
        $stmt_all_orders->execute();
        $all_orders = $stmt_all_orders->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách người dùng
        $stmt_users = $conn->query("SELECT id, username, email, phone, role FROM users");
        $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

        // Lấy chi tiết đơn hàng nếu có yêu cầu
        $order_details = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_details'])) {
            $order_id = (int)$_POST['order_id'];
            $stmt_details = $conn->prepare("
                SELECT p.name, od.quantity, p.price
                FROM orderdetail od
                JOIN products p ON od.productid = p.id
                WHERE od.orderid = ?
            ");
            $stmt_details->execute([$order_id]);
            $order_details = $stmt_details->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch(PDOException $e) {
        echo "<div class='container text-red-600'>Oops!: " . htmlspecialchars($e->getMessage()) . "</div>";
        exit;
    }
    ?>

    <!-- Header -->
    <header class="bg-blue-600 text-black p-4 shadow-md">
        <h1 class="text-2xl font-bold">Dashboard</h1>
    </header>

    <!-- Main Content -->
    <div class="container">
        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-700">Products</h2>
                <p class="text-3xl font-bold text-blue-600"><?php echo $products_count; ?></p>
            </div>
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-700">Orders</h2>
                <p class="text-3xl font-bold text-blue-600"><?php echo $orders_count; ?></p>
            </div>
        </div>

        <!-- Product Management Link -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Product manage</h2>
            <a href="../includes/dashboard-product.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Đi đến Quản Lý Sản Phẩm</a>
        </div>

        <!-- Recent Orders Table -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Recent Orders (Not Completed)</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order id</th>
                            <th>User id</th>
                            <th>Order date</th>
                            <th>Total amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_orders)): ?>
                            <tr><td colspan="6">No incomplete orders.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['userid']); ?></td>
                                    <td><?php echo htmlspecialchars($order['dateoforder']); ?></td>
                                    <td><?php echo $order['totalprice'] ? number_format($order['totalprice'], 2) : 'Chưa có hóa đơn'; ?> USD</td>
                                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                                    <td>
                                        <?php if (!$order['totalprice']): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="show_details" value="1">
                                                <button type="submit" class="btn btn-primary submit-sign-up">Create bill</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="complete_order" value="1">
                                                <button type="submit" class="btn btn-primary submit-sign-up">Complete</button>
                                            </form>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="cancel_order" value="1">
                                                <button type="submit" class="btn btn-primary submit-sign-up">Cancel</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Order Details Modal (hiển thị nếu có show_details) -->
        <?php if (!empty($order_details)): ?>
            <div class="modal" style="display: flex;">
                <div class="modal-content">
                    <h2 class="text-lg font-semibold mb-4">Bill Detail</h2>
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th>Products</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            foreach ($order_details as $item):
                                $subtotal = $item['quantity'] * $item['price'];
                                $total += $subtotal;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td><?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo number_format($subtotal, 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right font-bold">Final amount:</td>
                                <td><?php echo number_format($total, 2); ?> USD</td>
                            </tr>
                        </tfoot>
                    </table>
                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $_POST['order_id']; ?>">
                        <input type="hidden" name="create_bill" value="1">
                        <button type="submit" class="btn btn-primary submit-sign-up">Create</button>
                        <a href="dashboard.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mt-4 ml-2">Close</a>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- All Orders Table -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">All orders</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order id</th>
                            <th>User id</th>
                            <th>Order date</th>
                            <th>Final amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($all_orders)): ?>
                            <tr><td colspan="6">No orders.</td></tr>
                        <?php else: ?>
                            <?php foreach ($all_orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['userid']); ?></td>
                                    <td><?php echo htmlspecialchars($order['dateoforder']); ?></td>
                                    <td><?php echo $order['totalprice'] ? number_format($order['totalprice'], 2) : 'Invoice not ready'; ?> USD</td>
                                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                                    <td>
                                        <?php if (!$order['totalprice']): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="show_details" value="1">
                                                <button type="submit" class="btn btn-primary submit-sign-up">Create bill</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="complete_order" value="1">
                                                <button type="submit" class="btn btn-primary submit-sign-up">Complete</button>
                                            </form>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="cancel_order" value="1">
                                                <button type="submit" class="btn btn-primary submit-sign-up">Cancel</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Users</h2>
            <div class="mb-4">
                <h3 class="text-md font-semibold mb-2">Add user</h3>
                <form method="POST">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input type="text" name="username" placeholder="Username" class="border p-2 rounded" required>
                        <input type="password" name="password" placeholder="Password" class="border p-2 rounded" required>
                        <input type="email" name="email" placeholder="Email (optional)" class="border p-2 rounded">
                        <input type="text" name="phone" placeholder="Phone number" class="border p-2 rounded" required>
                        <select name="role" class="border p-2 rounded" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" name="add_user" class="btn btn-primary submit-sign-up">Add</button>
                </form>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone number</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email'] ?: 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this account?');">
                                        <input class="mt-1 p-2 w-full border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 input-sign-up" type="hidden" name="user_id" value="<?php echo $user['id']; ?> ">
                                        <input type="hidden" name="delete_user" value="1" class="mt-1 p-2 w-full border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 input-sign-up">
                                        <button type="submit" class="btn btn-primary submit-sign-up">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>