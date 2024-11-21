<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Report - {{ $report->title }}</title>
    <style>
        @media print {
            @page {
                margin: 0.5in;
            }
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }

        .clinic-info {
            margin-bottom: 20px;
        }

        .report-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .patient-info {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .field {
            margin-bottom: 10px;
        }

        .field-label {
            font-weight: bold;
            color: #666;
        }

        .field-value {
            margin-top: 3px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .signature-section {
            margin-top: 50px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
        }

        .signature-block {
            text-align: center;
        }

        .doctor-name, .date-value {
            margin-bottom: 10px;
            font-weight: bold;
        }

        .signature-line {
            border-top: 1px solid #333;
            padding-top: 5px;
            text-align: center;
            margin-top: 40px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Medical Report</h1>
        <div class="clinic-info">
            <h2>SUC Hospital</h2>
            <p>Address: 123 Main Street, Kuala Lumpur, Malaysia</p>
            <p>Phone: 60-177423008</p>
        </div>
    </div>

    <div class="report-title">{{ $report->title }}</div>
    <div class="patient-info">
        <div class="grid">
            <div>
                <div class="field">
                    <div class="field-label">Patient Name:</div>
                    <div class="field-value">{{ $appointment->patient->name }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Age:</div>
                    <div class="field-value">{{ $appointment->patient->getAgeFromIc() }} years</div>
                </div>
            </div>
            <div>
                <div class="field">
                    <div class="field-label">Gender:</div>
                    <div class="field-value">{{ ucfirst($appointment->patient->gender) }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Report Date:</div>
                    <div class="field-value">{{ date('F j, Y', strtotime($report->report_date)) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Vital Signs</div>
        <div class="grid">
            <div class="field">
                <div class="field-label">Blood Pressure:</div>
                <div class="field-value">{{ $report->blood_pressure_systolic }}/{{ $report->blood_pressure_diastolic }} mmHg</div>
            </div>
            <div class="field">
                <div class="field-label">Heart Rate:</div>
                <div class="field-value">{{ $report->heart_rate }} bpm</div>
            </div>
            <div class="field">
                <div class="field-label">Temperature:</div>
                <div class="field-value">{{ $report->temperature }}°C</div>
            </div>
            <div class="field">
                <div class="field-label">Respiratory Rate:</div>
                <div class="field-value">{{ $report->respiratory_rate }} /min</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Clinical Information</div>
        <div class="field">
            <div class="field-label">Chief Complaint:</div>
            <div class="field-value">{{ $report->symptoms }}</div>
        </div>
        <div class="field">
            <div class="field-label">Examination Findings:</div>
            <div class="field-value">{{ $report->examination_findings }}</div>
        </div>
        @if($report->lab_results)
        <div class="field">
            <div class="field-label">Laboratory Results:</div>
            <div class="field-value">{{ $report->lab_results }}</div>
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Diagnosis & Treatment</div>
        <div class="field">
            <div class="field-label">Diagnosis:</div>
            <div class="field-value">{{ $report->diagnosis }}</div>
        </div>
        <div class="field">
            <div class="field-label">Treatment Plan:</div>
            <div class="field-value">{{ $report->treatment_plan }}</div>
        </div>
        @if($report->medications)
        <div class="field">
            <div class="field-label">Medications:</div>
            <div class="field-value">{{ $report->medications }}</div>
        </div>
        @endif
    </div>

    <div class="signature-section">
        <div class="signature-block">
            <div class="doctor-name">{{ $appointment->doctor->name }}</div>
            <div class="signature-line">Doctor's Signature</div>
        </div>
        <div class="signature-block">
            <div class="date-value">{{ date('F j, Y', strtotime($report->report_date)) }}</div>
            <div class="signature-line">Date</div>
        </div>
    </div>

    <div class="footer">
        <p>This is a computer-generated medical report.</p>
    </div>

    <!-- Print Button - Only visible on screen -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
            Print Report
        </button>
    </div>
</body>
</html> 