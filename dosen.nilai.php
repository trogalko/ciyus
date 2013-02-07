<?php
// Author: Emanuel Setio Dewo
// 15 March 2006
// www.sisfokampus.net

include_once "jadwal.lib.php";
// *** Functions ***

function ValidJadwal($jadwalid, $dosen) {
  $ada = GetaField('jadwal', "INSTR(DosenID, '.$dosen.')>0 and JadwalID", $jadwalid, "JadwalID");
  return (!empty($ada));
}
/*
  <tr><td class=inp1>� Tugas 1</td><td class=ul><input type=text name='Tugas1' value='$jdwl[Tugas1]' size=4 maxlength=3 onChange='HitungBobot(bobot)'> %</td></tr>
  <tr><td class=inp1>� Tugas 2</td><td class=ul><input type=text name='Tugas2' value='$jdwl[Tugas2]' size=4 maxlength=3 onChange='HitungBobot(bobot)'> %</td></tr>
  <tr><td class=inp1>� Tugas 3</td><td class=ul><input type=text name='Tugas3' value='$jdwl[Tugas3]' size=4 maxlength=3 onChange='HitungBobot(bobot)'> %</td></tr>
  <tr><td class=inp1>� Tugas 4</td><td class=ul><input type=text name='Tugas4' value='$jdwl[Tugas4]' size=4 maxlength=3 onChange='HitungBobot(bobot)'> %</td></tr>
  <tr><td class=inp1>� Tugas 5</td><td class=ul><input type=text name='Tugas5' value='$jdwl[Tugas5]' size=4 maxlength=3 onChange='HitungBobot(bobot)'> %</td></tr>

*/
function scriptResetBobotDefault() {
  echo "<script>
  function ResetBobotDefault() {
    bobot.Presensi.value = 10;
    bobot.TugasMandiri.value = 20;
    bobot.Tugas1.value = 0;
    bobot.Tugas2.value = 0;
    bobot.Tugas3.value = 0;
    bobot.Tugas4.value = 0;
    bobot.Tugas5.value = 0;
    bobot.UTS.value = 20;
    bobot.UAS.value = 50;
    bobot.TOT.value = 100;
    alert('Tekan tombol Simpan untuk menyimpan perubahan');
  }
  </script>";
}
function bobot($jdwl) {
  global $mnux, $pref;
  scriptResetBobotDefault();
  //for ($i=1; $i<=5; $i++) $tot += $jdwl['Tugas'.$i];
  if ($jdwl['TugasMandiri'] == 0)
    $tot = $jdwl['Tugas1'] + $jdwl['Tugas2'] + $jdwl['Tugas3'] + $jdwl['Tugas4'] + $jdwl['Tugas5'] + $jdwl['Presensi'] + $jdwl['UTS'] + $jdwl['UAS'];
  else $tot = $jdwl['TugasMandiri'] + $jdwl['Presensi'] + $jdwl['UTS'] + $jdwl['UAS'];
  TuliskanScriptHitungBobot();
  $_strTugas = "Jika <b>Tugas Mandiri</b> diisi, maka persen di sini diabaikan.";
  // Jika sdh difinalisasi tdk dpt disimpan lagi
  if ($jdwl['Final'] == 'Y') {
    $disable = "READONLY=TRUE";
    $strSubmit = "";
    $strResetDefault = '';
  }
  else {
    $disable = ($jdwl['Penilaian'] == 'web') ? '' : "READONLY=TRUE";
    //$disable = '';
    $strSubmit = "<input type=submit name='Simpan' value='Simpan'>";
    $strResetDefault = "<input type=button name='ResetDefault' value='Reset Bobot ke Nilai Default' $disable onClick=\"ResetBobotDefault()\">";
  }
  //echo $jdwl['Penilaian'];
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='bobot' method=POST onSubmit=\"return CheckBobot(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='bobot'>
  <input type=hidden name='slnt' value='dosen.nilai.sav'>
  <input type=hidden name='slntx' value='NilaiSav'>
  <input type=hidden name='jadwalid' value='$jdwl[JadwalID]'>
  
  <tr><th class=ttl colspan=3>Bobot Nilai</th></tr>
  <tr><td class=inp1 colspan=2>Presensi</td>
    <td class=ul><input type=text name='Presensi' value='$jdwl[Presensi]' $disable size=5 maxlength=6 onChange='HitungBobot(bobot)'> %</td></tr>
  <tr><td class=inp1 colspan=2>Tugas Mandiri</td>
    <td class=ul><input type=text name='TugasMandiri' value='$jdwl[TugasMandiri]' $disable size=5 maxlength=6 onChange='HitungBobot(bobot)'> % <font color=red>Jika diisi selain 0, maka persentase akan otomatis didistribusikan ke setiap komponen Tugas.</td></tr>
  <tr><td class=ul><img src='img/brch.gif'></td><td class=inp1>Tugas 1</td>
    <td class=ul><input type=text name='Tugas1' value='$jdwl[Tugas1]' $disable size=5 maxlength=6 onChange='HitungBobot(bobot)'> % $_strTugas</td></tr>
  <tr><td class=ul><img src='img/brch.gif'></td><td class=inp1>Tugas 2</td>
    <td class=ul><input type=text name='Tugas2' value='$jdwl[Tugas2]' $disable size=5 maxlength=6 onChange='HitungBobot(bobot)'> % $_strTugas</td></tr>
  <tr><td class=ul><img src='img/brch.gif'></td><td class=inp1>Tugas 3</td>
    <td class=ul><input type=text name='Tugas3' value='$jdwl[Tugas3]' $disable size=5 maxlength=6 onChange='HitungBobot(bobot)'> % $_strTugas</td></tr>
  <tr><td class=ul><img src='img/brch.gif'></td><td class=inp1>Tugas 4</td>
    <td class=ul><input type=text name='Tugas4' value='$jdwl[Tugas4]' $disable size=5 maxlength=6 onChange='HitungBobot(bobot)'> % $_strTugas</td></tr>
  <tr><td class=ul><img src='img/brch.gif'></td><td class=inp1>Tugas 5</td>
    <td class=ul><input type=text name='Tugas5' value='$jdwl[Tugas5]' $disable size=5 maxlength=6 onChange='HitungBobot(bobot)'> % $_strTugas</td></tr>

  <tr><td class=inp1 colspan=2>Ujian Tengah Semester</td>
    <td class=ul><input type=integer name='UTS' value='$jdwl[UTS]' $disable size=5 maxlength=6 onChange='HitungBobot(bobot)'> %</td></tr>
  <tr><td class=inp1 colspan=2>Ujian Akhir Semester</td>
    <td class=ul><input type=integer name='UAS' value='$jdwl[UAS]' $disable size=5 maxlength=6 onChange='HitungBobot(bobot)'> %</td></tr>
  <tr><td class=ttl colspan=2><b>Total Bobot</b></td>
    <td class=cnnY><input type=integer name='TOT' READONLY value='$tot' $disable size=5 maxlength=6 onChange='HitungBobot(bobot)'> %
    $strResetDefault</td></tr>
  
  <tr><td class=inp1 colspan=2>Jika ada Responsi,<br />
    maka persentase responsi:</td><td class=ul><input type=integer name='Responsi' value='$jdwl[Responsi]' $disable size=5 maxlength=6></td></tr>
  
  <tr><td colspan=2>$strSubmit
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form></table></p>";
}
function TuliskanScriptHitungBobot() {
  echo <<<END
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function HitungBobot(frm) {
    var tm = parseFloat(frm.TugasMandiri.value);
    if (tm == 0) {
      var tot = parseFloat(frm.Tugas1.value) +
        parseFloat(frm.Tugas2.value) +
        parseFloat(frm.Tugas3.value) +
        parseFloat(frm.Tugas4.value) +
        parseFloat(frm.Tugas5.value) +
        parseFloat(frm.Presensi.value) +
        parseFloat(frm.UTS.value) +
        parseFloat(frm.UAS.value);
    }
    else {
      var tot = parseFloat(frm.TugasMandiri.value) +
        parseFloat(frm.Presensi.value) +
        parseFloat(frm.UTS.value) +
        parseFloat(frm.UAS.value);
    }
    frm.TOT.value = tot;
  }
  function CheckBobot(frm) {
    var tm = parseFloat(frm.TugasMandiri.value);
    if (tm == 0) {
      var tot = parseFloat(frm.Tugas1.value) +
        parseFloat(frm.Tugas2.value) +
        parseFloat(frm.Tugas3.value) +
        parseFloat(frm.Tugas4.value) +
        parseFloat(frm.Tugas5.value) +
        parseFloat(frm.Presensi.value) +
        parseFloat(frm.UTS.value) +
        parseFloat(frm.UAS.value);
    }
    else {
      var tot = parseFloat(frm.TugasMandiri.value) +
        parseFloat(frm.Presensi.value) +
        parseFloat(frm.UTS.value) +
        parseFloat(frm.UAS.value);
    }
    if (tot != 100) alert('Tidak dapat disimpan karena jumlah bobot tidak 100%');
    return tot == 100;
  }
  //-->
  </SCRIPT>
END;
}
function AmbilStatusKRS() {
  $arr = array();
  $s = "select * from statuskrs order by StatusKRSID";
  $r = _query($s);
  while ($w = _fetch_array($r)) 
    $arr[] = "$w[StatusKRSID]~$w[Nama]~$w[Ikut]~$w[Hitung]";
  return $arr;
}
function BuatOpsiStatusKRS($arr, $stt) {
  $opt = '';
  for ($i = 0; $i < sizeof($arr); $i++) {
    $str = explode('~', $arr[$i]);
    $sel = ($str[0] == $stt)? 'selected' : '';
    $opt .= "<option value='$str[0]' $sel>$str[1]</option> \n";
  }
  return $opt;
}
function mhsw($jdwl) {
  // Tombol cetak
  if ($jdwl['Final'] == 'Y') {
    $strHitung = "";
    $strExcel = "";
    $disable = 'readonly=true';
    $disable2 = 'disabled'; 
    $btnSubmit = '';
    $btnReset = '';
  }
  else {
    $strHitung = "<input type=button name='Hitung' value='Hitung Nilai' onClick=\"location='?mnux=dosen.nilai&slnt=dosen.nilai.sav&slntx=HitungNilai&jadwalid=$jdwl[JadwalID]'\">";
    $strExcel = "<input type=button name='Excel' value='Download Excel' onClick=\"location='dosen.nilai.excel.php?jdwlid=$jdwl[JadwalID]&tugasmandiri=$jdwl[TugasMandiri]'\">";
     $disable = ($jdwl['Penilaian'] == 'web') ? '' : "READONLY=TRUE";
    $disable2 = ($jdwl['Penilaian'] == 'web') ? '' : "READONLY=TRUE";
    $btnSubmit = "<input type=submit name='Simpan' value='Simpan Semua'>";
    $btnReset = "<input type=reset name='Reset' value='Reset Semua'>";
  }
  // Tampilkan pilihan
  echo "Pilihan:  
    <input type=button name='Cetak' value='Cetak UTS' onClick=\"location='dosen.nilai.cetak.php?jdwlid=$jdwl[JadwalID]&t=UTS'\">
    <input type=button name='Cetak' value='Cetak UAS' onClick=\"location='dosen.nilai.cetak.php?jdwlid=$jdwl[JadwalID]&t=UAS'\">
    <input type=button name='Cetak' value='Cetak Nilai Final' onClick=\"location='dosen.nilai.cetak.php?jdwlid=$jdwl[JadwalID]&t=FINAL'\">
    $strHitung
    $strExcel  
    <input type=button name='Rinci' value='Cetak Rincian' onClick=\"location='dosen.nilai.rinci.php?jdwlid=$jdwl[JadwalID]'\">";

  // tampilkan
  $nomer = 0; $t = 'class=ttl';
  $arrStatusKRS = AmbilStatusKRS();
  $_strTM = ($jdwl['TugasMandiri'] == 0)? '' : $jdwl['TugasMandiri'];
  $hdr = "<tr><th $t rowspan=2>#</th>
    <th $t rowspan=2>NPM</th>
    <th $t rowspan=2>Mahasiswa<br />$btnSubmit</th>
    <th $t colspan=5>Tugas Mandiri $_strTM%</th>
    <th $t rowspan=2 title='Presensi/Absensi'>Pres</th>
    <th $t rowspan=2 title='Ujian Tengah Semester'>UTS</th>
    <th $t rowspan=2 title='Ujian Akhir Semester'>UAS</th>
    <th $t rowspan=2 title='Responsi'>Resp</th>
    <th $t colspan=2>Nilai Akhir</th>
    </tr>
    <tr><th $t>1</th><th $t>2</th><th $t>3</th><th $t>4</th><th $t>5</th>
    <th $t>Nilai</th>
    <th $t>Grade</th></tr>";
  $hdrbbt = "<tr style='font-size: 0.8em'><th class=inp1>&nbsp;</th>
    <th class=inp1>&nbsp;</th>
    <th class=inp1>&nbsp;</th>
    <th class=inp1>$jdwl[Tugas1]%</th>
    <th class=inp1>$jdwl[Tugas2]%</th>
    <th class=inp1>$jdwl[Tugas3]%</th>
    <th class=inp1>$jdwl[Tugas4]%</th>
    <th class=inp1>$jdwl[Tugas5]%</th>
    <th class=inp1>$jdwl[Presensi]%</th>
    <th class=inp1>$jdwl[UTS]%</th>
    <th class=inp1>$jdwl[UAS]%</th>
    <th class=inp1>$jdwl[Responsi]%</th>
    <th class=inp1>&nbsp;</th>
    <th class=inp1>&nbsp;</th>
    </tr>";
  $s = "select k.*, m.Nama as NamaMhsw
    from krs k
      left outer join mhsw m on k.MhswID=m.MhswID
    where k.JadwalID='$jdwl[JadwalID]'
    order by k.MhswID";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=0>
    <form action='dosen.nilai.mhsw.php' target=_blank method=POST>
    <input type=hidden name='JadwalID' value='$jdwl[JadwalID]'>";
  echo $hdr . $hdrbbt;
  $jml = _num_rows($r);
  while ($w = _fetch_array($r)) {
    $nomer++;
    $_c = 'N';
    for ($i = 0; $i < sizeof($arrStatusKRS); $i++) {
      $strStatusKRS = explode('~', $arrStatusKRS[$i]);
      if ($strStatusKRS[0] == $w['StatusKRSID']) {
        $_c = $strStatusKRS[3];
      }
    }
    $c = ($_c == 'N')? "class=nac" : "class=ul";
    $krsid = $w['KRSID'];
    $StatusKRS = BuatOpsiStatusKRS($arrStatusKRS, $w['StatusKRSID']);
    $_t1 = $nomer;
    $_t2 = $nomer + $jml;
    $_t3 = $nomer + $jml *2;
    $_t4 = $nomer + $jml *3;
    $_t5 = $nomer + $jml *4;
    $_pr = $nomer + $jml *5;
    $_ut = $nomer + $jml *6;
    $_ua = $nomer + $jml *7;
    echo "<input type=hidden name='krsid[]' value='$krsid'>
      <tr>
      <td class=inp1>$nomer</td>
      <td $c>$w[MhswID]</td>
      <td $c>$w[NamaMhsw]</td>
      <td $c><input type=text name='Tugas1_$krsid' value='$w[Tugas1]' size=3 maxlength=3 tabindex=$_t1 $disable></td>
      <td $c><input type=text name='Tugas2_$krsid' value='$w[Tugas2]' size=3 maxlength=3 tabindex=$_t2 $disable></td>
      <td $c><input type=text name='Tugas3_$krsid' value='$w[Tugas3]' size=3 maxlength=3 tabindex=$_t3 $disable></td>
      <td $c><input type=text name='Tugas4_$krsid' value='$w[Tugas4]' size=3 maxlength=3 tabindex=$_t4 $disable></td>
      <td $c><input type=text name='Tugas5_$krsid' value='$w[Tugas5]' size=3 maxlength=3 tabindex=$_t5 $disable></td>
      <td $c><input type=text name='Presensi_$krsid' value='$w[Presensi]' size=3 maxlength=3 tabindex=$_pr $disable></td>
      <td $c><input type=text name='UTS_$krsid' value='$w[UTS]' size=3 maxlength=3 tabindex=$_ut  $disable></td>
      <td $c><input type=text name='UAS_$krsid' value='$w[UAS]' size=3 maxlength=3 tabindex=$_ua $disable></td>
      <td $c align=right>$w[Responsi]</td>
      <td $c align=right><b>$w[NilaiAkhir]</b></td>
      <td $c align=center><b>$w[GradeNilai]</b></td>
      <td $c align=center><select name='StatusKRSID_$krsid' $disable2>$StatusKRS</select></td>
      </tr>";
  }
  echo "<tr><td class=ul colspan=2></td>
    <td class=ul align=center>$btnSubmit</td></tr>
    </form></table></p>";
}
function finalisasi($jdwl) {
  if ($jdwl['Final'] == 'Y') {
    // Jika Admin
    $definal = ($_SESSION['_LevelID'] == 1)? 
      "<hr size=1 color=silver><input type=button name='Batal' value='Batalkan Finalisasi' onClick=\"location='?mnux=dosen.nilai&slnt=dosen.nilai.sav&slntx=DefinalisasiSav&jadwalid=$jdwl[JadwalID]'\"> 
	  <font color=red>*)</font> Jika Finalisasi dibatalkan, maka jika jadwal ini berstatus 'Gagal Penilaian Dosen', maka status 'Gagal' juga akan direset." : '';
	// Jika matakuliah gagal
	$gagal = ($jdwl['Gagal'] == 'Y')?
	  "<hr size=2 color=red />Matakuliah ini gagal penilaian dosen sehingga semua mahasiswa memperoleh nilai: <b>$jdwl[NilaiGagal]</b>.<br />
	  Catatan gagal:<br /><blockquote class=inp1>$jdwl[CatatanGagal]</blockquote>" : '';
    echo ErrorMsg("Nilai Sudah Final",
      "Nilai matakuliah ini sudah difinalisasi.
      $definal
	  $gagal");
  }
  else {
    // Tampilkan form untuk finalisasi
    $resp = ($jdwl['JenisJadwalID'] == 'R')? "<li>Mata kuliah ini adalah matakuliah <b>RESPONSI</b>. Nilai akhir akan ditransfer ke kelas matakuliahnya.</li>" : '';
    $psn = "
    <form action='?' name='finalisasi' method=POST>
    <input type=hidden name='mnux' value='dosen.nilai'>
    <input type=hidden name='jadwalid' value='$jdwl[JadwalID]'>
    <input type=hidden name='slnt' value='dosen.nilai.sav'>
    <input type=hidden name='slntx' value='FinalisasiSav'>
    Benar Anda akan memfinalisasi nilai matakuliah ini?<br />
    <ul>
      <li>Anda harus sudah mencetak nilai terlebih dahulu.</li>
      $resp
      <li>Setelah difinalisasi, bobot nilai dan nilai mahasiswa tidak dapat diubah lagi.</li>
    </ul>
    <hr size=1 color=silver />
    <input type=submit name='Finalisasi' value='Finalisasi Nilai'>
    </form>";
	
	// GAGALISASI
	if ($_SESSION['_LevelID'] == 1) {
	  $prd = TRIM($jdwl['ProdiID'], '.');
	  $prd = explode('.', $prd);
	  $prd = $prd[0];
	  $optnilai = GetOption2('nilai', "concat(Nama, ' (', Bobot, ')')", 'Bobot desc', '', "ProdiID='$prd' ", "NilaiID");
	  $psngagal = "<hr size=4 color=red />
	    <form action='?' name='gagalisasi' method=POST>
	    <input type=hidden name='mnux' value='dosen.nilai' />
	    <input type=hidden name='jadwalid' value='$jdwl[JadwalID]' />
	    <input type=hidden name='slnt' value='dosen.nilai.sav' />
	    <input type=hidden name='slntx' value='GagalisasiSav' />
	    Jika dosen tidak memberikan nilai sampai tenggat waktu, Anda dapat membuat matakuliah ini gagal.<br />
		Dengan demikian semua mahasiswa akan memperoleh nilai yang sama sesuai yang Anda tentukan. <br />
		Masukkan nilai untuk semua mahasiswa yang mengambil matakuliah ini:<br />
		<p><table class=box>
		<tr><td class=inp>Nilai semua mahasiswa: </td>
	  	<td class=ul><select name='NilaiGagal'>$optnilai</select> </td></tr>
		<tr><td class=inp>Catatan Gagal penilaian:</td>
	  	<td class=ul><textarea name='CatatanGagal' cols='30' rows='4'>$jdwl[CatatanGagal]</textarea></td></tr>
		</table></p>
		Setelah digagalkan, matakuliah langsung difinalisasi sehingga tidak dapat diubah lagi.
		<hr size=1 color=silver />
		<input type=submit name='Gagalisasi' value='Dosen Gagal Penilaian' />
		</form>";
	}
	else $psngagal = '';
    echo Konfirmasi("Konfirmasi Finalisasi", $psn . $psngagal);
  }
}
function TampilkanHeaderNilai($mnux='') {
  // ambil data prodi dari karyawan
  $prodiku = $_SESSION['_ProdiID'];
  $arrprodi = explode(',', $prodiku);
  $whr = array();
  foreach ($arrprodi as $val) {
    $whr[] = "INSTR(j.ProdiID, '.$val.')>0";
  }
  $_whr = " (". implode(' or ', $whr) . ") ";
  // query
  $s = "select j.JadwalID, j.MKKode, j.Nama, j.JenisJadwalID, j.NamaKelas, j.DosenID,
    concat(d.Nama, ', ', d.Gelar) as DSN
    from jadwal j
      left outer join dosen d on j.DosenID=d.Login
    where TahunID='$_SESSION[tahun]' 
    and $_whr 
    and j.JadwalSer = 0
    group by j.JadwalID, j.MKKode, j.JenisJadwalID, j.NamaKelas
    order by j.MKKode, j.NamaKelas, j.JenisJadwalID";
  $r = _query($s);
  // buat option
  $opt = "<option>--</option>";
  while ($w = _fetch_array($r)) {
    $sel = ($w['JadwalID'] == $_SESSION['JadwalID']) ? "selected" : "";
    $opt .= "<option value='$w[JadwalID]' $sel>$w[MKKode] $w[Nama] - $w[NamaKelas] ($w[JenisJadwalID]) - $w[DSN]</option>\r";
  }
  // Tampilkan opsi
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' methos=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <tr><td class=inp>Tahun :</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10> <input type=submit name='Tampilkan' value='Tampilkan'></td></tr>
  <tr><td class=inp>Jadwal :</td><td class=ul><select name='JadwalID' onChange='this.form.submit()'>$opt</select></td></tr>
  </form></table></p>";
}

// *** Parameters ***

$arrnilaipg = array('Bobot Nilai->bobot',
  'Nilai Mahasiswa->mhsw',
  'Finalisasi Nilai->finalisasi');
$dosen = GetSetVar('dosen');
$tahun = GetSetVar('tahun');

if (!empty($_REQUEST['Kirim'])) {
  $JadwalID = 0;
  $_SESSION['JadwalID'] = 0;
}
else {
  $JadwalID = GetSetVar('JadwalID');
}
// jika yg akses selain dosen
if ($_SESSION['_LevelID'] != 100) {
  if (!empty($JadwalID)) {
    $dosen = GetaField('jadwal', 'JadwalID', $JadwalID, 'DosenID');
    $_SESSION['dosen'] = $dosen;
  }
}

$mnux = 'dosen.nilai';
$pref = 'donil';
$token = GetSetVar($pref, 'bobot');

// *** Main ***
TampilkanJudul("Nilai Kuliah");
if ($_SESSION['_LevelID'] == 100) TampilkanHeaderDosenMKnyaSaja('dosen.nilai');
elseif (strpos(".1.2.40.41.", ".$_SESSION[_LevelID].") === false) 
  echo ErrorMsg("Anda Tidak Berhak", 
    "Anda tidak berhak mengakses modul penilaian ini."); 
else TampilkanHeaderNilai('dosen.nilai'); 

if (!empty($dosen) && ($JadwalID > 0)) {
  $jdwl = GetFields('jadwal', 'JadwalID', $JadwalID, '*');
  $assisten = GetaField('jadwaldosen', "JadwalID='$JadwalID' and DosenID", $dosen, 'JadwalDosenID');
  // Tampilkan
  //if ($dosen == $jdwl['DosenID'] || !empty($assisten)) {
    // Jika nilai sudah final, maka data sudah tidak dapat diubah
    if ($jdwl['Final'] == 'Y') {
      echo "<p><table class=box cellspacing=1 cellpadding=4 width=100%>
      <tr><td class=wrn>Nilai sudah final. Data sudah tidak dapat diubah lagi.</td></tr>
      </table></p>";
    }
    // Extract program
    $_prg = TRIM($jdwl['ProgramID'], '.');
    $_arrprg = explode('.', $_prg);
    $_prg = $_arrprg[0];
    // Extract prodi
    $_prd = TRIM($jdwl['ProdiID'], '.');
    $_arrprd = explode('.', $_prd);
    $_prd = $_arrprd[0];
    $_prd = GetaField('mk', "MKID", $jdwl['MKID'], 'ProdiID');
        $thn = GetFields('tahun', "TahunID='$tahun' and ProgramID = 'REGULER' and ProdiID", $_prd, '*');

    $Sekarang = date('Y-m-d');
    $Exp = $thn['TglNilai'] < $Sekarang;
    if ($Exp) echo ErrorMsg("Tanggal Pengisian Nilai Telah Lewat",
        "Tanggal batas pengisian nilai sudah terlampaui, yaitu tanggal: $thn[TglNilai].<br />
        Silakan hubungi bagian SIM untuk memasukkan nilai.");
    if (!Exp || $_SESSION['_LevelID']==1 || $_SESSION['_LevelID']==100 || $_SESSION['_LevelID']==41 || $_SESSION['_LevelID']==12 ) { 
      TampilkanSubMenu($mnux, $arrnilaipg, $pref, $token);
      if (!empty($token)) $token($jdwl);
    }
  //}
}
else echo ErrorMsg("Data Nilai Tidak Dapat Ditampilkan",
  "Data nilai matakuliah ini tidak dapat ditampilkan karena:
  <ol>
  <li>Matakuliah belum dipilih, atau</li>
  <li>Matakuliah belum diset Dosen/Penanggung jawabnya.</li>
  </ol>");
?>

<table style="border: activecaption">
    <td>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    </td>    
    <td>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    </td> 
</table>