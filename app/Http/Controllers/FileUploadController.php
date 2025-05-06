<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCsvUpload;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:50240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();
        $storedFilename = Str::uuid() . '.csv';

        // Store the file
        $file->storeAs('', $storedFilename);

        // Create upload record
        $fileUpload = FileUpload::create([
            'original_filename' => $originalFilename,
            'stored_filename' => $storedFilename,
            'status' => 'pending',
        ]);

        // Dispatch processing job
        ProcessCsvUpload::dispatch($fileUpload);

        return response()->json([
            'message' => 'File uploaded successfully',
            'upload_id' => $fileUpload->id,
        ]);
    }

    public function index()
    {
        $uploads = FileUpload::latest()->get()->map(function ($upload) {
            return [
                'id' => $upload->id,
                'filename' => $upload->original_filename,
                'status' => $upload->status,
                'uploaded_at' => $upload->created_at->diffForHumans(),
                'processed_rows' => $upload->processed_rows,
                'total_rows' => $upload->total_rows,
                'error_message' => $upload->error_message,
            ];
        });

        return response()->json($uploads);
    }

    public function show(FileUpload $fileUpload)
    {
        return response()->json([
            'id' => $fileUpload->id,
            'filename' => $fileUpload->original_filename,
            'status' => $fileUpload->status,
            'uploaded_at' => $fileUpload->created_at->diffForHumans(),
            'processed_rows' => $fileUpload->processed_rows,
            'total_rows' => $fileUpload->total_rows,
            'error_message' => $fileUpload->error_message,
        ]);
    }
}