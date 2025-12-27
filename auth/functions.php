<?php

    function emptyInputRegister($name, $email, $password, $confirm_password) {
        $result = false;

        if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
            $result = true;
        }

        return $result;
    }

    function invalidEmail($email) {
        $result = false;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result = true;
        }

        return $result;
    }

    function passwordMatch($password, $confirm_password) {
        $result = false;

        if ($password !== $confirm_password) {
            $result = true;
        }

        return $result;
    }

    function emailExists($conn, $email) {
        $result = "SELECT * FROM users WHERE email = ?;";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $result)) {
            header("Location: ./register.php?error=stmtfailed");
            exit;
        }

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt); 

        if ($row = mysqli_fetch_assoc($resultData)) { // assoc = omvandlas till assoc array. row = ["id" => 1]
            return $row;
        } else {
            return false;
        }

        mysqli_stmt_close($stmt);
    }

    function createAccount($conn, $name, $email, $password) {
        $result = "INSERT INTO users (name, email, password) VALUES (?, ?, ?);";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $result)) {
            header("Location: ./register.php?error=stmtfailed");
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashedPassword);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        loginUser($conn, $email, $password);
    }

    function emptyInputLogin($email, $password) {
        $result = false;

        if (empty($email) || empty($password)) {
            $result = true;
        }

        return $result;
    }

    function loginUser($conn, $email, $password) {
        $emailExists = emailExists($conn, $email);

        if ($emailExists === false) {
            header("Location: ./login.php?error=wronglogin");
            exit;
        }

        $passwordHashed = $emailExists["password"];
        $checkPassword = password_verify($password, $passwordHashed);

        if ($checkPassword === false) {
            header("Location: ./login.php?error=wronglogin");
            exit;
        } else if ($checkPassword === true) {
            session_start();
            $_SESSION["id"] = $emailExists["id"];
            $_SESSION["email"] = $emailExists["email"];
            $_SESSION["name"] = $emailExists["name"];
            header("Location: ../admin/dashboard.php");
            exit;
        }
    }