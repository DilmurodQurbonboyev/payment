<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Click\ClickException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\ClickTransaction;
use Illuminate\Support\Facades\DB;

class ClickController extends Controller
{
    public function prepare(Request $request): JsonResponse
    {
        $result = [
            'click_trans_id' => $request->click_trans_id,
            'merchant_trans_id' => $request->merchant_trans_id,
        ];

        $product = Order::query()->where('id', $request->merchant_trans_id)->first();
        $transaction = ClickTransaction::query()->where('click_trans_id', $request->click_trans_id)->first();

        if ($transaction !== null) {
            if ($transaction->status == ClickTransaction::STATUS_CANCEL) {
                return response()->json([
                    'error' => ClickException::TRANSACTION_CANCELLED,
                    'error_note' => 'Transaction canceled'
                ]);
            } else {
                return response()->json([
                    'error' => ClickException::ALREADY_PAID,
                    'error_note' => 'Already paid'
                ]);
            }
        }

        if ($request->error != 0) {
            return response()->json([
                'error' => ClickException::ACTION,
                'error_note' => 'Request error',
            ]);
        }

        if ($request->action != 0) {
            //Action not found
            return response()->json([
                'error' => ClickException::REQUEST_ERROR,
                'error_note' => 'Action not found',
            ]);
        }

        if (!$product) {
            $result['error'] = ClickException::ORDER_NOT_FOUND;
            $result['error_note'] = "Order not found!";
            return response()->json($result);
        }

        $transaction = ClickTransaction::query()->create([
            'click_trans_id' => $request->click_trans_id,
            'service_id' => config('app.click_service_id'),
            'click_paydoc_id' => $request->click_paydoc_id,
            'merchant_trans_id' => $product->id,
            'sign_time' => $request->sign_time,
            'amount' => $request->amount,
            'status' => ClickTransaction::STATUS_INACTIVE,
            'action' => $request->action,
            'error' => $request->error,
            'error_note' => $request->error_note,
            'sign_string' => md5(
                $request->click_trans_id .
                $request->service_id .
                config('app.click_secret_key') .
                $request->merchant_trans_id .
                ($request->action == 1 ? $request->merchant_prepare_id : '') .
                $request->amount .
                $request->action .
                $request->sign_time
            )
        ]);
        $result['error'] = 0;
        $result['error_note'] = "Success";
        $result['merchant_prepare_id'] = $transaction->id;
        return response()->json($result);
    }

    public function complete(Request $request)
    {
        $result = [
            'click_trans_id' => $request->click_trans_id,
            'merchant_trans_id' => $request->merchant_trans_id,
        ];

        if (empty($request->merchant_prepare_id)) {
            $result['error'] = ClickException::REQUEST_ERROR;
            $result['error_note'] = "Request error";
            return response()->json($result);
        }

        $transaction = ClickTransaction::query()
            ->where('click_trans_id', $request->click_trans_id)
            ->where('click_paydoc_id', $request->click_paydoc_id)
            ->first();

        $product = Order::query()->where('id', $request->merchant_prepare_id)->first();

        if ($transaction !== null) {
            # Check for request error to 0!

            if ($request->error == 0) {
                if ($request->amount == $transaction->amount) {
                    if ($transaction->status == ClickTransaction::STATUS_INACTIVE) {
                        DB::beginTransaction();
                        $transaction->status = ClickTransaction::STATUS_ACTIVE;
                        if (!$transaction->save()) {
                            DB::rollBack();
                            $result['error'] = '-n';
                            $result['error_note'] = "Unknown Error";
                            return response()->json($result);
                        }
                        DB::commit();
                        if (!empty($product)) {
                            $product->collected = $product->collected + $transaction->amount;
                            $product->save();
                        }
                        $result['error'] = 0;
                        $result['error_note'] = "Success";
                        $result['merchant_confirm_id'] = $transaction->id;
//                        $result['merchant_prepare_id'] = $request->merchant_prepare_id;
                        return response()->json($result);
                    } elseif ($transaction->status == ClickTransaction::STATUS_CANCEL) {
                        $result['error'] = ClickException::TRANSACTION_CANCELLED;
                        $result['error_note'] = "Transaction cancelled";
                        return response()->json($result);
                    } elseif ($transaction->status == ClickTransaction::STATUS_ACTIVE) {
                        $result['error'] = ClickException::ALREADY_PAID;
                        $result['error_note'] = "Already paid";
                        return response()->json($result);
                    } else {
                        $result['error'] = '-n';
                        $result['error_note'] = "Unknown Error";
                        return response()->json($result);
                    }
                } else {
                    if ($transaction->status == ClickTransaction::STATUS_INACTIVE) {
                        $result['error'] = ClickException::AMOUNT;
                        $result['error_note'] = "Incorrect parameter amount";
                        return response()->json($result);
                    }
                }
            } elseif ($request->error < 0) {
                if ($request->error == -5017) {
                    if ($transaction->status != ClickTransaction::STATUS_ACTIVE) {
                        $transaction->status = ClickTransaction::STATUS_CANCEL;
                        if ($transaction->save()) {
                            $result['error'] = ClickException::TRANSACTION_CANCELLED;
                            $result['error_note'] = "Transaction cancelled";
                            return response()->json($result);
                        }
                        $result['error'] = '-n';
                        $result['error_note'] = "Unknown Error";
                        return response()->json($result);
                    } else {
                        $result['error'] = '-n';
                        $result['error_note'] = "Unknown Error";
                        return response()->json($result);
                    }
                } elseif ($request->error == -1 && $transaction->status == ClickTransaction::STATUS_ACTIVE) {
                    $result['error'] = ClickException::ALREADY_PAID;
                    $result['error_note'] = "Already paid";
                    return response()->json($result);
                } else {
                    $result['error'] = '-n';
                    $result['error_note'] = "Unknown Error";
                    return response()->json($result);
                }
            } else {
                $result['error'] = '-n';
                $result['error_note'] = "Unknown Error";
                return response()->json($result);
            }
        }

        $result['error'] = 0;
        $result['error_note'] = 'Success';
        $result['merchant_confirm_id'] = $transaction->id;
        return response()->json($result);

    }
}
