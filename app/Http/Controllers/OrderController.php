<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request): Model|Builder
    {
        try {
            $order = Order::query()->create([
                'origin_id' => $request->origin_id,
                'amount' => $request->amount,
                'status' => $request->status
            ]);
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
        return $order;
    }

    public function getById($id): Model|Collection|Builder|array|null
    {
        return Order::query()->findOrFail($id);
    }
}
