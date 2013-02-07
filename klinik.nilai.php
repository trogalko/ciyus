<?php
// Author: Emanuel Setio Dewo
// 29 June 2006
// www.sisfokampus.net

// *** Functions ***
function DftrKRSMhswKlinik($jdwl) {
  CheckFormScript('GRD');
  echo "<p><a href='jadwal.klinik.cetak.php?gos=CetakNilaiKlinik&JadwalID=$jdwl[JadwalID]' target=_blank><img src='img/printer.gif'> Cetak Nilai</a> |
    <a href='?mnux=klinik.nilai&gos=Finalisasi&JadwalID=$jdwl[JadwalID]'>Finalisasi Nilai</a>";
  $s = "select k.*, m.Nama
    from krs k
      left outer join mhsw m on k.MhswID=m.MhswID
    where k.JadwalID='$jdwl[JadwalID]'
    order by k.MhswID";
  $r = _query($s); $n = 0;
  // Buat formulir
  $jml = _num_rows($r)+0;
  $_TglSurat = ($jdwl['TglSurat'] == '0000-00-00')? date('Y-m-d') : $jdwl['TglSurat'];
  $TglSurat = GetDateOption($_TglSurat, 'TglSurat');
  $frmnilai = "<form action='?' method=POST>
    <input type=hidden name='BypassMenu' value=1>
    <input type=hidden name='mnux' value='klinik.nilai'>
    <input type=hidden name='gos' value='NilaiKlinikSav'>
    <input type=hidden name='JadwalID' value='$jdwl[JadwalID]'>
    <input type=hidden name='jml' value='$jml'>";
  $frmnilai1 = "<p><table class=box cellspacing=0>
  <tr><td class=inp>Nomer Surat</td>
      <td class=ul><input type=text name='NoSurat' value='$jdwl[NoSurat]' size=20 maxlength=50></td>
      <td class=inp>Tgl Surat</td>
      <td class=ul>$TglSurat</td>";
  if ($jdwl['Final'] == 'N') {
    $_frmnilai = $frmnilai . $frmnilai1 . "<td class=ul><input type=submit name='Simpan' value='Simpan'></td>";
    $_frmbutton1 = "<input type=submit name='Simpan' value='Simpan'> <input type=reset name='Reset' value='Reset'>";
    $_frmbutton = "<tr><td class=ul colspan=6 align=right>$_frmbutton1</td></tr>";
    $_frmnilaiend = "</form>";
  }
  else {
    $_frmnilai = '';
    $_frmbutton = '';
    $_frmnilaiend = '';
  }
  echo $_frmnilai . "</tr></table></p>";;
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>#</th>
    <th class=ttl>NPM</th>
    <th class=ttl>Nama Mahasiswa</th>
    <th class=ttl>Nilai<br />Akhir</th>
    <th class=ttl>Grade</th>
    <th class=ttl>Bobot</th>
    </tr>";
  echo $_frmbutton;
  while ($w = _fetch_array($r)) {
    $n++;
    //$optnilai = GetOpsiNilaiKlinik($w['GradeNilai']);
    $ro = ($jdwl['Final'] == 'Y')? 'READONLY' : '';
    $nilai = "<input type=hidden name='KRS_$n' value='$w[KRSID]'>
        <td class=ul><input type=text name='NilaiAkhir_$n' value='$w[NilaiAkhir]' size=6 maxlengh=6 $ro></td>
        <td class=ul align=center><b>$w[GradeNilai]</b></td>
        <td class=ul align=right>$w[BobotNilai]</td>";
    echo "
    <tr><td class=inp>$n</td>
    <td class=ul>$w[MhswID]</td>
    <td class=ul>$w[Nama]</td>
    $nilai
    </tr>";
  }
  echo $_frmbutton;
  echo "</table></p>";
  echo $_formnilaiend;
}
function GetOpsiNilaiKlinik($grd) {
  global $arrnilai;
  $a = "<option></option>\r\n";
  for ($i=0; $i<sizeof($arrnilai); $i++) {
    $_x = $arrnilai[$i];
    $x = explode('~', $_x);
    $sel = ($grd == $x[0])? 'selected' : '';
    $k = $x[0];
    $_k = str_pad($k, 2, ' ');
    $a .= "<option value='$_x' $sel>$_k($x[1])</option>\r\n";
  }
  return $a;
}
function NilaiKlinikSav_xxx($jdwl) {
  $GRD = $_REQUEST['GRD'];
  if (!empty($GRD)) {
    $_grd = explode('~', $GRD);
    $KRSID = $_REQUEST['KRSID'];
    $s = "update krs set GradeNilai='$_grd[0]', BobotNilai=$_grd[1]
      where KRSID=$KRSID";
    $r = _query($s);
  } else echo ErrorMsg("Ada Kesalahan",
    "Nilai harus diisi. Perubahan tidak disimpan.");
  DftrKRSMhswKlinik($jdwl);
}
function NilaiKlinikSav($jdwl) {
  global $KodeID;
  // Simpan jdwl dulu
  $NoSurat = sqling($_REQUEST['NoSurat']);
  $TglSurat = "$_REQUEST[TglSurat_y]-$_REQUEST[TglSurat_m]-$_REQUEST[TglSurat_d]";
  if (($NoSurat != $jdwl['NoSurat']) || ($TglSurat != $jdwl['TglSurat'])) {
    $s = "update jadwal set NoSurat='$NoSurat', TglSurat='$TglSurat' where JadwalID='$jdwl[JadwalID]' ";
    $r = _query($s);
  }
  // Simpan data nilai
  $jml = $_REQUEST['jml'] +0;
  if ($jml > 0) {
    for ($i=1; $i <= $jml; $i++) {
      $krsid = $_REQUEST["KRS_$i"]+0;
      $NilaiAkhir = $_REQUEST["NilaiAkhir_$i"]+0;
      $krs = GetFields('krs', 'KRSID', $krsid, "*");
      $ProdiID = GetaField('mhsw', 'MhswID', $krs['MhswID'], 'ProdiID');
      $arrnilai = GetFields('nilai', 
        "KodeID='$KodeID' and NilaiMin <= '$NilaiAkhir' and '$NilaiAkhir' <= NilaiMax and ProdiID",
        $ProdiID, "Nama, Bobot");
      $GradeNilai = (empty($arrnilai['Nama']))? 'X' : $arrnilai['Nama'];
      $BobotNilai = $arrnilai['Bobot']+0;
      // Simpan data
      $s = "update krs set NilaiAkhir='$NilaiAkhir',
        GradeNilai='$GradeNilai', BobotNilai='$BobotNilai'
        where KRSID=$krsid";
      $r = _query($s);
    }
  }
  echo "<script>window.location = '?mnux=$_SESSION[mnux]';</script>";
}
/*
$ProdiID = GetaField('mhsw', "MhswID", $w['MhswID'], "ProdiID");
    $arrgrade = GetFields('nilai', 
      "KodeID='$_SESSION[KodeID]' and NilaiMin <= $nilai and $nilai <= NilaiMax and ProdiID",
      $ProdiID, "Nama, Bobot");
*/
function TampilkanHeaderMatakuliahKlinik($jdwl, $mnux='') {
  $NamaRS = GetaField('rumahsakit', 'RSID', $jdwl['RuangID'], 'Nama');
  $TM = FormatTanggal($jdwl['TglMulai']);
  $TS = FormatTanggal($jdwl['TglSelesai']);
  echo "<p><table class=box cellspacing=1>
  <tr><td class=inp>Matakuliah</td>
    <td class=ul>$jdwl[Nama] ($jdwl[MKKode])</td>
    <td class=inp>SKS</td>
    <td class=ul>$jdwl[SKS]</td>
    </tr>
  <tr><td class=inp>Rumah Sakit</td>
    <td class=ul>$NamaRS ($jdwl[RuangID])</td>
    <td class=inp>Periode</td>
    <td class=ul>$TM s/d $TS</td>
    </tr>
  <tr><td class=ul colspan=3>
    <input type=button name='Kembali' value='Kembali ke Daftar'
      onClick=\"location='?mnux=jadwal.klinik'\">
    </td></tr>
  </table></p>";
}
function BuatArrayNilai($jdwl) {
  $prd = TRIM($jdwl['ProdiID'], '.');
  $s = "select Nama, Bobot
    from nilai
    where ProdiID='$prd'
    order by Bobot desc";
  $r = _query($s);
  $a = array();
  while ($w = _fetch_array($r)) {
    $a[] = "$w[Nama]~$w[Bobot]";
  }
  return $a;
}
function Finalisasi($jdwl) {
  echo Konfirmasi("Konfirmasi Finalisasi",
    "Anda akan melakukan finalisasi untuk matakuliah <font size=+1>$jdwl[MKKode]</font> ini?<br />
    Setelah difinalisasi nilai mahasiswa tidak bisa diubah lagi.
    <hr size=1 color=silver>
    Pilihan: <input type=button name='Finalisasi' value='Finalisasi' 
      onClick=\"location='?mnux=klinik.nilai&gos=Finalisasi1&JadwalID=$jdwl[JadwalID]'\">
    <input type=button name='Batal' value='Tidak Jadi' onClick=\"location='?mnux=klinik.nilai'\">");
}
function Finalisasi1($jdwl) {
  $s = "update jadwal set Final='Y' where JadwalID='$jdwl[JadwalID]' ";
  $r = _query($s);
  $jdwl['Final'] = 'Y';
  DftrKRSMhswKlinik($jdwl);
}

// *** Parameters ***
$JadwalID = GetSetVar('JadwalID');
$gos = (empty($_REQUEST['gos']))? "DftrKRSMhswKlinik" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Penilaian Klinik");
if (!empty($JadwalID)) {
  $jdwl = GetFields('jadwal', 'JadwalID', $JadwalID, '*');
  // Cek apakah matakuliah klinik?
  if ($jdwl['NamaKelas'] == 'KLINIK') {
    TampilkanHeaderMatakuliahKlinik($jdwl);
    // Cek apakah telah difinalisasi?
    if ($jdwl['Final'] == 'N') {
      $arrnilai = BuatArrayNilai($jdwl);
      $gos($jdwl);
    }
    else {
      echo ErrorMsg("Nilai Telah Final",
        "Nilai matakuliah <font size=+1>$jdwl[Nama]</font> ($jdwl[MKKode]) telah final.<br />
        Anda tidak dapat mengubah nilai matakuliah ini.");
      DftrKRSMhswKlinik($jdwl);
    }
  }
  else echo ErrorMsg("Bukan Matakuliah Klinik",
    "Matakuliah <font size=+1>$jdwl[Nama]</font> ($jdwl[MKKode]) bukan matakuliah Klinik.<br />
    Anda tidak dapat mengisi nilai matakuliah ini.");
}
?>
