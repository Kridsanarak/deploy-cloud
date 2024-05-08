<?php
include 'includes/header.php';
include 'includes/navbar.php';
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
                        $sql = "SELECT * FROM users WHERE user_id <> 1";
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

                <!-- Task Title -->
                <div class="form-group">
                    <label> Task Title </label>
                    <input type="text" name="task_title" class="form-control" required placeholder="Enter Task Title">
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label> Description </label>
                    <input type="text" name="task_description" class="form-control" placeholder="Enter Description">
                </div>

                <!-- Start Date -->
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" required placeholder="Enter Start Date"
                        min="<?php
                        date_default_timezone_set('Asia/Bangkok');
                        echo date('Y-m-d', strtotime('+1 day')); ?>">
                </div>

                <div class="form-group">
                    <label> Floor Number </label>
                    <select name="floor_number" class="form-control" required>
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
                    <label> Room Number </label>
                    <select name="room_number" class="form-control" required>
                        <option value="">--- Please select ---</option>
                        <?php
                        // Display options for room numbers
                        for ($i = 1; $i <= 6; $i++) {
                            echo '<option value="' . $i . '">' . $i . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <!-- Room Type -->
                <div class="form-group">
                    <label> Room Type </label>
                    <select name="room_type" class="form-control" required>
                        <option value="">--- Please select ---</option>
                        <option value="lecture">lecture</option>
                        <option value="meeting">meeting</option>
                        <option value="lab">lab</option>
                    </select>
                </div>

                <!-- Room Status -->
                <div class="form-group">
                    <label> Room Status </label>
                    <select name="room_status" class="form-control" required>
                        <option value="">--- Please select ---</option>
                        <option value="Ready">Ready</option>
                        <option value="Waiting">Waiting</option>
                        <option value="Not Ready">Not Ready</option>
                    </select>
                </div>

                <!-- Toilet Gender -->
                <div class="form-group">
                    <label> Toilet Gender </label>
                    <select name="toilet_gender" class="form-control" required>
                        <option value="">--- Please select ---</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <!-- Toilet Status -->
                <div class="form-group">
                    <label> Toilet Status </label>
                    <select name="toilet_status" class="form-control" required>
                        <option value="">--- Please select ---</option>
                        <option value="Ready">Ready</option>
                        <option value="Waiting">Waiting</option>
                        <option value="Not Ready">Not Ready</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="modal-footer">
            <button type="submit" name="add_task_btn" class="btn btn-primary">Add Task</button>
        </div>
    </form>

</div>

<?php
include 'includes/scripts.php';
include 'includes/footer.php';
?>