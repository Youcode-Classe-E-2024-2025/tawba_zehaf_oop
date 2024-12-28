document.addEventListener('DOMContentLoaded', function() {
    // Task status update
    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const taskId = this.getAttribute('data-task-id');
            const newStatus = this.value;
            fetch('index.php?action=update_task_status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${taskId}&status=${newStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Task status updated successfully');
                } else {
                    alert('Failed to update task status');
                    this.value = this.getAttribute('data-original-status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating task status');
                this.value = this.getAttribute('data-original-status');
            });
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
                window.location.href = `index.php?action=delete_${this.itemType}&id=${this.itemId}`;
            }
        }));
    }
});

