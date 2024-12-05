<?php

namespace App\Traits;

use App\Models\Task;
use Carbon\Carbon;

trait TaskStatusCheck
{
    protected function updatePassedTasks()
    {
        try {
            $now = Carbon::now();
            
            // Update all tasks where due_date has passed and status is still pending
            Task::where('due_date', '<', $now)
                ->where('status', 'pending')
                ->update(['status' => 'passed']);

            return true;
        } catch (\Exception $e) {
            \Log::error('Error updating passed tasks: ' . $e->getMessage());
            return false;
        }
    }
} 