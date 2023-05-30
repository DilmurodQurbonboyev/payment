<?php

namespace App\Services\Click;

class ClickException extends \Exception
{
    const SUCCESS = 0;
    const SIGN = -1;
    const AMOUNT = -2;
    const ACTION = -3;
    const ALREADY_PAID = -4;
    const ORDER_NOT_FOUND = -5;
    const TRANSACTION_NOT_FOUND = -6;
    const UPDATE_FAILED = -7;
    const REQUEST_ERROR = -8;
    const TRANSACTION_CANCELLED = -9;


}
