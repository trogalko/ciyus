<?php
// Author: Emanuel Setio Dewo
// 23 June 2006
// www.sisfokampus.net

// *** Functions ***
function KoreksiNilai() {
  global $_lf;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  
  $mxc = 80;
  $mxb = 50;
  $grs = str_pad('-', $mxc, '-').$_lf;
  $_prd = (empty($_SESSION['prodi']))? "Semua" : GetaField("prodi", 'ProdiID', $_SESSION['prodi'], 'Nama');
  $hdr = str_pad("Daftar SK Koreksi Nilai $_SESSION[tahun]", $mxc, ' ', STR_PAD_BOTH).$_lf.
    str_pad("Program Studi: $_prd ($_SESSION[prodi])", $mxc, ' ', STR_PAD_BOTH).$_lf.$grs.
    str_pad("No.", 5).
    str_pad("Nomer SK", 25).
    str_pad("N P M", 15).
    str_pad("Nama Mahasiswa", 26).
    str_pad("Lama", 5).
    str_pad("Baru", 5).$_lf.$grs;
  fwrite($f, $hdr);
  $whr = (empty($_SESSION['prodi']))? "" : "and m.ProdiID='$_SESSION[prodi]' ";
  $s = "select kn.*, LEFT(m.Nama, 25) as Nama, 
    m.ProdiID, m.ProgramID, mk.MKKode, mk.Nama as NamaMK
    from koreksinilai kn
      left outer join mhsw m on kn.MhswID=m.MhswID
      left outer join mk mk on kn.MKID=mk.MKID
    where kn.TahunID='$_SESSION[tahun]'
      $whr
    order by SK";
  $r = _query($s); $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    fwrite($f, str_pad($n, 5).
      str_pad($w['SK'], 25).
      str_pad($w['MhswID'], 15).
      str_pad($w['Nama'], 26).
      str_pad($w['GradeLama'], 5).
      str_pad($w['GradeNilai'], 5).
      $_lf); 
  }
  fwrite($f, $grs);
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'akd.lap');
}


// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');

// *** Main ***
TampilkanJudul("Daftar Koreksi Nilai Mahasiswa");
TampilkanTahunProdiProgram('akd.lap.koreksinilai', 'KoreksiNilai');
if (!empty($tahun)) KoreksiNilai();
?>
