<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UpiData;
use App\Models\Device;
use Validator;

class UpiDataController extends Controller
{
    public function search($text)
    {
        $upiList = UpiData::where('is_deleted', 0)
            ->where(function ($query) use ($text) {
                $query->where('customer_name', 'like', '%' . $text . '%')
                    ->orWhere('customer_number', 'like', '%' . $text . '%')
                    ->orWhere('amount', 'like', '%' . $text . '%')
                    ->orWhere('comment', 'like', '%' . $text . '%')
                    ->orWhere('upi_serial_num', 'like', '%' . $text . '%');
            })
            ->orderBy('updated_at', 'DESC')
            ->limit(50)
            ->get();

        return response(['data' => $upiList, 'message' => 'Retrieved successfully'], 200);
    }

    public function searchnew(Request $request)
    {
        $data = $request->all();
        $upiList = UpiData::query()->where('is_deleted', 0);

        if ((int)($data['buildnumber'] ?? 0) < 5) {
            return response(['error' => "Error", 'message' => 'Update the App']);
        }

        $device = Device::where('uniqueid', $data['uniqueid'])
            ->where('isactive', 'true')
            ->first();

        if (!$device) {
            return response()->json([
                'error' => 'Device not registered. Please register the device first.'
            ], 403);
        }

        if (!empty($data['text'])) {
            $upiList->where(function ($query) use ($data) {
                $query->where('customer_name', 'like', '%' . $data['text'] . '%')
                    ->orWhere('customer_number', 'like', '%' . $data['text'] . '%')
                    ->orWhere('amount', 'like', '%' . $data['text'] . '%')
                    ->orWhere('comment', 'like', '%' . $data['text'] . '%')
                    ->orWhere('upi_serial_num', 'like', '%' . $data['text'] . '%');
            });
        }

        if (!empty($data['from_date']) && !empty($data['to_date'])) {
            $upiList->whereBetween('created_at', [$data['from_date'], $data['to_date']]);
        } elseif (!empty($data['from_date'])) {
            $upiList->where('created_at', '>=', $data['from_date']);
        } elseif (!empty($data['to_date'])) {
            $upiList->where('created_at', '<=', $data['to_date']);
        }

        $upiList->orderBy('updated_at', 'DESC');

        if (!empty($data['is_export']) && $data['is_export'] == 1) {
            $upiList = $upiList->get();
        } else {
            $upiList = $upiList->paginate(200);
        }

        return response([
            'data' => $upiList,
            'message' => 'Retrieved successfully'
        ], 200);
    }

    public function index()
    {
        $upiList = UpiData::where('is_deleted', 0)->orderBy('updated_at', 'DESC')->get();

        return response(['data' => $upiList, 'message' => 'Retrieved successfully'], 200);
    }

    public function getallupi(Request $request)
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
            ], 403);
        }

        $upiList = UpiData::where('is_deleted', 0)
            ->orderBy('updated_at', 'DESC')
            ->paginate($request->per_page, ['*'], 'page', $request->current_page);

        return response(['data' => $upiList, 'message' => 'Retrieved successfully'], 200);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'customer_name' => 'required|max:255',
            'customer_number' => 'required|max:255',
            'amount' => 'required|numeric',
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
            ], 403);
        }

        if ($request->file('customer_photo')) {
            $uploadFile = $request->file('customer_photo');
            $file_name = 'cust_' . time() . '.' . $request->customer_photo->extension();
            $data['customer_photo'] = $uploadFile->storeAs('public/upiCustomerImg', $file_name);
        }

        if ($request->file('customer_id_photo')) {
            $uploadFile = $request->file('customer_id_photo');
            $file_name = 'cust_id_' . time() . '.' . $request->customer_id_photo->extension();
            $data['customer_id_photo'] = $uploadFile->storeAs('public/upiIdImg', $file_name);
        }

        if ($request->file('upi_screenshot_photo')) {
            $uploadFile = $request->file('upi_screenshot_photo');
            $file_name = 'upi_ss_' . time() . '.' . $request->upi_screenshot_photo->extension();
            $data['upi_screenshot_photo'] = $uploadFile->storeAs('public/upiScreenshotImg', $file_name);
        }

        $unique_no = UpiData::orderBy('id', 'DESC')->pluck('id')->first();

        if ($unique_no == null || $unique_no == "") {
            $unique_no = 1;
        } else {
            $unique_no = $unique_no + 1;
        }

        $data['upi_serial_num'] = 'UPI' . $unique_no;

        $upiAdd = UpiData::create($data);
        $upiAdd['buildnumber'] = (int)($data['buildnumber'] ?? 0);

        return response(['data' => $upiAdd, 'message' => 'Created successfully'], 201);
    }

    public function delete($id)
    {
        $upiData = UpiData::find($id);

        if ($upiData != null) {
            $upiData->is_deleted = 1;
            $upiData->update();

            return response(['message' => 'Delete successfully'], 200);
        }

        return response(['message' => 'Not deleted'], 200);
    }
}
