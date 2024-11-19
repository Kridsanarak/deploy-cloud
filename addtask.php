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
    <form action="code.php" method="POST">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label><?php echo $translations['assign_work']; ?></label>
                    <select name="user_id" class="form-control" required>
                        <option value=""><?php echo $translations['please_select']; ?> ---</option>
                        <?php
                            $servername = "db"; // Use the service name 'db' defined in docker-compose
                            $username = "user"; // User defined in docker-compose
                            $password = "user_password"; // Password defined in docker-compose
                            $dbname = "project_maidmanage";
                        
                            $connection = new mysqli($servername, $username, $password, $dbname);
                            if ($connection->connect_error) {
                                die("Connection failed: " . $connection->connect_error);
                            }
                            
                            $sql = "SELECT * FROM users WHERE role_id != '1' AND status_id = '1'";
                            $result = mysqli_query($connection, $sql);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="' . $row['user_id'] . '">' . $row['fullname'] . '</option>';
                                }
                            } else {
                                echo '<option value=' . $translations['no_user'] . '</option>';
                            }
                            $connection->close();
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><?php echo $translations['date']; ?></label>
                    <input type="date" name="task_date" class="form-control" required
                        min="<?php echo date('Y-m-d'); ?>">
                </div>


                <div class="form-group">
                    <label><?php echo $translations['floor']; ?></label>
                    <div class="row">
                        <?php
                        for ($i = 1; $i <= 11; $i++) {
                            echo '<div class="col-3">';
                            echo '<div class="form-check">';
                            echo '<input class="form-check-input" type="checkbox" name="floor_id[]" value="' . $i . '" id="floor_' . $i . '" onchange="fetchRooms()">';
                            echo '<label class="form-check-label" for="floor_' . $i . '">' . $translations['floor'] . ' ' . $i . '</label><br>';
                            echo '</div>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <label style="margin-top: 1rem;"><?php echo $translations['status']; ?></label>
                        <select name="status_id" class="form-control">
                            <option value="2"><?php echo $translations['not_ready']; ?></option>
                            <option value="1"><?php echo $translations['ready']; ?></option>
                            <option value="3"><?php echo $translations['waiting']; ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?php echo $translations['toilet_status']; ?></label>
                        <select name="toilet_status_id" class="form-control">
                            <option value="2"><?php echo $translations['not_ready']; ?></option>
                            <option value="1"><?php echo $translations['ready']; ?></option>
                            <option value="3"><?php echo $translations['waiting']; ?></option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="col-md-6">
                <div id="roomSelections"></div>


            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" name="add_task_btn" class="btn btn-primary"><?php echo $translations['assign_work']; ?></button>
        </div>
    </form>
</div>

<script>
function fetchRooms() {
    var selectedFloors = Array.from(document.querySelectorAll('input[name="floor_id[]"]:checked')).map(input => input
        .value);
    var roomSelections = document.getElementById('roomSelections');

    roomSelections.innerHTML = '';

    if (selectedFloors.length > 0) {
        selectedFloors.forEach(floorId => {
            var floorDiv = document.createElement('div');
            floorDiv.className = 'form-group';

            var label = document.createElement('label');
            label.textContent = 'Select Rooms for Floor ' + floorId;

            floorDiv.appendChild(label);

            // Fetch rooms for the selected floor
            fetch('get_rooms.php?floor_id=' + floorId)
                .then(response => response.json())
                .then(data => {
                    data.forEach(room => {
                        var roomCheckboxDiv = document.createElement('div');
                        roomCheckboxDiv.className =
                            'form-check'; // Bootstrap class for checkbox styling

                        var checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.name = 'room_id[' + floorId +
                            '][]'; // Modified to allow multiple room selections per floor
                        checkbox.value = room.room_id;
                        checkbox.id = 'room_' + room.room_id; // Unique ID for each checkbox
                        checkbox.className = 'form-check-input'; // Bootstrap class for checkbox

                        var labelCheckbox = document.createElement('label');
                        labelCheckbox.htmlFor = 'room_' + room
                            .room_id; // Link the label to the checkbox
                        labelCheckbox.textContent = room.room_name; // Room name as label text
                        labelCheckbox.className =
                            'form-check-label'; // Bootstrap class for label styling

                        roomCheckboxDiv.appendChild(checkbox);
                        roomCheckboxDiv.appendChild(labelCheckbox);
                        floorDiv.appendChild(roomCheckboxDiv);
                    });
                })
                .catch(error => console.error('Error fetching rooms:', error));


            roomSelections.appendChild(floorDiv);
        });
    }
}
</script>


<script src="vendor/jquery/jquery.min.js"></script>

<?php
include 'includes/scripts.php';
include 'includes/footer.php';
?>