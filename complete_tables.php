<?php
session_start();
include 'includes/header.php';

// ตรวจสอบบทบาทของผู้ใช้
if (!isset($_SESSION['role_id']) || !isset($_SESSION['user_id'])) {
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
$current_user_id = $_SESSION['user_id'];

// เลือก Navbar ตามบทบาทของผู้ใช้
switch ($role_id) {
    case '1':
        include 'includes/navbar.php';
        break;
    case '2':
        include 'includes/headmaid_navbar.php';
        break;
    case '3':
        include 'includes/maid_navbar.php';
        break;
    default:
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
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $translations['data_table']; ?></h6>
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

        // สร้างคำสั่ง SQL เพื่อดึงข้อมูลเฉพาะที่มี start_date ก่อนวันนี้ และเรียงจากใหม่สุดไปเก่าสุด
        $today = date('Y-m-d');

        if ($role_id == 1 || $role_id == 2) {
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
                        u.fullname AS user_fullname,
                        r.room_name
                    FROM task t
                    INNER JOIN users u ON t.user_id = u.user_id
                    LEFT JOIN room r ON t.room_id = r.room_id
                    WHERE t.end_date < '$today'
                    ORDER BY t.end_date DESC";
        } else {
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
                        u.fullname AS user_fullname,
                        r.room_name
                    FROM task t
                    INNER JOIN users u ON t.user_id = u.user_id
                    LEFT JOIN room r ON t.room_id = r.room_id
                    WHERE t.end_date < '$today'
                    AND t.user_id = $current_user_id
                    ORDER BY t.end_date DESC";
        }

        $result = $conn->query($sql);

        if ($result === false) {
            die('Error: ' . $conn->error);
        }

        if ($result->num_rows > 0) {
            echo '<div class="card-body">';
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>' . $translations['date'] . '</th>';
            echo '<th>' . $translations['floor'] . '</th>';
            echo '<th>' . $translations['rooms'] . '</th>';
            echo '<th>' . $translations['status'] . '</th>';
            echo '<th>' . $translations['toilet_status'] . '</th>';
            if ($role_id == 1 || $role_id == 2) {
                echo '<th>' . $translations['user'] . '</th>';  // Display User column only for roles 1 and 2
            }
            echo '<th>' . $translations['action_table'] . '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            while ($row = $result->fetch_assoc()) {
                // แปลงค่า ID เป็นข้อความที่อ่านได้
                if ($row['status_id'] == 1) {
                    $status = $translations['ready'];
                } elseif ($row['status_id'] == 2) {
                    $status = $translations['not_ready'];
                } elseif ($row['status_id'] == 3) {
                    $status = $translations['waiting'];
                } else {
                    $status = '-';
                }
                
                if ($row['toilet_status_id'] == 1) {
                    $toilet_status = $translations['ready'];
                } elseif ($row['toilet_status_id'] == 2) {
                    $toilet_status = $translations['not_ready'];
                } elseif ($row['toilet_status_id'] == 3) {
                    $toilet_status = $translations['waiting'];
                } else {
                    $toilet_status = '-';
                }
                
                echo '<tr>';
                echo '<td>' . ($row["end_date"] ?? '-') . '</td>';
                echo '<td>' . ($row["floor_id"] ?? '-') . '</td>';
                echo '<td>' . ($row["room_name"] ?? '-') . '</td>';
                echo '<td>' . $status . '</td>';
                echo '<td>' . $toilet_status . '</td>';
                if ($role_id == 1 || $role_id == 2) {
                    echo '<td>' . ($row["user_fullname"] ?? '-') . '</td>';
                }
                echo '<td>';
                if (!empty($row["image"])) {
                    echo '<button type="button" class="btn btn-info btn-circle btn-sm" data-toggle="modal" data-target="#imageModal' . $row["task_id"] . '"><i class="fas fa-image"></i></button>';
                } else {
                    echo '<button type="button" class="btn btn-secondary btn-circle btn-sm" disabled><i class="fas fa-image"></i></button>';
                }
                echo '</td>';
                echo '</tr>';

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
                echo !empty($row["image"]) ? '<img src="upload/' . $row["image"] . '" class="img-fluid" alt="Image">' : 'No Image Available';
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
            echo "<p class='text-center'>You have no tasks that have already passed.</p>";
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
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
