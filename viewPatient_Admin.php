<?php
session_start();

include("connection.php");
include("function.php");

$user_data = check_login($con);

$pid = $name = $lastname = $address = $age = $phone_number = $gender = $status = '';

// Validate and process form submission via POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Ensure PID is passed and is a valid integer
    if (isset($_GET['pid']) && ctype_digit($_GET['pid'])) {
        $pid = intval($_GET['pid']);  // Get PID securely from POST

        // Fetch patient details based on PID
        $query = "SELECT * FROM patient_records WHERE pid = $pid LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $patient = mysqli_fetch_assoc($result);
        } else {
            echo "<div class='alert alert-danger' style='text-align: center;'>No record found for PID: $pid</div>";
            exit();
        }
    } else {
        echo "<div class='alert alert-danger' style='text-align: center;'>Invalid PID.</div>";
        exit();
    }

    // Retrieve medical records associated with the PID
    $medicalQuery = "SELECT * FROM medical_records WHERE pid = $pid";
    $medicalResult = mysqli_query($con, $medicalQuery);

    // Retrieve prescription data associated with the PID
    $prescriptionQuery = "SELECT * FROM prescriptions_data WHERE pid = $pid";
    $prescriptionResult = mysqli_query($con, $prescriptionQuery);

    // Check for SQL errors
    if (!$medicalResult || !$prescriptionResult) {
      echo "<div class='alert alert-danger' style='text-align: center;'>Error retrieving data.</div>";
      exit();
    }

    // Fetch vital signs associated with the PID
    $vitalQuery = "SELECT * FROM vital_signs WHERE pid = $pid";
    $vitalResult = mysqli_query($con, $vitalQuery);
    $vital_signs = mysqli_fetch_all($vitalResult, MYSQLI_ASSOC);
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
    h2, h3{
      font-weight: bold;
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
          <form class="form-inline" action="patientRecords_Admin.php" method="get">
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
      <img src=".//img/logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-4" style="opacity: 1">
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

          <!-- Start of nested menu items -->
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
                <a href="patientRecords.php" class="nav-link active">
                  <i class="nav-icon fas fa-user"></i>
                  <p>Patient Records</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="Admin_Prescription.php" class="nav-link">
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

<br>
  <div class="content-wrapper">
        <h2>Patient Record for <?php echo htmlspecialchars($patient['name']); ?></h2>
        <table class="table table-bordered">
            <tr><th>PID</th><td><?php echo htmlspecialchars($patient['pid']); ?></td></tr>
            <tr><th>First Name</th><td><?php echo htmlspecialchars($patient['name']); ?></td></tr>
            <tr><th>Last Name</th><td><?php echo htmlspecialchars($patient['lastname']); ?></td></tr>
            <tr><th>Address</th><td><?php echo htmlspecialchars($patient['address']); ?></td></tr>
            <tr><th>Age</th><td><?php echo htmlspecialchars($patient['age']); ?></td></tr>
            <tr><th>Birthday</th><td><?php echo htmlspecialchars($patient['birthday']); ?></td></tr>
            <tr><th>Phone Number</th><td><?php echo htmlspecialchars($patient['phone_number']); ?></td></tr>
            <tr><th>Gender</th><td><?php echo htmlspecialchars($patient['gender']); ?></td></tr>
            <tr><th>Status</th><td><?php echo htmlspecialchars($patient['status']); ?></td></tr>
        </table>
<br>
<br>
      <h3>Vital Signs</h3>
      <table class="table table-bordered">
          <thead>
              <tr>
                  <th>Date</th>
                  <th>BP</th>
                  <th>CR</th>
                  <th>RR</th>
                  <th>T</th>
                  <th>WT</th>
                  <th>HT</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($vital_signs as $vital): ?>
              <tr>
                  <td><?php echo htmlspecialchars($vital['date']); ?></td>
                  <td><?php echo htmlspecialchars($vital['bp']); ?></td>
                  <td><?php echo htmlspecialchars($vital['cr']); ?></td>
                  <td><?php echo htmlspecialchars($vital['rr']); ?></td>
                  <td><?php echo htmlspecialchars($vital['t']); ?></td>
                  <td><?php echo htmlspecialchars($vital['wt']); ?></td>
                  <td><?php echo htmlspecialchars($vital['ht']); ?></td>
              </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
<br>
<br>
      <h3>Diagnosis Records</h3>
          <?php
          // Fetch diagnosis records
          if (isset($pid) && $pid > 0) {
              $diagnosisQuery = "SELECT * FROM diagnosis WHERE pid = ?";
              $stmt = $con->prepare($diagnosisQuery);
              $stmt->bind_param("i", $pid);
              $stmt->execute();
              $diagnosisResult = $stmt->get_result();
              
              if ($diagnosisResult->num_rows > 0) {
                  echo '<table class="table table-bordered">
                      <thead>
                          <tr>
                              <th>Date</th>
                              <th>Subjective</th>
                              <th>Objective</th>
                              <th>Assessment</th>
                              <th>Plan</th>
                              <th>Laboratory</th>
                          </tr>
                      </thead>
                      <tbody>';
                  while ($row = $diagnosisResult->fetch_assoc()) {
                      echo '<tr>
                          <td>' . htmlspecialchars($row['date']) . '</td>
                          <td>' . htmlspecialchars($row['subjective']) . '</td>
                          <td>' . htmlspecialchars($row['objective']) . '</td>
                          <td>' . htmlspecialchars($row['assessment']) . '</td>
                          <td>' . htmlspecialchars($row['plan']) . '</td>
                          <td>' . (!empty($row['laboratory']) ? htmlspecialchars($row['laboratory']) : 'N/A') . '</td>
                      </tr>';
                  }
                  echo '</tbody></table>';
              } else {
                  echo '<div class="alert alert-info">No diagnosis records found for this patient.</div>';
              }
          }
          ?>
<br>
<br>
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
<br>
<br>
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
