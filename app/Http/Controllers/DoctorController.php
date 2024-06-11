<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = User::where('userType', 'doctor')->get();
        if ($doctors->count() > 0) {
            return response()->json([
                'status' => 200,
                'DoctorAccounts' => $doctors
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
                'userType'=> 'doctor'
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
            if ($user->userType === 'doctor') {
                return response()->json([
                    'status' => 200,
                    'doctor' => $user
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'The user with ID '.$id.' is not a doctor.'
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
            'password' => 'string | min:8 | max:191',
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
