<?php

namespace BadChoice\Mojito\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderContentLot extends Model
{
    use SoftDeletes;

    protected $table    = "purchase_order_contents_lots";
    protected $guarded  = ['id'];
}
