@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Active Patient Calls</h2>
    <div id="calls-container">
        <!-- Calls will be displayed here -->
    </div>
</div>

@push('scripts')
<script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-database.js"></script>

<script>
// Your Firebase configuration
const firebaseConfig = {
    // Add your Firebase config here
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const database = firebase.database();

// Listen for new calls
database.ref('nurse_calls').on('value', (snapshot) => {
    const calls = snapshot.val();
    let html = '';
    
    for (let callId in calls) {
        const call = calls[callId];
        if (call.status === 'pending') {
            html += `
                <div class="call-card" id="call-${callId}">
                    <h3>Patient: ${call.patient_name}</h3>
                    <p>Location: <a href="https://www.google.com/maps?q=${call.latitude},${call.longitude}" target="_blank">
                        View on Map
                    </a></p>
                    <p>Time: ${call.created_at}</p>
                    <button onclick="attendCall('${callId}')">Attend Call</button>
                </div>
            `;
        }
    }
    
    document.getElementById('calls-container').innerHTML = html;
});

function attendCall(callId) {
    fetch(`/nurse/calls/${callId}/update`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
}
</script>
@endpush
@endsection 