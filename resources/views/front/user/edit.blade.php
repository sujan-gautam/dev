@extends('front.layouts.default')

@section('meta')
<meta name="description" content="{!!config('settings.meta_description')!!}">
<meta name="keywords" content="{!!config('settings.meta_keywords')!!}">
<meta name="author" content="{{config('settings.site_name')}}">
@stop

@section('after_scripts')
<script type="text/javascript">
function loadFile(event, id) {
    // alert(event.files[0]);
    var reader = new FileReader();
    reader.onload = function () {
        var output = document.getElementById(id);
        output.src = reader.result;
        //$("#imagePreview").css("background-image", "url("+this.result+")");
    };
    reader.readAsDataURL(event.files[0]);
}
</script>
@stop

@section('content')
	<main>
		<!--Main layout-->
		<div class="container">
			<!--First row-->
			<div class="row">
				<div class="col-md-3">
					@include('front.user.sidebar')
				</div>
				<div class="col-md-9">
				@include('front.includes.messages')
				<!-- Material form register -->
					<div class="card">
						<h5 class="card-header"><i class="fa fa-user-circle-o"></i> <strong>{{ $page_title}}</strong>
						</h5>
						<!--Card content-->
						<div class="card-body  pt-0">
							<form class="p-3" method="post" enctype="multipart/form-data" action="">
								@csrf
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>{{ __('Username')}}</label>
											<input type="text" id="defaultContactFormName" class="form-control mb-4" value="{{$user->name}}" disabled>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>{{ __('E-Mail address')}}</label>
											<input type="email" id="defaultContactFormEmail" class="form-control mb-4" value="{{$user->email}}" disabled>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label>{{ __('Avatar')}}</label> <br />
									<img src="{{$user->avatar}}" id="avatar" class="rounded-circle z-depth-1-half avatar-pic mb-4" height="80" width="80">
									<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload')}}</span>
										</div>
										<div class="custom-file">
											<input type="file" class="custom-file-input" name="avatar" id="inputGroupFile01" onchange="loadFile(this,'avatar')" aria-describedby="inputGroupFileAddon01">
											<label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file jpg-png Max 1MB')}}</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label>{{ __('About Me')}}</label>
									<textarea name="about" class="form-control mb-4">{{old('about',$user->about)}}</textarea>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label><i class="fa fa-facebook"></i> {{ __('Facebook Link')}}</label>
											<input type="text" name="fb" class="form-control mb-4" value="{{old('fb',$user->fb)}}">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label><i class="fa fa-twitter"></i> {{ __('Twitter Link')}}</label>
											<input type="text" name="tw" class="form-control mb-4" value="{{old('tw',$user->tw)}}">
										</div>
									</div>
								</div>								
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label><i class="fa fa-telegram"></i> {{ __('Telegram Link')}}</label>
											<input type="text" name="tg" class="form-control mb-4" value="{{old('tg',$user->tg)}}">
										</div>
									</div>									
									<div class="col-md-6">
										<div class="form-group">
											<label><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-discord" viewBox="0 0 16 16">
            <path d="M13.545 2.907a13.227 13.227 0 0 0-3.257-1.011.05.05 0 0 0-.052.025c-.141.25-.297.577-.406.833a12.19 12.19 0 0 0-3.658 0 8.258 8.258 0 0 0-.412-.833.051.051 0 0 0-.052-.025c-1.125.194-2.22.534-3.257 1.011a.041.041 0 0 0-.021.018C.356 6.024-.213 9.047.066 12.032c.001.014.01.028.021.037a13.276 13.276 0 0 0 3.995 2.02.05.05 0 0 0 .056-.019c.308-.42.582-.863.818-1.329a.05.05 0 0 0-.01-.059.051.051 0 0 0-.018-.011 8.875 8.875 0 0 1-1.248-.595.05.05 0 0 1-.02-.066.051.051 0 0 1 .015-.019c.084-.063.168-.129.248-.195a.05.05 0 0 1 .051-.007c2.619 1.196 5.454 1.196 8.041 0a.052.052 0 0 1 .053.007c.08.066.164.132.248.195a.051.051 0 0 1-.004.085 8.254 8.254 0 0 1-1.249.594.05.05 0 0 0-.03.03.052.052 0 0 0 .003.041c.24.465.515.909.817 1.329a.05.05 0 0 0 .056.019 13.235 13.235 0 0 0 4.001-2.02.049.049 0 0 0 .021-.037c.334-3.451-.559-6.449-2.366-9.106a.034.034 0 0 0-.02-.019Zm-8.198 7.307c-.789 0-1.438-.724-1.438-1.612 0-.889.637-1.613 1.438-1.613.807 0 1.45.73 1.438 1.613 0 .888-.637 1.612-1.438 1.612Zm5.316 0c-.788 0-1.438-.724-1.438-1.612 0-.889.637-1.613 1.438-1.613.807 0 1.451.73 1.438 1.613 0 .888-.631 1.612-1.438 1.612Z"/>
          </svg>  {{ __('Discord Link')}}</label>
											<input type="text" name="disc" class="form-control mb-4" value="{{old('disc',$user->disc)}}">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>{{ __('Password')}}</label>
											<input type="password" name="password" class="form-control mb-4">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>{{ __('Confirm Password')}}</label>
											<input type="password" name="password_confirmation" class="form-control mb-4">
										</div>
									</div>
								</div>
								<!-- Save button -->
								<button class="btn btn-blue-grey darken-5 btn-block" type="submit">{{ __('Save')}}</button>
							</form>
							<!-- Default form contact -->
						</div>
					</div>
					<!-- Material form register -->
				</div>
			</div>
		</div>
	</main>

@stop