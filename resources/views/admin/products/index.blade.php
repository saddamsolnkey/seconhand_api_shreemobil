@extends('admin.master')
@section('content')
<div class="content-wrapper">
  @extends('admin.master')
@section('content')
<!-- <div class="content-wrapper">
   
   <div class="content-header">
      <div class="container-fluid">
         <div class="row mb-2">
            <div class="col-sm-6">
               <h1 class="m-0">Product List</h1>
            </div>
            
            <div class="col-sm-6">
               <div class="pull-right">
                  <a class="btn btn-primary" href="{{ route('admin.product.create') }}"> Product Create</a>
               </div>
            </div>
            
         </div>
         
      </div>
      
   </div>
   <section class="content">
      @if(isset($products))
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
               <th>Seller Name</th>
               <th>Buyer Name</th>
               <th>Mobile</th>
               <th>Date Created</th>
               <th>Actions</th>
            </tr>
            <tbody id="myTable">
               <?php $i = 1;?>
               @foreach ($products as $product)
               <tr>
                  <td>{{$i}}</td>
                  <td>{{ $product->mobile_name ? $product->mobile_name: '' }}</td>
                  <td>{{ $product->sellerName ? $product->sellerName: '' }}</td>
                  <td>{{ $product->buyerName ? $product->buyerName: '' }}</td>
                  <td>{{$product->mobile_price ? $product->mobile_price : ''}}</td>
                  <td>{{$product->buy_date ? $product->buy_date: '' }}</td>
                  <td>
                     <form action="{{route('admin.product.delete',$product->id)}}" method="POST">   
                        <a class="btn btn-info" href="{{ route('admin.product.show',$product->id) }}">Show</a>    
                        @csrf
                        @method('DELETE')      
                        <button  onclick="return confirm('Are you sure product delete?')" type="submit" class="btn btn-danger">Delete</button>
                     </form>
                  </td>
               </tr>
               <?php $i++; ?>
               @endforeach
            </tbody>
         </table>
         {!! $products->links() !!}     
      </div>
   </section>
   @endif
</div> -->
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