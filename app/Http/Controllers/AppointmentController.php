<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $doctorID = $request->input('doctorID');
        $patientID = $request->input('patientID');

        $query = Appointment::join('users as patients', 'appointments.patientID', '=', 'patients.id')
            ->join('users as doctors', 'appointments.doctorID', '=', 'doctors.id')
            ->select('appointments.*', 'patients.name as PatientName', 'doctors.name as DoctorName');

        if ($doctorID) {
            $query->where('appointments.doctorID', $doctorID);
        }

        if ($patientID) {
            $query->where('appointments.patientID', $patientID);
        }

        $appointments = $query->get();

        if ($appointments->count() > 0) {
            return response()->json([
                'status' => 200,
                'Appointments' => $appointments
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'Message' => 'No Records Found'
            ], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'time' => 'required',
                'patientID' => 'required',
                'doctorID' => 'required',
                'status' => 'required'
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->messages()
            ], 400);
        }

        try {
            Appointment::create([
                'time' => $request->time,
                'patientID' => $request->patientID,
                'doctorID' => $request->doctorID,
                'status' => $request->status
            ]);

            return response()->json([
                'status' => 200,
                'message' => "Appointment Created Succesfully"
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
        $appointment = Appointment::find($id);

        if ($appointment) {
            return response()->json([
                'status' => 200,
                'appointment' => $appointment
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No user found."
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'time' => 'required',
                'patientID' => 'required',
                'doctorID' => 'required',
                'status' => 'required'
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->messages()
            ], 400);
        }

        try {
            $appointment = Appointment::find($id);

            $appointment->update([
                'time' => $request->time,
                'patientID' => $request->patientID,
                'doctorID' => $request->doctorID,
                'status' => $request->status
            ]);

            return response()->json([
                'status' => 200,
                'message' => "Appointment updated Succesfully"
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
        $appointment = Appointment::find($id);

        if ($appointment) {
            $appointment->delete();
            return response()->json([
                'status' => 200,
                'message' => "Appointment deleted succesfully"
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No valid appointment to delete."
            ], 404);
        }
    }
}
