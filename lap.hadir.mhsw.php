<?php
// Author: Emanuel Setio Dewo
// 18 April 2006
session_start();
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
PresensiMhsw();
include_once "disconnectdb.php";

function PresensiMhsw() {
  global $_lf;
  $DariNPM = GetSetVar('DariNPM');
  $SampaiNPM = GetSetVar('SampaiNPM');
  $JadwalID = $_REQUEST['JadwalID'];
  $jdwl = GetFields("jadwal", "JadwalID", $JadwalID, "*");
  $jns = GetaField("jenisjadwal", "JenisJadwalID", $jdwl['JenisJadwalID'], 'Nama');
  // Program Studi
  $arrprodi = explode('.', TRIM($jdwl['ProdiID'], '.'));
  $strprodi = implode(',', $arrprodi);
  $prodi = (empty($strprodi))? "" : GetArrayTable("select Nama from prodi where ProdiID in ($strprodi) order by ProdiID",
    "ProdiID", "Nama", ", ");
  // Dosen
  $arrdosen = explode('.', TRIM($jdwl['DosenID'], '.'));
  $strdosen = implode(',', $arrdosen);
  $dosen = (empty($strdosen))? '' : GetArrayTable("select Nama from dosen where Login in ($strdosen) order by Nama",
      "Login", "Nama", ', ');
  $tglakhir = GetaField('presensi', "JadwalID", $JadwalID, "max(Tanggal)");
  $_tglakhir = FormatTanggal($tglakhir);
  // Buat file
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15));
  $mxb = 50; $brs = 0;
  $mxc = 112;
  $div = str_pad('-', $mxc, '-').$_lf;
  // Buat Header
  $RentangNPM = (!empty($DariNPM) && !empty($SampaiNPM))? "and '$DariNPM'<=krs.MhswID and krs.MhswID<='$SampaiNPM' " : '';
  $hdrRentangNPM = (!empty($DariNPM) && !empty($SampaiNPM))? 
    "Rentang NPM  : $DariNPM s/d $SampaiNPM " . $_lf : '';
  $hdr = str_pad("*** Daftar Absensi Mahasiswa per Matakuliah ***", $mxc, ' ', STR_PAD_BOTH).$_lf.$_lf.
    "Semester     : " . $jdwl['TahunID'] . $_lf.
    "Fak/Jur      : " . $prodi . $_lf.
    "Jenis Kuliah : " . $jns . $_lf.
    "Kode MK      : " . $jdwl['MKKode'] . ' - ' . $jdwl['Nama'] . ' (Kelas ' . $jdwl['NamaKelas'] .')'.$_lf.
    "Dosen        : " . $dosen . $_lf.
    "Kehadiran    : " . $jdwl['Kehadiran'] . " kali s.d tanggal $_tglakhir " . $_lf.
    $hdrRentangNPM.
    "Catatan      : NPM yang tidak tercetak berarti 100% hadir ". $_lf.
    $div .
    "No. NPM            Mahasiswa                               Tanggal     Jam    Alasan    % Hadir " . $_lf.
    $div;
  // Ambil isi
  fwrite($f, $hdr);
  $s = "select krs.KRSID, krs.MhswID, m.Nama
    from krs krs
      left outer join mhsw m on krs.MhswID=m.MhswID
    where krs.JadwalID='$JadwalID' $RentangNPM
    order by krs.MhswID";
  $r = _query($s); $n = 0;
  while ($w = _fetch_array($r)) {
    $hadir = GetaField("presensimhsw", "KRSID", $w['KRSID'], "sum(Nilai)")+0;
    if ($hadir < $jdwl['Kehadiran']) {
      $n++; $brs++;
      AmbilDetailMhsw($n, $f, $jdwl, $w, $hadir, $brs, $mxb, $hdr, $div);
      fwrite($f, $_lf);
    }
  }
  $_tgl = date('d-m-Y H:i');
  fwrite($f, $div);
  fwrite($f, "Dicetak oleh: $_SESSION[_Login], $_tgl".$_lf);
  fwrite($f, chr(12));
  // Tutup file
  fclose($f);
  if ($_REQUEST['prn'] == 1) {
    include_once "dwoprn.php";
    DownloadDWOPRN($nmf);
  }
  else TampilkanFileDWOPRN($nmf, "");
}
function AmbilDetailMhsw($n, $f, $jdwl, $krs, $hadir, &$brs, $mxb, $hdr, $div) {
  global $_lf;
  $prsnhadir = ($jdwl['Kehadiran'] == 0)? 0 : $hadir/$jdwl['Kehadiran']*100;
  $s = "select pm.*, jp.Nama as NM,
    date_format(p.Tanggal, '%d-%m-%Y') as TGL,
    time_format(p.JamMulai, '%H:%i') as JAM
    from presensimhsw pm
      left outer join presensi p on p.PresensiID=pm.PresensiID
      left outer join jenispresensi jp on jp.JenisPresensiID=pm.JenisPresensiID 
    where KRSID=$krs[KRSID] and pm.Nilai=0";
  $r = _query($s); $jml = _num_rows($r);
  $nn = 0;
  if ($jml <= 0) { 
    fwrite($f, str_pad($n, 4, ' ').
      str_pad($krs['MhswID'], 15).
      str_pad($krs['Nama'], 40).
      str_pad(' ', 29).
      str_pad(number_format($prsnhadir, 2), 5, ' ', STR_PAD_LEFT).' %'.
      $_lf);
  }
  while ($w = _fetch_array($r)) {
    $nn++;  $brs++;
    if($brs > $mxb){
			  fwrite($f,$div);
				$hal++; $brs = 1;
				fwrite($f, chr(12));
				fwrite($f, $hdr.$_lf);
			}
    if ($nn == 1) {
      $str = str_pad($n, 4, ' ') .
        str_pad($krs['MhswID'], 15) .
        str_pad($krs['Nama'], 40, ' ') .
        str_pad($w['TGL'], 12).
        str_pad($w['JAM'], 7).
        str_pad($w['NM'], 10).
        str_pad(number_format($prsnhadir, 2), 5, ' ', STR_PAD_LEFT). ' %'.
        $_lf; 
    }
    else {
      $str = str_pad(' ', 59).
        str_pad($w['TGL'], 12).
        str_pad($w['JAM'], 7).
        str_pad($w['NM'], 10).
        $_lf;
    }
    fwrite($f, $str);
  }
  //fwrite($f, $_lf);
}
?>
