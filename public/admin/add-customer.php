<?php
$page = "Add Customer";
include_once('../template/admin/header.php');
include_once('../template/admin/sidebar.php');
include_once('../template/admin/navbar.php');

if (isset($_POST['submit'])) {
    $valid = 1;

    $name = clean($_POST['name']);
    $address = clean($_POST['address']);
    $phone_num = clean($_POST['phone_num']);
    $fb_id = clean($_POST['fb_id']);
    $service_status = isset($_POST['service_status']) ? 'active' : 'pending';

    // ... rest of your code
    

    // Domain Details
    $domain_name = clean($_POST['domain_name']);
    $domain_cost = clean($_POST['domain_cost']);
    $domain_provider = clean($_POST['domain_provider']);
    $domain_renewal_date = clean($_POST['domain_renewal_date']);
    $domain_renewal_charge = clean($_POST['domain_renewal_charge']);
    $domain_email = clean($_POST['domain_email']);
    $domain_password = clean($_POST['domain_password']);

    // Hosting Details
    $hosting_name = clean($_POST['hosting_name']);
    $hosting_cost = clean($_POST['hosting_cost']);
    $hosting_renewal_date = clean($_POST['hosting_renewal_date']);
    $hosting_renewal_charge = clean($_POST['hosting_renewal_charge']);
    $hosting_email = clean($_POST['hosting_email']);
    $hosting_password = clean($_POST['hosting_password']);

    $service_status = isset($_POST['service_status']) ? 'active' : 'pending';
    $advance_amt = clean($_POST['advance_amt']);
    $remaining_amt = clean($_POST['remaining_amt']);
    $project_submitted_date = clean($_POST['project_submitted_date']);
    $maintenance_cost = clean($_POST['maintenance_cost']);

    // Check if fields are empty
    if (empty($name) || empty($address) || empty($phone_num) || empty($project_submitted_date)) {
        $valid = 0;
        $errors[] = 'Please fill in all required fields.';
    }

    // If everything is OK - code starts
    if ($valid == 1) {
        // Insert the data into customers table
        $insertCustomer = $conn->prepare("INSERT INTO customers (name, address, phone_num, fb_id, service_status, advance_amt, remaining_amt, project_submitted_date, maintenance_cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insertCustomer->execute(array($name, $address, $phone_num, $fb_id, $service_status, $advance_amt, $remaining_amt, $project_submitted_date, $maintenance_cost));

        // Get the customer ID
        $customer_id = $conn->lastInsertId();

        // Insert Domain Details into domains table
        $insertDomain = $conn->prepare("INSERT INTO domains (customer_id, domain_name, domain_cost, domain_provider, renewal_date, renewal_charge, domain_email, domain_password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insertDomain->execute(array($customer_id, $domain_name, $domain_cost, $domain_provider, $domain_renewal_date, $domain_renewal_charge, $domain_email, $domain_password));

        // Insert Hosting Details into hosting table
        $insertHosting = $conn->prepare("INSERT INTO hosting (customer_id, hosting_name, hosting_cost, renewal_date, renewal_charge, hosting_email, hosting_password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertHosting->execute(array($customer_id, $hosting_name, $hosting_cost, $hosting_renewal_date, $hosting_renewal_charge, $hosting_email, $hosting_password));

        // Update the total_amt in customers table
        $updateTotalAmt = $conn->prepare("UPDATE customers SET total_amt = advance_amt + remaining_amt WHERE customer_id = ?");
        $updateTotalAmt->execute(array($customer_id));

        // Insert the data - code ends
        $_SESSION['success'] = 'Customer has been added successfully!';
        header('location: customers.php');
        exit(0);
    }
}

?>

<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Add</strong> Customer</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12 col-lg-4 d-flex">
                    <!-- Customer Info Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Customer Info</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Customer Info Fields -->
                                    <div class="mb-3">
                                        <label class="form-label" for="inputName">Name</label>
                                        <input type="text" class="form-control" id="inputName" placeholder="Enter Name" name="name">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputAddress">Address</label>
                                        <input type="text" class="form-control" id="inputAddress" placeholder="Enter Address" name="address">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputPhone">Phone Number</label>
                                        <input type="text" class="form-control" id="inputPhone" placeholder="Enter Phone Number" name="phone_num">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputFB">Facebook ID</label>
                                        <input type="text" class="form-control" id="inputFB" placeholder="Enter Facebook ID" name="fb_id">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 d-flex">
                    <!-- Domain Details Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Domain Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Domain Details Fields -->
                                    <div class="mb-3">
                                        <label class="form-label" for="inputDomainName">Domain Name</label>
                                        <input type="text" class="form-control" id="inputDomainName" placeholder="Enter Domain Name" name="domain_name">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputDomainCost">Domain Cost</label>
                                        <input type="text" class="form-control" id="inputDomainCost" placeholder="Enter Domain Cost" name="domain_cost">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputDomainProvider">Domain Provider</label>
                                        <input type="text" class="form-control" id="inputDomainProvider" placeholder="Enter Domain Provider" name="domain_provider">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputDomainRenewalDate">Domain Renewal Date</label>
                                        <input type="date" class="form-control" id="inputDomainRenewalDate" name="domain_renewal_date">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputDomainRenewalCharge">Domain Renewal Charge</label>
                                        <input type="text" class="form-control" id="inputDomainRenewalCharge" placeholder="Enter Domain Renewal Charge" name="domain_renewal_charge">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputDomainEmail">Domain Email</label>
                                        <input type="text" class="form-control" id="inputDomainEmail" placeholder="Enter Domain Email" name="domain_email">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputDomainPassword">Domain Password</label>
                                        <input type="password" class="form-control" id="inputDomainPassword" placeholder="Enter Domain Password" name="domain_password">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 d-flex">
                    <!-- Hosting Details Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Hosting Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Hosting Details Fields -->
                                    <div class="mb-3">
                                        <label class="form-label" for="inputHostingName">Hosting Name</label>
                                        <input type="text" class="form-control" id="inputHostingName" placeholder="Enter Hosting Name" name="hosting_name">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputHostingCost">Hosting Cost</label>
                                        <input type="text" class="form-control" id="inputHostingCost" placeholder="Enter Hosting Cost" name="hosting_cost">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputHostingRenewalDate">Hosting Renewal Date</label>
                                        <input type="date" class="form-control" id="inputHostingRenewalDate" name="hosting_renewal_date">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputHostingRenewalCharge">Hosting Renewal Charge</label>
                                        <input type="text" class="form-control" id="inputHostingRenewalCharge" placeholder="Enter Hosting Renewal Charge" name="hosting_renewal_charge">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputHostingEmail">Hosting Email</label>
                                        <input type="text" class="form-control" id="inputHostingEmail" placeholder="Enter Hosting Email" name="hosting_email">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputHostingPassword">Hosting Password</label>
                                        <input type="password" class="form-control" id="inputHostingPassword" placeholder="Enter Hosting Password" name="hosting_password">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 d-flex">
                    <!-- Customer Status Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Customer Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Customer Status Fields -->
                                    <div class="mt-4">
                                        <label for="flexSwitchCheckChecked">Enable / Disable</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked="" name="service_status">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-12">
                    <!-- Additional Customer Info Card -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Additional Customer Info Fields -->
                                    <div class="mb-3">
                                        <label class="form-label" for="inputAdvance">Advance Amount</label>
                                        <input type="text" class="form-control" id="inputAdvance" placeholder="Enter Advance Amount" name="advance_amt">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputRemaining">Remaining Amount</label>
                                        <input type="text" class="form-control" id="inputRemaining" placeholder="Enter Remaining Amount" name="remaining_amt">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputSubmittedDate">Submitted Date</label>
                                        <input type="date" class="form-control" id="inputSubmittedDate" name="project_submitted_date">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="inputMaintenanceCost">Maintenance Cost</label>
                                        <input type="text" class="form-control" id="inputMaintenanceCost" placeholder="Enter Maintenance Cost" name="maintenance_cost">
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
