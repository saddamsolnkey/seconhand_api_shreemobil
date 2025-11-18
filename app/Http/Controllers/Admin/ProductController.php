<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Validator;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $products = Product::latest()->paginate(25);
        return view('admin.products.index',compact('products'))->with('i', (request()->input('page', 1 ) - 1 )* 25 );

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $data = $request->all();
        $request->validate([
        'mobile_name' => 'required',
        'mobile_price' => 'required',
        ]);
        
        // $validator = Validator::make($data, [
        //     'mobile_name' => 'required|max:255',
        //     'mobile_price' => 'required|max:255',
        // ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }
        if ($request->file('mobile_photo')) {
        $path = $request->file('mobile_photo')->store('public/mobileImg/');
        $uploadFile = $request->file('mobile_photo');
        $file_name = time().'.'.$request->mobile_photo->extension();
        $data['mobile_photo'] = $uploadFile->storeAs('public/mobileImg', $file_name);
        }
        if($request->file('mobile_bill_photo')){
        $path = $request->file('mobile_bill_photo')->store('public/billImg/');
        $uploadFileBill = $request->file('mobile_bill_photo');
        $file_name1 = time().'.'.$request->mobile_photo->extension();
        $data['mobile_bill_photo'] = $uploadFileBill->storeAs('public/billImg', $file_name1);
        }
        if ($request->file('buyer_id_photo')) {
        $path = $request->file('buyer_id_photo')->store('public/buyerImg/');
        $uploadFileBuyer = $request->file('buyer_id_photo');
        $file_name2 = time().'.'.$request->buyer_id_photo->extension();
        $data['buyer_id_photo'] = $uploadFileBuyer->storeAs('public/buyerImg', $file_name2);

        }
        if($request->file('seller_id_photo')){
        $path = $request->file('seller_id_photo')->store('public/sellerImg/');
        $uploadFileSeller = $request->file('seller_id_photo');
        $file_name1 = time().'.'.$request->mobile_photo->extension();
        $product->seller_id_photo = $uploadFileSeller->storeAs('public/sellerImg', $file_name1);
        }

        

       $productAdd =  Product::create($data);
       return redirect()->route('admin.product')
       ->with('success','Product created successfully.');
       

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {   
       //echo "<pre>"; print_r($product);die();
        return view('admin.products.show', compact('product'));
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
        
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->back();
        //return redirect()->route('admin.product')->with('completed', 'Product has been deleted');
    }
}
