<?php
// Author: Emanuel Setio Dewo
// 03 March 2006

function frmOrtu() {
  global $datamhsw, $mnux, $pref;
  // Data ayah
  $AgamaAyah = GetOption2("agama", "concat(Agama, ' - ', Nama)", 'Agama', $datamhsw['AgamaAyah'], '', 'Agama');
  $PendidikanAyah = GetOption2('pendidikanortu', "concat(Pendidikan, ' - ', Nama)", 'Pendidikan', $datamhsw['PendidikanAyah'], '', 'Pendidikan');
  $PekerjaanAyah = GetOption2('pekerjaanortu', "concat(Pekerjaan, ' - ', Nama)", 'Pekerjaan', $datamhsw['PekerjaanAyah'], '', 'Pekerjaan');
  $HidupAyah = GetOption2('hidup', "concat(Hidup, ' - ', Nama)", 'Hidup', $datamhsw['HidupAyah'], '', 'Hidup');

  // Data ayah
  $AgamaIbu = GetOption2("agama", "concat(Agama, ' - ', Nama)", 'Agama', $datamhsw['AgamaIbu'], '', 'Agama');
  $PendidikanIbu = GetOption2('pendidikanortu', "concat(Pendidikan, ' - ', Nama)", 'Pendidikan', $datamhsw['PendidikanIbu'], '', 'Pendidikan');
  $PekerjaanIbu = GetOption2('pekerjaanortu', "concat(Pekerjaan, ' - ', Nama)", 'Pekerjaan', $datamhsw['PekerjaanIbu'], '', 'Pekerjaan');
  $HidupIbu = GetOption2('hidup', "concat(Hidup, ' - ', Nama)", 'Hidup', $datamhsw['HidupIbu'], '', 'Hidup');

  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='$_SESSION[$pref]'>
  <input type=hidden name='slnt' value='mhsw.edt.ortu'>
  <input type=hidden name='slntx' value='OrtuSav'>
  <input type=hidden name='mhswid' value='$datamhsw[MhswID]'>

  <tr><td colspan=2 class=ul><b>Data Ayah</b></td></tr>
  <tr><td class=ul>Nama</td><td class=ul><input type=text name='NamaAyah' value='$datamhsw[NamaAyah]' size=40 maxlength=50></td></tr>
  <tr><td class=ul>Agama</td><td class=ul><select name='AgamaAyah'>$AgamaAyah</select></td></tr>
  <tr><td class=ul>Pendidikan</td><td class=ul><select name='PendidikanAyah'>$PendidikanAyah</select></td></tr>
  <tr><td class=ul>Pekerjaan</td><td class=ul><select name='PekerjaanAyah'>$PekerjaanAyah</select></td></tr>
  <tr><td class=ul>Hidup</td><td class=ul><select name='HidupAyah'>$HidupAyah</select></td></tr>

  <tr><td colspan=2 class=ul><b>Data Ibu</b></td></tr>
  <tr><td class=ul>Nama</td><td class=ul><input type=text name='NamaIbu' value='$datamhsw[NamaIbu]' size=40 maxlength=50></td></tr>
  <tr><td class=ul>Agama</td><td class=ul><select name='AgamaIbu'>$AgamaIbu</select></td></tr>
  <tr><td class=ul>Pendidikan</td><td class=ul><select name='PendidikanIbu'>$PendidikanIbu</select></td></tr>
  <tr><td class=ul>Pekerjaan</td><td class=ul><select name='PekerjaanIbu'>$PekerjaanIbu</select></td></tr>
  <tr><td class=ul>Hidup</td><td class=ul><select name='HidupIbu'>$HidupIbu</select></td></tr>

  <tr><td colspan=2 class=ul><b>Alamat Orang Tua</b></td></tr>
  <tr><td class=ul>Alamat</td><td class=ul><input type=text name='AlamatOrtu' value='$datamhsw[AlamatOrtu]' size=100 maxlength=200></td></tr>
  <tr><td class=ul>RT</td><td class=ul><input type=text name='RTOrtu' value='$datamhsw[RTOrtu]' size=10 maxlength=5>
    RW <input type=text name='RWOrtu' value='$datamhsw[RWOrtu]' size=10 maxlength=5></td></tr>
  <tr><td class=ul>Kota</td><td class=ul><input type=text name='KotaOrtu' value='$datamhsw[KotaOrtu]' size=20 maxlength=50>
    Kode Pos <input type=text name='KodePosOrtu' value='$datamhsw[KodePosOrtu]' size=20 maxlength=50></td></tr>
  <tr><td class=ul>Propinsi</td><td class=ul><input type=text name='PropinsiOrtu' value='$datamhsw[PropinsiOrtu]' size=30 maxlength=50></td></tr>
  <tr><td class=ul>Negara</td><td class=ul><input type=text name='NegaraOrtu' value='$datamhsw[NegaraOrtu]' size=30 maxlength=50></td></tr>

  <tr><td colspan=2 class=ul><b>Kontak</b></td></tr>
  <tr><td class=ul>Telepon</td><td class=ul><input type=text name='TeleponOrtu' value='$datamhsw[TeleponOrtu]' size=30 maxlength=50></td></tr>
  <tr><td class=ul>Handphone</td><td class=ul><input type=text name='HandphoneOrtu' value='$datamhsw[HandphoneOrtu]' size=30 maxlength=50></td></tr>
  <tr><td class=ul>Email</td><td class=ul><input type=text name='EmailOrtu' value='$datamhsw[EmailOrtu]' size=30 maxlength=50></td></tr>

  <tr><td colspan=2 class=ul><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </table></p>";
}
function OrtuSav() {
  $NamaAyah = sqling($_REQUEST['NamaAyah']);
  $NamaIbu = sqling($_REQUEST['NamaIbu']);
  $AlamatOrtu = sqling($_REQUEST['AlamatOrtu']);
  $RTOrtu = sqling($_REQUEST['RTOrtu']);
  $RWOrtu = sqling($_REQUEST['RWOrtu']);
  $KotaOrtu = sqling($_REQUEST['KotaOrtu']);
  $PropinsiOrtu = sqling($_REQUEST['PropinsiOrtu']);
  $NegaraOrtu = sqling($_REQUEST['NegaraOrtu']);
  $TeleponOrtu = sqling($_REQUEST['TeleponOrtu']);
  $HandphoneOrtu = sqling($_REQUEST['HandphoneOrtu']);
  $EmailOrtu = sqling($_REQUEST['EmailOrtu']);
  // Simpan
  $s = "update mhsw set NamaAyah='$NamaAyah', AgamaAyah='$_REQUEST[AgamaAyah]',
    PendidikanAyah='$_REQUEST[PendidikanAyah]',
    PekerjaanAyah='$_REQUEST[PekerjaanAyah]',
    HidupAyah='$_REQUEST[HidupAyah]',
    NamaIbu='$_REQUEST[NamaIbu]',
    AgamaIbu='$_REQUEST[AgamaIbu]',
    PendidikanIbu='$_REQUEST[PendidikanIbu]',
    PekerjaanIbu='$_REQUEST[PekerjaanIbu]',
    HidupIbu='$_REQUEST[HidupIbu]',
    AlamatOrtu='$_REQUEST[AlamatOrtu]',
    RTOrtu='$_REQUEST[RTOrtu]',
    RWOrtu='$_REQUEST[RWOrtu]',
    KotaOrtu='$_REQUEST[KotaOrtu]',
    KodePosOrtu='$_REQUEST[KodePosOrtu]',
    PropinsiOrtu='$_REQUEST[PropinsiOrtu]',
    NegaraOrtu='$_REQUEST[NegaraOrtu]',
    TeleponOrtu='$_REQUEST[TeleponOrtu]',
    HandphoneOrtu='$_REQUEST[HandphoneOrtu]',
    EmailOrtu='$_REQUEST[EmailOrtu]'
    where MhswID='$_REQUEST[mhswid]' ";
  $r = _query($s);
}

?>
