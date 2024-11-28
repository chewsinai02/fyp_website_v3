<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class FirebaseController extends Controller
{
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    // Create/Update Data
    public function store()
    {
        $reference = $this->database->getReference('users/1');
        $reference->set([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        return response()->json(['message' => 'Data stored successfully']);
    }

    // Read Data
    public function show()
    {
        $reference = $this->database->getReference('users/1');
        $value = $reference->getValue();
        
        return response()->json($value);
    }

    // Update Specific Fields
    public function update()
    {
        $reference = $this->database->getReference('users/1');
        $reference->update([
            'name' => 'Jane Doe'
        ]);

        return response()->json(['message' => 'Data updated successfully']);
    }

    // Delete Data
    public function delete()
    {
        $reference = $this->database->getReference('users/1');
        $reference->remove();

        return response()->json(['message' => 'Data deleted successfully']);
    }

    // Working with Lists
    public function pushToList()
    {
        $postRef = $this->database->getReference('posts')->push([
            'title' => 'New Post',
            'content' => 'Post content'
        ]);

        return response()->json(['post_id' => $postRef->getKey()]);
    }

    // Query Data
    public function query()
    {
        $reference = $this->database->getReference('users')
            ->orderByChild('name')
            ->equalTo('John Doe')
            ->limitToFirst(10);
        
        $value = $reference->getValue();
        
        return response()->json($value);
    }
}
