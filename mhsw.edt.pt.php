<?php
// Author: Emanuel Setio Dewo
// 15 March 2006

function CariPTScript() {
  echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function caript(frm){
    lnk = "cariperguruantinggi.php?PerguruanTinggiID="+frm.AsalPT.value+"&Cari="+frm.NamaPT.value;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  -->
  </script>
EOF;
}
function frmPT() {
  global $datamhsw, $mnux, $pref;
  CariPTScript();
  $NamaPT = GetaField('perguruantinggi', 'PerguruanTinggiID', $datamhsw['AsalPT'], "concat(Nama, ', ', Kota)");
  $lulus = ($datamhsw['LulusAsalPT'] == 'Y')? 'checked' : '';
  $TglLulusAsalPT = GetDateOption($datamhsw['TglLulusAsalPT'], 'TL');
  //$optjur = GetOption2('jurusansekolah', "concat(JurusanSekolahID, ' - ', Nama, ' - ', NamaJurusan)", 'JurusanSekolahID', $datamhsw['JurusanSekolah'], '', 'JurusanSekolahID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='data' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='$_SESSION[$pref]'>
  <input type=hidden name='slnt' value='mhsw.edt.pt'>
  <input type=hidden name='slntx' value='PTSav'>
  <input type=hidden name='mhswid' value='$datamhsw[MhswID]'>

  <tr><td colspan=2 class=ul><b>Perguruan Tinggi Asal Mahasiswa</td></tr>

  <tr><td class=ul rowspan=2>Perguruan Tinggi</td><td class=ul><input type=text name='AsalPT' value='$datamhsw[AsalPT]' size=10 maxlength=50></td></tr>
    <tr><td class=ul><input type=text name='NamaPT' value='$NamaPT' size=50 maxlength=50> <a href='javascript:caript(data)'>Cari</a></td></tr>
  <tr><td class=ul>Jurusan</td><td class=ul><select name='JurusanSekolah'>$optjur</select></td></tr>
  <tr><td class=ul>Lulus?</td><td class=ul><input type=checkbox name='LulusAsalPT' value='Y' $lulus>,
    Lulus tahun: $TglLulusAsalPT</td></tr>
  <tr><td class=ul>Nilai IPK</td><td class=ul><input type=text name='IPKAsalPT' value='$datamhsw[IPKAsalPT]' size=5 maxlength=5></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form></table></p>";
}
function PTSav() {
  $AsalPT = $_REQUEST['AsalPT'];
  $LulusAsalPT = (empty($_REQUEST['LulusAsalPT']))? 'N' : $_REQUEST['LulusAsalPT'];
  $TglLulusAsalPT = "$_REQUEST[TL_y]-$_REQUEST[TL_m]-$_REQUEST[TL_d]";
  echo $TglLulusAsalPT;
  $IPKAsalPT = $_REQUEST['IPKAsalPT'];
  $s = "update mhsw set AsalPT='$AsalPT', LulusAsalPT='$LulusAsalPT', 
    TglLulusAsalPT='$TglLulusAsalPT', IPKAsalPT='$IPKAsalPT'
    where MhswID='$_REQUEST[mhswid]' ";
  $r = _query($s);
}
?>