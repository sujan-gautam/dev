@extends('front.layouts.default')

@section('content')
<main>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header h4 text-center">{{ __('Confirm Password') }}</div>
          <div class="card-body">
            <div class="alert alert-info" role="alert">
                {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}                
            </div>
            <form method="POST" action="{{ route('password.confirm') }}">
              @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

              <div class="form-group row">
                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
                <div class="col-md-6">
                  <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"  required>
                  @if ($errors->has('password')) <span class="invalid-feedback" role="alert"> <strong>{{ $errors->first('password') }}</strong> </span> @endif </div>
              </div>


              @include('front.includes.messages')
              <div class="form-group row mb-0">
                <div class="col-md-6 offset-md-4">
                  <button type="submit" class="btn btn-blue-grey" > {{ __('Confirm Password') }} </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection 
