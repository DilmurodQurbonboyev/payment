<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickTransaction extends Model
{
    use HasFactory;

    const STATUS_CANCEL = -1;
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    protected $fillable = [
        'click_trans_id',
        'service_id',
        'click_paydoc_id',
        'merchant_trans_id',
        'amount',
        'action',
        'error',
        'status',
        'error_note',
        'sign_time',
        'sign_string'
    ];
}
