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
<div class="container-fluid">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Task</h6>
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
            echo '<th>Status</th>';
            echo '<th>Toilet Status</th>';
            echo '<th>Action</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
        
            while ($row = $result->fetch_assoc()) {
                // แปลงค่า ID เป็นข้อความที่อ่านได้
                $status = '';
                switch ($row['status_id']) {
                    case 1:
                        $status = 'Ready';
                        break;
                    case 2:
                        $status = 'Waiting';
                        break;
                    case 3:
                        $status = 'Not Ready';
                        break;
                    default:
                        $status = '-';
                }

                $toilet_status = '';
                switch ($row['toilet_status_id']) {
                    case 1:
                        $toilet_status = 'Ready';
                        break;
                    case 2:
                        $toilet_status = 'Waiting';
                        break;
                    case 3:
                        $toilet_status = 'Not Ready';
                        break;
                    default:
                        $toilet_status = '-';
                }
        
                echo '<tr>';
                echo '<td>IF-' . ($row["floor_id"] ?? '-') . '</td>';
                echo '<td>' . ($row["room_name"] ?? '-') . '</td>';
                echo '<td>' . $status . '</td>';
                echo '<td>' . $toilet_status . '</td>';
                echo '<td>';
                if (($status == 'Ready' || is_null($status)) && ($toilet_status == 'Ready' || is_null($toilet_status))) {
                        // ถ้า status และ toilet_status เป็น 'Ready' ทั้งคู่
                        echo '<button type="button" class="btn btn-secondary btn-circle btn-sm" disabled><i class="fas fa-paper-plane"></i></button>';
                    } elseif ($_SESSION["role_id"] == 'maid' && $row["user_id"] != $_SESSION["user_id"]) {
                        // ถ้าเป็นหัวหน้าและเป็นงานของตัวเอง
                        echo '<button type="button" class="btn btn-secondary btn-circle btn-sm" disabled><i class="fas fa-paper-plane"></i></button>';
                    } else {
                        // ถ้าไม่ใช่
                        echo '<button type="button" class="btn btn-success btn-circle btn-sm" data-toggle="modal" data-target="#sendTaskModal' . $row["task_id"] . '"><i class="fas fa-paper-plane"></i></button>';
                    }
                    echo '  ';
                    
                    echo '</td>';
                    echo '</tr>';
        
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
                
                // Assuming $row['status_id'] and $row['toilet_status'] are available
                echo '<div class="form-group">';
                echo '<label>Room Status</label>';
                echo '<select name="status_id" class="form-control" required>';
                echo '<option value="">--- Please select ---</option>';
                echo '<option value="1"' . ($row['status_id'] == 1 ? ' selected' : '') . '>Ready</option>';
                echo '<option value="2"' . ($row['status_id'] == 2 ? ' selected' : '') . '>Waiting</option>';
                echo '<option value="3"' . ($row['status_id'] == 3 ? ' selected' : '') . '>Not Ready</option>';
                echo '</select>';
                echo '</div>';
                
                echo '<div class="form-group">';
                echo '<label>Toilet Status</label>';
                echo '<select name="toilet_status_id" class="form-control" required>';
                echo '<option value="">--- Please select ---</option>';
                echo '<option value="1"' . ($row['toilet_status_id'] == 1 ? ' selected' : '') . '>Ready</option>';
                echo '<option value="2"' . ($row['toilet_status_id'] == 2 ? ' selected' : '') . '>Waiting</option>';
                echo '<option value="3"' . ($row['toilet_status_id'] == 3 ? ' selected' : '') . '>Not Ready</option>';
                echo '</select>';
                echo '</div>';

                echo '<div class="form-group">';
                echo '<label>Upload Image</label>';
                echo '<input type="file" name="image" class="form-control" accept="image/*">';
                echo '</div>';

                echo '</div>';
                echo '<div class="modal-footer">';
                echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                echo '<button type="submit" name="send_task_btn" class="btn btn-primary">Submit</button>';
                echo '</div>';
                echo '</form>';
                echo '</div>'; // Close modal-content
                echo '</div>'; // Close modal-dialog
                echo '</div>'; // Close modal
                
                
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
                    echo 'No Image Available';
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
            echo "<p class='text-center'>คุณไม่มีงานที่กำหนดไว้ในวันนี้</p>";
            echo "</div>";
        }
        
        // ปิดการเชื่อมต่อกับฐานข้อมูล
        $conn->close();
        ?>
    </div>

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<script src="vendor/jquery/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        // เมื่อมีการเลือกไฟล์
        $('.custom-file-input').on('change', function () {
            var fileName = $(this).val().split('\\').pop(); // ดึงชื่อไฟล์ออกมาจาก path
            $(this).next('.custom-file-label').html(fileName); // แสดงชื่อไฟล์ใน label
        });
    });
</script>

<!-- Bootstrap core JavaScript-->
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<?php
include 'includes/footer.php';
include 'includes/scripts.php';
?>
