<?php include_once('../template/admin/header.php'); ?>
<?php include_once('../template/admin/sidebar.php'); ?>
<?php include_once('../template/admin/navbar.php'); ?>
<?php 
  // Check the id is valid or not
  if(!isset($_GET['edit']) OR !is_numeric($_GET['edit'])) {
        header('location: edit-work.php');
        exit;
      } else {

        $statement = $conn->prepare("SELECT * FROM work WHERE work_id=?");
        $statement->execute(array($_GET['edit']));
        $total  = $statement->rowCount();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if( $total == 0 ) {
          header('location: edit-work.php');
          exit;
        }
        else{
          $a = extract($result,EXTR_PREFIX_ALL, "edit");
        }
    }
?>
<?php 
	if(isset($_POST['submit'])){

		$valid 						= 1;
		$work_title 	= clean($_POST['work_title']);
		$work_desc 				= clean($_POST['work_desc']);
		$work_url 				= clean($_POST['work_url']);
		
		if(isset($_POST['work_status'])){
			$work_status 		= clean($_POST['work_status']);

			if($work_status == 'on'){
				$work_status = 1;
			}else{
				$work_status = 0;
			}
		}else{
			$work_status = 0;
		}

		//check if fields empty - code starts
		if(empty($work_title)){
		    $valid    = 0;
		    $errors[] = 'Please Enter work Title';
		}
		if(empty($work_desc)){
		    $valid    = 0;
		    $errors[] = 'Please Enter work Description';
		}
		//check if fields empty - code ends

		//check User Photo - code starts
  	$work_photo     = $_FILES['work_image']['name'];
  	$work_photo_tmp = $_FILES['work_image']['tmp_name'];
  	if($work_photo!='') {
    	$work_photo_ext = pathinfo( $work_photo, PATHINFO_EXTENSION );
    	$file_name = basename( $work_photo, '.' . $work_photo_ext );
	    	if( $work_photo_ext!='jpg' && $work_photo_ext!='png' && $work_photo_ext!='jpeg' && $work_photo_ext!='gif' ) {
	      	$valid = 0;
	      	$errors[]= 'You must have to upload jpg, jpeg, gif or png file<br>';
	    }
	  }
	  //check User Photo - code ends

	  //If everthing is OK - code starts
	if($valid == 1) {

	  	//Upload user Photo if available
			if($work_photo!='') {
		    $work_photo_file = '../work-photo-'.time().'.'.$work_photo_ext;
		    move_uploaded_file( $work_photo_tmp, '../storage/funwork/'.$work_photo_file );
			}else{
				$work_photo_file = $edit_work_photo;
			}

			//insert the data

			$insert = $conn->prepare("UPDATE work SET work_title = ?, work_desc = ?, work_url = ?, work_photo = ?, work_status = ?, w_updated = ? WHERE work_id = ?");

			$insert = $conn->prepare("UPDATE work SET work_title = ?, work_desc = ?, work_url = ?, work_photo = ?, work_status = ?, w_updated = NOW() WHERE work_id = ?");
$insert->execute(array($work_title, $work_desc, $work_url, $work_photo_file, $work_status, $edit_work_id));


			//insert the data - code ends

			$_SESSION['success'] = 'work has been updated successfully!';
		  header('location: work.php');
		  exit(0);
	  }
	}
?>
<main class="content">
	<div class="container-fluid p-0">
		<h1 class="h3 mb-3"><strong>Edit</strong> work</h1>
		<form action="" method="POST" enctype="multipart/form-data">
			<div class="row">
				<div class="col-12 col-lg-4 d-flex">
					<div class="card">
						<div class="card-header">
							<h5 class="card-title mb-0">work info</h5>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									<div class="mb-3">
                                    <label class="form-label" for="inputTitle">work Title</label>
										<input type="text" class="form-control" id="inputTitle" placeholder="Enter work Title" name="work_title" value="<?php echo clean($edit_work_title); ?>">
									</div>
									<div class="mb-3">
                                    <label class="form-label" for="work_desc">work Description</label>
                                        <input type="text" rows="4" class="form-control" id="work_desc" placeholder="Enter work Description"name="work_desc" value="<?php echo clean($edit_work_desc); ?>">

									</div>
									<div class="mb-3">
                                    <label class="form-label" for="inputurl"> Demo Url</label>
										<input type="text" class="form-control" id="inputurl" placeholder="Enter Url" name="work_url" value="<?php echo clean($edit_work_url); ?>">
									</div>
							</div>							
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-4 d-flex">
					<div class="card">
						<div class="card-header">
							<h5 class="card-title mb-0">work Status</h5>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									<div class="mt-4">
										<label for="flexSwitchCheckChecked">Enable / Disable</label>
										<div class="form-check form-switch mt-2">
											<input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" <?php if($edit_work_status == 1){echo 'checked=""';} ?> name="work_status">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-4 d-flex">
					<div class="card">
						<div class="card-header">
							<h5 class="card-title mb-0">work Image</h5>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									<div class="text-center">
										<img alt="work Image" src="../storage/work/<?php echo clean($edit_work_photo); ?>" class="rounded mx-auto d-block" width="100" height="100" id="workImg">
										<div class="mt-2">
											<button type="button" class="btn btn-primary">Choose Image
												<input type="file" class="file-upload edit-file" value="Upload" name="work_image" onchange="previewFile(this);" accept="image/*">
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-12">
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									<button type="submit" name="submit" class="btn btn-primary">Save changes</button>
								</div>
							</div>
						</div>
					</div>
				</div>				
			</div>
		</form>
	</div>
</main>
<?php include_once('../template/admin/footer.php'); ?>