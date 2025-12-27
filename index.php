<?php
    session_start();

    if(isset($_SESSION["id"])) {
        header("Location: admin/dashboard.php");
        exit;
    } else {
        header("Location: auth/login.php");
        exit;
    }
?>