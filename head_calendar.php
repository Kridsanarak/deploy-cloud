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

    <!-- Content Row -->
    <?php
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

    // สร้างคำสั่ง SQL เพื่อนับจำนวนห้องที่ใช้สัปดาห์นี้
    $sql_weekly_room_count = "SELECT COUNT(task_id) AS room_count FROM task WHERE WEEK(start_date) = WEEK(NOW()) AND user_id = {$_SESSION['user_id']} ";
    $result_weekly_room_count = $conn->query($sql_weekly_room_count);


    $sql_total_room_count = "SELECT COUNT(task_id) AS total_rooms FROM task WHERE WEEK(start_date) = WEEK(NOW()) AND user_id = {$_SESSION['user_id']} ";
    $result_total_room_count = $conn->query($sql_total_room_count);

    // สร้างคำสั่ง SQL เพื่อนับจำนวนห้องที่ทำความสะอาดแล้ว (โดยใช้เงื่อนไขเฉพาะที่ room_status เป็น Ready)
    $sql_cleaned_room_count = "SELECT COUNT(task_id) AS cleaned_rooms FROM task WHERE room_status = 'Ready' AND toilet_status = 'Ready' AND WEEK(start_date) = WEEK(NOW()) AND user_id = {$_SESSION['user_id']} ";
    $result_cleaned_room_count = $conn->query($sql_cleaned_room_count);


    $sql_complete_room_count = "SELECT COUNT(task_id) AS complete_rooms FROM task WHERE room_status = 'Ready' AND toilet_status = 'Ready' AND WEEK(start_date) = WEEK(NOW()) AND user_id = {$_SESSION['user_id']} ";
    $result_complete_room_count = $conn->query($sql_complete_room_count);

    $sql_floor_user = "SELECT t.floor_number, COUNT(*) AS floor_count
    FROM task t
    INNER JOIN users u ON t.user_id = u.user_id
    WHERE WEEK(t.start_date) = WEEK(NOW()) AND u.user_id = {$_SESSION['user_id']} 
    GROUP BY t.floor_number
    ORDER BY floor_count DESC";
    $result_floor_user = $conn->query($sql_floor_user);


    $floor_numbers = array();
    if ($result_floor_user && $result_floor_user->num_rows > 0) {
        while ($row = $result_floor_user->fetch_assoc()) {
            $floor_numbers[] = $row['floor_number'];
        }
    }

    ?>



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
                    date_default_timezone_set('Asia/Bangkok');
                    $today = date("Y-m-d");  // วันที่ปัจจุบัน
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
            WHERE t.user_id = {$_SESSION['user_id']} AND WEEK(start_date) = WEEK(NOW())  -- เพิ่มเงื่อนไขเพื่อกรองวันที่เริ่มต้นหลังจากวันที่ปัจจุบัน
            ORDER BY t.start_date ASC";

                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo '<div class="card-body">';
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>Start Date</th>';
                        echo '<th>Task</th>';
                        echo '<th>Floor</th>';
                        echo '<th>Type</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . ($row["start_date"] ?? '-') . '</td>';
                            echo '<td>' . ($row["task_title"] ?? '-') . '</td>';
                            echo '<td>IF-' . ($row["floor_number"] ?? '-') . '0' . ($row["room_number"] ?? '-') . '</td>';
                            echo '<td>' . ($row["room_type"] ?? '-') . '</td>';

                        }
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                        echo '</div>';
                    } else {
                        echo "0 ผลลัพธ์";
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
                                /* ปรับขนาด font ตามต้องการ */
                                color: #333;
                                /* สีข้อความ */
                                padding: 10px;
                                /* ระยะห่างของข้อความ */
                            }
                        </style>

                        <?php
                        // สร้างคำสั่ง SQL เพื่อดึงข้อมูล
                        date_default_timezone_set('Asia/Bangkok');
                        $today = date('Y-m-d');
                        // $tomorrow = date('Y-m-d', strtotime('+1 day'));
                        $sql = "SELECT * FROM task WHERE user_id = {$_SESSION['user_id']} AND start_date = '$today'";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<div class="card">';
                                echo '<div class="card-header" id="heading' . $row["task_id"] . '">';
                                echo '<h2 class="mb-0">';
                                echo '<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse' . $row["task_id"] . '" aria-expanded="true" aria-controls="collapse' . $row["task_id"] . '">';
                                $status_icon = '';
                                if ($row["room_status"] == 'Ready' && $row["toilet_status"] == 'Ready') {
                                    $status_icon = '<i class="fas fa-check-circle text-success"></i>'; // Green check square icon
                                } else {
                                    $status_icon = '<i class="fas fa-exclamation-circle text-danger"></i>'; // Red square icon
                                }
                                echo "IF-" . $row["floor_number"] . '0' . $row["room_number"] . ' - ' . $row["task_title"] . ' ' . $status_icon;
                                echo '</button>';
                                echo '</h2>';
                                echo '</div>';
                                echo '<div id="collapse' . $row["task_id"] . '" class="collapse" aria-labelledby="heading' . $row["task_id"] . '" data-parent="#taskAccordion">';
                                echo '<div class="card-body">';
                                echo '<p>';
                                echo 'รายละเอียด:<br>';
                                echo 'เริ่ม: ' . $row["start_date"] . '<br>';
                                echo 'ชั้น: ' . $row["floor_number"] . '<br>';
                                echo 'ห้อง: ' . $row["room_number"] . '<br>';
                                echo 'สถานะ: ';
                                switch ($row["room_status"]) {
                                    case 'Ready':
                                        echo 'พร้อม';
                                        break;
                                    case 'Not Ready':
                                        echo 'ไม่พร้อม';
                                        break;
                                    case 'Waiting':
                                        echo 'รอทำความสะอาด';
                                        break;
                                    default:
                                        echo $row["room_status"];
                                        break;
                                }
                                echo '<br>';
                                echo 'ประเภท: ' . $row["room_type"] . '<br>';
                                echo 'ห้องน้ำ: ';
                                if ($row["toilet_gender"] == 'male') {
                                    echo 'ชาย';
                                } elseif ($row["toilet_gender"] == 'female') {
                                    echo 'หญิง';
                                } else {
                                    echo $row["toilet_gender"];
                                }
                                echo '<br>';
                                echo 'สถานะ: ';
                                switch ($row["toilet_status"]) {
                                    case 'Ready':
                                        echo 'ทำความสะอาดแล้ว';
                                        break;
                                    case 'Not Ready':
                                        echo 'ไม่พร้อม';
                                        break;
                                    case 'Waiting':
                                        echo 'รอทำความสะอาด';
                                        break;
                                    default:
                                        echo $row["toilet_status"];
                                        break;
                                }
                                echo '<br>';
                                echo '</p>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo "No tasks found for tomorrow.";
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