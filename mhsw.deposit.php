<?php
// Author: Emanuel Setio Dewo
// 18 June 2006
// www.sisfokampus.net

include_once "klinik.lib.php";

// *** Functions ***
function TampilkanDepositMhsw() {
  $MhswID = $_SESSION['crval'];
  $_SESSION['MhswID'] = $MhswID;
  $mhsw = GetFields('mhsw', 'MhswID', $MhswID, '*');
  if (!empty($mhsw)) TampilkanDepositMhsw1($mhsw);
}
function TampilkanDepositMhsw1($mhsw) {
  TampilkanHeaderMhswKlinik($mhsw);
  echo "<p><a href='?mnux=mhsw.deposit&crval=$mhsw[MhswID]&gos=DepositAdd&md=1'>Tambahkan Deposit</a>
    </p>";
  // daftar Deposit
  $s = "select dep.*, date_format(dep.Tanggal, '%d-%m-%Y') as TGL
    from depositmhsw dep
    where dep.MhswID='$mhsw[MhswID]'
    order by dep.Tanggal";
  $r = _query($s); $n = 0;
  echo "<p><table class=box>
    <tr><th class=ttl>#</th>
    <th class=ttl>Tanggal</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Terpakai</th>
    <th class=ttl>Status</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['Tutup'] == 'Y')? "class=nac" : "class=ul";
    $jml = number_format($w['Jumlah']);
    $pki = number_format($w['Dipakai']);
    echo "<tr><td class=inp>$n</td>
    <td $c>$w[TGL]</td>
    <td $c align=right>$jml</td>
    <td $c align=right>$pki</td>
    <td class=ul align=center><img src='img/book$w[Tutup].gif'></td>
    </tr>"; 
  }
  echo "</table></p>";
}
function DepositAdd() {
  $md = $_REQUEST['md']+0;
  $crval = $_REQUEST['crval'];
  $NamaMhsw = GetaField('mhsw', 'MhswID', $crval, 'Nama');
  if ($md == 0) {
    $DepositMhswID = $_REQUEST['DMID']+0;
    $w = GetFields('depositmhsw', 'DepositMhswID', $DepositMhswID, '*');
    $jdl = "Edit Deposit Mhsw";
  }
  else {
    $w = array();
    $w['DepositMhswID'] = 0;
    $w['Tanggal'] = date('Y-m-d');
    $w['Jumlah'] = 0;
    $jdl = "Tambah Deposit";
  }
  echo "<p><table class=box>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='mhsw.deposit'>
  <input type=hidden name='gos' value='DepositSav'>
  <input type=hidden name='crval' value='$crval'>
  <input type=hidden name='DMID' value='$w[DepositMhswID]'>
  <input type=hidden name='md' value='$md'>
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Nama Mahasiswa</td>
    <td class=ul><b>$NamaMhsw</b></td></tr>
  <tr><td class=inp>Jumlah Deposit</td>
    <td class=ul><input type=text name='Jumlah' value='$w[Jumlah]'></td></tr>
  <tr><td class=inp>Catatan</td>
    <td class=ul><textarea name='Catatan' cols=30 rows=4>$w[Catatan]</textarea></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=mhsw.deposit'\">
    </td></tr>
  </form></table></p>";
}
function DepositSav() {
  $md = $_REQUEST['md']+0;
  $MhswID = $_REQUEST['crval'];
  $Jumlah = $_REQUEST['Jumlah']+0;
  $Catatan = sqling($_REQUEST['Catatan']);
  if ($md == 0) {
    $DMID = $_REQUEST['DMID'];
    $s = "update depositmhsw set Jumlah='$Jumlah', Catatan='$Catatan',
      LoginEdit='$_SESSION[_Login]', TglEdit=now()
      where DepositMhswID='$DMID' ";
    $r = _query($s);
  }
  else {
    $s = "insert into depositmhsw (MhswID, Tanggal, Jumlah, Catatan, TglBuat, LoginBuat)
      values ('$MhswID', now(), $Jumlah, '$Catatan', now(), '$_SESSION[_Login]')";
    $r = _query($s);
  }
  TampilkanDepositMhsw();
}

// *** Parameters ***
$crkey = GetSetVar('crkey');
$crval = GetSetVar('crval');
$MhswID = GetSetVar('MhswID', $crval);
$gos = (empty($_REQUEST['gos']))? "TampilkanDepositMhsw" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Deposit Mahasiswa");
TampilkanCariMhsw1('mhsw.deposit', 0);
$gos();
?>
