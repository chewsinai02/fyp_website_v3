<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Contract\Database;

class NurseCallController extends Controller
{
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function index()
    {
        try {
            $reference = $this->database->getReference('nurse_calls');
            $calls = $reference->getValue() ?? [];
            
            return view('nurse.calls', compact('calls'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateCallStatus($callId)
    {
        try {
            $reference = $this->database->getReference('nurse_calls/' . $callId);
            $reference->update([
                'status' => 'attended',
                'attended_at' => date('Y-m-d H:i:s')
            ]);

            return response()->json(['message' => 'Call status updated']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} 