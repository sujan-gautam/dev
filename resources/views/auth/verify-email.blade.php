@extends('front.layouts.default')

@section('meta')
<meta name="description" content="{{config('settings.meta_description')}}">
<meta name="keywords" content="{{config('settings.meta_keywords')}}">
@stop

@section('content')
<main>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header h4 text-center">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">
                        <div class="alert alert-success" role="alert">
                            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}                            
                        </div>
                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf

                        <div>
                            <button type="submit" class="btn btn-primary">
                                {{ __('Resend Verification Email') }}
                            </button>
                        </div>
                    </form>


                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="btn btn-secondary">
                            {{ __('Log Out') }}
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
</main>
@endsection
