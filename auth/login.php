<?php
    session_start();

    if (isset($_SESSION["id"])) {
        header("Location: ../admin/dashboard.php");
        exit;
    }

    if (isset($_POST["submit"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        require_once "./dbh.php";
        require_once "./functions.php";

        if (emptyInputLogin($email, $password) !== false) {
            header("Location: ./login.php?error=emptyinput");
            exit;
        }

        if (invalidEmail($email) !== false) {
            header("Location: ./login.php?error=invalidemail");
            exit;
        }

        loginUser($conn, $email, $password);
    }

    $error = $_GET["error"] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in - OMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex flex-col">
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="w-full max-w-md">
            <div class="bg-gray-800 shadow-xl rounded-lg border border-gray-700 p-8">
                <div class="mb-8 text-center">
                    <h1 class="text-2xl font-semibold text-white">Sign in</h1>
                    <p class="mt-2 text-sm text-gray-400">OMS admin panel</p>
                </div>

                <?php if ($error): ?>
                    <div class="mb-6 rounded-lg border border-red-500 bg-red-900/30 px-3 py-2 text-sm text-red-200">
                        <?php if ($error === "emptyinput"): ?>
                            Please fill in all fields.
                        <?php elseif ($error === "invalidemail"): ?>
                            Please enter a valid email address.
                        <?php elseif ($error === "wronglogin"): ?>
                            Wrong email/password.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- <div class="mb-6 bg-red-900/20 border border-red-800 rounded-lg p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-red-400">Invalid credentials</h3>
                        <p class="mt-1 text-sm text-red-300">The email or password you entered is incorrect.</p>
                    </div>
                </div> -->

                <form action="login.php" class="space-y-5" method="POST">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-white placeholder:text-gray-500"
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

                    <button
                        type="submit"
                        name="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors duration-150"
                    >
                        Sign in
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-400">
                        Don't have an account?
                        <a href="../auth/register.php" class="font-medium text-indigo-400 hover:text-indigo-300">Create an account</a>
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