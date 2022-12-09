<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        DB::table('audits')->insert(
            ['user_id' => auth()->user()->id, 'user_type' => 'App\Models\User', 'event' => 'created', 'auditable_id' => $role->id, 'auditable_type' => 'App\Models\Role', 'tags' => 'Role created Successfully',  'old_values'=> '[]', 'new_values'=> '{"name" : "'.$input['name'].'"}',  'ip_address' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'created_at'=>Carbon::now(), 'updated_at'=>Carbon::now()]
        );

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

        DB::table('audits')->insert(
            ['user_id' => auth()->user()->id, 'user_type' => 'App\Models\User', 'event' => 'updated', 'auditable_id' => $role->id, 'auditable_type' => 'App\Models\Role', 'tags' => $role->name .' Role updated Successfully',  'old_values'=> '[]', 'new_values'=> '{"name" : "'.$input['name'].'"}',  'ip_address' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'created_at'=>Carbon::now(), 'updated_at'=>Carbon::now()]
        );

        return redirect()->route('roles.list')->with(['success'=> 'Role updated successfully']);
    }

    public function deleteRole($id, Request $request){

        $role=Role::find($id);

        if(!$role){
            return redirect()->route('roles.list')->with(['error'=> 'Role does not exist']);
        }

        $role->syncPermissions([]);

        $role->delete();

        DB::table('audits')->insert(
            ['user_id' => auth()->user()->id, 'user_type' => 'App\Models\User', 'event' => 'deleted', 'auditable_id' => $role->id, 'auditable_type' => 'App\Models\Role', 'tags' => $role->name .' Role deleted Successfully',  'old_values'=> '[]', 'new_values'=> '[]',  'ip_address' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'created_at'=>Carbon::now(), 'updated_at'=>Carbon::now()]
        );

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
        $user = user::where('id', $request->id)->orwhere('phoneno', $request->id)->orwhere('email', $request->id)->orwhere('user_name', $request->id)->first();

        if(!$user){
            return redirect()->route('admin.role')->with(['error', 'User does not exist']);
        }

        $user->status = "admin";
        $user->save();

//        $user->assignRole($request->role);
        $user->syncRoles($request->role);


        DB::table('audits')->insert(
            ['user_id' => auth()->user()->id, 'user_type' => 'App\Models\User', 'event' => 'updated', 'auditable_id' => $user->id, 'auditable_type' => 'App\Models\Role', 'tags' => $user->user_name .' got a role of '.$request->role,  'old_values'=> '[]', 'new_values'=> '[]',  'ip_address' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'created_at'=>Carbon::now(), 'updated_at'=>Carbon::now()]
        );

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


        DB::table('audits')->insert(
            ['user_id' => auth()->user()->id, 'user_type' => 'App\Models\User', 'event' => 'updated', 'auditable_id' => $user->id, 'auditable_type' => 'App\Models\Role', 'tags' => $user->user_name .' has been demoted from admin',  'old_values'=> '[]', 'new_values'=> '[]',  'ip_address' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'created_at'=>Carbon::now(), 'updated_at'=>Carbon::now()]
        );

        return redirect()->route('admin.role')->with('success', $user->user_name . " role has been change to " . $request->role);
    }



}
