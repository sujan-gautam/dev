@if(config('settings.cookie_consent_notification'))
<!-- COOKIES -->
<div class="alert alert-dismissible text-center cookiealert" role="alert">
    <div class="cookiealert-container">
        <b>{{ __('Do you like cookies') }}?</b> &#x1F36A; {{ __('We use cookies to ensure you get the best experience on our website.') }} <a href="{{ route('page.show',['privacy-policy']) }}" target="_blank">{{ __('Learn more') }}</a>

        <button type="button" class="btn btn-primary btn-sm acceptcookies" aria-label="Close">
            {{ __('I agree') }}
        </button>
    </div>
</div>
<!-- /COOKIES -->
@endif

@yield('before_scripts')
<script>
var max_content_size_kb = {{ config('settings.max_content_size_kb') }};	
var paste_editor_height = {{ config('settings.paste_editor_height') }};
var ad_block_message = '{{ __("Ad Block Detected") }}';

@if(config('settings.ad_block_detection') == 1)
var isAdBlockActive = true;
@else
var isAdBlockActive = false;
@endif
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="{{url('js/bootstrap.min.js')}}"></script>
<script src="{{url('js/mdb.min.js?v=2')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>
<script src="{{url('js/prebid-ads.js')}}"></script>
@if(config('settings.cookie_consent_notification'))
<script src="{{url('plugins/cookiealert/cookiealert.min.js')}}"></script>
@endif
@stack('js_before')
<script src="{{url('js/app.min.js?v=1.5')}}"></script>
@yield('after_scripts')
{!! html_entity_decode(config('settings.analytics_code')) !!}
{!! html_entity_decode(config('settings.footer_code')) !!}