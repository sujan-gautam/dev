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
				<div class="@if(config('settings.site_layout') == 1) col-md-9 @else col-md-12 @endif">
					@if(config('settings.ad') == 1 && !empty(config('settings.ad1')))
						<div class="col-md-12 m-2 text-center">{!! html_entity_decode(config('settings.ad1')) !!}</div>@endif						
					<div class="card">
						<div class="card-header d-flex justify-content-between">
								<span>
									{{ __('Archive')}} - {{$syntax->name}}
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
								<li class="list-group-item">
									<i class="fa fa-paste blue-grey-text small"></i> @if(!empty($paste->password))
										<i class="fa fa-lock pink-text small"></i>@endif @if(!empty($paste->expire_time))
										<i class="fa fa-clock-o text-warning small"></i> @endif
									<a href="{{$paste->url}}">{{$paste->title_f}}</a>
									<p>
										<small class="text-muted"><a href="{{ route('archive',[$paste->syntax]) }}">{{get_syntax_name($paste->syntax)}}</a> |
											<i class="fa fa-eye blue-grey-text"></i> {{$paste->views_f}} | {{$paste->created_ago}}
										</small></p>
								</li>
							@empty
								<li class="list-group-item text-center">{{ __('No results')}}</li>
							@endforelse
						</ul>
					</div>
					<div class="row">
						<div class=" mx-auto mt-3 d-none d-sm-none d-md-block"> {{$pastes->appends(['keyword'=>app('request')->get('keyword')])->links()}} </div>							

						<div class=" mx-auto mt-3 d-sm-block d-md-none"> {{$pastes->appends(['keyword'=>app('request')->get('keyword'), 'tag'=>app('request')->get('tag')])->links('pagination::simple-bootstrap-4')}} </div>
					</div>
					@if(config('settings.ad') == 1 && !empty(config('settings.ad2')))
						<div class="col-md-12 m-2 text-center">{!! html_entity_decode(config('settings.ad2')) !!}</div>@endif
				</div>
				@include('front.paste.recent_pastes')
			</div>
			<!--/.First row-->
		</div>
		<!--/.Main layout-->
	</main>
@stop 