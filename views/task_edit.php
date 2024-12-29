<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - TaskFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-5">Edit Task</h1>
        <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $error; ?></span>
        </div>
        <?php endif; ?>
        <form action="index.php?action=update_task&id=<?php echo $task->id; ?>" method="post"
            class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                    Title
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="title" type="text" name="title" value="<?php echo htmlspecialchars($task->title); ?>" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                    Description
                </label>
                <textarea
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="description" name="description"
                    rows="3"><?php echo htmlspecialchars($task->description); ?></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                    Status
                </label>
                <select
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="status" name="status">
                    <option value="todo" <?php echo $task->status === 'todo' ? 'selected' : ''; ?>>To Do</option>
                    <option value="doing" <?php echo $task->status === 'doing' ? 'selected' : ''; ?>>Doing</option>
                    <option value="done" <?php echo $task->status === 'done' ? 'selected' : ''; ?>>Done</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                    Type
                </label>
                <select
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="type" name="type">
                    <option value="basic" <?php echo $task->type === 'basic' ? 'selected' : ''; ?>>Basic</option>
                    <option value="bug" <?php echo $task->type === 'bug' ? 'selected' : ''; ?>>Bug</option>
                    <option value="feature" <?php echo $task->type === 'feature' ? 'selected' : ''; ?>>Feature</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="assigned_to">
                    Assigned To
                </label>
                <select
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="assigned_to" name="assigned_to">
                    <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user['id']; ?>"
                        <?php echo $task->assigned_to == $user['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($user['username']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="flex items-center justify-between">
                <button
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                    Update Task
                </button>
                <a href="index.php?action=tasks"
                    class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</body>

</html>