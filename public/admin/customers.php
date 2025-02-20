<?php
$page = "Customers";
include_once('../template/admin/header.php');
include_once('../template/admin/sidebar.php');
include_once('../template/admin/navbar.php');

// Fetch data from the database
$query = "SELECT 
            customers.customer_id,
            customers.name,
            customers.address,
            customers.phone_num,
            customers.fb_id,
            customers.total_amt,
            customers.service_status,
            customers.advance_amt,
            customers.remaining_amt,
            customers.project_submitted_date,
            customers.maintenance_cost,
            domains.domain_name,
            domains.domain_cost,
            domains.domain_provider,
            domains.renewal_date AS domain_renewal_date,
            domains.renewal_charge AS domain_renewal_charge,
            domains.domain_email,
            domains.domain_password,
            hosting.hosting_name,
            hosting.hosting_cost,
            hosting.renewal_date AS hosting_renewal_date,
            hosting.renewal_charge AS hosting_renewal_charge,
            hosting.hosting_email,
            hosting.hosting_password
        FROM customers
        LEFT JOIN domains ON customers.customer_id = domains.customer_id
        LEFT JOIN hosting ON customers.customer_id = hosting.customer_id";

$statement = $conn->prepare($query);
$statement->execute();
$customers = $statement->fetchAll(PDO::FETCH_ASSOC);
// Calculate Total Income
$totalIncome = 0;
foreach ($customers as $customer) {
    $totalIncome += (
        $customer['advance_amt'] +
        $customer['remaining_amt'] -
        $customer['domain_cost'] -
        $customer['hosting_cost'] +
        $customer['maintenance_cost'] 
        // $customer['domain_renewal_charge'] -
        // $customer['hosting_renewal_charge']
    );
}
?>

<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3">Customers Details</h1>

          <!-- Display Total Income -->
          <div class="row mb-3">
            <div class="col-12">
                <div class="btn btn-pill btn-primary float-right" role="alert">
                    Total Income: Rs <?php echo number_format( $totalIncome, 2); ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 text-md-end">
				<a href="add-customer.php" class="btn btn-pill btn-primary float-right"><i class="align-middle"
						data-feather="plus"></i> Add Customers</a>
				<a href="luckydetails.php" class="btn btn-pill btn-primary float-right"><i class="align-middle"
						data-feather="plus"></i> Luckywheel Details</a>
			</div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Customer List</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Customer ID</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Phone Number</th>
                                        <th>Facebook ID</th>
                                        <th>Total Amount</th>
                                        <th>Service Status</th>
                                        <th>Advance Amount</th>
                                        <th>Remaining Amount</th>
                                        <th>Project Submitted Date</th>
                                        <th>Maintenance Cost</th>
                                        <th>Domain Name</th>
                                        <th>Domain Cost</th>
                                        <th>Domain Provider</th>
                                        <th>Domain Renewal Date</th>
                                        <th>Domain Renewal Charge</th>
                                        <th>Domain Email</th>
                                        <th>Domain Password</th>
                                        <th>Hosting Provider</th>
                                        <th>Hosting Cost</th>
                                        <th>Hosting Renewal Date</th>
                                        <th>Hosting Renewal Charge</th>
                                        <th>Hosting Email</th>
                                        <th>Hosting Password</th>
                                        <th>Edit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Loop through customers and display their information
                                    foreach ($customers as $customer) {
                                        echo "<tr>";
                                        echo "<td>{$customer['customer_id']}</td>";
                                        echo "<td>{$customer['name']}</td>";
                                        echo "<td>{$customer['address']}</td>";
                                        echo "<td>{$customer['phone_num']}</td>";
                                        echo "<td>{$customer['fb_id']}</td>";
                                        echo "<td>{$customer['total_amt']}</td>";
                                        echo "<td>{$customer['service_status']}</td>";
                                        echo "<td>{$customer['advance_amt']}</td>";
                                        echo "<td>{$customer['remaining_amt']}</td>";
                                        echo "<td>{$customer['project_submitted_date']}</td>";
                                        echo "<td>{$customer['maintenance_cost']}</td>";
                                        echo "<td>{$customer['domain_name']}</td>";
                                        echo "<td>{$customer['domain_cost']}</td>";
                                        echo "<td>{$customer['domain_provider']}</td>";
                                        echo "<td>{$customer['domain_renewal_date']}</td>";
                                        echo "<td>{$customer['domain_renewal_charge']}</td>";
                                        echo "<td>{$customer['domain_email']}</td>";
                                        echo "<td>{$customer['domain_password']}</td>";
                                        echo "<td>{$customer['hosting_name']}</td>";
                                        echo "<td>{$customer['hosting_cost']}</td>";
                                        echo "<td>{$customer['hosting_renewal_date']}</td>";
                                        echo "<td>{$customer['hosting_renewal_charge']}</td>";
                                        echo "<td>{$customer['hosting_email']}</td>";
                                        echo "<td>{$customer['hosting_password']}</td>";
                                        echo "<td><a href='edit-customer.php?id={$customer['customer_id']}' class='btn btn-primary'>Edit</a></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once('../template/admin/footer.php'); ?>
