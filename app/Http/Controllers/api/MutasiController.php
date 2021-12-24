<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Rekening;
use App\Models\User;

class MutasiController extends Controller
{
    public function mutasi(Request $request){
        $user_id = $request->user ?? '';
        $tipe = $request->tipe ?? '';
        $startdate = ($request->startdate != NULL) ? $request->startdate.'%' : '';
        $enddate = ($request->enddate != NULL) ? $request->enddate.'%' : '';
        // DB::enableQueryLog();
        $data = Rekening::where('user_id', $user_id)->with(['user','transaksi' => function ($q) use ($tipe, $startdate, $enddate) {
            if($tipe != ''){
                $q->where('tipe',$tipe);
            }if($startdate != '' && $enddate != ''){
                $q->whereBetween('created_at', [$startdate, $enddate]);
            }
        }])->get();

        return response()->json([
            'data' => $data,
            'msg' => 'success',
        ]);
    }
}
