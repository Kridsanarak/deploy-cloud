<?php
include 'includes/header.php';
include 'includes/navbar.php';
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">DataTables</h6>
        </div>
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

        // สร้างคำสั่ง SQL เพื่อดึงข้อมูล
        $sql = "SELECT 
    t.task_title,
    t.task_description,
    t.start_date,
    u.fullname AS user_fullname,
    t.floor_number,	
    t.room_number,	
    t.room_status,
    t.room_type,	
    t.toilet_gender,
    t.toilet_status	 FROM task t
    INNER JOIN users u ON t.user_id = u.user_id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<div class="card-body">';
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Title</th>';
            echo '<th>Description</th>';
            echo '<th>Start Date</th>';
            echo '<th>User</th>';
            echo '<th>Floor</th>';
            echo '<th>Room</th>';
            echo '<th>Room Status</th>';
            echo '<th>Type</th>';
            echo '<th>Toilet Gender</th>';
            echo '<th>Toilet Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . ($row["task_title"] ?? '-') . '</td>';
                echo '<td>' . ($row["task_description"] ?? '-') . '</td>';
                echo '<td>' . ($row["start_date"] ?? '-') . '</td>';
                echo '<td>' . ($row["user_fullname"] ?? '-') . '</td>';
                echo '<td>ชั้น ' . ($row["floor_number"] ?? '-') . '</td>';
                echo '<td>' . ($row["room_number"] ?? '-') . '</td>';
                echo '<td>' . ($row["room_type"] ?? '-') . '</td>';
                echo '<td>' . ($row["room_status"] ?? '-') . '</td>';
                echo '<td>' . ($row["toilet_gender"] ?? '-') . '</td>';
                echo '<td>' . ($row["toilet_status"] ?? '-') . '</td>';
                echo '</tr>';
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