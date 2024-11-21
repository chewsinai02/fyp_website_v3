@forelse($schedules as $schedule)
    <tr>
        <td>
            <div class="d-flex align-items-center">
                <img src="{{ $schedule->nurse->profile_picture_url }}" 
                     class="rounded-circle me-2" 
                     width="32" 
                     height="32"
                     alt="{{ $schedule->nurse->name }}'s Profile"
                     onerror="this.src='{{ asset('images/default-profile.png') }}'">
                {{ $schedule->nurse->name }}
            </div>
        </td>
        <td>{{ $schedule->date->format('M d, Y') }}</td>
        <td>
            <span class="badge bg-{{ $schedule->shift_color }}-subtle text-{{ $schedule->shift_color }}">
                {{ ucfirst($schedule->shift) }}
            </span>
        </td>
        <td>
            @if($schedule->room)
                <span class="badge bg-info">
                    Room {{ $schedule->room->room_number }}
                </span>
            @else
                <span class="text-muted">No room assigned</span>
            @endif
        </td>
        <td>
            <span class="badge bg-{{ $schedule->status_color }}-subtle text-{{ $schedule->status_color }}">
                {{ ucfirst($schedule->status) }}
            </span>
        </td>
        <td>
            <button class="btn btn-sm btn-outline-primary me-1" 
                    onclick="editSchedule({{ $schedule->id }})">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" 
                    onclick="deleteSchedule({{ $schedule->id }})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-4 text-muted">
            No schedules found for this date
        </td>
    </tr>
@endforelse 