<?php
// Author : Emanuel Setio Dewo
// 18 April 2006

// *** Functions ***
function ProsesIPK() {
  $mhsw = GetFields('mhsw', "MhswID", $_SESSION['crmhswid'], "*");
  if (empty($mhsw)) echo ErrorMsg("Data Tidak Ditemukan",
    "Mahasiswa dengan NPM: <b>$_SESSION[crmhswid]</b> tidak ditemukan.");
  else ProsesIPK1($mhsw);
}
function ProsesIPK1($mhsw) {
  include_once "mhsw.hdr.php";
  TampilkanHeaderKecil($mhsw, "prc.ipk");
  $s = "select krs.KRSID, krs.KHSID, krs.MhswID, krs.GradeNilai, krs.BobotNilai,
    j.MKKode, j.Nama, j.SKS
    from krs krs
      left outer join jadwal j on krs.JadwalID=j.JadwalID
    where krs.MhswID='$_SESSION[crmhswid]'
      and j.JenisJadwalID='K'
    order by j.MKKode asc, krs.BobotNilai desc";
  $r = _query($s); $n = 0;
  $_sks = 0;
  $_bbt = 0;
  $_nxk = 0;
  $mk = '';
  echo "<p><table class=bsc>
    <tr><th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Grade</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Bobot</th>
    <th class=ttl>N x K</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    if ($mk != $w['MKKode']) {
      $mk = $w['MKKode'];
      $c = "class=ul";
      $nxk = $w['SKS'] * $w['BobotNilai'];
      $_nxk += $nxk;
      $_sks += $w['SKS'];
    }
    else {
      $c = "class=nac";
      $nxk = x;
    }
    $n++;
    echo "<tr>
    <td class=inp>$n</td>
    <td $c>$w[MKKode]</td>
    <td $c>$w[Nama] $w[NamaKelas]</td>
    <td $c>$w[GradeNilai]</td>
    <td $c align=right>$w[SKS]</td>
    <td $c align=right>$w[BobotNilai]</td>
    <td $c align=right>$nxk</td>
    </tr>";
  }
  
  echo "<tr><td class=ul colspan=4 align=right>Total :</td>
    <td class=ul align=right><b>$_sks</b></td>
    <td class=ul align=right>Total :</td>
    <td class=ul align=right><b>$_nxk</b></td>
    </table></p>";
  $_ipk = ($_sks == 0) ? 0 : $_nxk/$_sks;
  $ipk = number_format($_ipk, 2);
  // update data mhsw
  $sm = "update mhsw set IPK='$_ipk', TotalSKS=$_sks where MhswID='$mhsw[MhswID]'";
  $rm = _query($sm);
  echo "<p>Total SKS: <b>$_sks</b> SKS, IPK: <b>$ipk</b> (Updated)</p>";
}
function TampilkanIPKBatch() {
  $optprg = GetOption2("program", "concat(ProgramID, ' - ', Nama)", "ProgramID", $_SESSION['prid'], '', 'ProgramID');
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  $keu = GetFields("keusetup", "NA", "N", '*');
  $optHTGN = GetOption2('bipotnama', "Nama", "Nama", $keu['HutangNext'], '', "BIPOTNamaID");
  $optDEPN = GetOption2('bipotnama', "Nama", "Nama", $keu['DepositNext'], '', "BIPOTNamaID");
  $optHTGP = GetOption2('bipotnama', "Nama", "Nama", $keu['HutangPrev'], '', "BIPOTNamaID");
  $optDEPP = GetOption2('bipotnama', "Nama", "Nama", $keu['DepositPrev'], '', "BIPOTNamaID");
  echo <<<END
  <p><table class=box cellspacing=1 cellpadding=4>
  <form action='prc.ipk.batch.php' name='Batch' method=POST target=_blank>
  <tr><td class=inp1 colspan=2><b>Proses IPK secara Batch</b></td>
    <td class=inp1 colspan=2><b>Setup Proses Keuangan</b></td>
    <td class=inp>Tahun Akd.</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]'></td></tr>
  <tr><td class=inp>Program</td><td class=ul><select name='prid'>$optprg</select></td>
    <td class=inp>Transfer Hutang</td><td class=ul><select name='HutangNext'>$optHTGN</select></td>
    <td class=inp>Hutang Ditransfer</td><td class=ul><select name='HutangPrev'>$optHTGP</select></td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul><select name='prodi'>$optprd</select></td>
    <td class=inp>Transfer Deposit</td><td class=ul><select name='DepositNext'>$optDEPN</select></td>
    <td class=inp>Deposit Ditransfer </td><td class=ul><select name='DepositPrev'>$optDEPP</select></td></tr>
  <tr><td colspan=2><input type=submit name='Proses' value='ProsesIPK'></td>
    <td colspan=4><input type=submit name='Proses' value='Simpan_Setup_Keuangan'>
    <input type=submit name='Proses' value='Proses_Tutup_Keuangan'></td></tr>
  </form></table></p>
END;
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prid = GetSetVar('prid');
$prodi = GetSetVar('prodi');
$crmhswid = GetSetVar('crmhswid');
$gos = (empty($_REQUEST['gos']))? "donothing" : "ProsesIPK";

// *** Main ***
TampilkanJudul("Proses Hitung IPK Mahasiswa");
TampilkanPencarianMhsw('prc.ipk', 'ProsesIPKMhsw', 1);
TampilkanIPKBatch();
if (!empty($tahun) && !empty($prid) && !empty($prodi)) {
  $gos();
}
?>
