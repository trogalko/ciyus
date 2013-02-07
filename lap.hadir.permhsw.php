<?php
// Author: Emanuel Setio Dewo
// 27 Juli 2006
// www.sisfokampus.net

// *** Functions ***
function CetakKehadiran() {
  $mhsw = GetFields('mhsw', 'MhswID', $_SESSION['crmhswid'], '*');
  if (empty($mhsw)) echo ErrorMsg("Mhsw Tidak Ditemukan",
    "Mahasiswa tidak ditemukan. Masukkan NPM yang valid.");
  else {
    CetakKehadiranMhsw($mhsw);
  }
}
function CetakKehadiranMhsw($mhsw) {
  global $_lf;
  $s = "select pm.*, j.Kehadiran, j.JadwalSer, 
    j.MKKode, LEFT(j.Nama, 30) as NamaMK, j.NamaKelas, j.JenisJadwalID, j.SKS,
    sum(pm.Nilai) as JML
    from presensimhsw pm
      left outer join jadwal j on pm.JadwalID=j.JadwalID
    where pm.MhswID='$mhsw[MhswID]' and j.TahunID='$_SESSION[tahun]' 
    group by pm.JadwalID";
  $r = _query($s);
  // buat file
  $nmf = "tmp\\$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  // parameter
  $mxc = 114;
  $mxb = 60;
  $grs = str_pad('-', $mxc, '-').$_lf;
  $thn = GetaField('tahun', "ProgramID='$mhsw[ProgramID]' and ProdiID='$mhsw[ProdiID]' and TahunID",
    $_SESSION['tahun'], 'Nama');
  $hdr = str_pad('*** Rekap Kehadiran per Mahasiswa ***', $mxc, ' ', STR_PAD_BOTH).$_lf.
    "Tahun Akd : $_SESSION[tahun] - $thn " . $_lf.
    "Mahasiswa : $mhsw[MhswID] - $mhsw[Nama] ". $_lf.
    $grs.
    str_pad('No.', 4). str_pad('Kode', 8). str_pad('Matakuliah', 31). str_pad('Kls', 4).str_pad("Dosen",6).
    str_pad('  Hadir', 10).$_lf.
    $grs;
  fwrite($f, $hdr);
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $persen = ($w['Kehadiran'] == 0)? 0 : $w['JML']/$w['Kehadiran']*100;
    $Jen = ($w['JenisJadwalID'] == 'R') ? "(R)" : '';
    $_persen = number_format($persen, 2);
    fwrite($f, str_pad($n.'.', 4).
      str_pad($w['MKKode'], 8).
      str_pad($w['NamaMK'].$Jen, 31).
      str_pad($w['NamaKelas'], 4).
      //str_pad($w['JML'], 3).
      str_pad($w['Kehadiran'].' X',5, ' ',STR_PAD_LEFT).
      //str_pad($w['JML'].' X',5, ' ',STR_PAD_LEFT).
      str_pad($_persen.'%', 9, ' ',STR_PAD_LEFT).
      $_lf);
      AmbilDetail($f, $mhsw, $w);
  }
  fwrite($f, $grs);
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'lap.hadir.permhsw');
}
function AmbilDetail($f, $mhsw, $w) {
  global $_lf;
  $s = "select pm.*, jp.Nama, jp.Nilai, p.Tanggal
    from presensimhsw pm
      left outer join presensi p on pm.PresensiID=p.PresensiID
      left outer join jenispresensi jp on pm.JenisPresensiID=jp.JenisPresensiID
    where pm.MhswID='$mhsw[MhswID]' and pm.JadwalID=$w[JadwalID]
      and pm.JenisPresensiID <> 'H'
    order by p.Tanggal";
  $r = _query($s);
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $tgl = FormatTanggal($w['Tanggal']);
    fwrite($f, str_pad(' ', 12). 
      str_pad($n.'.', 4).
      str_pad($tgl, 14).
      str_pad($w['Nama'], 10).
      $_lf);
  }
}

// *** Parameters ***
$crmhswid = GetSetVar('crmhswid');
$tahun = GetSetVar('tahun');
$gos = (empty($_REQUEST['gos']))? "donothing" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Laporan Kehadiran per Mahasiswa");
TampilkanPencarianMhswTahun('lap.hadir.permhsw', 'CetakKehadiran', 1);
if (!empty($crmhswid) && !empty($tahun)) $gos();
?>
