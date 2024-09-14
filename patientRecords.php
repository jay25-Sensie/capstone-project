<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("connection.php");

// sanitizing input data
function sanitize_input($con, $data) {
    return mysqli_real_escape_string($con, htmlspecialchars(strip_tags($data)));
}

// select patients from the database with optional search filtering
$search_query = '';
if (isset($_GET['search'])) {
    $search = sanitize_input($con, $_GET['search']);
    if (ctype_digit($search)) {
        $search_query = "WHERE pid = $search";
    } else {
        $search_query = "WHERE name LIKE '%$search%'";
    }
}
$query = "SELECT * FROM patient_records $search_query";
$result = mysqli_query($con, $query);
$patients = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Add/Update patient, Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_patient']) || isset($_POST['update_patient'])) {
        $name = sanitize_input($con, $_POST['name']);
        $lastname = sanitize_input($con, $_POST['lastname']);
        $address = sanitize_input($con, $_POST['address']);
        $age = intval($_POST['age']);
        $birthday = sanitize_input($con, $_POST['birthday']);
        $phone_number = sanitize_input($con, $_POST['phone_number']);
        $gender = sanitize_input($con, $_POST['gender']);

        if (isset($_POST['add_patient'])) {
            // Insert into patients table
            $query = "INSERT INTO patient_records (name, lastname, address, age, birthday, phone_number, gender, status) 
                      VALUES ('$name', '$lastname', '$address', $age, '$birthday', '$phone_number', '$gender', 'Active')";
            if (mysqli_query($con, $query)) {
                // Get the last inserted PID
                $pid = mysqli_insert_id($con);

                // Insert into user table
                $hashed_password = password_hash($pid, PASSWORD_BCRYPT);
                $query_user = "INSERT INTO users (username, password, role) VALUES ('$pid', '$hashed_password', 'patient')";
                mysqli_query($con, $query_user);

                header("Location: patientRecords.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($con);
            }
        } elseif (isset($_POST['update_patient'])) {
            $pid = intval($_POST['pid']);
            $query = "UPDATE patient_records SET 
                      name = '$name', lastname = '$lastname', address = '$address', 
                      age = $age, birthday = '$birthday', phone_number = '$phone_number', gender = '$gender' 
                      WHERE pid = $pid";
            if (mysqli_query($con, $query)) {
                header("Location: patientRecords.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($con);
            }
        }
    }

    if (isset($_POST['update_status'])) {
        $pid = intval($_POST['pid']);
        $status = sanitize_input($con, $_POST['status']);
        $query = "UPDATE patient_records SET status = '$status' WHERE pid = $pid";
        if (mysqli_query($con, $query)) {
            header("Location: patientRecords.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($con);
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard Patient Records</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        
        .content-wrapper{
          padding-left: 1%;
          padding-right: 1%;
        }
        .table-secondary {
            background-color: rgba(0, 0, 0, 0.1);
            color: white;
        }
        tr,th{
          text-align: center;
        }

    </style>

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
        <a href="index_home.php " class="nav-link">Home</a>
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
            <a href="Dashboard_Admin.php" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link ">
              <i class="nav-icon fas fa-folder"></i>
              <p>
                Menu
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


<div class="content-wrapper">
  <div class="container mt-4">
      <h2 class="mb-4">Patient Records</h2>

      <div class="mb-3">
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPatientModal">
              Add Patient
          </button>
      </div>

      <!-- Patient Records Table -->
      <!-- Patient Records Table -->
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>PID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Address</th>
                <th>Age</th>
                <th>Birthday</th>
                <th>Phone Number</th>
                <th>Gender</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($patients)): ?>
                <?php foreach ($patients as $patient): ?>
                    <tr class="<?php echo ($patient['status'] === 'Not Active') ? 'table-secondary' : ''; ?>">
                        <td><?php echo htmlspecialchars($patient['pid']); ?></td>
                        <td><?php echo htmlspecialchars($patient['name']); ?></td>
                        <td><?php echo htmlspecialchars($patient['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($patient['address']); ?></td>
                        <td><?php echo htmlspecialchars($patient['age']); ?></td>
                        <td><?php echo htmlspecialchars($patient['birthday']); ?></td>
                        <td><?php echo htmlspecialchars($patient['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($patient['gender']); ?></td>
                        <td><?php echo htmlspecialchars($patient['status']); ?></td>
                        <td class="action-buttons">
                            <a href="#" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editPatientModal<?php echo $patient['pid']; ?>">Edit</a>
                            <a href="viewPatient_Admin.php?pid=<?php echo $patient['pid']; ?>" class="btn btn-sm btn-info">View More</a>
                            <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#vitalSignModal<?php echo $patient['pid']; ?>">Vital Sign</a>
                        </td>
                    </tr>

                    <!-- Edit Patient Modal -->
                    <div class="modal fade" id="editPatientModal<?php echo $patient['pid']; ?>" tabindex="-1" role="dialog" aria-labelledby="editPatientModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <form action="patientRecords.php" method="post">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editPatientModalLabel">Edit Patient</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="pid" value="<?php echo $patient['pid']; ?>">
                                            <div class="form-group">
                                                <label for="edit-name">first Name</label>
                                                <input type="text" class="form-control" id="edit-name" name="name" value="<?php echo $patient['name']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="edit-lastname">Last Name</label>
                                                <input type="text" class="form-control" id="edit-lastname" name="lastname" value="<?php echo $patient['lastname']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="edit-address">Address</label>
                                                <input type="text" class="form-control" id="edit-address" name="address" value="<?php echo $patient['address']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="edit-age">Age</label>
                                                <input type="number" class="form-control" id="edit-age" name="age" value="<?php echo $patient['age']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="edit-birthday">Birthday</label>
                                                <input type="date" class="form-control" id="edit-birthday" name="birthday" placeholder="yyyy-mm-dd" value="<?php echo $patient['birthday']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="edit-phone-number">Phone Number</label>
                                                <input type="text" class="form-control" id="edit-phone-number" name="phone_number" value="<?php echo $patient['phone_number']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="edit-gender">Gender</label>
                                                <select class="form-control" id="edit-gender" name="gender">
                                                    <option value="Male" <?php echo ($patient['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                                                    <option value="Female" <?php echo ($patient['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                                                    <option value="Other" <?php echo ($patient['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" name="update_patient" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                            </div>
                        </div>
                    </div>

                    <!-- Vital Sign Modal for this patient -->
                    <div class="modal fade" id="vitalSignModal<?php echo $patient['pid']; ?>" tabindex="-1" role="dialog" aria-labelledby="vitalSignModalLabel<?php echo $patient['pid']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="vitalSignModalLabel<?php echo $patient['pid']; ?>">Vital Signs for <?php echo htmlspecialchars($patient['name']); ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Vital Sign Form -->
                                    <form action="vitalSign.php" method="post">
                                        <input type="hidden" name="pid" value="<?php echo $patient['pid']; ?>">
                                        <div class="form-group">
                                            <label for="date">Date</label>
                                            <input type="date" class="form-control" id="date" name="date" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="bp">Blood Pressure (BP)</label>
                                            <input type="text" class="form-control" id="bp" name="bp" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="cr">Heart Rate (CR)</label>
                                            <input type="text" class="form-control" id="cr" name="cr" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="rr">Respiratory Rate (RR)</label>
                                            <input type="text" class="form-control" id="rr" name="rr" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="t">Temperature (T)</label>
                                            <input type="text" class="form-control" id="t" name="t" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="wt">Weight (WT)</label>
                                            <input type="text" class="form-control" id="wt" name="wt" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="ht">Height (HT)</label>
                                            <input type="text" class="form-control" id="ht" name="ht" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Vital Signs</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center">No patients found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


  <!-- Add Patient -->
  <div class="modal fade" id="addPatientModal" tabindex="-1" role="dialog" aria-labelledby="addPatientModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <form action="patientRecords.php" method="post">
                  <div class="modal-header">
                      <h5 class="modal-title" id="addPatientModalLabel">Add New Patient</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <div class="form-group">
                          <label for="name">First Name</label>
                          <input type="text" class="form-control" id="name" name="name" required>
                      </div>
                      <div class="form-group">
                          <label for="lastname">Last Name</label>
                          <input type="text" class="form-control" id="lastname" name="lastname" required>
                      </div>
                      <div class="form-group">
                          <label for="address">Address</label>
                          <input type="text" class="form-control" id="address" name="address">
                      </div>
                      <div class="form-group">
                          <label for="age">Age</label>
                          <input type="number" class="form-control" id="age" name="age">
                      </div>
                      <div class="form-group">
                          <label for="birthday">Birthday</label>
                          <input type="date" class="form-control" id="birthday" placeholder="yyyy-mm-dd" name="birthday">
                      </div>
                      <div class="form-group">
                          <label for="phone_number">Phone Number</label>
                          <input type="text" class="form-control" id="phone_number" name="phone_number">
                      </div>
                      <div class="form-group">
                          <label for="gender">Gender</label>
                          <select class="form-control" id="gender" name="gender">
                              <option value="Male">Male</option>
                              <option value="Female">Female</option>
                              <option value="Other">Other</option>
                          </select>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="submit" name="add_patient" class="btn btn-primary">Add Patient</button>
                  </div>
              </form>
          </div>
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
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
  