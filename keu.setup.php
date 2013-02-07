<?php
// Author: Emanuel Setio Dewo
// www.sisfokampus.net
// 17 July 2006

// Setup akun2 penting keuangan Mhsw

// *** Functions ***
function TampilkanAkun() {
  $w = GetFields('keusetup', "NA", 'N', '*');
  $hp = GetOption2('bipotnama', "Nama", 'Nama', $w['HutangPrev'], '', 'BIPOTNamaID');
  $dp = GetOption2('bipotnama', "Nama", 'Nama', $w['DepositPrev'], '', 'BIPOTNamaID');
  $hn = GetOption2('bipotnama', 'Nama', 'Nama', $w['HutangNext'], '', 'BIPOTNamaID');
  $dn = GetOption2('bipotnama', 'Nama', 'Nama', $w['DepositNext'], '', 'BIPOTNamaID');
  $d1 = GetOption2('bipotnama', 'Nama', 'Nama', $w['Denda1'], '', 'BIPOTNamaID');
  $d2 = GetOption2('bipotnama', 'Nama', 'Nama', $w['Denda2'], '', 'BIPOTNamaID');
  CheckFormScript('HutangPrev,HutangNext,DepositPrev,DepositNext,Denda1,Denda2');
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='keu.setup'>
  <input type=hidden name='gos' value='KeuSav'>
  <tr><td class=inp>Transfer Hutang ke Smt Depan</td>
    <td class=ul><select name='HutangNext'>$hn</select></td>
    <td class=inp>Hutang Smt Lalu</td>
    <td class=ul><select name='HutangPrev'>$hp</select></td>
    </tr>
  <tr><td class=inp>Transfer Deposit ke Smt Depan</td>
    <td class=ul><select name='DepositNext'>$dn</select></td>
    <td class=inp>Deposit dari Smt Lalu</td>
    <td class=ul><select name='DepositPrev'>$dp</select></td>
    </tr>
  <tr><td class=inp>Denda Terlambat Bayar</td>
    <td class=ul><select name='Denda1'>$d1</select></td>
    <td class=inp>Denda Akhir Smt</td>
    <td class=ul><select name='Denda2'>$d2</select></td>
    </tr>
  <tr><td class=ul colspan=4><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset'></td></tr>
  </form></table></p>";
}
function KeuSav() {
  $HutangPrev = $_REQUEST['HutangPrev'];
  $HutangNext = $_REQUEST['HutangNext'];
  $DepositPrev = $_REQUEST['DepositPrev'];
  $DepositNext = $_REQUEST['DepositNext'];
  $Denda1 = $_REQUEST['Denda1'];
  $Denda2 = $_REQUEST['Denda2'];
  $s = "update keusetup set HutangPrev=$HutangPrev, HutangNext=$HutangNext,
    DepositPrev=$DepositPrev, DepositNext=$DepositNext,
    Denda1=$Denda1, Denda2=$Denda2";
  $r = _query($s);
  echo "<script>window.location='?mnux=keu.setup';</script>";
}

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "TampilkanAkun" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Account Keuangan Mhsw");
$gos();
?>
