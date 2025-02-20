<!-- The Modal -->
<div class="modal" id="localeModal">
  <div class="modal-dialog modal-sm">
    <div class="modal-content"> 
      
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">{{ __('Site Languages')}}</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

        <!-- Modal body -->
        <div class="modal-body"> 
          <ul style="list-style-type: none;">
            @forelse(get_locales() as $lang)
              <li><a href="{{url('lang/'.$lang->code)}}" style="margin-left: 4.0rem!important;">{{$lang->name}}</a></li>
            @empty
                {{ __('No results')}}
            @endforelse

            </ul>
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button class="btn btn-danger btn-sm" data-dismiss="modal">{{ __('Close')}}</button>
        </div>
    </div>
  </div>
</div>




<!--Footer-->
<footer class="page-footer text-center text-md-left mt-0 pt-4"> 
  <!--Footer links-->
  <div class="container-fluid">
    <div class="row"> 
      <!--First column-->
      <div class="col-lg-4 col-md-6 ml-auto">
        <h5 class="title mb-3"><strong>{{ __('About')}} {{config('settings.site_name')}}</strong></h5>
        <p>{{config('settings.footer_text')}}</p>
      </div>
      <!--/.First column-->
      <hr class="w-100 clearfix d-sm-none">
      <!--Second column-->
      <div class="col-lg-3 col-md-6 ml-auto mb-4">
        <h5 class="title mb-3"><strong>{{ __('Pages')}}</strong></h5>
        <ul class="list-unstyled">
          @foreach(get_pages_menu() as $p)
          <li> <a href="{{$p->url}}">{{ $p->title}}</a> </li>
          @endforeach
        </ul>
      </div>
      <!--/.Second column-->
      <hr class="w-100 clearfix d-sm-none">
      <!--Third column-->
      <div class="col-lg-3 col-md-6 ml-auto">
        <h5 class="title mb-3"><strong>{{ __('Useful Links')}}</strong></h5>
        <ul class="list-unstyled">
          <li> <a href="{{route('archive.list')}}">{{ __('Syntax Languages')}}</a> </li>
          <li> <a href="{{route('contact')}}">{{ __('Contact Us')}}</a> </li>
          <li> <a data-toggle="modal" data-target="#localeModal">{{ __('Site Languages')}}</a> </li>
          <li> <a href="{{route('sitemap')}}">{{ __('Sitemap')}}</a> </li>
        </ul>
      </div>
      <!--/.Third column-->
      <hr class="w-100 clearfix d-sm-none">
    </div>
    
    @if(!empty(config('settings.social_fb')) || !empty(config('settings.social_tw')) || !empty(config('settings.social_gp')) || !empty(config('settings.social_lin')) || !empty(config('settings.social_insta')) || !empty(config('settings.social_pin')))
    <!-- Grid row-->
    <div class="row"> 
      
      <!-- Grid column -->
      <div class="col-md-12">
        <div class="mb-5 flex-center"> 
          
          @if(!empty(config('settings.social_fb')))
          <a class="fb-ic" href="{{config('settings.social_fb')}}" target="_blank"> <i class="fa fa-facebook fa-lg white-text mr-md-5 mr-3 fa-2x"> </i> </a> @endif

          @if(!empty(config('settings.social_tw')))
          <a class="tw-ic" href="{{config('settings.social_tw')}}" target="_blank"> <i class="fa fa-twitter fa-lg white-text mr-md-5 mr-3 fa-2x"> </i> </a> @endif
        
          @if(!empty(config('settings.social_gp')))
          <a class="gplus-ic" href="{{config('settings.social_gp')}}" target="_blank"> <i class="fa fa-google-plus fa-lg white-text mr-md-5 mr-3 fa-2x"> </i> </a> @endif
        
          @if(!empty(config('settings.social_lin')))
          <a class="li-ic" href="{{config('settings.social_lin')}}" target="_blank"> <i class="fa fa-linkedin fa-lg white-text mr-md-5 mr-3 fa-2x"> </i> </a> @endif
        
          @if(!empty(config('settings.social_insta')))
          <a class="ins-ic" href="{{config('settings.social_insta')}}" target="_blank"> <i class="fa fa-instagram fa-lg white-text mr-md-5 mr-3 fa-2x"> </i> </a> 
          @endif          

          @if(!empty(config('settings.social_tg')))
          <a class="ins-ic" href="{{config('settings.social_tg')}}" target="_blank"> <i class="fa fa-telegram fa-lg white-text mr-md-5 mr-3 fa-2x"> </i> </a> 
          @endif          

          @if(!empty(config('settings.social_discord')))
          <a class="ins-ic mr-5" href="{{config('settings.social_discord')}}" target="_blank"> <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-discord" viewBox="0 0 16 16">
            <path d="M13.545 2.907a13.227 13.227 0 0 0-3.257-1.011.05.05 0 0 0-.052.025c-.141.25-.297.577-.406.833a12.19 12.19 0 0 0-3.658 0 8.258 8.258 0 0 0-.412-.833.051.051 0 0 0-.052-.025c-1.125.194-2.22.534-3.257 1.011a.041.041 0 0 0-.021.018C.356 6.024-.213 9.047.066 12.032c.001.014.01.028.021.037a13.276 13.276 0 0 0 3.995 2.02.05.05 0 0 0 .056-.019c.308-.42.582-.863.818-1.329a.05.05 0 0 0-.01-.059.051.051 0 0 0-.018-.011 8.875 8.875 0 0 1-1.248-.595.05.05 0 0 1-.02-.066.051.051 0 0 1 .015-.019c.084-.063.168-.129.248-.195a.05.05 0 0 1 .051-.007c2.619 1.196 5.454 1.196 8.041 0a.052.052 0 0 1 .053.007c.08.066.164.132.248.195a.051.051 0 0 1-.004.085 8.254 8.254 0 0 1-1.249.594.05.05 0 0 0-.03.03.052.052 0 0 0 .003.041c.24.465.515.909.817 1.329a.05.05 0 0 0 .056.019 13.235 13.235 0 0 0 4.001-2.02.049.049 0 0 0 .021-.037c.334-3.451-.559-6.449-2.366-9.106a.034.034 0 0 0-.02-.019Zm-8.198 7.307c-.789 0-1.438-.724-1.438-1.612 0-.889.637-1.613 1.438-1.613.807 0 1.45.73 1.438 1.613 0 .888-.637 1.612-1.438 1.612Zm5.316 0c-.788 0-1.438-.724-1.438-1.612 0-.889.637-1.613 1.438-1.613.807 0 1.451.73 1.438 1.613 0 .888-.631 1.612-1.438 1.612Z"/>
          </svg> 
        </a> 
        @endif
        
          @if(!empty(config('settings.social_pin')))
          <a class="pin-ic" href="{{config('settings.social_pin')}}" target="_blank"> <i class="fa fa-pinterest fa-lg white-text fa-2x"> </i> </a> 
          @endif


        </div>
      </div>
      <!-- Grid column --> 
      
    </div>
    <!-- Grid row--> 
    @endif

  </div>
  
  <!--/.Footer links--> 
  
  <!--Copyright-->
  <div class="footer-copyright py-3 text-center">
    <div class="containter-fluid"> © {{date('Y')}} <a href="{{url('/')}}">{{config('settings.site_name')}}</a>. Developed By <a href="https://sujan1919.com.np/" rel="nofollow">Sujan</a></div>
  </div>
  <!--/.Copyright--> 
</footer>
<!--/.Footer--> 
