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


    function isClosed($date, $selected_dates) {
      return in_array($date, $selected_dates); // Check if $date exists in $selected_dates array
    }
    
    $selected_dates = isset($_SESSION['selected_dates']) ? $_SESSION['selected_dates'] : [];
    
    // Generating a simple calendar
    $current_month = date('n'); // to get current month
    $current_year = date('Y'); // current year
    
    // Number of days in the current month
    $num_days_in_month = date('t', mktime(0, 0, 0, $current_month, 1, $current_year));
    
    // Starting day of the week for the first day of the month
    $start_day_of_week = date('N', mktime(0, 0, 0, $current_month, 1, $current_year));
    
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Doctors Dashboard</title>

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
    .calendar {
            margin-top: 20px;
        }
        .calendar th,
        .calendar td {
            text-align: center;
            width: 200px;
            height: 40px;
        }
        .calendar th {
            background-color: #007bff;
            color: #fff;
        }
        .closed {
            background-color: #f8d7da; 
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
                </div>
             </div>
          </form>
        </div>
      </li>      
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="logout.php" class="nav-link">
        <i class="nav-icon fas fa-sign-out-alt">log out</i>
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
            <a href="Dashboard_Doctor.php" class="nav-link active">
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
                <a href="patientRecords_Doctor.php" class="nav-link">
                  <i class="nav-icon fas fa-user"></i>
                  <p>Patient Records</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="Doctor_Prescription.php" class="nav-link">
                  <i class="nav-icon fas fa-prescription"></i>
                  <p>Prescription</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="genReports_Doctor.php" class="nav-link">
                  <i class="nav-icon fas fa-print"></i>
                  <p>Generate Reports</p>
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
    <div class="container" style="padding-left: 7%;"  >
          <h2 class="mt-4">Calendar View</h2>
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

                              // Checking if valid day to display
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
</body>
</html>
