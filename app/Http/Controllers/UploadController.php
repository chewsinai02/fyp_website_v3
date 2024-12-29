<?php

namespace App\Http\Controllers;

use App\Helpers\FirebaseStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    protected $firebaseStorage;
    
    public function __construct(FirebaseStorage $firebaseStorage)
    {
        $this->firebaseStorage = $firebaseStorage;
    }
    
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|image|max:5120', // 5MB max
            'type' => 'required|string|in:profile,medical,room',
            'id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        
        try {
            $file = $request->file('file');
            $type = $request->input('type');
            $id = $request->input('id');

            switch ($type) {
                case 'profile':
                    $url = $this->firebaseStorage->uploadProfilePicture($file, $id);
                    break;
                case 'medical':
                    $url = $this->firebaseStorage->uploadMedicalImage($file, $id);
                    break;
                case 'room':
                    $url = $this->firebaseStorage->uploadRoomImage($file, $id);
                    break;
                default:
                    throw new \Exception('Invalid upload type');
            }
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'message' => 'File uploaded successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'path' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $this->firebaseStorage->delete($request->input('path'));
            
            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }
} 