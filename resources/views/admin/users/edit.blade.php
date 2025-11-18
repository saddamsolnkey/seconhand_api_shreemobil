@extends('admin.master')

@section('content')

<style>
    
    .push-top {
      margin-top: 50px;
    }
</style>

<div class="content-wrapper">
	<div class="content-header">
    <div class="container-fluid">
<div class="card push-top">
  <div class="card-header">
    <h1>Edit User </h1>
    <a class="float-right btn btn-primary" href="{{ route('admin.user') }}"> Back</a>
  </div>

 <div class="row">
  <div class="card-body">
  	   
   <form method="post" action="{{route('admin.user.update',$user->id)}}">
          <div class="form-group form-row">
          	<div class="col-6">
              @csrf
              <label for="mobile_name">Name</label>
              <input type="text" value="{{$user->name}}" class="form-control" name="name"/>
              @error('name')
              <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="form-group col-6">
              <label for="email">Email</label>
              <input type="text" value="{{$user->email}}" class="form-control" name="email"/>
              @error('email')
              <div class="alert alert-danger mt-1 mb-1">{{$message}}</div>
              @enderror
            </div>

            <div class="form-group col-6">
	          <label for="password">Password</label>
	              <input type="password" value="{{$user->password}}" id="password" class="form-control" name="password" required>
	              @error('password'))
	                  <span class="text-danger">{{ $message }}</span>
	              @enderror
	       </div>

           </div>
         <div class="form-row justify-content-center">
         <button type="submit" class="btn btn-block btn-danger col-4">Create User</button>
         </div>
         </form>

         </div>
</div>
</div>
</div>
</div>
</div>
 
@endsection