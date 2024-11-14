<?php
session_start();
include 'includes/header.php';

// ตรวจสอบบทบาทของผู้ใช้
if (!isset($_SESSION['role_id']) || !isset($_SESSION['user_id'])) {
    echo "
    <script type='text/javascript'>
        alert('ไม่สามารถระบุบทบาทของผู้ใช้ได้');
        setTimeout(function() {
            window.location.href = 'login_send.php';
        }, 100);
    </script>
    ";
    exit;
}

// กำหนดค่าตัวแปร $role_id และ $current_user_id จาก Session
$role_id = $_SESSION['role_id'];
$current_user_id = $_SESSION['user_id'];

?>

<!-- Begin Page Content -->
<div class="container-fluid" style="margin-top: 1.5rem;">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Task :
                <span class="mr-2 d-none d-lg-inline text-black-600 bold">
                    <?php echo $_SESSION['full_name']; ?>
                </span>
            </h6>
            <a href="main.php" class="btn btn-primary">
                <i class="bi bi-house"></i> <!-- ใช้ Bootstrap Icon ที่ชื่อ "house-door" -->
            </a>
        </div>
        
        <?php
        date_default_timezone_set('Asia/Bangkok');
        
        // การกำหนดค่าในการเชื่อมต่อฐานข้อมูล
        $servername = "db";
        $username = "user";
        $password = "user_password";
        $dbname = "project_maidmanage";
        
        // การเชื่อมต่อกับ MySQL
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // ตรวจสอบการเชื่อมต่อ
        if ($conn->connect_error) {
            die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
        }

        // สร้างคำสั่ง SQL เพื่อดึงข้อมูลเฉพาะช่วงวันที่ที่กำหนด
        $today = date('Y-m-d');
        $nextWeek = date('Y-m-d', strtotime('+0 days'));
        
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
        WHERE t.start_date >= '$today' AND t.start_date <= '$nextWeek'
        AND t.user_id = $current_user_id
        ORDER BY t.start_date ASC";
                
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
            echo '<th>Floor</th>';
            echo '<th>Room</th>';
            echo '<th>Action</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            while ($row = $result->fetch_assoc()) {
                // แปลงค่า ID เป็นข้อความที่อ่านได้
                $status = $row['status_id'] == 1 ? 'Ready' : ($row['status_id'] == 2 ? 'Waiting' : ($row['status_id'] == 3 ? 'Not Ready' : '-'));
                $toilet_status = $row['toilet_status_id'] == 1 ? 'Ready' : ($row['toilet_status_id'] == 2 ? 'Waiting' : ($row['toilet_status_id'] == 3 ? 'Not Ready' : '-'));

                echo '<tr>';
                echo '<td>' . ($row["floor_id"]) . '</td>';
                echo '<td>' . ($row["room_name"] ?? 'All Room' ) . '</td>';
                echo '<td>';
                if (($status == 'Ready' || is_null($status)) && ($toilet_status == 'Ready' || is_null($toilet_status))) {
                    echo '<button type="button" class="btn btn-secondary btn-circle btn-sm" disabled><i class="fas fa-paper-plane"></i></button>';
                } elseif ($_SESSION["role_id"] == 'maid' && $row["user_id"] != $_SESSION["user_id"]) {
                    echo '<button type="button" class="btn btn-secondary btn-circle btn-sm" disabled><i class="fas fa-paper-plane"></i></button>';
                } else {
                    echo '<button type="button" class="btn btn-success btn-circle btn-sm" data-toggle="modal" data-target="#sendTaskModal' . $row["task_id"] . '"><i class="fas fa-paper-plane"></i></button>';
                }
                echo '</td>';
                echo '</tr>';
                
                // Modal สำหรับการส่งงาน
echo '<div class="modal fade" id="sendTaskModal' . $row["task_id"] . '" tabindex="-1" role="dialog" aria-labelledby="sendTaskModalLabel' . $row["task_id"] . '" aria-hidden="true">';
echo '<div class="modal-dialog" role="document">';
echo '<div class="modal-content">';
echo '<div class="modal-header">';
echo '<h5 class="modal-title" id="sendTaskModalLabel' . $row["task_id"] . '">ส่งงาน</h5>';
echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
echo '<span aria-hidden="true">&times;</span>';
echo '</button>';
echo '</div>';
echo '<form action="code.php" method="POST" enctype="multipart/form-data">';
echo '<div class="modal-body">';
echo '<input type="hidden" name="task_id" value="' . $row['task_id'] . '">';
echo '<input type="hidden" name="status_id" value="1">';  // ตั้งค่าเป็น Ready อัตโนมัติ
echo '<input type="hidden" name="toilet_status_id" value="1">';  // ตั้งค่าเป็น Ready อัตโนมัติ
echo '<div class="form-group">';
echo '<label>Upload Image</label>';
echo '<input type="file" name="image" class="form-control" accept="image/*" required>';  // เพิ่ม required เพื่อบังคับให้อัปโหลดรูป
echo '</div>';
echo '</div>';
echo '<div class="modal-footer">';
echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
echo '<button type="submit" name="send_task_btn" class="btn btn-primary">Submit</button>';
echo '</div>';
echo '</form>';
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
            echo "<p class='text-center'>คุณไม่มีงานที่กำหนดไว้ในวันนี้</p>";
            echo "</div>";
        }
        $conn->close();
        ?>
    </div>

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- ปุ่ม Logout -->
<a class="btn btn-primary" href="#" onclick="showLogoutModal()">Logout</a>

<!-- โครงสร้าง modal สำหรับการยืนยัน Logout -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                Select "Logout" below if you are ready to end your current session.
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript สำหรับแสดง modal -->
<script>
    function showLogoutModal() {
        var logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'), {
            backdrop: 'static'
        });
        logoutModal.show();
    }
</script>

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<?php
include 'includes/footer.php';
include 'includes/scripts.php';
?>
