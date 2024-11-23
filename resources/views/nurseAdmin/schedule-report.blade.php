<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .generated-date {
            text-align: right;
            font-size: 10px;
            color: #666;
            margin-bottom: 20px;
        }
        .date-header {
            background-color: #f0f0f0;
            padding: 5px;
            margin: 10px 0;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .shift-morning { color: #0d6efd; }
        .shift-afternoon { color: #fd7e14; }
        .shift-night { color: #6f42c1; }
        .status-scheduled { color: #0d6efd; }
        .status-completed { color: #198754; }
        .status-cancelled { color: #dc3545; }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Nurse Schedule Report</h2>
    </div>
    
    <div class="generated-date">
        Generated on: {{ $generatedDate }}
    </div>

    <div>
        Total Dates: {{ $groupedSchedules->count() }}
    </div>

    @foreach($groupedSchedules as $date => $daySchedules)
    <div class="date-header">
        {{ Carbon\Carbon::parse($date)->format('l, M d, Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Shift</th>
                <th>Nurse</th>
                <th>Room Assignment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($daySchedules as $schedule)
                <tr>
                    <td class="shift-{{ $schedule->shift }}">
                        {{ ucfirst($schedule->shift) }}
                    </td>
                    <td>
                        @if($schedule->nurse)
                            {{ $schedule->nurse->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($schedule->room)
                            Room {{ $schedule->room->room_number }}
                        @else
                            No room assigned
                        @endif
                    </td>
                    <td class="status-{{ $schedule->status }}">
                        {{ ucfirst($schedule->status) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach

</body>
</html>