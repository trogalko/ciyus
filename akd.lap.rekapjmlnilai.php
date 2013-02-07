<?php
function daftar (){
  global $_lf;  
  $s = "SELECT KRSID, mk.MKKode, mk.Nama, j.NamaKelas 
        from krs 
          left outer join mk on krs.MKID = mk.MKID 
          left outer join jadwal j on krs.JadwalID = j.JadwalID
        where krs.tahunid = $_SESSION[tahun] and 
        krs.gradenilai in (select nama from nilai where prodiid = $_SESSION[prodi]) 
        and j.JenisJadwalID = 'K'
        and mk.prodiID = $_SESSION[prodi] group by krs.MKKode, j.NamaKelas order by mk.MKKode, j.NamaKelas";
  $r = _query($s);
  // Buat file
  $MaxCol = 114;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15));
  $div = str_pad('-', $MaxCol, '-').$_lf;
  $_prodi = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  $_prid = GetaField('program', 'ProgramID', $_SESSION['prid'], 'Nama');
  $n = 0; $hal = 1; $n2 = 0;
  $brs = 0;
  $maxbrs = 50;
  $jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);
  $arrGrade = GetArrayNilai("select Nama as Grade from Nilai where ProdiID = $_SESSION[prodi] order by Bobot DESC", "Grade");
  $banyakGrade = sizeof($arrGrade);
  $banyakGrade1 = $banyakGrade + 1;
  echo "<p><a href='?mnux=akd.lap'>Kembali</a> | <a href=dwoprn.php?f=$nmf>Cetak Laporan</a></p>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr>
    <th class=ttl rowspan=2>#</th>
    <th class=ttl rowspan=2>Kode</th>
    <th class=ttl rowspan=2>Nama</th>
    <th class=ttl rowspan=2>Kelas</th>
    <th class=ttl colspan=$banyakGrade1>Grade</th>
    </tr>";
  for ($i=0; $i< $banyakGrade; $i++) {
    $str = explode('~', $arrGrade[$i]);
    $hdrGrd .= "<th class=ttl title='$str[0]'>$str[0]</th>";
    $hdrctk .= str_pad($str[0], 5, ' ', STR_PAD_LEFT);
  }
  $hdr = str_pad("*** Laporan Rekap Jumlah Nilai Per Prodi ***", $MaxCol, ' ', STR_PAD_BOTH).$_lf.$_lf.$_lf;
  $hdr .= "Periode   : " . NamaTahun($_SESSION['tahun']) . $_lf;
  $hdr .= "Prodi     : $_prodi".$_lf;
  $hdr .= "Program   : $_prid" . $_lf;
  $hdr .= $div;
  $hdr .= str_pad("NO", 4) . str_pad('KODE', 8) . str_pad('NAMA', 35) . str_pad('KELAS', 6). $hdrctk . str_pad('TOTAL', 9, ' ', STR_PAD_LEFT).$_lf.$div;
  fwrite($f, $hdr);
  echo "<tr>$hdrGrd<th class=ttl>TOTAL</td></tr>";
  while ($w = _fetch_array($r)){
      
    $_ssum = "select j.NamaKelas, GradeNilai, count(GradeNilai) as Jumlah
      from krs
        left outer join jadwal j on j.JadwalID = krs.JadwalID
      where krs.MKKode='$w[MKKode]' and krs.TahunID = '$_SESSION[tahun]' and j.NamaKelas = '$w[NamaKelas]'
      group by GradeNilai";
    $_rsum = _query($_ssum);
    $arrJumlah = array();
    while ($_wsum = _fetch_array($_rsum)) {
      $_kel = $_wsum['NamaKelas'];
      $_pid = $_wsum['GradeNilai'];
      $_jum = $_wsum['Jumlah'];
      $arrJumlah[$_pid] = $_jum+0;
    } 
    $n++;
    /*$brs++;
      if($brs > $maxbrs){
          fwrite($f, $div);
          fwrite($f, str_pad("Hal. : ".$hal.'/'.$jumhal, $maxcol, ' ', STR_PAD_LEFT).$_lf);
          $hal++; $brs = 1;
          fwrite($f, chr(12).$_lf);
          fwrite($f, $hdr);
      }*/ 
    $TOTAL = $arrJumlah['A'] + $arrJumlah['A-'] + $arrJumlah['B'] + $arrJumlah['B+']+$arrJumlah['B-']+$arrJumlah['C']+
             $arrJumlah['C+'] + $arrJumlah['D'] + $arrJumlah['E'];
    echo "<tr><td class=ul>$n</td><td class=ul>$w[MKKode]</td><td class=ul>$w[Nama]</td><td class=ul>$w[NamaKelas]</td>
    <td class=ul align=right>". ($arrJumlah['A']+0) ."</td>
    <td class=ul align=right>". ($arrJumlah['A-']+0) ."</td>
    <td class=ul align=right>". ($arrJumlah['B+']+0) ."</td>
    <td class=ul align=right>". ($arrJumlah['B']+0) ."</td>
    <td class=ul align=right>". ($arrJumlah['B-']+0) ."</td>
    <td class=ul align=right>". ($arrJumlah['C']+0) ."</td>
    <td class=ul align=right>". ($arrJumlah['C+']+0) ."</td>
    <td class=wrn align=right>". ($arrJumlah['D']+0) ."</td>
    <td class=wrn align=right>". ($arrJumlah['E']+0) ."</td>
    <td class=ul align=right>$TOTAL</td>
    </tr>";
    $brs++;
      if($brs > $maxbrs){
          $isi .= $div;
          $isi .= str_pad("Hal. : ".$hal.'/'.$jumhal, $maxcol, ' ', STR_PAD_LEFT).$_lf;
          $hal++; $brs = 1;
          $isi .= chr(12).$_lf;
          $isi .= $hdr;
      }
    if ($kdmk != $w['MKKode']) {
      $kdmk = $w['MKKode'];
      $_kdmk = $kdmk;
      $n2++;
    } else { 
        $_kdmk = '';
    }
    if ($nmmk != $w['Nama']) {
      $nmmk = $w['Nama'];
      $_nmmk = $nmmk;
    } else { 
        $_nmmk = '';
    }
    if ($n_ != $n2) {
      $n_ = $n2;
      $_n_ = $n_.".";
    } else {
    $_n_ = '';
    }
    $isi .= str_pad($_n_, 4) .
           str_pad($_kdmk, 8) .
           str_pad($_nmmk, 35) .
           str_pad($w['NamaKelas'], 6) .
           str_pad($arrJumlah['A']+0,5, ' ', STR_PAD_LEFT) .
           str_pad($arrJumlah['A-']+0,5, ' ', STR_PAD_LEFT) .
           str_pad($arrJumlah['B+']+0,5, ' ', STR_PAD_LEFT) .
           str_pad($arrJumlah['B']+0,5, ' ', STR_PAD_LEFT) .
           str_pad($arrJumlah['B-']+0,5, ' ', STR_PAD_LEFT) .
           str_pad($arrJumlah['C']+0,5, ' ', STR_PAD_LEFT) .
           str_pad($arrJumlah['C+']+0,5, ' ', STR_PAD_LEFT) .
           str_pad($arrJumlah['D']+0,5, ' ', STR_PAD_LEFT) .
           str_pad($arrJumlah['E']+0,5, ' ', STR_PAD_LEFT) .
           str_pad($TOTAL,9, ' ', STR_PAD_LEFT) .
           $_lf;
    //echo "<tr><td colspan=$banyakGrade class=ul>" . ($arrJumlah['A']+0) . "</td></tr>";
  }
  fwrite($f, $isi);
  fwrite($f, $div);
  fwrite($f, str_pad("Hal. : ".$hal.'/'.$jumhal, $maxcol, ' ', STR_PAD_LEFT).$_lf);
  fwrite($f, str_pad('Dicetak oleh : '.$_SESSION['_Login'],85,' ').str_pad('Dibuat : '.date("d-m-Y H:i"),29,' '));
  fwrite($f, chr(12));
  fclose($f);
  /*echo "<td class=ul>$totA</td>
        <td class=ul></td>
        <td class=ul></td>
        <td class=ul></td>
        <td class=ul></td>
        <td class=ul></td>
        <td class=ul></td>
        <td class=ul></td>
        <td class=ul><td>";*/
  echo "</table></p>";
}

function BuatSumNilai($w){
  $_ssum = "select GradeNilai, count(GradeNilai) as Jumlah
      from krs
      where KRSID='$w[KRSID]'
      group by GradeNilai";
    $_rsum = _query($_ssum);
    $arrJumlah = array();
    while ($_wsum = _fetch_array($_rsum)) {
      $_pid = $_wsum['GradeNilai'];
      $_jum = $_wsum['Jumlah'];
      $arrJumlah[$_pid] = $_jum+0;
    }
    return $arrJumlah;
}

function GetArrayNilai($sql, $nilai) {
  $a = array();
  $r = _query($sql);
  while ($w = _fetch_array($r)) {
    $a[] = $w[$nilai];
  }
  return $a;
}

$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$tahun = GetSetVar('tahun');

TampilkanJudul("Rekapitulasi Nilai Mahasiswa");
TampilkanTahunProdiProgram('akd.lap.rekapjmlnilai', 'daftar');
if(!empty($tahun) and !empty($prodi)) daftar();

?>
