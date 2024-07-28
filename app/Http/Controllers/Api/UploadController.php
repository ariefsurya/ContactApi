<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Imports\ContactsImport;
use App\Http\Resources\ResponseResource;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UploadController extends Controller
{
    public function uploadJson(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:json'
        ]);

        $file = $request->file('file');
        $content = file_get_contents($file);
        $json = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(new ResponseResource(false, 'Invalid JSON file', null), 400);
        }

        return new ResponseResource(true, 'List Data Upload Contact', $json);
    }


    public function uploadExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new ContactsImport();

        Excel::import($import, $request->file('file'));

        $data = $import->getData();

        return new ResponseResource(true, 'List Data Upload Contact', $data);
    }
}