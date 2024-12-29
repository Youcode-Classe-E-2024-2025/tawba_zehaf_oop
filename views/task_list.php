<?php
// Include the Database class
include 'config/Database.php';

// Start the session
session_start();

// Check if the session is set properly
if (!isset($_SESSION['role'])) {
    // Redirect to login or show a proper message
    header('Location: login.php');
    exit();
}

// Create a new Database object and get the connection
$database = new Database();
$conn = $database->getConnection();

// Fetch tasks from the database
$tasks = [];
try {
    // SQL query to fetch tasks
    $sql = "SELECT t.id, t.title, t.description, t.status, t.type, t.assigned_to, u.name as assigned_to_name
            FROM tasks t
            LEFT JOIN users u ON t.assigned_to = u.id"; // Assuming 'tasks' and 'users' are your tables

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all tasks as an associative array
} catch (PDOException $e) {
    echo "Error fetching tasks: " . $e->getMessage();
}

// If no tasks found, set an empty array
if (empty($tasks)) {
    $tasks = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task List - TaskFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <div class="flex justify-between items-center mb-5">
            <h1 class="text-3xl font-bold">Task List</h1>
            <div>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="index.php?action=create_task"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Create New Task</a>
                <a href="index.php?action=list_users"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded ml-2">Manage Users</a>
                <?php endif; ?>
                <a href="index.php?action=logout"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2">Logout</a>
            </div>
        </div>
        <table class="w-full bg-white shadow-md rounded mb-4">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Title</th>
                    <th class="py-3 px-6 text-left">Description</th>
                    <th class="py-3 px-6 text-left">Status</th>
                    <th class="py-3 px-6 text-left">Type</th>
                    <th class="py-3 px-6 text-left">Assigned To</th>
                    <th class="py-3 px-6 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php foreach ($tasks as $task): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap"><?php echo htmlspecialchars($task['title']); ?>
                    </td>
                    <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($task['description']); ?></td>
                    <td class="py-3 px-6 text-left">
                        <select class="status-select" data-task-id="<?php echo $task['id']; ?>"
                            data-original-status="<?php echo $task['status']; ?>"
                            <?php echo $_SESSION['role'] !== 'admin' && $task['assigned_to'] != $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                            <option value="todo" <?php echo $task['status'] === 'todo' ? 'selected' : ''; ?>>To Do
                            </option>
                            <option value="doing" <?php echo $task['status'] === 'doing' ? 'selected' : ''; ?>>Doing
                            </option>
                            <option value="done" <?php echo $task['status'] === 'done' ? 'selected' : ''; ?>>Done
                            </option>
                        </select>
                    </td>
                    <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($task['type']); ?></td>
                    <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($task['assigned_to_name']); ?></td>
                    <td class="py-3 px-6 text-left">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="index.php?action=update_task&id=<?php echo $task['id']; ?>"
                            class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                        <a href="#" onclick="confirmDelete(<?php echo $task['id']; ?>)"
                            class="text-red-600 hover:text-red-900">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
    function confirmDelete(taskId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href =
                    `index.php?action=delete_task&id=${taskId}&csrf_token=<?php echo $csrf_token; ?>`;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const statusSelects = document.querySelectorAll('.status-select');
        statusSelects.forEach(select => {
            select.addEventListener('change', function() {
                const taskId = this.getAttribute('data-task-id');
                const newStatus = this.value;
                updateTaskStatus(taskId, newStatus);
            });
        });
    });

    function updateTaskStatus(taskId, newStatus) {
        fetch('index.php?action=update_task_status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': '<?php echo $csrf_token; ?>'
                },
                body: `id=${taskId}&status=${newStatus}&csrf_token=<?php echo $csrf_token; ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Task status updated successfully',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: data.message || 'Failed to update task status',
                    });
                    // Revert the select element to its original value
                    document.querySelector(`select[data-task-id="${taskId}"]`).value = document.querySelector(
                        `select[data-task-id="${taskId}"]`).getAttribute('data-original-status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred while updating task status',
                });
                // Revert the select element to its original value
                document.querySelector(`select[data-task-id="${taskId}"]`).value = document.querySelector(
                    `select[data-task-id="${taskId}"]`).getAttribute('data-original-status');
            });
    }
    </script>
</body>

</html>