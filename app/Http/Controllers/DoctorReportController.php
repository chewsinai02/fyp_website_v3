<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Appointment;

class DoctorReportController extends Controller
{
    public function index($patientId)
    {
        $patient = User::findOrFail($patientId);
        $reports = Report::select('reports.*', 'users.name', 'users.gender', 'users.profile_picture', 'users.contact_number')
                        ->join('users', 'reports.patient_id', '=', 'users.id')
                        ->where('reports.patient_id', $patientId)
                        ->orderBy('reports.created_at', 'desc')
                        ->paginate(10);

        // Get the latest appointment for this patient
        $appointment = Appointment::where('patient_id', $patientId)
                                ->with('patient')
                                ->latest()
                                ->first();

        // If no appointment exists, create a temporary one
        if (!$appointment) {
            $appointment = new Appointment();
            $appointment->patient = $patient;
            $appointment->patient_id = $patientId;
        }

        return view('doctor.reportList', compact('reports', 'patient', 'appointment'));
    }

    public function create($patientId)
    {
        try {
            // Get the patient with their latest appointment
            $patient = User::select('users.*')
                ->leftJoin('appointments', 'users.id', '=', 'appointments.patient_id')
                ->where('users.id', $patientId)
                ->firstOrFail();

            // Get the latest appointment for this specific patient
            $appointment = Appointment::select('appointments.*', 'users.*')
                ->join('users', 'appointments.patient_id', '=', 'users.id')
                ->where('appointments.patient_id', $patientId)
                ->latest('appointments.created_at')
                ->first();

            // If no appointment exists, create a temporary one
            if (!$appointment) {
                $appointment = new Appointment();
                $appointment->patient_id = $patientId;
                $appointment->patient = $patient;
            }

            $report = null;
            return view('doctor.addReport', compact('appointment', 'report', 'patient'));
        } catch (\Exception $e) {
            \Log::error('Error in create:', [
                'patient_id' => $patientId,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Could not find patient information.');
        }
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'title' => 'required|max:255',
            'report_date' => 'required|date',
            
            // Vital Signs
            'blood_pressure_systolic' => 'nullable|numeric|between:0,300',
            'blood_pressure_diastolic' => 'nullable|numeric|between:0,300',
            'heart_rate' => 'nullable|numeric|between:0,300',
            'temperature' => 'nullable|numeric|between:20,45',
            'respiratory_rate' => 'nullable|numeric|between:0,100',
            'notes' => 'nullable|string',
            
            // Measurements
            'weight' => 'nullable|numeric|between:0,999.99',
            'height' => 'nullable|numeric|between:0,999.99',
            
            // Clinical Information
            'symptoms' => 'required|string',
            'examination_findings' => 'required|string',
            'lab_results' => 'nullable|string',
            
            // Diagnosis & Treatment
            'diagnosis' => 'required|string',
            'treatment_plan' => 'required|string',
            'description' => 'nullable|string',
            
            // Medical History
            'medical_history' => 'nullable|array',
            'medical_history.*' => 'string|in:none,allergy,diabetes,hypertension,others',
            
            // Medications
            'medications' => 'nullable|string',
            
            // Follow-up
            'follow_up_instructions' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            
            // Attachments
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120'
        ]);

        try {
            // Handle file uploads
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                    
                    // Create unique filename
                    $uniqueName = $fileName . '_' . time() . '_' . uniqid() . '.' . $extension;
                    
                    // Store file
                    $file->move(public_path('attachments'), $uniqueName);
                    $attachments[] = $uniqueName;
                }
            }

            // Handle medical history
            $medicalHistory = null;
            if (!empty($validated['medical_history'])) {
                if (count($validated['medical_history']) > 1 && in_array('none', $validated['medical_history'])) {
                    $validated['medical_history'] = array_diff($validated['medical_history'], ['none']);
                }
                $medicalHistory = implode(',', $validated['medical_history']);
            }

            // Create report
            $report = Report::create([
                'patient_id' => $validated['patient_id'],
                'doctor_id' => auth()->id(),
                'title' => $validated['title'],
                'report_date' => $validated['report_date'],
                'blood_pressure_systolic' => $validated['blood_pressure_systolic'],
                'blood_pressure_diastolic' => $validated['blood_pressure_diastolic'],
                'heart_rate' => $validated['heart_rate'],
                'temperature' => $validated['temperature'],
                'respiratory_rate' => $validated['respiratory_rate'],
                'notes' => $validated['notes'],
                'weight' => $validated['weight'],
                'height' => $validated['height'],
                'symptoms' => $validated['symptoms'],
                'examination_findings' => $validated['examination_findings'],
                'lab_results' => $validated['lab_results'],
                'diagnosis' => $validated['diagnosis'],
                'treatment_plan' => $validated['treatment_plan'],
                'description' => $validated['description'],
                'medical_history' => $medicalHistory,
                'medications' => $validated['medications'],
                'follow_up_instructions' => $validated['follow_up_instructions'],
                'follow_up_date' => $validated['follow_up_date'],
                'attachments' => $attachments,
                'status' => 'completed'
            ]);

            return redirect()
                ->route('doctor.reportList', $report->patient_id)
                ->with('success', 'Medical report created successfully!');

        } catch (\Exception $e) {
            // Clean up any uploaded files if there's an error
            foreach ($attachments as $file) {
                if (file_exists(public_path('attachments/' . $file))) {
                    unlink(public_path('attachments/' . $file));
                }
            }
            
            return back()
                ->withInput()
                ->with('error', 'Error creating report: ' . $e->getMessage());
        }
    }

    public function editReport(Report $report)
    {
        // Get the associated appointment and patient
        $appointment = Appointment::with('patient')
            ->where('patient_id', $report->patient_id)
            ->latest()
            ->firstOrFail();

        return view('doctor.editReport', compact('report', 'appointment'));
    }

    public function updateReport(Request $request, Report $report)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'title' => 'required|max:255',
            'report_date' => 'required|date',
            
            // Vital Signs
            'blood_pressure_systolic' => 'nullable|numeric|between:0,300',
            'blood_pressure_diastolic' => 'nullable|numeric|between:0,300',
            'heart_rate' => 'nullable|numeric|between:0,300',
            'temperature' => 'nullable|numeric|between:20,45',
            'respiratory_rate' => 'nullable|numeric|between:0,100',
            'notes' => 'nullable|string',
            
            // Measurements
            'weight' => 'nullable|numeric|between:0,999.99',
            'height' => 'nullable|numeric|between:0,999.99',
            
            // Clinical Information
            'symptoms' => 'required|string',
            'examination_findings' => 'required|string',
            'lab_results' => 'nullable|string',
            
            // Diagnosis & Treatment
            'diagnosis' => 'required|string',
            'treatment_plan' => 'required|string',
            'description' => 'nullable|string',
            
            // Medical History
            'medical_history' => 'nullable|array',
            'medical_history.*' => 'string|in:none,allergy,diabetes,hypertension,others',
            
            // Medications
            'medications' => 'nullable|string',
            
            // Follow-up
            'follow_up_instructions' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            
            // Attachments
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'remove_attachments' => 'nullable|array',
            'remove_attachments.*' => 'string'
        ]);

        try {
            // Get current attachments
            $currentAttachments = $report->attachments ?? [];

            // Remove attachments that were checked for removal
            if ($request->has('remove_attachments')) {
                $currentAttachments = array_values(
                    array_diff($currentAttachments, $request->remove_attachments)
                );
            }

            // Add new attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('attachments'), $fileName);
                    $currentAttachments[] = $fileName;
                }
            }

            // Update the report with new attachments array
            $report->attachments = $currentAttachments;
            
            // Update other fields
            $report->title = $validated['title'];
            $report->report_date = $validated['report_date'];
            $report->blood_pressure_systolic = $validated['blood_pressure_systolic'];
            $report->blood_pressure_diastolic = $validated['blood_pressure_diastolic'];
            $report->heart_rate = $validated['heart_rate'];
            $report->temperature = $validated['temperature'];
            $report->respiratory_rate = $validated['respiratory_rate'];
            $report->notes = $validated['notes'];
            $report->weight = $validated['weight'];
            $report->height = $validated['height'];
            $report->symptoms = $validated['symptoms'];
            $report->examination_findings = $validated['examination_findings'];
            $report->lab_results = $validated['lab_results'];
            $report->diagnosis = $validated['diagnosis'];
            $report->treatment_plan = $validated['treatment_plan'];
            $report->description = $validated['description'];
            $report->medical_history = $validated['medical_history'];
            $report->medications = $validated['medications'];
            $report->follow_up_instructions = $validated['follow_up_instructions'];
            $report->follow_up_date = $validated['follow_up_date'];

            $report->save();

            return redirect()
                ->route('doctor.reportList', $report->patient_id)
                ->with('success', 'Medical report updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Error updating report:', [
                'report_id' => $report->id,
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Error updating report: ' . $e->getMessage());
        }
    }

    public function destroy(Report $report)
    {
        try {
            // Delete associated attachments
            if (!empty($report->attachments)) {
                foreach ($report->attachments as $attachment) {
                    if (file_exists(public_path('attachments/' . $attachment))) {
                        unlink(public_path('attachments/' . $attachment));
                    }
                }
            }

            // Delete the report
            $report->delete();

            return redirect()
                ->route('doctor.reportList', $report->patient_id)
                ->with('success', 'Report deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting report: ' . $e->getMessage());
        }
    }

    public function show($patientId)
    {
        try {
            // Find the report with the given ID
            $report = Report::findOrFail($patientId);
            
            // Get the associated appointment and patient
            $appointment = Appointment::where('patient_id', $report->patient_id)
                ->with('patient')
                ->latest()
                ->firstOrFail();

            // If no appointment exists, create a temporary one with patient data
            if (!$appointment) {
                $patient = User::findOrFail($report->patient_id);
                $appointment = new Appointment();
                $appointment->patient = $patient;
                $appointment->patient_id = $report->patient_id;
            }

            return view('doctor.viewReport', compact('report', 'appointment'));
            
        } catch (\Exception $e) {
            \Log::error('Error viewing report:', [
                'report_id' => $patientId,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Could not find the requested report.');
        }
    }

    public function printReport($patientId)
    {
        try {
            // Find the report with the given ID
            $report = Report::findOrFail($patientId);
            
            // Get the associated appointment and patient
            $appointment = Appointment::where('patient_id', $report->patient_id)
                ->with('patient')
                ->latest()
                ->firstOrFail();

            // If no appointment exists, create a temporary one with patient data
            if (!$appointment) {
                $patient = User::findOrFail($report->patient_id);
                $appointment = new Appointment();
                $appointment->patient = $patient;
                $appointment->patient_id = $report->patient_id;
            }

            return view('doctor.printReport', compact('report', 'appointment'));
            
        } catch (\Exception $e) {
            \Log::error('Error generating print report:', [
                'report_id' => $patientId,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Could not generate print report.');
        }
    }
}