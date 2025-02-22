@extends('front.layouts.default')

@section('meta')
<meta name="description" content="{!!config('settings.meta_description')!!}">
<meta name="keywords" content="{!!config('settings.meta_keywords')!!}">
<meta name="author" content="{{config('settings.site_name')}}">
<meta property="og:title" content="@if(isset($page_title)){{$page_title.' - '}}@endif{{config('settings.site_name')}}" />
<meta property="og:type" content="website" />
<meta property="og:url" content="{{Request::url()}}" />
@if(!empty(config('settings.site_image')))
<meta property="og:image" content="{{url(config('settings.site_image'))}}" />
@endif
<meta property="og:site_name" content="{{config('settings.site_name')}}" />
<link rel="canonical" href="{{Request::url()}}" />
@stop

@section('content')
<main> 
  
  <!--Main layout-->
  <div class="container"> 
    <!--First row-->
    <div class="row " data-wow-delay="0.2s">
      <div class="col-md-8 mx-auto"> 
        
        <!--Card-->
        <div class="card"> 
          
          <!--Card content-->
          <div class="card-body"> 
            
            <!-- Form -->
            <form method="post" action="{{route('contact')}}">
              @csrf 
              <!-- Heading -->
              <h3 class="dark-grey-text text-center"> <strong>{{ __('Write to us')}}:</strong> </h3>
              <hr>
              <div class="md-form"> <i class="fa fa-user prefix grey-text"></i>
                <input type="text" id="form3" name="name" class="form-control" value="{{old('name')}}" tabindex="1" required>
                <label for="form3">{{ __('Your name')}}</label>
              </div>
              <div class="md-form"> <i class="fa fa-envelope prefix grey-text"></i>
                <input type="text" id="form2" name="email" class="form-control" value="{{old('email')}}" tabindex="2" required>
                <label for="form2">{{ __('Your email')}}</label>
              </div>
              <div class="md-form"> <i class="fa fa-pencil prefix grey-text"></i>
                <textarea type="text" id="form8" name="message" class="md-textarea" tabindex="3" required>{{old('message')}}</textarea>
                <label for="form8">{{ __('Your message')}}</label>
              </div>


              @include('front.includes.captcha') 
              
              @include('front.includes.messages')
              <div class="text-center">
                <button class="btn btn-blue-grey" type="submit" tabindex="5">{{ __('Send') }}</button>
             </div>
            </form>
            <!-- Form --> 
            
          </div>
        </div>
        <!--/.Card--> 
        
      </div>
    </div>
  </div>
</main>
@stop

