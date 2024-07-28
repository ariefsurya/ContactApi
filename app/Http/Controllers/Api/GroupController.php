<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Group;

use App\Http\Resources\ResponseResource;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::all();
        return new ResponseResource(true, 'List Data Contact Group', $groups);
    }
}
