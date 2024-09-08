<?php
session_start();
include("connection.php"); // Include your database connection script

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_prescription'])) {
        $pid = $_POST['pid'];
        $medicine_name = $_POST['medicine_name'];
        $dosage = $_POST['dosage'];
        $frequency = $_POST['frequency'];
        $time_to_take = $_POST['time_to_take'];

        $pidCheckSql = "SELECT COUNT(*) as count FROM patient_records WHERE pid='$pid'";
        $result = mysqli_query($con, $pidCheckSql);
        $row = mysqli_fetch_assoc($result);


        if ($row['count'] > 0) {
          
        // Inserting a new prescription
          $query = "INSERT INTO prescriptions_data (pid, medicine_name, dosage, frequency, time_to_take) VALUES (?, ?, ?, ?, ?)";
          $stmt = $con->prepare($query);
          $stmt->bind_param("issss", $pid, $medicine_name, $dosage, $frequency, $time_to_take);
          $stmt->execute();
          $stmt->close();
        }else{
          echo '<script>alert("No Pid Found.");</script>';
        }


    } elseif (isset($_POST['update_prescription'])) {
        $id = $_POST['id'];
        $pid = $_POST['pid'];
        $medicine_name = $_POST['medicine_name'];
        $dosage = $_POST['dosage'];
        $frequency = $_POST['frequency'];
        $time_to_take = $_POST['time_to_take'];

        // Updating prescription
        $query = "UPDATE prescriptions_data SET pid = ?, medicine_name = ?, dosage = ?, frequency = ?, time_to_take = ? WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("issssi", $pid, $medicine_name, $dosage, $frequency, $time_to_take, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_prescription'])) {
        $id = $_POST['id'];

        // Delete prescription
        $query = "DELETE FROM prescriptions_data WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// select all prescriptions from the database
$query = "SELECT * FROM prescriptions_data";
$result = $con->query($query);
$prescriptions = $result->fetch_all(MYSQLI_ASSOC);
?>



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
    table{
        width: 100%;
        text-align: center;
    }
    .content-wrapper{
          padding-left: 2%;
          padding-right: 2%;
    }
    .table-responsive, .editPrescriptionModal{
      text-align: left;
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
                <a href="patientRecords_Doctor.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Patient Records</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="Doctor_Prescription.php" class="nav-link active">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Prescription</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="genReports_Doctor.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Reports</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="viewCalendar_Doctor.php" class="nav-link">
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

          <div class="mb-3">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPrescriptionModal">
                  Add Prescription
              </button>
          </div>

          <div class="table-responsive">
              <table class="table table-bordered table-striped">
                  <thead>
                      <tr>
                        
                          <th>Patient ID</th>
                          <th>Medicine Name</th>
                          <th>Dosage</th>
                          <th>Frequency</th>
                          <th>Time to Take</th>
                          <th>Actions</th>
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
                              <td class="action-buttons">

                                  <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editPrescriptionModal<?php echo $prescription['id']; ?>">Edit</button>

                                  <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deletePrescriptionModal<?php echo $prescription['id']; ?>">Delete</button>
                              </td>
                          </tr>

                          <!-- Edit Prescription -->
                          <div class="modal fade" id="editPrescriptionModal<?php echo $prescription['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editPrescriptionModalLabel" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                  <div class="modal-content">
                                      <form action="prescription.php" method="post">
                                          <div class="modal-header">
                                              <h5 class="modal-title" id="editPrescriptionModalLabel">Edit Prescription</h5>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                  <span aria-hidden="true">&times;</span>
                                              </button>
                                          </div>
                                          <div class="modal-body">
                                              <input type="hidden" name="id" value="<?php echo $prescription['id']; ?>">
                                              <div class="form-group">
                                                  <label for="edit-pid">Patient ID</label>
                                                  <input type="number" class="form-control" id="edit-pid" name="pid" value="<?php echo htmlspecialchars($prescription['pid']); ?>" required>
                                              </div>
                                              <div class="form-group">
                                                  <label for="edit-medicine_name">Medicine Name</label>
                                                  <input type="text" class="form-control" id="edit-medicine_name" name="medicine_name" value="<?php echo htmlspecialchars($prescription['medicine_name']); ?>" required>
                                              </div>
                                              <div class="form-group">
                                                  <label for="edit-dosage">Dosage</label>
                                                  <input type="text" class="form-control" id="edit-dosage" name="dosage" value="<?php echo htmlspecialchars($prescription['dosage']); ?>" required>
                                              </div>
                                              <div class="form-group">
                                                  <label for="edit-frequency">Frequency</label>
                                                  <input type="text" class="form-control" id="edit-frequency" name="frequency" value="<?php echo htmlspecialchars($prescription['frequency']); ?>" required>
                                              </div>
                                              <div class="form-group">
                                                  <label for="edit-time_to_take">Time to Take</label>
                                                  <input type="text" class="form-control" id="edit-time_to_take" name="time_to_take" value="<?php echo htmlspecialchars($prescription['time_to_take']); ?>" required>
                                              </div>
                                          </div>
                                          <div class="modal-footer">
                                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                              <button type="submit" name="update_prescription" class="btn btn-primary">Save changes</button>
                                          </div>
                                      </form>
                                  </div>
                              </div>
                          </div>

                          <!-- Delete Prescription -->
                          <div class="modal fade" id="deletePrescriptionModal<?php echo $prescription['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deletePrescriptionModalLabel" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                  <div class="modal-content">
                                      <form action="Doctor_Prescription.php" method="post">
                                          <div class="modal-header">
                                              <h5 class="modal-title" id="deletePrescriptionModalLabel">Confirm Deletion</h5>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                  <span aria-hidden="true">&times;</span>
                                              </button>
                                          </div>
                                          <div class="modal-body">
                                              <p>Are you sure you want to delete this prescription?</p>
                                              <input type="hidden" name="id" value="<?php echo $prescription['id']; ?>">
                                          </div>
                                          <div class="modal-footer">
                                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                              <button type="submit" name="delete_prescription" class="btn btn-danger">Delete</button>
                                          </div>
                                      </form>
                                  </div>
                              </div>
                          </div>

                      <?php endforeach; ?>
                  </tbody>
              </table>
          </div>
      </div>

      <!-- Add Prescription -->
      <div class="modal fade" id="addPrescriptionModal" tabindex="-1" role="dialog" aria-labelledby="addPrescriptionModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <form action="Doctor_Prescription.php" method="post">
                      <div class="modal-header">
                          <h5 class="modal-title" id="addPrescriptionModalLabel">Add New Prescription</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                      <div class="modal-body">
                          <div class="form-group">
                              <label for="pid">Patient ID</label>
                              <input type="number" class="form-control" id="pid" name="pid" required>
                          </div>
                          <div class="form-group">
                              <label for="medicine_name">Medicine Name</label>
                              <input type="text" class="form-control" id="medicine_name" name="medicine_name" required>
                          </div>
                          <div class="form-group">
                              <label for="dosage">Dosage</label>
                              <input type="text" class="form-control" id="dosage" name="dosage" required>
                          </div>
                          <div class="form-group">
                              <label for="frequency">Frequency</label>
                              <input type="text" class="form-control" id="frequency" name="frequency" required>
                          </div>
                          <div class="form-group">
                              <label for="time_to_take">Time to Take</label>
                              <input type="text" class="form-control" id="time_to_take" name="time_to_take" required>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          <button type="submit" name="add_prescription" class="btn btn-primary">Add Prescription</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>


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
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
