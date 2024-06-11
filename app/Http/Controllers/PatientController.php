<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patient = User::where('userType', 'patient')->get();
        if ($patient->count() > 0) {
            return response()->json([
                'status' => 200,
                'PatientAccounts' => $patient
            ],200);
        } else {
            return response()->json([
                'status' => 404,
                'Message' => 'No Records Found'
            ],404);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), 
        [
            'name' => 'required | string | max:191',
            'email' => 'required|string|email|max:191|unique:users,email',
            'password' => 'required | string | min:8 | max:191'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->messages()
            ], 400);
        } 

        try {
            User::create([
                'name'=> $request->name,
                'email'=> $request->email,
                'password'=> $request->password,
                'userType'=> 'patient'
            ]);

            return response()->json([
                'status' => 200,
                'message' => "Account Created Succesfully"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => "Something went wrong: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the user by ID
        $user = User::find($id);
    
        // Check if the user exists
        if ($user) {
            // Check if the userType is 'doctor'
            if ($user->userType === 'patient') {
                return response()->json([
                    'status' => 200,
                    'patient' => $user
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'The user with ID '.$id.' is not a patient.'
                ], 404);
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No user found with ID '.$id
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = Validator::make($request->all(), 
        [
            'name' => 'required | string | max:191',
            'email' => 'required|string|email|max:191|',
            'password' => 'required | string | min:8 | max:191',
            'userType' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->messages()
            ], 400);
        } 

        try {
            $account = User::find($id);

            $account->update([
                'name'=> $request->name,
                'email'=> $request->email,
                'password'=> $request->password,
                'userType'=> $request->userType
            ]);

            return response()->json([
                'status' => 200,
                'message' => "Account updated Succesfully"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => "Something went wrong: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return response()->json([
                'status' => 200,
                'message' => "Account deleted succesfully"
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No valid account to delete."
            ], 404);
        }
    }
}