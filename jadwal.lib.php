<?php
// Author: Emanuel Setio Dewo
// 25 May 2006
// Kenaikan Yesus Kristus
// http://www.sisfokampus.net

function TampilkanHeaderDosenMKnyaSaja($mnux='') {
  $dsn = $_SESSION['_Login'];
  $_SESSION['dosen'] = $dsn;
  $NamaDosen = GetFields("dosen", "Login", $dsn, "Nama, Gelar");
  $optjdwl = GetOptJdwlDosenMK();
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=GET>
  <input type=hidden name='mnux' value='$mnux'>
  <tr><td class=wrn rowspan=2>$_SESSION[KodeID]</td>
    <td class=inp1>Tahun Akd.</td>
    <td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10></td>
    <td class=inp1>Dosen</td><td class=ul><b>$NamaDosen[Nama], $NamaDosen[Gelar]</b></td>
    <td class=ul><input type=submit name='Kirim' value='Kirim'></td></tr>
  <tr><td class=inp1>Matakuliah</td><td class=ul colspan=4><select name='JadwalID' onChange='this.form.submit()'>$optjdwl</select></td></tr>
  </form></table></p>";
}
function TampilkanHeaderDosenMK($mnux='') {
  //$optdsn = GetOption2('dosen', "concat(Nama, ', ', Gelar, ' (', Login, ')')",
  //  "Nama", $_SESSION['dosen'], '', 'Login');
  $optdsn = GetOptDosen();
  $optjdwl = GetOptJdwlDosenMK();
  /*$optjdwl = GetOption2("jadwal j
    left outer join hari h on j.HariID=h.HariID",
    "concat(h.Nama, ' - ', j.MKKode, ' - ', j.Nama, ' ', j.NamaKelas, ' (', j.JenisJadwalID, ')')", 
    "j.HariID, j.Nama", $_SESSION['jadwalid'],
    "j.KodeID='$_SESSION[KodeID]' and j.TahunID='$_SESSION[tahun]' and INSTR(j.DosenID, '.$_SESSION[dosen].')>0", 'JadwalID', 1);
  */
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <tr><td class=wrn rowspan=2>$_SESSION[KodeID]</td>
    <td class=inp1>Tahun Akd.</td>
    <td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10></td>
    <td class=inp1>Dosen</td><td class=ul><select name='dosen'>$optdsn</select></td>
    <td class=ul><input type=submit name='Kirim' value='Kirim'></td></tr>
  <tr><td class=inp1>Matakuliah</td><td class=ul colspan=4><select name='JadwalID' onChange='this.form.submit()'>$optjdwl</select></td></tr>
  </form></table></p>";
}
function GetOptDosen() {
  $tahun = $_SESSION['tahun'];
  $dosen = $_SESSION['dosen'];
  // Ambil dosen dari jadwal
  $s = "select d.Login as DosenID, d.Nama, concat(d.Nama, ', ', d.Gelar) as DSN
    from jadwal j
      left outer join dosen d on j.DosenID=d.Login
    where j.TahunID='$tahun'
    order by d.Nama";
  $r = _query($s);
  $arr = array();
  $arr[] = "--- Pilih Dosen ---~-";
  while ($w = _fetch_array($r)) {
    $arr[] = "$w[DSN]~$w[DosenID]";
  }
  // Ambil dosen dari assisten
  $s = "select jd.DosenID, d.Nama, concat(d.Nama, ', ', d.Gelar) as DSN
    from jadwaldosen jd
      left outer join jadwal j on jd.JadwalID=j.JadwalID
      left outer join dosen d on jd.DosenID=d.Login
    where j.TahunID='$tahun'
    order by d.Nama";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $arr[] = "$w[DSN]~$w[DosenID]";
  }
  
  sort($arr);
  //$arr = array_unique($arr);
  $a = ''; $sdh = '';
  for ($i=0; $i < sizeof($arr); $i++) {
    $_a = explode('~', $arr[$i]);
    if ($sdh != $arr[$i]) 
    {
      $sdh = $arr[$i];
      $sel = ($_a[1] == $dosen)? 'selected' : '';
      $a .= "<option value='$_a[1]' $sel>$_a[0] ($_a[1])</option>";
    }
  }
  return $a;
}
function GetOptJdwlDosenMK() {
  $tahun = $_SESSION['tahun'];
  $dosen = $_SESSION['dosen'];
  $jdwlid = $_SESSION['JadwalID'];
  //$jdwlid = 1;
  $arr = array();
  $arr[] = "<option value='0'>--- Pilih ---</option>";
  // Pilihan 1
  $s0 = "select j.JadwalID, j.MKKode, j.Nama, j.NamaKelas, j.JenisJadwalID, j.DosenID, j.Penilaian, j.JadwalSer 
    from jadwal j
      left outer join hari h on j.HariID=h.HariID
    where j.DosenID='$dosen'
		and j.TahunID='$tahun' 
		and (j.JadwalSer = 0 or jadwalSer is null)
    group by j.MKKode, j.JenisJadwalID, j.NamaKelas
    order by j.MKKode, j.NamaKelas, j.JenisJadwalID";
  $r0 = _query($s0);
  //echo "<pre>$s0</pre>";
  while ($w0 = _fetch_array($r0)) {
	$hitmhsw = GetaField('krs', "TahunID = $tahun and JadwalID", $w0['JadwalID'], "count(MhswID)");
    $penilaian = ($w0['Penilaian'] == 'web') ? '' : "     -> Dengan Disket";
    if (!empty($w0['NamaKelas'])) {
      if ($hitmhsw > 0) {
        $isi = "$w0[MKKode] - $w0[Nama] $w0[NamaKelas] ($w0[JenisJadwalID])";
        $sel = ($w0['JadwalID'] == $jdwlid) ? 'selected' : '';
        $arr[] = "<option value='$w0[JadwalID]' $sel>$isi$penilaian</option>";
      }
    }
  }
  // Bila sebagai asisten
  $s1 = "select j.JadwalID, j.MKKode, j.Nama, j.NamaKelas, j.JenisJadwalID, j.DosenID
    from jadwaldosen jd
      left outer join jadwal j on jd.JadwalID=j.JadwalID
      left outer join hari h on j.HariID=h.HariID
    where jd.DosenID='$dosen' ";
  $r1 = _query($s1);
  while ($w1 = _fetch_array($r1)) {
    $isi = "$w1[MKKode] - $w1[Nama] $w1[NamaKelas] ($w1[JenisJadwalID])";
    $sel = ($w1['JadwalID'] == $jdwlid) ? 'selected' : ''; 
    $arr1[] = "<option value='$w1[JadwalID]' $sel>$isi</option>";
  }
  // Kembalikan
  return implode("\n", $arr);
}
function DelAssDsn() {
  $JadwalDosenID = $_REQUEST['JDID'];
  $s = "delete from jadwaldosen where JadwalDosenID='$JadwalDosenID'";
  $r = _query($s);
}
function JdwlUrutMK() {
  include_once "sisfokampus.php";
  HeaderSisfoKampus('Jadwal Urut per Matakuliah');
  $tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid = $_REQUEST['prid'];
  if (empty($tahun) && empty($prodi) && empty($prid)) 
    die (ErrorMsg("Data Tidak Lengkap",
      "Isikan Tahun, Program, dan Program Studi sebelum mencetak"));
  $_prodi = GetaField('prodi', "ProdiID", $prodi, 'Nama');
  $_prid = GetaField('program', 'ProgramID', $prid, 'Nama');
  echo "<p><center><font size=+2>Jadwal Matakuliah $tahun</font><br />
  <font size=+1>Urut per Matakuliah</font><br />
  <b>Program:</b> $_prid, <b>Program Studi:</b> $_prodi</center></p>";
  // tampilkan header
  echo "<p><table class=box cellspacing=1>
  <tr><th class=ttl>#</th>
  <th class=ttl>Kode</th>
  <th class=ttl>Matakuliah</th>
  <th class=ttl>Kelas</th>
  <th class=ttl>Kuliah</th>
  <th class=ttl>SKS</th>
  <th class=ttl>Sesi</th>
  <th class=ttl>Hari</th>
  <th class=ttl>Jam</th>
  <th class=ttl>Dosen</th>
  </tr>";
  $s = "select j.MKKode, j.Nama, j.NamaKelas, j.SKS, j.SKSAsli, mk.Sesi, j.JenisJadwalID,
    jj.Nama as JenisJadwal, concat(d.Nama, ', ', d.Gelar) as DSN,
    h.Nama as HR, time_format(j.JamMulai, '%H:%i') as JM, time_format(j.JamSelesai, '%H:%i') as JS
    from jadwal j
      left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
      left outer join dosen d on j.DosenID=d.Login
      left outer join hari h on j.HariID=h.HariID
      left outer join mk mk on j.MKID=mk.MKID
    where j.TahunID='$tahun'
      and INSTR(j.ProgramID, '.$prid.') > 0
      and INSTR(j.ProdiID, '.$prodi.') > 0
    order by j.MKKode, j.NamaKelas, j.JenisJadwalID";
  $r = _query($s); $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr>
    <td class=inp>$n</td>
    <td class=ul>$w[MKKode]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[NamaKelas]</td>
    <td class=ul>$w[JenisJadwal]</td>
    <td class=ul>$w[SKS]</td>
    <td class=ul align=right>$w[Sesi]</td>
    <td class=ul>$w[HR]</td>
    <td class=ul>$w[JM]~$w[JS]</td>
    <td class=ul>$w[DSN]</td>
    </tr>";
  }
  echo "</table></p>";
  //echo "<script>window.print()</script>";
}
function JdwlperDosen() {
  include_once "sisfokampus.php";
  HeaderSisfoKampus('Jadwal Kuliah per Dosen');
  $tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid = $_REQUEST['prid'];
  if (empty($tahun) && empty($prodi) && empty($prid)) 
    die (ErrorMsg("Data Tidak Lengkap",
      "Isikan Tahun, Program, dan Program Studi sebelum mencetak"));
  $_prodi = GetaField('prodi', "ProdiID", $prodi, 'Nama');
  $_prid = GetaField('program', 'ProgramID', $prid, 'Nama');
  echo "<p><center><font size=+2>Jadwal Matakuliah $tahun</font><br />
  <font size=+1>Urut per Dosen</font><br />
  <b>Program:</b> $_prid, <b>Program Studi:</b> $_prodi</center></p>";
  // data
  $s = "select j.MKKode, j.Nama, j.NamaKelas, j.SKS, j.SKSAsli, mk.Sesi,
    jj.Nama as JenisJadwal, concat(d.Nama, ', ', d.Gelar) as DSN,
    h.Nama as HR, time_format(j.JamMulai, '%H:%i') as JM, time_format(j.JamSelesai, '%H:%i') as JS
    from jadwal j
      left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
      left outer join dosen d on j.DosenID=d.Login
      left outer join hari h on j.HariID=h.HariID
      left outer join mk mk on j.MKID=mk.MKID
    where j.TahunID='$tahun'
      and INSTR(j.ProgramID, '.$prid.') > 0
      and INSTR(j.ProdiID, '.$prodi.') > 0
    order by d.Nama, j.MKKode, j.NamaKelas, j.JenisJadwalID";
  $r = _query($s);
  $n = 0;
  $dsn = '';
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>#</th>
    <th class=ttl>Dosen</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>Kuliah</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Sesi</th>
    <th class=ttl>Hari</th>
    <th class=ttl>Jam</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    if ($dsn != $w['DSN']) {
      $dsn = $w['DSN'];
      $_dsn = $dsn;
    } else {
      $_dsn = "<img src='img/brch.gif' align=right>";
    }
    echo "<tr><td class=inp>$n</td>
    <td class=ul>$_dsn</td>
    <td class=ul>$w[MKKode]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[NamaKelas]</td>
    <td class=ul>$w[JenisJadwal]</td>
    <td class=ul>$w[SKS]</td>
    <td class=ul align=right>$w[Sesi]</td>
    <td class=ul>$w[HR]</td>
    <td class=ul>$w[JM]~$w[JS]</td>
    </tr>";
  }
  echo "</table>";
}
function JdwlUrutHari() {
  include "db.mysql.php";
  include "connectdb.php";
  include "dwo.lib.php";
  $tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid = $_REQUEST['prid'];
  // Header
  if (empty($tahun) && empty($prodi) && empty($prid)) 
    die (ErrorMsg("Data Tidak Lengkap",
      "Isikan Tahun, Program, dan Program Studi sebelum mencetak"));
  $_prodi = GetaField('prodi', "ProdiID", $prodi, 'Nama');
  $_prid = GetaField('program', 'ProgramID', $prid, 'Nama');
  // Ambil data
  $s = "select j.JadwalID, j.HariID, j.JamMulai, j.JamSelesai, j.JenisJadwalID,
    j.RuangID, j.MKKode, j.Nama, j.NamaKelas, j.SKS, j.SKSAsli
    from jadwal j
    where INSTR(j.ProgramID, '.$prid.')>0
      and INSTR(j.ProdiID, '.$prodi.')>0
    order by j.JenisJadwalID, j.HariID, j.JamMulai";
  $r = _query($s); $hr = -1; $n = 0;
  // parameter
  $lf = chr(13).chr(10);
  $mxc = 114;
  $mxb = 55;
  $grs = str_pad('-', $mxc, '-').$lf;
  $hdr = str_pad("Jadwal Kuliah - $tahun", $mxc, ' ', STR_PAD_BOTH).$lf.
    str_pad("Program: $_prid, Program Studi: $_prodi", $mxc, ' ', STR_PAD_BOTH).$lf;
  // file
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  $n = 0; $b = 0; $hr = -100;
  $jen = 'abcdefghijklmnopqrstuvwxyz';
  $hdr1 = str_pad('No.', 4). str_pad('Hari', 10). str_pad('Jam Kuliah', 13).
    str_pad('Ruang', 10). str_pad('Kode', 10). str_pad('Matakuliah', 50).
    str_pad('Kelas', 5). str_pad('SKS', 4, ' ', STR_PAD_LEFT). $lf.$grs;
  while ($w = _fetch_array($r)) {
    if ($jen != $w['JenisJadwalID']) {
      $jen = $w['JenisJadwalID'];
      if ($n != 0) fwrite($f, $grs);
      fwrite($f, chr(12));
      fwrite($f, $hdr);
      $_jen = GetaField('jenisjadwal', 'JenisJadwalID', $jen, 'Nama');
      fwrite($f, str_pad("Jenis Kuliah: $_jen", $mxc, ' ', STR_PAD_BOTH).$lf.$grs);
      fwrite($f, $hdr1);
      $n = 0;
      $b = 0;
      $hr = -100;
    }
    if ($hr != $w['HariID']) {
      $hr = $w['HariID'];
      $_hr = GetaField('hari', 'HariID', $hr, 'Nama');
    } 
    else $_hr = '';
    $n++;
    $b++;
    $JM = substr($w['JamMulai'], 0, 5);
    $JS = substr($w['JamSelesai'], 0, 5);
    fwrite($f, str_pad($n, 4).
      str_pad($_hr, 10).
      str_pad("$JM-$JS", 13).
      str_pad($w['RuangID'], 10).
      str_pad($w['MKKode'], 10).
      str_pad($w['Nama'], 50).
      str_pad($w['NamaKelas'], 5).
      str_pad($w['SKS'], 4, ' ', STR_PAD_LEFT).
      $lf);
  }
  fwrite($f, $grs);
  fclose($f);
  TampilkanFileDWOPRN($nmf);
}
function JdwlUrutHari_x() {
  include_once "sisfokampus.php";
  HeaderSisfoKampus('Jadwal Kuliah per Dosen');
  $tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid = $_REQUEST['prid'];
  // Header
  if (empty($tahun) && empty($prodi) && empty($prid)) 
    die (ErrorMsg("Data Tidak Lengkap",
      "Isikan Tahun, Program, dan Program Studi sebelum mencetak"));
  $_prodi = GetaField('prodi', "ProdiID", $prodi, 'Nama');
  $_prid = GetaField('program', 'ProgramID', $prid, 'Nama');
  echo "<p><center><font size=+2>Jadwal Matakuliah $tahun</font><br />
  <font size=+1>Urut Berdasarkan Hari</font><br />
  <b>Program:</b> $_prid, <b>Program Studi:</b> $_prodi</center></p>";
  // Ambil data
  $s = "select j.JadwalID, j.HariID, j.JamMulai, j.JamSelesai,
    j.RuangID, j.MKKode, j.Nama, j.NamaKelas, j.SKS, j.SKSAsli
    from jadwal j
    where INSTR(j.ProgramID, '.$prid.')>0
      and INSTR(j.ProdiID, '.$prodi.')>0
    order by j.HariID, j.JamMulai";
  $r = _query($s); $hr = -1; $n = 0;
  $hdr = "<tr><th class=ttl>#</th>
    <th class=ttl>Jam Kuliah</th>
    <th class=ttl>Ruang</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>SKS</th>
    </tr>";
  echo "<p><table class=box>";
  while ($w = _fetch_array($r)) {
    if ($hr != $w['HariID']) {
      $hr = $w['HariID'];
      $_hr = GetaField('hari', 'HariID', $hr, 'Nama');
      echo "<tr><td class=ul colspan=10><font size=+1><b>$_hr</b></font></td></tr>";
      echo $hdr;
    }
    $n++;
    $JM = substr($w['JamMulai'], 0, 5);
    $JS = substr($w['JamSelesai'], 0, 5);
    echo "<tr><td class=inp>$n</td>
    <td class=ul>$JM - $JS</td>
    <td class=ul>$w[RuangID]</td>
    <td class=ul>$w[MKKode]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[NamaKelas]</td>
    <td class=ul>$w[SKS] ($w[SKSAsli])</td>
    </tr>";
  }
  echo "</table></p>";
}
function SemuaJadwal() {
  include "db.mysql.php";
  include "connectdb.php";
  include "dwo.lib.php";
  $tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid = $_REQUEST['prid'];
  $thn = GetaField('tahun', "TahunID='$tahun' and ProgramID='$prid' and ProdiID", $prodi, "Nama");
  $prd = GetFields('prodi', 'ProdiID', $prodi, "FakultasID, Nama");
  $fak = GetaField('fakultas', 'FakultasID', $prd['FakultasID'], 'Nama');

  $lf = chr(13).chr(10);
  $mxc = 150;
  $mxb = 52;
  $grs = str_pad('-', $mxc, '-').$lf;
  $TGL = date('d-m-Y');
  
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(77).chr(27).chr(15));
  
  // query
  $s = "select j.*, LEFT(j.Nama, 35) as MK
    from jadwal j
    where j.TahunID='$tahun'
      and INSTR(j.ProdiID, '$prodi')>0
    order by j.HariID, j.JamMulai, j.MKKode";
  $r = _query($s);
  $n = 0;
  $hal = 1;
  $kol = 0;
  $isi = array();
  ResetArrayIsi($isi, $mxb);
  $hr = 'abcdefghijklmnopqrstuvwxyz';
  while ($w = _fetch_array($r)) {
    if ($n == 0 && $kol == 0) {
      $hdr = str_pad("*** DAFTAR JADWAL KULIAH/RESPONSI ***", $mxc, ' ', STR_PAD_BOTH). $lf.
        str_pad("Semester   : $thn ", 126) . 
        "Tanggal    : $TGL" .$lf.
        str_pad("Fak/Jur    : $fak/$prd[Nama]", 126) . 
        "Halaman    : $hal" .$lf .
        $grs .
        "Hari    Jam          Kode    Matakuliah                         Kls JEN RE ".
        "Hari    Jam          Kode    Matakuliah                         Kls JEN RE ". 
        $lf.$grs;
      fwrite($f, $hdr);
    }
    if ($hr != $w['HariID'] || ($n == 0)) {
      $hr = $w['HariID'];
      $_hr = GetaField('hari', 'HariID', $w['HariID'], 'Nama');
    }
    else {
      $_hr = str_pad(' ', 7);
    }
    $jm = substr($w['JamMulai'], 0, 5);
    $js = substr($w['JamSelesai'], 0, 5);
    $jam = "$jm-$js";
    $p = TRIM($w['ProgramID'], '.');
    $p = explode('.', $p);
    $p = substr($p[0], 0, 1);
    $isi[$n] .=
      str_pad($_hr, 8).
      str_pad($jam, 13).
      str_pad($w['MKKode'], 8).
      str_pad($w['MK'], 37).
      str_pad($w['NamaKelas'], 3).
      str_pad($w['JenisJadwalID'], 3).
      str_pad($p, 3);
    $n++;
    if ($n >= $mxb && $kol == 0) {
      $n = 0;
      $kol = 1;
    }
    if ($n >= $mxb && $kol ==1) {
      $n = 0;
      $kol = 0;
      $hal++;
      for ($i = 0; $i <= $mxb; $i++) fwrite($f, $isi[$i].$lf);
      fwrite($f, chr(12));
      ResetArrayIsi($isi, $mxb);
    }
  }
  for ($i = 0; $i <= $mxb; $i++) fwrite($f, $isi[$i].$lf);
  fclose($f);
  TampilkanFileDWOPRN($nmf);
}
function ResetArrayIsi(&$isi, $mxb) {
  for ($i=0; $i < $mxb; $i++) {
    $isi[$i] = '';
  }
}

// *** Main ***
//$gos = $_REQUEST['gos'];
//if (!empty($gos)) $gos();
//TampilkanHeaderDosenMK('');
?>
