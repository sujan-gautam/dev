<?php
$page = "Visitors";
include_once('../template/admin/header.php');
include_once('../template/admin/sidebar.php');
include_once('../template/admin/navbar.php');

// Include your database connection file here
// include_once('../path/to/your/database/connection.php');

// Make sure $conn is a valid PDO connection
if (!isset($conn)) {
    die("Database connection is not established.");
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    // Add deletion logic if needed
}

// Fetch visitor data from the database
$statement = $conn->prepare('SELECT * FROM visitor_data ORDER BY id DESC');
$statement->execute();
$visitors = $statement->fetchAll(PDO::FETCH_ASSOC);

?>
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">


<main class="content">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-md-12">
                <h1 class="h3 mb-3"><strong>All</strong> Visitors</h1>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table dataTable table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>IP Address</th>
                            <th>Device Type</th>
                            <th>Browser</th>
                            <th>Date and Time</th>
                            <th>Action</th> <!-- Add this column for the View button -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($visitors as $visitor) { ?>
                            <tr>
                                <td><?php echo $visitor['id']; ?></td>
                                <td><?php echo $visitor['ip']; ?></td>
                                <td><?php echo $visitor['device_type']; ?></td>
                                <td><?php echo $visitor['browser']; ?></td>
                                <td><?php echo $visitor['timestamp']; ?></td>
                                <td>
                                    <a href="#" class="view-details" data-ip="<?php echo $visitor['ip']; ?>">Trace</a>
                                    <a href="#" class="delete-visitor" data-id="<?php echo $visitor['id']; ?>">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Add a modal for displaying IP trace details -->
<div class="modal fade" id="ipTraceModal" tabindex="-1" role="dialog" aria-labelledby="ipTraceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ipTraceModalLabel">IP Trace Details</h5>
                <button type="button" class="close" data-dismiss="modal" id="ipTraceModalCloseBtn"></button>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="ipTraceDetails">
                <!-- IP trace details will be displayed here -->
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="ipTraceModalCloseBtn">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include_once('../template/admin/footer.php'); ?>

<!-- ... Your existing HTML code ... -->
<script>
   
  // Add JavaScript code to handle the View and Delete button clicks
$(document).ready(function () {
    // Use event delegation for dynamically added elements (view-details button)
    $(".table").on("click", ".view-details", function () {
        var ip = $(this).data("ip");

        // Use AJAX to fetch IP trace details
        $.ajax({
            url: "https://ipapi.co/" + ip + "/json/",
            dataType: "json",
            success: function (data) {
                // Extract specific fields for display
                var ipDetails = {
                    "IP Address": data.ip,
                    "City": data.city,
                    "Region": data.region,
                    "Country": data.country_name,
                    "European Union": data.in_eu ? "Yes" : "No",
                    "Latitude / Longitude": data.latitude + " / " + data.longitude,
                    "Time Zone": data.timezone,
                    "Calling Code": data.country_calling_code,
                    "Currency": data.currency,
                    "Languages": data.languages,
                    "ASN": data.asn,
                    "Org": data.org
                };

                // Generate HTML for displaying IP trace details in the modal
                var detailsHtml = "<dl>";
                for (var key in ipDetails) {
                    detailsHtml += "<dt>" + key + "</dt><dd>" + ipDetails[key] + "</dd>";
                }
                detailsHtml += "</dl>";

                // Display IP trace details in the modal
                $("#ipTraceDetails").html(detailsHtml);
                $("#ipTraceModal").modal("show");
            },
            error: function () {
                alert("Error fetching IP trace details");
            }
        });
    });

    // Use event delegation for dynamically added elements (delete-visitor button)
    $(".table").on("click", ".delete-visitor", function () {
        var visitorId = $(this).data("id");

        // Use AJAX to send a request to delete the visitor data
        $.ajax({
            url: "delete_visitor.php",
            method: "POST",
            data: { id: visitorId },
            success: function (response) {
                // Optionally, you can handle the response here
                // For example, reload the page to reflect the changes
                location.reload();
            },
            error: function () {
                alert("Error deleting visitor");
            }
        });
    });

    // Manually hide the modal when the "Close" button is clicked
    $("#ipTraceModalCloseBtn").on("click", function () {
        $("#ipTraceModal").modal("hide");
    });
});


</script>
