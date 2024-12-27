<?php
require_once 'User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données du formulaire
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Créer une instance de User et essayer de se connecter
    $user = new User();
    $message = $user->login($username, $password);

    // Afficher le message de connexion
    echo $message;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TaskFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-8 px-4">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold mb-6">Login to TaskFlow</h1>
            
            <?php if(isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form id="loginForm" onsubmit="handleLogin(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-3 py-2 border rounded-md">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-3 py-2 border rounded-md">
                </div>
                
                <button type="submit" 
                    class="w-full px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                    Login
                </button>
                
                <p class="text-center text-sm text-gray-600">
                    Don't have an account? 
                    <a href="index.php?action=showSignup" class="text-blue-500 hover:text-blue-600">Sign up</a>
                </p>
            </form>
        </div>
    </div>
    <script>
        function handleLogin(event) {
            event.preventDefault();
           
        }
    </script>
</body>
</html>