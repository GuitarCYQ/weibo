<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest',[
            'only' => ['create']
        ]);
    }

    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'requird'
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))){
            if (Auth::user()->activated){
                //登陆成功后的相关操作
                session()->flash('success','欢迎回来！');
                $fallback =  route('users.show',[Auth::user()]);
                //intended方法为了将页面重定向到上一次请求尝试访问的页面
                return redirect()->intended($fallback);
            }else{
                Auth::logout();
                session()->flash('warning','您的账号未激活，请检查邮箱中的注册邮件进行激活');
                return redirect('/');
            }

        }else{

            //登陆失败后的相关操作
            session()->flash('danger','很抱歉，您的邮箱和密码不匹配');
            //使用 withInput() 后模板里 old('email') 将能获取到上一次用户提交的内容
            return redirect()->back()->withInput();
        }

    }

    public function destroy()
    {
        Auth::logout();
        session()->flash('success','您已成功退出');
        return redirect('login');
    }
}
