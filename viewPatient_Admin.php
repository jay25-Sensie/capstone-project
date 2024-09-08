<?php
session_start();

    include("connection.php");
    include("function.php");

    $user_data = check_login($con);

    $pid = $name = $lastname = $address = $age = $phone_number = $gender = $status = '';

    // search query
    $search_query = '';
    if (isset($_GET['search'])) {
        $search = sanitize_input($con, $_GET['search']);
        $search_query = "WHERE pid LIKE '%$search%' OR name LIKE '%$search%'";
    }
    
    // update status operations
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update patient status
        if (isset($_POST['update_status'])) {
            $pid = intval($_POST['pid']); // Ensure pid is integer
            $status = sanitize_input($con, $_POST['status']);
    
            $query = "UPDATE patient_records SET status = '$status' WHERE pid = $pid";
            mysqli_query($con, $query);
            header("Location: patientRecords_Doctor.php");
            exit();
        }
    }
    
    // selecting all patients from the database with optional search filtering
    $query = "SELECT * FROM patient_records $search_query";
    $result = mysqli_query($con, $query);
    $patients = mysqli_fetch_all($result, MYSQLI_ASSOC);




// Ensure PID is passed and is a valid integer
if (isset($_GET['pid']) && ctype_digit($_GET['pid'])) {
    $pid = intval($_GET['pid']);

    // Fetch patient details based on PID
    $query = "SELECT * FROM patient_records WHERE pid = $pid LIMIT 1";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $patient = mysqli_fetch_assoc($result);
    } else {
        echo "No record found for PID: $pid";
        exit();
    }
} else {
    echo "Invalid PID.";
    exit();
}

// Retrieve medical records associated with the PID
$medicalQuery = "SELECT * FROM medical_records WHERE pid = '$pid'";
$medicalResult = mysqli_query($con, $medicalQuery);

// Retrieve prescription data associated with the PID
$prescriptionQuery = "SELECT * FROM prescriptions_data WHERE pid = '$pid'";
$prescriptionResult = mysqli_query($con, $prescriptionQuery);

// Check for SQL errors
if (!$medicalResult || !$prescriptionResult) {
  echo "<div class='alert alert-danger' style='text-align: center;'>Error retrieving data.</div>";
  exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>View Patient Admin</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">

  <style>
    .content-wrapper{
            padding: 3%;
        }
  </style>



</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src=".//img/logo.png" alt="AdminLTELogo" height="200" width="200">
    <h2>Loading...</h2>
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index_home.php" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline" action="patientRecords_Doctor.php" method="get">
              <div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" type="search" name="search" placeholder="Search by PID or Name" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-navbar" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
             </div>
          </form>
        </div>
      </li>

     
      
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">15 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src=".//img/logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-4" style="opacity: 1">
      <span class="brand-text font-weight-light">WBHR_MS</span>
    </a>

    <!-- Sidebar -->

    <div class="sidebar">
     

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
            <a href="index3.html" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="patientRecords.php" class="nav-link active">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Patient Records</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="Admin_Prescription.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Prescription</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="medical_Records.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Medical Records</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="index3.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Reports</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="setCalendar.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Set Calendar</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="viewCalendar.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Calendar</p>
                </a>
              </li>
            </ul>
          </li>                 
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>


  <div class="content-wrapper" style="padding: 3%;">
        <h2>Patient Record for <?php echo htmlspecialchars($patient['name']); ?></h2>
        <table class="table table-bordered">
            <tr><th>PID</th><td><?php echo htmlspecialchars($patient['pid']); ?></td></tr>
            <tr><th>Name</th><td><?php echo htmlspecialchars($patient['name']); ?></td></tr>
            <tr><th>Lastname</th><td><?php echo htmlspecialchars($patient['lastname']); ?></td></tr>
            <tr><th>Address</th><td><?php echo htmlspecialchars($patient['address']); ?></td></tr>
            <tr><th>Age</th><td><?php echo htmlspecialchars($patient['age']); ?></td></tr>
            <tr><th>Birthday</th><td><?php echo htmlspecialchars($patient['birthday']); ?></td></tr>
            <tr><th>Phone Number</th><td><?php echo htmlspecialchars($patient['phone_number']); ?></td></tr>
            <tr><th>Gender</th><td><?php echo htmlspecialchars($patient['gender']); ?></td></tr>
            <tr><th>Status</th><td><?php echo htmlspecialchars($patient['status']); ?></td></tr>
        </table>
    </div>


    <!-- Display Prescription Data -->
    <div class="content-wrapper">
      <h3>Prescriptions</h3>
              <?php if (mysqli_num_rows($prescriptionResult) > 0): ?>
                  <table class="table table-bordered">
                      <thead>
                          <tr>
                              <th>PID</th>
                              <th>Medicine</th>
                              <th>Frequency</th>
                              <th>Time to take</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php while ($prescription = mysqli_fetch_assoc($prescriptionResult)): ?>
                              <tr>
                                  <td><?php echo htmlspecialchars($prescription['pid']); ?></td>
                                  <td><?php echo htmlspecialchars($prescription['medicine_name']); ?></td>
                                  <td><?php echo htmlspecialchars($prescription['frequency']); ?></td>
                                  <td><?php echo htmlspecialchars($prescription['time_to_take']); ?></td>
                              </tr>
                          <?php endwhile; ?>
                      </tbody>
                  </table>
              <?php else: ?>
                  <div class='alert alert-info' style='text-align: center;'>No prescriptions found for this patient.</div>
              <?php endif; ?>

      <!-- Display Medical Records -->
      <h3>Medical Records</h3>
              <?php if (mysqli_num_rows($medicalResult) > 0): ?>
                  <table class="table table-bordered">
                      <thead>
                          <tr>
                              <th>File</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php while ($record = mysqli_fetch_assoc($medicalResult)): ?>
                              <tr>
                                  <td>
                                      <a href="<?php echo htmlspecialchars($record['file_path']); ?>" target="_blank">
                                          <?php echo basename(htmlspecialchars($record['file_path'])); ?>
                                      </a>
                                  </td>
                              </tr>
                          <?php endwhile; ?>
                      </tbody>
                  </table>
              <?php else: ?>
                  <div class='alert alert-info' style='text-align: center;'>No medical records found for this patient.</div>
              <?php endif; ?>
          </div>
      </div>
    </div>



    





  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>
</body>
</html>
