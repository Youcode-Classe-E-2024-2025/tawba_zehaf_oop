<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - TaskFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-5">Edit User</h1>

        <!-- Error Message -->
        <?php if (isset($error)): ?>
        <p class="text-red-500 mb-3"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if (isset($user)): ?>
        <form action="index.php?action=edit_user&id=<?php echo htmlspecialchars($user['id']); ?>" method="post"
            class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="username" type="text" name="username"
                    value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="email" type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                    required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="role">Role</label>
                <select
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="role" name="role">
                    <option value="user"
                        <?php echo (isset($user['role']) && $user['role'] === 'user') ? 'selected' : ''; ?>>
                        User
                    </option>
                    <option value="admin"
                        <?php echo (isset($user['role']) && $user['role'] === 'admin') ? 'selected' : ''; ?>>
                        Admin
                    </option>
                </select>
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="flex items-center justify-between">
                <button
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                    Update User
                </button>
                <a href="index.php?action=list_users"
                    class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancel
                </a>
            </div>
        </form>
        <?php else: ?>
        <p class="text-red-500">User not found. Please try again.</p>
        <?php endif; ?>
    </div>
</body>

</html>