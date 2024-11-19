<?php
session_start();
include 'includes/header.php';

// เช็ค Role
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

// เก็บค่า Role 
$role_id = $_SESSION['role_id'];

// เลือก Navbar ตาม Role
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

if (isset($_SESSION['status'])) {
    echo "
    <script type='text/javascript'>
        $(document).ready(function() {
            $('#statusModal').modal('show');
            setTimeout(function() {
                $('#statusModal').modal('hide');
            }, 5000); // 5 seconds
        });
    </script>
    ";
}

?>

<div class="container-fluid">
    <div class="card shadow mb-4">

        <!-- Add User -->
        <div class="card-header py-3">
            <div class="modal fade" id="addadminprofile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"><?php echo $translations['add_user']; ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="code.php" method="POST">

                            <div class="modal-body">

                                <div class="form-group">
                                    <label><?php echo $translations['fullname']; ?></label>
                                    <input type="text" name="fullname" class="form-control"
                                        placeholder="<?php echo $translations['enter_fullname']; ?>">
                                </div>
                                <div class="form-group">
                                    <label><?php echo $translations['username']; ?></label>
                                    <input type="text" name="username" class="form-control"
                                        placeholder="<?php echo $translations['enter_username']; ?>">
                                </div>
                                <div class="form-group">
                                    <label><?php echo $translations['password']; ?></label>
                                    <input type="password" name="password" class="form-control"
                                        placeholder="<?php echo $translations['enter_password']; ?>">
                                </div>
                                <div class="form-group">
                                    <label><?php echo $translations['role']; ?></label>
                                    <select name="role_id" class="form-control">
                                        <option value=""><?php echo $translations['please_select']; ?> ---</option>
                                        <option value="1"><?php echo $translations['admin']; ?></option>
                                        <option value="2"><?php echo $translations['head_maid']; ?></option>
                                        <option value="3"><?php echo $translations['maid']; ?></option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label><?php echo $translations['status']; ?></label>
                                    <select name="status_id" class="form-control">
                                        <option value=""><?php echo $translations['please_select']; ?> ---</option>
                                        <option value="1"><?php echo $translations['ready']; ?></option>
                                        <option value="2"><?php echo $translations['not_ready']; ?></option>
                                        <option value="3"><?php echo $translations['leave']; ?></option>
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
                <?php echo $translations['add_user']; ?>
            </button>
            <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="statusModalLabel">Notification</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php
                echo $_SESSION['status'];
                unset($_SESSION['status']);
                ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Add User -->

        <!-- เชื่อม Database -->
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
        $sql = "SELECT * FROM users";
        $result = $conn->query($sql);

        // แสดงตาราง User
        if ($result->num_rows > 0) {
            echo '<div class="card-body">';
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>' . $translations['fullname'] . '</th>';
            echo '<th>' . $translations['username'] . '</th>';
            echo '<th>' . $translations['role'] . '</th>';
            echo '<th>' . $translations['timestamp'] . '</th>';
            echo '<th>' . $translations['status'] . '</th>';
            echo '<th>' . $translations['action'] . '</th>';

            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = $result->fetch_assoc()) {
                if ($row['role_id'] == 1) {
                    $role = $translations['admin'];
                } elseif ($row['role_id'] == 2) {
                    $role = $translations['head_maid'];
                } else {
                    $role = $translations['maid'];
                }

                if ($row['status_id'] == 1) {
                    $status = $translations['ready'];
                } elseif ($row['status_id'] == 3) {
                    $status = $translations['leave'];
                } else {
                    $status = $translations['not_ready'];
                }

                echo '<tr>';
                echo '<td>' . htmlspecialchars($row["fullname"], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row["username"], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . $role . '</td>';
                if (empty($row["timestamp"])) {
                    echo '<td>New User</td>'; 
                } else {
                    echo '<td>' . htmlspecialchars($row["timestamp"], ENT_QUOTES, 'UTF-8') . '</td>';
                }
                echo '<td>' . $status . '</td>';

                // ปุ่ม Edit , Delete
                echo '<td>
                    <button class="btn btn-primary btn-circle btn-sm mr-1" data-toggle="modal" data-target="#editadminprofile' . $row["user_id"] . '"><i class="fas fa-edit"></i></button>
                    <form action="code.php" method="POST" style="display:inline;">
                        <input type="hidden" name="deleteUserId" value="' . $row["user_id"] . '">
                        <button type="submit" class="btn btn-danger btn-circle btn-sm" onclick="return confirm(\'Are you sure you want to delete this user?\')"><i class="fas fa-trash"></i></button>
                    </form>
                </td>';

                // Edit User
                echo '<div class="modal fade" id="editadminprofile' . $row["user_id"] . '" tabindex="-1" role="dialog" aria-labelledby="editAdminProfileLabel' . $row["user_id"] . '" aria-hidden="true">';
                echo '<div class="modal-dialog" role="document">';
                echo '<div class="modal-content">';
                echo '<div class="modal-header">';
                echo '<h5 class="modal-title" id="editAdminProfileLabel' . $row["user_id"] . '">' . $translations['edit_user_profile'] . '</h5>';
                echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button>';
                echo '</div>';
                echo '<form action="edit_user.php" method="POST">';
                echo '<div class="modal-body">';
                echo '<input type="hidden" name="user_id" value="' . $row["user_id"] . '">';
                echo '<div class="form-group">';
                echo '<label>'. $translations['fullname'] .'</label>';
                echo '<input type="text" name="fullname" class="form-control" value="' . htmlspecialchars($row["fullname"], ENT_QUOTES, 'UTF-8') . '">';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label>'. $translations['username'] .'</label>';
                echo '<input type="text" name="username" class="form-control" value="' . htmlspecialchars($row["username"], ENT_QUOTES, 'UTF-8') . '">';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label>'. $translations['password'] .'</label>';
                echo '<input type="password" name="password" class="form-control" value="' . htmlspecialchars($row["password"], ENT_QUOTES, 'UTF-8') . '">';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label>'. $translations['role'] .'</label>';
                echo '<select name="role_id" class="form-control">';
                echo '<option value="1"> ' . $translations['admin'] . '</option>';
                echo '<option value="2"> ' . $translations['head_maid'] . '</option>';
                echo '<option value="3"> ' . $translations['maid'] . '</option>';
                echo '</select>';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label>'. $translations['status'] .'</label>';
                echo '<select name="status_id" class="form-control">';
                echo '<option value="1"> ' . $translations['ready'] . '</option>';
                echo '<option value="2"> ' . $translations['not_ready'] . '</option>';
                echo '<option value="3"> ' . $translations['leave'] . '</option>';
                echo '</select>';
                echo '</div>';
                echo '</div>';
                echo '<div class="modal-footer">';
                echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">' . $translations['cancel'] . '</button>';
                echo '<button type="submit" name="edit_user_btn" class="btn btn-primary">' . $translations['save'] . '</button>';
                echo '</div>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                // End Edit User
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            echo '</div>';
        } else {
            echo "0 ผลลัพธ์";
        }

        $conn->close();
        ?>

    </div>

</div>

<script src="vendor/jquery/jquery.min.js"></script>

<?php
include 'includes/scripts.php';
include 'includes/footer.php';
?>