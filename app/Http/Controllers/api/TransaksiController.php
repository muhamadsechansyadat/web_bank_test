<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\Rekening;
use App\Models\User;

class TransaksiController extends Controller
{
    public function index()
    {
        $data = Transaksi::get();
        return response()->json([
            'data' => $data,
            'msg' => 'success',
        ]);
    }

    public function topup(Request $request)
    {
        $rules = [
            'jumlah' => 'required|numeric|min:10000',
            'tipe' => 'required|numeric|min:1|max:3',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        $user_id = Auth::user()->id;
        $rekening = Rekening::where('user_id', $user_id)->select('id', 'no_rek', 'jumlah')->first();
        $form = [
            'jumlah' => $request->jumlah,
            'tipe' => $request->tipe,
            'rekening_id' => $rekening->id,
            'tujuan' => $rekening->no_rek,
        ];
        DB::beginTransaction();
        try {
            $data = Transaksi::create($form);
            $dataRekeningTujuan = Rekening::where('no_rek', $rekening->no_rek)->select('jumlah')->first();
            $updateRekTujuan = Rekening::where('no_rek', $rekening->no_rek)->update([
                'jumlah' => $dataRekeningTujuan->jumlah + $data->jumlah,
            ]);
            DB::commit();
            return response()->json([
                'msg' => 'Success top up'
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function wd(Request $request)
    {
        $rules = [
            'jumlah' => 'required|numeric|min:10000',
            'tipe' => 'required|numeric|min:1|max:3',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        $user_id = Auth::user()->id;
        $rekening = Rekening::where('user_id', $user_id)->select('id', 'no_rek', 'jumlah')->first();
        $form = [
            'jumlah' => $request->jumlah,
            'tipe' => $request->tipe,
            'rekening_id' => $rekening->id,
            'tujuan' => $rekening->no_rek,
        ];
        if ($rekening->jumlah >= $request->jumlah) {
            DB::beginTransaction();
            try {
                $data = Transaksi::create($form);
                $dataRekeningTujuan = Rekening::where('no_rek', $rekening->no_rek)->select('jumlah')->first();
                $updateRekTujuan = Rekening::where('no_rek', $rekening->no_rek)->update([
                    'jumlah' => $dataRekeningTujuan->jumlah - $data->jumlah,
                ]);
                DB::commit();
                return response()->json([
                    'msg' => 'Success withdraw'
                ]);
            } catch (\Exception $ex) {
                DB::rollBack();
                return response()->json(['error' => $ex->getMessage()], 500);
            }
        } else {
            return response()->json([
                'msg' => 'Saldo Tidak Mencukupi'
            ]);
        }
    }

    public function tf(Request $request)
    {
        $rules = [
            'jumlah' => 'required|numeric|min:10000',
            'tipe' => 'required|numeric|min:1|max:3',
            'tujuan' => 'required|numeric|digits_between:7,10',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        $user_id = Auth::user()->id;
        $rekening = Rekening::where('user_id', $user_id)->select('id', 'no_rek', 'jumlah')->first();
        $form = [
            'jumlah' => $request->jumlah,
            'tipe' => $request->tipe,
            'rekening_id' => $rekening->id,
            'tujuan' => $request->tujuan,
        ];
        if ($rekening->jumlah >= $request->jumlah) {
            DB::beginTransaction();
            try {
                $data = Transaksi::create($form);
                $dataRekeningTujuan = Rekening::where('no_rek', $rekening->no_rek)->select('jumlah')->first();
                $updateRekTujuan = Rekening::where('no_rek', $rekening->no_rek)->update([
                    'jumlah' => $dataRekeningTujuan->jumlah - $data->jumlah,
                ]);
                $dataRekeningTujuanTF = Rekening::where('no_rek', $request->tujuan)->with('user')->first();
                $updateRekTujuanTF = Rekening::where('no_rek', $request->tujuan)->update([
                    'jumlah' => $dataRekeningTujuanTF->jumlah + $data->jumlah,
                ]);
                DB::commit();
                return response()->json([
                    'msg' => 'Success Transfer Ke sdr.' . $dataRekeningTujuanTF->user->name,
                ]);
            } catch (\Exception $ex) {
                DB::rollBack();
                return response()->json(['error' => $ex->getMessage()], 500);
            }
        } else {
            return response()->json([
                'msg' => 'Saldo Tidak Mencukupi'
            ]);
        }
    }

    public function mutasi(Request $request)
    {
        $user_id = Auth::user()->id;
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
