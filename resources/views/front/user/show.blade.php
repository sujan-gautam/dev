@extends('front.layouts.default')

@section('meta')
<meta name="description" content="{!!config('settings.meta_description')!!}">
<meta name="keywords" content="{!!config('settings.meta_keywords')!!}">
<meta name="author" content="{{config('settings.site_name')}}">
<meta property="og:title" content="@if(isset($page_title)){{$page_title.' - '}}@endif{{config('settings.site_name')}}" />
<meta property="og:type" content="article" />
<meta property="og:url" content="{{Request::url()}}" />
@if(!empty(config('settings.site_image')))
<meta property="og:image" content="{{url(config('settings.site_image'))}}" />
@endif
<meta property="og:site_name" content="{{config('settings.site_name')}}" />
<link rel="canonical" href="{{Request::url()}}" />
@stop

@section('after_styles')
<style type="text/css">
.profile-card{margin-top:70px}.profile-card .avatar{max-width:150px;max-height:150px;margin-top:-70px;margin-left:auto;margin-right:auto;-webkit-border-radius:50%;border-radius:50%;overflow:hidden}.profile-card p{font-weight:300}.user-card{margin-top:100px}.user-card .admin-up .data span{font-size:15px}  
</style>
@stop


@section('content')
<main> 
  
  <!--Main layout-->
  <div class="container"> 
    <!--First row-->
    <div class="row">
      <div class="col-md-3"> 
        
        <!-- Section: Basic Info -->
        <section class="card profile-card mb-4 text-center">
          <div class="avatar z-depth-1-half"> <img src="{{$user->avatar}}" alt="{{$user->name}}" class="img-fluid" style="width: 120px;"> </div>
          <!-- Card content -->
          <div class="card-body"> 
            <!-- Title -->
            <h4 class="card-title"><strong>{{$user->name}}</strong></h4>
            <h5>{{ __('Registered Member')}}</h5>
            <p class="dark-grey-text">{{ __('Joined')}} {{$user->created_ago}}</p>
            @if($user->status == 1)
            <p class="green-text font-weight-bold">{{ __('Active')}}</p>
            @elseif($user->status == 0)
            <p class="blue-text font-weight-bold">{{ __('Pending')}}</p>
            @else
            <p class="red-text font-weight-bold">{{ __('Banned')}}</p>
            @endif 
            <!-- Social --> 
            @if(!empty($user->fb))
            <a href="{{$user->fb}}" class="btn btn-primary btn-sm waves-effect waves-light"><i class="fa fa-facebook-f white-text"></i></a> @endif
            @if(!empty($user->tw))
            <a href="{{$user->tw}}" class="btn btn-info btn-sm waves-effect waves-light"><i class="fa fa-twitter white-text"></i></a> @endif
            @if(!empty($user->tg))
            <a href="{{$user->tg}}" class="btn btn-danger btn-sm waves-effect waves-light"><i class="fa fa-telegram white-text"></i></a> @endif           
            @if(!empty($user->disc))
            <a href="{{$user->disc}}" class="btn btn-danger btn-sm waves-effect waves-light">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-discord" viewBox="0 0 16 16">
            <path d="M13.545 2.907a13.227 13.227 0 0 0-3.257-1.011.05.05 0 0 0-.052.025c-.141.25-.297.577-.406.833a12.19 12.19 0 0 0-3.658 0 8.258 8.258 0 0 0-.412-.833.051.051 0 0 0-.052-.025c-1.125.194-2.22.534-3.257 1.011a.041.041 0 0 0-.021.018C.356 6.024-.213 9.047.066 12.032c.001.014.01.028.021.037a13.276 13.276 0 0 0 3.995 2.02.05.05 0 0 0 .056-.019c.308-.42.582-.863.818-1.329a.05.05 0 0 0-.01-.059.051.051 0 0 0-.018-.011 8.875 8.875 0 0 1-1.248-.595.05.05 0 0 1-.02-.066.051.051 0 0 1 .015-.019c.084-.063.168-.129.248-.195a.05.05 0 0 1 .051-.007c2.619 1.196 5.454 1.196 8.041 0a.052.052 0 0 1 .053.007c.08.066.164.132.248.195a.051.051 0 0 1-.004.085 8.254 8.254 0 0 1-1.249.594.05.05 0 0 0-.03.03.052.052 0 0 0 .003.041c.24.465.515.909.817 1.329a.05.05 0 0 0 .056.019 13.235 13.235 0 0 0 4.001-2.02.049.049 0 0 0 .021-.037c.334-3.451-.559-6.449-2.366-9.106a.034.034 0 0 0-.02-.019Zm-8.198 7.307c-.789 0-1.438-.724-1.438-1.612 0-.889.637-1.613 1.438-1.613.807 0 1.45.73 1.438 1.613 0 .888-.637 1.612-1.438 1.612Zm5.316 0c-.788 0-1.438-.724-1.438-1.612 0-.889.637-1.613 1.438-1.613.807 0 1.451.73 1.438 1.613 0 .888-.631 1.612-1.438 1.612Z"/>
          </svg>
            </a> 
            @endif
            
            <!-- Text -->
            <p class="card-text mt-3">{{$user->about}}</p>
            <button type="button" class="btn btn-info btn-rounded btn-sm waves-effect waves-light" data-toggle="modal" data-target="#modalContactForm">{{ __('Contact')}}<i class="fa fa-paper-plane ml-2"></i></button>
          </div>
        </section>
        <!-- Section: Basic Info --> 
        
      </div>
      <div class="col-md-9"> 
        @if(config('settings.ad') == 1 && !empty(config('settings.ad1')))
        <div class="col-md-12 m-2 text-center">{!! html_entity_decode(config('settings.ad1')) !!}</div>
        @endif 
        @include('front.includes.messages')
        <div class="card">
          <div class="card-header d-flex justify-content-between">
            <span>
              @if(request()->input('keyword')){{ __('Search Result').' - '.request()->input('keyword') }}@else{{ __('Recent Pastes')}}@endif
            </span>
            <span>
              <form action="">
                <div class="md-form form-sm m-0">
                  <input type="text" name="keyword" placeholder="{{ __('Search') }}" class="form-control form-control-sm pb-0 m-0 text-white card_search" value="{{ old('keyword',request()->input('keyword')) }}">
                </div>
              </form>
            </span>
        </div>
          <ul class="list-group list-group-flush">
            @forelse($pastes as $paste)
            <li class="list-group-item"> <i class="fa fa-paste blue-grey-text small"></i> @if(!empty($paste->password))<i class="fa fa-lock pink-text small"></i>@endif @if(!empty($paste->expire_time))<i class="fa fa-clock-o text-warning small"></i> @endif <a href="{{$paste->url}}">{{$paste->title_f}}</a>
              <p><small class="text-muted"><a href="{{ route('archive',[$paste->syntax]) }}">{{get_syntax_name($paste->syntax)}}</a> | <i class="fa fa-eye blue-grey-text"></i> {{$paste->views_f}} | {{$paste->created_ago}}</small></p>
            </li>
            @empty
            <li class="list-group-item text-center">{{ __('No results')}}</li>
            @endforelse
          </ul>
        </div>
        @if(config('settings.ad') == 1 && !empty(config('settings.ad2')))
        <div class="col-md-12 m-2 text-center">{!! html_entity_decode(config('settings.ad2')) !!}</div>
        @endif </div>
    </div>
  </div>
  <!--/.First row-->
  
  </div>
  <!--/.Main layout--> 
  
</main>

<!-- Modal: form -->
<div class="modal fade" id="modalContactForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
          aria-hidden="true">
  <div class="modal-dialog cascading-modal" role="document"> 
    <!-- Content -->
    <div class="modal-content"> 
      
      <!-- Header -->
      <div class="modal-header light-blue darken-3 white-text">
        <h4 class=""><i class="fas fa-pencil-alt"></i> {{ __('Contact')}} {{$user->name}}</h4>
        <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <!-- Body -->
      <div class="modal-body mb-0">
        <form method="post" action="{{route('user.contact',['name'=>$user->name])}}">
          @csrf
          <div class="md-form form-sm"> <i class="fa fa-user prefix"></i>
            <input type="text" name="name" id="form19" class="form-control form-control-sm" tabindex="1" required>
            <label for="form19">{{ __('Your name')}}</label>
          </div>
          <div class="md-form form-sm"> <i class="fa fa-envelope prefix"></i>
            <input type="email" name="email" id="form20" class="form-control form-control-sm" tabindex="2" required>
            <label for="form20">{{ __('Your email')}}</label>
          </div>
          <div class="md-form form-sm"> <i class="fa fa-pencil prefix"></i>
            <textarea type="text" name="message" id="form8" class="md-textarea form-control form-control-sm" minlength="10" maxlength="255" rows="3" tabindex="3" required></textarea>
            <label for="form8">{{ __('Your message')}}</label>
          </div>

            @include('front.includes.captcha') 

          <div class="text-center mt-1-half">
            <button class="btn btn-info mb-2" type="submit" tabindex="5">{{ __('Send')}} <i class="fa fa-paper-plane ml-1"></i></button>
          </div>
        </form>
      </div>
    </div>
    <!-- Content --> 
  </div>
</div>
<!-- Modal: form --> 
@stop                   
