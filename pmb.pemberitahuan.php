<?php
// Author: Emanuel Setio Dewo
// 2006-01-12

// *** Functions ***
function FilterPemberitahuanUSM() {
  $_stawl = GetOption2("statusawal", "Nama", "Nama", $_SESSION['stawl'], '', 'StatusAwalID');
  $_prodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], '', 'ProdiID');
  $_jnsek = GetOption2('jenissekolah', "Nama", "Nama", $_SESSION['jnsek'], '', 'JenisSekolahID');
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='pmb.pemberitahuan'>
  <tr><th class=ttl colspan=2>Filter Daftar</th></tr>
  <tr><td class=inp1>Periode PMB</td><td class=ul><input type=text name='pmbaktif' value='$_SESSION[pmbaktif]' size=10 maxlength=10></td></tr>
  <tr><td class=inp1>Status Calon</td><td class=ul><select name='stawl'>$_stawl</select></td></tr>
  <tr><td class=inp1>Program Studi</td><td class=ul><select name='prodi'>$_prodi</select></td></tr>
  <tr><td class=inp1>Jenis Sekolah</td><td class=ul><select name='jnsek'>$_jnsek</select></td></tr>
  <tr><td colspan=2><input type=submit name='Filter' value='Filter'></td></td>
  </form></table></p>";
}

function DftrPMB() {
  FilterPemberitahuanUSM();
  if (!empty($_SESSION['prodi'])) TampilkanDftrPMB();
}
function TampilkanDftrPMB() {
  $whr = '';
  $whr .= (empty($_SESSION['stawl']))? '' : " and p.StatusAwalID='$_SESSION[stawl]' ";
  $whr .= (empty($_SESSION['jnsek']))? '' : " and p.JenisSekolahID='$_SESSION[jnsek]' ";
  $s = "select p.PMBID, p.Nama, p.JenisSekolahID, p.LulusUjian, NomerSurat,
    sa.Nama as STAWL, bpt.Nama as BPT
    from pmb p
      left outer join statusawal sa on p.StatusAwalID=sa.StatusAwalID
      left outer join bipot bpt on p.BIPOTID=bpt.BIPOTID
    where p.ProdiID='$_SESSION[prodi]' and PMBPeriodID='$_SESSION[pmbaktif]' $whr
    order by p.NilaiUjian DESC, p.PMBID ASC, p.Nama ASC";
  $r = _query($s);
  $n = 0;
  echo "<p><b>Catatan:</b> Perhatikan Master Biaya & Potongan (BIPOT) calon mahasiswa. Jika masih kosong, maka harus diset terlebih dahulu oleh Kepala Admisi.</p>";
  echo "<form action='pmb.pemberitahuan1.php' method=POST target=_blank>
    <input type=hidden name='pmbaktif' value='$_SESSION[pmbaktif]'>
    <input type=hidden name='prn' value='$prn'>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>No.</th>
    <th class=ttl>No. PMB</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Status</th>
    <th class=ttl>Sekolah</th>
    <th class=ttl>Lulus?</th>
    <th class=ttl>BIPOT</th>
    <th class=ttl><input type=submit name='Cetak' value='Cetak'></th>
    <th class=ttl>Cetak<br />Individual</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['LulusUjian'] == 'Y')? 'class=ul' : 'class=nac';
	$chk = ($w['LulusUjian'] == 'Y')? "checked" : '';
    echo "<tr><td class=inp1>$n</td>
      <td $c>$w[PMBID]</td>
      <td $c>$w[Nama]</td>
      <td $c>$w[STAWL]</td>
      <td $c>$w[JenisSekolahID]&nbsp;</td>
      <td $c align=center><img src='img/$w[LulusUjian].gif'></td>
      <td $c>$w[BPT]</td>
      <td $c align=center><input type=checkbox name='pmbid[]' value='$w[PMBID]' $chk></td>
      <td $c align=center><a href='pmb.pemberitahuan1.php?Lulus=$w[LulusUjian]&pmbid[]=$w[PMBID]&pmbaktif=$_SESSION[pmbaktif]&Sekolah=$w[JenisSekolahID]' target=_blank>Cetak</a></td>
      </tr>";
  }
  echo "<tr><td colspan=7>&nbsp;</td>
    <td class=ul><input type=submit name='Cetak' value='Cetak'></td></tr>
    </table></p>";
  echo "</form>";
}

// *** Parameters ***
$pmbaktif = GetSetVar('pmbaktif');
$_stawl = GetSetVar('stawl');
$_prodi = GetSetVar('prodi');
$_jnsek = GetSetVar('jnsek');
$gos = (empty($_REQUEST['gos']))? 'DftrPMB' : $_REQUEST['gos'];

// *** Main ***
$NamaTPMB = NamaTahunPMB($_SESSION['pmbaktif']);
TampilkanJudul("Pemberitahuan Lulus USM - $NamaTPMB");
$gos();
?>
