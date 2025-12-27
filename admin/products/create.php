<?php
    session_start();

    if (!isset($_SESSION["id"])) {
        header("Location: ../../auth/login.php");
        exit;
    }

    require_once "../../auth/dbh.php";
    require_once "../../auth/functions.php";
    require_once "../functions.php";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $sku = $_POST['sku'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];

        $sql = "INSERT INTO products (name, sku, description, price, stock) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssdi", $name, $sku, $description, $price, $stock);
            mysqli_stmt_execute($stmt);

            $product_id = mysqli_insert_id($conn);

            mysqli_stmt_close($stmt);
            header("Location: detail.php?id=" . $product_id);
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New product - OMS</title>
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
                    <a href="../../admin/products/index.php" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <nav class="mb-6 flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm">
                        <li><a href="../../admin/products/index.php" class="text-gray-500 hover:text-gray-300">Products</a></li>
                        <li><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
                        <li><span class="text-white font-medium">New product</span></li>
                    </ol>
                </nav>

                <div class="max-w-2xl mx-auto">
                    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                        <h1 class="text-2xl font-semibold text-white mb-6">New product</h1>

                        <form method="POST" class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-300 mb-1.5">Product name</label>
                                <input type="text" name="name" id="name" required class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Wireless Keyboard Nordic">
                            </div>

                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-300 mb-1.5">SKU</label>
                                <input type="text" name="sku" id="sku" required class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="KB-WL-001">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-300 mb-1.5">Price (SEK)</label>
                                    <input type="number" name="price" id="price" step="0.01" required class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="799">
                                </div>
                                <div>
                                    <label for="stock" class="block text-sm font-medium text-gray-300 mb-1.5">Stock quantity</label>
                                    <input type="number" name="stock" id="stock" required class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="50">
                                </div>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-300 mb-1.5">Description</label>
                                <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Product description..."></textarea>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 justify-end pt-6 border-t border-gray-700">
                                <a href="../../admin/products/index.php" class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-900 border border-gray-600 rounded-lg text-center">Cancel</a>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">Save</button>
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