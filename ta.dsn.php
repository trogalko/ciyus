<?php
// Author: Emanuel Setio Dewo
// 23 April 2006

// *** Functions ***
function TADSN($mhsw, $ta) {
  $TM = FormatTanggal($ta['TglMulai']);
  $TS = FormatTanggal($ta['TglSelesai']);
  $TU = FormatTanggal($ta['TglUjian']);
  $_TU = ($TU == '00/00/0000')? "&nbsp;" : $TU;
  $pemb = GetaField('dosen', "Login", $ta['Pembimbing'], "concat(Nama, ', ', Gelar)");
  $peng = GetaField('dosen', "Login", $ta['Penguji'], "concat(Nama, ', ', Gelar)");
  //echo "<p><a href='?mnux=ta'>Kembali ke Data Tugas Akhir</a></p>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp>Batas Tanggal</td><td class=ul>$TM ~ $TS</td>
    <td class=inp>Tgl Ujian</td><td class=ul>$_TU</td></tr>
  <tr><td class=inp>Pembimbing Utama <a href='?mnux=ta&gos=TAEdt&taid=$ta[TAID]&md=0'><img src='img/edit.png'></a></td>
    <td class=ul>$pemb &nbsp;</td>
    <td class=inp>Penguji Utama <img src='img/edit.png'></td>
    <td class=ul>$peng &nbsp;</td></tr>
  <tr><td class=inp>Judul</td>
    <td class=ul colspan=3>$ta[Judul]</td></tr>
  <tr><td class=inp>Opsi</td>
    <td class=ul colspan=3><input type=button name='Kembali' value='Kembali ke Data Mhsw' onClick=\"location='?mnux=ta'\"></td></tr>
  </table></p>";
  DaftarPendamping($mhsw, $ta);
}
function DaftarPendamping($mhsw, $ta) {
  $s = "select td.*, concat(dsn.Nama, ', ', dsn.Gelar) as DSN
    from tadosen td
      left outer join dosen dsn on td.DosenID=dsn.Login
    where td.TAID='$ta[TAID]' and td.Tipe='$_SESSION[tipta]'
    order by dsn.Nama";
  $r = _query($s); $n = 0;
  $str = ($_SESSION['tipta'] == 0)? "Pembimbing" : "Penguji";
  $optdsn2 = GetOption2("dosen", "concat(Nama, ', ', Gelar)", "Nama", '', 
    "INSTR(ProdiID, '.$mhsw[ProdiID].')>0", 'Login');
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  echo "<form action='?' name='tmbhdsn2' method=POST>
    <input type=hidden name='mnux' value='ta.dsn'>
    <input type=hidden name='taid' value='$ta[TAID]'>
    <input type=hidden name='gos' value='TambahDSN'>
    <tr><td colspan=3 class=inp>Tambah $str</td>
    <td class=ul><select name='dsn2'>$optdsn2</select> <input type=submit name='Tambah' value='Tambahkan'></td></tr>
    </form>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr><td class=inp>$n</td>
      <td class=inp1>$w[DosenID]</td>
      <td class=ul align=center><a href='?mnux=ta.dsn&gos=DSNDel&tadid=$w[TADosenID]'><img src='img/del.gif'></a></td>
      <td class=ul>$w[DSN]</td>
      <tr>";
  }
  echo "</table></p>";
}
function TambahDSN($mhsw, $ta) {
  $dsn2 = $_REQUEST['dsn2'];
  $sdh = GetaField("tadosen", "TAID='$ta[TAID]' and Tipe='$_SESSION[tipta]' and DosenID", $dsn2, "TADosenID")+0;
  if ($sdh > 0) {
    echo ErrorMsg("Gagal Simpan",
      "Dosen <b>$dsn2</b> telah menjadi pembimbing/penguji mahasiswa ini. <hr size=1 color=silver>
      Pilihan: <a href='?mnux=ta.dsn'>Refresh Tampilan</a>");
  }
  else {
    $s = "insert into tadosen (TAID, MhswID, DosenID, Tipe,
      LoginBuat, TanggalBuat)
      values ('$ta[TAID]', '$mhsw[MhswID]', '$dsn2', '$_SESSION[tipta]',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
  }
  TADSN($mhsw, $ta);
}
function DSNDel($mhsw, $ta) {
  $tadid = $_REQUEST['tadid'];
  $s = "delete from tadosen where TADosenID='$tadid' ";
  $r = _query($s);
  TADSN($mhsw, $ta);
}

// *** Parameters ***
$tipta = GetSetVar('tipta', 0);
$taid = GetSetVar('taid');
$gos = (empty($_REQUEST['gos']))? "TADSN" : $_REQUEST['gos'];

// *** Main ***
$arrTip = array(0=>"Pembimbing Tugas Akhir", 1=>"Penguji Tugas Akhir");
TampilkanJudul($arrTip[$tipta]);
if (!empty($taid)) {
  $ta = GetFields('ta', 'TAID', $taid, '*');
  $mhsw = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join bipot bpt on m.BIPOTID=bpt.BIPOTID",
    "MhswID", $ta['MhswID'],
    "m.*, prg.Nama as PRG, prd.Nama as PRD, bpt.Nama as BPT");
  if (!empty($mhsw)) {
    include_once "mhsw.hdr.php";
    TampilkanHeaderBesar($mhsw, 'ta.dsn', '', 0);
    $gos($mhsw, $ta);
  }
}
?>
