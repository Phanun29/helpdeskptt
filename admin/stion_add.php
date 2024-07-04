<?php
require('include/header.php');
require('include/sidebar.php');
?>
<?php
//session_start();  // Start the session

include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $station_id = $_POST['station_id'];
  $station_name = $_POST['station_name'];
  $station_type = $_POST['station_type'];

  $sql = "INSERT INTO tbl_station (station_id, station_name, station_type) 
            VALUES ('$station_id', '$station_name', '$station_type')";

  if ($conn->query($sql) === TRUE) {
    // Successful insertion
    $_SESSION['success_message'] = "New station created successfully";
    // header('Location: ' . $_SERVER['REQUEST_URI']);
    // exit();
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}
$conn->close();
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Gas Station</h1>
        </div><!-- /.col -->


        <!-- alert -->
        <?php if (isset($_SESSION['success_message'])) : ?>
          <div class="alert alert-success alert-dismissible fade show col-sm-6" role="alert">
            <strong><?php echo $_SESSION['success_message']; ?></strong>
            <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert(this)"></button>
          </div>
          <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- button back -->
  <div class="content-header">
    <div class="container-fluid ml-2">
      <div class="row mb-2">


        <div class="col-sm-6">
          <a href="station.php" class="btn btn-primary">BACK</a>
        </div>

      </div>
    </div>
  </div>
  <!-- dashboard -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Create Station</h3>
            </div>
            <form method="POST" >
              <div class="card-body">
                <div class="form-group">
                  <label for="exampleInputStatioID">Station ID</label>
                  <input type="text" name="station_id" class="form-control" id="exampleInputStatioID" placeholder="Station ID" required>
                </div>
                <div class="form-group">
                  <label for="exampleInputStatioName">Station Name</label>
                  <input type="text" name="station_name" class="form-control" id="exampleInputStatioName" placeholder="Station Name" required>
                </div>
                <div class="form-group">
                  <label>Station Type</label>
                  <select name="station_type" class="form-control select2bs4" style="width: 100%;" required>
                    <option value="">Select</option>
                    <option value="CoCo">CoCo</option>
                    <option value="DoDo">DoDO</option>
                  </select>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
require('include/footer.php');
?>

<script>
  function closeAlert(button) {
    var alert = button.closest('.alert');
    alert.style.display = 'none';
  }
</script>