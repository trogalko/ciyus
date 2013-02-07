<?php
// Author: Emanuel Setio Dewo, 18 Oktober 2006

// *** Functions ***
function HeaderSkorsing($mhsw) {
  return "<tr><td class=inp>NPM</td>
    <td class=ul><b>$mhsw[MhswID]</b></td>
    <td class=inp>Nama</td>
    <td class=ul><b>$mhsw[Nama]</td></tr>
  <tr><td class=inp>Program</td>
    <td class=ul><b>$mhsw[PRG]</b></td>
    <td class=inp>Program Studi</td>
    <td class=ul><b>$mhsw[PRD]</td></tr>
  <tr><td class=inp>Angkatan</td>
    <td class=ul>$mhsw[TahunID]</td>
    <td class=inp>Batas Studi</td>
    <td class=ul>$mhsw[BatasStudi]</td></tr>
  <tr><td class=inp>Penasehat Akd</td>
    <td class=ul colspan=3>$mhsw[PA]</td></tr>";
}
function TrxSkorsing1($mhsw) {
  CheckFormScript("NoSurat");
  $str = "<font color=red>*) Kosongkan jika tidak perlu";
  $hdr = HeaderSkorsing($mhsw);
  $TglSurat = GetDateOption(date('Y-m-d'), 'TglSurat');
  echo Konfirmasi("Konfirmasi Skorsing",
  "Anda akan membuat transaksi skorsing utk
  <p><table class=box cellspacing=1 cellpadding=4>
  $hdr
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='skorsing.det'>
  <input type=hidden name='gos' value='TrxSkorsing2'>
  <input type=hidden name='mhswid' value='$mhsw[MhswID]'>
  <tr><th class=ttl colspan=4>Skorsing</th></tr>
  <tr><td class=inp rowspan=4>Tahun Akademik<br />yg Diskorsing</td>
    <td class=ul>Semester ke-1</td><td class=ul colspan=2><input type=text name='Tahun1' size=10 maxlength=10></td></tr>
    <td class=ul>Semester ke-2</td><td class=ul colspan=2><input type=text name='Tahun2' size=10 maxlength=10> $str</td></tr>
    <td class=ul>Semester ke-3</td><td class=ul colspan=2><input type=text name='Tahun3' size=10 maxlength=10> $str</td></tr>
    <td class=ul>Semester ke-4</td><td class=ul colspan=2><input type=text name='Tahun4' size=10 maxlength=10> $str</td></tr>
  <tr><td class=inp>Nomer Surat</td>
    <td class=ul colspan=3><input type=text name='NoSurat' size=50 maxlength=50></td></tr>
  <tr><td class=inp>Tgl Surat</td><td class=ul colspan=3>$TglSurat</td></tr>
  <tr><td class=ul colspan=4>Keterangan<br />
    <textarea name='Keterangan' cols=50 rows=4></textarea></td></tr>
  <tr><td class=ul colspan=4><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=skorsing'\"></td></tr>
  </table></p>");
}
function TrxSkorsing2($mhsw) {
  $Tahun1 = $_REQUEST['Tahun1'];
  $Tahun2 = $_REQUEST['Tahun2'];
  $Tahun3 = $_REQUEST['Tahun3'];
  $Tahun4 = $_REQUEST['Tahun4'];
  $NoSurat = sqling($_REQUEST['NoSurat']);
  $TglSurat = "$_REQUEST[TglSurat_y]-$_REQUEST[TglSurat_m]-$_REQUEST[TglSurat_d]";
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $_TglSurat = FormatTanggal($TglSurat);
  $hdr = HeaderSkorsing($mhsw);
  echo "<p><table class=box cellspacing=1>$hdr
  <tr><td class=inp>No Surat</td><td class=ul colspan=3><b>$NoSurat</td></tr>
  <tr><td class=inp>Tanggal Surat</td><td class=ul colspan=3><b>$_TglSurat</td></tr>
  <tr><td class=inp>Keterangan</td><td class=ul colspan=3>$Keterangan</td></tr>
  </table>";
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>#</th>
    <th class=ttl>Tahun</th>
    <th class=ttl>Smt</th>
    <th class=ttl>Status</th>
    <th class=ttl>Juml<br />MK</th>
    <th class=ttl>SKS<br />Diambil</th>
    <th class=ttl>Catatan</th>
    </tr>";
  echo "<form action='?' method=POST>
  <input type=hidden name='mnux' value='skorsing.det'>
  <input type=hidden name='gos' value='TrxSkorsing3'>
  <input type=hidden name='Tahun1' value='$Tahun1'>
  <input type=hidden name='Tahun2' value='$Tahun2'>
  <input type=hidden name='Tahun3' value='$Tahun3'>
  <input type=hidden name='Tahun4' value='$Tahun4'>
  <input type=hidden name='NoSurat' value='$NoSurat'>
  <input type=hidden name='TglSurat' value='$TglSurat'>
  <input type=hidden name='Keterangan' value='$Keterangan'>
  ";
  CekTahunSkors($mhsw, $Tahun1, 1);
  CekTahunSkors($mhsw, $Tahun2, 2);
  CekTahunSkors($mhsw, $Tahun3, 3);
  CekTahunSkors($mhsw, $Tahun4, 4);
  echo "<tr><td class=inp>&nbsp;</td><td class=ul colspan=6><input type=submit name='Proses' value='Proses Skorsing'>
    <input type=button name='Batalkan' value='Batalkan Proses' onClick=\"location='?mnux=skorsing'\"></td></tr>";
  echo "</form></table></p>";
}
function CekTahunSkors($mhsw, $thn, $k) {
  $khs = GetFields("khs k left outer join statusmhsw sm on k.StatusMhswID=sm.StatusMhswID", 
    "k.MhswID='$mhsw[MhswID]' and k.TahunID", $thn, "k.*, sm.Nama as SM");
  if (!empty($khs)) {
    echo "<tr><td class=inp>$k</td>
    <td class=ul>$thn</td>
    <td class=ul align=right>$khs[Sesi]</td>
    <td class=ul align=right>$khs[SM]</td>
    <td class=ul align=right>$khs[JumlahMK]</td>
    <td class=ul align=right>$khs[TotalSKS]</td>
    <td class=ul>KRS akan dinonaktifkan. Kegiatan akd dihapus & kewajiban KRS akan di buat 0.<br />
      Jika telah membayar, maka dianggap kelebihan bayar.</td>
    </tr>";
  }
  elseif (!empty($thn)) {
    echo "<tr><td class=inp>$k</td>
    <td class=ul>$thn</td>
    <td class=nac colspan=4>Belum diaktifkan</td>
    <td class=ul>Smt akan dibuat tetapi dgn status SKORS. Mhsw tdk dapat melakukan kegiatan akademik.</td>
    </tr>";
  }
}
function TrxSkorsing3($mhsw) {
  $Tahun1 = $_REQUEST['Tahun1'];
  $Tahun2 = $_REQUEST['Tahun2'];
  $Tahun3 = $_REQUEST['Tahun3'];
  $Tahun4 = $_REQUEST['Tahun4'];
  $NoSurat = sqling($_REQUEST['NoSurat']);
  $TglSurat = $_REQUEST['TglSurat'];
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $strs = '';
  $strs .= SkorsKHS($mhsw, $Tahun1, $NoSurat, $TglSurat, $Keterangan);
  $strs .= SkorsKHS($mhsw, $Tahun2, $NoSurat, $TglSurat, $Keterangan);
  $strs .= SkorsKHS($mhsw, $Tahun3, $NoSurat, $TglSurat, $Keterangan);
  $strs .= SkorsKHS($mhsw, $Tahun4, $NoSurat, $TglSurat, $Keterangan);
  echo Konfirmasi("Proses Skorsing", "Berikut adalah proses skorsing: <ol>$strs</ol>
    <hr size=1 color=silver>
    <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=skorsing'\">");
}
function SkorsKHS($mhsw, $thn, $NoSurat='', $TglSurat='', $Keterangan='') {
  global $KodeID;
  $_StatusSkors = 'S';
  $khs = GetFields("khs k left outer join statusmhsw sm on k.StatusMhswID=sm.StatusMhswID", 
    "k.MhswID='$mhsw[MhswID]' and k.TahunID", $thn, "k.*, sm.Nama as SM");
  if (!empty($khs)) {
    // 1. Bekukan KRS
    $s1 = "update krs set StatusKRSID='$_StatusSkors' where KHSID=$khs[KHSID] ";
    $r1 = _query($s1);
    // 2. Kewajiban dinolkan
    $_BipotRefund = array(5, 16);
    for ($i = 0; $i < sizeof($_BipotRefund); $i++) {
      $_b = $_BipotRefund[$i];
      $s2 = "update bipotmhsw set Besar=0 where BIPOTNamaID=$_b and TahunID='$thn' and MhswID='$mhsw[MhswID]' ";
      $r2 = _query($s2);
    }
    // 2a. Hitung ulang
    include_once "mhswkeu.lib.php";
    HitungBiayaBayarMhsw($mhsw['MhswID'], $khs['KHSID']);
    // 3. Set status mhsw di KHS menjadi skors
    $s3 = "update khs set StatusMhswID='$_StatusSkors', NoSurat='$NoSurat', 
      TglSurat='$TglSurat', Keterangan='$Keterangan' 
      where KHSID='$khs[KHSID]' ";
    $r3 = _query($s3);
    return "<li>$thn &raquo; Diskorsing. KRS dibekukan, Kewajiban SKS dibekukan.</li>";
  }
  elseif (!empty($thn)) {
    $_sesi = GetaField('khs', "MhswID", $mhsw['MhswID'], "max(Sesi)")+1;
    // 1. Buat KHS dgn status SKORS
    $s1 = "insert into khs (TahunID, KodeID, ProgramID, ProdiID, MhswID,
      StatusMhswID, Sesi, BIPOTID,
      NoSurat, TglSurat, Keterangan,
      LoginBuat, TanggalBuat)
      values ('$thn', '$KodeID', '$mhsw[ProgramID]', '$mhsw[ProdiID]', '$mhsw[MhswID]',
      '$_StatusSkors', '$_sesi', '$mhsw[BIPOTID]',
      '$NoSurat', '$TglSurat', '$Keterangan',
      '$_SESSION[_Login]', now())";
    $r1 = _query($s1);
    return "<li>$thn &raquo; Dibuat dgn status Skors (S). Mhsw tidak dapat melakukan kegiatan akademik.</li>";
  }
}

// *** Parameters ***
$mhswid = GetSetVar('mhswid');
$gos = (empty($_REQUEST['gos']))? 'TrxSkorsing1' : $gos;

// *** Main ***
TampilkanJudul("Skorsing Mahasiswa");
if (!empty($mhswid)) {
  $mhsw = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    left outer join dosen d on m.PenasehatAkademik=d.Login",
    'm.MhswID', $mhswid,
    "m.*, prg.Nama as PRG, prd.Nama as PRD, sm.Nama as SM, sm.Keluar,
    concat(d.Nama, ', ', d.Gelar) as PA");
  if ($mhsw['Keluar'] == 'Y')
    echo ErrorMsg("Tidak Dapat Diskors",
      "Status Mahasiswa: <b>$mhsw[SM]</b> yang berarti sudah tidak
      dapat diskors lagi.
      <hr size=1 color=silver>
      Pilihan: <a href='?mnux=skorsing'>Kembali</a>");
  else $gos($mhsw);
}
?>
