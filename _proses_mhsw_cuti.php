<?php
// Emanuel Setio Dewo, 13/03/2007

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Proses Mhsw Cuti");

// *** Parameters ***

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'TampilkanPesanProsesCuti' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanPesanProsesCuti() {
  TampilkanJudul("Proses Mhsw Cuti");
  $jml = number_format(GetaField('_mhswcuti', 'StatusMhswID', 'C', 'count(*)'));
  echo "<p>Script CUTI ini akan memproses data cuti mhsw dari tabel <font size=+1>_mhswcuti</font>.<br />
  Pastikan tabel tersebut sudah terisi.<br />
  Script akan mengecek apakah data KHS mhsw sudah ada atau belum. Jika sudah ada, maka data tidak diproses.
  Sedangkan jika belum ada, maka sistem akan menambahkan data di tabel KHS.<br />
  Saat ini terdeteksi <font size=+1>$jml</font> data yg akan diproses.
  <hr>
  Opsi: <input type=button name='Proses' value='Proses' onClick=\"location='_proses_mhsw_cuti.php?gos=ProsesMhswCuti0'\">
  ";
}
function ProsesMhswCuti0() {
  TampilkanJudul("Proses...");
  $s = "select * from _mhswcuti order by TahunID, MhswID";
  $r = _query($s); $n = 0;
  $_SESSION['CUTI-JML'] = _num_rows($r);
  $_SESSION['CUTI-POS'] = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION['CUTI-MhswID-'.$n] = $w['MhswID'];
    $_SESSION['CUTI-TahunID-'.$n] = $w['TahunID'];
    $_SESSION['CUTI-StatusMhswID-'.$n] = $w['StatusMhswID']; 
  }
  echo "<IFRAME src='_proses_mhsw_cuti.php?gos=ProsesMhswCuti' width=90% height=80%></IFRAME>";
}
function ProsesMhswCuti() {
  $jml = $_SESSION['CUTI-JML'];
  $pos = $_SESSION['CUTI-POS']++;
  
  $MhswID = $_SESSION['CUTI-MhswID-'.$pos];
  $TahunID = $_SESSION['CUTI-TahunID-'.$pos];
  $StatusMhswID = $_SESSION['CUTI-StatusMhswID-'.$pos];
  
  if (!empty($MhswID)) {
    // Proses
    $khs = GetFields('khs', "MhswID='$MhswID' and TahunID", $TahunID, "*");
    if (empty($khs)) {
      $mhsw = GetFields('mhsw', 'MhswID', $MhswID, 'ProgramID, ProdiID');
      // Tambahkan di KHS
      $s = "insert into khs
        (TahunID, KodeID,
        ProgramID, ProdiID, MhswID,
        StatusMhswID, Sesi,
        LoginBuat, TanggalBuat, NA)
        values
        ('$TahunID', 'UKRIDA',
        '$mhsw[ProgramID]', '$mhsw[ProdiID]', '$MhswID',
        '$w[StatusMhswID]', 99,
        'CUTI20070313', now(), 'N')";
      $r = _query($s);
      $statusproses = "DIPROSES";
      
      // Urutkan
      $s = "select KHSID, Sesi, TahunID
        from khs
        where MhswID='$MhswID'
        order by TahunID";
      $r = _query($s); $n = 0; $_urut = '';
      while ($w = _fetch_array($r)) {
        $n++;
        $s1 = "update khs set Sesi=$n where KHSID='$w[KHSID]'";
        $r1 = _query($s1);
        $_urut .= "$n. $w[TahunID] ($w[StatusMhswID])<br />";
      }
    }
    
    // Tampilkan
    $prs = ($jml >0)? number_format($pos/$jml*100) : 0;
    echo "<p>Proses: <font size=+2>$prs</font>%<br />
    - Detail: $pos/$jml<br />
    - MhswID: $MhswID<br />
    - TahunID: $TahunID<br />
    - StatusMhsw: $StatusMhswID<br />
    - Sesi: <font size=+1>$sesi</font><br />
    - Status Proses: $statusproses<br />
    <hr>
    $_urut
    </p>";
  }
  
  if ($pos <= $jml) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 0);</script>";
  }
  else echo "<hr><p>Proses Selesai.<br />
  Opsi: <a href='?' target=_top>Kembali</a></p>";
}
?>
