<?php
// Author: Emanuel Setio Dewo
// 28 Februari 2006

function frmAlamat() {
  global $datamhsw, $mnux, $pref;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='$_SESSION[$pref]'>
  <input type=hidden name='slnt' value='mhsw.edt.almt'>
  <input type=hidden name='slntx' value='AlamatSav'>
  <input type=hidden name='mhswid' value='$datamhsw[MhswID]'>

  <tr><td colspan=2 class=ul><b>Alamat menetap di Jakarta bagi Mahasiwa dari luar Jakarta</td></tr>
  <tr><td class=ul>Alamat</td><td class=ul><input type=text name='AlamatAsal' value='$datamhsw[AlamatAsal]' size=100 maxlength=200></td></tr>
  <tr><td class=ul>RT</td><td class=ul><input type=text name='RTAsal' value='$datamhsw[RTAsal]' size=10 maxlength=5>
    RW <input type=text name='RWAsal' value='$datamhsw[RWAsal]' size=10 maxlength=5></td></tr>
  <tr><td class=ul>Kota</td><td class=ul><input type=text name='KotaAsal' value='$datamhsw[KotaAsal]' size=20 maxlength=50>
    Kode Pos <input type=text name='KodePosAsal' value='$datamhsw[KodePosAsal]' size=10 maxlength=20></td></tr>
  <tr><td class=ul>Propinsi</td><td class=ul><input type=text name='PropinsiAsal' value='$datamhsw[PropinsiAsal]' size=30 maxlength=50></td></tr>
  <tr><td class=ul>Negara</td><td class=ul><input type=text name='NegaraAsal' value='$datamhsw[NegaraAsal]' size=30 maxlength=50></td></tr>
  <tr><td class=ul>Telepon</td><td class=ul><input type=text name='TeleponAsal' value='$datamhsw[TeleponAsal]' size=30 maxlength=50></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form></table></p>";
}
function AlamatSav() {
  $AlamatAsal = sqling($_REQUEST['AlamatAsal']);
  $RTAsal = sqling($_REQUEST['RTAsal']);
  $RWAsal = sqling($_REQUEST['RWAsal']);
  $KotaAsal = sqling($_REQUEST['KotaAsal']);
  $KodePosAsal = sqling($_REQUEST['KodePosAsal']);
  $PropinsiAsal = sqling($_REQUEST['PropinsiAsal']);
  $NegaraAsal = sqling($_REQUEST['NegaraAsal']);
  $TeleponAsal = sqling($_REQUEST['TeleponAsal']);
  // Simpan
  $s = "update mhsw set AlamatAsal='$AlamatAsal',
    RTAsal='$RTAsal', RWAsal='$RWAsal',
    KotaAsal='$KotaAsal', KodePosAsal='$KodePosAsal',
    PropinsiAsal='$PropinsiAsal', NegaraAsal='$NegaraAsal', TeleponAsal='$TeleponAsal'
    where MhswID='$_REQUEST[mhswid]' ";
  $r = _query($s);

}

?>
