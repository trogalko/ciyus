
<?php
//$len = 229;
//if ($len ) {
  //  $Last = $tmp;
  //  $Last = str_replace('~NMR3~', '001', $Last);
  //  $Last = str_replace('~NMR4~', '0001', $Last);
  //  $Last = str_replace('~NMR5~', '00001', $Last);
 // }
  //else {
    //$_lst = $w['LAST'];
    //$base = $tmp;
    //$base = str_replace('~NMR3~', '', $base);
    //$base = str_replace('~NMR4~', '', $base);
    //$base = str_replace('~NMR5~', '', $base);
    //$_lst = str_replace($base, '', $_lst) +1;
    // Format jumlah digit
    $Last = '~NMR4~';
    //$Last = str_replace('~NMR3~', str_pad($_lst, 3, '0', STR_PAD_LEFT), $Last);
    $Last = str_replace('~NMR4~', str_pad(4, 4, '0', STR_PAD_LEFT), $Last);
    //$Last = str_replace('~NMR5~', str_pad($_lst, 5, '0', STR_PAD_LEFT), $Last);
  //}//
  //echo $Last;
  
  function GenerateNoIjazah($mhsw, $tglsk, $NoIndukFak, $NoIndukKOP){
  $FormatNoIjazah = "M~PRD~-~NOINDUKFAK~/UKW-~NOINDUKKOP~/~DATE~";
  
  $tmp = $FormatNoIjazah;
  // Replace ~PRD~ dengan ProdiID
  $tmp = str_replace('~PRD~', $mhsw['ProdiID'], $tmp);
  // Replace No Induk Fakultas 
  $tmp = str_replace('~NOINDUKFAK~', str_pad($NoIndukFak, 4, '0', STR_PAD_LEFT), $tmp);
  // Replace No Induk Kopertis
  $tmp = str_replace('~NOINDUKKOP~', str_pad($NoIndukKOP, 5, '0', STR_PAD_LEFT), $tmp);
  
  $tglsk = substr($tglsk, -7, 7);
  $tglsk = str_replace('-', '/', $tglsk);
  
  // Replace bulan/tahun
  $tmp = str_replace('~DATE~', $tglsk, $tmp);
  
  return $tmp;
}
$arrBulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                    'Agustus', 'September', 'Oktober', 'November', 'Desember');
//echo GenerateNoIjazah('312002002', '12/10/2007', '204', '3200');
function BuatTanggal($tgl, $bhs='id') {
    global $arrBulan;
    
    $tmp = array();
    $tmp = explode('-', $tgl);
    $nm_b = $arrBulan[$tmp[1]];
    
    return $tmp[2] . ' ' . $nm_b . ' ' . $tmp[0];
}

//echo BuatTanggal('2007-12-31');
echo dirname( __FILE__ );
?>


