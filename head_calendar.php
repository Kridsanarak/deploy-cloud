<?php
session_start();
include 'includes/header.php';
include 'includes/calendar.php';

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
// กำหนดตัวแปร $user_id
$user_id = $_SESSION['user_id'];

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

// ฟังก์ชันสำหรับการรันคำสั่ง SQL
function executeQuery($conn, $sql)
{
    return $conn->query($sql);
}

date_default_timezone_set('Asia/Bangkok');
// การกำหนดค่าในการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_maidmanage";

// การเชื่อมต่อกับ MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

// สร้างคำสั่ง SQL เพื่อดึงข้อมูลเฉพาะช่วงวันที่ที่กำหนด
$today = date('Y-m-d');
$nextWeek = date('Y-m-d', strtotime('+7 days'));

// นับจำนวนงาน (ห้อง) ในช่วง 7 วันจากวันนี้
$sql_weekly_room_count = "SELECT COUNT(task_id) AS room_count 
                          FROM task 
                          WHERE start_date BETWEEN '$today' AND '$nextWeek'";
$result_weekly_room_count = executeQuery($conn, $sql_weekly_room_count);
$weekly_room_count = $result_weekly_room_count->fetch_assoc()['room_count'];

// นับจำนวนห้องทั้งหมดในช่วง 7 วันจากวันนี้
$result_total_room_count = executeQuery($conn, $sql_weekly_room_count);
$total_room_count = $result_total_room_count->fetch_assoc()['room_count'];

// นับจำนวนห้องที่ทำความสะอาดแล้ว (สถานะ Ready) ในช่วง 7 วันจากวันนี้
$sql_cleaned_room_count = "SELECT COUNT(task_id) AS cleaned_rooms 
                           FROM task 
                           WHERE (status_id = 1 OR status_id IS NULL)  -- 1 คือ 'Ready' หรือ NULL
                           AND (toilet_status_id = 1 OR toilet_status_id IS NULL)  -- 1 คือ 'Ready' หรือ NULL
                           AND start_date BETWEEN '$today' AND '$nextWeek'";
$result_cleaned_room_count = executeQuery($conn, $sql_cleaned_room_count);
$cleaned_room_count = $result_cleaned_room_count->fetch_assoc()['cleaned_rooms'];

// นับจำนวนห้องที่เสร็จสมบูรณ์ (สถานะ Ready) ในช่วง 7 วันจากวันนี้
$complete_room_count = $cleaned_room_count;

// นับจำนวนงานที่จัดกลุ่มตามหมายเลขชั้นในช่วง 7 วันจากวันนี้
$sql_floor_user = "SELECT t.floor_id AS floor_number, COUNT(*) AS floor_count
                   FROM task t
                   WHERE start_date BETWEEN '$today' AND '$nextWeek' 
                   GROUP BY t.floor_id
                   ORDER BY floor_count DESC";
$result_floor_user = executeQuery($conn, $sql_floor_user);

// ดึงหมายเลขชั้นมาใส่ใน array
$floor_numbers = array();
if ($result_floor_user && $result_floor_user->num_rows > 0) {
    while ($row = $result_floor_user->fetch_assoc()) {
        $floor_numbers[] = $row['floor_number'];
    }
}
?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Content Row -->
    <div class="row">

        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Work This Week</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <?php
                    $sql = "SELECT 
                t.task_id,
                t.start_date,
                t.end_date,
                u.fullname AS user_fullname,
                t.floor_id,   
                t.room_id,    
                t.status_id,
                t.toilet_gender_id,
                t.toilet_status_id,
                t.image,
                t.user_id
            FROM task t
            INNER JOIN users u ON t.user_id = u.user_id
            WHERE t.start_date BETWEEN '$today' AND '$nextWeek' 
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
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . ($row["start_date"] ?? '-') . '</td>';
                            echo '<td>' . ($row["end_date"] ?? '-') . '</td>';
                            echo '<td>' . ($row["user_fullname"] ?? '-') . '</td>';
                            echo '<td>' . ($row["floor_id"] ?? '-') . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                        echo '</div>';
                    } else {
                        echo "No tasks found for this week.";
                    }
                    ?>
                </div>

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
                            <div class="modal-body">Select "Logout" below if you are ready to end your current session.
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                <a class="btn btn-primary" href="index.php">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Today Work Detail</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="accordion" id="taskAccordion">
                        <style>
                            .card-body p {
                                font-family: 'Prompt', sans-serif;
                                font-size: 16px;
                                color: #333;
                                padding: 10px;
                            }
                        </style>

                        <?php
                        // กำหนดเขตเวลา
                        date_default_timezone_set('Asia/Bangkok');
                        $today = date('Y-m-d');

                        // สร้างคำสั่ง SQL เพื่อดึงข้อมูลของทุก user_id และรวมตาราง room_type
                        $sql = "SELECT 
            t.task_id,
            t.start_date,
            t.end_date,
            u.fullname AS user_fullname,
            t.floor_id,
            t.room_id,
            t.status_id,
            t.toilet_gender_id,
            t.toilet_status_id,
            t.image,
            r.room_name,
            r.room_type_id,
            rt.room_type_name
        FROM task t
        INNER JOIN users u ON t.user_id = u.user_id
        LEFT JOIN room r ON t.room_id = r.room_id
        LEFT JOIN room_type rt ON r.room_type_id = rt.room_type_id
        WHERE t.start_date = '$today'
        ORDER BY t.start_date ASC";

                        // รันคำสั่ง SQL
                        $result = $conn->query($sql);

                        // ตรวจสอบว่ามีข้อมูลหรือไม่
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<div class="card">';
                                echo '<div class="card-header" id="heading' . $row["task_id"] . '">';
                                echo '<h2 class="mb-0">';
                                echo '<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse' . $row["task_id"] . '" aria-expanded="true" aria-controls="collapse' . $row["task_id"] . '">';

                                // กำหนดไอคอนสถานะ
                                $status_icon = '';
                                if (($row["status_id"] == 1 || is_null($row["status_id"])) && ($row["toilet_status_id"] == 1 || is_null($row["toilet_status_id"]))) {
                                    $status_icon = '<i class="fas fa-check-circle text-success"></i>'; // ไอคอนสีเขียว
                                } else {
                                    $status_icon = '<i class="fas fa-exclamation-circle text-danger"></i>'; // ไอคอนสีแดง
                                }

                                echo "Floor " . $row["floor_id"] . ' - ' . $row["user_fullname"] . ' ' . $status_icon;
                                echo '</button>';
                                echo '</h2>';
                                echo '</div>';
                                echo '<div id="collapse' . $row["task_id"] . '" class="collapse" aria-labelledby="heading' . $row["task_id"] . '" data-parent="#taskAccordion">';
                                echo '<div class="card-body">';
                                echo '<p>';
                                echo 'รายละเอียด:<br>';
                                echo 'เริ่ม: ' . $row["start_date"] . '<br>';
                                echo 'สิ้นสุด: ' . $row["end_date"] . '<br>';
                                echo 'ชั้น: ' . $row["floor_id"] . '<br>';
                                echo 'ห้อง: ' . ($row["room_name"] ?? '-') . '<br>';
                                echo 'สถานะ: ';

                                // แสดงสถานะของงาน
                                switch ($row["status_id"]) {
                                    case 1:
                                        echo 'Ready';
                                        break;
                                    case 2:
                                        echo 'Not Ready';
                                        break;
                                    case 3:
                                        echo 'Waiting';
                                        break;
                                    default:
                                        echo '-';
                                        break;
                                }
                                echo '<br>';

                                // แสดงประเภทห้อง
                                echo 'ประเภท: ' . ($row["room_type_name"] ?? '-') . '<br>';

                                // แสดงห้องน้ำ
                                echo 'ห้องน้ำ: ';
                                switch ($row["toilet_gender_id"]) {
                                    case 1:
                                        echo 'Male';
                                        break;
                                    case 2:
                                        echo 'Female';
                                        break;
                                    case 3:
                                        echo 'Both';
                                        break;
                                    default:
                                        echo '-';
                                        break;
                                }
                                echo '<br>';

                                // แสดงสถานะห้องน้ำ
                                echo 'สถานะห้องน้ำ: ';
                                switch ($row["toilet_status_id"]) {
                                    case 1:
                                        echo 'Ready';
                                        break;
                                    case 2:
                                        echo 'Not Ready';
                                        break;
                                    case 3:
                                        echo 'Waiting';
                                        break;
                                    default:
                                        echo '-';
                                        break;
                                }
                                echo '<br>';

                                if (!empty($row["image"])) {
                                    $upload_dir = "upload/";
                                    $image_path = $upload_dir . $row["image"];
                                    echo '<img src="' . $image_path . '" class="img-thumbnail" alt="Image" style="max-width: 100px; max-height: 100px;">';
                                } else {
                                    echo 'No Image';
                                }
                                echo '</p>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo "No tasks found for today.";
                        }
                        ?>


                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php
    // ปิดการเชื่อมต่อกับ MySQL
    $conn->close();
    ?>
</div>

<script src="vendor/jquery/jquery.min.js"></script>

<?php
include ('includes/scripts.php');
include ('includes/footer.php');
?>