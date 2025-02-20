<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/brands.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- Add your other stylesheets here -->

    <title>Sidebar</title>
</head>

<body>
    <nav id="sidebar" class="sidebar js-sidebar">
        <div class="sidebar-content js-simplebar">
            <a class="sidebar-brand" href="dashboard.php">
                <span class="align-middle"><?php echo clean($site_name); ?></span>
            </a>

            <?php $page = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1); ?>
            <ul class="sidebar-nav">
                <li class="sidebar-header">
                    <!-- Sidebar header content -->
                </li>

                <!-- Dashboard -->
                <li class="sidebar-item <?php if ($page == "dashboard.php") echo "active"; ?>">
                    <a class="sidebar-link" href="dashboard.php">
                        <i class="fas fa-sliders"></i> <span class="align-middle">Dashboard</span>
                    </a>
                </li>

                <!-- Custom Menu Items -->
                <li class="sidebar-item <?php if ($page == "aboutme.php") echo "active"; ?>">
                    <a class="sidebar-link" href="about.php">
                        <i class="fas fa-user"></i> <span class="align-middle">About Me</span>
                    </a>
                </li>

                <!-- Education -->
                <li class="sidebar-item <?php if ($page == "education.php") echo "active"; ?>">
                    <a class="sidebar-link" href="education.php">
                        <i class="fas fa-book"></i> <span class="align-middle">Education</span>
                    </a>
                </li>

                <!-- Portfolio -->
                <li class="sidebar-item <?php if ($page == "portifolio.php") echo "active"; ?>">
                    <a class="sidebar-link" href="portifolio.php">
                        <i class="fas fa-briefcase"></i> <span class="align-middle">Portfolio</span>
                    </a>
                </li>

                <!-- Fun Work -->
                <li class="sidebar-item <?php if ($page == "work.php") echo "active"; ?>">
                    <a class="sidebar-link" href="work.php">
                        <i class="fas fa-briefcase"></i> <span class="align-middle">Fun Work</span>
                    </a>
                </li>

                <!-- Story -->
                <li class="sidebar-item <?php if ($page == "index.php") echo "active"; ?>">
                    <a class="sidebar-link" href="story.php">
                        <i class="fas fa-book-open"></i> <span class="align-middle">Story</span>
                    </a>
                </li>

                <!-- Popups -->
                <li class="sidebar-item <?php if ($page == "index.php") echo "active"; ?>">
                    <a class="sidebar-link" href="popups.php">
                        <i class="fas fa-pager"></i> <span class="align-middle">Popups</span>
                    </a>
                </li>

                <!-- Contact -->
                <li class="sidebar-item <?php if ($page == "contact.php") echo "active"; ?>">
                    <a class="sidebar-link" href="contact.php">
					<i class="fa-solid fa-address-book"></i> <span class="align-middle">Contact</span>
                    </a>
                </li>

                <!-- Visitors -->
                <li class="sidebar-item <?php if ($page == "index.php") echo "active"; ?>">
                    <a class="sidebar-link" href="visitors.php">
                        <i class="fas fa-users-rays"></i> <span class="align-middle">Visitors</span>
                    </a>
                </li>

                <!-- Users -->
                <li class="sidebar-item <?php if ($page == "users.php") echo "active"; ?>">
                    <a class="sidebar-link" href="users.php">
                        <i class="fas fa-users"></i> <span class="align-middle">Users</span>
                    </a>
                </li>

                <!-- Customers -->
                <li class="sidebar-item <?php if ($page == "index.php") echo "active"; ?>">
                    <a class="sidebar-link" href="customers.php">
                        <i class="fas fa-users"></i> <span class="align-middle">Customers</span>
                    </a>
                </li>

                <!-- Settings -->
                <li class="sidebar-item <?php if ($page == "settings.php") echo "active"; ?>">
                    <a class="sidebar-link" href="settings.php">
					<i class="fa-solid fa-gear"></i> <span class="align-middle">Settings</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Your page content goes here -->

    <!-- Add your scripts here -->
</body>

</html>
