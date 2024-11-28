<div class="modal fade" id="addVitalsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Vital Signs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('nurse.patient.vitals.store', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Temperature (°C)</label>
                        <input type="number" step="0.1" class="form-control @error('temperature') is-invalid @enderror" 
                               name="temperature" value="{{ old('temperature') }}" required>
                        @error('temperature')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Blood Pressure</label>
                        <input type="text" class="form-control @error('blood_pressure') is-invalid @enderror" 
                               name="blood_pressure" placeholder="120/80" value="{{ old('blood_pressure') }}" required>
                        @error('blood_pressure')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Heart Rate (bpm)</label>
                        <input type="number" class="form-control @error('heart_rate') is-invalid @enderror" 
                               name="heart_rate" value="{{ old('heart_rate') }}" required>
                        @error('heart_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Respiratory Rate (/min)</label>
                        <input type="number" class="form-control @error('respiratory_rate') is-invalid @enderror" 
                               name="respiratory_rate" value="{{ old('respiratory_rate') }}" required>
                        @error('respiratory_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div> 