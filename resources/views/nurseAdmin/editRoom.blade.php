<div class="modal fade" id="editRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRoomForm" method="POST" action="{{ route('rooms.update', ['room' => $room->id]) }}">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_room_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Room Number</label>
                        <input type="text" class="form-control" id="edit_room_number" name="room_number" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Floor</label>
                        <input type="number" class="form-control" id="edit_floor" name="floor" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" id="edit_type" name="type" required>
                            <option value="ward">Ward</option>
                            <option value="private">Private</option>
                            <option value="icu">ICU</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Beds</label>
                        <input type="number" class="form-control" id="edit_total_beds" name="total_beds" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Available Beds</label>
                        <input type="number" class="form-control" id="edit_available_beds" name="available_beds" readonly>
                        <small class="text-muted">Available beds are automatically calculated based on unoccupied beds</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editRoomModal = document.getElementById('editRoomModal');
    
    // Refresh on modal close (X button or clicking outside)
    editRoomModal.addEventListener('hidden.bs.modal', function (e) {
        window.location.reload();
    });

    // Refresh when Cancel button is clicked
    const cancelButton = editRoomModal.querySelector('button[data-bs-dismiss="modal"]');
    if (cancelButton) {
        cancelButton.addEventListener('click', function() {
            window.location.reload();
        });
    }
});

function editRoom(id) {
    console.log('Editing room:', id);
    
    fetch(`/nurseadmin/rooms/${id}/edit`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Received data:', data);
        
        if (data.success) {
            const modal = document.getElementById('editRoomModal');
            modal.querySelector('#edit_room_id').value = data.id;
            modal.querySelector('#edit_room_number').value = data.room_number;
            modal.querySelector('#edit_floor').value = data.floor;
            modal.querySelector('#edit_type').value = data.type;
            modal.querySelector('#edit_total_beds').value = data.total_beds;
            modal.querySelector('#edit_available_beds').value = data.available_beds;
            modal.querySelector('#edit_notes').value = data.notes || '';
            
            modal.querySelector('#edit_available_beds').setAttribute('readonly', true);
            
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to fetch room details'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to fetch room details'
        });
    });
}

document.getElementById('editRoomForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const roomId = document.getElementById('edit_room_id').value;
    const formData = new FormData(this);

    fetch(`/nurseadmin/rooms/${roomId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Room updated successfully',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to update room'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to update room'
        });
    });
});
</script>