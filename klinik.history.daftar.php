<?php
// Author: Emanuel Setio Dewo
// 23 November 2006

// *** Parameters ***
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');

// *** Main ***
TampilkanJudul("Daftar History Matakuliah Mahasiswa");
TampilkanHeaderDaftarHistoryKlinik();

// *** Functions ***
function TampilkanHeaderDaftarHistoryKlinik() {
  CheckFormScript('DariNPM,SampaiNPM');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='klinik.history.daftar.excel.php' method=POST onSubmit=\"return CheckForm(this)\">

  <tr><td class=wrn rowspan=2>$_SESSION[KodeID]</td>
    <td class=inp>Dari NPM</td><td class=ul colspan=2><input type=text name='DariNPM' value='$_SESSION[DariNPM]' size=20 maxlength=50></td></tr>
  <tr><td class=inp>Sampain NPM</td><td class=ul><input type=text name='SampaiNPM' value='$_SESSION[SampaiNPM]' size=20 maxlength=50></td>
    <td class=ul><input type=submit name='Donlot' value='Download Excel'></td></tr>
  </form></table></p>";
}
?>
