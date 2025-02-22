<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="csrf-token" content="{{csrf_token()}}" />
<title>@if(isset($page_title)){{$page_title.' - '}}@endif{{config('settings.site_name')}}</title>
<link rel="shortcut icon" href="{{url('favicon.ico')}}" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="{{url('css/bootstrap.min.css')}}" rel="stylesheet">
<link href="{{url('css/mdb.min.css')}}" rel="stylesheet">
<link href="{{url('css/style.min.css')}}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{url('plugins/prismjs/prism.css')}}">
<style>
h1,h2,h3,h4,h5{ word-break: break-all !important; }
@if(config('settings.paste_break_word') ==  1)
code{ white-space: pre-wrap !important;     word-wrap: break-word !important; }
@endif

.toolbar{
  display: none !important;
}
</style>
@if(config('settings.syntax_highlighter') == 'codemirror')
    <link rel="stylesheet" href="{{url('plugins/codemirror-5.52.0/lib/codemirror.min.css')}}" />
    <link rel="stylesheet" href="{{url('plugins/codemirror-5.52.0/theme/'.config('settings.codemirror_skin').'.css')}}" />
    <style>

        @if(config('settings.paste_view_height') == 'auto')
        .CodeMirror{ border:1px solid lightgray;height:auto; }
        @else
        .CodeMirror{ border:1px solid lightgray;height:700px; }
        @endif

    .CodeMirror-lines{ padding:0 !important;}
      .cm-s-monokai .CodeMirror-linenumber{ text-align: center !important; }
  </style>
    @if(get_syntax_by_name($paste->syntax)->active_link == 1)
    <style>.cm-url { color: #007bff; cursor: pointer; }</style>
    @endif  
@elseif(config('settings.syntax_highlighter') != 'ace')
<style>
            @if(config('settings.paste_view_height') != 'auto')
            pre{ max-height:{{ config('settings.paste_editor_height') * 22 . 'px' }}; }
            @endif  
</style>
@endif

</head>
<body>
<div class="card" id="printarea">
<div class="card-header"> {{$paste->title_f}} - <span class="badge badge-light"><a href="{{ route('archive',[$paste->syntax]) }}">{{get_syntax_name($paste->syntax)}}</a></span> <small class="text-muted">{{$paste->content_size}} {{ __('KB') }}</small> 
    @if(!empty($paste->expire_time))<small><i class="fa fa-clock-o text-warning ml-2" title="{{ __('Expire') }}"></i> {{ $paste->expire_time_f }}</small>@endif
    <div class="pull-right d-print-none">
        @if(config('settings.feature_copy') == 1)
            <a class="badge badge-grey" onclick="copyToClip(content)">{{ __('copy')}}</a>
        @endif
        @if(config('settings.feature_raw') == 1 && empty($paste->password))<a href="{{url('raw/'.$paste->slug)}}" class="badge badge-default">{{ __('raw')}}</a> @endif
        @if(config('settings.feature_download') == 1 && empty($paste->password))<a href="{{url('download/'.$paste->slug)}}" class="badge badge-primary">{{ __('download')}}</a> @endif
        @if(config('settings.feature_print') == 1)<a onclick="printDiv('printarea')" class="badge badge-info">{{ __('print')}}</a> @endif
    </div>
  </div>
  <div class="card-body p-2">
    @if(!empty($paste->password))
       <form id="unlock_form">
        <div class="row justify-content-center">
              <div class="form-group col-md-3  text-center">
                    <small class="text-muted">{{ __('To unlock this paste, please enter password.')}}</small>
                    <input type="password" class="form-control mb-1" id="password" placeholder="{{ __('Password')}}" autofocus tabindex="1">
                    <small id="password_response" class="pt-1"></small>
              </div>
              <div class="form-group col-md-12 text-center">
                  <button class="btn btn-sm btn-default m-0" type="submit" id="passwordBtn" tabindex="2">{{ __('Unlock')}}</button>
              </div>
        </div>
      </form>
    @endif
    <pre class="@if(config('settings.syntax_highlighter_line_numbers') == 1) line-numbers @endif pre-editor @if(!empty($paste->password)) d-none @endif" id="pre">{{ __("Loading Please wait")}}...</pre>
    @if(config('settings.syntax_highlighter') == 'ace' || config('settings.syntax_highlighter') == 'codemirror') <textarea id="editor" class="d-none"></textarea>@endif
    <p class="text-center p-0 mt-1 mb-0">{{ __('Paste Hosted With')}} <i class="fa fa-heart"></i> {{ __('By')}} <a href="{{url('/')}}" target="_blank">{{config('settings.site_name')}}</a></p>
  </div>
</div>
<script type="text/javascript">
var content = '';
var txt_copied = '{{ __("Copied")}}';
var txt_copy = '{{ __("Copy")}}';
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="{{url('js/bootstrap.min.js')}}"></script>
<script src="{{url('js/mdb.min.js')}}"></script>
<script src="{{url('js/embed.js')}}"></script>

@if(config('settings.syntax_highlighter') == 'ace')
<!-- Ace -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.3/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.3/ext-modelist.js"></script>
<script type="text/javascript">
  var type = 1;
  var syntax = "javascript";
  var syntax_extension = "{{$paste->extension}}";

</script>
@elseif(config('settings.syntax_highlighter') == 'codemirror')
<script src="{{url('plugins/codemirror-5.52.0/lib/codemirror.min.js')}}"></script>
<script src="{{url('plugins/codemirror-5.52.0/addon/mode/loadmode.js')}}"></script>
<script src="{{url('plugins/codemirror-5.52.0/addon/edit/matchbrackets.js')}}"></script>
<script src="{{url('plugins/codemirror-5.52.0/addon/fold/foldcode.js')}}"></script>
<script src="{{url('plugins/codemirror-5.52.0/addon/fold/foldgutter.js')}}"></script>
<script src="{{url('plugins/codemirror-5.52.0/mode/meta.js')}}"></script>
<script src="{{url('js/codemirror-link-overlay.js')}}"></script>
<script>
var syntax_extension = "{{$paste->extension}}"; 
var theme = "{{config('settings.codemirror_skin')}}"; 
CodeMirror.modeURL = '{{url("plugins/codemirror-5.52.0")}}/mode/%N/%N.js'; 
function changeMode(editor,ext)
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

  
$(document).on('click touchstart', '.cm-url', function(event) {

  var link = $(this).text();
  if (!(new RegExp('^(?:(?:https?|ftp):\/\/)', 'i')).test(link)) link="http:\/\/"+link;

  @if(get_syntax_by_name($paste->syntax)->redirect_page == 1)

    link = '{{ route("redirect").'?url=' }}'+btoa(link);

  @endif

  window.open(link, '_blank');  
});
</script>
@else
<script src="{{url('plugins/prismjs/prism.js')}}"></script>
<script type="text/javascript">
(function(){

if (
    typeof self !== 'undefined' && !self.Prism ||
    typeof global !== 'undefined' && !global.Prism
) {
    return;
}

Prism.hooks.add('before-highlight', function(env) {
    var tokens = env.grammar;

    if (!tokens) return;

    tokens.tab = '';
    tokens.crlf = '';
    tokens.lf = '';
    tokens.cr = '';
    tokens.space = '';
});
})();
</script>
@endif
@if(!empty($paste->password))
<script type="text/javascript">
$(document).ready(function($) {

  $('#unlock_form').on('submit', function(event) {
    event.preventDefault();
    $("#passwordBtn").prop('disabled','disabled');
    $("#password_response").html(' ');
    $.ajax({
      url: '{{route("paste.get")}}',
      type: 'POST',
      data: {slug: '{{$paste->slug}}',password: $('#password').val(), _token: '{{ csrf_token() }}'},
    })
    .done(function(data) {
      if(data.status == 'success')
      {
          $("pre").removeClass('d-none');
          content = atob(data.content);
          content = decodeURIComponent(content.replace(/\+/g, '%20'));

       @if(config('settings.syntax_highlighter') == 'ace')

        var editor = ace.edit("editor");
        editor.setTheme("ace/theme/{{config('settings.ace_editor_skin')}}");
        editor.$blockScrolling = Infinity;
        editor.setValue(content, -1);
        editor.setShowPrintMargin(false);
        editor.setReadOnly(true);
        editor.setHighlightActiveLine(false);

        editor.setOptions({
            autoScrollEditorIntoView: true,
            wrap: true,
            @if(config('settings.paste_view_height') == 'auto')
            maxLines: Infinity,
            @else
            maxLines: 50,
            @endif
            @if(config('settings.syntax_highlighter_line_numbers') == 0)
            showLineNumbers: false,
            showGutter: false
            @endif
        });
        editor.renderer.$cursorLayer.element.style.display = "none"

        var tempPath = "file."+syntax_extension;
        var modelist = ace.require("ace/ext/modelist");
        var tempMode = modelist.getModeForPath(tempPath).mode;
        editor.session.setMode(tempMode);

        $(".pre-editor").addClass("d-none");

          @elseif(config('settings.syntax_highlighter') == 'codemirror')

                  var editor = CodeMirror.fromTextArea(document.getElementById("editor"), {
                    lineNumbers: true,
                    lineWrapping: true,
                    matchBrackets: true,
                    styleActiveLine: true,
                      readOnly: true,                   
                    theme: theme,
                    mode: "text"
                  });
                  editor.setValue(content, -1);
                  changeMode(editor,syntax_extension);
                  $(".pre-editor").addClass("d-none");
                  hyperlinkOverlay(editor);
          @else


          var code = document.createElement('code');
          code.className = 'language-{{$paste->syntax}}';
          var pre = document.getElementById("pre");
          pre.textContent = '';

          code.textContent = content;
          pre.appendChild(code);

          Prism.highlightElement(code);

          @endif


          $("#unlock_form").remove();
      }
      else{
        $('#password').val('');
        $("#password_response").html(data.message);
      }
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      console.log("complete");
      $("#passwordBtn").removeAttr('disabled');
    });


  });

});
</script>
@else
<script type="text/javascript">

$(document).ready(function($) {

$.ajax({
url: '{{route("paste.get")}}',
type: 'POST',
data: {slug: '{{$paste->slug}}',password: $('#password').val(), _token: '{{ csrf_token() }}'},
})
.done(function(data) {
if(data.status == 'success')
{
content = atob(data.content);
content = decodeURIComponent(content.replace(/\+/g, '%20'));

@if(config('settings.syntax_highlighter') == 'ace')

    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/{{config('settings.ace_editor_skin')}}");
    editor.$blockScrolling = Infinity;
    editor.setValue(content, -1);
    editor.setShowPrintMargin(false);
    editor.setReadOnly(true);
    editor.setHighlightActiveLine(false);

    editor.setOptions({
        autoScrollEditorIntoView: true,
        wrap: true,
        @if(config('settings.paste_view_height') == 'auto')
        maxLines: Infinity,
        @else
        maxLines: 50,
        @endif
        @if(config('settings.syntax_highlighter_line_numbers') == 0)
        showLineNumbers: false,
        showGutter: false
        @endif
    });
    editor.renderer.$cursorLayer.element.style.display = "none"

    var tempPath = "file."+syntax_extension;
    var modelist = ace.require("ace/ext/modelist");
    var tempMode = modelist.getModeForPath(tempPath).mode;
    editor.session.setMode(tempMode);

    $(".pre-editor").addClass("d-none");

@elseif(config('settings.syntax_highlighter') == 'codemirror')

    var editor = CodeMirror.fromTextArea(document.getElementById("editor"), {
      lineNumbers: true,
      lineWrapping: true,
      matchBrackets: true,
      styleActiveLine: true,
      readOnly: true,
      theme: theme,
      mode: "text"
    });
    editor.setValue(content, -1);

    changeMode(editor,syntax_extension);
    $(".pre-editor").addClass("d-none");
    hyperlinkOverlay(editor);
@else


var code = document.createElement('code');
code.className = 'language-{{$paste->syntax}}';
var pre = document.getElementById("pre");
pre.textContent = '';

code.textContent = content;
pre.appendChild(code);

Prism.highlightElement(code);

@endif

}
else{
$("#password_response").html(data.message);
}
})
.fail(function() {
  console.log("error");
})
.always(function() {
  console.log("complete");
  $("#passwordBtn").removeAttr('disabled');
});


});
</script>
@endif
</body>
</html>