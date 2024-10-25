<?php
session_start();

include("connection.php");
include("function.php");

if (!isset($_SESSION['userID']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: Admin_Staff_login.php");
  exit();
}
if (isset($_GET['pid'])) {
    $pid = htmlspecialchars($_GET['pid']);
    
    // Function to fetch diagnosis records by PID
    function fetchDiagnosisRecordsByPID($pid) {
        global $con;
        $stmt = $con->prepare("SELECT 
                                    d.date, 
                                    d.subjective, 
                                    d.objective, 
                                    d.assessment, 
                                    d.plan,
                                    p.pid, 
                                    p.name, 
                                    p.lastname,
                                    p.address, 
                                    p.phone_number, 
                                    p.age, 
                                    p.gender, 
                                    p.birthday 
                                FROM diagnosis d
                                JOIN patient_records p ON d.pid = p.pid
                                WHERE d.pid = ?");
        $stmt->bind_param("s", $pid);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC); // Return results as an associative array
    }

    // Fetch the diagnosis records for the specified PID
    $diagnosisRecords = fetchDiagnosisRecordsByPID($pid);     
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Prescription</title>

<!-- jQuery UI (local) -->
<link rel="stylesheet" href="plugins/jquery-ui/jquery-ui.min.css">
<!-- Font Awesome (local) -->
<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
<!-- Tempusdominus Bootstrap 4 (local) -->
<link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
<!-- iCheck (local) -->
<link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<!-- JQVMap (local) -->
<link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
<!-- Theme style (local) -->
<link rel="stylesheet" href="dist/css/adminlte.min.css">
<!-- overlayScrollbars (local) -->
<link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<!-- Daterange picker (local) -->
<link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
<!-- Summernote (local) -->
<link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">

  <style>
   
    .table-responsive{
        width: 100%;
        text-align: center;
    }
    .content-wrapper{
          padding-left: 5%;
          padding-right: 5%;
          padding-top: 3%;
    }
    .nav-treeview .nav-item {
        padding-left: 3%;
    }
    @media print {
        #printButton {
            display: none;
        }
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
                <form class="form-inline" action="patientRecords.php" method="post">
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
            <a href="Dashboard_Admin.php" class="nav-link active">
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
                <a href="patientRecords.php" class="nav-link">
                  <i class="nav-icon fas fa-user"></i>
                  <p>Patient Records</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="Admin_Prescription.php" class="nav-link active">
                  <i class="nav-icon fas fa-prescription"></i>
                  <p>Prescription</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="medical_Records.php" class="nav-link">
                  <i class="nav-icon fas fa-file-medical"></i>
                  <p>Add Medical Records</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="genReports.php" class="nav-link">
                  <i class="nav-icon fas fa-print"></i>
                  <p>Generate Reports</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="setCalendar.php" class="nav-link">
                  <i class="nav-icon fas fa-calendar-alt"></i>
                  <p>Set Calendar</p>
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
            <h3 class="text-center mb-4">Immaculate Medico-Surgical Clinic</h3>
            <h4 class="text-center mb-4">Diagnosis Records for PID: <?php echo $pid; ?></h4>
            <hr>
            <br> <br>

            <?php
            if (!empty($diagnosisRecords)) {
                $record = $diagnosisRecords[0]; // Get the first record for patient info
                echo '<div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> ' . htmlspecialchars($record['name'])  . ' ' . htmlspecialchars($record['lastname']). '</p>
                            <p><strong>Address:</strong> ' . htmlspecialchars($record['address']) . '</p>
                            <p><strong>Phone Number:</strong> ' . htmlspecialchars($record['phone_number']) . '</p>
                        </div>
                        <div class="col-md-6" style="text-align: right;">
                            <p><strong>Age:</strong> ' . htmlspecialchars($record['age']) . '</p>
                            <p><strong>Sex:</strong> ' . htmlspecialchars($record['gender']) . '</p>
                            <p><strong>Birthday:</strong> ' . htmlspecialchars($record['birthday']) . '</p>
                        </div>
                    </div>';
            } else {
                echo '<div class="alert alert-info">No diagnosis records found for PID: ' . $pid . '.</div>';
            }
            ?>

            <div class="row">
                <div class="col-md-12">
                    <?php
                    if (!empty($diagnosisRecords)) {
                        echo '<table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Subjective</th>
                                    <th>Objective</th>
                                    <th>Assessment</th>
                                    <th>Plan</th>
                                </tr>
                            </thead>
                            <tbody>';
                        foreach ($diagnosisRecords as $record) {
                            echo '<tr>
                                <td>' . htmlspecialchars($record['date']) . '</td>
                                <td>' . htmlspecialchars($record['subjective']) . '</td>
                                <td>' . htmlspecialchars($record['objective']) . '</td>
                                <td>' . htmlspecialchars($record['assessment']) . '</td>
                                <td>' . htmlspecialchars($record['plan']) . '</td>
                            </tr>';
                        }
                        echo '</tbody></table>';
                    } else {
                        echo '<div class="alert alert-info">No diagnosis records found for PID: ' . $pid . '.</div>';
                    }
                    ?>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12 text-left">
                    <button onclick="window.print()" class="btn btn-primary" id="printButton">Print Diagnosis</button>
                </div>
            </div>
        </div>
    </div>

<!-- ./wrapper -->

<!-- jQuery (local) -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI (local) -->
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
<script>
function setPatientId(patientId) {
    document.getElementById('patientId').value = patientId;
}
</script>
</body>
</html>