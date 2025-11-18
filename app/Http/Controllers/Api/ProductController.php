<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Device;
use Validator;

class ProductController extends Controller
{
    
    public function search($text)
    {
            
        $proList= Product::where('mobile_emi', 'like', '%' . $text . '%')->orWhere('mobile_name', 'like', '%' . $text . '%')->orderBy('updated_at', 'DESC')->limit(50)->get();
        //$proList = Product::orderBy('updated_at', 'DESC')->get();
        // return response([]);
        return response([ 'data' => $proList, 'message' => 'Retrieved successfully'], 200);
    
    }
    
    public function searchnew(Request $request)
    {
        $data = $request->all();
        $proList = Product::query();  // Use query builder
    
        if ((int)($data['buildnumber'] ?? 0) < 5) {
            return response(['error' => "Error", 'message' => 'Update the App']);
        }
        $device = Device::where('uniqueid', $data['uniqueid'])
                    ->where('isactive', 'true')
                    ->first();
        if (!$device) {
            return response()->json([
                'error' => 'Device not registered. Please register the device first.'
            ], 403); // 403 Forbidden
        }
        
        // Search condition
        if (!empty($data['text'])) {
            $proList->where(function ($query) use ($data) {
                $query->where('mobile_name', 'like', '%' . $data['text'] . '%')
                      ->orWhere('mobile_emi', 'like', '%' . $data['text'] . '%');
            });
        }
    
        // Determine sorting column
        $type = (!empty($data['type']) && $data['type'] === 'buy') ? 'created_at' : 'updated_at';
    
        // Apply date filters
        if (!empty($data['from_date']) && !empty($data['to_date'])) {
            $proList->whereBetween($type, [$data['from_date'], $data['to_date']]);
        } elseif (!empty($data['from_date'])) {
            $proList->where($type, '>=', $data['from_date']);
        } elseif (!empty($data['to_date'])) {
            $proList->where($type, '<=', $data['to_date']);
        }
    
        // Fetch results with ordering and limit
            $proList->orderBy('updated_at', 'DESC');
            
        if (!empty($data['is_export']) && $data['is_export']==1) {
            $proList = $proList->get();
        }else{
            $proList = $proList->paginate(200); 
        }
        
     
    
        return response([
            'data' => $proList,
            'message' => 'Retrieved successfully'
        ], 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\R
     */
    public function index()
    {
        $proList = Product::orderBy('updated_at', 'DESC')->get();
      //return response([]);
      return response([ 'data' => $proList, 'message' => 'Retrieved successfully'], 200);

    }
    
    public function getallproduct(Request $request)
    {
        if ((int)($request->buildnumber ?? 0) < 5) {
            return response(['error' => "Error", 'message' => 'Update the App']);
        }
        $device = Device::where('uniqueid', $request->uniqueid)
                    ->where('isactive', 'true')
                    ->first();
        if (!$device) {
            return response()->json([
                'error' => 'Device not registered. Please register the device first.'
            ], 403); // 403 Forbidden
        }
        
        
        $proList = Product::where('is_deleted',0)->orderBy('updated_at', 'DESC')->paginate($request->per_page, ['*'], 'page', $request->current_page);
    //   return response([]);
        return response([ 'data' => $proList, 'message' => 'Retrieved successfully'], 200);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
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
        $validator = Validator::make($data, [
            'mobile_name' => 'required|max:255',
            'mobile_price' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }
        if ((int)($data['buildnumber'] ?? 0) < 5) {
            return response(['error' => $validator->errors(), 'message' => 'Update the App']);
        }
        
        $device = Device::where('uniqueid', $data['deviceuniqueid'])
                    ->where('isactive', 'true')
                    ->first();
        if (!$device) {
            return response()->json([
                'error' => 'Device not registered. Please register the device first.'
            ], 403); // 403 Forbidden
        }
        
        if ($request->file('mobile_photo')) {
        $path = $request->file('mobile_photo')->store('public/mobileImg/');
        $uploadFile = $request->file('mobile_photo');
        $file_name = 'm_is'.time().'.'.$request->mobile_photo->extension();
        $data['mobile_photo'] = $uploadFile->storeAs('public/mobileImg', $file_name);
        }
        if($request->file('mobile_bill_photo')){
        $path = $request->file('mobile_bill_photo')->store('public/billImg/');
        $uploadFileBill = $request->file('mobile_bill_photo');
        $file_name1 = 'bill_is'.time().'.'.$request->mobile_bill_photo->extension();
        $data['mobile_bill_photo'] = $uploadFileBill->storeAs('public/billImg', $file_name1);
        }
        if ($request->file('buyer_id_photo')) {
        $path = $request->file('buyer_id_photo')->store('public/buyerImg/');
        $uploadFileBuyer = $request->file('buyer_id_photo');
        $file_name2 = 'by_is'.time().'.'.$request->buyer_id_photo->extension();
        $data['buyer_id_photo'] = $uploadFileBuyer->storeAs('public/buyerImg', $file_name2);

        }
        
        
        if($request->file('seller_id_photo')){
        $path = $request->file('seller_id_photo')->store('public/buyerImg/');
        $uploadFileSeller = $request->file('seller_id_photo');
        $file_name3 = 's_is'.time().'.'.$request->seller_id_photo->extension();
        $data['seller_id_photo'] = $uploadFileSeller->storeAs('public/buyerImg', $file_name3);
        }
        $unique_no = Product::orderBy('id', 'DESC')->pluck('id')->first();

        if($unique_no == null or $unique_no == ""){
        $unique_no = 1;
        }
        else{
        $unique_no = $unique_no + 1;
        }
        $data['pro_serial_num'] ='PROD'.$unique_no;
        

        $productAdd =  Product::create($data);
        $productAdd['buildnumber'] = (int)($data['buildnumber'] ?? 0);
        return response(['data' => $productAdd, 'message' => 'Created successfully'], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   

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
    public function update(Request  $request)
    {   
        
        $data = $request->all();
        $validator = Validator::make($data, [
            'mobile_name' => 'required|max:255',
            'mobile_price' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $product = Product::find($request->id);
        $product->mobile_name = $data['mobile_name'];
        $product->mobile_price = $data['mobile_price'];
        $product->sell_status = $data['sell_status'];
        $product->buyerName = $data['buyerName'];
        $product->selling_price = $data['selling_price']; 
        $product->buyer_number = $data['buyer_number'];
        $product->deviceuniqueid = $data['deviceuniqueid']??"";
        $product->devicename = $data['devicename']??"";
        $product->custom_date = $data['custom_date']??"";
        
        
        if($request->file('buyer_id_photo')){
        $path = $request->file('buyer_id_photo')->store('public/buyerImg/');
        $uploadFileBuyer = $request->file('buyer_id_photo');
        $file_name = 'buyer_id'.time().'.'.$request->buyer_id_photo->extension();
        $product['buyer_id_photo'] = $uploadFileBuyer->storeAs('public/buyerImg', $file_name);
        }
        // if($request->file('seller_id_photo')){
        // $path = $request->file('seller_id_photo')->store('public/buyerImg/');
        // $uploadFileSeller = $request->file('seller_id_photo');
        // $file_name3 = 's_is'.time().'.'.$request->seller_id_photo->extension();
        // $product['seller_id_photo'] = $uploadFileSeller->storeAs('public/buyerImg', $file_name3);
        // }
    
        $proUpdate = $product->update();
        if($proUpdate){
            return response(['status'=>true,'msg'=>$product]);
        }
        return response(['status'=> false , 'msg'=> 'Not update']);
        

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {   
       
        $product = Product::find($id);


        if($product != null){
        $product->is_deleted = 1;
        $product->update();

        return response(['message' => 'Delete successfully'], 200);

        }else{
         return response(['message' => 'Not deleted'], 200);
        }
    }
    
    public function getalldevice(Request $request)
    {
        if ((int)($request->buildnumber ?? 0) < 5) {
            return response(['error' => "Error", 'message' => 'Update the App']);
        }
        $device = Device::where('uniqueid', $request->uniqueid)
                    ->where('isactive', 'true')
                    ->first();
       
        
        
        $device = Device::get();
    //   return response([]);
        return response([ 'data' => $device, 'message' => 'Retrieved successfully'], 200);

    }
    
    public function updatedevice(Request  $request)
    {   
        
        $data = $request->all();
        $validator = Validator::make($data, [
            'id' => 'required',
            'isactive' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $device = Device::find($request->id);
        $device->isactive = $data['isactive']??"false";
        
        $deviceUpdate = $device->update();
        if($deviceUpdate){
            return response(['status'=>true,'msg'=>$deviceUpdate]);
        }
        return response(['status'=> false , 'msg'=> 'Not update']);
        

    }
}
