<?php
    session_start();

    if (isset($_SESSION["id"])) {
        header("Location: ../admin/dashboard.php");
        exit;
    }

    if (isset($_POST["submit"])) {
        $name = $_POST["fullname"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $confirm_password = $_POST["confirm_password"];

        require_once "./dbh.php";
        require_once "./functions.php";

        if (emptyInputRegister($name, $email, $password, $confirm_password) !== false) {
            header("Location: ./register.php?error=emptyinput");
            exit;
        }

        if (invalidEmail($email) !== false) {
            header("Location: ./register.php?error=invalidemail");
            exit;
        }
        
        if (passwordMatch($password, $confirm_password) !== false) {
            header("Location: ./register.php?error=passwordsdontmatch");
            exit;
        }

        if (emailExists($conn, $email) !== false) {
            header("Location: ./register.php?error=emailtaken");
            exit;
        }

        createAccount($conn, $name, $email, $password);
    }

    $error = $_GET["error"] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account - OMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex flex-col">
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="w-full max-w-md">
            <div class="bg-gray-800 shadow-xl rounded-lg border border-gray-700 p-8">
                <div class="mb-8 text-center">
                    <h1 class="text-2xl font-semibold text-white">Create account</h1>
                    <p class="mt-2 text-sm text-gray-400">Admin access for OMS</p>
                </div>

                <?php if ($error): ?>
                    <div class="mb-6 rounded-lg border border-red-500 bg-red-900/30 px-3 py-2 text-sm text-red-200">
                        <?php if ($error === "emptyinput"): ?>
                            Please fill in all fields.
                        <?php elseif ($error === "invalidemail"): ?>
                            Please enter a valid email address.
                        <?php elseif ($error === "passwordsdontmatch"): ?>
                            Passwords do not match.
                        <?php elseif ($error === "emailtaken"): ?>
                            That email is already registered.
                        <?php elseif ($error === "stmtfailed"): ?>
                            Something went wrong on the server. Please try again.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form action="register.php" class="space-y-5" method="POST">
                    <div>
                        <label for="fullname" class="block text-sm font-medium text-gray-300 mb-1.5">Full name</label>
                        <input
                            type="text"
                            id="fullname"
                            name="fullname"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-white placeholder:text-gray-400"
                            placeholder="John Doe"
                        >
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-white placeholder:text-gray-400"
                            placeholder="you@example.com"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-1.5">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-white placeholder:text-gray-400"
                            placeholder="••••••••"
                        >
                    </div>

                    <div>
                        <label for="confirm-password" class="block text-sm font-medium text-gray-300 mb-1.5">Confirm password</label>
                        <input
                            type="password"
                            id="confirm-password"
                            name="confirm_password"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-white placeholder:text-gray-400"
                            placeholder="••••••••"
                        >
                    </div>

                    <button
                        type="submit"
                        name="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors duration-150"
                    >
                        Create account
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-400">
                        Already have an account?
                        <a href="../auth/login.php" class="font-medium text-indigo-400 hover:text-indigo-300">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <footer class="py-6 text-center">
        <p class="text-sm text-gray-500">&copy; OMS</p>
    </footer>
</body>
</html>