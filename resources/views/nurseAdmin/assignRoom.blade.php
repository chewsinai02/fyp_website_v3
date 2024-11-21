<div class="modal fade" id="assignRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assign_room_form" action="{{ route('nurseadmin.assignRoom') }}" method="POST">
                @csrf
                <input type="hidden" id="nurse_id_for_room" name="nurse_id">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Room</label>
                        <select class="form-select" name="room_id" id="room_id" required>
                            <!-- Rooms will be populated via JavaScript -->
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" id="assignment_notes" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Room</button>
                </div>
            </form>
        </div>
    </div>
</div> 