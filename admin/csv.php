<?php
require_once('phpexcel/Classes/PHPExcel.php');
include 'api/bdd.php';
include 'api/fonctionsUtiles.php';

$dateDebut=date('Y')."-01-01 00:00:00";
$dateFin="2100-01-01 00:00:00";

$export=getExports($dateDebut,$dateFin,$dbh);
echo json_encode($export);


$doc = new PHPExcel();

//*****GPSPORTS******
$objWorkSheet = $doc->createSheet(0);
$doc->setActiveSheetIndex(0);
$doc->getActiveSheet()->fromArray($export['GPTransports']);
$objWorkSheet->setTitle("GPSport");

//*****GPTRANSPORT******
$objWorkSheet = $doc->createSheet(1);
$doc->setActiveSheetIndex(1);
$doc->getActiveSheet()->fromArray($export['GPSports']);
$objWorkSheet->setTitle("GPTransport");
 
$doc->removeSheetByIndex(2);


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . "export.xls" . '"');
header('Cache-Control: max-age=0'); 

$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel5');
$objWriter->save('php://output');
?>