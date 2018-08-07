<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Traits\ActiveUserHelper;
    use HasRoles;

    use Notifiable {
        notify as protected laravelNotify;
    }
    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::id()) {
            return;
        }
        $this->increment('notification_count');
        $this->laravelNotify($instance);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'introduction', 'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    // 用户授权方法,在TopicPolicy里调用该方法
    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    // 未读消息读取后取消通知提示
    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    // 后台修改密码问题
    public function setPasswordAttribute($value)
    {
        // 如果长度等于60,即认为是已经做过加密的情况
        if (strlen($value) != 60) {
            // 不等于60做密码加密处理
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }

    // 后台上传头像
    public function setAvatarAttribute($path)
    {
        if ( ! starts_with($path, 'http')) {

            // 拼接完整的URL
            $path = config('app.url') . "/uploads/images/avatars/$path";
        }

        $this->attributes['avatar'] = $path;
    }
}
