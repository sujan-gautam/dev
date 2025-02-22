<html>
  <head>
    <title>@if(isset($page_title)){{$page_title.' - '}}@endif {{ config('settings.site_name') }}</title>

    <link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>

    <style>
      body {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        color: #B0BEC5;
        display: table;
        font-weight: 100;
        font-family: 'Lato';
      }

      .container {
        text-align: center;
        display: table-cell;
        vertical-align: middle;
      }

      .content {
        text-align: center;
        display: inline-block;
      }

      .title {
        font-size: 156px;
      }

      .quote {
        font-size: 36px;
      }

      .explanation {
        font-size: 24px;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="content">
        <div class="title">420</div>
        <div class="quote">{{ __('IP Blocked') }}</div>
        <div class="explanation">
          <br>
          <small>
            {!! __('Your IP address is blocked by site administrator') !!}
         </small>
       </div>
      </div>
    </div>
  </body>
</html>
