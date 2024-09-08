<?php
session_start();
include("connection.php");
include("function.php");


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pid'])) {
    $pid = $_POST['pid'];

    // Fetch patient details
    $patientQuery = "SELECT * FROM patient_records WHERE pid = '$pid'";
    $patientResult = mysqli_query($con, $patientQuery);
    $patient = mysqli_fetch_assoc($patientResult);

    // Fetch prescriptions
    $prescriptionQuery = "SELECT * FROM prescriptions_data WHERE pid = '$pid'";
    $prescriptionResult = mysqli_query($con, $prescriptionQuery);
    $prescriptions = mysqli_fetch_all($prescriptionResult, MYSQLI_ASSOC);

    // Fetch medical records
    $medicalRecordsQuery = "SELECT * FROM medical_records WHERE pid = '$pid'";
    $medicalRecordsResult = mysqli_query($con, $medicalRecordsQuery);
    $medicalRecords = mysqli_fetch_all($medicalRecordsResult, MYSQLI_ASSOC);

    $noPatientFound = !$patient; // Set flag if no patient found
} else {
    $pid = null; // No PID provided
    $noPatientFound = false; // No alert needed
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Generate Report Admin</title>

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
        padding-left: 3%;
        padding-right: 3%;
    }
  </style>


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
            <a href="viewCalendar.php" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="patientRecords.php" class="nav-link">
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
                <a href="genReports.php" class="nav-link active">
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


  <div class="content-wrapper">
    <div class="wrapper">
        <?php if ($pid === null): ?>
            <!-- Form to enter PID -->
            <div class="container mt-4">
                <h2>Generate Patient Report</h2>
                <form method="POST" action="genReports.php">
                    <div class="form-group">
                        <label for="pid">Enter Patient PID:</label>
                        <input type="text" class="form-control" name="pid" id="pid" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </form>
            </div>
                <?php else: ?>
            <!-- Display Patient Report -->
                <div class="container mt-4">
                    <?php if ($patient): ?>
                        <h2>Generate Reports for <?php echo htmlspecialchars($patient['name']); ?></h2>
                        
                        <!-- Patient Information -->
                        <h4>Patient Information</h4>
                        <table class="table table-bordered">
                            <tr><th>PID</th><td><?php echo htmlspecialchars($patient['pid']); ?></td></tr>
                            <tr><th>Name</th><td><?php echo htmlspecialchars($patient['name']); ?> <?php echo htmlspecialchars($patient['lastname']); ?></td></tr>
                            <tr><th>Address</th><td><?php echo htmlspecialchars($patient['address']); ?></td></tr>
                            <tr><th>Phone Number</th><td><?php echo htmlspecialchars($patient['phone_number']); ?></td></tr>
                            <tr><th>Gender</th><td><?php echo htmlspecialchars($patient['gender']); ?></td></tr>
                        </table>

                        <!-- Prescription Information -->
                        <h4>Prescription Information</h4>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Medicine Name</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Time to Take</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($prescriptions)): ?>
                                    <?php foreach ($prescriptions as $prescription): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($prescription['medicine_name']); ?></td>
                                            <td><?php echo htmlspecialchars($prescription['dosage']); ?></td>
                                            <td><?php echo htmlspecialchars($prescription['frequency']); ?></td>
                                            <td><?php echo htmlspecialchars($prescription['time_to_take']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4">No prescriptions found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- Medical Records -->
                        <h4>Medical Records</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>File Path</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($medicalRecords)): ?>
                                    <?php foreach ($medicalRecords as $record): ?>
                                        <tr>
                                            <td><a href="<?php echo htmlspecialchars($record['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($record['file_path']); ?></a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td>No medical records found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- Print Button -->
                        <button onclick="window.print()" class="btn btn-primary">Print Report</button>

                    <?php else: ?>
                        <!-- Alert Message -->
                        <div class="alert alert-warning mt-4" id="alert">No patient found with the provided PID.</div>
                    <?php endif; ?>
                </div>
        <?php endif; ?>

    </div>

  </div>
    

  

 <!--put calendar view here with inline-->

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

