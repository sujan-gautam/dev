@extends('front.layouts.default')

@section('meta')
<meta name="description" content="{{config('settings.meta_description')}}">
<meta name="keywords" content="{{config('settings.meta_keywords')}}">
@stop

@section('content')
<main> 
  
  <!--Main layout-->
  <div class="container"> 
    <!--First row-->
    <div class="row " data-wow-delay="0.2s">
      <div class="col-md-8 mx-auto"> 
        
        <!-- Default form login -->
        <form class="text-center border border-light p-5" method="post" action="{{route('login')}}">
          @csrf
          <p class="h4 mb-4">{{ __('Sign in') }}</p>
          
          <!-- Email -->
          <input type="text" name="email" id="defaultLoginFormEmail" autocomplete="off" class="form-control mb-4" placeholder="{{ __('Username or Email address')}}"  required autofocus>
          
          <!-- Password -->
          <input type="password" name="password" id="defaultLoginFormPassword" class="form-control mb-4" placeholder="{{ __('Password')}}"  required>


            @include('front.includes.captcha')  



          <div class="d-flex justify-content-around">
            <div> 
              <!-- Remember me -->
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="defaultLoginFormRemember" name="remember" value="on" >
                <label class="custom-control-label" for="defaultLoginFormRemember">{{ __('Remember me')}}</label>
              </div>
            </div>
            <div> 
              <!-- Forgot password --> 
              
              @if (Route::has('password.request')) <a href="{{ route('password.request') }}"> {{ __('Forgot Your Password') }}? </a> @endif </div>
          </div>
          <div class="mt-2">@include('front.includes.messages')</div>
          
          <!-- Sign in button -->
          <button class="btn btn-blue-grey btn-block my-4" type="submit" >{{ __('Sign in')}}</button>
          
          <!-- Register -->
          <p>{{ __('Not a member')}}? <a href="{{url('register')}}">{{ __('Register')}}</a> </p>
          <hr>
          @if (config('settings.social_login_facebook') == 1) <a href="{{ route('social.login', ['facebook', 'action' => 'login']) }}" class="btn btn-primary btn-block mb-1"> <i class="fa fa-facebook"></i> {{ __('Login with Facebook') }} </a> @endif
          
          @if (config('settings.social_login_twitter') == 1) <a href="{{ route('social.login', ['twitter', 'action' => 'login']) }}" class="btn btn-cyan btn-block mb-1"> <i class="fa fa-twitter"></i> {{ __('Login with Twitter') }} </a> @endif
          
          @if (config('settings.social_login_google') == 1) <a href="{{ route('social.login', ['google', 'action' => 'login']) }}" class="btn btn-danger btn-block mb-1"> <i class="fa fa-google-plus"></i> {{ __('Login with Google') }} </a> @endif
        </form>
        <!-- Default form login --> 
        
      </div>
    </div>
  </div>
</main>
@stop