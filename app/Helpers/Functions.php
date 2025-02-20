<?php

if(!function_exists('get_recent_pastes'))
{
    function get_recent_pastes()
    {
        return \Cache::remember('recent_pastes',90,function(){
            return \App\Models\Paste::where('status', 1)->where(function ($query) {
                $query->where('expire_time', '>', \Carbon\Carbon::now())->orWhereNull('expire_time');
            })->orderBy('created_at', 'desc')->limit(config('settings.recent_pastes_limit'))->get(['title', 'syntax', 'slug', 'created_at', 'expire_time', 'views','password']);
        });
    }
}

if(!function_exists('get_my_recent_pastes'))
{
    function get_my_recent_pastes()
    {
        if(auth()->check()){
            return \Cache::remember('my_recent_pastes_'.auth()->user()->id,60,function(){
                return \App\Models\Paste::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->limit(config('settings.my_recent_pastes_limit'))->get(['title', 'syntax', 'slug', 'created_at', 'password', 'expire_time', 'views']);
            });
        }
        else{
            return [];
        }
    }
}

if(!function_exists('update_recent_pastes'))
{
    function update_recent_pastes($id)
    {
        foreach(get_recent_pastes() as $paste)
        {
            if($paste->id == $id){
                \Cache::forget('recent_pastes');
                return;
            }
        }
    }
}

if(!function_exists('get_popular_syntaxes'))
{
    function get_popular_syntaxes()
    {
        return \Cache::remember('popular_syntaxes',9000,function(){
            return \App\Models\Syntax::where('active', 1)->where('popular', 1)->get();
        });
    }
}

if(!function_exists('get_syntaxes'))
{
    function get_syntaxes()
    {
        return \Cache::remember('syntaxes',9000,function(){
            return \App\Models\Syntax::where('active', 1)->where('popular', 0)->get();
        });
    }
}

if(!function_exists('get_all_syntaxes'))
{
    function get_all_syntaxes()
    {
        return \Cache::remember('all_syntaxes',9000,function(){
            return \App\Models\Syntax::where('active', 1)->orderby('name')->get();
        });
    }
}

if(!function_exists('get_syntax_name'))
{
    function get_syntax_name($slug)
    {
        foreach (get_all_syntaxes() as $syntax) {
            if($syntax->slug == $slug) return $syntax->name;
        }
        return 'Plaintext';
    }
}

if(!function_exists('get_syntax_by_name'))
{
    function get_syntax_by_name($slug)
    {
        foreach (get_all_syntaxes() as $syntax) {
            if($syntax->slug == $slug) return $syntax;
        }
        return [];
    }
}

if(!function_exists('get_my_folders'))
{
    function get_my_folders()
    {
        if(auth()->check())
        {
            return \Cache::remember('my_folders_'.auth()->user()->id,90,function(){
                return \App\Models\Folder::where('user_id',\Auth::user()->id)->orderBy('name')->get();  
            });
        }
        else{
            return [];
        }
    }
}

if(!function_exists('get_pages_menu'))
{
    function get_pages_menu()
    {
        return \Cache::remember('pages_menu',9000,function(){
            return \App\Models\Page::where('active',1)->where('slug','!=','ace-editor-help')->orderBy('title')->get(['id','title','slug']); 
        });
    }
}

if(!function_exists('get_locales'))
{
    function get_locales()
    {
        return \Cache::remember('locales',9000,function(){
            return \App\Models\Language::orderBy('name')->get(['name', 'code','country_code']);  
        });
    }
}

if(!function_exists('get_selected_locale'))
{
    function get_selected_locale()
    {
        foreach (get_locales() as $locale) {
            if($locale->code == \App::getLocale()) return $locale;
        }
    }
}

if(!function_exists('get_trending_today'))
{
    function get_trending_today()
    {
        return \Cache::remember('trending_today',300, function(){ 
            return \App\Models\Paste::where('status', 1)->whereDate('created_at', date('Y-m-d'))->orderby('views', 'DESC')->limit(config('settings.trending_pastes_limit'))->get(['title', 'syntax', 'slug', 'created_at', 'password', 'expire_time', 'views']);
        });
    }
}

if(!function_exists('get_trending_week'))
{
    function get_trending_week()
    {
        return \Cache::remember('trending_week',3000, function(){ 
            return \App\Models\Paste::where('status', 1)->whereBetween('created_at', [
                \Carbon\Carbon::parse('last monday')->startOfDay(),
                \Carbon\Carbon::parse('next sunday')->endOfDay(),
            ])->orderby('views', 'DESC')->limit(config('settings.trending_pastes_limit'))->get(['title', 'syntax', 'slug', 'created_at', 'password', 'expire_time', 'views']);
        });
    }
}

if(!function_exists('get_trending_month'))
{
    function get_trending_month()
    {
        return \Cache::remember('trending_month',6000, function(){ 
            return \App\Models\Paste::where('status', 1)->whereBetween('created_at', [
                \Carbon\Carbon::now()->startOfMonth(),
                \Carbon\Carbon::now()->endOfMonth(),
            ])->orderby('views', 'DESC')->limit(config('settings.trending_pastes_limit'))->get(['title', 'syntax', 'slug', 'created_at', 'password', 'expire_time', 'views']);
        });
    }
}

if(!function_exists('get_trending_year'))
{
    function get_trending_year()
    {
        return \Cache::remember('trending_year',9000, function(){ 
            return \App\Models\Paste::where('status', 1)->whereBetween('created_at', [
                \Carbon\Carbon::now()->startOfYear(),
                \Carbon\Carbon::now()->endOfYear(),
            ])->orderby('views', 'DESC')->limit(config('settings.trending_pastes_limit'))->get(['title', 'syntax', 'slug', 'created_at', 'password', 'expire_time', 'views']);
        });
    }
}

if(!function_exists('update_trendings'))
{
    function update_trendings($id)
    {
        foreach(get_trending_today() as $paste){
            if($paste->id == $id){
                \Cache::forget('trending_today');
                break;
            }
        }        

        foreach(get_trending_week() as $paste){
            if($paste->id == $id){
                \Cache::forget('trending_week');
                break;
            }
        }        

        foreach(get_trending_month() as $paste){
            if($paste->id == $id){
                \Cache::forget('trending_month');
                break;
            }
        }        

        foreach(get_trending_year() as $paste){
            if($paste->id == $id){
                \Cache::forget('trending_year');
                break;
            }
        }
        return;
    }
}



if(!function_exists('curl_post_contents'))
{
    function curl_post_contents($url,$params)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);  

        // In real life you should use something like:
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $server_output = curl_exec($ch);
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close ($ch);

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
                
        if($header['errno'] != 0) die('cURL Error - bad url, timeout, redirect loop');

        if ($header['http_code'] != 200) die('cURL Error - no page, no permissions, no service');

        // Further processing ...
        return $server_output;
    }
}

if(!function_exists('curl_get_contents'))
{
    function curl_get_contents($url)
    {
        $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

        $options = array(

            CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
            CURLOPT_POST           =>false,        //set to GET
            CURLOPT_USERAGENT      => $user_agent, //set user agent
            CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
            CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;

        if($header['errno'] != 0) die('cURL Error - bad url, timeout, redirect loop');

        if ($header['http_code'] != 200) die('cURL Error - no page, no permissions, no service');

        return $header['content'];
    }
}
