<?php
session_start();

    include("connection.php");
    include("function.php");

    $user_data = check_login($con);

    
    // selecting from prescription
$query = "SELECT * FROM prescriptions_data";
$result = $con->query($query);
$prescriptions = $result->fetch_all(MYSQLI_ASSOC);



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['number']) && isset($_POST['message'])) {
    $phone = $_POST['number'];
    $message = $_POST['message'];

    // Movider API credentials (replace with your actual credentials)
    $apiKey = "1KDFRBMfpbf8184RghTlffnbGT5";  // Replace with your actual API key
    $apiSecret = "tzp99FCPR89o9rMJ••••••••••••••••••••••••";  // Replace with your actual API secret
    $smsUrl = "https://api.movider.co/v1/sms";

    // Data for the SMS
    $data = [
        'to'         => $phone,
        'text'       => $message,
        'api_key'    => $apiKey,
        'api_secret' => $apiSecret
    ];

    // Initialize cURL
    $ch = curl_init($smsUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));  // Use http_build_query for application/x-www-form-urlencoded
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    // Execute the request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

  
    echo '<script type="text/javascript">';
    if ($error) {
        echo 'alert("Failed to send SMS due to cURL error: ' . addslashes($error) . '");';
    } else {
        if ($http_code == 200) {
            $response_data = json_decode($response, true);
            if (isset($response_data['phone_number_list']) && count($response_data['phone_number_list']) > 0) {
                echo 'alert("SMS sent successfully!");';
            } else {
                echo 'alert("Failed to send SMS. Response: ' . addslashes($response) . '");';
            }
        } else {
            echo 'alert("Failed to send SMS. HTTP Code: ' . $http_code . ' | Response: ' . addslashes($response) . '");';
        }
    }
    echo '</script>';
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Prescription</title>

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
   
    .table-responsive{
        width: 100%;
        text-align: center;
    }
    .content-wrapper{
          padding-left: 5%;
          padding-right: 5%;
          padding-top: 3%;
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
            <a href="index3.html" class="nav-link active">
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
                <a href="Admin_Prescription.php" class="nav-link active">
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
    <div class="container mt-4">
        <h2 class="mb-4">Prescriptions</h2>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>PID</th>
                        <th>Medicine Name</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Time to Take</th>
                        <th>Set Prescription</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prescriptions as $prescription): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($prescription['pid']); ?></td>
                            <td><?php echo htmlspecialchars($prescription['medicine_name']); ?></td>
                            <td><?php echo htmlspecialchars($prescription['dosage']); ?></td>
                            <td><?php echo htmlspecialchars($prescription['frequency']); ?></td>
                            <td><?php echo htmlspecialchars($prescription['time_to_take']); ?></td>
                            <td>
                              <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#sendSMSModal" onclick="setPatientId('<?php echo htmlspecialchars($prescription['pid']); ?>')">Send Prescription</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="sendSMSModal" tabindex="-1" role="dialog" aria-labelledby="sendSMSModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="sendSMSModalLabel">Send SMS</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post">
              <div class="form-group">
                <label for="number">Phone Number:</label>
                <input type="text" class="form-control" id="number" name="number" placeholder="+1234567890" required>
              </div>
              <div class="form-group">
                <label for="message">Message:</label>
                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Your message here..." required></textarea>
              </div>
              <input type="hidden" id="patientId" name="pid">
              <button type="submit" class="btn btn-primary">Send SMS</button>
            </form>
          </div>
        </div>
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
<script>
function setPatientId(patientId) {
    document.getElementById('patientId').value = patientId;
}
</script>
</body>
</html>
