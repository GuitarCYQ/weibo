<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    //消息通知相关功能引用
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //只有包含在该属性中的字段才能够被正常更新：
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    //当我们需要对用户密码或其它敏感信息在用户实例通过数组或 JSON 显示时进行隐藏
    protected $hidden = [
        'password', 'remember_token',
    ];

    //在用户注册之前生成邮件令牌 以便激活
    public static function boot(){
        parent::boot();

        static::creating(function ($user){
            $user->activation_token = Str::random(10);
        });
    }


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    //Gravatar 头像
    public function gravatar($size = '100')
    {
        $hash =  md5(strtolower(trim($this->attributes['email'])));
        return "https://www.gravatar.com/avatar/$hash?s=$size";
    }


    //一个用户有多条微博
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    //将当前用户发布过的所有微博从数据库中取出，并根据创建时间来倒序排序
    public function feed()
    {
        return $this->statuses()
            ->orderBy('created_at','desc');
    }

    //粉丝关系
    //获取粉丝关系
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    //获取用户关注人列表
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    //关注
    public function follow($user_ids)
    {
        if (! is_array($user_ids)){
            $user_ids = compact('user_ids');

        }
        $this->followings()->sync($user_ids, false);
    }


    //取消关注
    public function unfollow($user_ids)
    {
        if (! is_array($user_ids)){
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}
