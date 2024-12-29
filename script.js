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

