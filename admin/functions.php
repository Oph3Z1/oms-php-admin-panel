<?php
    // Get all orders
    function getOrders($conn, $limit = null) {
        $sql = "SELECT id, order_number, customer_id, status, total_amount, order_date FROM orders ORDER BY order_date DESC";

        if ($limit !== null) {
            $sql .= " LIMIT " .intval($limit);
        }

        $result = mysqli_query($conn, $sql);

        if (!$result) return [];

        $orders = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }

        return $orders;
    }

    // Get orders count
    function getOrdersCount($conn) {
        $sql = "SELECT COUNT(*) AS total FROM orders";
        $result = mysqli_query($conn, $sql);

        if (!$result) return 0;

        $row = mysqli_fetch_assoc($result);
        return intval($row["total"]);
    }

    // Get revenue
    function getTotalOrderAmount($conn) {
        $sql = "SELECT SUM(total_amount) AS total FROM orders";
        $result = mysqli_query($conn, $sql);

        if (!$result) return 0;

        $row = mysqli_fetch_assoc($result);
        return floatval($row["total"]);
    }

    // Get all customers
    function getCustomers($conn) {
        $sql = "SELECT id, name, email, phone FROM customers";
        $result = mysqli_query($conn, $sql);

        if (!$result) return [];

        $customers = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $customers[] = $row;
        }

        return $customers;
    }

    // Get how many customers there is in database
    function getCustomerCount($conn) {
        $sql = "SELECT COUNT(*) AS total FROM customers";
        $result = mysqli_query($conn, $sql);

        if (!$result) return 0;

        $row = mysqli_fetch_assoc($result);
        return intval($row["total"]);
    }


    // Get customer by id
    function getCustomerByID($conn, $id) {
        $sql = "SELECT name, email, phone FROM customers WHERE id = ? LIMIT 1";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            return $row;
        } else {
            return null;
        }

        mysqli_stmt_close($stmt);
    }

    // Get all products
    function getProducts($conn) {
        $sql = "SELECT id, name, sku, description, price, stock FROM products";
        $result = mysqli_query($conn, $sql);

        if (!$result) return [];

        $products = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }

        return $products;
    }

    // Get products with low stock (under 10)
    function getLowStockProducts($conn, $limit = null) {
        $sql = "SELECT id, name, sku, description, price, stock FROM products WHERE stock < 10 ORDER BY stock ASC";

        if ($limit !== null) {
            $sql .= " LIMIT " . intval($limit);
        }

        $result = mysqli_query($conn, $sql);
        if (!$result) return [];

        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }

        return $products;
    }

    // Get total amount of products with low stock (under 10)
    function countLowStockProducts($conn) {
        $sql = "SELECT COUNT(*) AS total FROM products WHERE stock < 10";
        $result = mysqli_query($conn, $sql);

        if (!$result) return 0;

        $row = mysqli_fetch_assoc($result);
        return intval($row["total"]);
    }

    // Get order by ID
    function getOrderByID($conn, $id) {
        $sql = "SELECT id, order_number, customer_id, status, total_amount, order_date FROM orders WHERE id = ? LIMIT 1";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            mysqli_stmt_close($stmt);
            return $row;
        } else {
            mysqli_stmt_close($stmt);
            return null;
        }
    }

    // Get order items by order ID
    function getOrderItems($conn, $order_id) {
        $sql = "SELECT oi.id, oi.quantity, oi.price, p.name, p.sku FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";

        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }

        mysqli_stmt_close($stmt);
        return $items;
    }

    // Get product by ID
    function getProductByID($conn, $id) {
        $sql = "SELECT id, name, sku, description, price, stock FROM products WHERE id = ? LIMIT 1";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            mysqli_stmt_close($stmt);
            return $row;
        } else {
            mysqli_stmt_close($stmt);
            return null;
        }
    }