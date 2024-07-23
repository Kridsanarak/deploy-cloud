<?php
session_start();
include 'includes/header.php';

// ตรวจสอบบทบาทของผู้ใช้
if (!isset($_SESSION['role_id'])) {
    echo "
    <script type='text/javascript'>
        alert('ไม่สามารถระบุบทบาทของผู้ใช้ได้');
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 100);
    </script>
    ";
    exit;
}

// กำหนดค่าตัวแปร $role_id จาก Session
$role_id = $_SESSION['role_id'];

// เลือก Navbar ตามบทบาทของผู้ใช้
if ($role_id == '1') {
    include 'includes/navbar.php';
} elseif ($role_id == '2') {
    include 'includes/headmaid_navbar.php';
} elseif ($role_id == '3') {
    include 'includes/maid_navbar.php';
} else {
    echo "ไม่สามารถระบุบทบาทของผู้ใช้ได้";
    exit;
}
include 'includes/calendar.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Add Task</h1>

    <!-- Form for adding tasks -->
    <form action="code.php" method="POST">
        <div class="row">
            <!-- First Half of the Form -->
            <div class="col-md-6">
                <!-- Assign User -->
                <div class="form-group">
                    <label>Assign User</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">--- Please select ---</option>
                        <?php
                        // Database connection
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "project";
                        $conn = new mysqli($servername, $username, $password, $dbname);
                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }
                        // SQL query to retrieve all users except user_id 1
                        $sql = "SELECT * FROM users WHERE role_id != '1' AND status_id = '1'";
                        $result = mysqli_query($conn, $sql);
                        // Loop through all users and display as options
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<option value="' . $row['user_id'] . '">' . $row['fullname'] . '</option>';
                        }
                        // Close connection
                        $conn->close();
                        ?>
                    </select>
                </div>

                <!-- Start Date -->
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" required min="<?php
                    date_default_timezone_set('Asia/Bangkok');
                    echo date('Y-m-d'); ?>">
                </div>

                <!-- End Date -->
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" required min="<?php
                    date_default_timezone_set('Asia/Bangkok');
                    echo date('Y-m-d', strtotime('+1 day')); ?>">
                </div>

                <!-- Floor Number -->
                <div class="form-group">
                    <label>Floor Number</label>
                    <select id="floor_id" name="floor_id" class="form-control" required>
                        <option value="">--- Please select ---</option>
                        <?php
                        // Display options for floor numbers
                        for ($i = 1; $i <= 11; $i++) {
                            echo '<option value="' . $i . '">' . $i . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Second Half of the Form -->
            <div class="col-md-6">
                <!-- Room Number -->
                <div class="form-group">
                    <label>Room Number</label>
                    <select id="room_id" name="room_id" class="form-control">
                        <option value="">--- Please select ---</option>
                    </select>
                </div>

                <!-- Room Status -->
                <div class="form-group">
                    <label>Room Status</label>
                    <select name="status_id" class="form-control">
                        <option value="">--- Please select ---</option>
                        <option value="1">Ready</option>
                        <option value="2">Waiting</option>
                        <option value="3">Not Ready</option>
                    </select>
                </div>

                <!-- Toilet Gender -->
                <div class="form-group">
                    <label>Toilet Gender</label>
                    <select name="toilet_gender_id" class="form-control">
                        <option value="">--- Please select ---</option>
                        <option value="1">Male</option>
                        <option value="2">Female</option>
                        <option value="3">Both</option>
                    </select>
                </div>

                <!-- Toilet Status -->
                <div class="form-group">
                    <label>Toilet Status</label>
                    <select name="toilet_status_id" class="form-control">
                        <option value="">--- Please select ---</option>
                        <option value="1">Ready</option>
                        <option value="2">Waiting</option>
                        <option value="3">Not Ready</option>
                    </select>
                </div>
            </div>


        </div>
</div>

<!-- Submit Button -->
<div class="modal-footer">
    <button type="submit" name="add_task_btn" class="btn btn-primary">Add Task</button>
</div>
</form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var floorSelect = document.getElementById('floor_id');
        var roomSelect = document.getElementById('room_id');

        // Disable the roomSelect initially
        roomSelect.disabled = true;

        floorSelect.addEventListener('change', function () {
            var floorId = this.value;
            if (floorId && floorId !== '1' && floorId !== '9') {
                fetch('get_rooms.php?floor_id=' + floorId)
                    .then(response => response.json())
                    .then(data => {
                        roomSelect.innerHTML = '<option value="">--- Please select ---</option>';
                        data.forEach(room => {
                            var option = document.createElement('option');
                            option.value = room.room_id;
                            option.textContent = room.room_name;
                            roomSelect.appendChild(option);
                        });
                        // Enable the roomSelect when there are options
                        roomSelect.disabled = false;
                    })
                    .catch(error => console.error('Error fetching rooms:', error));
            } else {
                roomSelect.innerHTML = '<option value="">--- Please select ---</option>';
                // Disable the roomSelect when no floor is selected or floor_id is 1 or 9
                roomSelect.disabled = true;
            }
        });
    });
</script>


<script src="vendor/jquery/jquery.min.js"></script>

<?php
include 'includes/scripts.php';
include 'includes/footer.php';
?>