<?php
session_start();
include 'includes/header.php';

// ตรวจสอบบทบาทของผู้ใช้
if (!isset($_SESSION['role'])) {
    echo "ไม่สามารถระบุบทบาทของผู้ใช้ได้";
    exit;
}

// กำหนดค่าตัวแปร $role จาก Session
$role = $_SESSION['role'];

// เลือก Navbar ตามบทบาทของผู้ใช้
if ($role == 'admin') {
    include 'includes/navbar.php';
} elseif ($role == 'headmaid') {
    include 'includes/headmaid_navbar.php';
} elseif ($role == 'maid') {
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
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">DataTables</h6>
        </div>
        <?php
        date_default_timezone_set('Asia/Bangkok');
        // การกำหนดค่าในการเชื่อมต่อฐานข้อมูล
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "project";

        // การเชื่อมต่อกับ MySQL
        $conn = new mysqli($servername, $username, $password, $dbname);

        // ตรวจสอบการเชื่อมต่อ
        if ($conn->connect_error) {
            die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
        }

        // สร้างคำสั่ง SQL เพื่อดึงข้อมูลเฉพาะเดือนปัจจุบัน
        $currentMonth = date('m');
        $currentYear = date('Y');
        $sql = "SELECT 
            t.task_id,
            t.task_title,
            t.start_date,
            t.task_description,
            u.fullname AS user_fullname,
            t.floor_number,   
            t.room_number,    
            t.room_status,
            t.room_type,    
            t.toilet_gender,
            t.toilet_status,
            t.image,
            t.user_id
        FROM task t
        INNER JOIN users u ON t.user_id = u.user_id
        WHERE MONTH(t.start_date) = $currentMonth AND YEAR(t.start_date) = $currentYear
        ORDER BY t.start_date ASC";  // เรียงตาม start_date จากน้อยไปหามาก
        
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<div class="card-body">';
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Start Date</th>';
            echo '<th>Title</th>';
            // echo '<th>Description</th>';
            echo '<th>User</th>';
            echo '<th>Floor</th>';
            echo '<th>Type</th>';
            echo '<th>Room Status</th>';
            echo '<th>Toilet Gender</th>';
            echo '<th>Toilet Status</th>';
            echo '<th>Action</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . ($row["start_date"] ?? '-') . '</td>';
                echo '<td>' . ($row["task_title"] ?? '-') . '</td>';
                // echo '<td>' . ($row["task_description"] ?? '-') . '</td>';
                echo '<td>' . ($row["user_fullname"] ?? '-') . '</td>';
                echo '<td>IF-' . ($row["floor_number"] ?? '-') . '0' . ($row["room_number"] ?? '-') . '</td>';
                echo '<td>' . ($row["room_type"] ?? '-') . '</td>';
                echo '<td>';
                if ($row["room_status"] == 'Ready') {
                    echo 'พร้อม';
                } elseif ($row["room_status"] == 'Not Ready') {
                    echo 'ไม่พร้อม';
                } elseif ($row["room_status"] == 'Waiting') {
                    echo 'รอ';
                } else {
                    echo ($row["room_status"] ?? '-');
                }
                echo '</td>';
                echo '<td>';
                if ($row["toilet_gender"] == 'male') {
                    echo 'ชาย';
                } elseif ($row["toilet_gender"] == 'female') {
                    echo 'หญิง';
                } else {
                    echo ($row["toilet_gender"] ?? '-');
                }
                echo '</td>';

                echo '<td>';
                if ($row["toilet_status"] == 'Ready') {
                    echo 'พร้อม';
                } elseif ($row["toilet_status"] == 'Not Ready') {
                    echo 'ไม่พร้อม';
                } elseif ($row["toilet_status"] == 'Waiting') {
                    echo 'รอ';
                } else {
                    echo ($row["toilet_status"] ?? '-');
                }
                echo '</td>';
                echo '<td>';
                // echo '<button type="button" class="btn btn-success btn-circle btn-sm" data-toggle="modal" data-target="#submitWorkModal' . $row["task_id"] . '"><i class="fas fa-paper-plane"></i></button>';
                // echo '  ';
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
                echo '<label>Task Title</label>';
                echo '<input type="text" name="task_title" class="form-control" value="' . $row["task_title"] . '" readonly>';
                echo '</div>';

                echo '<div class="form-group">';
                echo '<label>Room Status</label>';
                echo '<select name="room_status" class="form-control">';
                echo '<option value="Ready" ' . ($row["room_status"] == "Ready" ? "selected" : "") . '>Ready</option>';
                echo '<option value="Waiting" ' . ($row["room_status"] == "Waiting" ? "selected" : "") . '>Waiting</option>';
                echo '<option value="Not Ready" ' . ($row["room_status"] == "Not Ready" ? "selected" : "") . '>Not Ready</option>';
                echo '</select>';
                echo '</div>';

                // เพิ่ม input field สำหรับแก้ไข toilet_status
                echo '<div class="form-group">';
                echo '<label>Toilet Status</label>';
                echo '<select name="toilet_status" class="form-control">';
                echo '<option value="Ready" ' . ($row["toilet_status"] == "Ready" ? "selected" : "") . '>Ready</option>';
                echo '<option value="Waiting" ' . ($row["toilet_status"] == "Waiting" ? "selected" : "") . '>Waiting</option>';
                echo '<option value="Not Ready" ' . ($row["toilet_status"] == "Not Ready" ? "selected" : "") . '>Not Ready</option>';
                echo '</select>';
                echo '</div>';

                // เพิ่ม input field สำหรับอัปโหลดรูปภาพ
                echo '<div class="form-group">';
                echo '<label>Image</label>';
                echo '<input type="file" name="image" class="form-control">';
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
                // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีรูปภาพหรือไม่
                if (!empty($row["image"])) {
                    // ระบุตำแหน่งของโฟลเดอร์ upload ของคุณ
                    $upload_dir = "upload/";

                    // เรียกใช้ชื่อไฟล์รูปภาพจากฐานข้อมูล
                    $image_name = $row["image"];

                    // รวมตำแหน่งของไฟล์รูปภาพ
                    $image_path = $upload_dir . $image_name;

                    // แสดงรูปภาพ
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

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="index.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>

<?php
include 'includes/scripts.php';
include 'includes/footer.php';
?>
