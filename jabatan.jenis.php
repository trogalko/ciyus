<?php
// Author: Sugeng
// 9 Juni 2008

function DftrJenisJabatan() {
  global $mnux, $tok;
  
  $s = "SELECT * FROM jenisjabatan jj WHERE KodeID = '$_SESSION[_KodeID]' ORDER BY Urutan";
  $r = _query($s);
  echo "<a href='?mnux=$mnux&tok=$tok&sub=JabatanNamaEdt&md=1'>Tambah Master Nama Jabatan</a>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
	<tr><th class=ttl>#</th><th class=ttl>Kode Jabatan</th><th class=ttl>Urutan Jabatan</th><th class=ttl>Nama Jabatan</th></tr>";
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr><td class=inp1>$n &nbsp; </td><td class=ul><a href='?mnux=$mnux&tok=$tok&sub=JabatanNamaEdt&md=0&jjbnid=$w[JenisJabatanID]'><img src='img/edit.png'> </a>$w[Singkatan]</td><td class=ul>$w[Urutan]</td><td class=ul>$w[Nama]</td></tr>";
  }
  
  echo "</table></p>";
}

function JabatanNamaEdt() {
  global $mnux, $tok;

  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('jenisjabatan', 'JenisJabatanID', $_REQUEST['jjbnid'], '*');
    $Jdl = "Edit Nama Jabatan";
  }
  else {
    $w = array();
    $w['JenisJabatanID'] = 0;
    $w['Urutan'] = 0;
    $w['Nama'] = '';
    $w['Singkatan'] = '';
    $w['Catatan'] = '';
    $w['NA'] = 'N';
    $Jdl = "Tambah Nama Jabatan";
  }
  $NA = ($w['NA'] == 'Y')? 'checked' : '';

  CheckFormScript("Nama,Urutan");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='tok' value='$tok'>
  <input type=hidden name='sub' value='JabatanNamaSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='jjbnid' value='$w[JenisJabatanID]'>
  <tr><th class=ttl colspan=2>$Jdl</th></tr>
  <tr><td class=inp1>Urutan</td><td class=ul><input type=text name='Urutan' value='$w[Urutan]' size=3 maxlength=3> <font color=red>*)</font></td></tr>
  <tr><td class=inp1>Kode Jabatan</td><td class=ul><input type=text name='Singkatan' value='$w[Singkatan]' size=25 maxlength=50></td></tr>
  <tr><td class=inp1>Nama Jabatan</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40></td></tr>
  <tr><td class=inp1>Catatan</td><td class=ul><textarea name='Catatan' cols=30 rows=2>$w[Catatan]</textarea></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&tok=$tok&sub='\"></td></tr>
  </form></table></p>";
}
function JabatanNamaSav() {
  $md = $_REQUEST['md']+0;
  $Urutan = $_REQUEST['Urutan']+0;
  $Nama = sqling($_REQUEST['Nama']);
  $Singkatan = sqling($_REQUEST['Singkatan']);
  $Catatan = sqling($_REQUEST['Catatan']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  // Simpan
  if ($md == 0) {
    $s = "update jenisjabatan set Urutan='$Urutan', Nama='$Nama', Singkatan='$Singkatan', 
      Catatan='$Catatan', NA='$NA',
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where JenisJabatanID='$_REQUEST[jjbnid]' ";
  }
  else {
    $s = "insert into jenisjabatan (Urutan, Nama, Singkatan, KodeID, 
	  Catatan, NA, TanggalBuat, LoginBuat)
      values ('$Urutan', '$Nama', '$Singkatan', '$_SESSION[KodeID]', 
	  '$Catatan', '$NA', now(), '$_SESSION[_Login]')";
  }
  $r = _query($s);
  
  DftrJenisJabatan();
}

?>
