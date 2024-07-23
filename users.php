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
    <h1 class="h3 mb-2 text-gray-800">Users</h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="modal fade" id="addadminprofile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add Users</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="code.php" method="POST">

                            <div class="modal-body">

                                <div class="form-group">
                                    <label> Fullname </label>
                                    <input type="text" name="fullname" class="form-control"
                                        placeholder="Enter Fullname">
                                </div>
                                <div class="form-group">
                                    <label> Username </label>
                                    <input type="text" name="username" class="form-control"
                                        placeholder="Enter Username">
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Enter Password">
                                </div>
                                <div class="form-group">
                                    <label> Role </label>
                                    <select name="role_id" class="form-control">
                                        <option value="">--- Please select ---</option>
                                        <option value="1">Admin</option>
                                        <option value="2">หัวหน้าแม่บ้าน</option>
                                        <option value="3">แม่บ้าน</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status_id" class="form-control">
                                        <option value="">--- Please select ---</option>
                                        <option value="1">พร้อม</option>
                                        <option value="3">ลา</option>
                                    </select>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" name="registerbtn" class="btn btn-primary">Save</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addadminprofile">
                Add Users
            </button>


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
        $sql = "SELECT * FROM users";
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
            echo '<th>Action</th>';
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
                } else {
                    $status = 'ลา';
                }

                echo '<tr>';
                echo '<td>' . htmlspecialchars($row["fullname"], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row["username"], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . $role . '</td>';
                echo '<td>' . htmlspecialchars($row["timestamp"], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . $status . '</td>';
                echo '<td>
                    <button class="btn btn-primary btn-circle btn-sm mr-1" data-toggle="modal" data-target="#editadminprofile' . $row["user_id"] . '"><i class="fas fa-edit"></i></button>
                    <form action="code.php" method="POST" style="display:inline;">
                        <input type="hidden" name="deleteUserId" value="' . $row["user_id"] . '">
                        <button type="submit" class="btn btn-danger btn-circle btn-sm" onclick="return confirm(\'Are you sure you want to delete this user?\')"><i class="fas fa-trash"></i></button>
                    </form>
                </td>';
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
                echo '<input type="hidden" name="user_id" value="' . $row["user_id"] . '">';
                echo '<div class="form-group">';
                echo '<label>Fullname</label>';
                echo '<input type="text" name="fullname" class="form-control" value="' . htmlspecialchars($row["fullname"], ENT_QUOTES, 'UTF-8') . '">';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label>Username</label>';
                echo '<input type="text" name="username" class="form-control" value="' . htmlspecialchars($row["username"], ENT_QUOTES, 'UTF-8') . '">';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label>Password</label>';
                echo '<input type="password" name="password" class="form-control" value="' . htmlspecialchars($row["password"], ENT_QUOTES, 'UTF-8') . '">';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label>Role</label>';
                echo '<select name="role_id" class="form-control">';
                echo '<option value="1" ' . ($row["role_id"] == 1 ? "selected" : "") . '>Admin</option>';
                echo '<option value="2" ' . ($row["role_id"] == 2 ? "selected" : "") . '>Headmaid</option>';
                echo '<option value="3" ' . ($row["role_id"] == 3 ? "selected" : "") . '>Maid</option>';         
                echo '</select>';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label>Status</label>';
                echo '<select name="status_id" class="form-control">';
                echo '<option value="1" ' . ($row["status_id"] == 1 ? "selected" : "") . '>พร้อม</option>';
                echo '<option value="3" ' . ($row["status_id"] == 3 ? "selected" : "") . '>ลา</option>';
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