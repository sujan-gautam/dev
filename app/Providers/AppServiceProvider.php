<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Config;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

       if (env('DB_DATABASE') == 'MY_DBNAME') {
            redirect('/install')->send();
        }

        if (starts_with(env('APP_URL', 'http'), 'https')) {
            \URL::forceScheme('https');
        }

        $settings = \Cache::remember('settings',600,function(){
            return \App\Models\Setting::get(['key', 'value']);
        }); 

        foreach ($settings as $setting) {
            \Config::set('settings.' . $setting->key, $setting->value);
        }

        /**
         * Validate ExistsInDatabase or 0/null
         */
        \Validator::extend(
            'exists_or_null',
            function ($attribute, $value, $parameters) {
                if ($value == 0 || is_null($value)) {
                    return true;
                } else {
                    $validator = Validator::make([$attribute => $value], [
                        $attribute => 'exists:' . implode(",", $parameters),
                    ]);
                    return !$validator->fails();
                }
            }
        );


        //Add this custom validation rule.
        \Validator::extend('eco_alpha_spaces', function ($attribute, $value) {

            // This will only accept alpha and spaces.
            // If you want to accept hyphens use: /^[\pL\s-]+$/u.
            return preg_match('/^[\pL\s]+$/u', $value);

        });

        \Validator::extend('alpha_num_spaces', function ($attribute, $value) {
            return preg_match('/^[a-zA-Z0-9\s]+$/', $value);
        });        


        \Validator::extend('eco_alpha_num_spaces', function ($attribute, $value) {
            return preg_match('/^[\p{L}\p{N}\040\]+$/u', $value);
        });

        \Validator::extend('eco_string', function ($attribute, $value) {
            if(config('settings.string_validation') == 2) return is_string($value);
            else return preg_match('/^[\p{L}\p{N}\040\_.-]+$/u', $value);
        });        

        \Validator::extend('eco_long_string', function ($attribute, $value) {
            if(config('settings.string_validation') == 2) return is_string($value);
            else return preg_match('/^[\p{L}\p{N}\r\t\n\040\_.-]+$/u', $value);
        });

        \Validator::extend('eco_color', function ($attribute, $value) {
            return preg_match('/#([a-f0-9]{3}){1,2}\b/i', $value);
        });

        

        if (!session('language')) {
            \Config::set('app.locale', config('settings.default_locale'));
        }

        //Timezone
        \Config::set('app.timezone', config('settings.default_timezone'));
        date_default_timezone_set(config('settings.default_timezone'));

        if(empty(config('settings.recaptcha_v2_site_key')) || empty(config('settings.recaptcha_v2_secret_key'))) \Config::set('settings.captcha', 0);


        \Validator::extend('captcha', function ($attribute, $value, $parameters) {
            

            $response = curl_post_contents('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('settings.recaptcha_v2_secret_key'),
                'response' => $value,
            ]);

            $data = json_decode($response);

            return $data->success;
        });          
             

        //Mail
        \Config::set('mail.default', config('settings.mail_driver'));
        \Config::set('mail.mailers.smtp.host', config('settings.mail_host'));
        \Config::set('mail.mailers.smtp.port', config('settings.mail_port'));
        \Config::set('mail.mailers.smtp.username', config('settings.mail_username'));
        \Config::set('mail.mailers.smtp.password', config('settings.mail_password'));
        \Config::set('mail.mailers.smtp.encryption', config('settings.mail_encryption'));
        \Config::set('mail.from.address', config('settings.mail_from_address'));
        \Config::set('mail.from.name', config('settings.mail_from_name'));



        //Socialite
        if(!empty(config('settings.FACEBOOK_CLIENT_ID')) && !empty(config('settings.FACEBOOK_CLIENT_SECRET'))){
            \Config::set('services.facebook.client_id', config('settings.FACEBOOK_CLIENT_ID'));
            \Config::set('services.facebook.client_secret', config('settings.FACEBOOK_CLIENT_SECRET'));
        }
        else{
            \Config::set('settings.social_login_facebook', 0);            
        }        
        if(!empty(config('settings.TWITTER_CLIENT_ID')) && !empty(config('settings.TWITTER_CLIENT_SECRET'))){
            \Config::set('services.twitter.client_id', config('settings.TWITTER_CLIENT_ID'));
            \Config::set('services.twitter.client_secret', config('settings.TWITTER_CLIENT_SECRET'));
        }
        else{
            \Config::set('settings.social_login_twitter', 0);            
        }   

        if(!empty(config('settings.GOOGLE_CLIENT_ID')) && !empty(config('settings.GOOGLE_CLIENT_SECRET'))){
            \Config::set('services.google.client_id', config('settings.GOOGLE_CLIENT_ID'));
            \Config::set('services.google.client_secret', config('settings.GOOGLE_CLIENT_SECRET'));
        }
        else{
            \Config::set('settings.social_login_google', 0);            
        }
        Paginator::defaultView('pagination::bootstrap-4');
        Paginator::defaultSimpleView('pagination::simple-bootstrap-4');

       // view()->share(compact('locales'));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

   
}
