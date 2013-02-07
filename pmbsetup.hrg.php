<?php
// Author: Emanuel Setio Dewo
// 2005-12-17

function DftrHrg() {
  global $pref, $mnux, $arrID;
  $s = "select * from pmbformulir order by PMBFormulirID";
  $r = _query($s);
  $_tambah = "<a href=\"?mnux=$mnux&$pref=Hrg&sub=FrmEdt&md=1\">Tambah Formulir</a>";
  echo "<table class=box cellspacing=1 cellpadding=4>
    <tr><td class=ul colspan=5><strong>$arrID[Nama]</strong></td></tr>
    <tr><td class=ul colspan=5>$_tambah</td></tr>
    <tr><th class=ttl>#</th><th class=ttl>Formulir</th>
    <th class=ttl>Harga</th><th class=ttl>Jml Pilihan</th>
    <th class=ttl>NA</th></tr>";
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    $hrg = number_format($w['Harga'], 0, ',', '.');
    echo "<tr><td class=inp1>$n</td>
    <td $c><a href=\"?mnux=$mnux&$pref=Hrg&md=0&sub=FrmEdt&fid=$w[PMBFormulirID]\"><img src='img/edit.png' border=0>
    $w[Nama]</a></td>
    <td $c align=right>$hrg</td>
    <td $c align=right>$w[JumlahPilihan]</td>
    <td $c align=center><img src='img/book$w[NA].gif'></td>
    </tr>";
  }
  echo "</table>";
}
function FrmEdt() {
  global $mnux, $pref, $_PMBMaxPilihan, $arrID;
  $md = $_REQUEST['md']+0;
  $PMBFormulirID = $_REQUEST['fid']+0;
  if ($md == 0) {
    $w = GetFields('pmbformulir', 'PMBFormulirID', $PMBFormulirID, '*');
    $jdl = 'Edit Formulir PMB';
  }
  else {
    $w = array();
    $w['PMBFormulirID'] = 0;
    $w['Nama'] = '';
    $w['KodeID'] = $arrID['Kode'];
    $w['Harga'] = 0;
    $w['JumlahPilihan'] = 1;
    $w['HanyaProdi1'] = '';
    $w['KecualProdi1'] = '';
    $w['HanyaProdi2'] = '';
    $w['KecualProdi2'] = '';
    $w['HanyaProdi3'] = '';
    $w['KecualProdi3'] = '';
    $w['NA'] = 'N';
    $w['Keterangan'] = '';
    $jdl = 'Tambah Formulir PMB';
  }
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  $pil = GetNumberOption(1, $_PMBMaxPilihan, $w['JumlahPilihan']);
  $optKID = GetOption2('identitas', "concat(Kode, ' - ', Nama)", 'Kode', $w['KodeID'], '', 'Kode');
  $snm = session_name(); $sid = session_id();
  CheckFormScript("_KodeID,Nama,JumlahPilihan,Harga");
  echo "<table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='Hrg'>
  <input type=hidden name='sub' value='FrmSav'>
  <input type=hidden name='PMBFormulirID' value='$w[PMBFormulirID]'>
  
  <tr><th colspan=2 class=ttl>$jdl</th></tr>
  <tr><td class=inp1>Institusi</td><td class=ul><select name='_KodeID'>$optKID</select></td></tr>
  <tr><td class=inp1>Nama Formulir</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>Jumlah Pilihan</td><td class=ul><select name='JumlahPilihan'>$pil</select></td></tr>
  <tr><td class=inp1>Harga Formulir</td><td class=ul><input type=text name='Harga' value='$w[Harga]' size=15 maxlength=15></td></tr>
  <tr><td class=inp1>Tidak aktif?</td><td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td class=inp1>Keterangan</td><td class=ul><textarea name='Keterangan' cols=40 rows=4>$w[Keterangan]</textarea></td></tr>
  
  <tr><td class=ul colspan=2><b>Setup Pilihan 1</td></tr>
  <tr><td class=inp1>Hanya Prodi <font color=red>*)</td><td class=ul><input type=text name='HanyaProdi1' value='$w[HanyaProdi1]' size=50 maxlength=100></td></tr>
  <tr><td class=inp1>Kecuali Prodi <font color=red>**)</td><td class=ul><input type=text name='KecualiProdi1' value='$w[KecualiProdi1]' size=50 maxlength=100></td></tr>
  
  <tr><td class=ul colspan=2><b>Setup Pilihan 2</td></tr>
  <tr><td class=inp1>Hanya Prodi <font color=red>*)</td><td class=ul><input type=text name='HanyaProdi2' value='$w[HanyaProdi2]' size=50 maxlength=100></td></tr>
  <tr><td class=inp1>Kecuali Prodi <font color=red>**)</td><td class=ul><input type=text name='KecualiProdi2' value='$w[KecualiProdi2]' size=50 maxlength=100></td></tr>
  
  <tr><td class=ul colspan=2><b>Setup Pilihan 3</td></tr>
  <tr><td class=inp1>Hanya Prodi <font color=red>*)</td><td class=ul><input type=text name='HanyaProdi3' value='$w[HanyaProdi3]' size=50 maxlength=100></td></tr>
  <tr><td class=inp1>Kecuali Prodi <font color=red>**)</td><td class=ul><input type=text name='KecualiProdi3' value='$w[KecualiProdi3]' size=50 maxlength=100></td></tr>
  
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=Hrg&sub=&$snm=$sid'\"></td></tr>
  
  </form></table><br>";
  // Keterangan
  echo "<table class=box cellspacing=0 cellpadding=4>
  <tr><td class=ul><font color=red>*)</td><td class=ul>Jika formulir ini hanya berlaku untuk prodi ini.</td></tr>
  <tr><td class=ul><font color=red>**)</td><td class=ul>Jika formulir ini tidak berlaku untuk prodi ini.</td></tr>
  <tr><td colspan=2>Masing-masing Prodi diapit tanda titik (.)</td></tr>
  </table>";
}
function FrmSav() {
  global $_PMBMaxPilihan;
  $md = $_REQUEST['md']+0;
  $PMBFormulirID = $_REQUEST['PMBFormulirID']+0;
  $Nama = sqling($_REQUEST['Nama']);
  $JumlahPilihan = $_REQUEST['JumlahPilihan']+0;
  $Harga = $_REQUEST['Harga']+0;
  $_KodeID = $_REQUEST['_KodeID'];
  // buat array penampung
  $HanyaProdi = array();
  $KecualiProdi = array();
  // Tampung ke array
  $HanyaProdi[1] = sqling($_REQUEST['HanyaProdi1']);
  $KecualiProdi[1] = sqling($_REQUEST['KecualiProdi1']);
  $HanyaProdi[2] = sqling($_REQUEST['HanyaProdi2']);
  $KecualiProdi[2] = sqling($_REQUEST['KecualiProdi2']);
  $HanyaProdi[3] = sqling($_REQUEST['HanyaProdi3']);
  $KecualiProdi[3] = sqling($_REQUEST['KecualiProdi3']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  $Keterangan = sqling($_REQUEST['Keterangan']);
  // Hanya & Kecuali
  for ($i=1; $i<=$_PMBMaxPilihan; $i++) {
    if ($i>$JumlahPilihan) {
      $KecualiProdi[$i] = '';
      $HanyaProdi[$i] = '';
    }
  }
  // Simpan
  if ($md == 0) {
    $s = "update pmbformulir set Nama='$Nama', KodeID='$_KodeID', JumlahPilihan=$JumlahPilihan,
      Harga=$Harga, 
      HanyaProdi1='$HanyaProdi[1]', KecualiProdi1='$KecualiProdi[1]',
      HanyaProdi2='$HanyaProdi[2]', KecualiProdi2='$KecualiProdi[2]', 
      HanyaProdi3='$HanyaProdi[3]', KecualiProdi3='$KecualiProdi[3]',  
      NA='$NA', Keterangan='$Keterangan'
      where PMBFormulirID=$PMBFormulirID";
  }
  else {
    $s = "insert into pmbformulir(Nama, KodeID, JumlahPilihan, Harga, 
      HanyaProdi1, KecualiProdi1, HanyaProdi2, KecualiProdi2, HanyaProdi3, KecualiProdi3,
      NA, Keterangan)
      values('$Nama', '$_KodeID', $JumlahPilihan, $Harga, 
      '$HanyaProdi[1]', '$KecualiProdi[1]', '$HanyaProdi[2]', '$KecualiProdi[2]', '$HanyaProdi[3]', '$KecualiProdi[3]', 
      '$NA', '$Keterangan')";
  }
  $r = _query($s);
  DftrHrg();
}
?>
