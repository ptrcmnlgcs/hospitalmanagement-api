<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $patientID = $request->input('patientID');

        $query = MedicalRecord::leftjoin('users as patients', 'medical_records.patientID', '=', 'patients.id')
        ->select('medical_records.*', 'patients.name as PatientName');

        if ($patientID) {
            $query->where('medical_records.patientID', $patientID);
        }

        $medicalRecords = $query->get();

        if ($medicalRecords->count() > 0) {
            return response()->json([
                'status' => 200,
                'MedicalRecords' => $medicalRecords
            ],200);
        } else {
            return response()->json([
                'status' => 404,
                'Message' => 'No Records Found'
            ],404); 
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(),
        [
            'patientID' => 'required',
            'RecordDetails' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->messages()
            ], 400);
        }

        try {
            MedicalRecord::create([
                'patientID' => $request->patientID,
                'RecordDetails' => $request->RecordDetails
            ]);

            return response()->json([
                'status' => 200,
                'message' => "Medical Record Created Succesfully"
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
        $medicalRecord = MedicalRecord::find($id);

        if ($medicalRecord) {
            return response()->json([
                'status' => 200,
                'medicalRecord' => $medicalRecord
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No record found."
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
            'patientID' => 'required',
            'RecordDetails' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->messages()
            ], 400);
        }

        try {
            $MedicalRecord = MedicalRecord::find($id);
            
            $MedicalRecord->update([
                'patientID' => $request->patientID,
                'RecordDetails' => $request->RecordDetails
            ]);

            return response()->json([
                'status' => 200,
                'message' => "Medical Record updated Succesfully"
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
        $medicalRecord = MedicalRecord::find($id);

        if ($medicalRecord) {
            $medicalRecord->delete();
            return response()->json([
                'status' => 200,
                'message' => "Medical Record deleted succesfully"
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No valid medical record to delete."
            ], 404);
        }
    }
}
