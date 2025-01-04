<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FamilyMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FamilyMemberController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'relationship' => 'required|exists:users,id',
            'relation' => 'required|string'
        ]);

        // Check if a record already exists for this user
        $familyMember = FamilyMember::where('user_id', $request->user_id)->first();

        if ($familyMember) {
            // If exists, update the arrays
            $relationships = array_filter(explode(',', $familyMember->relationship));
            $relations = array_filter(explode(',', $familyMember->relation ?: ''));

            if (!in_array($request->relationship, $relationships)) {
                $relationships[] = $request->relationship;
                $relations[] = $request->relation;
                
                $familyMember->relationship = implode(',', array_values($relationships));
                $familyMember->relation = implode(',', array_values($relations));
                $familyMember->save();
            }
        } else {
            // If doesn't exist, create new record
            FamilyMember::create([
                'user_id' => $request->user_id,
                'relationship' => $request->relationship,
                'relation' => $request->relation
            ]);
        }

        return redirect()->back()->with('success', 'Family member added successfully');
    }

    public function destroy($id)
    {
        $familyMember = FamilyMember::findOrFail($id);
        $familyMember->delete();
        return redirect()->back()->with('success', 'Family member removed successfully');
    }

    public function getFamilyMembers($userId)
    {
        $familyMember = FamilyMember::where('user_id', $userId)->first();
        
        if (!$familyMember || empty($familyMember->relationship)) {
            return collect();
        }

        $relationshipIds = array_filter(explode(',', $familyMember->relationship));
        $relations = array_filter(explode(',', $familyMember->relation ?: ''));
        $familyMembers = collect();

        foreach ($relationshipIds as $index => $id) {
            $member = User::where('id', $id)
                         ->select('id', 'name', 'email', 'contact_number')
                         ->first();
            
            if ($member) {
                $familyMembers->push([
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'contact_number' => $member->contact_number,
                    'relation' => isset($relations[$index]) ? $relations[$index] : 'Unknown'
                ]);
            }
        }

        return $familyMembers;
    }

    public function updateRelation(Request $request)
    {
        $request->validate([
            'family_member_id' => 'required|exists:family_members,id',
            'relation' => 'required|string',
            'index' => 'required|integer'
        ]);

        $familyMember = FamilyMember::findOrFail($request->family_member_id);
        
        // Get the arrays
        $relations = array_filter(explode(',', $familyMember->relation ?: ''));
        if (empty($relations)) {
            $relations = array_fill(0, count(explode(',', $familyMember->relationship)), 'Unknown');
        }
        
        // Update the relation at the specified index
        $relations[$request->index] = $request->relation;
        
        // Save back to database
        $familyMember->relation = implode(',', array_values($relations));
        $familyMember->save();

        return redirect()->back()->with('success', 'Relation updated successfully');
    }

    public function removeFamilyMember(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'relationship_id' => 'required|exists:users,id',
            'index' => 'required|integer'
        ]);

        $familyMember = FamilyMember::where('user_id', $request->user_id)->first();
        
        if ($familyMember) {
            $relationships = array_filter(explode(',', $familyMember->relationship));
            $relations = array_filter(explode(',', $familyMember->relation ?: ''));
            
            // Remove elements at the specified index
            unset($relationships[$request->index]);
            if (isset($relations[$request->index])) {
                unset($relations[$request->index]);
            }
            
            if (empty($relationships)) {
                $familyMember->delete();
            } else {
                $familyMember->relationship = implode(',', array_values($relationships));
                $familyMember->relation = implode(',', array_values($relations));
                $familyMember->save();
            }
        }

        return redirect()->back()->with('success', 'Family member removed successfully');
    }
}
