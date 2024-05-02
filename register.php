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
        $sql = "SELECT user_id, fullname, password, role, lasttime_Login, status FROM users";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // แสดงข้อมูลในรูปแบบของตาราง HTML
            echo '<div class="card-body">';
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Name</th>';
            echo '<th>Password</th>';
            echo '<th>Role</th>';
            echo '<th>Lasttime Login</th>';
            echo '<th>Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row["fullname"] . '</td>';
                echo '<td>' . $row["password"] . '</td>';
                echo '<td>' . $row["role"] . '</td>';
                echo '<td>' . $row["lasttime_Login"] . '</td>';
                echo '<td>' . $row["status"] . '</td>';
                // เพิ่มปุ่ม delete user
                echo '<td><button class="btn btn-danger btn-circle btn-sm" onclick="deleteUser(' . $row["user_id"] . ')"><i class="fas fa-trash"></i></button></td>';
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

<?php
include 'includes/scripts.php';
include 'includes/footer.php';
?>