<?php
// Author: Emanuel Setio Dewo
// 12 Feb 2006

// *** Functions ***
function TampilkanFilterBIPOTMhsw() {
  global $arrID;
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], '', 'ProdiID');
  $optprid = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['prid'], '', 'ProgramID');
  $optstawal = GetOption2('statusawal', "concat(StatusAwalID, ' - ', Nama)", 'StatusAwalID', $_SESSION['stawal'], '', 'StatusAwalID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='mhswbipot'>
  <input type=hidden name='gos' value=''>
  <tr><td colspan=2 class=ul><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>Periode PMB</td><td class=ul><input type=text name='pmbperiod' value='$_SESSION[pmbperiod]' size=10 maxlength=10></td></tr>
  <tr><td class=inp1>Program</td><td class=ul><select name='prid'>$optprid</select></td></tr>
  <tr><td class=inp1>Program Studi</td><td class=ul><select name='prodi'>$optprodi</select></td></tr>
  <tr><td class=inp1>Status Awal</td><td class=ul><select name='stawal'>$optstawal</select></td></tr>
  <tr><td colspan=2><input type=submit name='Filter' value='Filter'></td></tr>
  </form></table></p>";
}
function BuatArrayBipot() {
  $s = "select *
    from bipot
    where ProgramID='$_SESSION[prid]' and ProdiID='$_SESSION[prodi]' and NA='N'
    order by Tahun desc, Nama";
  $r = _query($s);
  $arr = array();
  while ($w = _fetch_array($r)) {
    $arr[] = "$w[BIPOTID]->$w[Tahun]->$w[Nama]";
  }
  return $arr;
}
function PilihanBipotMhsw($arr, $id) {
  $ret = '<option></option>';
  for ($i=0; $i< sizeof($arr); $i++) {
    $arrdet = explode('->', $arr[$i]);
    $sel = ($arrdet[0] == $id)? 'selected' : '';
    $ret .= "<option value='$arrdet[0]' $sel>$arrdet[1] - $arrdet[2]</option>";
  }
  return "<select name='BIPOTID'>$ret</select>";
}
// Tampilkan daftar mahasiswa
function DftrMhswBIPOT() {
  $arrBipot = BuatArrayBipot();
  $whr = '';
  $whr .= (empty($_SESSION['stawal']))? '' : "and p.StatusAwalID='$_SESSION[stawal]' ";
  $s = "select p.*
    from pmb p
    where p.ProgramID='$_SESSION[prid]' and p.ProdiID='$_SESSION[prodi]'
      and PMBPeriodID='$_SESSION[pmbperiod]' $whr
    order by p.StatusAwalID, p.PMBID";
  $r = _query($s);

  $n = 0; $stawal = '';
  $hdr = "<tr><th class=ttl>#</th>
    <th class=ttl>PMBID</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Telepon</th>
    <th class=ttl>Sekolah</th>
    <th class=ttl>Nilai</th>
    <th class=ttl>Grade</th>
    <th class=ttl>Master Bipot</th>
    </tr>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  while ($w = _fetch_array($r)) {
    if ($stawal != $w['StatusAwalID']) {
      $stawal = $w['StatusAwalID'];
      $_stawal = GetaField('statusawal', 'StatusAwalID', $stawal, 'Nama');
      $n = 0;
      echo "<tr><td class=ul colspan=10><b>$_stawal</b></td></tr>";
      echo $hdr;
    }
    $optbipot = PilihanBipotMhsw($arrBipot, $w['BIPOTID']);
    $n++;
    $c = ($w['LulusUjian'] == 'Y')? 'class=ul' : 'class=nac';
    echo "<tr><td class=inp1>$n</td>
    <td $c>$w[PMBID]</td>
    <td $c>$w[Nama]</td>
    <td $c>$w[Telepon]&nbsp;</td>
    <td $c>$w[JenisSekolahID]&nbsp;</td>
    <td $c align=right>&nbsp;$w[NilaiUjian]</td>
    <td $c align=center>&nbsp;$w[GradeNilai]</td>
    
    <form action='mhswbipotset.php' method=POST target=_blank>
    <input type=hidden name='PMBID' value='$w[PMBID]'>
    <td $c>$optbipot <input type=submit name='Simpan' value='Simpan'></td>
    </form>
    </tr>";
  }
  echo "</table></p>";
}

// *** Parameters ***
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$pmbperiod = GetSetVar("pmbperiod");
$stawal = GetSetVar('stawal');
if (empty($pmbperiod)) {
  $pmbperiod = GetaField("pmbperiod", "NA", 'N', "PMBPeriodID");
  $_SESSION['pmbperiod'] = $pmbperiod;
}
$gos = (empty($_REQUEST['gos']))? 'DftrMhswBIPOT' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Master Biaya & Potongan Calon Mahasiswa");
TampilkanFilterBIPOTMhsw();
if (!empty($prodi) && !empty($prid)) $gos();
?>
