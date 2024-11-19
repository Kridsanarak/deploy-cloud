<?php
/// ตรวจสอบว่ามีการเลือกภาษาใหม่ผ่าน URL หรือไม่
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// กำหนดภาษาเริ่มต้นเป็นภาษาอังกฤษ
$lang = $_SESSION['lang'] ?? 'th';

// โหลดไฟล์ภาษาตามการเลือกของผู้ใช้
$translations = include("lang/lang_{$lang}.php");
?>

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="main.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-2" style="font-family: Prompt;"><?php echo $translations['brand_text']; ?>
        </div>
    </a>
    <!-- SB Admin <sup>2</sup> -->
    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        <?php echo $translations['interface']; ?>
    </div>

    <li class="nav-item">
        <a class="nav-link" href="addtask.php">
            <i class="fas fa-fw fa-edit"></i>
            <span><?php echo $translations['add_task']; ?></span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="send.php">
            <i class="fas fa-fw fa-paper-plane"></i>
            <span><?php echo $translations['send']; ?></span></a>
    </li>

        <!-- Nav Item - Users -->
    <li class="nav-item">
        <a class="nav-link" href="head_users.php">
            <i class="fas fa-fw fa-user"></i>
            <span><?php echo $translations['user']; ?></span></a>
    </li>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link" href="head_calendar.php">
            <i class="fas fa-fw fa-table"></i>
            <span><?php echo $translations['work_schedule']; ?></span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="tables.php">
            <i class="far fa-check-circle"></i>
            <span><?php echo $translations['work_check']; ?></span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="complete_tables.php">
            <i class="far fa-folder-open"></i>
            <span><?php echo $translations['complete_work']; ?></span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->

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
                <h5 class="modal-title" id="exampleModalLabel"><?php echo $translations['logout_modal_title']; ?></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body"><?php echo $translations['logout_modal_body']; ?></div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button"
                    data-dismiss="modal"><?php echo $translations['cancel']; ?></button>
                <a class="btn btn-primary" href="logout.php"><?php echo $translations['logout']; ?></a>
            </div>
        </div>
    </div>
</div>

<div id="content-wrapper" class="d-flex flex-column">

    <!-- maid Content -->
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <!-- Topbar Navbar -->
            <ul class="navbar-nav ml-auto">

                <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                <li class="nav-item dropdown no-arrow d-sm-none">
                    <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-search fa-fw"></i>
                    </a>
                    <!-- Dropdown - Messages -->
                    <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                        aria-labelledby="searchDropdown">
                        <form class="form-inline mr-auto w-100 navbar-search">
                            <div class="input-group">
                                <input type="text" class="form-control bg-light border-0 small"
                                    placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button">
                                        <i class="fas fa-search fa-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </li>

                <!-- Language Switcher -->
                <div class="language-switch">
                <?php
                    $currentLang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : 'th');
                        if ($currentLang == 'th') {
                            echo '<a href="?lang=en"><img src="./lang/thailand.png" alt="ภาษาไทย"  style="width: 30px; height: auto; margin-top: 1.2rem;"></a>';
                        } else {
                            echo '<a href="?lang=th"><img src="./lang/united-states.png" alt="English" style="width: 30px; height: auto; margin-top: 1.2rem;"></a>';
                        }
                ?>
                </div>
                
                <div class="topbar-divider d-none d-sm-block"></div>

                <!-- Nav Item - User Information -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['full_name']; ?></span>
                        <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                    </a>
                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                        aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </li>

            </ul>

        </nav>
        <!-- End of Topbar -->