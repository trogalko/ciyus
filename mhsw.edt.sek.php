<?php
// Author: Emanuel Setio Dewo
// 28 Februari 2006

function CariSekolahScript() {
  echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function carisekolah(frm){
    lnk = "carisekolah.php?SekolahID="+frm.AsalSekolah.value+"&Cari="+frm.NamaSekolah.value;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  -->
  </script>
EOF;
}
function frmSekolah() {
  global $datamhsw, $mnux, $pref;
  CariSekolahScript();
  $NamaSekolah = GetaField('asalsekolah', 'SekolahID', $datamhsw['AsalSekolah'], "concat(Nama, ', ', Kota)");
  $optjur = GetOption2('jurusansekolah', "concat(JurusanSekolahID, ' - ', Nama, ' - ', NamaJurusan)", 'JurusanSekolahID', $datamhsw['JurusanSekolah'], '', 'JurusanSekolahID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='data' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='$_SESSION[$pref]'>
  <input type=hidden name='slnt' value='mhsw.edt.sek'>
  <input type=hidden name='slntx' value='SekolahSav'>
  <input type=hidden name='mhswid' value='$datamhsw[MhswID]'>

  <tr><td colspan=2 class=ul><b>Sekolah Menengah Atas Mahasiswa</td></tr>

  <tr><td class=ul rowspan=2>Sekolah Asal</td><td class=ul><input type=text name='AsalSekolah' value='$datamhsw[AsalSekolah]' size=10 maxlength=50></td></tr>
    <tr><td class=ul><input type=text name='NamaSekolah' value='$NamaSekolah' size=50 maxlength=50> <a href='javascript:carisekolah(data)'>Cari</a></td></tr>
  <tr><td class=ul>Jenis Sekolah</td><td class=ul><b>$datamhsw[JenisSekolahID]</b></td></tr>
  <tr><td class=ul>Jurusan</td><td class=ul><select name='JurusanSekolah'>$optjur</select></td></tr>
  <tr><td class=ul>Tahun Lulus</td><td class=ul><input type=text name='TahunLulus' value='$datamhsw[TahunLulus]' size=10 maxlength=5></td></tr>
  <tr><td class=ul>Nilai Sekolah</td><td class=ul><input type=text name='NilaiSekolah' value='$datamhsw[NilaiSekolah]' size=5 maxlength=5></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form></table></p>";
}
function SekolahSav() {
  $AsalSekolah = $_REQUEST['AsalSekolah'];
  $JurusanSekolah = $_REQUEST['JurusanSekolah'];
  $TahunLulus = $_REQUEST['TahunLulus'];
  $NilaiSekolah = $_REQUEST['NilaiSekolah'];
  $JenisSekolahID = GetaField('asalsekolah', 'SekolahID', $AsalSekolah, 'JenisSekolahID');
  // Simpan
  $s = "update mhsw set AsalSekolah='$AsalSekolah', JenisSekolahID='$JenisSekolahID',
    JurusanSekolah='$JurusanSekolah',
    TahunLulus='$TahunLulus', NilaiSekolah='$NilaiSekolah'
    where MhswID='$_REQUEST[mhswid]' ";
  $r = _query($s);
}

?>
