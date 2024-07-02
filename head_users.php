<?php
include 'includes/header.php';
include 'includes/headmaid_navbar.php';
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
        $sql = "SELECT * FROM users WHERE user_id <> 1";
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
            echo '<th>Lasttime Login</th>';
            echo '<th>Status</th>';
            echo '<th>Action</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row["fullname"] . '</td>';
                echo '<td>' . $row["username"] . '</td>';
                echo '<td>' . $row["role"] . '</td>';
                echo '<td>' . $row["lasttime_login"] . '</td>';
                echo '<td>' . $row["status"] . '</td>';
                // เพิ่มปุ่ม edit user
                echo '<td>
                <button class="btn btn-primary btn-circle btn-sm mr-1" data-toggle="modal" data-target="#editadminprofile' . $row["user_id"] . '"><i class="fas fa-edit"></i></button>
                <button class="btn btn-danger btn-circle btn-sm" onclick="deleteUser(' . $row["user_id"] . ')"><i class="fas fa-trash"></i></button>
                </td>';
                echo '</tr>';

                // เพิ่มโมดัลเชียลแก้ไขผู้ใช้ (edit user modal) ด้วย ID ที่ไม่ซ้ำกันตาม user_id
                echo '<div class="modal fade" id="editadminprofile' . $row["user_id"] . '" tabindex="-1" role="dialog" aria-labelledby="editAdminProfileLabel' . $row["user_id"] . '" aria-hidden="true">';
                echo '<div class="modal-dialog" role="document">';
                echo '<div class="modal-content">';
                echo '<div class="modal-header">';
                echo '<h5 class="modal-title" id="editAdminProfileLabel' . $row["user_id"] . '">Edit User Profile</h5>';
                echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button>';
                echo '</div>';
                echo '<form action="edit_user.php" method="POST">';
                echo '<div class="modal-body">';
                echo '<input type="hidden" name="user_id" value="' . $row["user_id"] . '">'; // ส่งค่า user_id ไปยังหน้า edit_user.php
                echo '<div class="form-group">';
                echo '<label>Fullname</label>';
                echo '<input type="text" name="fullname" class="form-control" value="' . $row["fullname"] . '">';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label>Username</label>';
                echo '<input type="text" name="username" class="form-control" value="' . $row["username"] . '">';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label>Status</label>';
                echo '<select name="status" class="form-control">';
                echo '<option value="พร้อม" ' . ($row["status"] == "พร้อม" ? "selected" : "") . '>พร้อม</option>';
                echo '<option value="ลา" ' . ($row["status"] == "ลา" ? "selected" : "") . '>ลา</option>';
                echo '</select>';
                echo '</div>';
                echo '</div>';
                echo '<div class="modal-footer">';
                echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                echo '<button type="submit" name="edit_user_btn" class="btn btn-primary">Save Changes</button>';
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