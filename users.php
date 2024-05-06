<?php
include 'includes/header.php';
include 'includes/navbar.php';
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
                                    <select name="role" class="form-control">
                                        <option value="admin">Admin</option>
                                        <option value="maid">แม่บ้าน</option>
                                        <option value="headmaid">หัวหน้าแม่บ้าน</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="พร้อม">พร้อม</option>
                                        <option value="ลา">ลา</option>
                                        <option value="ไม่พร้อม">ไม่พร้อม</option>
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
        $sql = "SELECT user_id, fullname, username, password, role, lasttime_Login, status FROM users";
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
            echo '<th>Password</th>';
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
                echo '<td>' . $row["password"] . '</td>';
                echo '<td>' . $row["role"] . '</td>';
                echo '<td>' . $row["lasttime_Login"] . '</td>';
                echo '<td>' . $row["status"] . '</td>';
                // เพิ่มปุ่ม edit user
                echo '<td><button class="btn btn-primary btn-circle btn-sm" data-toggle="modal" data-target="#editadminprofile' . $row["user_id"] . '"><i class="fas fa-edit"></i></button></td>';
                // เพิ่มปุ่ม delete user
                echo '<td><button class="btn btn-danger btn-circle btn-sm" onclick="deleteUser(' . $row["user_id"] . ')"><i class="fas fa-trash"></i></button></td>';
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
                echo '<label>Password</label>';
                echo '<input type="text" name="password" class="form-control" value="' . $row["password"] . '">';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label>Role</label>';
                echo '<select name="role" class="form-control">';
                echo '<option value="admin" ' . ($row["role"] == "admin" ? "selected" : "") . '>Admin</option>';
                echo '<option value="maid" ' . ($row["role"] == "maid" ? "selected" : "") . '>แม่บ้าน</option>';
                echo '<option value="headmaid" ' . ($row["role"] == "headmaid" ? "selected" : "") . '>หัวหน้าแม่บ้าน</option>';
                echo '</select>';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label>Status</label>';
                echo '<select name="status" class="form-control">';
                echo '<option value="พร้อม" ' . ($row["status"] == "พร้อม" ? "selected" : "") . '>พร้อม</option>';
                echo '<option value="ลา" ' . ($row["status"] == "ลา" ? "selected" : "") . '>ลา</option>';
                echo '<option value="ไม่พร้อม" ' . ($row["status"] == "ไม่พร้อม" ? "selected" : "") . '>ไม่พร้อม</option>';
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
