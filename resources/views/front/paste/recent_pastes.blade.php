@if(config('settings.site_layout') == 1)
<div class="col-md-3 recent_paste_container">
  @if(Auth::check())
  @if(!empty(get_my_recent_pastes()))
  <div class="card mb-2 mt-2 paste_list">
    <div class="card-header"> {{ __('My Recent Pastes')}} </div>
    <ul class="list-group list-group-flush">
      @forelse(get_my_recent_pastes() as $p)
      <li class="list-group-item"> <i class="fa fa-paste blue-grey-text small"></i> @if(!empty($p->password))<i class="fa fa-lock pink-text small"></i>@endif @if(!empty($p->expire_time))<i class="fa fa-clock-o text-warning small"></i> @endif <a href="{{$p->url}}">{{$p->title_f}}</a>
        <p><small class="text-muted"><a href="{{ route('archive',[$p->syntax]) }}">{{get_syntax_name($p->syntax)}}</a> | <i class="fa fa-eye blue-grey-text"></i> {{$p->views_f}} | {{$p->created_ago}}</small></p>
      </li>
      @empty
      <li class="list-group-item text-center">{{ __('No results')}}</li>
      @endforelse
    </ul>
  </div>
  @endif
  @endif

  @if(config('settings.recent_pastes_limit') > 0)
  <div class="card paste_list">
    <div class="card-header"> {{ __('Recent Pastes')}} </div>
    <ul class="list-group list-group-flush">
      @forelse(get_recent_pastes() as $p)
      <li class="list-group-item"> <i class="fa fa-paste blue-grey-text small"></i> @if(!empty($p->password))<i class="fa fa-lock pink-text small"></i>@endif @if(!empty($p->expire_time))<i class="fa fa-clock-o text-warning small"></i> @endif <a href="{{$p->url}}">{{$p->title_f}}</a>
        <p><small class="text-muted"><a href="{{ route('archive',[$p->syntax]) }}">{{get_syntax_name($p->syntax)}}</a> | <i class="fa fa-eye blue-grey-text"></i> {{$p->views_f}} | {{$p->created_ago}}</small></p>
      </li>
      @empty
      <li class="list-group-item text-center">{{ __('No results')}}</li>
      @endforelse
    </ul>
  </div>
  @endif

  @if(config('settings.ad') == 1 && !empty(config('settings.ad3')))<div class="col-md-12 m-2 text-center">{!! html_entity_decode(config('settings.ad3')) !!}</div>@endif 
</div>
@endif