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

    <!-- DataTales Example -->
    <div class="container-fluid">

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">DataTables</h6>
            </div>
            <?php
            date_default_timezone_set('Asia/Bangkok');
            // การกำหนดค่าในการเชื่อมต่อฐานข้อมูล
            $servername = "db"; // Use the service name 'db' defined in docker-compose
            $username = "user"; // User defined in docker-compose
            $password = "user_password"; // Password defined in docker-compose
            $dbname = "project_maidmanage";

            // การเชื่อมต่อกับ MySQL
            $conn = new mysqli($servername, $username, $password, $dbname);

            // ตรวจสอบการเชื่อมต่อ
            if ($conn->connect_error) {
                die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
            }

            // สร้างคำสั่ง SQL เพื่อดึงข้อมูล
            $today = date("Y-m-d");
            $startOfWeek = date("Y-m-d", strtotime('monday this week'));
            $endOfWeek = date("Y-m-d", strtotime('sunday this week'));

            $sql = "SELECT 
        t.task_id,
        t.start_date,
        t.end_date,
        t.user_id,
        t.floor_id,
        t.room_id,
        t.status_id,
        t.toilet_status_id,
        t.image,
        u.fullname AS user_fullname
    FROM task t
    INNER JOIN users u ON t.user_id = u.user_id
    WHERE t.start_date BETWEEN '$startOfWeek' AND '$endOfWeek'
    ORDER BY t.start_date ASC";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo '<div class="card-body">';
                echo '<div class="table-responsive">';
                echo '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Start Date</th>';
                echo '<th>End Date</th>';
                echo '<th>User</th>';
                echo '<th>Floor</th>';
                echo '<th>Room</th>';
                echo '<th>Status</th>';
                echo '<th>Toilet Status</th>';
                echo '<th>Image</th>';
                echo '<th>Action</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                while ($row = $result->fetch_assoc()) {
                    // แปลงค่า ID เป็นข้อความที่อ่านได้
                    $status = match ($row["status_id"]) {
                        1 => 'Ready',
                        2 => 'Not Ready',
                        3 => 'Waiting',
                        default => '-'
                    };
                    $toilet_status = match ($row["toilet_status_id"]) {
                        1 => 'Ready',
                        2 => 'Not Ready',
                        3 => 'Waiting',
                        default => '-'
                    };

                    echo '<tr>';
                    echo '<td>' . ($row["start_date"] ?? '-') . '</td>';
                    echo '<td>' . ($row["end_date"] ?? '-') . '</td>';
                    echo '<td>' . ($row["user_fullname"] ?? '-') . '</td>';
                    echo '<td>IF-' . ($row["floor_id"] ?? '-') . '0' . ($row["room_id"] ?? '-') . '</td>';
                    echo '<td>' . ($row["room_id"] ?? '-') . '</td>';
                    echo '<td>' . $status . '</td>';
                    echo '<td>' . $toilet_status . '</td>';
                    echo '<td>';
                    if (!empty($row["image"])) {
                        $upload_dir = "upload/";
                        $image_path = $upload_dir . $row["image"];
                        echo '<img src="' . $image_path . '" class="img-thumbnail" alt="Image" style="max-width: 100px; max-height: 100px;">';
                    } else {
                        echo 'No Image';
                    }
                    echo '</td>';
                    echo '<td>';
                    if ($status == 'Ready' && $toilet_status == 'Ready') {
                        // ถ้า status และ toilet_status เป็น 'Ready' ทั้งคู่
                        echo '<button type="button" class="btn btn-secondary btn-circle btn-sm" disabled><i class="fas fa-paper-plane"></i></button>';
                    } elseif ($_SESSION["role_id"] == 'maid' && $row["user_id"] != $_SESSION["user_id"]) {
                        // ถ้าเป็นหัวหน้าและเป็นงานของตัวเอง
                        echo '<button type="button" class="btn btn-secondary btn-circle btn-sm" disabled><i class="fas fa-paper-plane"></i></button>';
                    } else {
                        // ถ้าไม่ใช่
                        echo '<button type="button" class="btn btn-success btn-circle btn-sm" data-toggle="modal" data-target="#submitWorkModal' . $row["task_id"] . '"><i class="fas fa-paper-plane"></i></button>';
                    }
                    echo '  ';
                    echo '<button type="button" class="btn btn-info btn-circle btn-sm" data-toggle="modal" data-target="#imageModal' . $row["task_id"] . '"><i class="fas fa-image"></i></button>';
                    echo '</td>';
                    echo '</tr>';

                    // Modal สำหรับ Submit Work
                    echo '<div class="modal fade" id="submitWorkModal' . $row["task_id"] . '" tabindex="-1" role="dialog" aria-labelledby="submitWorkModalLabel' . $row["task_id"] . '" aria-hidden="true">';
                    echo '<div class="modal-dialog" role="document">';
                    echo '<div class="modal-content">';
                    echo '<div class="modal-header">';
                    echo '<h5 class="modal-title" id="submitWorkModalLabel' . $row["task_id"] . '">Submit Work</h5>';
                    echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                    echo '<span aria-hidden="true">&times;</span>';
                    echo '</button>';
                    echo '</div>';

                    // เพิ่ม input hidden สำหรับ task_id
                    echo '<form action="submit_work.php" method="POST" enctype="multipart/form-data">';
                    echo '<div class="modal-body">';
                    echo '<input type="hidden" name="task_id" value="' . $row["task_id"] . '">';

                    echo '<div class="form-group">';
                    echo '<label>Room Status</label>';
                    echo '<select name="room_status" class="form-control">';
                    echo '<option value="1" ' . ($row["status_id"] == "1" ? "selected" : "") . '>Ready</option>';
                    echo '<option value="3" ' . ($row["status_id"] == "3" ? "selected" : "") . '>Waiting</option>';
                    echo '<option value="2" ' . ($row["status_id"] == "2" ? "selected" : "") . '>Not Ready</option>';
                    echo '</select>';
                    echo '</div>';

                    // เพิ่ม input field สำหรับแก้ไข toilet_status
                    echo '<div class="form-group">';
                    echo '<label>Toilet Status</label>';
                    echo '<select name="toilet_status" class="form-control">';
                    echo '<option value="1" ' . ($row["toilet_status_id"] == "1" ? "selected" : "") . '>Ready</option>';
                    echo '<option value="3" ' . ($row["toilet_status_id"] == "3" ? "selected" : "") . '>Waiting</option>';
                    echo '<option value="2" ' . ($row["toilet_status_id"] == "2" ? "selected" : "") . '>Not Ready</option>';
                    echo '</select>';
                    echo '</div>';

                    // เพิ่ม input field สำหรับอัปโหลดรูปภาพ
                    echo '<div class="form-group">';
                    echo '<label for="image">Image</label>';
                    echo '<div class="custom-file">';
                    echo '<input type="file" name="image" class="custom-file-input" id="image">';
                    echo '<label class="custom-file-label" for="image">Choose file</label>';
                    echo '</div>';
                    echo '</div>';

                    echo '</div>';
                    echo '<div class="modal-footer">';
                    echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                    echo '<button type="submit" name="submit_work_btn" class="btn btn-primary">Submit Work</button>';
                    echo '</div>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';

                    // Modal สำหรับแสดงรูปภาพ
                    echo '<div class="modal fade" id="imageModal' . $row["task_id"] . '" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel' . $row["task_id"] . '" aria-hidden="true">';
                    echo '<div class="modal-dialog modal-dialog-centered" role="document">';
                    echo '<div class="modal-content">';
                    echo '<div class="modal-header">';
                    echo '<h5 class="modal-title" id="imageModalLabel' . $row["task_id"] . '">Image Preview</h5>';
                    echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                    echo '<span aria-hidden="true">&times;</span>';
                    echo '</button>';
                    echo '</div>';
                    echo '<div class="modal-body">';
                    if (!empty($row["image"])) {
                        $upload_dir = "upload/";
                        $image_path = $upload_dir . $row["image"];
                        echo '<img src="' . $image_path . '" class="img-fluid" alt="Image">';
                    } else {
                        echo 'No Submitted';
                    }
                    echo '</div>';
                    echo '<div class="modal-footer">';
                    echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '</div>';
                echo '</div>';
            } else {
                echo "<div class='card-body'>";
                echo "<p class='text-center'>You have no tasks scheduled for today.</p>";
                echo "</div>";
            }
            // ปิดการเชื่อมต่อกับฐานข้อมูล
            $conn->close();
            ?>
        </div>

    </div>
    <!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<script src="vendor/jquery/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // เมื่อมีการเลือกไฟล์
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop(); // ดึงชื่อไฟล์ออกมาจาก path
            $(this).next('.custom-file-label').html(fileName); // แสดงชื่อไฟล์ใน label
        });
    });
</script>

<?php
include 'includes/scripts.php';
include 'includes/footer.php';
?>