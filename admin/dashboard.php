<?php
    session_start();

    if (!isset($_SESSION["id"])) {
        header("Location: ../auth/login.php");
        exit;
    }

    require_once "../auth/dbh.php";
    require_once "../auth/functions.php";
    require_once "./functions.php";

    $orders = getOrders($conn, 5);
    $productsWithLowStock = getLowStockProducts($conn, 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - OMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900">
    <header class="bg-gray-800 border-b border-gray-700 sticky top-0 z-50">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="../admin/dashboard.php" class="flex items-center gap-2">
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
                        <a href="../admin/settings/index.php" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-200 hover:bg-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Settings
                        </a>
                        <a href="../auth/logout.php" class="flex items-center gap-3 px-4 py-2 text-sm text-red-400 hover:bg-red-900/20">
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
                    <a href="../admin/dashboard.php" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="../admin/orders/index.php" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Orders
                    </a>
                    <a href="../admin/products/index.php" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Products
                    </a>
                    <a href="../admin/customers/index.php" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 rounded-lg">
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
                <div class="mb-8">
                    <h1 class="text-2xl font-semibold text-white">Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-400">Overview of your order management system</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-gray-400 mb-1">Orders</h3>
                        <p class="text-3xl font-semibold text-white"><?= getOrdersCount($conn) ?></p>
                    </div>

                    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-gray-400 mb-1">Revenue</h3>
                        <p class="text-3xl font-semibold text-white"><?= number_format(getTotalOrderAmount($conn), 2) ?> SEK</p>
                    </div>

                    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-gray-400 mb-1">Low stock items</h3>
                        <p class="text-3xl font-semibold text-white"><?= countLowStockProducts($conn) ?></p>
                    </div>

                    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-gray-400 mb-1">Customers</h3>
                        <p class="text-3xl font-semibold text-white"><?= getCustomerCount($conn) ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-gray-800 rounded-lg border border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-700">
                            <h2 class="text-lg font-semibold text-white">Recent orders</h2>
                        </div>
                        <?php if (!empty($orders)): ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-700">
                                    <thead class="bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Order</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                                        <?php foreach ($orders as $order): ?>
                                            <tr class="hover:bg-gray-700">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="../admin/orders/detail.php?id=<?= $order['id'] ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">#<?= $order["order_number"] ?></a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white"><?= getCustomerByID($conn, $order["customer_id"])["name"] ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-900/30 text-emerald-400 capitalize"><?= $order["status"] ?></span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white text-right"><?= number_format($order["total_amount"], 2) ?> SEK</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-6 py-4 border-t border-gray-700">
                                <a href="../admin/orders/index.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View all orders →</a>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-white/40 py-12">There is no order data!</p>
                        <?php endif; ?>
                    </div>

                    <div class="bg-gray-800 rounded-lg border border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-700">
                            <h2 class="text-lg font-semibold text-white">Low stock products</h2>
                        </div>
                        <?php if (!empty($productsWithLowStock)): ?>
                            <div class="divide-y divide-gray-700">
                                <?php foreach ($productsWithLowStock as $products): ?>
                                    <div class="px-6 py-4 hover:bg-gray-700">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-white"><?= $products["name"] ?></p>
                                                <p class="text-xs text-gray-500 mt-0.5">SKU: <?= $products["sku"] ?></p>
                                            </div>
                                            <div class="flex items-center gap-3 ml-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900/30 text-red-400">Low stock</span>
                                                <span class="text-sm font-medium text-white"><?= $products["stock"] ?> units</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="px-6 py-4 border-t border-gray-700">
                                <a href="../admin/products/index.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View products →</a>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-white/40 py-12">There is no data!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../public/assets/js/main.js"></script>
</body>
</html>