<?php
    session_start();

    if (!isset($_SESSION["id"])) {
        header("Location: ../../auth/login.php");
        exit;
    }

    require_once "../../auth/dbh.php";
    require_once "../../auth/functions.php";

    $success_message = "";
    $error_message = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $user_id = $_SESSION['id'];

        if (empty($name) || empty($email)) {
            $error_message = "Name and email cannot be empty.";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Invalid email format.";
        } else {
            $checkEmail = emailExists($conn, $email);
            if ($checkEmail && $checkEmail['id'] != $user_id) {
                $error_message = "Email already in use by another account.";
            } else {
                $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
                $stmt = mysqli_stmt_init($conn);

                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $user_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    $_SESSION['name'] = $name;
                    $_SESSION['email'] = $email;

                    $success_message = "Profile updated successfully.";
                } else {
                    $error_message = "Failed to update profile.";
                }
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $user_id = $_SESSION['id'];


        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_message = "All password fields are required.";
        } else if ($new_password !== $confirm_password) {
            $error_message = "New passwords do not match.";
        } else {
            $sql = "SELECT password FROM users WHERE id = ?";
            $stmt = mysqli_stmt_init($conn);

            if (mysqli_stmt_prepare($stmt, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);

                if (password_verify($current_password, $user['password'])) {
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                    $sql = "UPDATE users SET password = ? WHERE id = ?";
                    $stmt = mysqli_stmt_init($conn);

                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);

                        $success_message = "Password updated successfully.";
                    } else {
                        $error_message = "Failed to update password.";
                    }
                } else {
                    $error_message = "Current password is incorrect.";
                }
            } else {
                $error_message = "Failed to verify current password.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - OMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900">
    <header class="bg-gray-800 border-b border-gray-700 sticky top-0 z-50">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="../../admin/dashboard.php" class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">O</span>
                        </div>
                        <span class="text-xl font-semibold text-white hidden sm:block">OMS</span>
                    </a>
                </div>
                <div class="relative">
                    <button class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-700">
                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-indigo-600">AD</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="hidden absolute right-0 mt-2 w-48 bg-gray-700 rounded-lg shadow-2xl border border-gray-600 py-1 z-50">
                        <div class="px-4 py-2 border-b border-gray-600">
                            <p class="text-sm font-medium text-white"><?= $_SESSION["name"] ?></p>
                            <p class="text-xs text-gray-400 mt-0.5"><?= $_SESSION["email"] ?></p>
                        </div>
                        <a href="../../admin/settings/index.php" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-200 hover:bg-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Settings
                        </a>
                        <a href="../../auth/logout.php" class="flex items-center gap-3 px-4 py-2 text-sm text-red-400 hover:bg-red-900/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex min-h-screen">
        <aside class="hidden lg:flex lg:flex-col w-64 bg-gray-800 border-r border-gray-700 flex-shrink-0">
            <div class="flex-1 flex flex-col py-6">
                <nav class="flex-1 px-4 space-y-1">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Administration</h3>
                    <a href="../../admin/dashboard.php" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-900 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="../../admin/orders/index.php" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-900 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Orders
                    </a>
                    <a href="../../admin/products/index.php" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-900 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Products
                    </a>
                    <a href="../../admin/customers/index.php" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-900 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Customers
                    </a>
                </nav>
            </div>
        </aside>

        <main class="flex-1">
            <div class="px-4 sm:px-6 lg:px-8 py-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-white">Settings</h1>
                </div>

                <?php if ($success_message): ?>
                    <div class="max-w-4xl mb-6 bg-green-900/20 border border-green-700 rounded-lg p-4">
                        <p class="text-sm text-green-400"><?= htmlspecialchars($success_message) ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="max-w-4xl mb-6 bg-red-900/20 border border-red-700 rounded-lg p-4">
                        <p class="text-sm text-red-400"><?= htmlspecialchars($error_message) ?></p>
                    </div>
                <?php endif; ?>

                <div class="max-w-4xl space-y-6">
                    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                        <h2 class="text-lg font-semibold text-white mb-6">Profile Information</h2>

                        <form method="POST" class="space-y-6">
                            <div>
                                <label for="profile-name" class="block text-sm font-medium text-gray-300 mb-1.5">Name</label>
                                <input type="text" name="name" id="profile-name" value="<?= htmlspecialchars($_SESSION["name"]) ?>" required class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="profile-email" class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                                <input type="email" name="email" id="profile-email" value="<?= htmlspecialchars($_SESSION["email"]) ?>" required class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-700">
                                <button type="submit" name="update_profile" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">Save changes</button>
                            </div>
                        </form>
                    </div>

                    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                        <h2 class="text-lg font-semibold text-white mb-6">Change Password</h2>

                        <form method="POST" class="space-y-6">
                            <div>
                                <label for="current-password" class="block text-sm font-medium text-gray-300 mb-1.5">Current password</label>
                                <input type="password" name="current_password" id="current-password" required class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="new-password" class="block text-sm font-medium text-gray-300 mb-1.5">New password</label>
                                <input type="password" name="new_password" id="new-password" required class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="confirm-password" class="block text-sm font-medium text-gray-300 mb-1.5">Confirm password</label>
                                <input type="password" name="confirm_password" id="confirm-password" required class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-700">
                                <button type="submit" name="update_password" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">Update password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../../public/assets/js/main.js"></script>
</body>
</html>