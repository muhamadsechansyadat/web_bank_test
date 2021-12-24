<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Rekening;
use Illuminate\Support\Facades\Validator;

class RekeningController extends Controller
{
    public function index()
    {
        $data = Rekening::get();
        return response()->json([
            'data' => $data,
            'msg' => 'success'
        ]);
    }

    public function show($id)
    {
        $data = Rekening::find($id);
        return response()->json([
            'data' => $data,
            'msg' => 'success'
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'jumlah' => 'required|numeric|min:0',
            'no_rek' => 'required|unique:rekening|numeric|digits_between:7,10',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        $data = Rekening::create([
            'jumlah' => $request->jumlah,
            'no_rek' => $request->no_rek,
        ]);

        if ($data) {
            return response()->json([
                'data' => $data,
                'msg' => 'success'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'jumlah' => 'required|numeric|min:0',
            'no_rek' => 'required|unique:rekening|numeric|digits_between:7,10',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        $data = Rekening::where('id', $id)->update([
            'jumlah' => $request->jumlah,
            'no_rek' => $request->no_rek,
        ]);

        if ($data) {
            return response()->json([
                'data' => $data,
                'msg' => 'success'
            ]);
        }
    }
}
