<?php
require_once __DIR__ . '/vendor/autoload.php';
$connect = new mysqli('localhost', 'root', '', 'tus-control_pen');
// Check connection
if (!$connect) {
  die("Connection failed: " . mysqli_connect_error());
}

$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
$fontDirs = $defaultConfig['fontDir'];

$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];
$mpdf = new \Mpdf\Mpdf([
  'mode' => 'utf-8',
  'format' => 'A4',
  'margin_left' => 15,
  'margin_right' => 15,
  'margin_top' => 16,
  'margin_bottom' => 16,
  'margin_header' => 9,
  'margin_footer' => 9,
  'mirrorMargins' => true,

  'fontDir' => array_merge($fontDirs, [
    __DIR__ . 'vendor/mpdf/mpdf/custom/font/directory',
  ]),
  'fontdata' => $fontData + [
    'thsarabun' => [
      'R' => 'THSarabunNew.ttf',
      'I' => 'THSarabunNew Italic.ttf',
      'B' => 'THSarabunNew Bold.ttf',
      'U' => 'THSarabunNew BoldItalic.ttf'
    ]
  ],
  'default_font' => 'thsarabun',
  'defaultPageNumStyle' => 1
]);

$mpdf->setFooter('{PAGENO}'); //ตัวรันหน้า
//http://fordev22.com/


$tableh1 = '
	

	<h2 style="text-align:center">รายงานรายชื่อพนักงานบริษัทเรียน.com</h2>

	<table id="bg-table" width="100%" style="border-collapse: collapse;font-size:12pt;margin-top:8px;">
	    <tr style="border:1px solid #000;padding:4px;">
	        <td  style="border-right:1px solid #000;padding:4px;text-align:center;"   width="10%">ลำดับ</td>
	        <td  style="border-right:1px solid #000;padding:4px;text-align:center;"  width="15%">ฝ่าย/แผนก</td>
	        <td  width="15%" style="border-right:1px solid #000;padding:4px;text-align:center;">&nbsp; จำนวน </td>
	    </tr>

	</thead>
		<tbody>';
$s = 1;
//คำสั่งให้เลือกข้อมูลจาก TABLE ชื่อ tbl_member โดยเรียงจาก member_id และให้เรียงลำดับจากมากไปน้อยคือ DESC และ เปิดดู error เวลามีปัญหา
$query = "SELECT count(*) as present_absent_count, dept_id,
    case
        when dept_id = 01 then 'บัญชี'
        when dept_id = 02 then 'ขาย'
        when dept_id = 03 then 'บุคคล'
      end as dept_id FROM employee GROUP BY dept_id ;";
$result = mysqli_query($connect, $query);


//ประกาศตัวแปร sqli
// $result = mysqli_query($conn, $query);
// $sql = "SELECT * FROM tb_member";
$result = mysqli_query($connect, $query);

$content = "";
// if (mysqli_num_rows($result) > 1) {
// $i = 1;
foreach ($result as $rs) {

  $tablebody .= '<tr style="border:1px solid #000;">
				<td style="border-right:1px solid #000;padding:3px;text-align:center;"  >' . $s++ . '</td>
				<td style="border-right:1px solid #000;padding:3px;">' . $rs['dept_id'] . '</td>
				<td style="border-right:1px solid #000;padding:3px;">' . $rs['present_absent_count'] . '</td>
			</tr>';
  // $i++;
}
//}

//mysqli_close($conn);


$tableend1 = "</tbody>
</table>";
// print_r($result);
// $mpdf->Output();
// exit();




$fordev22 = '
<style>
     

div{
       
    }
table {
  
   
  border-collapse: collapse;
  width: 100%;
}

td, th {
    font-size: 18px;
  border: 1px solid #AED6F1;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #F5FFFA;
}

</style>


<img width="250"  src="logo_fordev22_2.jpg" style="vertical-align: middle;
  width: 250px;">


</div>

';





$mpdf->WriteHTML($fordev22);

$mpdf->WriteHTML($tableh1);

$mpdf->WriteHTML($tablebody);

$mpdf->WriteHTML($tableend1);
//$output = 'fordev22.com';
$mpdf->Output($output, 'I');