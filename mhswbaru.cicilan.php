<?php
// Author: Emanuel Setio Dewo
// 04 March 2006

// *** Functions ***
function TampilkanHeaderCicilan($cm) {
  $BIA = number_format($cm['TotalBiayaMhsw']);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp1>No PMB</td><td class=ul>$cm[PMBID]</td>
    <td class=inp1>Status</td><td class=ul>$cm[STA]</td></tr>
  <tr><td class=inp1>Nama Calon Mhsw</td><td class=ul>$cm[Nama]</td>
    <td class=inp1>Jenis Sekolah</td><td class=ul>$cm[JenisSekolahID]&nbsp;</td></tr>
  <tr><td class=inp1>Program</td><td class=ul>$cm[PRG]</td>
    <td class=inp1>Grade Nilai</td><td class=ul>$cm[GradeNilai] &nbsp;</td></tr></tr>
  <tr><td class=inp1>Program Studi</td><td class=ul>$cm[PRD]</td>
    <td class=inp1>Total Biaya</td><td class=ul>Rp. $BIA</td></tr>
  </table></p>";
  /*
  <tr><td class=ul colspan=2>Pilihan:
    <a href='?mnux=mhswbaru&gos=ImprtPMB&trm=$cm[PMBID]&pmbid=$cm[PMBID]'>Kembali ke Pemrosesan</a> |
    <a href='?mnux=mhswbaru'>Kembali ke Daftar Calon Mahasiswa</a></td></tr>
  </table></p>";
  */
}
function DftrCicilan($cm) {
  echo "<p><a href='?mnux=mhswbaru.cicilan&gos=CclEdt&md=1&pmbid=$cm[PMBID]'>Tambah Cicilan</a></p>";
  $s = "select *,
    format(Jumlah, 0) as JML,
    date_format(DariTanggal, '%d/%m/%Y') as DR,
    date_format(SampaiTanggal, '%d/%m/%Y') as SMP
    from cicilanmhsw
    where PMBID='$cm[PMBID]' and PMBMhswID=0
    order by Urutan";

  $r = _query($s); $ttl = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl rowspan=2>No</th>
    <th class=ttl rowspan=2>Judul</th>
    <th class=ttl rowspan=2>Jumlah</th>
    <th class=ttl colspan=2>Dibayarkan</th>
    <th class=ttl rowspan=2>Keterangan</th>
    <th class=ttl rowspan=2>Hapus</th>
    </tr>
    <tr><th class=ttl>Dari</th><th class=ttl>Sampai</th></tr>";
  while ($w = _fetch_array($r)) {
    $ttl += $w['Jumlah'];
    echo "<tr>
    <td class=inp1><a href='?mnux=mhswbaru.cicilan&gos=CclEdt&pmbid=$cm[PMBID]&cclid=$w[CicilanID]'><img src='img/edit.png'>
      $w[Urutan]</a></td>
    <td class=ul>$w[Judul]</td>
    <td class=ul align=right>$w[JML]</td>
    <td class=ul>$w[DR]</td>
    <td class=ul>$w[SMP]</td>
    <td class=ul>$w[Keterangan]&nbsp;</td>
    <td class=ul align=center><a href='?mnux=mhswbaru.cicilan&gos=CclDel&cclid=$w[CicilanID]'><img src='img/del.gif'></a></td>
    </tr>";
  }
  $_ttl = number_format($ttl, 0);
  echo "<tr><td class=ul colspan=2 align=right>Total :</td>
    <td class=ul align=right><b>$_ttl</td></tr>
    </table></p>";
}
function CclEdt($cm) {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('cicilanmhsw', 'CicilanID', $_REQUEST['cclid'], '*');
    $jdl = "Edit Cicilan";
  }
  else {
    $w = array();
    $w['CicilanID'] = 0;
    $w['Urutan'] = 0;
    $w['Judul'] = '';
    $w['Jumlah'] = 0;
    $w['DariTanggal'] = date('Y-m-d');
    $w['SampaiTanggal'] = date('Y-m-d');
    $w['Keterangan'] = '';
    $jdl = "Tambah Cicilan";
  }
  $dari = GetDateOption($w['DariTanggal'], 'dari');
  $sampai = GetDateOption($w['SampaiTanggal'], 'sampai');
  // Tampilkan
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='mhswbaru.cicilan'>
  <input type=hidden name='gos' value='CclSav'>
  <input type=hidden name='cclid' value='$w[CicilanID]'>
  <input type=hidden name='md' value='$md'>

  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp1>Urutan</td><td class=ul><input type=text name='Urutan' value='$w[Urutan]' size=3 maxlength=3></td></tr>
  <tr><td class=inp1>Judul</td><td class=ul><input type=text name='Judul' value='$w[Judul]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>Jumlah</td><td class=ul><input type=text name='Jumlah' value='$w[Jumlah]' size=20 maxlength=15></td></tr>
  <tr><td class=inp1>Dari Tanggal</td><td class=ul>$dari</td></tr>
  <tr><td class=inp1>Sampai Tanggal</td><td class=ul>$sampai</td></tr>
  <tr><td class=inp1>Keterangan</td><td class=ul><textarea name='Keterangan' cols=40 rows=6>$w[Keterangan]</textarea></td></tr>
  <tr><td colspan=2 class=ul><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=mhswbaru.cicilan'\"></td></tr>
  </form></table></p>";
}
function CclSav($cm) {
  $Urutan = $_REQUEST['Urutan']+0;
  $Judul = sqling($_REQUEST['Judul']);
  $Jumlah = $_REQUEST['Jumlah']+0;
  $dari = "$_REQUEST[dari_y]-$_REQUEST[dari_m]-$_REQUEST[dari_d]";
  $sampai = "$_REQUEST[sampai_y]-$_REQUEST[sampai_m]-$_REQUEST[sampai_d]";
  $ket = sqling($_REQUEST['Keterangan']);
  $md = $_REQUEST['md']+0;
  // simpan
  if ($md == 0) {
    $s = "update cicilanmhsw set Judul='$Judul', Urutan='$Urutan', Jumlah=$Jumlah,
    DariTanggal='$dari', SampaiTanggal='$sampai', Keterangan='$ket',
    LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
    where CicilanID='$_REQUEST[cclid]' ";
    $r = _query($s);
  }
  else {
    $s = "insert into cicilanmhsw (PMBID, Urutan, Judul, Jumlah,
      PMBMhswID, TahunID,
      DariTanggal, SampaiTanggal, Keterangan,
      LoginBuat, TanggalBuat)
      values ('$cm[PMBID]', '$Urutan', '$Judul', '$Jumlah',
      0, '0',
      '$dari', '$sampai', '$ket',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
  }
  DftrCicilan($cm);
}
function CclDel($cm) {
  echo Konfirmasi("Konfirmasi Penghapusan",
  "Benar Anda akan menghapus cicilan ini?<hr size=1 color=silver>
  Pilihan: <a href='?mnux=mhswbaru.cicilan&gos=CclDel1&pmbid=$cm[PMBID]&cclid=$_REQUEST[cclid]'>Hapus</a> |
  <a href='?mnux=mhswbaru.cicilan&pmbid=$cm[PMBID]'>Batal</a>");
}
function CclDel1($cm) {
  $s = "delete from cicilanmhsw where CicilanID='$_REQUEST[cclid]' ";
  $r = _query($s);
  DftrCicilan($cm);
}

// *** Parameters ***
$pmbid = GetSetVar('srcpmbid');
$pmbid = GetSetVar('pmbid');
$gos = (empty($_REQUEST['gos']))? 'DftrCicilan' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Setup Cicilan Calon Mahasiswa");
TampilkanPencarianCAMA('mhswbaru.cicilan');
if (empty($pmbid)) {}
else {
  if (!empty($pmbid)) {
    $cm = GetFields("pmb p
      left outer join statusawal sa on p.StatusAwalID=sa.StatusAwalID
      left outer join program prg on p.ProgramID=prg.ProgramID
      left outer join prodi prd on p.ProdiID=prd.ProdiID",
      "p.PMBID", $pmbid,
      "p.*, sa.Nama as STA, prg.Nama as PRG, prd.Nama as PRD");
    if (!empty($cm)) {
      TampilkanHeaderCicilan($cm);
      $gos($cm);
    }
    else echo ErrorMsg("Data Tidak Ditemukan",
      "Calon mahasiswa dengan PMB ID: <b>$pmbid</b> tidak ditemukan.");
  }
}
?>
