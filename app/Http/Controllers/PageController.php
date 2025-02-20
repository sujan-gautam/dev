<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Paste;
use App\Models\Syntax;
use Illuminate\Http\Request;
use Mail;
use Validator;

class PageController extends Controller
{

    public function show($slug)
    {
        $page = \Cache::remember('page_'.$slug,6000,function() use($slug){
            return Page::where('slug', $slug)->where('active', 1)->firstOrfail();
        });

        $description = trim(preg_replace('/\s+/', ' ', strip_tags($page->content)));

        $page->description = str_limit($description, 200, '');

        return view('front.page.show', compact('page'))->with('page_title', $page->title);
    }

    public function contact()
    {
        return view('front.page.contact')->with('page_title', __('Contact Us'));
    }

    public function contactPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|eco_alpha_spaces|min:2|max:100',
            'email' => 'required|email|max:100',
            'message' => 'required|string|min:10|max:5000',
            'g-recaptcha-response' => (config('settings.captcha') == 1) ? 'required|captcha' : ''
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        } else {

            try {
                Mail::send('emails.contact', ['request' => $request], function ($m) {
                    $m->to(config('settings.site_email'))->subject(config('settings.site_name') . ' - ' . __('Contact Message'));
                });
            } catch (\Exception $e) {
                \Log::info($e->getMessage());
                return redirect('contact')->with('warning', __('Your message was not sent due to invalid mail configuration'));
            }

            return redirect('contact')->with('success', __('Your message successfully sent'));
        }

    }

    public function sitemaps()
    {
        $first_product = Paste::orderBy('created_at')->firstOrfail(['created_at']);

        $last_product = Paste::orderBy('created_at','DESC')->firstOrfail(['created_at']);

        $start_date = $first_product->created_at->format('Y-m-d');
        $end_date = $last_product->created_at->format('Y-m-d');

        return response()->view('front.page.sitemaps',compact('start_date','end_date'))->header('Content-Type', 'text/xml');
    }


    public function sitemapMain()
    {
        return response()->view('front.page.sitemap_main')->header('Content-Type', 'text/xml');
    }

    public function sitemap($date)
    {
        if($date == date('Y-m-d'))
        {
            $pastes = Paste::where('status', 1)->where(function ($query) {
                $query->where('expire_time', '>', \Carbon\Carbon::now())->orWhereNull('expire_time');
            })->whereDate('created_at',$date)
            ->orderBy('created_at', 'DESC')->get(['id', 'slug']);

            $users = \App\Models\User::where('status',1)->whereDate('created_at',$date)->orderBy('created_at','DESC')->get(['id','name','avatar','role','status']);
        }
        else
        {
            $pastes = \Cache::remember('pastes_sitemap_'.$date,6000,function() use($date){
                return Paste::where('status', 1)->where(function ($query) {
                    $query->where('expire_time', '>', \Carbon\Carbon::now())->orWhereNull('expire_time');
                })->whereDate('created_at',$date)
                ->orderBy('created_at', 'DESC')->get(['id', 'slug']);
            });

            $users = \Cache::remember('users_sitemap_'.$date,6000,function() use($date){
                return \App\Models\User::where('status',1)->whereDate('created_at',$date)->orderBy('created_at','DESC')->get(['id','name','avatar','role','status']);
            });
        }

        return response()->view('front.page.sitemap',compact('pastes','users'))->header('Content-Type', 'text/xml');
    }

    public function redirect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        } 

        try {
            $url = base64_decode($request->url);

        } catch (\Exception $e) {
            return redirect('/')->withErrors(__('The url format is invalid'));     
        }

        return view('front.page.redirect',compact('url'))->With('page_title',__('Leaving').' '.config('settings.site_name'));
    }
}
