
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Doctor Prescription</title>

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
        table {
            width: 100%;
            text-align: center;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f4f4f4;
        }
        input[type="number"] {
            width: 50px;
            text-align: center;
        }
        h4{
            text-align: right;
        }
        .form-control-num{
            width: 30%;
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
                <a href="Doctor_Prescription.php" class="nav-link active">
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
    <div class="container mt-5">
        <!-- Display the current date -->
        <h4 style="text-align: right;">Date: <?php echo date('m-d-Y'); ?></h4>

        <?php
        // Get the patient ID from the URL
        $pid = isset($_GET['pid']) ? htmlspecialchars($_GET['pid']) : 'N/A';
        ?>

        <!-- Display the selected patient ID -->
        <h3>Medicine Taking Schedule for Patient ID: <?php echo $pid; ?></h3>

        <!-- Form for submitting the schedule -->
        <form action="submit_schedule.php" method="post">
            <input type="hidden" name="pid" value="<?php echo $pid; ?>"> <!-- Hidden input for patient ID -->

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Before</th>
                        <th><input type="time" name="time_1" value="07:00" class="form-control"></th>
                        <th>Before</th>
                        <th><input type="time" name="time_2" value="12:00" class="form-control"></th>
                        <th>Before</th>
                        <th><input type="time" name="time_3" value="19:00" class="form-control"></th>
                        <th>Before</th>
                        <th><input type="time" name="time_3" value="19:00" class="form-control"></th>
                       
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="medicine_name[]" placeholder="Medicine name" class="form-control"></td>
                        <td><input type="number" name="before_time_1_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_1_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="before_time_2_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_2_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="before_time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="before_time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        
                    </tr>
                    <tr>
                        <td><input type="text" name="medicine_name[]" placeholder="Medicine name" class="form-control"></td>
                        <td><input type="number" name="before_time_1_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_1_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="before_time_2_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_2_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="before_time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="before_time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        
                    </tr>
                    <tr>
                        <td><input type="text" name="medicine_name[]" placeholder="Medicine name" class="form-control"></td>
                        <td><input type="number" name="before_time_1_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_1_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="before_time_2_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_2_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="before_time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="before_time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        
                    </tr>
                    <tr>
                        <td><input type="text" name="medicine_name[]" placeholder="Medicine name" class="form-control"></td>
                        <td><input type="number" name="before_time_1_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_1_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="before_time_2_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_2_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="before_time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="before_time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        
                    </tr>
                    <tr>
                        <td><input type="text" name="medicine_name[]" placeholder="Medicine name" class="form-control"></td>
                        <td><input type="number" name="before_time_1_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_1_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="before_time_2_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_2_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="before_time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="before_time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        <td><input type="number" name="time_3_taken[]" class="form-control-num" min="0" max="1" value="0"></td>
                        
                    </tr>
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary" id="btn-sched">Submit Schedule</button>
        </form>
    </div>
</div>

<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
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
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>
</html>
