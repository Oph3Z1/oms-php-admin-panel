<?php
    session_start();

    if (!isset($_SESSION["id"])) {
        header("Location: ../../auth/login.php");
        exit;
    }

    require_once "../../auth/dbh.php";
    require_once "../../auth/functions.php";
    require_once "../functions.php";

    $customers = getCustomers($conn);
    $products = getProducts($conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $customer_id = $_POST['customer_id'];
        $order_date = date('Y-m-d');
        $status = 'pending';

        $total_amount = 0;
        $order_items = json_decode($_POST['order_items'], true);

        foreach ($order_items as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }

        $order_number = 'ORD-' . date('Y') . '-' . str_pad(getOrdersCount($conn) + 1, 3, '0', STR_PAD_LEFT);

        $sql = "INSERT INTO orders (order_number, customer_id, status, total_amount, order_date) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "sisds", $order_number, $customer_id, $status, $total_amount, $order_date);
            mysqli_stmt_execute($stmt);

            $order_id = mysqli_insert_id($conn);

            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $item_stmt = mysqli_stmt_init($conn);

            if (mysqli_stmt_prepare($item_stmt, $item_sql)) {
                foreach ($order_items as $item) {
                    mysqli_stmt_bind_param($item_stmt, "iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                    mysqli_stmt_execute($item_stmt);
                }
                mysqli_stmt_close($item_stmt);
            }

            mysqli_stmt_close($stmt);

            header("Location: detail.php?id=" . $order_id);
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New order - OMS</title>
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
                    <a href="../../admin/orders/index.php" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <nav class="mb-6 flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm">
                        <li>
                            <a href="../../admin/orders/index.php" class="text-gray-500 hover:text-gray-300">Orders</a>
                        </li>
                        <li>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </li>
                        <li>
                            <span class="text-white font-medium">New order</span>
                        </li>
                    </ol>
                </nav>

                <form id="orderForm" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <input type="hidden" name="order_items" id="orderItemsInput">

                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                            <h2 class="text-lg font-semibold text-white mb-4">Customer</h2>
                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-gray-300 mb-1.5">Select customer</label>
                                <select name="customer_id" id="customer_id" required class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    <option value="">Choose a customer...</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?= $customer['id'] ?>"><?= $customer['name'] ?> (<?= $customer['email'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold text-white">Order items</h2>
                                <button type="button" id="addItemBtn" class="px-3 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-500 border border-indigo-600 rounded-lg">
                                    Add product
                                </button>
                            </div>

                            <div id="orderItems" class="space-y-4"></div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 justify-end">
                            <a href="../../admin/orders/index.php" class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-900 border border-gray-600 rounded-lg text-center">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">
                                Create order
                            </button>
                        </div>
                    </div>
                    
                    <div class="lg:col-span-1">
                        <div class="lg:sticky lg:top-24">
                            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                                <h2 class="text-lg font-semibold text-white mb-4">Order summary</h2>
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm pb-3 border-b border-gray-700">
                                        <span class="text-gray-400">Total</span>
                                        <span id="totalAmount" class="text-white">0.00 SEK</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="../../public/assets/js/main.js"></script>
    <script>
        const products = <?= json_encode($products) ?>;

        let orderItems = [];
        let itemIdCounter = 0;

        document.getElementById('addItemBtn').addEventListener('click', function() {
            addOrderItem();
        });

        function addOrderItem(productId = null, quantity = 1) {
            const itemId = itemIdCounter++;
            const item = {
                id: itemId,
                product_id: productId,
                quantity: quantity,
                price: 0
            };

            orderItems.push(item);
            renderOrderItems();
        }

        function removeOrderItem(itemId) {
            orderItems = orderItems.filter(item => item.id !== itemId);
            renderOrderItems();
        }

        function updateOrderItem(itemId, field, value) {
            const item = orderItems.find(item => item.id === itemId);
            if (item) {
                if (field === 'product_id') {
                    item.product_id = parseInt(value);
                    const product = products.find(p => p.id == value);
                    if (product) {
                        item.price = parseFloat(product.price);
                    }
                } else if (field === 'quantity') {
                    item.quantity = parseInt(value) || 1;
                }
                renderOrderItems();
            }
        }

        function renderOrderItems() {
            const container = document.getElementById('orderItems');
            container.innerHTML = '';

            if (orderItems.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">No products added yet. Click "Add product" to start.</p>';
                updateTotal();
                return;
            }

            orderItems.forEach(item => {
                const product = products.find(p => p.id == item.product_id);
                const subtotal = item.quantity * item.price;

                const itemDiv = document.createElement('div');
                itemDiv.className = 'flex items-start gap-4 p-4 border border-gray-700 rounded-lg';
                itemDiv.innerHTML = `
                    <div class="flex-1 min-w-0">
                        <select onchange="updateOrderItem(${item.id}, 'product_id', this.value)" class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent mb-2">
                            <option value="">Select product...</option>
                            ${products.map(p => `
                                <option value="${p.id}" ${p.id == item.product_id ? 'selected' : ''}>
                                    ${p.name} - ${parseFloat(p.price).toFixed(2)} SEK
                                </option>
                            `).join('')}
                        </select>
                        ${product ? `<p class="text-xs text-gray-500">SKU: ${product.sku}</p>` : ''}
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="updateOrderItem(${item.id}, 'quantity', ${item.quantity - 1})" class="p-1 text-gray-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </button>
                        <input type="number" value="${item.quantity}" onchange="updateOrderItem(${item.id}, 'quantity', this.value)" class="w-16 px-2 py-1 text-center bg-gray-700 text-white border border-gray-600 rounded-lg" min="1">
                        <button type="button" onclick="updateOrderItem(${item.id}, 'quantity', ${item.quantity + 1})" class="p-1 text-gray-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="text-right min-w-[100px]">
                        <p class="text-sm text-gray-400">${item.price.toFixed(2)} SEK</p>
                        <p class="text-sm font-medium text-white">${subtotal.toFixed(2)} SEK</p>
                    </div>
                    <button type="button" onclick="removeOrderItem(${item.id})" class="p-1 text-gray-400 hover:text-red-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                `;
                container.appendChild(itemDiv);
            });

            updateTotal();
        }

        function updateTotal() {
            const total = orderItems.reduce((sum, item) => sum + (item.quantity * item.price), 0);
            document.getElementById('totalAmount').textContent = total.toFixed(2) + ' SEK';
        }

        document.getElementById('orderForm').addEventListener('submit', function(e) {
            if (orderItems.length === 0) {
                e.preventDefault();
                alert('Please add at least one product to the order.');
                return;
            }

            const hasEmptyProducts = orderItems.some(item => !item.product_id);
            if (hasEmptyProducts) {
                e.preventDefault();
                alert('Please select a product for all items.');
                return;
            }

            const itemsData = orderItems.map(item => ({
                product_id: item.product_id,
                quantity: item.quantity,
                price: item.price
            }));

            document.getElementById('orderItemsInput').value = JSON.stringify(itemsData);
        });

        addOrderItem();
    </script>
</body>
</html>