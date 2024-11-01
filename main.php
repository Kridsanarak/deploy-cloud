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
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 mb-0 text-gray-650">Dashboard</h1>
    </div>

    <!-- Content Row -->
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

    // คำนวณวันจันทร์ของสัปดาห์ปัจจุบัน
    $today = date('Y-m-d');
    $monday = date('Y-m-d', strtotime('monday this week', strtotime($today)));

    // คำนวณวันอาทิตย์ของสัปดาห์ถัดไป
    $nextSunday = date('Y-m-d', strtotime('sunday next week', strtotime($monday)));

    // เงื่อนไขการกรองข้อมูลตามบทบาท
    $user_condition = $role_id == '3' ? "AND user_id = '$user_id'" : "";

    // นับจำนวนห้องที่เริ่มต้นในช่วง 7 วันจากวันจันทร์ถึงวันอาทิตย์
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
                           WHERE (status_id = 1 OR status_id IS NULL)  -- 1 คือ 'Ready' หรือ NULL
                           AND (toilet_status_id = 1 OR toilet_status_id IS NULL)  -- 1 คือ 'Ready' หรือ NULL
                               AND start_date >= '$monday' AND start_date <= '$nextSunday' 
                               $user_condition";
    $result_cleaned_room_count = $conn->query($sql_cleaned_room_count);
    $cleaned_room_count = $result_cleaned_room_count->fetch_assoc()['cleaned_rooms'];

    // นับจำนวนห้องที่เสร็จสมบูรณ์ในช่วง 7 วันจากวันจันทร์ถึงวันอาทิตย์
    $sql_complete_room_count = "SELECT COUNT(task_id) AS complete_rooms 
                                FROM task 
                           WHERE (status_id = 1 OR status_id IS NULL)  -- 1 คือ 'Ready' หรือ NULL
                           AND (toilet_status_id = 1 OR toilet_status_id IS NULL)  -- 1 คือ 'Ready' หรือ NULL
                                AND start_date >= '$monday' AND start_date <= '$nextSunday' 
                                $user_condition";
    $result_complete_room_count = $conn->query($sql_complete_room_count);
    $complete_room_count = $result_complete_room_count->fetch_assoc()['complete_rooms'];

    // หาหมายเลขชั้นที่มีจำนวนห้องมากที่สุด 3 อันดับแรกในช่วง 7 วันจากวันจันทร์ถึงวันอาทิตย์
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
                                จำนวนห้องที่ใช้สัปดาห์นี้
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                // แสดงจำนวนห้องที่ใช้สัปดาห์นี้
                                echo '<span style="font-family: Prompt;">' . ($weekly_room_count ? $weekly_room_count : '0') . ' ห้อง</span>';
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
                                ทำความสะอาดแล้ว
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                // แสดงจำนวนห้องที่ทำความสะอาดแล้ว
                                echo '<span style="font-family: Prompt;">' . ($cleaned_room_count ? $cleaned_room_count : '0') . ' ห้อง</span>';
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
                                คิดเป็น
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        <?php
                                        // เริ่มต้นค่าเปอร์เซ็นต์เป็น 0.0
                                        $percentage = 0.0;

                                        // ตรวจสอบว่าผลลัพธ์ของการคำนวณมีข้อมูลหรือไม่
                                        if ($total_room_count > 0) {
                                            // ตรวจสอบและดึงข้อมูลจำนวนห้องที่เสร็จสมบูรณ์
                                            $complete_rooms = $complete_room_count;
                                            // คำนวณเปอร์เซ็นต์
                                            $percentage = ($complete_rooms / $total_room_count) * 100;
                                        }

                                        // รูปแบบเปอร์เซ็นต์
                                        $formatted_percentage = number_format($percentage, 1);
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
                                ชั้นที่ต้องดูแลเป็นพิเศษ
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                // แสดงหมายเลขชั้นที่ต้องดูแลเป็นพิเศษ
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
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Calendar</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>

        <!-- Today Notify -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Today Notify</h6>
                </div>
                <!-- Card Body -->
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
                            echo "No tasks found for today.";
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