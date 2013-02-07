<?php
// Author: Emanuel Setio Dewo
// 2006-01-12

include_once "pmb.usm.def.php";
  
// *** Functions ***
function aplod() {
  global $pmbaktif;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='index.php' enctype='multipart/form-data' method=POST>
  <input type=hidden name='mnux' value='pmb.usm'>
  <input type=hidden name='gos' value='aplodSav'>
  <tr><th class=ttl colspan=2>Upload File USM</th></tr>
  <tr><td class=inp1>Gelombang</td><td class=ul><input type=text name='pmbaktif' value='$pmbaktif' size=10 maxlength=20></td></tr>
  <tr><td class=inp1>Nama file Kunci Ujian</td><td class=ul><input type=file name='userfile[]' size=30></td></tr>
  <tr><td class=inp1>Nama file Jawaban Ujian</td><td class=ul><input type=file name='userfile[]' size=30></td></tr>
  <tr><td colspan=2><input type=submit name='Upload' value='Upload File USM'></td></tr>
  </form></table></p>";
}
function HapusTabelUSM() {
  $s = "delete from pmbusmfile";
  $r = _query($s);
}
function aplodSav() {
  global $_tmpdir;
  HapusTabelUSM();
  $nmf = "tmp/pmb.usm.up.txt";
  $nmj = "tmp/pmb.usm.jwb.txt";
  //$nmf = "C:\\pmb.usm.up.txt";
  $upf = $_FILES['userfile']['tmp_name'][1];
  $ukf = $_FILES['userfile']['size'][1];
  if (move_uploaded_file($upf, $nmf)) {
    if (move_uploaded_file($_FILES['userfile']['tmp_name'][0], $nmj)) aplodPrc($nmf, $nmj);
    else echo ErrorMsg("Proses Upload Gagal",
      "Tidak dapat memproses file jawaban yang di-upload. Periksa file yang akan di-upload.");
  }
  else {
    echo ErrorMsg("Proses Upload Gagal", "Tidak dapat memproses file hasil USM yang diupload. Periksa file yang akan diupload.");
    aplod();
  }
}
function aplodPrc($nmf, $nmj) {
  global $_lf, $_DefStruUSM, $pmbaktif;
  
  $hnd = fopen($nmf, 'r');
  //echo filesize($nmf);
  $isi = fread($hnd, filesize($nmf));
  fclose($hnd);
  // Tampilkan isi
  $arr = explode($_lf, $isi);
  
  // buat array definisi struktur
  $_nm = array(); $_uk = array(); $_tmp = array();
  for ($i=0; $i<sizeof($_DefStruUSM); $i++) {
    $_atmp = explode(':', $_DefStruUSM[$i]);
    $_nm[] = $_atmp[0];
    $_uk[] = $_atmp[1];
  }
  // Masukkan kunci baru
  MasukkanKunci($nmj, $_nm, $_uk);
  
  // Buat file template SQL
  //$_fields = 
  $_templateSQL = "insert into pmbusmfile (". implode(", ", $_nm). ", PMBPeriodID) values (";

  $n=0;
  for ($i=0; $i<sizeof($arr); $i++) {
    $dt = $arr[$i];
    $isi = array();
    for ($j=0; $j<sizeof($_nm); $j++) {
      $isi[] = "'".substr($dt, 0, $_uk[$j])."'";
      $dt = substr($dt, $_uk[$j]);
    }
    $n++;
    // Konstruksi SQL
    $_pmbid = trim($isi[1], "'");
    if (!empty($_pmbid)) {
      $_sql = $_templateSQL . implode(", ", $isi). ", '$pmbaktif')";
      $_r = _query($_sql);
    }
  }
  // Periksa jawaban
  PeriksaJawaban($nmj);
}
function MasukkanKunci($nmj, $_nm, $_uk) {
  global $_lf, $_DefStruUSM, $pmbaktif;
  // Baca isi file kunci
  $hnd = fopen($nmj, 'r');
  $isi = fread($hnd, filesize($nmj));
  fclose($hnd);
  // Tampilkan isi
  $arr = explode($_lf, $isi);
  
  // Hapus kunci dulu
  $s = "delete from pmbusmkey"; $r = _query($s);
  // Masukkan kunci
  $_templateSQL = "insert into pmbusmkey (". implode(", ", $_nm).", PMBPeriodID) values (";
  $n = 0;
  for ($i=0; $i<sizeof($arr); $i++) {
    $dt = $arr[$i];
    $isi = array();
    for ($j = 0; $j<sizeof($_nm); $j++) {
      $isi[] = "'".substr($dt, 0, $_uk[$j])."'";
      $dt = substr($dt, $_uk[$j]);
    }
    $n++;
    // Konstruksikan SQL
    $_pmbid = trim($isi[1], "'");
    if (!empty($_pmbid)) {
      $_sql = $_templateSQL . implode(", ", $isi). ", '$pmbaktif')";
      $_r = _query($_sql);
    }
  }
}
function StrukturJawabanTest() {
  global $pmbaktif;
  $s = "select * from prodiusm
    where PMBPeriodID='$pmbaktif'
    order by ProdiID, Urutan";
  $r = _query($s);
  $str = array();
  $prd = '';
  while ($w = _fetch_array($r)) {
    if ($prd != $w['ProdiID']) {
      $prd = $w['ProdiID'];
      $str[$prd] = "$w[PMBUSMID]:$w[JumlahSoal]";
    }
    else $str[$prd] .= ",$w[PMBUSMID]:$w[JumlahSoal]";
  }
  // Cek isinya dulu yak?
  $s1 = "select Nama, ProdiID from prodi order by ProdiID";
  $r1 = _query($s1);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><th class=ttl colspan=2>Prodi</th><th class=ttl>Soal Ujian</th></tr>";
  while ($w = _fetch_array($r1)) {
    $nm = $w['ProdiID'];
    echo "<tr><td class=ul>$w[ProdiID]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$str[$nm]</td></tr>";
  }
  echo "</table></p>";
  return $str;
}
function SemuaTestUSM() {
  $s = "select * from pmbusm group by PMBUSMID";
  $r = _query($s);
  $str = '.';
  while ($w = _fetch_array($r)) $str .= "$w[PMBUSMID]:$w[Nama].";
  //echo $str;
  return $str;
}
function BuatHeaderDetTest($d) {
  $d = trim($d, '.');
  $hd = explode('.', $d);
  $str = '';
  for ($i=0; $i<=sizeof($hd); $i++) {
    $det = explode(':', $hd[$i]);
    $str .= "<th class=ttl>$det[0]<br>$det[1]</th>";
  }
  return $str;
}
function arrJawaban($d) {
  $d = trim($d, '.');
  $hd = explode('.', $d);
  $str = array();
  for ($i=0; $i<sizeof($hd); $i++) {
    $_hd = explode(':', $hd[$i]);
    $str[$i] = $_hd[0];
    //echo "<b>$i</b>, $hd[$i]<br>";
  }
  return $str;
}
function PeriksaJawaban($nmj) {
  global $_lf, $_JawabanUSM1, $_JawabanUSM2;
  /*
  // Ambil 1 baris jawaban
  $hnd = fopen($nmj, 'r');
  $jwb = fread($hnd, filesize($nmj));
  fclose($hnd);
  $_jwb = explode($_lf, $jwb);
  $jwb = substr($_jwb[0], $_JawabanUSM1, $_JawabanUSM1+$_JawabanUSM2);
  // Tampilkan jawaban
  $pjg = strlen($jwb);
  $jml = $pjg - substr_count($jwb, '*');
  echo "Jumlah jawaban: $jml<hr size=1 color=silver>";
  */
  
  // Baca stuktur jawaban: jumlah jawaban utk masing2 jenis test
  $StruJwbnTest = StrukturJawabanTest();
  // Masalah header
  $DetTest = SemuaTestUSM();
  $HeaderDetTest = BuatHeaderDetTest($DetTest);
  $_arrJwbn = arrJawaban($DetTest);

  // Periksa ujian peserta
  $s = "select puf.*, p.ProdiID
    from pmbusmfile puf
    left outer join pmb p on puf.PMBID=p.PMBID
    order by puf.PMBID";
  $r = _query($s);
  $c = 'class=ul'; $n = 0;
  $vld = 0; $tot = _num_rows($r);
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  echo "<tr><th class=ttl>#</th>
    <th class=ttl>PMBID</th>
    <th class=ttl>Jawaban</th>
    <th class=ttl>Jenis</th><th class=ttl>Benar</th>
    <th class=ttl>Nilai</th>
    <th class=ttl>Upload</th>
    $HeaderDetTest</tr>";
  while ($w = _fetch_array($r)) {
    // struktur yg jawaban
    $prd = (empty($w['ProdiID']))? 'ERR' : $w['ProdiID'];
    $arrJwbn = array();
    if (!empty($w['ProdiID'])) {
      $_stru = $StruJwbnTest[$w['ProdiID']];
      // pisahkan tiap test
      $stru = explode(',', $_stru);
      $pos1 = 0;
      $nil1 = 0;
      $nil2 = '';
      $jwbn = GetFields('pmbusmkey', 'KodeTest', $w['KodeTest'], '*');
      $jwb = $jwbn['Jawaban'];
      $pjg = strlen($jwb);
      $jml = $pjg - substr_count($jwb, '*');
      
      for ($i1=0; $i1<sizeof($stru); $i1++) {
        $_testusm = explode(':', $stru[$i1]);
        $_kodetest = $_testusm[0];
        $_jmltest = $_testusm[1];
        for ($n1 = $pos1; $n1<$pos1+$_jmltest; $n1++) {
          if ($w['Jawaban'][$n1] != '*') $nil1 += ($w['Jawaban'][$n1] == $jwb[$n1])? 1 : 0;
        }
        $pos1 = $n1;
        $nil2 .= "$_kodetest:$nil1.";
        $key = array_search($_kodetest, $_arrJwbn);
        $arrJwbn[$key] = $nil1;
        // reset
        $nil1 = 0;
      }
      $nil2 = ".$nil2";
      $prd .= "> $nil2";
    }
    $n++;
    $benar = 0;
    for ($i=0; $i<$pjg; $i++) {
      if (($w['Jawaban'][$i] != '*') && ($jwb[$i] != ' '))
        $benar += ($w['Jawaban'][$i] == $jwb[$i])? 1 : 0;
    }
    if ($jml == 0) {
      $nilai = 0;
      $_nilai = 0;
    }
    else {
      $nilai = $benar;
      $_nilai = number_format($nilai, 2);
    }
    // parse tahap 2
    $detail = '';
    for ($i=0; $i<sizeof($_arrJwbn); $i++)
      $detail .= "<td class=ul align=right>$arrJwbn[$i]</td>";
    
    // Update nilai
    $s1 = "update pmbusmfile set Benar=$benar, Nilai='$_nilai' where PMBID='$w[PMBID]'";
    $r1 = _query($s1);
    // update tabel PMB
    $s2 = "update pmb set NilaiUjian='$_nilai', DetailNilai='$nil2' where PMBID='$w[PMBID]'";
    $r2 = _query($s2);
    
    $ok = _affected_rows();
    $vld += $ok;
    $c = ($ok > 0) ? "class=ul" : "class=nac";

    echo "<tr><td $c>$n</td>
      <td $c>$w[PMBID]</td>
      <td $c>$prd</td>
      <td $c>$w[KodeTest]</td>
      <td $c align=right>$benar</td>
      <td $c align=right>$_nilai%</td>
      <td $c>$ok</td>
      $detail
      <td $c>$jwb</td>
      </tr>";
  }
  echo "</table></p>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=ul>Jumlah total hasil scan:</td><td class=ul align=right>$tot</td></tr>
  <tr><td class=ul>Jumlah yang diupload:</td><td class=ul align=right>$vld</td></tr>
  </table></p>";
}

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "aplod" : $_REQUEST['gos'];
$pmbaktif = GetSetVar('pmbaktif', GetaField('pmbperiod', 'NA', 'N', 'PMBPeriodID'));

// *** Main ***
TampilkanJudul("File Ujian Saringan Masuk");
$gos();
?>
