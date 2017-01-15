<?php
/**
 * Created by PhpStorm.
 * User: HongYang
 * Date: 2016/12/22
 * Time: 18:50
 */

include_once 'PHPExcel.php';
include_once 'PHPExcel/Writer/Excel5.php';
require_once '../../DataAccess/DataProcessor.php';
require_once '../../DataAccess/BranchMember.php';

$name = $_GET['name'];

header("content-type:text/html;charset=utf-8");

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("YangHONG");
$objPHPExcel->getProperties()->setLastModifiedBy("YangHONG");
$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
$objPHPExcel->getProperties()->setDescription("党员信息统计");

// 设置当前页和表头
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->SetCellValue('A1', '序号');
$objPHPExcel->getActiveSheet()->SetCellValue('B1', '姓名');
$objPHPExcel->getActiveSheet()->SetCellValue('C1', '性别');
$objPHPExcel->getActiveSheet()->SetCellValue('D1', '班号');
$objPHPExcel->getActiveSheet()->SetCellValue('E1', '出生年月');
$objPHPExcel->getActiveSheet()->SetCellValue('F1', '民族');
$objPHPExcel->getActiveSheet()->SetCellValue('G1', '籍贯');
$objPHPExcel->getActiveSheet()->SetCellValue('H1', '正式/预备');
$objPHPExcel->getActiveSheet()->SetCellValue('I1', '发展时间');
$objPHPExcel->getActiveSheet()->SetCellValue('J1', '转正时间');
$objPHPExcel->getActiveSheet()->SetCellValue('K1', '所属支部名称');
$objPHPExcel->getActiveSheet()->SetCellValue('L1', '学生分类');
$objPHPExcel->getActiveSheet()->SetCellValue('M1', '所学专业');
$objPHPExcel->getActiveSheet()->SetCellValue('N1', '手机号码');

$rst = array();
$i = 0;
$dp = new DataProcessor();
if ($dp->connectMySQL()) {
    $conn = $dp->getConn();
    $sql_getInfo = "SELECT m.member_name, i.class, i.birth_date, i.nation, i.place_of_origin, i.state, i.develop_date, i.formal_date, b.branch_name , i.type, i.major, i.phone, i.post, i.gender
                        FROM branch_member m, branch_member_info i, branch b
                        WHERE m.member_id = i.member_id AND b.branch_id = m.branch_id AND (i.develop_date IS NOT NULL AND i.develop_date != '')
		                    AND m.branch_id = (SELECT branch_id FROM branch_member WHERE member_name = '".$name."')";
    $result = $conn->query($sql_getInfo);
    while ($row = $result->fetch_assoc()) {
        $i_pp = $i + 2;
        $i_p = $i + 1;

        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i_pp, $i_p);
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i_pp, $row["member_name"]);
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i_pp, $row["gender"]);
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i_pp, $row["class"]);
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i_pp, $row["birth_date"]);
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i_pp, $row["nation"]);
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$i_pp, $row["place_of_origin"]);
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$i_pp, $row["state"]);
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$i_pp, $row["develop_date"]);
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$i_pp, $row["formal_date"]);
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$i_pp, $row["branch_name"]);
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$i_pp, $row["type"]);
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$i_pp, $row["major"]);
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$i_pp, $row["phone"]);
        $i++;
    }
    $dp->closeConn();
}
// 设置表头字号
$objPHPExcel->getActiveSheet()->getStyle('A1:N1')->getFont()->setSize(11);
$objPHPExcel->getActiveSheet()->getStyle('A1:N1')->getFont()->setBold(true);
for ($j = 1; $j <= $i+1; $j++) {
    $objPHPExcel->getActiveSheet()->getStyle('A'.$j.':'.'N'.$j)->getFont()->setName('宋体');
}

// 冻结表头
$objPHPExcel->getActiveSheet()->freezePane('A2');

// 设置列宽
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(13);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(13);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(13);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(60);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(13);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(13);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(14);

// 设置水平、垂直居中
for ($j = 1; $j <= $i+1; $j++) {
    $objPHPExcel->getActiveSheet()->getStyle('A'.$j.':'.'N'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$j.':'.'N'.$j)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
}

// 绘制边框
$styleArray = array(
    'borders' => array(
        'allborders' => array(
            //'style' => PHPExcel_Style_Border::BORDER_THICK,//边框是粗的
            'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框
            //'color' => array('argb' => 'FFFF0000'),
        ),
    ),
);
$objPHPExcel->getActiveSheet()->getStyle('A1:N'.($i+1))->applyFromArray($styleArray);

// 重命名当前页
$objPHPExcel->getActiveSheet()->setTitle('党员信息统计表');

$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
//$objWriter->save(str_replace('.php', '.xls', __FILE__));
header("Pragma: public");
header("Expires: 0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Content-Type:application/force-download");
header("Content-Type:application/vnd.ms-execl");
header("Content-Type:application/octet-stream");
header("Content-Type:application/download");
header("Content-Disposition:attachment;filename=memberInfo.xls");
header("Content-Transfer-Encoding:binary");
$objWriter->save("php://output");
