<?php
session_start();

    include("connection.php");
    include("function.php");

    $user_data = check_login($con);

   
    $uploadDir = 'uploads/';

// Check if the directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); // Create directory with proper permissions
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['upload'])) {
        $pid = $_POST['pid'];
        $file = $_FILES['file']['name'];
        $targetFile = $uploadDir . basename($file);

        // Check if the PID exists in the database
        $pidCheckSql = "SELECT COUNT(*) as count FROM patient_records WHERE pid='$pid'";
        $result = mysqli_query($con, $pidCheckSql);
        $row = mysqli_fetch_assoc($result);

        if ($row['count'] > 0) {
            // PID exists, proceed with file upload
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                $sql = "INSERT INTO medical_records (pid, file_path) VALUES ('$pid', '$targetFile')";
                if (mysqli_query($con, $sql)) {
                    echo "<div class='alert alert-success' style='padding-left:20%; text-align: center;'>File uploaded successfully.</div>";
                } else {
                    echo "<div class='alert alert-danger' style='padding-left:20%; text-align: center;'>Error uploading file to database.</div>";
                }
            } else {
                echo "<div class='alert alert-danger' style='padding-left:20%; text-align: center;'>Error moving file. Check directory permissions.</div>";
            }
        } else {
            // PID does not exist
            echo "<div class='alert alert-danger' style='padding-left:20%; text-align: center;'>The provided PID does not exist in the records.</div>";
        }
    }
}
    

   
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Medical Records</title>

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
                <form class="form-inline" action="patientRecords.php" method="get">
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
                <a href="medical_Records.php" class="nav-link active">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Medical Records</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="genReports.php" class="nav-link">
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
        <div class="container mt-5">
            <!-- Display messages -->
            <?php if (!empty($message)): ?>
                <div class="alert-container">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Form to upload files -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upload Medical Records</h3>
                </div>
                <div class="card-body">
                    <form action="medical_records.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="pid">Patient ID:</label>
                            <input type="text" class="form-control" id="pid" name="pid" required>
                        </div>
                        <div class="form-group">
                            <label for="file">Select File/Image:</label>
                            <input type="file" class="form-control-file" id="file" name="file" required>
                        </div>
                        <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Display patient records -->
    



    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
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
