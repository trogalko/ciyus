<?php
// Author: Emanuel Setio Dewo
// 26 April 2006
// www.sisfokampus.net

function DftrJenisBeasiswa() {
  global $pref, $token;
  echo "<p><a href='?mnux=beasiswa&$pref=JenisBeasiswa&sub=BeaEdt&md=1'>Tambah Jenis Beasiswa</a></p>";
  $s = "select b.*, bn.Nama as BN
    from beasiswa b
      left outer join bipotnama bn on b.BIPOTNamaID=bn.BIPOTNamaID
    where b.KodeID='$_SESSION[KodeID]'
    order by b.BeasiswaID";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl colspan=2>Kode</th>
    <th class=ttl>Nama</th>
    <th class=ttl>IPS Min</th>
    <th class=ttl>IPK Min</th>
    <th class=ttl>Akun Potongan</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? "class=nac" : "class=ul";
    echo "<tr><td class=inp>$n</td>
    <td class=ul><a href='?mnux=beasiswa&$pref=$token&sub=BeaEdt&md=0&BeasiswaID=$w[BeasiswaID]'><img src='img/edit.png'></a></td>
    <td $c>$w[BeasiswaID]</td>
    <td $c>$w[Nama]</td>
    <td $c align=right>$w[IPSMin]</td>
    <td $c align=right>$w[IPKMin]</td>
    <td $c>$w[BN]</td>
    <td class=ul align=center><img src='img/book$w[NA].gif'></td>
    </tr>";
  }
  echo "</table></p>";
}
function BeaEdt() {
  global $pref, $token;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('beasiswa', "BeasiswaID", $_REQUEST['BeasiswaID'], '*');
    $BeasiswaID = "<input type=hidden name='BeasiswaID' value='$w[BeasiswaID]'><b>$w[BeasiswaID]</b>";
    $jdl = "Edit Jenis Beasiswa";
  }
  else {
    $w = array();
    $BeasiswaID = "<input type=text name='BeasiswaID' size=20 maxlength=30>";
    $w['BeasiswaID'] = '';
    $w['KodeID'] = $_SESSION['KodeID'];
    $w['BIPOTNamaID'] = 0;
    $w['Nama'] = '';
    $w['IPSMin'] = 0;
    $w['IPKMin'] = 0;
    $w['Prasyarat'] = '';
    $w['Keterangan'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Jenis Beasiswa";
  }
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  $optbpt = GetOption2('bipotnama', "Nama", 'Nama', $w['BIPOTNamaID'], 
    "KodeID='$w[KodeID]' and TrxID=-1 and BIPOTNamaID", "BIPOTNamaID");
  // Tampilan
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST name='jenbeasiswa'>
  <input type=hidden name='mnux' value='beasiswa'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='$pref' value='$token'>
  <input type=hidden name='sub' value='BeaSav'>
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Kode Beasiswa</td><td class=ul>$BeasiswaID</td></tr>
  <tr><td class=inp>Beasiswa</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=50 maxlength=50></td></tr>
  <tr><td class=inp>Akun Potongan</td><td class=ul><select name='BIPOTNamaID'>$optbpt</select></td></tr>
  <tr><td class=inp>IPS Minimal</td><td class=ul><input type=text name='IPSMin' value='$w[IPSMin]' size=5 maxlength=5></td></tr>
  <tr><td class=inp>IPK Minimal</td><td class=ul><input type=text name='IPKMin' value='$w[IPKMin]' size=5 maxlength=5></td></tr>
  <tr><td class=inp>Prasyarat</td><td class=ul><textarea name='Prasyarat' cols=30 rows=8>$w[Prasyarat]</textarea></td></tr>
  <tr><td class=inp>Keterangan</td><td class=ul><textarea name='Keterangan' cols=30 rows=5>$w[Keterangan]</textarea></td></tr>
  <tr><td class=inp>Tidak aktif (NA)?</td><td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=beasiswa'\"></td></tr>
  </form>
  </table></p>";
}
function BeaSav() {
  $md = $_REQUEST['md'];
  $BeasiswaID = $_REQUEST['BeasiswaID'];
  $Nama = sqling($_REQUEST['Nama']);
  $BIPOTNamaID = $_REQUEST['BIPOTNamaID'];
  $IPSMin = $_REQUEST['IPSMin']+0;
  $IPKMin = $_REQUEST['IPKMin']+0;
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $Prasyarat = sqling($_REQUEST['Prasyarat']);
  $NA = (empty($_REQUEST['NA']))? "N" : $_REQUEST['NA'];
  // Simpan
  if ($md == 0) {
    $s = "update beasiswa set Nama='$Nama', IPSMin='$IPSMin', IPKMin='$IPKMin', Keterangan='$Keterangan', NA='$NA',
      BIPOTNamaID='$BIPOTNamaID',
      Prasyarat='$Prasyarat', LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where BeasiswaID='$BeasiswaID' ";
    $r = _query($s);
  }
  else {
    $ada = GetFields('beasiswa', "BeasiswaID", $BeasiswaID, "*");
    if (empty($ada)) {
      $s = "insert into beasiswa (BeasiswaID, KodeID, BIPOTNamaID, Nama, IPSMin, IPKMin, Keterangan, NA,
        Prasyarat, LoginBuat, TanggalBuat)
        values ('$BeasiswaID', '$_SESSION[KodeID]', '$BIPOTNamaID', '$Nama', '$IPSMin', '$IPKMin', '$Keterangan', '$NA',
        '$Prasyarat', '$_SESSION[_Login]', now())";
      $r = _query($s);
    }
    else echo ErrorMsg("Tidak Dapat Disimpan",
      "Beasiswa dengan kode: <b>$ada[BeasiswaID]</b> telah ada.");
  }
  DftrJenisBeasiswa();
}
?>
