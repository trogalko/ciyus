<?php
// Author: Emanuel Setio Dewo
// 17 Feb 2006

// *** Functions ***
function DftrCAMA() {
  $minDigit = 7;
  if (!empty($_SESSION['srcpmbid'])) {
    if (strlen($_SESSION['srcpmbid']) >= $minDigit) DftrCAMA1();
    else echo ErrorMsg("Minimal Digit Pencarian",
      "Masukkan minimal <b>$minDigit</b> digit Nomer PMB supaya proses pencarian
      dapat cepat dan hasil yang ditampilkan lebih sedikit.");
  }
}
function DftrCAMA1() {
  $s = "select p.PMBID, p.NIM, p.Nama, p.ProdiID, p.Pilihan1, p.Pilihan2,
    concat(prd.ProdiID, ' - ', prd.Nama) as PRD, prg.Nama as PRG,
    p.StatusAwalID, sa.Nama as STT
    from pmb p
    left outer join prodi prd on p.ProdiID=prd.ProdiID
    left outer join program prg on p.ProgramID=prg.ProgramID
    left outer join statusawal sa on p.StatusAwalID=sa.StatusAwalID
    where p.KodeID='$_SESSION[KodeID]' and
      p.PMBID like '$_SESSION[srcpmbid]%'
    order by p.StatusAwalID, p.PMBID";
  $r = _query($s);
  $stt = '';
  $hdr = "<tr><th class=ttl colspan=2># PMB</th>
    <th class=ttl>NIM</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Program</th>
    <th class=ttl>Program Studi</th>
    <th class=ttl>Cetak Surat</th>
    </tr>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  while ($w = _fetch_array($r)) {
    // Grouping berdasarkan StatusAwalID
    if ($stt != $w['StatusAwalID']) {
      $stt = $w['StatusAwalID'];
      echo "<tr><td class=inp1 colspan=10><b>$w[STT]</td></tr>";
      echo $hdr;
    }
    // cek apakah sudah memiliki NIM?
    if (empty($w['NIM'])) {
      $c = 'class=ul';
      $_lnk = "<a href='?mnux=pmbpindahprodi&gos=CAMAEdt&pmbid=$w[PMBID]' title='Pindah Program Studi'><img src='img/edit.png'></a>";
      $_srt = "<a href='pmb.pemberitahuan1.php?pmbid=$w[PMBID]'><img src='img/printer.gif'></a>";
    }
    else {
      $c = 'class=nac';
      $_lnk = "&nbsp";
      $_srt = "&nbsp";
    }
    echo "<tr>
    <td $c>$_lnk</td>
    <td $c>$w[PMBID]</td>
    <td $c>$w[NIM]&nbsp;</td>
    <td $c>$w[Nama]&nbsp;</td>
    <td $c>$w[PRG]&nbsp;</td>
    <td $c>$w[PRD]&nbsp;</td>
    <td $c  align=center>$_srt</td>
    </tr>";
  }
  echo "</table></p>";
}
function CAMAEdt() {
  $w = GetFields('pmb', 'PMBID', $_REQUEST['pmbid'], '*');
  if (!empty($w)) {
    // Tampilkan
    $optprd = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $w['ProdiID'], '', 'ProdiID');
    $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $w['ProgramID'], '', 'ProgramID');
    $snm = session_name(); $sid = session_id();
    echo "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='pmbpindahprodi'>
    <input type=hidden name='gos' value='CAMASav'>
    <input type=hidden name='pmbid' value='$w[PMBID]'>
  
    <tr><th class=ttl colspan=2>Pindah Program Studi</th></tr>
    <tr><td class=inp1># PMB</td><td class=ul><b>$w[PMBID]</td></tr>
    <tr><td class=inp1>Nama Calon Mahasiswa</td><td class=ul><b>$w[Nama]</td></tr>
    <tr><td class=inp1>Asal Sekolah</td><td class=ul><b>$w[JenisSekolahID]</td></tr>
    <tr><td class=inp1>Program</td><td class=ul><select name='prid'>$optprg</select></td></tr>
    <tr><td class=inp1>Program Studi</td><td class=ul><select name='prodi'>$optprd</select></td></tr>
    <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
      <input type=submit name='Reset' value='Reset'>
      <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=pmbpindahprodi&gos=&$snm=$sid'\"></td></tr>
    </form></table></p>";
  }
  else echo ErrorMsg("Data Tidak Ditemukan",
    "Data calon mahasiswa dengan # PMB: <b>$_REQUEST[pmbid]</b> tidak ditemukan.");
}
function CAMASav() {
  //$BIPOTID
  $s = "update pmb set ProgramID='$_REQUEST[prid]', ProdiID='$_REQUEST[prodi]'
    where PMBID='$_REQUEST[pmbid]' ";
  $r = _query($s);
  echo Konfirmasi("Berhasil Simpan", "Perubahan telah berhasil disimpan.<hr size=1>
    Pilihan: <a href='pmb.pemberitahuan1.php?pmbid=$_REQUEST[pmbid]'>Cetak Surat Pemberitahuan</a>");
  
  DftrCAMA();
}

// *** Parameters ***
$srcpmbid = GetSetVar('srcpmbid');
$gos = (empty($_REQUEST['gos']))? 'DftrCAMA' : $_REQUEST['gos'];


// *** Main ***
TampilkanJudul("Pindah Program Studi");
TampilkanPencarianCAMA('pmbpindahprodi');
$gos();
?>
