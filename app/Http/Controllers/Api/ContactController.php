<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Contact;
use App\Models\Group;

use App\Http\Resources\ResponseResource;
use App\Services\InputSanitizer;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class ContactController extends Controller
{
    protected $sanitizer;

    public function __construct(InputSanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'nullable|integer',
            'search' => 'nullable|string',
            'iPage' => 'integer',
            'iTake' => 'integer'
        ]);
        if ($validator->fails()) {
            // return response()->json(['error' => $validator->errors()], 400);
            
            $errors = $validator->errors();
            $firstError = $errors->first();
            
            return response()->json(new ResponseResource(false, $firstError, null), 400);
        }
        
        $group_id = $request->query('group_id');
        $search = $request->query('search');
        $iPage = $request->query('iPage', 1);
        $iTake = $request->query('iTake', 10);

        $query = Contact::query();
        if ($group_id) {
            $query->where('group_id', $group_id);
        }
        if ($search) {
            $query->whereAny([
                'name',
                'address',
                'phone_number',
            ], 'LIKE', '%' . $search . '%');
        }

        $query->orderBy('id');
        $contacts = $query->paginate($iTake, ['*'], 'iPage', $iPage);

        return new ResponseResource(true, 'List Data Contact', $contacts);
    }

    public function show($id)
    {
        $contact = Contact::find($id);

        if ($contact) {
            return new ResponseResource(true, 'Data Contact', $contact);
        } else {
            return response()->json(new ResponseResource(false, 'Contact not found', null), 404);
        }
    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'phone_number' => 'required',
            'group_name' => 'nullable',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors();
            $firstError = $errors->first();
            
            return response()->json(new ResponseResource(false, $firstError, null), 422);
        }

        DB::beginTransaction();
        try {
            if (!empty($request->group_name)) {
                $group = Group::firstOrCreate(['name' => $this->sanitizer->sanitize($request->group_name)]);
                $groupId = $group->id;
            } else {
                $groupId = 0;
            }

            //create contact
            $contact = Contact::create([
                'name'          =>  $this->sanitizer->sanitize($request->name),
                'address'       =>  $this->sanitizer->sanitize($request->address),
                'phone_number'  =>  $this->sanitizer->sanitize($request->phone_number),
                'group_id'      =>  $groupId,
            ]);

            DB::commit();
            //return response
            return new ResponseResource(true, 'Contact Berhasil Ditambahkan!', $contact);
        } catch (\Exception $e) {
            DB::rollBack();

            // Return an error response
            return response()->json(new ResponseResource(false, 'Failed to create contact', $e->getMessage()), 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'phone_number' => 'required',
            'group_name' => 'nullable',
        ]);

        if ($validator->fails()) {
            // return response()->json(['errors' => $validator->errors()], 400);
            $errors = $validator->errors();
            $firstError = $errors->first();
            
            return response()->json(new ResponseResource(false, $firstError, null), 400);
        }

        $contact = Contact::findOrFail($id);
        if (!$contact) {
            return response()->json(new ResponseResource(false, 'Contact not found', null), 404);
        }
        
        $group = Group::firstOrCreate(['name' => $request->group_name]);
        $contact->update($request->all());

        return response()->json($contact);
    }

    public function destroy($id)
    {
        $contact = Contact::find($id);
        
        if ($contact) {
            $contact->delete();
            return new ResponseResource(true, 'Deleted 1 Contact', null);
        } else {
            return response()->json(new ResponseResource(false, 'Contact not found', null), 404);
        }
    }

    public function postContactList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.name' => 'required|string',
            '*.address' => 'required|string',
            '*.phone_number' => 'required|string',
            '*.group_name' => 'string',
        ]);

        if ($validator->fails()) {
            // return response()->json(['success' => false, 'message' => 'Validation errors', 'data' => $validator->errors()], 422);
            $errors = $validator->errors();
            $firstError = $errors->first();
            
            return response()->json(new ResponseResource(false, $firstError, null), 400);
        }

        $contactsData = $request->all();

        $contacts = [];
        foreach ($contactsData as $data) {
            $group = Group::firstOrCreate(['name' => $data['group_name']]);

            $contact = new Contact([
                'name' => $data['name'],
                'address' => $data['address'],
                'phone_number' => $data['phone_number'],
                'group_id' => $group->id,
            ]);

            $contact->save();
            $contacts[] = $contact;
        }

        return new ResponseResource(true, 'List Contacts saved successfully', $contacts);
    }
}
