<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Validator;

class SettingController extends Controller
{
    public function edit()
    {
        $data = Setting::get(['key', 'value'])->toArray();
        $settings = array();
        foreach ($data as $d) {
            $settings[$d['key']] = $d['value'];
        }

        return view('admin.settings.index', compact('settings'))->with('page_title', 'Settings');
    }

    public function update(Request $request)
    {
        header("X-XSS-Protection: 0");

        if ($request->type == 'general') {
            $validator = Validator::make($request->all(), [
                'site_name' => 'required|min:2|max:50',
                'site_email' => 'required|email|max:100',
                'default_locale' => 'required|exists:languages,code',
                'default_timezone' => 'required|timezone',
                'site_logo' => 'nullable|mimes:png,gif|max:200',
                'background_image' => 'nullable|mimes:png,jpg,jpeg|max:500',
                'site_favicon' => 'nullable|mimes:png,ico|max:100',
                'footer_text' => 'nullable|string|max:5000',
                'registration_open' => 'required|numeric|in:0,1',
                'site_layout' => 'required|numeric|in:1,2',
                'auto_approve_user' => 'required|numeric|in:0,1',
                'site_skin' => 'required|alpha|in:default,brown,teal,unique,orange,pink,special',
                'maintenance_mode' => 'required|numeric|in:0,1',
                'maintenance_text' => 'required|string',
                'navbar' => 'required|in:default,fixed',
                'string_validation' => 'required|numeric|in:1,2',
                'ad_block_detection' => 'required|numeric|in:0,1',
                'cookie_consent_notification' => 'required|numeric|in:0,1',
                'background_color' => 'required|eco_color',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('type', $request->type)->withInput()
                    ->withErrors($validator);
            }

            Setting::where('key', 'site_name')->update(['value' => $request->site_name]);
            Setting::where('key', 'site_email')->update(['value' => $request->site_email]);
            Setting::where('key', 'default_locale')->update(['value' => $request->default_locale]);
            Setting::where('key', 'default_timezone')->update(['value' => $request->default_timezone]);
            Setting::where('key', 'footer_text')->update(['value' => $request->footer_text]);
            Setting::where('key', 'registration_open')->update(['value' => $request->registration_open]);
            Setting::where('key', 'site_layout')->update(['value' => $request->site_layout]);

            Setting::where('key', 'auto_approve_user')->update(['value' => $request->auto_approve_user]);
            Setting::where('key', 'site_skin')->update(['value' => $request->site_skin]);
            Setting::where('key', 'maintenance_mode')->update(['value' => $request->maintenance_mode]);
            Setting::where('key', 'maintenance_text')->update(['value' => $request->maintenance_text]);


            Setting::where('key', 'navbar')->update(['value' => $request->navbar]);
            Setting::where('key', 'string_validation')->update(['value' => $request->string_validation]);
            Setting::where('key', 'background_color')->update(['value' => $request->background_color]);
            Setting::where('key', 'ad_block_detection')->update(['value' => $request->ad_block_detection]);
            Setting::where('key', 'cookie_consent_notification')->update(['value' => $request->cookie_consent_notification]);

            if ($request->hasFile('site_logo')) {
                $old_logo = config('settings.site_logo');
                if (!empty($old_logo)) {
                    if (file_exists(ltrim($old_logo, '/'))) {
                        @unlink(ltrim($old_logo, '/'));
                    }

                }
                $site_logo = $request->file('site_logo');
                $file_ext = $site_logo->getClientOriginalExtension();
                $site_logo_name = str_random(10) . '.'.$file_ext;
                $destinationPath = 'uploads';
                $site_logo->move($destinationPath, $site_logo_name);

                $site_logo_path = '/' . $destinationPath . '/' . $site_logo_name;
                Setting::where('key', 'site_logo')->update(['value' => $site_logo_path]);
            }

            if ($request->hasFile('site_favicon')) {
                $old_favicon = config('settings.site_favicon');
                if (!empty($old_favicon)) {
                    if (file_exists(ltrim($old_favicon, '/'))) {
                        @unlink(ltrim($old_favicon, '/'));
                    }

                }
                $site_favicon = $request->file('site_favicon');
                $file_ext = $site_favicon->getClientOriginalExtension();
                $site_favicon_name = str_random(10) . '.'.$file_ext;
                $destinationPath = 'uploads';
                $site_favicon->move($destinationPath, $site_favicon_name);

                $site_favicon_path = '/' . $destinationPath . '/' . $site_favicon_name;
                Setting::where('key', 'site_favicon')->update(['value' => $site_favicon_path]);
            }

            if ($request->hasFile('background_image')) {
                $old_background_image = config('settings.background_image');
                if (!empty($old_background_image)) {
                    if (file_exists(ltrim($old_background_image, '/'))) {
                        @unlink(ltrim($old_background_image, '/'));
                    }

                }
                $background_image = $request->file('background_image');
                $background_image_name = str_random(10) . '.png';
                $destinationPath = 'uploads';
                $background_image->move($destinationPath, $background_image_name);

                $background_image_path = '/' . $destinationPath . '/' . $background_image_name;
                Setting::where('key', 'background_image')->update(['value' => $background_image_path]);
            }

            if ($request->remove_logo) {
                $old_logo = config('settings.site_logo');
                if (!empty($old_logo)) {
                    if (file_exists(ltrim($old_logo, '/'))) {
                        @unlink(ltrim($old_logo, '/'));
                    }

                }
                Setting::where('key', 'site_logo')->update(['value' => '']);
            }

            if ($request->remove_background_image) {
                $old_background_image = config('settings.background_image');
                if (!empty($old_background_image)) {
                    if (file_exists(ltrim($old_background_image, '/'))) {
                        @unlink(ltrim($old_background_image, '/'));
                    }

                }
                Setting::where('key', 'background_image')->update(['value' => '']);
            }

        } elseif ($request->type == 'paste') {
            $validator = Validator::make($request->all(), [
                'paste_slug_type' => 'required|in:title,random',
                'default_syntax' => 'required|exists:syntax,slug',
                'max_folders_per_user' => 'required|numeric',
                'paste_editor_height' => 'required|numeric',
                'syntax_highlighter_break_word' => 'required|numeric|in:0,1',
                'paste_editor_line_numbers' => 'required|numeric|in:0,1',
                'syntax_highlighter_line_numbers' => 'required|numeric|in:0,1',
                'public_paste' => 'required|numeric|in:0,1',
                'public_download' => 'required|numeric|in:0,1',
                'paste_title_required' => 'required|numeric|in:0,1',
                'user_paste' => 'required|numeric|in:0,1',
                'recent_pastes_limit' => 'required|numeric|min:0|max:20',
                'self_destroy_after_views' => 'required|numeric',
                'pastes_per_page' => 'required|numeric|min:10|max:500',
                'my_recent_pastes_limit' => 'required|numeric|min:0|max:20',
                'daily_paste_limit_unauth' => 'required|numeric|min:0|max:20000',
                'daily_paste_limit_auth' => 'required|numeric|min:0|max:20000',
                'max_content_size_kb' => 'required|numeric|min:1|max:3000',
                'syntax_highlighting_style' => 'required|in:default,dark,okadia,funky,coy,solarized-light,tomorrow-night,twilight',
                'paste_page_layout' => 'required|numeric|in:1,2',
                'trending_pastes_limit' => 'required|numeric|max:100',
                'paste_time_restrict_auth' => 'required|numeric',
                'paste_time_restrict_unauth' => 'required|numeric',
                'paste_storage' => 'required|alpha|in:database,file',
                'paste_editor' => 'required|alpha|in:default,ace,codemirror',
                'syntax_highlighter' => 'required|alpha|in:prismjs,ace,codemirror',
                'ace_editor_skin' => 'required|in:chrome,clouds,crimson_editor,dawn,dreamweaver,eclipse,github,iplastic,solarized_light,textmate,tomorrow,xcode,kuroir,katzenmilch,sqlserver,ambiance,chaos,clouds_midnight,dracula,cobalt,gruvbox,gob,idle_fingers,kr_theme,merbivore,merbivore_soft,mono_industrial,monokai,pastel_on_dark,solarized_dark,terminal,tomorrow_night,tomorrow_night_blue,tomorrow_night_bright,tomorrow_night_eighties,twilight,vibrant_ink',
                'codemirror_skin' => 'required',
                'paste_view_height' => 'required|in:auto,scroll',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('type', $request->type)->withInput()
                    ->withErrors($validator);
            }

            Setting::where('key', 'paste_view_height')->update(['value' => $request->paste_view_height]);
            Setting::where('key', 'paste_editor_height')->update(['value' => $request->paste_editor_height]);
            Setting::where('key', 'paste_slug_type')->update(['value' => $request->paste_slug_type]);
            Setting::where('key', 'max_folders_per_user')->update(['value' => $request->max_folders_per_user]);
            Setting::where('key', 'paste_editor_line_numbers')->update(['value' => $request->paste_editor_line_numbers]);
            Setting::where('key', 'syntax_highlighter_line_numbers')->update(['value' => $request->syntax_highlighter_line_numbers]);
            Setting::where('key', 'syntax_highlighting_style')->update(['value' => $request->syntax_highlighting_style]);
            Setting::where('key', 'recent_pastes_limit')->update(['value' => $request->recent_pastes_limit]);
            Setting::where('key', 'self_destroy_after_views')->update(['value' => $request->self_destroy_after_views]);
            Setting::where('key', 'my_recent_pastes_limit')->update(['value' => $request->my_recent_pastes_limit]);
            Setting::where('key', 'max_content_size_kb')->update(['value' => $request->max_content_size_kb]);
            Setting::where('key', 'daily_paste_limit_unauth')->update(['value' => $request->daily_paste_limit_unauth]);
            Setting::where('key', 'daily_paste_limit_auth')->update(['value' => $request->daily_paste_limit_auth]);
            Setting::where('key', 'public_paste')->update(['value' => $request->public_paste]);
            Setting::where('key', 'pastes_per_page')->update(['value' => $request->pastes_per_page]);
            Setting::where('key', 'paste_page_layout')->update(['value' => $request->paste_page_layout]);
            Setting::where('key', 'trending_pastes_limit')->update(['value' => $request->trending_pastes_limit]);
            Setting::where('key', 'paste_time_restrict_auth')->update(['value' => $request->paste_time_restrict_auth]);
            Setting::where('key', 'paste_time_restrict_unauth')->update(['value' => $request->paste_time_restrict_unauth]);
            Setting::where('key', 'paste_storage')->update(['value' => $request->paste_storage]);
            Setting::where('key', 'paste_editor')->update(['value' => $request->paste_editor]);
            Setting::where('key', 'syntax_highlighter')->update(['value' => $request->syntax_highlighter]);
            Setting::where('key', 'ace_editor_skin')->update(['value' => $request->ace_editor_skin]);
            Setting::where('key', 'user_paste')->update(['value' => $request->user_paste]);
            Setting::where('key', 'public_download')->update(['value' => $request->public_download]);
            Setting::where('key', 'paste_title_required')->update(['value' => $request->paste_title_required]);
            Setting::where('key', 'syntax_highlighter_break_word')->update(['value' => $request->syntax_highlighter_break_word]);
            Setting::where('key', 'default_syntax')->update(['value' => $request->default_syntax]);
            Setting::where('key', 'codemirror_skin')->update(['value' => $request->codemirror_skin]);

            if ($request->qr_code_share) {
                $qr_code_share = 1;
            } else {
                $qr_code_share = 0;
            }

            if ($request->feature_share) {
                $feature_share = 1;
            } else {
                $feature_share = 0;
            }

            if ($request->feature_copy) {
                $feature_copy = 1;
            } else {
                $feature_copy = 0;
            }

            if ($request->feature_raw) {
                $feature_raw = 1;
            } else {
                $feature_raw = 0;
            }

            if ($request->feature_download) {
                $feature_download = 1;
            } else {
                $feature_download = 0;
            }

            if ($request->feature_clone) {
                $feature_clone = 1;
            } else {
                $feature_clone = 0;
            }

            if ($request->feature_embed) {
                $feature_embed = 1;
            } else {
                $feature_embed = 0;
            }

            if ($request->feature_report) {
                $feature_report = 1;
            } else {
                $feature_report = 0;
            }

            if ($request->feature_print) {
                $feature_print = 1;
            } else {
                $feature_print = 0;
            }

            if ($request->trending_page) {
                $trending_page = 1;
            } else {
                $trending_page = 0;
            }

            if ($request->search_page) {
                $search_page = 1;
            } else {
                $search_page = 0;
            }

            if ($request->archive_page) {
                $archive_page = 1;
            } else {
                $archive_page = 0;
            }

            Setting::where('key', 'feature_share')->update(['value' => $feature_share]);
            Setting::where('key', 'feature_copy')->update(['value' => $feature_copy]);
            Setting::where('key', 'feature_raw')->update(['value' => $feature_raw]);
            Setting::where('key', 'feature_download')->update(['value' => $feature_download]);
            Setting::where('key', 'feature_clone')->update(['value' => $feature_clone]);
            Setting::where('key', 'feature_embed')->update(['value' => $feature_embed]);
            Setting::where('key', 'feature_report')->update(['value' => $feature_report]);
            Setting::where('key', 'feature_print')->update(['value' => $feature_print]);
            Setting::where('key', 'qr_code_share')->update(['value' => $qr_code_share]);
            Setting::where('key', 'archive_page')->update(['value' => $archive_page]);
            Setting::where('key', 'search_page')->update(['value' => $search_page]);
            Setting::where('key', 'trending_page')->update(['value' => $trending_page]);

        } elseif ($request->type == "social_auth") {
            $validator = Validator::make($request->all(), [
                'social_login_facebook' => 'required|numeric|in:0,1',
                'social_login_twitter' => 'required|numeric|in:0,1',
                'social_login_google' => 'required|numeric|in:0,1',
                'FACEBOOK_CLIENT_ID' => 'nullable|string',
                'FACEBOOK_CLIENT_SECRET' => 'nullable|string',
                'TWITTER_CLIENT_ID' => 'nullable|string',
                'TWITTER_CLIENT_SECRET' => 'nullable|string',
                'GOOGLE_CLIENT_ID' => 'nullable|string',
                'GOOGLE_CLIENT_SECRET' => 'nullable|string',
                'facebook_app_id' => 'nullable|string',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('type', $request->type)->withInput()
                    ->withErrors($validator);
            }
            Setting::where('key', 'social_login_facebook')->update(['value' => $request->social_login_facebook]);
            Setting::where('key', 'social_login_twitter')->update(['value' => $request->social_login_twitter]);
            Setting::where('key', 'social_login_google')->update(['value' => $request->social_login_google]);
            Setting::where('key', 'FACEBOOK_CLIENT_ID')->update(['value' => $request->FACEBOOK_CLIENT_ID]);
            Setting::where('key', 'FACEBOOK_CLIENT_SECRET')->update(['value' => $request->FACEBOOK_CLIENT_SECRET]);
            Setting::where('key', 'TWITTER_CLIENT_ID')->update(['value' => $request->TWITTER_CLIENT_ID]);
            Setting::where('key', 'TWITTER_CLIENT_SECRET')->update(['value' => $request->TWITTER_CLIENT_SECRET]);
            Setting::where('key', 'GOOGLE_CLIENT_ID')->update(['value' => $request->GOOGLE_CLIENT_ID]);
            Setting::where('key', 'GOOGLE_CLIENT_SECRET')->update(['value' => $request->GOOGLE_CLIENT_SECRET]);
            Setting::where('key', 'facebook_app_id')->update(['value' => $request->facebook_app_id]);


        } elseif ($request->type == 'advertisement') {
            $validator = Validator::make($request->all(), [
                'ad' => 'required|numeric|in:0,1',
                'ad1' => 'nullable|max:5000',
                'ad2' => 'nullable|max:5000',
                'ad3' => 'nullable|max:5000',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('type', $request->type)->withInput()
                    ->withErrors($validator);
            }

            Setting::where('key', 'ad')->update(['value' => $request->ad]);
            Setting::where('key', 'ad1')->update(['value' => htmlentities($request->ad1)]);
            Setting::where('key', 'ad2')->update(['value' => htmlentities($request->ad2)]);
            Setting::where('key', 'ad3')->update(['value' => htmlentities($request->ad3)]);

        } elseif ($request->type == 'seo') {

            $validator = Validator::make($request->all(), [
                'meta_keywords' => 'nullable|string|max:500',
                'meta_description' => 'nullable|string|max:500',
                'analytics_code' => 'nullable',
                'header_code' => 'nullable',
                'footer_code' => 'nullable',
                'site_image' => 'nullable|mimes:png,jpg,jpeg|max:200',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('type', $request->type)->withInput()
                    ->withErrors($validator);
            }

            if ($request->hasFile('site_image')) {
                $site_image = $request->file('site_image');
                $site_image_name = str_random(10) . '.png';
                $destinationPath = 'uploads';
                $site_image->move($destinationPath, $site_image_name);

                $site_image_path = '/' . $destinationPath . '/' . $site_image_name;
                Setting::where('key', 'site_image')->update(['value' => $site_image_path]);
            }

            if ($request->remove_site_image) {
                $old_logo = config('settings.site_image');
                if (!empty($old_logo)) {
                    if (file_exists(ltrim($old_logo, '/'))) {
                        @unlink(ltrim($old_logo, '/'));
                    }

                }
                Setting::where('key', 'site_image')->update(['value' => '']);
            }

            Setting::where('key', 'meta_keywords')->update(['value' => $request->meta_keywords]);
            Setting::where('key', 'meta_description')->update(['value' => $request->meta_description]);
            Setting::where('key', 'analytics_code')->update(['value' => htmlentities($request->analytics_code)]);
            Setting::where('key', 'header_code')->update(['value' => htmlentities($request->header_code)]);
            Setting::where('key', 'footer_code')->update(['value' => htmlentities($request->footer_code)]);

        } elseif ($request->type == 'comments') {
            $validator = Validator::make($request->all(), [
                'comments' => 'required|numeric|in:0,1',
                'facebook_comments' => 'required|numeric|in:0,1',
                'comments_code' => 'nullable|max:3000',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('type', $request->type)->withInput()
                    ->withErrors($validator);
            }
            Setting::where('key', 'comments')->update(['value' => $request->comments]);
            Setting::where('key', 'facebook_comments')->update(['value' => $request->facebook_comments]);
            Setting::where('key', 'comments_code')->update(['value' => htmlentities($request->comments_code)]);

        } elseif ($request->type == 'captcha') {
            $validator = Validator::make($request->all(), [
                'captcha' => 'required|numeric|in:0,1',
                'recaptcha_v2_site_key' => 'nullable|max:255',
                'recaptcha_v2_secret_key' => 'nullable|max:255',                
                'captcha_for_verified_users' => 'required|numeric|in:0,1',

            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('type', $request->type)->withInput()
                    ->withErrors($validator);
            }
            Setting::where('key', 'captcha')->update(['value' => $request->captcha]);
            Setting::where('key', 'recaptcha_v2_site_key')->update(['value' => $request->recaptcha_v2_site_key]);
            Setting::where('key', 'recaptcha_v2_secret_key')->update(['value' => $request->recaptcha_v2_secret_key]);
            Setting::where('key', 'captcha_for_verified_users')->update(['value' => $request->captcha_for_verified_users]);


        } elseif ($request->type == 'mail') {
            $validator = Validator::make($request->all(), [
                'mail_driver' => 'required|in:mail,smtp,sendmail,mailgun,mandrill,ses,log,sparkpost',
                'mail_host' => 'nullable|max:255',
                'mail_port' => 'nullable|max:10',
                'mail_encryption' => 'nullable|max:10',
                'mail_username' => 'nullable|max:100',
                'mail_password' => 'nullable|max:100',
                'mail_from_address' => 'nullable|max:100',
                'mail_from_name' => 'nullable|max:100',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('type', $request->type)->withInput()
                    ->withErrors($validator);
            }
            Setting::where('key', 'mail_driver')->update(['value' => $request->mail_driver]);
            Setting::where('key', 'mail_host')->update(['value' => $request->mail_host]);
            Setting::where('key', 'mail_port')->update(['value' => $request->mail_port]);
            Setting::where('key', 'mail_encryption')->update(['value' => $request->mail_encryption]);
            Setting::where('key', 'mail_username')->update(['value' => $request->mail_username]);
            Setting::where('key', 'mail_password')->update(['value' => $request->mail_password]);
            Setting::where('key', 'mail_from_address')->update(['value' => $request->mail_from_address]);
            Setting::where('key', 'mail_from_name')->update(['value' => $request->mail_from_name]);

        } elseif ($request->type == 'social_links') {
            $validator = Validator::make($request->all(), [
                'social_fb' => 'nullable|max:255',
                'social_tw' => 'nullable|max:255',
                'social_gp' => 'nullable|max:255',
                'social_lin' => 'nullable|max:255',
                'social_insta' => 'nullable|max:255',
                'social_pin' => 'nullable|max:255',
                'social_tg' => 'nullable|max:255',
                'social_disc' => 'nullable|max:255',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('type', $request->type)->withInput()
                    ->withErrors($validator);
            }
            Setting::where('key', 'social_fb')->update(['value' => $request->social_fb]);
            Setting::where('key', 'social_tw')->update(['value' => $request->social_tw]);
            Setting::where('key', 'social_gp')->update(['value' => $request->social_gp]);
            Setting::where('key', 'social_lin')->update(['value' => $request->social_lin]);
            Setting::where('key', 'social_insta')->update(['value' => $request->social_insta]);
            Setting::where('key', 'social_tg')->update(['value' => $request->social_tg]);
            Setting::where('key', 'social_pin')->update(['value' => $request->social_pin]);
            Setting::where('key', 'social_disc')->update(['value' => $request->social_disc]);
        } elseif ($request->type == 'spam-protection') {
            $validator = Validator::make($request->all(), [
                'blocked_ips' => 'nullable|max:10000',
                'blocked_words' => 'nullable|max:10000',
                'banned_words' => 'nullable|max:10000',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('type', $request->type)->withInput()
                    ->withErrors($validator);
            }
            Setting::where('key', 'blocked_ips')->update(['value' => $request->blocked_ips]);
            Setting::where('key', 'blocked_words')->update(['value' => $request->blocked_words]);
            Setting::where('key', 'banned_words')->update(['value' => $request->banned_words]);
        } else {
            return redirect()->back();
        }

        \Cache::forget('settings');

        return redirect()->back()->with('type', $request->type)->withSuccess('Settings successfully updated.');
    }
}
