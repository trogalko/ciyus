<?php
require_once "File/PDF.php";

// Set Nama Universitas
$UNIVERSITAS = "UNIVERSITAS KRISTEN KRIDA WACANA";

// memberikan kepada
$
$my_address = "123 Aaron Way, Gotham City, 12421 RQ, USA";
// Set some initial margins
$lm = 22;
$rm = 22;
$tm = 22;
$bm = 22;
$padding = 10;
$pdf = File_PDF::factory(array('orientation' => 'L',
                                'unit' => 'mm',
                                'format' => 'A4'));
$pdf->open();
// Can also be done with setMargins
$pdf->setLeftMargin($lm + $padding);
$pdf->setRightMargin($rm + $padding);
$pdf->addPage();
// Set the typeface for the title
$pdf->setFont('Arial', 'B', '24');
$pos = $tm + $padding;
$pdf->setXY(10, $pos);
// Draw the Company Name
$pdf->cell(0, $padding, $UNIVERSITAS, null, 0, 'C');

$pdf->setFont('Arial', 'B', '10');
$pos += 10;
//$pdf->setXY(10, $pos);
$pdf->newLine();
$pdf->cell(0, 0, 'Memberikan ijazah kepada', null, 1, 'C');
$pos += 3;
$pdf->setXY($lm, $pos);
$pdf->line($lm + $padding, $pos, 210 - $rm - $lm, $pos);
$pos += 10;
$pdf->setXY($lm, $pos);
$pdf->newLine();
$pdf->write('4', "John Smith");
$pdf->newLine();
$pdf->write('4', "122 Peters Lane");
$pdf->newLine();
$pdf->write('4', "32235 City, State");
$pdf->newLine();
$pdf->write('4', "Country");
$pdf->newLine();
$pos += 20;
$pdf->setXY($lm, $pos);
$pdf->newLine();
$pdf->write('4', "To whom it may Concern:");
$pos += 6;
$pdf->setXY($lm, $pos);
$pdf->newLine();
// shortened for the sake of brevity
$text = "Lorem ipsum dolor ... porta eleifend. ?";
$pdf->MultiCell(210 -$lm -$rm - $padding *2, 3, $text, null, "J");
$pdf->newLine(10);
$pdf->write("10", "Best Regards,");
$pdf->output();
?>
