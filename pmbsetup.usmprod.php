<?php
// Author: Emanuel Setio Dewo, setio_dewo@sisfokampus.net
// 2005-12-27

// *** Functions ***
$prodid = GetSetVar('prodiid');
$drpmbperiod = GetSetVar('drpmbperiod');

function DefUSMProd() {
  global $mnux, $pref;
  $ki = DaftarProdi("mnux=$mnux&$pref=UsmProd");
  $ka = (!empty($_SESSION['prodiid']) && !empty($_SESSION['pmbperiod']))? TambahUSMProdi().DaftarUSMProdi() : '&nbsp;';
  $ka = "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='$mnux'>
    <input type=hidden name='$pref' value='UsmProd'>
    <tr><td class=ul>Periode PMB</td>
      <td class=ul><input type=text name='pmbperiod' value='$_SESSION[pmbperiod]' size=10 maxlength=50>
      <input type=submit name='Tampilkan' value='Tampilkan'></td></tr>
    </form></table></p> $ka";
  
  echo "<table class=bsc cellspacing=0 cellpadding=4>
  <tr><td valign=top>$ki</td>
  <td valign=top>$ka</td>
  </tr></table>";
}
function DaftarUSMProdi() {
  global $mnux, $pref;
  if (isset($_SESSION['prodiid'])) {
    $a = "<p><table class=box cellspacing=1 cellpadding=4>
      <form action='?' method=POST>
      <input type=hidden name='mnux' value='$mnux'>
      <input type=hidden name='$pref' value='UsmProd'>
      <input type=hidden name='sub' value='UsmProdHapusAll'>
      <input type=hidden name='pmbperiod' value='$_SESSION[pmbperiod]'>
      <input type=hidden name='prodiid' value='$_SESSION[prodiid]'>
      <input type=hidden name='konfirm' value='-1'>
      <td class=wrn>Hapus Semua Test dalam Periode ini</td>
      <td class=ul><input type=submit name='Hapus' value='Hapus Test'></td>
      </form></table></p>";
    $a .= "<p><table class=box cellspacing=1 cellpadding=4>
      <tr><th class=ttl>Urutan</th>
      <th class=ttl>Kode</th>
      <th class=ttl>Test</th>
      <th class=ttl>Ujian</th>
      <th class=ttl>Ruang</th>
      <th class=ttl>Jumlah<br>Soal</th>
      <th class=ttl>Hapus</th></tr>";
    $s = "select pu.*, pmbu.Nama, date_format(pu.TanggalUjian, '%d/%m/%Y %H:%i') as UJN
      from prodiusm pu
      left outer join pmbusm pmbu on pu.PMBUSMID=pmbu.PMBUSMID
      where pu.ProdiID='$_SESSION[prodiid]' and pu.PMBPeriodID='$_SESSION[pmbperiod]'
      order by pu.Urutan";
    $r = _query($s);
    $jml = 0;
    while ($w = _fetch_array($r)) {
      $jml += $w['JumlahSoal'];
      $a .= "<tr><td class=ul><a href='?mnux=$mnux&$pref=UsmProd&sub=UsmProdEdt&prodiusmid=$w[ProdiUSMID]'><img src='img/edit.png' border=0>
        $w[Urutan]</a></td>
        <td class=ul>$w[PMBUSMID]</td>
        <td class=ul>$w[Nama]</td>
        <td class=ul>$w[UJN]</td>
        <td class=ul>$w[RuangID]&nbsp;</td>
        <td class=ul align=right>$w[JumlahSoal]</td>
        <td class=ul align=center><a href='?mnux=$mnux&$pref=UsmProd&sub=UsmProdDel&prodiusmid=$w[ProdiUSMID]'><img src='img/del.gif' border=0></a></td>
        </tr>";
    }
    $a .= "<tr><td colspan=4 align=right>Total Soal :</td><td align=right><b>$jml</b></td></tr>";
    return $a . "</table></p>";
  }
  else return Konfirmasi("Pilih Prodi", "Tentukan salah satu prodi terlebih dahulu");
}
function UsmProdHapusAll() {
  global $mnux, $pref;
  if ($_REQUEST['konfirm'] == 1) {
    // Hapus
    $s = "delete from prodiusm where ProdiID='$_SESSION[prodiid]' and PMBPeriodID='$_SESSION[pmbperiod]' ";
    $r = _query($s);
    echo $s;
    DefUSMProd();
  }
  else {
    // konfirmasi penghapusan
    echo Konfirmasi("Konfirmasi Penghapusan",
      "Benar Anda akan menghapus semua test dalam periode PMB <b>$_SESSION[pmbperiod]</b>
      dari Program Studi <b>$_SESSION[prodiid]</b> ini?
      <hr size=1 color=silver>
      Pilihan: <a href='?mnux=$mnux&$pref=UsmProd&sub=UsmProdHapusAll&konfirm=1&prodiid=$_SESSION[prodiid]&pmbperiod=$_SESSION[pmbperiod]'>Hapus</a> |
      <a href=''>Batal</a>");
  }
}
function UsmProdEdt() {
  global $mnux, $pref;
  $prodiusmid = $_REQUEST['prodiusmid'];
  $w = GetFields("prodiusm pru left outer join pmbusm pu on pu.PMBUSMID=pru.PMBUSMID", 
    "pru.ProdiUSMID", $prodiusmid, "pru.*, pru.ProdiID, pu.Nama, date_format(pru.TanggalUjian, '%H:%i') as JamUjian");
  if (!empty($w)) {
    $prodi = GetaField('prodi', 'ProdiID', $w['ProdiID'], 'Nama');
    $tgl = GetDateOption($w['TanggalUjian'], 'tu');
    $jam = GetTimeOption($w['JamUjian'], 'tu');
    $_checkboxruang = GetCheckboxes('ruang', 'RuangID', "Concat(Nama, ' (Kaps: ', KapasitasUjian, ' orang)') as NM", "NM", $w['RuangID']);
    echo "<table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='$mnux'>
    <input type=hidden name='$pref' value='UsmProd'>
    <input type=hidden name='sub' value='UsmProdTglSav'>
    <input type=hidden name='prodiusmid' value='$prodiusmid'>
    <tr><th colspan=2 class=ttl>Edit Test Prodi</th></tr>
    <tr><td class=ul>Program Studi</td><td class=ul>$w[ProdiID] - <b>$prodi</td></tr>
    <tr><td class=ul>Nama Test</td><td class=ul><b>$w[Nama]</td></tr>
    <tr><td class=ul>Tanggal ujian</td><td class=ul>$tgl</td></tr>
    <tr><td class=ul>Waktu ujian</td><td class=ul>$jam</td></tr>
    <tr><td class=ul>Ruangan Ujian</td><td class=ul>$_checkboxruang</td></tr>
    <tr><td class=ul>Jumlah Soal</td><td class=ul><input type=text name='JumlahSoal' value='$w[JumlahSoal]' size=3 maxlength=3></td></tr>
    <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
      <input type=reset name='Reset' value='Reset'>
      <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=UsmProd'\"></td></tr>
    </form></table>";
  }
  else DefUSMProd();
}
function CopyUsmProdSav() {
  $s = "select *
    from prodiusm
    where ProdiID='$_SESSION[prodiid]' and PMBPeriodID='$_SESSION[drpmbperiod]'
    order by Urutan";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $s1 = "insert into prodiusm
      (PMBUSMID, PMBPeriodID, ProdiID, Urutan, 
      TanggalUjian, RuangID, JumlahSoal)
      values ('$w[PMBUSMID]', '$_SESSION[pmbperiod]',
      '$w[ProdiID]', '$w[Urutan]',
      '$w[TanggalUjian]', '$w[RuangID]', '$w[JumlahSoal]')";
    $r1 = _query($s1);
  }
  DefUSMProd();
}
function UsmProdTglSav() {
  $prodiusmid = $_REQUEST['prodiusmid'];
  $TanggalUjian = "$_REQUEST[tu_y]-$_REQUEST[tu_m]-$_REQUEST[tu_d] $_REQUEST[tu_h]:$_REQUEST[tu_n]";
  $JumlahSoal = $_REQUEST['JumlahSoal']+0;
  $RuangID = array();
  $RuangID = $_REQUEST['RuangID'];
  $_strRuangID = (empty($RuangID))? '' : implode(',', $RuangID);
  $s = "update prodiusm set TanggalUjian='$TanggalUjian', JumlahSoal='$JumlahSoal', RuangID='$_strRuangID'
    where ProdiUSMID=$prodiusmid";
  $r = _query($s);
  DefUSMProd();
  //echo $s;
}
function TambahUSMProdi() {
  global $mnux, $pref;
  //GetOption2($_table, $_field, $_order='', $_default='', $_where='', $_value='', $not=0) {
  $opt = GetOption2('pmbusm', "Nama", 'Nama', '', '', 'PMBUSMID');
  $a = "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><th colspan=2 class=ul>Tambahkan Test</th>
      <th colspan=2 class=ul>Salin dari Periode Lain</th>
  </tr>
  
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='UsmProd'>
  <input type=hidden name='sub' value='UsmProdSav'>
  
  <tr><td class=inp1>Test</td><td class=ul><select name='PMBUSMID'>$opt</select>
    <input type=submit name='Tambahkan' value='Tambahkan'></td>
  </form>
  
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='UsmProd'>
  <input type=hidden name='sub' value='CopyUsmProdSav'>

  <td class=inp1>Dari Periode</td>
    <td class=ul><input type=text name='drpmbperiod' value='$_SESSION[drpmbperiod]' size=10 maxlength=50>
    <input type=submit name='Salin' value='Salin'></td>
  </form>
  
  
  </tr>
  </table></p>";
  return $a;
}
function UsmProdSav() {
  // cek dulu
  $_per = GetFields('pmbperiod', "NA", 'N', '*');
  $ada = GetaField('prodiusm', "ProdiID='$_SESSION[prodiid]' and PMBPeriodID='$_SESSION[pmbperiod]' and PMBUSMID", $_REQUEST['PMBUSMID'], 'ProdiUSMID');
  if (!empty($ada)) echo ErrorMsg('Error', "Test sudah pernah ditambahkan.");
  else {
    $Urutan = GetaField('prodiusm', "PMBPeriodID='$_SESSION[pmbperiod]' and ProdiID", $_SESSION['prodiid'], "max(Urutan)")+1;
    $s = "insert into prodiusm(PMBPeriodID, ProdiID, PMBUSMID, Urutan, TanggalUjian)
      values('$_SESSION[pmbperiod]',
      '$_SESSION[prodiid]', '$_REQUEST[PMBUSMID]', $Urutan, '$_per[UjianMulai] 08:00:00')";
    $r = _query($s);
  }
  DefUSMProd();
}
function UsmProdDel() {
  global $mnux, $pref;
  $data = GetFields("prodiusm pu left outer join pmbusm pmbu on pu.PMBUSMID=pmbu.PMBUSMID",
    "ProdiUSMID", $_REQUEST['prodiusmid'], "pu.*, pmbu.Nama");
  echo Konfirmasi('Konfirmasi Hapus',
    "Benar Anda akan menghapus test <b>$data[Urutan]. $data[Nama]</b>?<hr size=1 color=silver>
    Pilihan: <a href='?mnux=$mnux&$pref=UsmProd&sub=UsmProdDel1&prodiusmid=$_REQUEST[prodiusmid]'>Hapus</a> |
    <a href='?mnux=$mnux&$pref=UsmProd'>Batal</a>");
}
function UsmProdDel1() {
  $s = "delete from prodiusm where ProdiUSMID='$_REQUEST[prodiusmid]'";
  $r = _query($s);
  DefUSMProd();
}
?>