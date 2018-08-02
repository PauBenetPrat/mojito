<?php

namespace BadChoice\Mojito\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderContent extends Model
{
    use SoftDeletes;

    protected $table    = "purchase_order_contents";
    protected $guarded  = ['id'];
    protected $appends  = ['itemName', 'itemBarcode'];
    protected $hidden   = ['item', 'vendorItem'];

    const STATUS_PENDING            = 0;
    const STATUS_SENT               = 1;
    const STATUS_PARTIAL_RECEIVED   = 2;
    const STATUS_RECEIVED           = 3;
    const STATUS_DRAFT              = 4;
    const STATUS_PARTIAL_REFUNDED   = 5;
    const STATUS_REFUND_PENDING     = 6;
    const STATUS_REFUNDED           = 7;

    //============================================================================
    // REGISTER EVENT LISTENRES
    //============================================================================
    public static function boot()
    {
        parent::boot();
        static::saved(function ($purchaseOrderContent) {
            $po = PurchaseOrder::find($purchaseOrderContent->order_id);
            $po->update([
                "total"     => $po->calculateTotal(),
                "status"    => $po->calculateStatus(),
            ]);
        });
    }

    //============================================================================
    // RELATIONSHIPS
    //============================================================================
    public function order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'order_id');
    }

    public function vendor()
    {
        return $this->vendorItem->vendor;
    }

    public function item()
    {
        return $this->vendorItem->item;
    }

    public function vendorItem()
    {
        return $this->belongsTo(VendorItemPivot::class, 'item_vendor_id')->withTrashed();
    }

    //============================================================================
    // JSON APPENDS
    //============================================================================
    public function getItemNameAttribute()
    {
        return $this->vendorItem->item->name ?? "";
    }

    public function getItemBarcodeAttribute()
    {
        return $this->vendorItem->item->barcode ?? "";
    }

    //============================================================================
    // METHODS
    //============================================================================
    public function receive($quantity, $warehouseId)
    {
        $warehouse  = Warehouse::find($warehouseId);
        $warehouse->add($this->vendorItem->item_id, $quantity, $this->vendorItem->unit_id);

        $this->received = $this->received + $quantity;
        $this->status = $this->calculateStatus();
        $this->save();
    }

    public function statusName()
    {
        return static::getStatusName($this->status);
    }

    public static function getStatusName($status)
    {
        return static::statusArray()[$status] ?? '?';
    }

    public static function statusArray()
    {
        return [
            static::STATUS_PENDING              => __('admin.pending'),
            static::STATUS_SENT                 => __('admin.sent'),
            static::STATUS_PARTIAL_RECEIVED     => __('admin.partialReceived'),
            static::STATUS_RECEIVED             => __('admin.received'),
            static::STATUS_DRAFT                => __('admin.draft'),
            static::STATUS_REFUND_PENDING       => __('admin.refundPending'),
            static::STATUS_PARTIAL_REFUNDED     => __('admin.partialRefunded'),
            static::STATUS_REFUNDED             => __('admin.refunded'),
        ];
    }

    public function updatePrice($price)
    {
        $this->update(["price" => str_replace(',', '.', $price)]);
    }

    public function updateQuantity($quantity, $warehouseId)
    {
        $leftToReceive      = $quantity - $this->received;
        $this->quantity     = $quantity;
        $this->received     = $leftToReceive < 0 ? $quantity : $this->received;
        $this->status       = $this->calculateStatus();
        $this->adjustStock($leftToReceive, $warehouseId);
        $this->save();
    }

    public function calculateStatus()
    {
        if ($this->status == static::STATUS_DRAFT) {
            return static::STATUS_DRAFT;
        }
        $leftToReceive = $this->quantity - $this->received;
        if ($this->quantity < 0) {
            return $this->calculateRefundedStatus($leftToReceive);
        }
        return $this->calculateReceivedStatus($leftToReceive);
    }

    private function adjustStock($leftToReceive, $warehouseId)
    {
        if ($leftToReceive >= 0) {
            return;
        }
        Warehouse::find($warehouseId)->add($this->vendorItem->item_id, $leftToReceive, $this->vendorItem->unit_id);
    }

    private function calculateRefundedStatus($leftToReceive) {
        if ($leftToReceive >= 0) {
            return static::STATUS_REFUNDED;
        } elseif ($leftToReceive == $this->quantity) {
            return static::STATUS_REFUND_PENDING;
        }
        return static::STATUS_PARTIAL_REFUNDED;
    }

    private function calculateReceivedStatus($leftToReceive) {
        if ($leftToReceive <= 0) {
            return static::STATUS_RECEIVED;
        } elseif ($leftToReceive == $this->quantity) {
            return static::STATUS_PENDING;
        }
        return static::STATUS_PARTIAL_RECEIVED;
    }
}
