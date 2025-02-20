@if(config('settings.captcha') == 1)
@push('js_before')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush

<div class="form-group {{ Route::is('register') || Route::is('password.request')  || Route::is('password.email') ? 'offset-4' : '' }} col-md-6" style="margin-bottom: 1rem;">
<div class="g-recaptcha" data-sitekey="{{ config('settings.recaptcha_v2_site_key') }}"></div>
</div>

@endif
          
          