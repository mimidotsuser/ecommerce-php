<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{

    public function index()
    {
        $this->authorize('view', Role::class);

        return response()->json(["message" => "All roles",
            'users' => Role::with('permissions:id,name,description')->get()]);
    }


    public function store(Request $request)
    {
        $this->authorize('create', Role::class);


        //validate the request data
        $validated = $validated = $this->validate($request, [
            'name' => ['required', 'min:2', 'unique:roles,name', 'max:200'],
            'description' => ['sometimes', 'max:200'],
            'permissions' => ['required', 'array'],
            'permissions.*.id' => ['numeric', 'exists:permissions,id']
        ], ['name.unique' => 'Role with similar name already exist',
            'permissions.*.id.exists' => 'Invalid permission provided']);

        //create the role and then attach the permissions
        DB::beginTransaction();

        $role = new Role;
        $role->name = $request->get('name');
        $role->description = $request->get('description');
        $role->save();

        //attach the permissions
        $role->permissions()->attach(Arr::flatten($request->get('permissions')),
            ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::commit();

        //reload the permissions
        $role->load('permissions');

        return response()
            ->json(['message' => 'Role created successfully.',
                'role' => $role], 201);
    }




}
