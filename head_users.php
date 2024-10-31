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

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800"></h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
    <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Users</h6>
        </div>
        <?php
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
        $sql = "SELECT * FROM users WHERE role_id != '1'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // แสดงข้อมูลในรูปแบบของตาราง HTML
            echo '<div class="card-body">';
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Name</th>';
            echo '<th>Username</th>';
            echo '<th>Role</th>';
            echo '<th>Timestamp</th>';
            echo '<th>Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = $result->fetch_assoc()) {
                // แปลง role_id เป็นข้อความที่อ่านได้
                if ($row['role_id'] == 1) {
                    $role = 'Admin';
                } elseif ($row['role_id'] == 2) {
                    $role = 'Headmaid';
                } else {
                    $role = 'Maid';
                }

                // แปลง status_id เป็นข้อความที่อ่านได้
                if ($row['status_id'] == 1) {
                    $status = 'พร้อม';
                } elseif ($row['status_id'] == 2) {
                    $status = 'ไม่พร้อม';
                } else {
                    $status = 'ลา';
                }
                echo '<tr>';
                echo '<td>' . $row["fullname"] . '</td>';
                echo '<td>' . $row["username"] . '</td>';
                echo '<td>' . $role . '</td>';
                echo '<td>' . $row["timestamp"] . '</td>';
                echo '<td>' . $status . '</td>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            echo '</div>';
        } else {
            echo "0 ผลลัพธ์";
        }

        // ปิดการเชื่อมต่อกับฐานข้อมูล
        $conn->close();
        ?>

    </div>

</div>

<script src="vendor/jquery/jquery.min.js"></script>

<?php
include 'includes/scripts.php';
include 'includes/footer.php';
?>