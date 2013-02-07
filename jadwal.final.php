<?php
// Author: Emanuel Setio Dewo
// 03 Agustus 2006
// www.sisfokampus.net

// *** Functions ***
$arrUrutkanJadwal = array("Kode~j.MKKode", "Kelas~j.NamaKelas", "Jenis~j.JenisJadwalID", "Dosen~d.Nama");
function TampilkanUrutkanJadwal($mnux='', $gos='') {
  global $arrUrutkanJadwal;
  $str = "";
  for ($i = 0; $i < sizeof($arrUrutkanJadwal); $i++) {
    $k = explode('~', $arrUrutkanJadwal[$i]);
    $sel = ($k[0] == $_SESSION['UrutkanJadwal'])? 'selected' : '';
    $str .= "<option value='$k[0]' $sel>$k[0]</option> \n";
  }
  echo "<p><table class=box cellspacing=1 cellpadding=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <tr><td class=ul>Urutkan :</td>
    <td class=ul><select name='UrutkanJadwal' onChange='this.form.submit()'>$str</select></td>
  </form></table>";
}
function TampilkanFinal() {
  global $arrUrutkanJadwal;
  TampilkanUrutkanJadwal('jadwal.final', 'TampilkanFinal');
  $urut = '';
  for ($i = 0; $i < sizeof($arrUrutkanJadwal); $i++) {
    $k = explode('~', $arrUrutkanJadwal[$i]);
    if ($k[0] == $_SESSION['UrutkanJadwal']) {
      $urut = $k[1];
    }
  }
  $s = "select j.*, 
    concat(d.Nama, ', ', d.Gelar) as DSN, concat(d.Telephone, ', ', d.Handphone) as TELP
    from jadwal j
      left outer join dosen d on j.DosenID=d.Login
    where j.TahunID='$_SESSION[tahun]'
      and INSTR(j.ProdiID, '.$_SESSION[prodi].') > 0
    order by $urut";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=1>
    <tr><th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>Jenis</th>
    <th class=ttl>Program</th>
    <th class=ttl>Dosen</th>
    <th class=ttl>Final</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['Final'] == 'Y')? "class=inp1" : "class=ul";
    echo "<tr><td class=inp>$n</td>
    <td $c>$w[MKKode]</td>
    <td $c>$w[Nama]</td>
    <td $c>$w[NamaKelas]</td>
    <td $c>$w[JenisJadwalID]</td>
    <td $c>$w[ProgramID]</td>
    <td $c><abbr title='Telp: $w[TELP]'>$w[DSN]</abbr></td>
    <td $c align=center><img src='img/book$w[Final].gif'></td>
    </tr>";
  }
  echo "</table></p>";
}

// *** Parameters ***
$UrutkanJadwal = GetSetVar('UrutkanJadwal', 'Kode');
$tahun = GetSetVar('tahun');
$prid = GetSetVar('prid');
$prodi = GetSetVar('prodi');
$gos = (empty($_REQUEST['gos']))? "TampilkanFinal" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Inquiry Nilai yg Telah Difinalisasi");
TampilkanTahunProdiProgram("jadwal.final", "TampilkanFinal");
if (!empty($_REQUEST['gos']) && !empty($tahun) && !empty($prodi)) 
  $_REQUEST['gos']();
?>
