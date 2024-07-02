<?php
include 'includes/header.php';
include 'includes/headmaid_navbar.php';
include 'includes/calendar.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">

<div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Calendar</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div id='calendar'></div>
                    <style>
                        #calendar {
                            max-width: 1000px;
                            width: 100%;
                            margin: 0 auto;
                        }
                    </style>
                </div>
            </div>
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

<?php
include 'includes/scripts.php';
include 'includes/footer.php';
?>
