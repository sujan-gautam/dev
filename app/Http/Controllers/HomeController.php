<?php

namespace App\Http\Controllers;

use App\Models\Paste;
use App\Models\Syntax;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paste = new \stdClass();

        if(\Auth::check()) {
            $paste->title = (!empty(\Auth::user()->default_paste->title)) ? \Auth::user()->default_paste->title : "";
            $paste->status = (!empty(\Auth::user()->default_paste->status)) ? \Auth::user()->default_paste->status : "";
            $paste->syntax = (!empty(\Auth::user()->default_paste->syntax)) ? \Auth::user()->default_paste->syntax : config('settings.default_syntax');
            $paste->expire = (!empty(\Auth::user()->default_paste->expire)) ? \Auth::user()->default_paste->expire : "";
            $paste->password = (!empty(\Auth::user()->default_paste->password)) ? \Auth::user()->default_paste->password : "";
            $paste->encrypted = (!empty(\Auth::user()->default_paste->encrypted)) ? \Auth::user()->default_paste->encrypted : "";
            $paste->folder_id = (!empty(\Auth::user()->default_paste->folder_id)) ? \Auth::user()->default_paste->folder_id : "";
            $paste->description = (!empty(\Auth::user()->default_paste->description)) ? \Auth::user()->default_paste->description : "";
            $paste->tags = (!empty(\Auth::user()->default_paste->tags)) ? \Auth::user()->default_paste->tags : "";
        }
        else{
            $paste->title = "";
            $paste->status = "";
            $paste->syntax = config('settings.default_syntax');
            $paste->expire = "";
            $paste->password = "";
            $paste->encrypted = "";
            $paste->folder_id = "";
            $paste->description = "";
            $paste->tags = "";
        }

        return view('front.home.index', compact('paste'));
    }
}
