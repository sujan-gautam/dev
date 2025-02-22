<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Paste;
use App\Models\Report;
use App\Models\Syntax;
use Illuminate\Http\Request;
use Validator;

class PasteController extends Controller
{
    public function show($slug)
    {
        $paste = Paste::where('slug', $slug)->with('user')->firstOrfail();
        if ($paste->status == 3) {
            if (\Auth::check()) {
                if ($paste->user_id != \Auth::user()->id) {
                    abort(404);
                }
            } else {
                abort(404);
            }

        }

        if ($paste->self_destroy == 1 && $paste->views > config('settings.self_destroy_after_views') && empty($paste->expire_time)) {
            $paste->expire_time = date("Y-m-d H:i:s");
            $paste->save();
        }

        if (!empty($paste->expire_time)) {
            if (strtotime($paste->expire_time) < time()) {
                return view('errors.expired')->with('page_title', __('Paste is expired'));
            }
        }

        if (session()->has('already_viewed')) {
            $already_viewed = session('already_viewed');

            if (!in_array($paste->id, $already_viewed)) {
                array_push($already_viewed, $paste->id);
                $paste->views = $paste->views + 1;
                $paste->save();
            }

            session(['already_viewed' => $already_viewed]);
        } else {
            $already_viewed = [$paste->id];
            session(['already_viewed' => $already_viewed]);
            $paste->views = $paste->views + 1;
            $paste->save();
        }

        if ($paste->storage == 2) {
            $paste->content = file_get_contents(ltrim($paste->content, '/'));
        }

        if ($paste->encrypted == 1) {
            $paste->content = decrypt($paste->content);
        }

        $extension = (!empty(get_syntax_by_name($paste->syntax)->extension)) ? get_syntax_by_name($paste->syntax)->extension : 'txt';

        $paste->extension = $extension;

        config(['settings.site_layout' => config('settings.paste_page_layout')]);
        return view('front.paste.show', compact('paste'))->with('page_title', $paste->title_f);
    }

    public function store(Request $request)
    {
        if (\Auth::check()) {
            if (\Auth::user()->role != 1) {
                if (config('settings.user_paste') != 1) {
                    return redirect()->back()->withErrors(__('User pasting is currently disabled'))->withInput();
                }
            }
            $allowed_status = '1,2,3';
        } else {
            if (config('settings.public_paste') != 1) {
                return redirect()->back()->withErrors(__('Public pasting is currently disabled please login to create a paste'))->withInput();
            }
            $allowed_status = '1,2';
        }

        $title_required = 'nullable';
        if (config('settings.paste_title_required') == 1) $title_required = 'required';
        $validator = Validator::make($request->all(), [
            'content' => 'required|min:1',
            'folder_id' => 'nullable|exists:folders,id',
            'status' => 'required|numeric|in:' . $allowed_status,
            'syntax' => 'nullable|exists:syntax,slug',
            'expire' => 'required|max:3|in:N,10M,1H,1D,1W,2W,1M,6M,1Y,SD',
            'title' => $title_required . '|max:80|eco_string',
            'password' => 'nullable|max:50|string',
            'description' => 'nullable|string|max:2000',
            'tags' => 'nullable|string|max:200',
            'g-recaptcha-response' => (config('settings.captcha') == 1) ? 'required|captcha' : ''
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)->withInput();
        }
        $content_size = strlen($request->content) / 1000;

        if ($content_size > config('settings.max_content_size_kb')) {
            return redirect()->back()->withErrors(__('Max allowed content size is') . ' ' . config('settings.max_content_size_kb') . 'kb')->withInput();
        }

        $ip_address = request()->ip();
        if (!session()->has('paste_count')) {
            session(['paste_count' => 0]);
        }

        if (\Auth::check()) {
            $paste_count = Paste::where('user_id', \Auth::user()->id)->whereDate('created_at', date("Y-m-d"))->count();
            if ($paste_count >= config('settings.daily_paste_limit_auth')) {
                return redirect()->back()->withErrors(__('Daily paste limit reached'))->withInput();
            }

            $last_paste = Paste::where('user_id', \Auth::user()->id)->orderBy('created_at', 'DESC')->limit(1)->first();
            if (!empty($last_paste) && config('settings.paste_time_restrict_auth') > 0) {

                if (strtotime($last_paste->created_at) > strtotime('-' . config('settings.paste_time_restrict_auth') . ' seconds')) {
                    $mins = config('settings.paste_time_restrict_auth') / 60;
                    return redirect()->back()->withErrors(__('Please wait') . ', ' . $mins . ' ' . __('minutes before making another paste'))->withInput();
                }
            }
        } else {
            $paste_count = Paste::where('ip_address', $ip_address)->whereDate('created_at', date("Y-m-d"))->count();

            if ($paste_count >= config('settings.daily_paste_limit_unauth') || session('paste_count') >= config('settings.daily_paste_limit_unauth')) {
                return redirect()->back()->withErrors(__('Daily paste limit reached') . ', ' . __('Please login to increase your paste limit'))->withInput();
            }

            $last_paste = Paste::where('ip_address', $ip_address)->orderBy('created_at', 'DESC')->limit(1)->first();
            if (!empty($last_paste) && config('settings.paste_time_restrict_unauth') > 0) {

                if (strtotime($last_paste->created_at) > strtotime('-' . config('settings.paste_time_restrict_unauth') . ' seconds')) {
                    $mins = config('settings.paste_time_restrict_unauth') / 60;
                    return redirect()->back()->withErrors(__('Please wait') . ', ' . $mins . ' ' . __('minutes before making another paste'))->withInput();
                }
            }
        }

        if(!empty(config('settings.banned_words')))
        {
            $banned_words = explode(',',config('settings.banned_words'));
            foreach($banned_words as $banned_word){
                preg_match('/\b'.$banned_word.'\b/iu', $request->content, $matches, PREG_OFFSET_CAPTURE);
                if(count($matches) > 0){
                    return redirect()->back()->withErrors(__('Please remove') . ' "' . $banned_word . '" ' . __('word from your paste content'))->withInput();
                }
            }
        }


        if(!empty($request->folder_id))
        {
            Folder::where('id',$request->folder_id)->where('user_id',\Auth::user()->id)->firstOrfail();
        }          

        $paste = new Paste();
        $paste->title = $request->title;
        if(\Auth::check())
        {
            $paste->description = $request->description;
            $paste->tags = $request->tags;  
            $paste->folder_id = $request->folder_id;      
        }
        
        $paste->syntax = (!empty($request->syntax)) ? $request->syntax : config('settings.default_syntax');

        switch ($request->expire) {
            case '10M':
                $expire = '10 minutes';
                break;

            case '1H':
                $expire = '1 hour';
                break;

            case '1D':
                $expire = '1 day';
                break;

            case '1W':
                $expire = '1 week';
                break;

            case '2W':
                $expire = '2 week';
                break;

            case '1M':
                $expire = '1 month';
                break;

            case '6M':
                $expire = '6 months';
                break;

            case '1Y':
                $expire = '1 year';
                break;

            case 'SD':
                $expire = 'SD';
                break;

            default:
                $expire = 'N';
                break;
        }

        if ($expire != 'N') {
            if ($expire == 'SD') {
                $paste->self_destroy = 1;
            } else {
                $paste->expire_time = date('Y-m-d H:i:s', strtotime('+' . $expire));
            }

        }

        $paste->status = $request->status;

        if (\Auth::check()) {
            $paste->user_id = \Auth::user()->id;
        }
        $paste->ip_address = $ip_address;

        if ($request->password) {
            $paste->password = \Hash::make($request->password);
        }

        if ($request->encrypted) {
            $paste->encrypted = 1;
            $paste->content = encrypt($request->content);

        } else {
            $paste->content = htmlentities($request->content);
        }

        if (config('settings.paste_storage') == 'file') {
            $paste->storage = 2;
            $content = $paste->content;
            if (\Auth::check()) {
                $destination_path = 'uploads/users/' . \Auth::user()->name;
            } else {
                $destination_path = 'uploads/pastes/' . date('Y') . '/' . date('m') . '/' . date('d');
            }

            if (!file_exists($destination_path)) {
                mkdir($destination_path, 0775, true);
            }
            $filename = str_random(20) . '.txt';
            file_put_contents($destination_path . '/' . $filename, $content);
            $paste->content = '/' . $destination_path . '/' . $filename;
        }

        $paste->save();

        $paste_count = session('paste_count');
        $paste_count++;
        session(['paste_count' => $paste_count]);

        if(auth()->check()) \Cache::forget('my_recent_pastes_'.auth()->user()->id);

        return redirect($paste->url)->withSuccess(__('Paste successfully created'));
    }

    public function search(Request $request)
    {
        if (config('settings.search_page') != 1) return redirect()->back()->withErrors(__('This feature is disabled'));
        $validator = Validator::make($request->all(), [
            'keyword' => 'sometimes|min:2|max:100|eco_string',
        ]);
        if ($validator->fails()) {
            return redirect('/')
                ->withErrors($validator);
        }

        $search_term = $request->keyword;

        $pastes = Paste::where(function ($q) use ($search_term) {
            $q->orWhere('title', 'like', $search_term . '%');
            $q->orWhere('syntax', 'like', $search_term);

        })->where(function ($query) {
            $query->where('expire_time', '>', \Carbon\Carbon::now())->orWhereNull('expire_time');
        })->where('status', 1)->orderBy('created_at', 'desc')->paginate(config('settings.pastes_per_page'), ['title', 'syntax', 'slug', 'created_at', 'password', 'expire_time', 'views']);
        
        return view('front.paste.search', compact('pastes'))->with('page_title', __('Search Results'));
    }

    public function archive($slug, Request $request)
    {
        if (config('settings.archive_page') != 1) return redirect()->back()->withErrors(__('This feature is disabled'));
        
        $validator = Validator::make($request->all(), [
            'keyword' => 'nullable|min:2|max:100|eco_string',
            'tag' => 'nullable|min:1|max:100',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }        

        $syntax = get_syntax_by_name($slug);
        
        $pastes = Paste::where('syntax', $slug)->where(function ($query) {
            $query->where('expire_time', '>', \Carbon\Carbon::now())->orWhereNull('expire_time');
        })->where('status', 1);

        if(!empty($request->keyword))
        {
            $pastes = $pastes->where(function ($q) use ($request) {
                $q->orWhere('title', 'like', $request->keyword . '%');
                $q->orWhere('syntax', 'like', $request->keyword);
            });
        }        

        if(!empty($request->tag))
        {
            $pastes = $pastes->where('tags', 'like', '%'. $request->tag . '%');
        }
        
        $pastes = $pastes->orderBy('created_at', 'DESC')->paginate(config('settings.pastes_per_page'), ['title', 'syntax', 'slug', 'created_at', 'password', 'expire_time', 'views']);

        return view('front.paste.archive', compact('pastes', 'syntax'))->with('page_title', $syntax->name . ' ' . __('Archive'));
    }

    public function archiveList()
    {
        if (config('settings.archive_page') != 1) return redirect()->back()->withErrors(__('This feature is disabled'));

        return view('front.paste.archive_list')->with('page_title', __('Archive'));
    }

    public function raw($slug)
    {
        if (config('settings.feature_raw') != 1) {
            return redirect()->back()->withErrors(__('This feature is disabled'));
        }

        $paste = Paste::where('slug', $slug)->whereNull('password')->firstOrfail();
        if ($paste->status == 3) {
            if (\Auth::check()) {
                if ($paste->user_id != \Auth::user()->id) {
                    abort(404);
                }
            } else {
                abort(404);
            }

        }

        if ($paste->self_destroy == 1 && $paste->views > config('settings.self_destroy_after_views') && empty($paste->expire_time)) {
            $paste->expire_time = date("Y-m-d H:i:s");
            $paste->save();
        }

        if (!empty($paste->expire_time)) {
            if (strtotime($paste->expire_time) < time()) {
                return response(__('Paste is expired'), 200)
                ->header('Content-Type', 'text/plain');                
            }
        }

        if (session()->has('already_viewed')) {
            $already_viewed = session('already_viewed');

            if (!in_array($paste->id, $already_viewed)) {
                array_push($already_viewed, $paste->id);
                $paste->views = $paste->views + 1;
                $paste->save();
            }

            session(['already_viewed' => $already_viewed]);
        } else {
            $already_viewed = [$paste->id];
            session(['already_viewed' => $already_viewed]);
            $paste->views = $paste->views + 1;
            $paste->save();
        }
        if ($paste->storage == 2) {
            $paste->content = file_get_contents(ltrim($paste->content, '/'));
        }
        if ($paste->encrypted == 1) {
            $paste->content = decrypt($paste->content);
        }
        else{
            $paste->content = html_entity_decode($paste->content);
        }

        if(!empty(config('settings.blocked_words')))
        {
            $blocked_words = explode(',',config('settings.blocked_words'));
            foreach($blocked_words as $blocked_word){
                $paste->content = preg_replace('/\b'.$blocked_word.'\b/iu', str_repeat("*", strlen($blocked_word)), $paste->content);
            }
        }        

        return response($paste->content, 200)
            ->header('Content-Type', 'text/plain');
    }

    public function download($slug)
    {
        if (config('settings.feature_download') != 1) {
            return redirect()->back()->withErrors(__('This feature is disabled'));
        }

        if (!\Auth::check()) {
            if (config('settings.public_download') != 1) {
                return redirect()->guest(route('login'))->withErrors(__('You must login to download this paste'));
            }
        }

        $paste = Paste::where('slug', $slug)->whereNUll('password')->firstOrfail();
        if ($paste->status == 3) {
            if (\Auth::check()) {
                if ($paste->user_id != \Auth::user()->id) {
                    abort(404);
                }
            } else {
                abort(404);
            }

        }

        if ($paste->self_destroy == 1 && $paste->views > config('settings.self_destroy_after_views') && empty($paste->expire_time)) {
            $paste->expire_time = date("Y-m-d H:i:s");
            $paste->save();
        }

        if (!empty($paste->expire_time)) {
            if (strtotime($paste->expire_time) < time()) {
                return view('errors.expired')->with('page_title', __('Paste is expired'));
            }
        }

        if (session()->has('already_viewed')) {
            $already_viewed = session('already_viewed');

            if (!in_array($paste->id, $already_viewed)) {
                array_push($already_viewed, $paste->id);
                $paste->views = $paste->views + 1;
                $paste->save();
            }

            session(['already_viewed' => $already_viewed]);
        } else {
            $already_viewed = [$paste->id];
            session(['already_viewed' => $already_viewed]);
            $paste->views = $paste->views + 1;
            $paste->save();
        }
        if ($paste->storage == 2) {
            $paste->content = file_get_contents(ltrim($paste->content, '/'));
        }
        if ($paste->encrypted == 1) {
            $paste->content = decrypt($paste->content);
        }
        else{
            $paste->content = html_entity_decode($paste->content);
        }

        if(!empty(config('settings.blocked_words')))
        {
            $blocked_words = explode(',',config('settings.blocked_words'));
            foreach($blocked_words as $blocked_word){
                $paste->content = preg_replace('/\b'.$blocked_word.'\b/iu', str_repeat("*", strlen($blocked_word)), $paste->content);
            }
        } 

        $extension = (!empty(get_syntax_by_name($paste->syntax)->extension)) ? get_syntax_by_name($paste->syntax)->extension : 'txt';

        $response = response($paste->content, 200, [
            'Content-Disposition' => 'attachment; filename="' . $paste->title_f . '.' . $extension . '"',
        ]);

        return $response;
    }

    function clone($slug)
    {
        if (config('settings.feature_clone') != 1) {
            return redirect()->back()->withErrors(__('This feature is disabled'));
        }

        $paste = Paste::where('slug', $slug)->whereNUll('password')->firstOrfail(['syntax', 'content', 'encrypted', 'status', 'expire_time', 'title','storage']);
        if ($paste->status == 3) {
            if (\Auth::check()) {
                if ($paste->user_id != \Auth::user()->id) {
                    abort(404);
                }
            } else {
                abort(404);
            }

        }

        if ($paste->self_destroy == 1 && $paste->views > config('settings.self_destroy_after_views') && empty($paste->expire_time)) {
            $paste->expire_time = date("Y-m-d H:i:s");
            $paste->save();
        }

        if (!empty($paste->expire_time)) {
            if (strtotime($paste->expire_time) < time()) {
                return view('errors.expired')->with('page_title', __('Paste is expired'));
            }
        }

        if (session()->has('already_viewed')) {
            $already_viewed = session('already_viewed');

            if (!in_array($paste->id, $already_viewed)) {
                array_push($already_viewed, $paste->id);
                $paste->views = $paste->views + 1;
                $paste->save();
            }

            session(['already_viewed' => $already_viewed]);
        } else {
            $already_viewed = [$paste->id];
            session(['already_viewed' => $already_viewed]);
            $paste->views = $paste->views + 1;
            $paste->save();
        }
        if ($paste->storage == 2) {
            $paste->content = file_get_contents(ltrim($paste->content, '/'));
        }
        if ($paste->encrypted == 1) {
            $paste->content = decrypt($paste->content);
        }
        else{
            $paste->content = html_entity_decode($paste->content);
        }

        if(!empty(config('settings.blocked_words')))
        {
            $blocked_words = explode(',',config('settings.blocked_words'));
            foreach($blocked_words as $blocked_word){
                $paste->content = preg_replace('/\b'.$blocked_word.'\b/iu', str_repeat("*", strlen($blocked_word)), $paste->content);
            }
        }         

        return view('front.paste.clone', compact('paste'));
    }

    public function embed($slug)
    {
        if (config('settings.feature_embed') != 1) {
            return redirect()->back()->withErrors(__('This feature is disabled'));
        }

        $paste = Paste::where('slug', $slug)->firstOrfail();
        if ($paste->status == 3) {
            if (\Auth::check()) {
                if ($paste->user_id != \Auth::user()->id) {
                    abort(404);
                }
            } else {
                abort(404);
            }

        }

        $extension = (!empty(get_syntax_by_name($paste->syntax)->extension)) ? get_syntax_by_name($paste->syntax)->extension : 'txt';

        if ($paste->self_destroy == 1 && $paste->views > config('settings.self_destroy_after_views') && empty($paste->expire_time)) {
            $paste->expire_time = date("Y-m-d H:i:s");
            $paste->save();
        }

        if (!empty($paste->expire_time)) {
            if (strtotime($paste->expire_time) < time()) {
                return view('errors.expired')->with('page_title', __('Paste is expired'));
            }
        }

        if (session()->has('already_viewed')) {
            $already_viewed = session('already_viewed');

            if (!in_array($paste->id, $already_viewed)) {
                array_push($already_viewed, $paste->id);
                $paste->views = $paste->views + 1;
                $paste->save();
            }

            session(['already_viewed' => $already_viewed]);
        } else {
            $already_viewed = [$paste->id];
            session(['already_viewed' => $already_viewed]);
            $paste->views = $paste->views + 1;
            $paste->save();
        }
        if ($paste->storage == 2) {
            $paste->content = file_get_contents(ltrim($paste->content, '/'));
        }
        if ($paste->encrypted == 1) {
            $paste->content = decrypt($paste->content);
        }

        $paste->extension = $extension;

        return view('front.paste.embed', compact('paste'))->with('page_title', $paste->title_f . ' ' . __('Embed'));
    }

    public function edit($slug)
    {
        $paste = Paste::where('slug', $slug)->where('user_id', \Auth::user()->id)->firstOrfail();
        if ($paste->status == 3) {
            if (\Auth::check()) {
                if ($paste->user_id != \Auth::user()->id) {
                    abort(404);
                }
            } else {
                abort(404);
            }

        }

        if (!empty($paste->expire_time)) {
            if (strtotime($paste->expire_time) < time()) {
                return view('errors.expired')->with('page_title', __('Paste is expired'));
            }
        }

        if ($paste->storage == 2) {
            $paste->content = file_get_contents(ltrim($paste->content, '/'));
        }
        if ($paste->encrypted == 1) {
            $paste->content = decrypt($paste->content);
        }

        return view('front.paste.edit', compact('paste'));
    }

    public function update($slug, Request $request)
    {
        $paste = Paste::where('slug', $slug)->where('user_id', \Auth::user()->id)->firstOrfail();

        if (!empty($paste->expire_time)) {
            if (strtotime($paste->expire_time) < time()) {
                return view('errors.expired')->with('page_title', __('Paste is expired'));
            }
        }
        $title_required = 'nullable';
        if (config('settings.paste_title_required') == 1) $title_required = 'required';
        $validator = Validator::make($request->all(), [
            'content' => 'required|min:1',
            'folder_id' => 'nullable|exists:folders,id',
            'status' => 'required|numeric|in:1,2,3',
            'syntax' => 'nullable|exists:syntax,slug',
            'title' => $title_required . '|max:80|eco_string',
            'password' => 'nullable|max:50|string',
            'description' => 'nullable|string|max:2000',
            'tags' => 'nullable|string|max:200',            
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)->withInput();
        }

        $content_size = strlen($request->content) / 1000;

        if ($content_size > config('settings.max_content_size_kb')) {
            return redirect()->back()->withErrors(__('Max allowed content size is') . ' ' . config('settings.max_content_size_kb') . 'kb')->withInput();
        }

        if(!empty($request->folder_id))
        {
            Folder::where('id',$request->folder_id)->where('user_id',\Auth::user()->id)->firstOrfail();
        }          

        if(!empty(config('settings.banned_words')))
        {
            $banned_words = explode(',',config('settings.banned_words'));
            foreach($banned_words as $banned_word){
                preg_match('/\b'.$banned_word.'\b/iu', $request->content, $matches, PREG_OFFSET_CAPTURE);
                if(count($matches) > 0){
                    return redirect()->back()->withErrors(__('Please remove') . ' "' . $banned_word . '" ' . __('word from your paste content'))->withInput();
                }
            }
        }

        $paste->title = $request->title;
        $paste->description = $request->description;
        $paste->tags = $request->tags;
        $paste->folder_id = $request->folder_id;
        $paste->syntax = (!empty($request->syntax)) ? $request->syntax : config('settings.default_syntax');
        $paste->status = $request->status;

        if($request->remove_password){
            $paste->password = null;
        }

        if ($request->password) {
            $paste->password = \Hash::make($request->password);
        }

        if ($request->encrypted) {
            $paste->encrypted = 1;
            $paste->content = encrypt($request->content);

        } else {
            $paste->encrypted = 0;
            $paste->content = htmlentities($request->content);
        }

        if (config('settings.paste_storage') == 'file') {

            if ($paste->storage == 2) {
                if (file_exists(ltrim($paste->content, '/'))) {
                    unlink(ltrim($paste->content, '/'));
                }
            }

            $paste->storage = 2;
            $content = $paste->content;
            if (\Auth::check()) {
                $destination_path = 'uploads/users/' . \Auth::user()->name;
            } else {
                $destination_path = 'uploads/pastes/' . date('Y') . '/' . date('m') . '/' . date('d');
            }

            if (!file_exists($destination_path)) {
                mkdir($destination_path, 0775, true);
            }
            $filename = str_random(20) . '.txt';
            file_put_contents($destination_path . '/' . $filename, $content);
            $paste->content = '/' . $destination_path . '/' . $filename;
        }

        $paste->save();

        if(auth()->check()) \Cache::forget('my_recent_pastes_'.auth()->user()->id);

        return redirect($paste->url)->withSuccess(__('Paste successfully updated'));
    }

    public function destroy($slug)
    {
        $paste = Paste::where('slug', $slug)->where('user_id', \Auth::user()->id)->firstOrfail();
        update_recent_pastes($paste->id);
        update_trendings($paste->id);        
        $paste->delete();

        if(auth()->check()) \Cache::forget('my_recent_pastes_'.auth()->user()->id);

        return redirect('my-pastes')->withSuccess(__('Paste successfully deleted'));
    }

    public function report(Request $request)
    {
        if (config('settings.feature_report') != 1) {
            return redirect()->back()->withErrors(__('This feature is disabled'));
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:pastes,id',
            'reason' => 'required|eco_long_string|min:10|max:1000',

        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $report = new Report();
        $report->paste_id = $request->id;
        $report->user_id = \Auth::user()->id;
        $report->reason = $request->reason;
        $report->save();

        return redirect()->back()->withSuccess(__('Paste successfully reported'));
    }

    public function getPaste(Request $request)
    {
        $paste = Paste::where('slug', $request->slug)->where(function ($query) {
            $query->where('expire_time', '>', \Carbon\Carbon::now())->orWhereNull('expire_time');
        })->firstOrfail();
        if ($paste->status == 3) {
            if (\Auth::check()) {
                if ($paste->user_id != \Auth::user()->id) {
                    abort(404);
                }
            } else {
                abort(404);
            }

        }
        if ($paste->storage == 2) {
            $paste->content = file_get_contents(ltrim($paste->content, '/'));
        }

        $paste->content = str_replace("\r\n", "\n", $paste->content);

        if (!empty($paste->password)) {
            if (password_verify($request->password, $paste->password)) {

                if ($paste->encrypted == 1) {
                    $paste->content = decrypt($paste->content);
                } else {
                    $paste->content = html_entity_decode($paste->content);
                }

                if(!empty(config('settings.blocked_words')))
                {
                    $blocked_words = explode(',',config('settings.blocked_words'));
                    foreach($blocked_words as $blocked_word){
                        $paste->content = preg_replace('/\b'.$blocked_word.'\b/iu', str_repeat("*", strlen($blocked_word)), $paste->content);
                    }
                }

                $paste->content = urlencode($paste->content);
                $paste->content = base64_encode($paste->content);

                $response = ["status" => "success", "content" => $paste->content];
            } else {
                $message = '<div class="alert alert-danger">' . __('Please enter valid password') . '</div>';
                $response = ["status" => "error", "message" => $message];
            }
        } else {
            if ($paste->encrypted == 1) {
                $paste->content = decrypt($paste->content);
            } else {
                $paste->content = html_entity_decode($paste->content);
            }

            if(!empty(config('settings.blocked_words')))
            {
                $blocked_words = explode(',',config('settings.blocked_words'));
                foreach($blocked_words as $blocked_word){
                    $paste->content = preg_replace('/\b'.$blocked_word.'\b/iu', str_repeat("*", strlen($blocked_word)), $paste->content);
                }
            }

            $paste->content = urlencode($paste->content);
            $paste->content = base64_encode($paste->content);            

            $response = ["status" => "success", "content" => $paste->content];
        }

        return response()->json($response);
    }

    public function trending()
    {
        if (config('settings.trending_page') != 1) return redirect()->back()->withErrors(__('This feature is disabled'));

        $trending_today = get_trending_today();

        $trending_week = get_trending_week();

        $trending_month = get_trending_month();

        $trending_year = get_trending_year();

        return view('front.paste.trending', compact('trending_today', 'trending_week', 'trending_month', 'trending_year'))->with('page_title', __('Trending'));
    }
}
