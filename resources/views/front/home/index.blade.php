@extends('front.layouts.default')

@section('meta')
<meta name="description" content="{!!config('settings.meta_description')!!}">
<meta name="keywords" content="{!!config('settings.meta_keywords')!!}">
<meta name="author" content="{{config('settings.site_name')}}">

<link rel="stylesheet" href="assets/css/story.css">
<meta property="og:title" content="@if(isset($page_title)){{$page_title.' - '}}@endif{{config('settings.site_name')}}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/brands.min.css" integrity="sha512-8RxmFOVaKQe/xtg6lbscU9DU0IRhURWEuiI0tXevv+lXbAHfkpamD4VKFQRto9WgfOJDwOZ74c/s9Yesv3VvIQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<meta property="og:type" content="website" />
<meta property="og:url" content="{{Request::url()}}" />
@if(!empty(config('settings.site_image')))
<meta property="og:image" content="{{url(config('settings.site_image'))}}" />
@endif
<meta property="og:site_name" content="{{config('settings.site_name')}}" />
<link rel="canonical" href="{{Request::url()}}" />
@stop

@section('after_styles')
	@if(config('settings.paste_editor') == 'ace')
		<link rel="stylesheet" href="{{url('plugins/ace/css/ace.min.css')}}" />
	@elseif(config('settings.paste_editor') == 'codemirror')
		<link rel="stylesheet" href="{{url('plugins/codemirror-5.52.0/lib/codemirror.min.css')}}" />
		<style>.CodeMirror{ border:1px solid lightgray; }</style>
	@endif
	<link rel="stylesheet" href="{{ url('plugins/tagsinput/tagsinput.min.css') }}" />
@stop

@section('after_scripts')
<script src="{{ url('plugins/tagsinput/tagsinput.min.js') }}"></script>
@if(config('settings.paste_editor') == 'ace')
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.3/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.3/ext-modelist.js"></script>
<script>
    var mode = "text";
    var syntax = "Text";
    var syntax_extension = "txt";
    var text = "";
    var type = 1;
    var editor = ace.edit("editor");
    editor.$blockScrolling = Infinity;
    //editor.setValue(text, -1);
    editor.setShowPrintMargin(false);
    editor.setOptions({
        autoScrollEditorIntoView: true,
        wrap: true,
        minLines: paste_editor_height,
        maxLines: paste_editor_height,
		@if(config('settings.syntax_highlighter_line_numbers') == 0)
        showLineNumbers: false,
        showGutter: false
		@endif

    });
    editor.focus();

    $('button[type="submit"]').on('click', function (event) {
        $('input[name="content"]').val(editor.getValue());
    });

	$("select[name='syntax']").on("change", function () {

        var ext = $(this).find('option:selected').data('ext');
        var tempPath = "file." + ext;
        var modelist = ace.require("ace/ext/modelist");
        var tempMode = modelist.getModeForPath(tempPath).mode;
        editor.session.setMode(tempMode);

    });

    var ext = $("select[name='syntax']").find('option:selected').data('ext');
    var tempPath = "file." + ext;
    var modelist = ace.require("ace/ext/modelist");
    var tempMode = modelist.getModeForPath(tempPath).mode;
    editor.session.setMode(tempMode);
</script>
@elseif(config('settings.paste_editor') == 'codemirror')
<script src="{{url('plugins/codemirror-5.52.0/lib/codemirror.min.js')}}"></script>
<script src="{{url('plugins/codemirror-5.52.0/addon/mode/loadmode.js')}}"></script>
<script src="{{url('plugins/codemirror-5.52.0/addon/edit/matchbrackets.js')}}"></script>
<script src="{{url('plugins/codemirror-5.52.0/addon/fold/foldcode.js')}}"></script>
<script src="{{url('plugins/codemirror-5.52.0/addon/fold/foldgutter.js')}}"></script>
<script src="{{url('plugins/codemirror-5.52.0/mode/meta.js')}}"></script>
<script>
CodeMirror.modeURL = '{{url("plugins/codemirror-5.52.0")}}/mode/%N/%N.js';	
var syntax_extension = $("select[name='syntax']").find('option:selected').data('ext');
var editor = CodeMirror.fromTextArea(document.getElementById("editor"), {
	lineNumbers: true,
	lineWrapping: true,
    matchBrackets: true,
    styleActiveLine: true,
	mode: "text"
});

changeMode(syntax_extension);

function changeMode(ext)
{
	var info = CodeMirror.findModeByExtension(ext);	
	if (typeof info === 'undefined') {
		mime = 'text/plain';
		mode = null;
	}
	else{
		mime = info.mime;
		mode = info.mode
	}
 	editor.setOption("mode", mime);
    CodeMirror.autoLoadMode(editor, mode);
}

$("select[name='syntax']").on("change", function () {
	var ext = $(this).find('option:selected').data('ext');
	changeMode(ext);
});

$('button[type="submit"]').on('click', function (event) {
    $('input[name="content"]').val(editor.getValue());
});

</script>	

@endif
@stop

@section('content')
	<main>
	<?php

// Establish database connection
$servername = "localhost";
$username = "sujancom_dev2user";
$password = "sujan.sujan";
$dbname = "sujancom_dev2";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the latest popup (you can modify this query as needed)
$popup_query = 'SELECT * FROM popups ORDER BY id DESC LIMIT 1';
$popup_result = $conn->query($popup_query);
$popup = $popup_result->fetch_assoc();

// Fetch about data (assuming you want to fetch it for some purpose)
$about_query = 'SELECT * FROM about ORDER BY about_id DESC';
$about_result = $conn->query($about_query);
$about = $about_result->fetch_all(MYSQLI_ASSOC);

// Fetch stories
$stories_query = 'SELECT st.*, m.file_name AS music_file FROM stories_text_music_image st INNER JOIN music m ON st.music_id = m.id ORDER BY upload_time DESC LIMIT 10';
$stories_result = $conn->query($stories_query);
$stories = $stories_result->fetch_all(MYSQLI_ASSOC);

// Increment views and update the database
foreach ($stories as $story) {
    $storyId = $story['id'];
    $update_query = "UPDATE stories_text_music_image SET views = views + 1 WHERE id = $storyId";
    $conn->query($update_query);
}

// Close connection
$conn->close();
?>

<style>
	.stories-section { margin-top: 14px; margin-bottom: 14px; padding-top: 0; padding-bottom: 0; overflow-x: auto; position: relative; } .title-stories{ margin-bottom:0px !important; } .stories-header { text-align: left; /* margin-bottom: 10px; */ font-family: inherit; color: #E7E7E7; text-shadow: 0px 0px 4px #e7e7e7b5; font-weight: 800; letter-spacing: 1px; } #stories-container { display: flex; color: #a5aaaa; } .stories-container{ width:fit-content; } /* For WebKit browsers */ .stories-section::-webkit-scrollbar { width: 12px; display:none; } .stories-section::-webkit-scrollbar-track { background-color: #f1f1f1; display:none; } .stories-section::-webkit-scrollbar-thumb { background-color: #888; border-radius: 6px; display:none; } .stories-section::-webkit-scrollbar-thumb:hover { background-color: #555; display:none; } .story { margin-right: 10px; cursor: pointer; overflow: hidden; display: flex; flex-direction: column; align-items: center; position: relative; background:  #61ba65!important; padding: 4px; border-radius: 50px; } .story-text { margin: 0; font-size: 5px; /* Adjusted font size */ text-align: center; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); } .story-image { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; } #selected-story-container { visibility: hidden; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.9); z-index: 1000; } #selected-story-container img { max-width: 80%; max-height: 80%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1; /* Ensure the image is above the overlay */ } #selected-story-container p { font-size: 20px; /* Adjust font size as needed */ margin: 0; text-align: center; } .story-overlay { position: relative; height: 100%; display: flex; align-items: center; justify-content: center; } .close-btn, .swipe-btn { position: absolute; font-size: 24px; cursor: pointer; color: white; z-index: 999; background: #ffffff00; border-radius: 50%  !important; border: 1px solid white; padding: 4px 7px; } .close-btn { top: 80px; right: 20px; cursor: pointer; position: absolute; } .swipe-btn.left { left: 20px; color:white; padding: 4px 7px; } .swipe-btn.right { right: 20px; color:white; padding: 5px 7px; } .outer-layer { position: relative; padding: 4px; border-radius: 50%; overflow: hidden; width: 50px; height: 50px; box-sizing: content-box; position: relative; border:2px solid black; } .story img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 0; /* Ensure the image is below the text */ } #selected-story-text { position: relative; z-index: 1; } #selected-story-container img { } #close-btn { position: absolute; top: 80px !important;padding:7px 16px;color:red; right: 10px; cursor: pointer;  /* color: #333; */ } #swipe-left, #swipe-right { position: absolute; top: 50%; transform: translateY(-50%); font-size: 24px; cursor: pointer; color:white; /* color: #333; */ display: none; padding: 5px 14px; } #swipe-left:hover, #swipe-right:hover { color:red; } #swipe-left { left: 10px; } #swipe-right { right: 10px; } .story-overlay { z-index: 9999; display: flex; width: 100%; justify-content: space-around; align-items: center; vertical-align: middle; } .custom-text-style { display: flex !important; width: 100px !important; height: fit-content !important; max-height: 250px; /* top: -152px; */ bottom:35%; font-size:14px !important; font-weight:600 !important; } .textt{ display: flex !important; font-size:14px !important; /* text-shadow: 0px 0px 20px white; */ font-weight: 800; } .story-views { display: flex; align-items: center; justify-content: space-between; padding: 5px; background-color: rgba(0, 0, 0, 0.8); color: white; font-size: 14px; position: absolute; top: 0; left: 0; right: 0; } .story-views i { margin-right: 5px; } .view-count { color: white; font-size: 12px; } .view-count-selected { position: absolute; top: 5px; left: 50%; transform: translateX(-50%); color: white; font-size: 16px; display: flex; align-items: center; } #selected-view-count { margin-left: 5px; } .story-views { display: none; /* Hide view count for the home story */ } .story-views-container { position: absolute; bottom: 10px; right: 10px; display: flex; flex-direction: column; align-items: flex-end; justify-content: flex-end; } /* #selected-story-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; z-index: 1000; } */ /* .story-overlay { background-color: rgba(0, 0, 0, 0.7); position: relative; padding: 20px; border-radius: 10px; text-align: center; color: white; display: flex; flex-direction: column; align-items: center; max-width: 350px; width: 100%; } */ #selected-story-text { font-size: 24px; color: white; white-space: ; /* Prevent line breaks */ text-overflow: ellipsis; /* Display ellipsis (...) for overflow text */ padding: 4px; border-radius: 5px; }
</style>


		<!--Main layout-->
		<div class="container">
			<!--First row-->
			<!-- STORIES SECTION STARTS -->
			<link rel="stylesheet" href="assets/css/story.css">
<section class="stories-section title-stories">
    <!-- <h2 class="stories-header">STORIES:</h2> -->
</section>
<section class="stories-section">
    <div id="stories-container" class="stories-container">
        <?php
        // Check if there are any stories
        foreach ($stories as $story) {
            $imageSrc = "storage/story/{$story['image_file_name']}";
            $bgColor = $story['bg_color'];
            $imageExists = file_exists($imageSrc) && $story['image_file_name'] !== 'default.png';
        ?>
        <div class="story" 
            data-story-id="<?php echo $story['id']; ?>" 
            data-bg-color="<?php echo $story['bg_color']; ?>" 
            data-text-color="<?php echo $story['text_color']; ?>" 
            data-music-url="storage/music/<?php echo $story['music_file']; ?>" 
            data-image-file="<?php echo $story['image_file_name']; ?>" 
            data-image-exists="<?php echo $imageExists ? 'true' : 'false'; ?>">
            <div class="outer-layer" style="background-color: transparent; color: <?php echo $story['text_color']; ?>;">
                <?php if ($imageExists) { ?>
                    <img class="story-image" src="<?php echo $imageSrc; ?>" alt="Story Image" data-src="<?php echo $imageSrc; ?>">
                <?php } ?>
                <div class="story-views">
                    <i class="fas fa-eye"></i>
                    <span class="view-count" data-story-id="<?php echo $story['id']; ?>">
                        <?php echo $story['views']; ?>
                    </span>
                </div>
                <p class="story-text"><?php echo $story['story_text']; ?></p>
            </div>
        </div>
        <?php
        } 
        ?>
    </div>
    <div id="selected-story-container">
        <div class="story-overlay">
            <div class="close-btn" id="close-btn"><i class="fas fa-times"></i></div>
            <div class="swipe-btn left" id="swipe-left"><i class="fas fa-chevron-left"></i></div>
            <p id="selected-story-text" class="textt" style="color: white;
                height: fit-content;
                min-width: 250px;
                max-width: 350px;
                min-height: fit-content;
                padding: 4px;
                border-radius: 5px;
                text-align: center;
                display: flex;
                justify-content: center;
                vertical-align: middle;
                align-items: center;
                font-size: 24px;"></p>

            <!-- Display views with icon in the 200x250px box -->
           
            <div class="view-count-selected">
                <i class="fas fa-eye"></i>
                <span id="selected-view-count">0</span>
            </div>
            <div class="swipe-btn right" id="swipe-right"><i class="fas fa-chevron-right"></i></div>
        </div>
    </div>
</section>
<script src="assets/js/story.js"></script>
<!-- STORIES SECTION STOPS -->
			<div class="row " data-wow-delay="0.2s">
				<div class="@if(config('settings.site_layout') == 1) col-md-9 @else col-md-12 @endif">
					@if(config('settings.ad') == 1 && !empty(config('settings.ad1')))
						<div class="col-md-12 m-2 text-center">{!! html_entity_decode(config('settings.ad1')) !!}</div>
					@endif
					@include('front.includes.messages')
					<div class="card">
						<div class="card-body">
							<input id="paste_file" type="file" onchange="handleFileSelect(this);" style="display: none;" />
							<form method="post" action="{{route('paste.store')}}">
								@csrf
								<div class="form-group">
									<label class="font-weight-bold d-flex justify-content-between">
										<span>{{ __('New Paste')}} </span>
										<small><a id="load_file">{{ __('Browse') }}</a></small>
									</label>
									@if(config('settings.paste_editor') == 'ace')
										<textarea id="editor" class="hide">{{old('content')}}</textarea>
										<input name="content" type="hidden">
									@elseif(config('settings.paste_editor') == 'codemirror')
										<textarea id="editor" autofocus>{{old('content')}}</textarea>
										<input name="content" type="hidden">
									@else
										<textarea name="content" class="form-control" rows="{{ config('settings.paste_editor_height') }}"  autofocus>{{old('content')}}</textarea>
									@endif
								</div>
								<h5>{{ __('Paste Settings')}}
								</h5>
								<hr class="extra-margin" />

								<div class="row">
										<div class="form-group col-md-6">
											<label>{{ __('Paste Title')}} : <small
														class="text-muted">@if(config('settings.paste_title_required') == 0)
														[{{ __('Optional')}}] @endif
												</small> </label> 
												<input type="text" name="title" class="form-control" placeholder="{{ __('Paste Title')}}" value="{{old('title',$paste->title)}}"
													autocomplete="off">
										</div>										

										<div class="form-group col-md-6">
											<label>{{ __('Paste Folder')}} :
											<small class="text-muted">[{{ __('Optional')}}] @if(Auth::check())<a href="{{ route('folder.create') }}"><i class="fa fa-plus"></i> {{ __('Create Folder') }}</a>@endif</small> </label>
											@php $selected = old('folder_id',$paste->folder_id); @endphp
											<select class="form-control select2" name="folder_id"  @if(!Auth::check()) disabled @endif>
												<option value="" @if(!Auth::check()) title="{{ __('You must login to use this feature') }}" @endif>{{ __('Select')}}</option>
												<optgroup label="{{ __('My Folders')}}">
													@foreach(get_my_folders() as $my_folder)
														<option value="{{$my_folder->id}}" @if($selected == $my_folder->id) selected @endif>{{$my_folder->name}}</option>
													@endforeach
												</optgroup>
											</select>

										</div>										
								</div>	

								<div class="row">
									<div class="col-md-6 form-group">
											<label>{{ __('Syntax Highlighting')}} :
												<small class="text-muted">[{{ __('Optional')}}] </small> </label>
											@php $selected = old('syntax',$paste->syntax); @endphp
											<select class="form-control select2" name="syntax" >
												<option value="none">{{ __('Select')}}
												</option>
												<optgroup label="{{ __('Popular Languages')}}">
													@foreach(get_popular_syntaxes() as $syntax)
														<option value="{{$syntax->slug}}"
																data-ext="{{(!empty($syntax->extension))?$syntax->extension:'txt'}}"
																@if($selected == $syntax->slug) selected @endif>{{$syntax->name}}</option>
													@endforeach
												</optgroup>
												<optgroup label="{{ __('All Languages')}}">
													@foreach(get_syntaxes() as $syntax)
														<option value="{{$syntax->slug}}"
																data-ext="{{(!empty($syntax->extension))?$syntax->extension:'txt'}}"
																@if($selected == $syntax->slug) selected @endif>{{$syntax->name}}</option>
													@endforeach
												</optgroup>
											</select>
										</div>
										<div class="col-md-6 form-group">
											<label>{{ __('Paste Expiration')}} :
												<small class="text-muted">[{{ __('Optional')}}] </small> </label>
											@php $selected = old('expire',$paste->expire); @endphp
											<select class="form-control" name="expire" >
												<option value="N" @if($selected == 'N') selected @endif>{{ __('Never')}}
												</option>
												<option value="SD"
														@if($selected == 'SD') selected @endif>{{ __('Self Destroy')}}
												</option>
												<option value="10M"
														@if($selected == '10M') selected @endif>{{ __('10 Minutes')}}
												</option>
												<option value="1H"
														@if($selected == '1H') selected @endif>{{ __('1 Hour')}}
												</option>
												<option value="1D"
														@if($selected == '1D') selected @endif>{{ __('1 Day')}}
												</option>
												<option value="1W"
														@if($selected == '1W') selected @endif>{{ __('1 Week')}}
												</option>
												<option value="2W"
														@if($selected == '2W') selected @endif>{{ __('2 Weeks')}}
												</option>
												<option value="1M"
														@if($selected == '1M') selected @endif>{{ __('1 Month')}}
												</option>
												<option value="6M"
														@if($selected == '6M') selected @endif>{{ __('6 Months')}}
												</option>
												<option value="1Y"
														@if($selected == '1Y') selected @endif>{{ __('1 Year')}}
												</option>
											</select>
										</div>									


								</div>			

								<div class="row">
									<div class="col-md-6 form-group">
										<label>{{ __('Paste Status')}} :
											<small class="text-muted">[{{ __('Optional')}}] </small> </label>
										@php $selected = old('status',$paste->status); @endphp
										<select class="form-control" name="status" >
											<option value="1" @if($selected == 1) selected @endif>{{ __('Public')}}
											</option>
											<option value="2"
													@if($selected == 2) selected @endif>{{ __('Unlisted')}}
											</option>
											<option value="3" @if(!Auth::check()) disabled
													@else  @if($selected == 3) selected @endif @endif>
												{{ __('Private')}} ({{ __('members only')}})
											</option>
										</select>
									</div>
									<div class="col-md-6 form-group">
											<label>{{ __('Password')}} :
												<small class="text-muted">[{{ __('Optional')}}] </small> </label>
											<input type="password" name="password" class="form-control" placeholder="{{ __('Password')}}"  value="{{old('password',$paste->password)}}" autocomplete="off">
									</div>
								</div>	

								<div class="row">
									<div class="col-md-6 form-group">
										<label>{{ __('Description') }}: <small class="text-muted">[{{ __('Optional')}}] </small></label>
										<input type="text" name="description" class="form-control" value="{{ old('description',$paste->description) }}" autocomplete="off"  @if(!Auth::check()) disabled placeholder="{{ __('You must login to use this feature') }}" @else placeholder="{{ __('Write something about your paste') }}" @endif>
									</div>									
									<div class="col-md-6 form-group">
										<label>{{ __('Tags') }}: <small class="text-muted">[{{ __('Optional')}}] </small></label>
										<input type="text" name="tags" class="form-control" value="{{ old('tags',$paste->tags) }}" autocomplete="off" @if(!Auth::check()) disabled placeholder="{{ __('You must login to use this feature') }}" @else data-role="tagsinput" placeholder="{{ __('Tags separated by comma') }}" @endif>
									</div>
								</div>	

								<div class="row">										
										<div class="form-group col-md-6">
											<div class="custom-control custom-checkbox">
												<input type="checkbox" class="custom-control-input" id="encrypted" name="encrypted"  value="1" @if(old('encrypted',$paste->encrypted) == 1) checked @endif>
												<label class="custom-control-label" for="encrypted">{{ __('Encrypt Paste')}}</label>
												<span>(<small class="cp" title="{{ __('Protect your text by Encrypting and Decrypting any given text with a key that no one knows')}}">?</small>)</span>
											</div>
										</div>
									@include('front.includes.captcha')								
									
								</div>
								<div class="row">
									<div class="form-group col-md-12">
										<button type="submit" class="btn btn-success"
												>{{ __('Create New Paste')}}
										</button>
									</div>
								</div>
							</form>
							@if(!Auth::check())
								<div class="alert alert-warning" role="alert">
									<i class="fa fa-info-circle"> </i> {{ __('You are currently not logged in')}}
									, {{ __('this means you can not edit or delete anything you paste')}}.
									<a href="{{url('register')}}">{{ __('Sign Up')}}
									</a> {{ __('or') }}
									<a href="{{url('login')}}">{{ __('Login')}}
									</a>
								</div>
							@endif
						</div>
					</div>
					@if(config('settings.ad') == 1 && !empty(config('settings.ad2')))
						<div class="col-md-12 m-2 text-center">{!! html_entity_decode(config('settings.ad2')) !!}
						</div>
					@endif
				</div>
				@include('front.paste.recent_pastes')
			</div>
			<!--/.First row-->
		</div>
		<script>
			
			document.addEventListener('DOMContentLoaded', function () {
        const stories = document.querySelectorAll('.story');

        stories.forEach(story => {
            const imageExists = story.getAttribute('data-image-exists') === 'true';
            const imageFileName = story.getAttribute('data-image-file');
            const bgColor = story.getAttribute('data-bg-color');
            const outerLayer = story.querySelector('.outer-layer');

            if (imageExists && imageFileName !== 'default.png') {
                outerLayer.style.backgroundColor = '#0d0d0d';
            } else {
                outerLayer.style.backgroundColor = bgColor || 'transparent';
            }
        });
    });
document.addEventListener('DOMContentLoaded', function () {
    let backgroundAudio;
    let currentStoryIndex = 0;

    const closeBtn = document.getElementById('close-btn');
    const selectedStoryContainer = document.getElementById('selected-story-container');
    const selectedStoryText = document.getElementById('selected-story-text');
    const stories = document.querySelectorAll('.story');
    const swipeLeftBtn = document.getElementById('swipe-left');
    const swipeRightBtn = document.getElementById('swipe-right');
    const selectedViewCount = document.getElementById('selected-view-count');

    stories.forEach((story, index) => {
        story.addEventListener('click', function () {
            const bgColor = story.getAttribute('data-bg-color');
            const storyId = story.getAttribute('data-story-id');
            const viewCountSpan = story.querySelector('.view-count');

            // Fetch view count from the server (assuming you have a PHP script to retrieve it)
            fetch(`increment_view_count.php?story_id=${storyId}`)
                .then(response => response.json())
                .then(data => {
                    // Set the view count in the selected story container
                    selectedViewCount.textContent = story.querySelector('.view-count').textContent;
                })
                .catch(error => console.error('Error fetching view count:', error));

            const imageExists = story.getAttribute('data-image-exists') === 'true';
            const imageFile = story.getAttribute('data-image-file');
            const musicUrl = story.getAttribute('data-music-url');
            const storyText = story.querySelector('.story-text').textContent;
            const textColor = story.getAttribute('data-text-color');

            // Set styles for selected-story-text based on the image
            if (imageExists && imageFile !== 'default.png') {
                setCustomTextStyle(selectedStoryText);
            } else {
                setDefaultImageStyle(selectedStoryText);
            }

            // Set the selected story text
            selectedStoryText.textContent = storyText;
            selectedStoryText.style.backgroundColor = bgColor;
            selectedStoryText.style.color = textColor;

            // Show the selected story container
            selectedStoryContainer.style.visibility = 'visible';

            // Remove existing audio element
            removeBackgroundAudio();

            // Create a new audio element
            backgroundAudio = document.createElement('audio');
            backgroundAudio.src = musicUrl;
            backgroundAudio.autoplay = true;
            backgroundAudio.loop = true;

            // Append the new audio element to the story overlay
            document.querySelector('.story-overlay').appendChild(backgroundAudio);

            // Remove existing image element
            removeSelectedImage();

            // Add logic to handle image display
            if (imageExists && imageFile !== 'default.png') {
                // If it's not 'default.png', create and append the selected image
                createSelectedImage(`storage/story/${imageFile}`);
            } else {
                // If the image file name is 'default.png', create a colored box
                createImageBox(bgColor);
            }

            // Add logic to handle swipe and close buttons
            closeBtn.addEventListener('click', handleCloseButtonClick);
            swipeLeftBtn.addEventListener('click', handleSwipeLeftButtonClick);
            swipeRightBtn.addEventListener('click', handleSwipeRightButtonClick);

            currentStoryIndex = index;
            // Hide or show swipe buttons based on the current index
            handleSwipeButtonsVisibility();
        });
    });


    function handleCloseButtonClick() {
        // Pause and remove the background music when the story is closed
        removeBackgroundAudio();

        // Remove the selected image element, if it exists
        removeSelectedImage();

        // Clear the story text
        selectedStoryText.textContent = '';

        hideSelectedStory();
    }

    function hideSelectedStory() {
        // Clear the content of the selected story container
        selectedStoryContainer.style.visibility = 'hidden';

        // Remove event listeners to prevent potential memory leaks
        closeBtn.removeEventListener('click', handleCloseButtonClick);
        swipeLeftBtn.removeEventListener('click', handleSwipeLeftButtonClick);
        swipeRightBtn.removeEventListener('click', handleSwipeRightButtonClick);
    }

    function handleSwipeLeftButtonClick() {
        if (currentStoryIndex > 0) {
            currentStoryIndex--;
             // Update the view count in the overlay
             selectedViewCount.textContent = stories[currentStoryIndex].querySelector('.view-count').textContent;
            updateStoryContent();
        }
    }

    function handleSwipeRightButtonClick() {
        if (currentStoryIndex < stories.length - 1) {
            currentStoryIndex++;
             // Update the view count in the overlay
             selectedViewCount.textContent = stories[currentStoryIndex].querySelector('.view-count').textContent;
            updateStoryContent();
        }
    }

    function updateStoryContent() {
        const currentStory = stories[currentStoryIndex];

        // Update the story text, background color, and text color
        selectedStoryText.textContent = currentStory.querySelector('.story-text').textContent;
        selectedStoryText.style.backgroundColor = currentStory.getAttribute('data-bg-color');
        selectedStoryText.style.color = currentStory.getAttribute('data-text-color');

        // Set styles for selected-story-text based on the image
        const imageExists = currentStory.getAttribute('data-image-exists') === 'true';
        const imageFile = currentStory.getAttribute('data-image-file');

        if (imageExists && imageFile !== 'default.png') {
            setCustomTextStyle(selectedStoryText);
        } else {
            // For default.png, set width and height to 250px
            setDefaultImageStyle(selectedStoryText);
        }

        // Change the background music based on the current story
        const newMusicUrl = currentStory.getAttribute('data-music-url');
        backgroundAudio.src = newMusicUrl;

        // Remove existing image element
        removeSelectedImage();

        // Add logic to handle image display
        if (imageExists && imageFile !== 'default.png') {
            // If it's not 'default.png', create and append the selected image
            createSelectedImage(`storage/story/${imageFile}`);
        } else {
            // If the image file name is 'default.png', create a colored box
            createImageBox(currentStory.getAttribute('data-bg-color') || 'transparent');
        }

        // Always show text and background color
        selectedStoryContainer.style.visibility = 'visible';

        // Hide or show swipe buttons based on the current index
        handleSwipeButtonsVisibility();
    }

    function handleSwipeButtonsVisibility() {
        swipeLeftBtn.style.display = currentStoryIndex > 0 ? 'block' : 'none';
        swipeRightBtn.style.display = currentStoryIndex < stories.length - 1 ? 'block' : 'none';
    }

    function removeBackgroundAudio() {
        if (backgroundAudio) {
            backgroundAudio.pause();
            backgroundAudio.remove();
        }
    }

    function removeSelectedImage() {
        const selectedImage = selectedStoryContainer.querySelector('img');
        if (selectedImage) {
            selectedImage.remove();
        }
    }

    function createSelectedImage(src) {
        const selectedImage = document.createElement('img');
        selectedImage.src = src;
        selectedImage.alt = 'Selected Story Image';
        selectedImage.style.width = '250px';
        selectedImage.style.height = 'auto';
        selectedImage.style.objectFit = 'cover';
        selectedStoryContainer.appendChild(selectedImage);
        selectedStoryText.classList.add('custom-text-style');
    }

    function createImageBox(bgColor) {
        const imageBox = document.createElement('div');
        imageBox.style.backgroundColor = bgColor;
        imageBox.style.width = '100%';
        imageBox.style.height = '100%';
        imageBox.style.borderRadius = '10px';
        selectedStoryContainer.appendChild(imageBox);
        selectedStoryText.classList.remove('custom-text-style');
    }

    function setCustomTextStyle(element) {
        element.style.height = 'fit-content';
        element.style.minWidth = '250px';
        element.style.maxWidth = '250px';
        element.style.minHeight = 'fit-content';
    }

    function setDefaultImageStyle(element) {
        element.style.height = '250px';
        element.style.width = '250px';
        element.style.minWidth = '250px';
        element.style.maxWidth = '250px';
        element.style.minHeight = '250px';
    }
});
		</script>
		<!--/.Main layout-->
	</main>
@stop
