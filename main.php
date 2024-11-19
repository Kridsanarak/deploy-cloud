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
$user_id = $_SESSION['user_id'];

// โหลดไฟล์ภาษา
$lang = $_SESSION['lang'] ?? 'en';
$translations = include("lang/lang_{$lang}.php");

// เลือก Navbar ตามบทบาทของผู้ใช้
if ($role_id == '1') {
    include 'includes/navbar.php';
} elseif ($role_id == '2') {
    include 'includes/headmaid_navbar.php';
} elseif ($role_id == '3') {
    include 'includes/maid_navbar.php';
} else {
    echo $translations['error_no_role'];
    exit;
}
include 'includes/calendar.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Content Row -->
    <?php
    date_default_timezone_set('Asia/Bangkok');
    $servername = "db";
    $username = "user";
    $password = "user_password";
    $dbname = "project_maidmanage";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die($translations['db_connection_failed'] . ": " . $conn->connect_error);
    }

    $today = date('Y-m-d');
    $monday = date('Y-m-d', strtotime('monday this week', strtotime($today)));
    $nextSunday = date('Y-m-d', strtotime('sunday next week', strtotime($monday)));
    $user_condition = $role_id == '3' ? "AND user_id = '$user_id'" : "";

    $sql_weekly_room_count = "SELECT COUNT(task_id) AS room_count 
                              FROM task 
                              WHERE start_date >= '$monday' AND start_date <= '$nextSunday' 
                              $user_condition";
    $result_weekly_room_count = $conn->query($sql_weekly_room_count);
    $weekly_room_count = $result_weekly_room_count->fetch_assoc()['room_count'];

    // นับจำนวนห้องทั้งหมดในช่วง 7 วันจากวันจันทร์ถึงวันอาทิตย์
    $sql_total_room_count = "SELECT COUNT(task_id) AS total_rooms 
                             FROM task 
                             WHERE start_date >= '$monday' AND start_date <= '$nextSunday' 
                             $user_condition";
    $result_total_room_count = $conn->query($sql_total_room_count);
    $total_room_count = $result_total_room_count->fetch_assoc()['total_rooms'];

    // นับจำนวนห้องที่ทำความสะอาดแล้วในช่วง 7 วันจากวันจันทร์ถึงวันอาทิตย์
    $sql_cleaned_room_count = "SELECT COUNT(task_id) AS cleaned_rooms 
                               FROM task 
                               WHERE (status_id = 1 OR status_id IS NULL)  
                               AND (toilet_status_id = 1 OR toilet_status_id IS NULL)
                               AND start_date >= '$monday' AND start_date <= '$nextSunday' 
                               $user_condition";
    $result_cleaned_room_count = $conn->query($sql_cleaned_room_count);
    $cleaned_room_count = $result_cleaned_room_count->fetch_assoc()['cleaned_rooms'];

    $sql_complete_room_count = "SELECT COUNT(task_id) AS complete_rooms 
                                FROM task 
                                WHERE (status_id = 1 OR status_id IS NULL)  
                                AND (toilet_status_id = 1 OR toilet_status_id IS NULL)
                                AND start_date >= '$monday' AND start_date <= '$nextSunday' 
                                $user_condition";
    $result_complete_room_count = $conn->query($sql_complete_room_count);
    $complete_room_count = $result_complete_room_count->fetch_assoc()['complete_rooms'];

    $sql_top_floor_count = "SELECT floor_id, COUNT(*) AS floor_count
                            FROM task
                            WHERE start_date >= '$monday' AND start_date <= '$nextSunday'
                            $user_condition
                            GROUP BY floor_id
                            ORDER BY floor_count DESC
                            LIMIT 3";
    $result_top_floor_count = $conn->query($sql_top_floor_count);

    $floor_ids = array();
    if ($result_top_floor_count && $result_top_floor_count->num_rows > 0) {
        while ($row = $result_top_floor_count->fetch_assoc()) {
            $floor_ids[] = $row['floor_id'];
        }
    }
    ?>

    <div class="row">
        <!-- Number of Rooms Used This Week Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"
                                style="font-family: Prompt; font-size: 14.5px;">
                                <?php echo $translations['rooms_used_this_week']; ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                echo '<span style="font-family: Prompt;">' . ($weekly_room_count ? $weekly_room_count : '0') . ' ' . $translations['rooms'] . '</span>';
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cleaned Rooms Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1"
                                style="font-family: Prompt; font-size: 14.5px;">
                                <?php echo $translations['cleaned_rooms']; ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                echo '<span style="font-family: Prompt;">' . ($cleaned_room_count ? $cleaned_room_count : '0') . ' ' . $translations['rooms'] . '</span>';
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completion Percentage Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1"
                                style="font-family: Prompt; font-size: 14.5px;">
                                <?php echo $translations['completion_percentage']; ?>
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        <?php
                                        $percentage = 0.0;

                                        // ตรวจสอบว่าผลลัพธ์ของการคำนวณมีข้อมูลหรือไม่
                                        if ($total_room_count > 0) {
                                            // ตรวจสอบและดึงข้อมูลจำนวนห้องที่เสร็จสมบูรณ์
                                            $complete_rooms = $complete_room_count;
                                            // คำนวณเปอร์เซ็นต์
                                            $percentage = ($complete_rooms / $total_room_count) * 100;
                                        }
                                        $formatted_percentage = number_format($percentage, 2);
                                        ?>
                                        <span style="font-family: Prompt;"><?php echo $formatted_percentage; ?> %</span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: <?php echo $formatted_percentage; ?>%"
                                            aria-valuenow="<?php echo $formatted_percentage; ?>" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Special Care Floors Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1"
                                style="font-family: Prompt; font-size: 14.5px;">
                                <?php echo $translations['special_care_floors']; ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                echo implode(', ', $floor_ids);
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Calendar -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo $translations['calendar']; ?></h6>
                </div>
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>

        <!-- Today Notify -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo $translations['today_notify']; ?></h6>
                </div>
                <div class="card-body">
                    <div class="accordion" id="taskAccordion">
                        <?php
                        // กำหนดเขตเวลา
                        date_default_timezone_set('Asia/Bangkok');
                        $today = date('Y-m-d');

                        // สร้างคำสั่ง SQL เพื่อดึงข้อมูลของทุก user_id และรวมตาราง room_type
                        if ($role_id == '3') {
                            $user_id = $_SESSION['user_id']; // รับ user_id จาก session
                        
                            // สร้างคำสั่ง SQL เพื่อดึงข้อมูลของทุก user_id และรวมตาราง room_type โดยแสดงเฉพาะ user_id ของผู้ใช้ที่มี role_id = 3
                            $sql = "SELECT 
                                t.task_id,
                                t.start_date,
                                t.end_date,
                                u.fullname AS user_fullname,
                                t.floor_id,
                                t.room_id,
                                t.status_id,
                                t.toilet_status_id,
                                t.image,
                                r.room_name,
                                r.room_type_id,
                                rt.room_type_name
                            FROM task t
                            INNER JOIN users u ON t.user_id = u.user_id
                            LEFT JOIN room r ON t.room_id = r.room_id
                            LEFT JOIN room_type rt ON r.room_type_id = rt.room_type_id
                            WHERE t.start_date = '$today' AND t.user_id = '$user_id'
                            ORDER BY t.start_date ASC";
                        } else {
                            // คำสั่ง SQL สำหรับผู้ใช้ที่มี role_id ไม่ใช่ 3
                            $sql = "SELECT 
                                t.task_id,
                                t.start_date,
                                t.end_date,
                                u.fullname AS user_fullname,
                                t.floor_id,
                                t.room_id,
                                t.status_id,
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
                        }

                        // รันคำสั่ง SQL
                        $result = $conn->query($sql);
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
                                echo 'Date: ' . $row["start_date"] . '<br>';
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

                                // แสดงภาพหากมี
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
                            echo $translations['no_tasks_today'];
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ปิดการเชื่อมต่อกับ MySQL -->
    <?php
    $conn->close();
    ?>


    <?php
    include ('includes/scripts.php');
    include ('includes/footer.php');
    ?>