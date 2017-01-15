<?php
/**
 * Created by PhpStorm.
 * User: HongYang
 * Date: 2016/12/23
 * Time: 14:31
 */

include_once 'PHPExcel.php';
include_once 'PHPExcel/Writer/Excel5.php';
require_once '../../DataAccess/DataProcessor.php';
require_once '../../DataAccess/BranchMember.php';

//$name = $_GET['name'];

header("content-type:text/html;charset=utf-8");

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("YangHONG");
$objPHPExcel->getProperties()->setLastModifiedBy("YangHONG");
$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
$objPHPExcel->getProperties()->setDescription("党员转正信息表");

// 设置当前页和表头
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->mergeCells('A1:F1'); // 合并A1-F1
$objPHPExcel->getActiveSheet()->SetCellValue('A1', '软件与微电子学院学生党总支（盖章）');
//$objPHPExcel->getActiveSheet()->SetCellValue('B1', '姓名');
//$objPHPExcel->getActiveSheet()->SetCellValue('C1', '性别');
//$objPHPExcel->getActiveSheet()->SetCellValue('D1', '班号');
//$objPHPExcel->getActiveSheet()->SetCellValue('E1', '出生年月');
//$objPHPExcel->getActiveSheet()->SetCellValue('F1', '民族');
$objPHPExcel->getActiveSheet()->SetCellValue('G1', '');
$objPHPExcel->getActiveSheet()->SetCellValue('H1', '');
$objPHPExcel->getActiveSheet()->mergeCells('I1:K1');
$objPHPExcel->getActiveSheet()->SetCellValue('I1', '上会日期：');
$objPHPExcel->getActiveSheet()->SetCellValue('L1', '');
$objPHPExcel->getActiveSheet()->mergeCells('M1:N1');
$objPHPExcel->getActiveSheet()->SetCellValue('M1', '发展  人');
$objPHPExcel->getActiveSheet()->SetCellValue('O1', '转正  人');

$objPHPExcel->getActiveSheet()->SetCellValue('A2', '序号');
$objPHPExcel->getActiveSheet()->SetCellValue('B2', '姓名');
$objPHPExcel->getActiveSheet()->SetCellValue('C2', '性别');
$objPHPExcel->getActiveSheet()->SetCellValue('D2', '班号');
$objPHPExcel->getActiveSheet()->SetCellValue('E2', '出生年月');
$objPHPExcel->getActiveSheet()->SetCellValue('F2', '民族');
$objPHPExcel->getActiveSheet()->SetCellValue('G2', '籍贯');
$objPHPExcel->getActiveSheet()->SetCellValue('H2', '正式/预备');
$objPHPExcel->getActiveSheet()->SetCellValue('I2', '发展时间');
$objPHPExcel->getActiveSheet()->SetCellValue('J2', '转正时间');
$objPHPExcel->getActiveSheet()->SetCellValue('K2', '所属支部名称');
$objPHPExcel->getActiveSheet()->SetCellValue('L2', '学生分类');
$objPHPExcel->getActiveSheet()->SetCellValue('M2', '所学专业');
$objPHPExcel->getActiveSheet()->SetCellValue('N2', '最高学历毕业时间');
$objPHPExcel->getActiveSheet()->SetCellValue('O2', '身份证号');

// 设置表头字号
$objPHPExcel->getActiveSheet()->getStyle('A1:N1')->getFont()->setSize(11);
//for ($j = 1; $j <= $i+1; $j++) {
//    $objPHPExcel->getActiveSheet()->getStyle('A'.$j.':'.'N'.$j)->getFont()->setName('宋体');
//}

// 冻结表头
$objPHPExcel->getActiveSheet()->freezePane('C3');

// 设置列宽
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(2.63);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13.38);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(3.25);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(7.5);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8.5);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(4.13);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(11.25);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(13.25);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8.63);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(37.25);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(13.13);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(23.38);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(8.63);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(37.13);

// 设置水平、垂直居中
//for ($j = 1; $j <= $i+1; $j++) {
//    $objPHPExcel->getActiveSheet()->getStyle('A'.$j.':'.'N'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//    $objPHPExcel->getActiveSheet()->getStyle('A'.$j.':'.'N'.$j)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
//}

// 重命名当前页
$objPHPExcel->getActiveSheet()->setTitle('党员转正信息表');

$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
//$objWriter->save(str_replace('.php', '.xls', __FILE__));
header("Pragma: public");
header("Expires: 0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Content-Type:application/force-download");
header("Content-Type:application/vnd.ms-execl");
header("Content-Type:application/octet-stream");
header("Content-Type:application/download");
header("Content-Disposition:attachment;filename=test.xls");
header("Content-Transfer-Encoding:binary");
$objWriter->save("php://output");