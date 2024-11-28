@extends('nurse.layout')
@section('title', 'My Schedule')

@section('content')
<div class="container-fluid p-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">My Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Shift</th>
                                    <th>Room</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $schedule->shift === 'morning' ? 'info' : ($schedule->shift === 'afternoon' ? 'warning' : 'dark') }}">
                                                {{ ucfirst($schedule->shift) }}
                                            </span>
                                        </td>
                                        <td>Room {{ $schedule->room->room_number }}</td>
                                        <td>
                                            <span class="badge bg-{{ $schedule->date->isPast() ? 'secondary' : 'success' }}">
                                                {{ $schedule->date->isPast() ? 'Completed' : 'Upcoming' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No schedules found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 