@extends('admin.layouts.default')

@section('after_styles')
	<link rel="stylesheet" type="text/css" href="{{url('css/addons/datatables.min.css')}}">
	<link rel="stylesheet" href="https://cdn.datatables.net/responsive/1.0.7/css/responsive.dataTables.min.css">
@stop

@section('after_scripts')
	<script type="text/javascript" src="{{url('js/addons/datatables.min.js')}}"></script>
	<script src="https://cdn.datatables.net/responsive/1.0.7/js/dataTables.responsive.min.js"></script>
	<script>
        $(function () {

            var table = $('#example1').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                order: [[1, 'desc']],
                ajax: '{{route("comments.get")}}',
                columns: [
                    {data: 'check', name: 'id', orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    {data: 'commenter', name: 'commenter_id', orderable: false, searchable: false},
                    {data: 'commentable', name: 'commentable_id', orderable: false, searchable: false},
                    {data: 'guest_name', name: 'guest_name'},
                    {data: 'guest_email', name: 'guest_email'},
                    {data: 'comment', name: 'comment'},
                    {data: 'approved', name: 'approved'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });

            $("#check_all").on('click', function () {
                if ($('#check_all').is(':checked')) {
                    $('.check').prop('checked', true);
                } else {
                    $('.check').prop('checked', false);
                }
            });

            $('.del_selected').on('click', function () {
                var url = "{{url('admin/comments/delete-selected')}}";
                var data = {'ids[]': []};
                $(".check:checked").each(function () {
                    data['ids[]'].push($(this).val());
                });
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                })
                    .done(function (data) {
                        if (data == 'success') {
                            $("#response").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Items successfully deleted.</div>');
                            table.ajax.reload();
                            $('#check_all').prop('checked', false);
                        } else {
                            $("#response").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>No items selected.</div>');
                        }
                    });
            });            

            $('.approve_selected').on('click', function () {
                var url = "{{url('admin/comments/approve-selected')}}";
                var data = {'ids[]': []};
                $(".check:checked").each(function () {
                    data['ids[]'].push($(this).val());
                });
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                })
                    .done(function (data) {
                        if (data == 'success') {
                            $("#response").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Items successfully approved.</div>');
                            table.ajax.reload();
                            $('#check_all').prop('checked', false);
                        } else {
                            $("#response").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>No items selected.</div>');
                        }
                    });
            });



        });


        $('tbody').on('click', '.view_comment', function(event) {
        	event.preventDefault();
        	
        	$('#commenter').html($(this).data('commenter'));
        	$('#commenter_avatar').attr('src',$(this).data('avatar'));
        	$('#comment').html(atob($(this).data('comment')));
        	$('#comment_created').html($(this).data('created'));
        	$('#commentModal').modal('toggle');
        });
	</script>
@stop


@section('content')
	<!--Main layout-->
	<main class="pt-5 mx-lg-5">
		<div class="container-fluid mt-5">
			<!-- Heading -->
			<div class="card mb-4 ">
				<!--Card content-->
				<div class="card-body d-sm-flex justify-content-between">
					<h4 class="mb-2 mb-sm-0 pt-1"><a href="{{url('admin/dashboard')}}">Admin</a> <span>/</span>
						<span>{{$page_title}}</span></h4>
				</div>
			</div>
			<!-- Heading -->
			<!--Grid row-->
			<div class="row ">
				<!--Grid column-->
				<div class="col-md-12 mb-4"> @include('admin.includes.messages')
					<div id="response"></div>
					<!--Card-->
					<div class="card mb-4">
						<!-- Card header -->
						<div class="card-header">
							<h4 class="float-left"> {{$page_title}}</h4>
							<!-- Basic dropdown -->
							<button class="btn btn-primary btn-sm dropdown-toggle mr-4 float-right" type="button" data-toggle="dropdown" aria-haspopup="true"
									aria-expanded="false">Action
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item approve_selected"><i class="fa fa-check"></i> Approve Selected</a>
								<a class="dropdown-item del_selected"><i class="fa fa-trash"></i> Delete Selected</a>
							</div>
							<!-- Basic dropdown -->
						</div>
						<!--Card content-->
						<div class="card-body">
							<table id="example1" class="table table-bordered table-striped responsive nowrap" cellspacing="0" width="100%">
								<thead>
								<tr>
									<th><input type="checkbox" id="check_all" class="check_all"></th>
									<th>ID</th>
									<th>Commenter</th>
									<th>Paste</th>
									<th>Guest Name</th>
									<th>Guest Email</th>
									<th>Comment</th>
									<th>Approved</th>
									<th>Created at</th>
									<th>Action</th>
								</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
								<tr>
									<th>#</th>
									<th>ID</th>
									<th>Commenter</th>
									<th>Paste</th>
									<th>Guest Name</th>
									<th>Guest Email</th>	
									<th>Comment</th>								
									<th>Approved</th>								
									<th>Created at</th>
									<th>Action</th>
								</tr>
								</tfoot>
							</table>
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



  <div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-notify modal-info" role="document">
      <!--Content-->
      <div class="modal-content">
        <!--Header-->
        <div class="modal-header">
          <img class="rounded-circle img-fluid" id="commenter_avatar" src="">
          <p class="heading lead ml-2">
          	<span id="commenter"></span><br/>
          	<small class="text-muted" id="comment_created"></small>
          </p>

          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="white-text">&times;</span>
          </button>
        </div>

        <!--Body-->
        <div class="modal-body">
          <div id="comment"></div>
        </div>

        <!--Footer-->
        <div class="modal-footer">
          <a type="button" class="btn btn-outline-info waves-effect" data-dismiss="modal">Close</a>
        </div>
      </div>
      <!--/.Content-->
    </div>
  </div>

@stop