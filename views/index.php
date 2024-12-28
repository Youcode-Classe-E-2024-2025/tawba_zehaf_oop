<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-8 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-bold">TaskFlow</h1>
                <a href="index.php?action=create" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    New Task
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php while($row = $tasks->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-lg"><?= htmlspecialchars($row['title']) ?></h3>
                            <span class="px-2 py-1 text-sm rounded <?= $row['status'] === 'todo' ? 'bg-gray-200' : ($row['status'] === 'in-progress' ? 'bg-blue-200' : 'bg-green-200') ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-4"><?= htmlspecialchars($row['description']) ?></p>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Assigned to: <?= $row['assigned_username'] ? htmlspecialchars($row['assigned_username']) : 'Unassigned' ?></span>
                            <span><?= date('M j, Y', strtotime($row['created_at'])) ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>