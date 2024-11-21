<div class="modal fade" id="addRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('nurseadmin.rooms.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Room Number</label>
                        <input type="text" class="form-control" name="room_number" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Floor</label>
                        <input type="number" class="form-control" name="floor" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Room Type</label>
                        <select class="form-select" name="type" required>
                            <option value="ward">Ward</option>
                            <option value="private">Private</option>
                            <option value="icu">ICU</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Initial Number of Beds</label>
                        <input type="number" class="form-control" name="total_beds" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Room</button>
                </div>
            </form>
        </div>
    </div>
</div> 