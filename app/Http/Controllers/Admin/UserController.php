<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;




class UserController extends Controller
{

	public function index(){
		$users = User::latest()->where('user_type', 'customer')->paginate(5);
		return view('admin.users.index', compact('users'))->with('i',(request()->input('page', 1)- 1) * 5);
	}

	public function create(){
		return view('admin.users.create');
	}

	public function store(Request $request){
		$data = $request->all();
		$data['password'] = Hash::make($request['password']);

		$request->validate([
            'name'=> 'required',
		    'email' => 'required|email|unique:users,email',
		    'password' => 'required|min:6',
		]);
       
	    User::create($data);
	    return redirect()->route('admin.user')->with('success', 'User created successfully');

	}

	public function edit($id){

       $user = User::where('id', $id)->first();
       
		
		return view('admin.users.edit',compact('user'));

	}

	public function update(Request $request){

        $object = User::findOrFail($request->id);
		$request->validate([
            'name'=> 'required',
		    'email' => 'required',
		    'password' => 'required|min:6',
            Rule::unique('users')->ignore($object->id),]);
           $object->name = $request->name;
           $object->password = Hash::make($request->password);
           $object->email = $request->email;
	       $saveData = $object->save();
	    return redirect()->route('admin.user')->with('success', 'User update successfully');
	}
    
    public function delete($id){
           $getUser = User::findOrFail($id);
           $getUser->delete();
           return redirect()->back();
    }
}