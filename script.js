document.addEventListener('DOMContentLoaded', function() {
    // Task status update
    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const taskId = this.getAttribute('data-task-id');
            const newStatus = this.value;
            updateTaskStatus(taskId, newStatus);
        });
    });

    // Delete confirmation modal
    if (typeof Alpine !== 'undefined') {
        Alpine.data('modalData', () => ({
            showModal: false,
            itemId: null,
            itemType: null,
            openModal(id, type) {
                this.showModal = true;
                this.itemId = id;
                this.itemType = type;
            },
            closeModal() {
                this.showModal = false;
                this.itemId = null;
                this.itemType = null;
            },
            confirmDelete() {
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
                        window.location.href = `index.php?action=delete_${this.itemType}&id=${this.itemId}`;
                    }
                });
            }
        }));
    }
});


document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: data.message || 'Invalid username or password',
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!',
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