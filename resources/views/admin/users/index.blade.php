@extends('admin.master')

@section('content')

   <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">User List</h1>
          </div><!-- /.col -->
           <div class="col-sm-6">
               <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('admin.user.create') }}"> User Create</a>
            </div>
           </div>
         
         <!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

     <section class="content">
        @if(isset($users))

      <div class="container-fluid">
  
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
        <input id="myInput" type="text" placeholder="Search..">
  
    <table class="table table-bordered table-responsive-lg col-12">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Email</th>
            <th>Date Created</th>
            <th>Actions</th>
        </tr>
        <tbody id="myTable">
        <?php $i = 1;?>
        @foreach ($users as $user)

            <tr>
                <td>{{$i}}</td>
                <td>{{ $user->name ? $user->name: '' }}</td>
                <td>{{$user->email ? $user->email : ''}}</td>
                <td>{{$user->updated_at ? $user->updated_at: '' }}</td>
                <td>
                   <form action="{{route('admin.user.delete',$user->id)}}" method="POST"> 
                    <a class="btn btn-primary" href="{{ route('admin.user.edit',$user->id) }}">Edit</a>
  
                    @csrf
                    @method('DELETE')      
                    <button  onclick="return confirm('Are you sure user delete?')" type="submit" class="btn btn-danger">Delete</button>
                </form>
                </td>
            </tr>
            <?php $i++; ?>
        @endforeach
        </tbody>
    </table>

    {!! $users->links() !!}     

</div>
    </section>
     @endif
</div>

@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>

