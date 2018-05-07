<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    // 登录页
    public function create()
    {
        return view('sessions.create');
    }

    // 登录认证
    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            // 检查邮箱是否激活
            if (Auth::user()->activated) {
                // 登录成功
                session()->flash('success', '欢迎回来');
                //return redirect()->route('users.show', [Auth::user()]);
                return redirect()->intended(route('users.show', [Auth::user()]));
            } else {
                Auth::logout();
                // 提示去激活邮箱
                session()->flash('warning', '请激活邮箱后再次登录');

                return redirect('/');
            }

        } else {
            // 登录失败
            session()->flash('danger', '很抱歉，邮箱和密码不匹配');
            return redirect()->back();
        }
    }

    // 退出
    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '退出成功');
        return redirect()->route('login');
    }
}
