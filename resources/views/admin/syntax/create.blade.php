@extends('admin.layouts.default')

@section('content') 
<!--Main layout-->
<main class="pt-5 mx-lg-5">
  <div class="container mt-5">
    
    <!-- Heading -->
    <div class="card mb-4 "> 
      
      <!--Card content-->
      <div class="card-body d-sm-flex justify-content-between">
        <h4 class="mb-2 mb-sm-0 pt-1"> 
          <a href="{{url('admin/dashboard')}}">{{ __('Admin') }}</a> <span>/</span> 
          <a href="{{url('admin/syntax-languages')}}">{{$page_title}}</a> <span>/</span> 
          <span>{{ __('Create') }}</span> 
        </h4>
      </div>
    </div>
    <!-- Heading --> 
    
    <!--Grid row-->
    <div class="row "> 
      
      <!--Grid column-->
      <div class="col-md-12 mb-4"> @include('admin.includes.messages') 
        <!--Card-->
        <div class="card mb-4"> 
          
          <!-- Card header -->
          <div class="card-header"> {{$page_title}} {{ __('Create') }} </div>
          
          <!--Card content-->
          <div class="card-body">
            <form method="post">
              @csrf

              <div class="row">            
                <div class="form-group col-md-6">
                  <label>Name</label>
                  <input type="text" class="form-control" name="name" placeholder="Syntax Name" value="{{old('name')}}">
                </div>
                <div class="form-group col-md-6">
                  <label>Extension</label>
                  <input type="text" class="form-control" name="extension" placeholder="Extension [optional]" value="{{old('extension')}}">
                </div>
              </div>

              <div class="row">
                  <div class="form-group  col-md-6">
                    <label>Popular </label> <br />
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" class="custom-control-input" id="popular1" name="popular" value="1" @if(old('popular') == 1) checked @endif>
                      <label class="custom-control-label" for="popular1">Yes </label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" class="custom-control-input" id="popular2" name="popular" value="0" @if(old('popular') == 0) checked @endif>
                      <label class="custom-control-label" for="popular2">No </label>
                    </div>
                  </div>                        


                  <div class="form-group  col-md-6">
                    <label>Active </label> <br />
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" class="custom-control-input" id="active1" name="active" value="1" @if(old('active',1) == 1) checked @endif>
                      <label class="custom-control-label" for="active1">Yes </label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" class="custom-control-input" id="active2" name="active" value="0" @if(old('active',1) == 0) checked @endif>
                      <label class="custom-control-label" for="active2">No </label>
                    </div>
                  </div>
              </div>              

              <div class="row">
                  <div class="form-group  col-md-6">
                    <label>Active Link </label> <br />
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" class="custom-control-input" id="active_link1" name="active_link" value="1" @if(old('active_link') == 1) checked @endif>
                      <label class="custom-control-label" for="active_link1">Yes </label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" class="custom-control-input" id="active_link2" name="active_link" value="0" @if(old('active_link') == 0) checked @endif>
                      <label class="custom-control-label" for="active_link2">No </label>
                    </div>
                  </div>                        


                  <div class="form-group  col-md-6">
                    <label>Redirect Page </label> <br />
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" class="custom-control-input" id="redirect_page1" name="redirect_page" value="1" @if(old('redirect_page') == 1) checked @endif>
                      <label class="custom-control-label" for="redirect_page1">Yes </label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" class="custom-control-input" id="redirect_page2" name="redirect_page" value="0" @if(old('redirect_page') == 0) checked @endif>
                      <label class="custom-control-label" for="redirect_page2">No </label>
                    </div>
                  </div>
              </div>



              <div class="form-group col-md-6">
                <button class="btn btn-success" type="submit">Save</button>
                <a href="{{url('admin/syntax-languages')}}" class="btn btn-default">Cancel</a> </div>
            </form>
          </div>
        </div>
        <!--/.Card--> 
        
      </div>
      <!--Grid column--> 
      
    </div>
    <!--Grid row--> 
    
  </div>
</main>
<!--Main layout--> 
@stop