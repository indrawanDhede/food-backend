<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function callback(): JsonResponse
    {
        // Set konfigurasi midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        // Buat instance midtrans notification
        $notification = new Notification();

        // Assign ke variable untuk memudahkan coding
        $status = $notification->transaction_status;
        $type = $notification->payment_type;
        $fraud = $notification->fraud_status;
        $order_id = $notification->order_id;

        // Cari transaksi berdasarkan ID
        $transaction = Transaction::findOrFail($order_id);

        // Handle notification status midtrans
        if ($status == 'capture') {
            if ($type == 'credit_card'){
                if($fraud == 'challenge'){
                    $transaction->status = 'PENDING';
                }
                else {
                    $transaction->status = 'SUCCESS';
                }
            }
        }
        else if ($status == 'settlement'){
            $transaction->status = 'SUCCESS';
        }
        else if($status == 'pending'){
            $transaction->status = 'PENDING';
        }
        else if ($status == 'deny') {
            $transaction->status = 'CANCELLED';
        }
        else if ($status == 'expire') {
            $transaction->status = 'CANCELLED';
        }
        else if ($status == 'cancel') {
            $transaction->status = 'CANCELLED';
        }

        // Simpan transaksi
        return $transaction->save();

        // Kirimkan email
//        if ($transaction)
//        {
//            if($status == 'capture' && $fraud == 'accept' )
//            {
//                //
//            }
//            else if ($status == 'settlement')
//            {
//                //
//            }
//            else if ($status == 'success')
//            {
//                //
//            }
//            else if($status == 'capture' && $fraud == 'challenge' )
//            {
//                return response()->json([
//                    'meta' => [
//                        'code' => 200,
//                        'message' => 'Midtrans Payment Challenge'
//                    ]
//                ]);
//            }
//            else
//            {
//                return response()->json([
//                    'meta' => [
//                        'code' => 200,
//                        'message' => 'Midtrans Payment not Settlement'
//                    ]
//                ]);
//            }
//
//            return response()->json([
//                'meta' => [
//                    'code' => 200,
//                    'message' => 'Midtrans Notification Success'
//                ]
//            ]);
//        }
    }

    /**
     * @return Application|Factory|View
     */
    public function success()
    {
        return view('midtrans.success');
    }

    /**
     * @return Application|Factory|View
     */
    public function unfinish()
    {
        return view('midtrans.unfinish');
    }

    /**
     * @return Application|Factory|View
     */
    public function error()
    {
        return view('midtrans.error');
    }
}
