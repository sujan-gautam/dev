<?php $page = "Add Work"; ?>
<?php include_once('../template/admin/header.php'); ?>
<?php include_once('../template/admin/sidebar.php'); ?>
<?php include_once('../template/admin/navbar.php'); ?>
<?php 
    if(isset($_POST['submit'])){
        $valid              = 1;
        $work_title     = clean($_POST['work_title']);
        $work_desc          = clean($_POST['work_desc']);
        $work_url           = clean($_POST['work_url']);

        $w_created       = date('Y-m-d H:i:s');
        if(isset($_POST['work_status'])){
            $work_status        = clean($_POST['work_status']);

            if($work_status == 'on'){
                $work_status = 1;
            }else{
                $work_status = 0;
            }
        }else{
            $work_status = 0;
        }

        $statement = $conn->prepare('SELECT  * FROM work WHERE work_title = ?');
        $statement->execute(array($work_title));
        $total = $statement->rowCount();
        if( $total > 0 ) {
            $valid    = 0;
            $errors[] = 'This work is already registered.';
        }
        //check if fields empty - code starts
        if(empty($work_title)){
            $valid    = 0;
            $errors[] = 'Please Enter work Name';
        }
        if(empty($work_desc)){
            $valid    = 0;
            $errors[] = 'Please Enter work Description';
        }
        //check Service Image - code starts
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
        //check Service Image - code ends

        //If everything is OK - code starts
        if($valid == 1) {

            //Upload Service Image if available
            if($work_photo!='') {
                $work_photo_file = 'work-photo-'.time().'.'.$work_photo_ext;
                move_uploaded_file( $work_photo_tmp, '../storage/funwork/'.$work_photo_file );
            }else{
                $work_photo_file = "default.png";
            }

            //insert the data
            $insert = $conn->prepare("INSERT INTO work (work_title, work_url, work_desc, work_photo, work_status, w_created ) VALUES(?,?,?,?,?,?)");

            $insert->execute(array($work_title, $work_url, $work_desc, $work_photo_file, $work_status, $w_created));

            //insert the data - code ends
            $_SESSION['success'] = 'Work has been added successfully!';
            header('location: work.php');
            exit(0);
        }
    }
?>

<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Add</strong> Fun Works</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12 col-lg-4 d-flex">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Works info</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="mb-3">
                                        <label class="form-label" for="inputTitle">Work Title</label>
                                        <input type="text" class="form-control" id="inputTitle"
                                            placeholder="Enter Work Title" name="work_title">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="work_desc">Work Description</label>
                                        <textarea type="text" rows="4" class="form-control" id="work_desc"
                                            placeholder="Enter Work Description"
                                            name="work_desc"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputurl">Demo Url</label>
                                        <input type="text" class="form-control" id="inputurl" placeholder="Enter url"
                                            name="work_url">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 d-flex">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Work Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mt-4">
                                        <label for="flexSwitchCheckChecked">Enable / Disable</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked"
                                                checked="" name="work_status">
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
                            <h5 class="card-title mb-0">Work Image</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <img alt="funwork Image" src="../storage/funwork/default.png"
                                            class="rounded mx-auto d-block" width="100" height="100" id="workImg">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-primary">Choose Image
                                                <input type="file" class="file-upload" value="Upload"
                                                    name="work_image" onchange="previewFile(this);"
                                                    accept="image/*">
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

<script>
function previewFile(input) {
    const preview = document.getElementById('workImg');
    const file = input.files[0];
    const reader = new FileReader();

    reader.onload = function (e) {
        preview.src = e.target.result;
    };

    reader.readAsDataURL(file);
}
</script>

<?php include_once('../template/admin/footer.php'); ?>
