<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionController extends Controller
{

    public function roles(){
        $datas['items']=Role::latest()->get();

        return view('roles', $datas);

    }

    public function createRoleget(){

        $datas['items']=Permission::all();

        return view('roles_create', $datas);
    }

    public function createRole(Request $request){
        $input = $request->all();
        $rules = array(
            'permissions' => 'required',
            'name'      => 'required|max:100'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return redirect()->route('roles.create')->with('error', 'Incomplete request. Kindly check and try again');
        }

        $role=Role::create(['name' => $input['name'], 'guard_name' => 'web']);

        $role->givePermissionTo($input['permissions']);

        return redirect()->route('roles.list')->with(['success'=> 'Role created successfully.']);

    }

    public function role($id){

        $datas['role']=Role::find($id);
        $datas['mypermissions']=$datas['role']->permissions;
        $datas['mypermissions2']=$datas['role']->permissions->pluck('name');
        $datas['permissions']=Permission::latest()->get();

        return view('roles_edit', $datas);
    }

    public function updateRole($id, Request $request){
        $input = $request->all();
        $rules = array(
            'permissions' => 'required',
            'name'      => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return redirect()->route('roles.list')->with('error', 'Incomplete request. Kindly check and try again');
        }


        $role=Role::find($id);

        if(!$role){
            return redirect()->route('roles.list')->with(['error'=> 'Role does not exist']);
        }

        $role->name=$input['name'];
        $role->save();

        $role->syncPermissions($input['permissions']);

        return redirect()->route('roles.list')->with(['success'=> 'Role updated successfully']);
    }

    public function deleteRole($id, Request $request){

        $role=Role::find($id);

        if(!$role){
            return redirect()->route('roles.list')->with(['error'=> 'Role does not exist']);
        }

        $role->syncPermissions([]);

        $role->delete();

        return redirect()->route('roles.list')->with(['success'=> 'Role deleted successfully']);
    }

    public function permissions(){
        $datas['items']=Permission::latest()->get();

    }


    public function userole(Request $request)
    {
        $userlist = User::latest()->select('id', 'user_name')->paginate(50);
        $admins=User::where('status', 'admin')->orWhere('status', 'superadmin')->get();
        $roles=Role::all();
        $i=1;

        return view('role', compact('userlist', 'admins', 'roles', 'i'));
    }

    public function updateuserole(Request $request)
    {
        $user = user::where('id', $request->id)->orwhere('phoneno', $request->id)->orwhere('email', $request->id)->first();

        if(!$user){
            return redirect()->route('admin.role')->with(['danger', 'User does not exist']);
        }

        $user->status = "admin";
        $user->save();

//        $user->assignRole($request->role);
        $user->syncRoles($request->role);

        return redirect()->route('admin.role')->with('success', $user->user_name . " role has been change to " . $request->role);
    }


    public function revokeUserole(Request $request)
    {
        $user = user::where('id', $request->id)->orwhere('phoneno', $request->id)->orwhere('email', $request->id)->first();

        if(!$user){
            return redirect()->route('admin.role')->with(['danger', 'User does not exist']);
        }

        $user->status = "client";
        $user->save();

        return redirect()->route('admin.role')->with('success', $user->user_name . " role has been change to " . $request->role);
    }



}
