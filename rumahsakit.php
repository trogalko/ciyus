<?php
// Author: Emanuel Setio Dewo
// 23 November 2006

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "TampilkanDaftarRS" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Master Rumah Sakit");
$gos();

// *** Functions ***
function TampilkanDaftarRS() {
  $s = "select *
    from rumahsakit
    order by RSID";
  $r = _query($s);
  echo "<p><a href='?mnux=rumahsakit&gos=RSEDT&md=1'>Tambah RS</a></p>";
  
  $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Nama RS</th>
    <th class=ttl>Telepon</th>
    <th class=ttl>Alamat</th>
    <th class=ttl>Kota</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td class=inp>$n</td>
    <td $c><a href='?mnux=rumahsakit&gos=RSEDT&md=0&RSID=$w[RSID]'><img src='img/edit.png'> $w[RSID]</a></td>
    <td $c>$w[Nama]</td>
    <td $c>$w[Telephone]&nbsp;</td>
    <td $c>$w[Alamat]&nbsp;</td>
    <td $c>$w[Kota]&nbsp;</td>
    <td class=ul align=center><img src='img/book$w[NA].gif'></td>
    </tr>";
  }
  echo "</table></p>";
}
function RSEDT() {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $RSID = $_REQUEST['RSID'];
    $w = GetFields('rumahsakit', 'RSID', $RSID, '*');
    $jdl = "Edit Rumahsakit";
    $_RSID = "<input type=hidden name='RSID' value='$RSID'><b>$RSID</b>";
  }
  else {
    $w = array();
    $w['RSID'] = '';
    $w['Nama'] = '';
    $w['Alamat'] = '';
    $w['Kota'] = '';
    $w['KodePos'] = '';
    $w['Propinsi'] = '';
    $w['Negara'] = '';
    $w['Telephone'] = '';
    $w['Fax'] = '';
    $w['Website'] = '';
    $w['Email'] = '';
    $w['Kontak'] = '';
    $w['JabatanKontak'] = '';
    $w['HandphoneKontak'] = '';
    $w['EmailKontak'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Rumahsakit";
    $_RSID = "<input type=text name='RSID' size=30 maxlength=50>";
  }
  // params
  $_NA = ($w['NA'] == 'Y')? 'checked' : '';
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='rumahsakit'>
  <input type=hidden name='gos' value='RSSAV'>
  <input type=hidden name='md' value='$md'>
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Kode RS</td><td class=ul>$_RSID</td></tr>
  <tr><td class=inp>Rumah Sakit</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=50 maxlength=50></td></tr>
  <tr><td class=inp>Alamat</td><td class=ul><textarea name='Alamat' cols=40 rows=3>$w[Alamat]</textarea></td></tr>
  <tr><td class=inp>Kota</td><td class=ul><input type=text name='Kota' value='$w[Kota]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Kode Pos</td><td class=ul><input type=text name='KodePos' value='$w[KodePos]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Propinsi</td><td class=ul><input type=text name='Propinsi' value='$w[Propinsi]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Negara</td><td class=ul><input type=text name='Negara' value='$w[Negara]' size=30 maxlength=30></td></tr>
  <tr><td class=inp>Telephone</td><td class=ul><input type=text name='Telepone' value='$w[Telephone]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Fax</td><td class=ul><input type=text name='Fax' value='$w[Fax]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Website</td><td class=ul><input type=text name='Website' value='$w[Website]' size=50 maxlength=50></td></tr>
  <tr><td class=inp>Email</td><td class=ul><input type=text name='Email' value='$w[Email]' size=50 maxlength=50></td></tr>
  
  <tr><th class=ttl colspan=2>Kontak</th></tr>
  <tr><td class=inp>Nama Kontak</td><td class=ul><input type=text name='Kontak' value='$w[Kontak]' size=50 maxlength=50></td></tr>
  <tr><td class=inp>Jabatan</td><td class=ul><input type=text name='JabatanKontak' value='$w[JabatanKontak]' size=50 maxlength=50></td></tr>
  <tr><td class=inp>No Handphone</td><td class=ul><input type=text name='HandphoneKontak' value='$w[HandphoneKontak]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Email</td><td class=ul><input type=text name='EmailKontak' value='$w[EmailKontak]' size=50 maxlength=50></td></tr>
  <tr><td class=inp>Tidak Aktif? (NA)</td><td class=ul><input type=checkbox value='Y' name='NA' $_NA></td></tr>
  
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=rumahsakit'\"></td></tr>
  </form></table></p>";
}
function RSSAV() {
  $md = $_REQUEST['md']+0;
  $RSID = $_REQUEST['RSID'];
  $Nama = sqling($_REQUEST['Nama']);
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $KodePos = sqling($_REQUEST['KodePos']);
  $Propinsi = sqling($_REQUEST['Propinsi']);
  $Negara = sqling($_REQUEST['Negara']);
  $Telephone = sqling($_REQUEST['Telephone']);
  $Fax = sqling($_REQUEST['Fax']);
  $Website = sqling($_REQUEST['Website']);
  $Email = sqling($_REQUEST['Email']);
  $Kontak = sqling($_REQUEST['Kontak']);
  $JabatanKontak = sqling($_REQUEST['JabatanKontak']);
  $HandphoneKontak = sqling($_REQUEST['HandphoneKontak']);
  $EmailKontak = sqling($_REQUEST['EmailKontak']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update rumahsakit
      set Nama='$Nama',
      Alamat='$Alamat', Kota='$Kota',
      KodePos='$KodePos', Propinsi='$Propinsi',
      Negara='$Negara', Telephone='$Telephone', Fax='$Fax',
      Website='$Website', Email='$Email',
      Kontak='$Kontak', JabatanKontak='$JabatanKontak',
      HandphoneKontak='$HandphoneKontak', EmailKontak='$EmailKontak',
      NA='$NA'
      where RSID='$RSID' ";
    $r = _query($s);
    TampilkanDaftarRS();
  }
  else {
    $ada = GetFields("rumahsakit", "RSID", $RSID, "RSID, Nama");
    if (empty($ada)) {
      $s = "insert into rumahsakit
        (RSID, Nama, Alamat, Kota, KodePos, Propinsi,
        Negara, Telephone, Fax,
        Website, Email,
        Kontak, JabatanKontak,
        HandphoneKontak, EmailKontak, NA)
        values
        ('$RSID', '$Nama', '$Alamat', '$Kota', '$KodePos', '$Propinsi',
        '$Negara', '$Telephone', '$Fax',
        '$Website', '$Email',
        '$Kontak', '$JabatanKontak',
        '$Handphone', '$EmailKontak', '$NA')";
      $r = _query($s);
      echo "<script>window.location='?mnux=rumahsakit';</script>"; 
    }
    else echo ErrorMsg("Tidak Dapat Disimpan",
      "Data Rumahsakit tidak dapat disimpan karena Kode: <font size=+1>$RSID</font>
      telah digunakan oleh <b>$ada[Nama]</b>.<br />
      Gunakan kode lain.
      <hr size=1>
      Pilihan: <a href='?mnux=rumahsakit'>Kembali</a>");
  }
  
}
?>
