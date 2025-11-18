@extends('admin.master')

@section('content')

   <div class="content-wrapper">

   
    <div class="content-header">
    <div class="container-fluid">

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2> Show Product </h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('admin.product') }}"> Back</a>
            </div>
        </div>
    </div>
   </div>
</div>


<section class="mb-5">

  <div class="row">
    <div class="col-md-6 mb-4 mb-md-0">

      <div id="mdb-lightbox-ui"></div>

      <div class="mdb-lightbox">

        <div class="row product-gallery mx-1">

          <div class="col-12">
            <div class="row">
              <div class="col-3">
                <div class="view overlay rounded z-depth-1 gallery-item">
                  <img alt="no img" class="img-fluid" src="{{ asset('../storage/app/'.$product->mobile_photo) }}"" >
                  
                  <div class="mask rgba-white-slight"></div>
                </div>
              </div>
            
            
            </div>
          </div>
        </div>

      </div>

    </div>
    <div class="col-md-6">

      <h5>Mobile Name:</h5>
      <p class="mb-2 text-muted text-uppercase small">{{$product->mobile_name ? $product->mobile_name: ''}}</p>
      <h5>Seller Name:</h5>
       <p class="mb-2 text-muted text-uppercase small">{{$product->sellerName ? $product->sellerName: ''}}</p>
       <h5>Buyer Name:</h5> 
       <p class="mb-2 text-muted text-uppercase small">{{$product->buyerName ? $product->buyerName: ''}}</p>
     
      <p>Price: <span class="mr-1"><strong>{{$product->mobile_price ? $product->mobile_price: ''}}</strong></span></p>
      
      <p>EMI: <span class="mr-1"><strong>{{$product->mobile_emi ? $product->mobile_emi: ''}}</strong></span></p>
      
     <p>Buy Date <span class="mr-1"><strong>{{$product->buy_date ? $product->buy_date: ''}}</strong></span></p>
      
     
     
    </div>
  </div>

</section>
<!--Section: Block Content-->
</div>
@endsection

