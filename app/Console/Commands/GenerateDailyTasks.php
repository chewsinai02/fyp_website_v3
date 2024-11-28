<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Task;
use App\Models\NurseSchedule;
use Carbon\Carbon;

class GenerateDailyTasks extends Command
{
    protected $signature = 'tasks:generate-daily';
    protected $description = 'Generate daily tasks for nurses based on their patients';

    public function handle()
    {
        $today = Carbon::today();
        
        // Get today's nurse schedules
        $schedules = NurseSchedule::with(['nurse', 'room.beds.patient'])
            ->whereDate('date', $today)
            ->get();

        foreach ($schedules as $schedule) {
            if (!$schedule->room) continue;

            foreach ($schedule->room->beds as $bed) {
                if (!$bed->patient) continue;

                // Generate standard tasks for each patient
                $this->generatePatientTasks($schedule->nurse_id, $bed->patient_id, $today);
            }
        }

        $this->info('Daily tasks generated successfully!');
    }

    private function generatePatientTasks($nurseId, $patientId, $date)
    {
        $standardTasks = [
            [
                'title' => 'Vital Signs Check',
                'description' => 'Check and record patient vital signs',
                'priority' => 'high',
                'due_date' => $date->copy()->addHours(2)
            ],
            [
                'title' => 'Medication Administration',
                'description' => 'Administer prescribed medications',
                'priority' => 'high',
                'due_date' => $date->copy()->addHours(4)
            ],
            [
                'title' => 'Patient Assessment',
                'description' => 'Conduct routine patient assessment',
                'priority' => 'medium',
                'due_date' => $date->copy()->addHours(6)
            ]
        ];

        foreach ($standardTasks as $task) {
            Task::create([
                'nurse_id' => $nurseId,
                'patient_id' => $patientId,
                'title' => $task['title'],
                'description' => $task['description'],
                'priority' => $task['priority'],
                'due_date' => $task['due_date'],
                'status' => 'pending'
            ]);
        }
    }
} 