<!DOCTYPE html>
<html lang="{{App::getLocale()}}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="csrf-token" content="{{csrf_token()}}">
<title>@if(isset($page_title)){{$page_title.' | '}}@endif{{config('settings.site_name')}}</title>
<link rel="shortcut icon" href="{{url('favicon.ico')}}" />
@yield('before_styles')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="{{url('css/bootstrap.min.css')}}" rel="stylesheet">
<link href="{{url('css/mdb.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" />
@yield('after_styles')
</head>
<body>
<main> 
  
  <!--Main layout-->
  <div class="container">
    <h1 class="text-center mt-5 mb-4">{{config('settings.site_name')}} Admin</h1>
    <!--First row-->
    <div class="row">
      <div class="col-md-8 mx-auto"> 
        
        <!-- Default form login -->
        <form class="text-center border border-light p-5" method="post" action="{{route('login')}}">
          @csrf
          <p class="h4 mb-4">Sign in</p>
          
          <!-- Email -->
          <input type="text" name="email" id="defaultLoginFormEmail" class="form-control mb-4" placeholder="Username / E-mail address" tabindex="1" required>
          
          <!-- Password -->
          <input type="password" name="password" id="defaultLoginFormPassword" class="form-control mb-4" placeholder="Password" tabindex="2" required>
          
          @include('front.includes.captcha')


          <div class="d-flex justify-content-around">
            <div> 
              <!-- Remember me -->
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="defaultLoginFormRemember" name="remember" value="on" tabindex="4">
                <label class="custom-control-label" for="defaultLoginFormRemember">Remember me</label>
              </div>
            </div>
          </div>
          <div class="mt-2">@include('front.includes.messages')</div>
          
          <!-- Sign in button -->
          <button class="btn btn-indigo btn-block my-4" type="submit" tabindex="5">Sign in</button>
        </form>
        <!-- Default form login --> 
        
      </div>
    </div>
  </div>
</main>

<!--Footer-->
<footer class="text-center font-small mt-4"> 
  
  <!--Copyright-->
  <div class="footer-copyright py-3"> © {{date('Y')}} <a href="{{url('/')}}">{{config('settings.site_name')}}</a>. 
    Developed By <a href="https://ecodevs.com">EcoDevs</a> </div>
  <!--/.Copyright--> 
  
</footer>
<!--/.Footer--> 

<!-- SCRIPTS --> 
@yield('before_scripts') 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> 
<script type="text/javascript" src="{{url('js/popper.min.js')}}"></script> 
<script src="{{url('js/bootstrap.min.js')}}"></script> 
<script src="{{url('js/mdb.min.js')}}"></script> 
@yield('after_scripts') 
@stack('js_before') 
<script type="text/javascript">
new WOW().init();   
</script>
</body>
</html>