<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Paste;
use App\Models\Syntax;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $syntax_active   = \Cache::remember('syntax_active',120,function(){ return Syntax::where('active', 1)->count(); });
        $syntax_inactive = \Cache::remember('syntax_inactive',120,function(){ return Syntax::where('active', 0)->count(); });

        $pages_active   = \Cache::remember('pages_active',120,function(){ return Page::where('active', 1)->count(); });
        $pages_inactive = \Cache::remember('pages_inactive',120,function(){ return Page::where('active', 0)->count(); });

        $paste_public   = \Cache::remember('paste_public',120,function(){ return Paste::where('status', 1)->count(); });
        $paste_unlisted = \Cache::remember('paste_unlisted',120,function(){ return Paste::where('status', 2)->count(); });
        $paste_private  = \Cache::remember('paste_private',120,function(){ return Paste::where('status', 3)->count(); });

        $user_active   = \Cache::remember('user_active',120,function(){ return User::where('status', 1)->count(); });
        $user_inactive = \Cache::remember('user_inactive',120,function(){ return User::where('status', 0)->count(); });
        $user_banned   = \Cache::remember('user_banned',120,function(){ return User::where('status', 2)->count(); });

        $users = \Cache::remember('new_users',120,function(){ return User::orderBy('created_at', 'DESC')->limit(6)->get(['id', 'name', 'created_at', 'avatar']); });

        return view('admin.dashboard.index', compact('syntax_inactive', 'syntax_active', 'paste_public', 'paste_unlisted', 'paste_private', 'user_active', 'user_inactive', 'user_banned', 'pages_inactive', 'pages_active', 'users'))->with('page_title', 'Dashboard');
    }

    public function showLogin()
    {
        if (\Auth::check()) {
            if (\Auth::user()->role == 1) {
                return redirect('admin/dashboard');
            } else {
                return redirect('/');
            }

        }
        return view('admin.auth.login')->with('page_title', 'Admin Login');
    }
}
