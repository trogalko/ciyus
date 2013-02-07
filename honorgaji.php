<?php
// Author: Emanuel Setio Dewo
// 03 April 2006

// *** Functions ***
function DftrGolongan($prodi) {
  echo "<p><a href='?mnux=honorgaji&gos=HonjiEdt&md=1&prodi=$prodi'>Tambah Golongan</a> |
    <a href='?mnux=honorgaji&gos=HonjiCpy&prodi=$prodi'>Copy dari Prodi Lain</a></p>";
  $s = "select *, format(TunjanganFungsional, 0) as TFung,
    format(TunjanganSKS, 0) as TSKS,
    format(TunjanganTransport, 0) as TTrans,
    format(TunjanganTetap, 0) as TTtp
    from golongan
    where ProdiID='$prodi' and KodeID='$_SESSION[KodeID]'
    order by GolonganID, KategoriID";
  $r = _query($s);
  echo "<div class=sebelah_kiri>
    <p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>Gol</th>
    <th class=ttl>Kat</th>
    <th class=ttl>Pangkat</th>
    <th class=ttl title='Gaji Pokok'>T. Fung</th>
    <th class=ttl title='Tunjangan SKS'>T. SKS</th>
    <th class=ttl title='Transport'>T. Trans</th>
    <th class=ttl title='Per Pertemuan'>T. Tetap</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr>
      <td class=inp1 nowrap><a href='?mnux=honorgaji&gos=HonjiEdt&md=0&prodi=$w[ProdiID]&golid=$w[GolonganID]&katid=$w[KategoriID]'><img src='img/edit.png'>
      $w[GolonganID]</a></td>
      <td $c>$w[KategoriID]</td>
      <td $c>$w[Pangkat]</td>
      <td $c align=right>$w[TFung]</td>
      <td $c align=right>$w[TSKS]</td>
      <td $c align=right>$w[TTrans]</td>
      <td $c align=right>$w[TTtp]</td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  echo "</table></p></div>";
}
function HonjiCpy() {
  $prodi = $_SESSION['prodi'];
  $NamaProdi = GetaField('prodi', 'ProdiID', $prodi, 'Nama');
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", "ProdiID", '', '', 'ProdiID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='honorgaji'>
  <input type=hidden name='gos' value='HonjiCpy1'>
  <input type=hidden name='prodi' value='$prodi'>
  
  <tr><th class=ttl colspan=2>Copy Golongan dari Program Studi Lain</th></tr>
  <tr><td class=ul colspan=2>Saat menyalin golongan dari program studi lain, sistem akan melakukan:
    <ol>
      <li>Menghapus data golongan dari Program Studi ini.</li>
      <li>Menyalin data golongan dari Program Studi lain.</li>
    </ol></td></tr>
  <tr><td class=inp>Dari Program Studi:</td><td class=ul><select name='DariProdiID'>$optprodi</select></td></tr>
  <tr><td class=inp>Ke Program Studi:</td><td class=ul>$prodi - $NamaProdi</td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Copy' value='Copy'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=honorgaji'\">
    </td></tr>
  </table></p>";
}
function HonjiCpy1() {
  $prodi = $_SESSION['prodi'];
  $DariProdiID = $_REQUEST['DariProdiID'];
  if (!empty($DariProdiID)) {
    // Hapus data dari prodi
    $sdel = "delete from golongan where ProdiID='$prodi' ";
    $rdel = _query($sdel);
    // Ambil data dari prodi
    $spr = "select * from golongan 
      where ProdiID='$DariProdiID' and KodeID='$_SESSION[KodeID]'
      order by GolonganID, KategoriID";
    $rpr = _query($spr);
    while ($wpr = _fetch_array($rpr)) {
      $s = "insert into golongan (GolonganID, KategoriID, ProdiID, KodeID,
        Pangkat, TunjanganFungsional, TunjanganSKS)
        values('$wpr[GolonganID]', '$wpr[KategoriID]', '$prodi', '$_SESSION[KodeID]',
        '$wpr[Pangkat]', '$wpr[TunjanganFungsional]', '$wpr[TunjanganSKS]')";
      $r = _query($s);
    }
  }
  DftrGolongan($prodi);
}
function HonjiEdt() {
  $prodi = $_REQUEST['prodi'];
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $golid = $_REQUEST['golid'];
    $katid = $_REQUEST['katid'];
    $w = GetFields('golongan', "GolonganID='$golid' and KategoriID='$katid' and ProdiID", $prodi, '*');
    $jdl = "Edit Golongan";
    $golid = "<input type=hidden name='GolonganID' value='$w[GolonganID]'><b>$w[GolonganID]</b>";
    $katid = "<input type=hidden name='KategoriID' value='$w[KategoriID]'><b>$w[KategoriID]</b>";
  }
  else {
    $w = array();
    $w['ProdiID'] = '';
    $w['GolonganID'] = '';
    $w['KategoriID'] = '';
    $w['Pangkat'] = '';
    $w['TunjanganFungsional'] = 0;
    $w['TunjanganSKS'] = 0;
    $w['TunjanganTransport'] = 0;
    $w['TunjanganTetap'] = 0;
    $w['NA'] = 'N';
    $jdl = "Tambah Golongan";
    $golid = "<input type=text name='GolonganID' value='$w[GolonganID]' size=5 maxlength=5>";
    $katid = "<input type=text name='KategoriID' value='$w[KategoriID]' size=5 maxlength=5>";
  }
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  // Tampilkan
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST name='data'>
  <input type=hidden name='mnux' value='honorgaji'>
  <input type=hidden name='gos' value='HonjiSav'>
  <input type=hidden name='prodi' value='$_SESSION[prodi]'>
  <input type=hidden name='md' value='$md'>
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Golongan</th><td class=ul>$golid</td></tr>
  <tr><td class=inp>Kategori</th><td class=ul>$katid</td></tr>
  <tr><td class=inp>Pangkat</th><tD class=ul><input type=text name='Pangkat' value='$w[Pangkat]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Tunjangan Fungsional (dosen tetap)</td><td class=ul><input type=text name='TunjanganFungsional' value='$w[TunjanganFungsional]' size=20 maxlength=20> per bulan</td></tr>
  <tr><td class=inp>Honor per SKS (dosen honorer)</td><td class=ul><input type=text name='TunjanganSKS' value='$w[TunjanganSKS]' size=20 maxlength=20> per SKS</td></tr>
  <tr><td class=inp>Transport</td><td class=ul><input type=text name='TunjanganTransport' value='$w[TunjanganTransport]' size=20 maxlength=20> per pertemuan</td></tr>
  <tr><td class=inp>Honor Tetap</td><td class=ul><input type=text name='TunjanganTetap' value='$w[TunjanganTetap]' size=20 maxlength=20> per pertemuan</td></tr>
  <tr><td class=inp>Tidak aktif (NA)?</td><td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=honorgaji&gos='\"></td></tr>
  </form></table></p>";
}
function HonjiSav() {
  $md = $_REQUEST['md']+0;
  $GolonganID = $_REQUEST['GolonganID'];
  $KategoriID = $_REQUEST['KategoriID'];
  $Pangkat = $_REQUEST['Pangkat'];
  $TunjanganFungsional = $_REQUEST['TunjanganFungsional']+0;
  $TunjanganSKS = $_REQUEST['TunjanganSKS']+0;
  $TunjanganTransport = $_REQUEST['TunjanganTransport']+0;
  $TunjanganTetap = $_REQUEST['TunjanganTetap']+0;
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update golongan set Pangkat='$Pangkat',
      TunjanganFungsional='$TunjanganFungsional', TunjanganSKS='$TunjanganSKS',
      TunjanganTransport='$TunjanganTransport', TunjanganTetap='$TunjanganTetap',
      NA='$NA'
      where KodeID='$_SESSION[KodeID]'
        and GolonganID='$GolonganID' and KategoriID='$KategoriID'
        and ProdiID='$_SESSION[prodi]' ";
    $r = _query($s);
  }
  else {
    $ada = GetFields("golongan", "KodeID='$_SESSION[KodeID]'
      and GolonganID='$GolonganID' and KategoriID='$KategoriID' and ProdiID",
      $_SESSION['prodi'], '*');
    if (empty($ada)) {
    }
    else echo ErrorMsg("Tidak Dapat Disimpan",
      "Golongan: <b>$GolonganID</b> <br />
      Kategori: <b>$KategoriID</b> <br />
      Program Studi: <b>$_SESSION[prodi]</b> <br />
      Data sudah ada. Tidak dapat disimpan data yang identik.");
  }
  DftrGolongan($_SESSION['prodi']);
}

// *** Parameters ***
$prodi = GetSetVar('prodi');
$gos = (empty($_REQUEST['gos']))? 'DftrGolongan' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Honor dan Gaji Dosen");
TampilkanPilihanProdi('honorgaji', 'DftrGolongan', '', '');
if (!empty($prodi)) $gos($prodi);
?>