<?php

namespace App\Http\Controllers;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
// use App\Models\Role;
// use Spatie\Permission\Traits\HasPermissions;

use Illuminate\Http\Request;

class PermissionController extends Controller
{
    // use HasPermissions;

    public function __construct()
    {
        $this->middleware('role_or_permission:permission-list', ['only' => ['index']]);
        $this->middleware('role_or_permission:permission-create', ['only' => ['store']]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role_id = $request->role_id ?? 1;
        $roles = Role::get();
        return view('permission.index',[
            'roles' => $roles,
            'role_id' => $role_id,
            'list_url' => 'permission',
            'title' => 'Role And Permission Management'
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        foreach ($request->permission as $k => $p) {
            Permission::updateOrCreate(
                ['name' => $p],
                ['name' => $p]
            );
        }

        $permission = Permission::whereIn('name',$request->permission)->pluck('id')->toArray();
        $role = Role::find($request->role_id);
        $role->syncPermissions($permission);
        auth()->user()->assignRole(auth()->user()->role_id);
        return redirect()->route('permission.index',['role_id'=>$request->role_id])
                         ->with('success','Permission was saved successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
