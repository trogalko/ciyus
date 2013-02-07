<?php
// Author: Emanuel Setio Dewo
// March 2006
// www.sisfokampus.net

function TampilkanHeaderHonorDosen($mnux='dosen.honor') {
  $TM = GetDateOption($_SESSION['TglMulai'], 'TglMulai');
  $TS = GetDateOption($_SESSION['TglSelesai'], 'TglSelesai');
  $optmgg = GetOption2('minggu', "concat(MingguID, ' - ', Nama)", 'MingguID', $_SESSION['PeriodeMinggu'], '', 'MingguID');
  $optbln = GetMonthOption($_SESSION['PeriodeBulan']);
  $optthn = GetNumberOption(date('Y')-1, date('Y')+1, $_SESSION['PeriodeTahun']);
  $_ProdiID = trim($_SESSION['_ProdiID'], ',');
    //echo $_ProdiID;
    $arrProdi = explode(',', $_ProdiID);
    $_prodi = '';
    for ($i = 0; $i<sizeof($arrProdi); $i++) $_prodi .= ",'".$arrProdi[$i]."'";
    $_prodi = trim($_prodi, ',');
    $_prodi = (empty($arrProdi))? '-1' : $_prodi; //implode(', ', $arrProdi);
  $optprd = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], "ProdiID in ($_prodi)", 'ProdiID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=GET>
  <input type=hidden name='mnux' value='$mnux'>
  <tr><td class=inp>Tahun Akademik</td>
    <td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10>
    Prodi Jdwl: <select name='prodi' onChange='this.form.submit()'>$optprd</select></td>
    <td class=inp>Pilihan</td><td class=ul><input type=submit name='Tampilkan' value='Tampilkan'></td></tr>
  <tr><td class=inp>Dari Tgl</td><td class=ul>$TM</td>
    <td class=inp>Sampai Tgl</td><td class=ul>$TS</td></tr>
  <tr><td class=inp>Honor Periode</td><td class=ul>
    <select name='PeriodeMinggu' onChange='this.form.submit()'>$optmgg</select>
    <select name='PeriodeBulan' onChange='this.form.submit()'>$optbln</select>
    <select name='PeriodeTahun' onChange='this.form.submit()'>$optthn</select></td>
    <td class=inp>Proses</td><td class=ul>
    <input type=button name='ProsesHonor' value='Proses Honor Dosen' onClick=\"location='?mnux=dosen.honor&gos=HonDosPros'\">
    <input type=button name='CetakDaftar' value='Cetak Daftar' onClick=\"location='dosen.honor.daftar.php'\">
    <input type=button name='CetakPerBank' value='Cetak per Bank' onClick=\"location='dosen.honor.perbank.php'\">
    </td></tr>
  </form></table></p>";
}
?>
