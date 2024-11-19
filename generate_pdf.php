<?php
// รวมไลบรารี TCPDF
require_once('/var/www/html/realproject/TCPDF-main/tcpdf.php');

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "db"; // Use the service name 'db' defined in docker-compose
$username = "user"; // User defined in docker-compose
$password = "user_password"; // Password defined in docker-compose
$dbname = "project_maidmanage";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตั้งค่าวันที่เป็นวันนี้
$today = date('Y-m-d');

// คำสั่ง SQL สำหรับดึงข้อมูลเฉพาะของวันนี้
$sql = "SELECT 
    t.task_id,
    t.start_date,
    t.end_date,
    u.fullname AS user_fullname,
    t.floor_id,
    r.room_name,
    t.status_id,
    t.toilet_status_id
FROM task t
INNER JOIN users u ON t.user_id = u.user_id
LEFT JOIN room r ON t.room_id = r.room_id
WHERE t.start_date = '$today'
ORDER BY t.start_date ASC";

$result = $conn->query($sql);

if ($result === false) {
    die('Error: ' . $conn->error);
}

// สร้าง PDF ใหม่
$pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Company');
$pdf->SetTitle('Daily Task Report');

// กำหนดระยะห่างของ Header
$pdf->SetHeaderMargin(10); // เพิ่มระยะห่าง (หน่วยเป็น mm)

$pdf->SetHeaderData('', 0, 'Daily Task Report', $today);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// เพิ่มหน้าใหม่ใน PDF
$pdf->AddPage();

// ตั้งค่าฟอนต์
$pdf->SetFont('helvetica', '', 12);

// สร้างเนื้อหา HTML สำหรับแสดงใน PDF
$html = '<h1>Task Report for ' . $today . '</h1>';
$html .= '<table border="1" cellpadding="4">';
$html .= '<tr>
            <th><strong>Date</strong></th>
            <th><strong>Floor</strong></th>
            <th><strong>Room</strong></th>
            <th><strong>Status</strong></th>
            <th><strong>Toilet Status</strong></th>
            <th><strong>User</strong></th>

        </tr>';

while ($row = $result->fetch_assoc()) {
    // แปลงค่า status_id และ toilet_status_id
    $status = $row['status_id'] == 1 ? 'Ready' : ($row['status_id'] == 2 ? 'Not Ready' : 'Waiting');
    $toilet_status = $row['toilet_status_id'] == 1 ? 'Ready' : ($row['toilet_status_id'] == 2 ? 'Not Ready' : 'Waiting');

    // เพิ่มข้อมูลในตาราง HTML
    $html .= '<tr>
                <td>' . $row["start_date"] . '</td>
                <td>' . $row["floor_id"] . '</td>
                <td>' . ($row["room_name"] ?? '-') . '</td>
                <td>' . $status . '</td>
                <td>' . $toilet_status . '</td>
                <td>' . $row["user_fullname"] . '</td>
              </tr>';
}

$html .= '</table>';

// เขียนเนื้อหา HTML ลงใน PDF
$pdf->writeHTML($html, true, false, true, false, '');

// แสดง PDF บนเบราว์เซอร์
$pdf->Output('task_report.pdf', 'I');

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>