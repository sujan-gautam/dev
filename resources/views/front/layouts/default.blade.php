<!DOCTYPE html>
<html lang="{{App::getLocale()}}">
@include('front.includes.head')
<body>
@include('front.includes.header')
@yield('body_start')

@yield('content')

@include('front.includes.footer')
     
@include('front.includes.foot')
</body>
</html>