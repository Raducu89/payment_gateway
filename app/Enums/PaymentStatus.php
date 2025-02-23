<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending    = 'pending';
    case Processing = 'processing';
    case Paid       = 'paid';
    case Failed     = 'failed';
}
