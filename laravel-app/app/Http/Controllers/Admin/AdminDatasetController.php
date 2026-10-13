<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class AdminDatasetController extends Controller
{
    public function index(): View
    {
        $datasets = Dataset::with(['uploader', 'mlModels'])->latest()->paginate(10);
        return view('admin.datasets.index', compact('datasets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'dataset_file' => 'required|file|mimes:csv,txt|max:51200', // max 50MB
            'description' => 'nullable|string|max:500',
        ]);

        $file = $request->file('dataset_file');
        $originalFilename = $file->getClientOriginalName();
        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalFilename);

        $targetDir = base_path('../ml-service/datasets');
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $file->move($targetDir, $safeName);
        $fullPath = $targetDir . DIRECTORY_SEPARATOR . $safeName;

        // Parse CSV to determine row count and column count
        $rowCount = 0;
        $colCount = 0;
        $headers = [];

        if (($handle = fopen($fullPath, 'r')) !== false) {
            if (($headerRow = fgetcsv($handle, 2000, ',')) !== false) {
                $headers = $headerRow;
                $colCount = count($headerRow);
            }
            while (fgetcsv($handle, 2000, ',') !== false) {
                $rowCount++;
            }
            fclose($handle);
        }

        Dataset::create([
            'name' => $request->name,
            'filename' => $safeName,
            'file_path' => $fullPath,
            'row_count' => $rowCount,
            'column_count' => $colCount,
            'duplicate_count' => 0,
            'missing_values_count' => 0,
            'invalid_rows_count' => 0,
            'status' => 'validated',
            'description' => $request->description,
            'uploaded_by' => Auth::id(),
        ]);

        return back()->with('success', "Dataset '{$request->name}' ({$rowCount} rows, {$colCount} columns) uploaded and validated successfully.");
    }

    public function preview(int $id): JsonResponse
    {
        $dataset = Dataset::findOrFail($id);
        $filePath = $dataset->file_path;

        if (!File::exists($filePath)) {
            return response()->json(['error' => 'Dataset file not found on disk.'], 404);
        }

        $headers = [];
        $rows = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            if (($headerRow = fgetcsv($handle, 2000, ',')) !== false) {
                $headers = $headerRow;
            }
            $count = 0;
            while (($data = fgetcsv($handle, 2000, ',')) !== false && $count < 10) {
                $rows[] = $data;
                $count++;
            }
            fclose($handle);
        }

        return response()->json([
            'name' => $dataset->name,
            'headers' => $headers,
            'rows' => $rows,
            'row_count' => $dataset->row_count,
            'column_count' => $dataset->column_count,
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $dataset = Dataset::findOrFail($id);
        if (File::exists($dataset->file_path)) {
            File::delete($dataset->file_path);
        }
        $dataset->delete();

        return back()->with('success', 'Dataset record and file deleted.');
    }
}
