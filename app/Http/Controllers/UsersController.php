<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
        ]);

        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    // all users
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    // signup
    public function create()
    {
        return view('users.create');
    }

    // show
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    // store
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required|max:50',
            'email'    => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password)
        ]);

        //Auth::login($user);
        // 发送邮件
        $this->sendEmailConfirmationTo($user);
        //session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        session()->flash('success', '激活邮件发送成功，请激活邮件后登录');
        //return redirect()->route('users.show', [$user]);
        return redirect('/');
    }

    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'gaoxuhui2011@163.com';
        $name = 'david';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }

    // edit
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    // update
    public function update(User $user, Request $request)
    {
        $this->validate($request, [
            'name'     => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $this->authorize('update', $user);
        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success', '个人资料更新成功!');

        return redirect()->route('users.show', $user->id);
    }

    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '删除用户成功');
        return back();
    }

    //激活邮箱
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activation_token = null;
        $user->activated = true;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜您，激活邮箱成功');
        return redirect()->route('users.show', compact('user'));
    }
}
