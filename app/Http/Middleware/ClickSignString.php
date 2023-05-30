<?php

namespace App\Http\Middleware;

use App\Services\Click\ClickException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClickSignString
{
    public function handle(Request $request, Closure $next): Response
    {
        $sign_string = md5(
            $request->click_trans_id .
            $request->service_id .
            config('app.click_secret_key') .
            $request->merchant_trans_id .
            ($request->action == 1 ? $request->merchant_prepare_id : '') .
            $request->amount .
            $request->action .
            $request->sign_time
        );


        if ($sign_string != $request->sign_string) {
            return response()->json([
                'click_trans_id' => $request->click_trans_id,
                'merchant_trans_id' => $request->merchant_trans_id,
                'error' => ClickException::SIGN,
                'error_note' => 'SIGN CHECK FAILED!'
            ]);
        } else {
            return $next($request);
        }
    }
}
