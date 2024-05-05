<?php
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 mb-0 text-gray-650">Dashboard</h1>
    </div>

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
    $sql_weekly_room_count = "SELECT COUNT(task_id) AS room_count FROM task WHERE WEEK(start_date) = WEEK(NOW())";
    $result_weekly_room_count = $conn->query($sql_weekly_room_count);


    $sql_total_room_count = "SELECT COUNT(task_id) AS total_rooms FROM task WHERE WEEK(start_date) = WEEK(NOW())";
    $result_total_room_count = $conn->query($sql_total_room_count);

    // สร้างคำสั่ง SQL เพื่อนับจำนวนห้องที่ทำความสะอาดแล้ว (โดยใช้เงื่อนไขเฉพาะที่ room_status เป็น Ready)
    $sql_cleaned_room_count = "SELECT COUNT(task_id) AS cleaned_rooms FROM task WHERE room_status = 'Ready'";
    $result_cleaned_room_count = $conn->query($sql_cleaned_room_count);


    $sql_complete_room_count = "SELECT COUNT(task_id) AS complete_rooms FROM task WHERE room_status = 'Ready'";
    $result_complete_room_count = $conn->query($sql_complete_room_count);

    $sql_top_floor_count = "SELECT floor_number, COUNT(*) AS floor_count
                    FROM task
                    GROUP BY floor_number
                    ORDER BY floor_count DESC
                    LIMIT 2";
    $result_top_floor_count = $conn->query($sql_top_floor_count);


    $floor_numbers = array();
    if ($result_top_floor_count && $result_top_floor_count->num_rows > 0) {
        while ($row = $result_top_floor_count->fetch_assoc()) {
            $floor_numbers[] = $row['floor_number'];
        }
    }

    ?>

    <div class="row">
        <!-- Earnings (Weekly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"
                                style="font-family: Prompt; font-size: 14.5px;">
                                จำนวนห้องที่ใช้สัปดาห์นี้</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                if ($result_weekly_room_count && $result_weekly_room_count->num_rows > 0) {
                                    while ($row = $result_weekly_room_count->fetch_assoc()) {
                                        echo '<span style="font-family: Prompt;">' . $row["room_count"] . ' ห้อง</span>';
                                    }
                                } else {
                                    echo "0";
                                }
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

        <!-- Cleaned Rooms Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1"
                                style="font-family: Prompt; font-size: 14.5px;">
                                ทำความสะอาดแล้ว</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                if ($result_cleaned_room_count && $result_cleaned_room_count->num_rows > 0) {
                                    while ($row = $result_cleaned_room_count->fetch_assoc()) {
                                        echo '<span style="font-family: Prompt;">' . $row["cleaned_rooms"] . ' ห้อง</span>';
                                    }
                                } else {
                                    echo "0";
                                }
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
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1"
                                style="font-family: Prompt; font-size: 14.5px;">คิดเป็น
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        <?php
                                        if ($result_complete_room_count && $result_complete_room_count->num_rows > 0) {
                                            while ($row = $result_complete_room_count->fetch_assoc()) {
                                                $complete_rooms = $row["complete_rooms"];
                                                if ($result_total_room_count && $result_total_room_count->num_rows > 0) {
                                                    while ($row_total = $result_total_room_count->fetch_assoc()) {
                                                        $total_rooms = $row_total["total_rooms"];
                                                        if ($total_rooms > 0) {
                                                            $percentage = ($complete_rooms / $total_rooms) * 100;
                                                            $formatted_percentage = number_format($percentage, 1);
                                                        } else {
                                                            $formatted_percentage = 0.0;
                                                        }
                                                        echo '<span style="font-family: Prompt;">' . $formatted_percentage . ' % </span>';

                                                    }
                                                }
                                            }
                                        } else {
                                            echo "0";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: <?php echo $percentage; ?>%"
                                            aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0"
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

        <!-- Pending Requests Card Example -->
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
                                <?php echo implode(', ', $floor_numbers); ?>
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





<?php
// ปิดการเชื่อมต่อกับ MySQL
$conn->close();
?>


<!-- Content Row -->
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Dropdown Header:</div>
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Notify</h6>
                <div class="dropdown no-arrow">
                    <!-- <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a> -->
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Dropdown Header:</div>
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="myPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> Direct
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Social
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> Referral
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>



</div>

<?php
include ('includes/scripts.php');
include ('includes/footer.php');
?>