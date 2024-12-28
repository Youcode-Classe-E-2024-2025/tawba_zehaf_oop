<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task List - TaskFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <script src="script.js" defer></script>
</head>

<body class="bg-gray-100" x-data="modalData">
    <div class="container mx-auto mt-10" x-data="{ showModal: false, taskId: null }">
        <div class="flex justify-between items-center mb-5">
            <h1 class="text-3xl font-bold">Task List</h1>
            <div>
                <a href="index.php?action=create_task"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Create Task</a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="index.php?action=list_users"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">Manage Users</a>
                <?php endif; ?>
                <a href="index.php?action=logout"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2">Logout</a>
            </div>
        </div>
        <table class="w-full bg-white shadow-md rounded">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Title</th>
                    <th class="py-3 px-6 text-left">Status</th>
                    <th class="py-3 px-6 text-left">Type</th>
                    <th class="py-3 px-6 text-left">Assigned To</th>
                    <th class="py-3 px-6 text-left">Created By</th>
                    <th class="py-3 px-6 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php while ($row = $tasks->fetch(PDO::FETCH_ASSOC)): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap"><?php echo htmlspecialchars($row['title']); ?>
                    </td>
                    <td class="py-3 px-6 text-left">
                        <select class="status-select" data-task-id="<?php echo $row['id']; ?>"
                            <?php echo ($_SESSION['role'] === 'user' && $row['assigned_to'] != $_SESSION['user_id']) ? 'disabled' : ''; ?>>
                            <option value="todo" <?php echo $row['status'] === 'todo' ? 'selected' : ''; ?>>To Do
                            </option>
                            <option value="doing" <?php echo $row['status'] === 'doing' ? 'selected' : ''; ?>>Doing
                            </option>
                            <option value="done" <?php echo $row['status'] === 'done' ? 'selected' : ''; ?>>Done
                            </option>
                        </select>
                    </td>
                    <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($row['type']); ?></td>
                    <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($row['assigned_to_name']); ?></td>
                    <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($row['created_by_name']); ?></td>
                    <td class="py-3 px-6 text-left">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="index.php?action=update_task&id=<?php echo $row['id']; ?>"
                            class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                        <button @click="openModal(<?php echo $row['id']; ?>, 'task')"
                            class="text-red-600 hover:text-red-900">Delete</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Delete Confirmation Modal -->
        <div x-show="showModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Delete Task
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete this task? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="confirmDelete()" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                        <button @click="closeModal()" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>