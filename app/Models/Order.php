<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const REFUND_STATUS_PENDING    = 'pending';
    const REFUND_STATUS_APPLIED    = 'applied';
    const REFUND_STATUS_REFUSE     = 'refuse';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS    = 'success';
    const REFUND_STATUS_FAILED     = 'failed';

    const DELIVER_STATUS_PENDING    = 'pending';
    const DELIVER_STATUS_DELIVERING = 'delivering';
    const DELIVER_STATUS_DELIVERED  = 'delivered';
    const DELIVER_STATUS_FAILED     = 'failed';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_REFUSE     => '拒绝退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    public static $deliverStatusMap = [
        self::DELIVER_STATUS_PENDING    => '未出货',
        self::DELIVER_STATUS_DELIVERING => '正在出货',
        self::DELIVER_STATUS_DELIVERED  => '出货成功',
        self::DELIVER_STATUS_FAILED     => '出货失败',
    ];

    protected $fillable = [
        'no',
        'ordinal',
        'amount',
        'sold_price',
        'total_amount',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_note',
        'refund_picture',
        'refund_refuse_note',
        'refund_status',
        'refund_no',
        'is_closed',
        'deliver_status',
        'deliver_data',
        'extra',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'ship_data' => 'array',
        'extra'     => 'array',
    ];

    protected $dates = [
        'paid_at',
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 no 字段为空
            if (!$model->no) {
                // 调用 findAvailableNo 生成订单流水号
                $model->no = static::findAvailableNo();
                // 如果生成失败，则终止创建订单
                if (!$model->no) {
                    return false;
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function vendingMachine()
    {
        return $this->belongsTo(VendingMachine::class);
    }

    public function vendingMachineAisle()
    {
        return $this->belongsTo(VendingMachineAisle::class);
    }

    public static function findAvailableNo()
    {
        // 订单流水号前缀
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
        }
        \Log::warning('find order no failed');

        return false;
    }
}