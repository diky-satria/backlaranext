<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page') ? $request->query('page') : 0;
        $limit = $request->query('limit') ? $request->query('limit') : 10;
        $search = $request->query('search') ? $request->query('search') : '';

        $search_db = $search ? "WHERE u.name LIKE '%".$search."%' OR u.email LIKE '%".$search."%' OR d.name LIKE '%".$search."%'" : "";
        
        $offset = $page * $limit;

        $total = DB::select("SELECT count(*) as total from users as u join divisions as d on u.division_id = d.id ".$search_db."");

        $total_page = ceil($total[0]->total / $limit);

        $data = DB::select("SELECT u.*, u.id as 'key', d.name as division_name FROM users as u join divisions as d on u.division_id = d.id ".$search_db." order by u.id desc limit ".$offset.",".$limit."");

        return response()->json([
            'message' => 'all user',
            'page' => $page,
            'limit' => $limit,
            'total_rows' => $total[0]->total,
            'total_page' => $total_page,
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $data = User::find($id);
        return response()->json(['message' => 'detail user', 'data' => $data], 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'division_id' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ],[
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.unique' => 'Email is registered',
            'division_id.required' => 'Division is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password min 6 characters',
            'password_confirmation.required' => 'Password confirmation is required',
            'password_confirmation.same' => 'Password confirmation not match'
        ]);

        $data = User::create([
            'name' => ucwords($request->input('name')),
            'email' => $request->input('email'),
            'division_id' => $request->input('division_id'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json(['message' => 'Register', 'data' => $data], 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'division_id' => 'required',
        ],[
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.unique' => 'Email is registered',
            'division_id.required' => 'Division is required',
        ]);

        $data = $user->update([
            'name' => ucwords($request->input('name')),
            'email' => $request->input('email'),
            'division_id' => $request->input('division_id'),
        ]);

        return response()->json(['message' => 'user update successfully', 'data' => $data], 200);
    }

    public function destroy($id)
    {
        $data = User::find($id);
        if(!$data){
            return response()->json(['message' => 'user not found'], 404);
        }
        
        $data->delete();
        return response()->json(['message' => 'user delete successfully', 'data' => $data], 200);
    }
}
