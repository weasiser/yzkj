<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'nick_name',
        'avatar',
        'phone',
        'email',
        'password',
        'weixin_session_key',
        'weapp_openid', 'alipay_user_id',
        'alipay_access_token',
        'is_mobile_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_mobile_admin' => 'boolean'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'warehouse_managers', 'user_id', 'warehouse_id');
    }

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 nick_name 字段为空
            if (!$model->nick_name) {
                // 调用 findAvailableNickName 生成昵称
                $model->nick_name = static::findAvailableNickName();
                // 如果生成失败，则终止创建用户
                if (!$model->nick_name) {
                    return false;
                }
            }
        });
    }

    public static function findAvailableNickName()
    {
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $nickName = uniqid('yz_');
            // 判断是否已经存在
            if (!static::query()->where('nick_name', $nickName)->exists()) {
                return $nickName;
            }
        }
        \Log::warning('find nick name failed');

        return false;
    }
}
