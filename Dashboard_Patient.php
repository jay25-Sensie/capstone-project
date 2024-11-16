<?php
session_start();
include("connection.php");
include("function.php");

// Ensure the user is logged in by checking the session
if (!isset($_SESSION['pid'])) {
    header("Location: Patient_login.php");
    exit();
}

// Get the patient's PID from session
$pid = $_SESSION['pid'];

// Query to check if the user is a patient
$query = "SELECT role FROM users WHERE username = '$pid' LIMIT 1";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

// Check if the user is a patient, otherwise redirect
if ($user['role'] !== 'patient') {
    header("Location: Signin_Patient.php");
    exit();
}

// Your dashboard code here

// Sample logic to display the calendar or something else
function isClosed($date, $selected_dates) {
    return in_array($date, $selected_dates); // Check if $date exists in $selected_dates array
}

// Select selected dates from session
$selected_dates = isset($_SESSION['selected_dates']) ? $_SESSION['selected_dates'] : [];

// Get the current month and year
$current_month = date('n');
$current_year = date('Y');

// Get the name of the current month
$month_name = date('F');

// Number of days in the current month
$num_days_in_month = date('t', mktime(0, 0, 0, $current_month, 1, $current_year));

// Start day of the week for the first day of the month
$start_day_of_week = date('N', mktime(0, 0, 0, $current_month, 1, $current_year));

// Additional dashboard logic goes here
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Patient Dashboard</title>


<!-- Font Awesome (local) -->
<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">

<!-- Tempus Dominus Bootstrap 4 (local) -->
<link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">

<!-- iCheck Bootstrap (local) -->
<link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">

<!-- JQVMap (local) -->
<link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">

<!-- AdminLTE Theme (local) -->
<link rel="stylesheet" href="dist/css/adminlte.min.css">

<!-- OverlayScrollbars (local) -->
<link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">

<!-- Daterange Picker (local) -->
<link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">

<!-- Summernote (local) -->
<link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">

  <style>
        .calendar {
            margin-top: 20px;
        }
        .calendar th {
            background-color: #007bff;
            color: #fff;
        }
        .closed {
            background-color: #f8d7da; 
        }
        .nav-treeview .nav-item {
            padding-left: 3%;
        }
        .mt-4{
          text-align: right;
          padding-right: 10%;
        }       
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src=".//img/logo.png" alt="image Logo" height="200" width="200">
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
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button" >
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link" onclick="confirmLogout(event)">
            <i class="nav-icon fas fa-sign-out-alt"></i> Log out
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src=".//img/logo.png" alt="image Logo" class="brand-image img-circle elevation-4" style="opacity: 1">
      <span class="brand-text font-weight-light">IMSClinic_HRMS</span>
    </a>

    <!-- Sidebar -->

    <div class="sidebar">
     

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Dashboard menu item -->
          <li class="nav-item">
            <a href="Dashboard_Patient.php" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link ">
              <i class="nav-icon fas fa-folder"></i>
              <p>
                Services
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="patientrecordData.php" class="nav-link">
                  <i class="nav-icon fas fa-user"></i>
                  <p>Personal Record</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="patient_Prescription.php" class="nav-link">
                  <i class="nav-icon fas fa-prescription"></i>
                  <p>Prescription</p>
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
  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="container" style="padding-left: 10%;">
          <h2 class="mt-4">Calendar View</h2>
          <h3 class="month"><?php echo $month_name . " " . $current_year; ?></h3>
          <div class="calendar">
              <table class="table table-bordered" style="width: 90%;">
                  <thead>
                      <tr>
                          <th scope="col">Mon</th>
                          <th scope="col">Tue</th>
                          <th scope="col">Wed</th>
                          <th scope="col">Thu</th>
                          <th scope="col">Fri</th>
                          <th scope="col">Sat</th>
                          <th scope="col">Sun</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr>
                          <?php
                          $day_count = 1;
                          $current_date = 1;

                          // Loop through each day of the week
                          for ($i = 1; $i <= 7; $i++) {
                              echo "<td>";

                              // Checking valid day to display
                              if ($day_count >= $start_day_of_week && $current_date <= $num_days_in_month) {
                                  $date = sprintf('%04d-%02d-%02d', $current_year, $current_month, $current_date);
                                  echo $current_date;

                                  // Checking if the date is in the selected closed days array
                                  if (isClosed($date, $selected_dates)) {
                                      echo '<br><span class="badge badge-danger">Closed</span>';
                                  }

                                  $current_date++;
                              }

                              echo "</td>";
                              $day_count++;
                          }

                          echo "</tr>";

                          // Continue displaying calendar
                          while ($current_date <= $num_days_in_month) {
                              echo "<tr>";

                              for ($i = 0; $i < 7; $i++) {
                                  echo "<td>";

                                  if ($current_date <= $num_days_in_month) {
                                      $date = sprintf('%04d-%02d-%02d', $current_year, $current_month, $current_date);
                                      echo $current_date;

                                      if (isClosed($date, $selected_dates)) {
                                          echo '<br><span class="badge badge-danger">Closed</span>';
                                      }

                                      $current_date++;
                                  }

                                  echo "</td>";
                              }

                              echo "</tr>";
                          }
                          ?>
                      </tbody>
                  </table>
              </div>
          </div>
  </div>
  <!-- Control Sidebar and Footer skipped for brevity -->
</div>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery (local) -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 (local) -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 (local) -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS (local) -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline (local) -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap (local) -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart (local) -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker (local) -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 (local) -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote (local) -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars (local) -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App (local) -->
<script src="dist/js/adminlte.js"></script>
<script src="../wbhr_ms/logout.js"></script>

</body>
</html>