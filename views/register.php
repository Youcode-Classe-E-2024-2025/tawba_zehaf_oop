<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TaskFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-5">Register for TaskFlow</h1>
        <?php if (isset($error)): ?>
        <p class="text-red-500 mb-3"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="index.php?action=register" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4"
            id="registerForm">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="username" type="text" name="username" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="email" type="email" name="email" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline"
                    id="password" type="password" name="password" required>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="flex items-center justify-between">
                <button
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                    Register
                </button>
                <a href="index.php?action=login"
                    class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Already have an account? Login
                </a>
            </div>
        </form>
    </div>
    <script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Client-side validation
        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

        if (!usernameRegex.test(username)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Username',
                text: 'Username must be 3-20 characters long and contain only letters, numbers, and underscores',
            });
            return;
        }

        if (!emailRegex.test(email)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please enter a valid email address',
            });
            return;
        }

        if (!passwordRegex.test(password)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Password',
                text: 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number',
            });
            return;
        }

        this.submit();
    });
    </script>
</body>

</html>