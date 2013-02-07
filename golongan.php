<?php
// Author: Emanuel Setio Dewo
// 30 Sept 2006
// http://www.sisfokampus.net

// *** Functions ***
function DaftarGolongan() {
  $s = "select * 
    from golongan 
    where ProdiID='$_SESSION[prodi]' order by GolonganID, KategoriID";
  $r = _query($s); $n = 0;
  $count = _num_rows($r);
  if ($count == 0) echo "<a href='?mnux=golongan&gos=GolImprt'>Import Default Golongan</a>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>No</th>
    <th colspan=2 class=ttl title='Golongan'>Gol</th>
    <th class=ttl title='Kategori'>Kat</th>
    <th class=ttl>Pangkat</th>
    <th class=ttl>Nama</th>
    <th class=ttl title='Tunjangan Fungsional'>Fungsional</th>
    <th class=ttl title='Tunjangan per SKS'>per SKS</th>
    <th class=ttl title='Tunjangan Tranport'>Transport</th>
    <th class=ttl title='Tunjangan Tetap'>Tetap</th>
    <th class=ttl title='Tidak Aktif?'>NA</th>
    </tr>";
  $_gol = 'qwertyuiop';
  while ($w = _fetch_array($r)) {
    $n++;
    if ($_gol != $w['GolonganID']) {
      $_gol = $w['GolonganID'];
      $_strgol = "<b>$w[GolonganID]</b>";
    } 
    else $_strgol = "<img src='img/brch.gif'>";
    $TFun = number_format($w['TunjanganFungsional']);
    $TSKS = number_format($w['TunjanganSKS']);
    $TTra = number_format($w['TunjanganTransport']);
    $TTtp = number_format($w['TunjanganTetap']);
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr>
    <td class=inp>$n</td>
    <td class=ul><a href='?mnux=golongan&md=0&gos=GolEdt&_GID=$w[GolonganID]&_KID=$w[KategoriID]'><img src='img/edit.png'></a></td>
    <td class=ul>$_strgol</td>
    <td $c>$w[KategoriID]</td>
    <td $c>$w[Pangkat]&nbsp;</td>
    <td $c>$w[Nama]&nbsp;</td>
    <td $c align=right>$TFun</td>
    <td $c align=right>$TSKS</td>
    <td $c align=right>$TTra</td>
    <td $c align=right>$TTtp</td>
    <td class=ul><img src='img/book$w[NA].gif'></td>
    </tr>";
  }
  echo "</table></p>";
}
function GolImprt(){
  $w = GetFields('prodi', 'ProdiID', $_SESSION['prodi'],'Nama');
  echo Konfirmasi("Import Data Golongan", "Anda akan mengimport data default untuk Master Golongan pada Prodi <b>$w[Nama]</b>. Proses ini hanya akan mengimport data default. Silakan edit kembali nilai nominal uang sesuai dengan kebijakan di institusi anda.
                  <hr><a href=?mnux=golongan&gos=DaftarGolongan>Batal</a> | <a href='?mnux=golongan&gos=doImport'>Proses</a>");
}

function doImport(){
  $prodi = $_SESSION['prodi'];
  
  $s = "SELECT * FROM _golongan order by GolonganID";
  $r = _query($s);
  
  while ($w = _fetch_array($r)) {
    $s1 = "insert into golongan (GolonganID, KategoriID, KodeID, ProdiID, Pangkat, Nama)
           VALUES ('$w[GolonganID]', '$w[KategoriID]', '$_SESSION[_KodeID]', '$_SESSION[prodi]', '$w[Pangkat]', '$w[Nama]')";
    $r1 = _query($s1);
  }
  
  DaftarGolongan();
}
function GolEdt() {
  $_GID = $_REQUEST['_GID'];
  $_KID = $_REQUEST['_KID'];
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $gol = GetFields('golongan', "KategoriID='$_KID' and ProdiID='$_SESSION[prodi]' and GolonganID", $_GID, "*");
    $jdl = "Edit Golongan";
    $_strgol = "<input type=hidden name='_GID' value='$_GID'><font size=+1>$_GID</font>";
    $_strkat = "<input type=hidden name='_KID' value='$_KID'><font size=+1>$_KID</font>";
  }
  else {
    $gol = array();
    $gol['GolonganID'] = '';
    $gol['KategoriID'] = '';
    $gol['Pangkat'] = '';
    $gol['Nama'] = '';
    $gol['TunjanganFungsional'] = 0;
    $gol['TunjanganSKS'] = 0;
    $gol['TunjanganTransport'] = 0;
    $gol['TunjanganTetap'] = 0;
    $gol['NA'] = 'N';
    $jdl = "Tambah Golongan";
    $_strgol = "<input type=text name='_GID' size=10 maxlength=20>";
    $_strkat = "<input type=text name='_KID' size=10 maxlength=20>";
  }
  $_na = ($gol['NA'] == 'Y')? 'checked' : '';
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST name='GolEdt'>
  <input type=hidden name='mnux' value='golongan'>
  <input type=hidden name='gos' value='GolSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='BypassMenu' value=1>
  <tr><td class=ul colspan=2><font size=+1>$jdl</font></td></tr>
  <tr><td class=inp>Golongan</td><td class=ul>$_strgol</td></tr>
  <tr><td class=inp>Kategori</td><td class=ul>$_strkat</td></tr>
  <tr><td class=inp>Pangkat</td><td class=ul><input type=text name='Pangkat' value='$gol[Pangkat]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Nama (Keterangan)</td><td class=ul><input type=text name='Nama' value='$gol[Nama]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Tunjangan Fungsional</td><td class=ul><input type=text name='TunjanganFungsional' value='$gol[TunjanganFungsional]' size=30 maxlength=30></td></tr>
  <tr><td class=inp>Tunjangan SKS</td><td class=ul><input type=text name='TunjanganSKS' value='$gol[TunjanganSKS]' size=30 maxlength=30> per SKS per visit</td></tr>
  <tr><td class=inp>Tunjangan Transport</td><td class=ul><input type=text name='TunjanganTransport' value='$gol[TunjanganTransport]' size=30 maxlength=30> per visit</td></tr>
  <tr><td class=inp>Tunjangan Tetap (Fix)</td><td class=ul><input type=text name='TunjanganTetap' value='$gol[TunjanganTetap]' size=30 maxlength=30> per visit</td></tr>
  <tr><td class=inp>NA (Tidak aktif)?</td><td class=ul><input type=checkbox name='NA' value='Y' $_na> Beri centang jika tidak aktif</td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?'\"></td></tr>
  </form>
  </table></p>";
}
function GolSav() {
  $_GID = $_REQUEST['_GID'];
  $_KID = $_REQUEST['_KID'];
  $Pangkat = sqling($_REQUEST['Pangkat']);
  $Nama = sqling($_REQUEST['Nama']);
  $TunjanganFungsional = $_REQUEST['TunjanganFungsional']+0;
  $TunjanganSKS = $_REQUEST['TunjanganSKS']+0;
  $TunjanganTransport = $_REQUEST['TunjanganTransport']+0;
  $TunjanganTetap = $_REQUEST['TunjanganTetap']+0;
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  $md = $_REQUEST['md']+0;
  $balik = "<script>window.location='?mnux=golongan';</script>";
  if ($md == 0) {
    $s = "update golongan
      set Pangkat='$Pangkat', Nama='$Nama',
      TunjanganFungsional=$TunjanganFungsional,
      TunjanganSKS=$TunjanganSKS,
      TunjanganTransport=$TunjanganTransport,
      TunjanganTetap=$TunjanganTetap,
      NA='$NA'
      where GolonganID='$_GID' and ProdiID='$_SESSION[prodi]' and KategoriID='$_KID' ";
    $r = _query($s);
    echo $balik;
  }
  else {
    $ada = GetFields("golongan", "GolonganID='$_GID' and KategoriID", $_KID, '*');
    if (empty($ada)) {
    }
    else echo ErrorMsg("Data sudah Ada",
      "Data untuk golongan: <font size=+1>$_GID</font> dan Kategori: <font size=+1>$_KID</font>
      sudah ada. Anda tidak boleh memasukkan golongan & kategori ini lebih dari 1 kali.
      <hr size=1 color=silver>
      Pilihan: <input type=button name='Kembali' value='Kembali' onClick=\"location='?'\">");
  }
}

// *** Parameters ***
$prodi = GetSetVar('prodi');
$gos = (empty($_REQUEST['gos']))? "DaftarGolongan" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Master Golongan");
//TampilkanPilihanProdi($mnux='', $gos='', $pref='', $token='') {
TampilkanPilihanProdi('golongan');
if (!empty($prodi)) $gos();
?>
