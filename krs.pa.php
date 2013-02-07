<?php
// Author: Emanuel Setio Dewo
// 15 March 2006
// www.sisfokampus.net

include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
?>

<HTML>
<script language="JavaScript">
<!--
window.resizeTo(800,600);
window.moveTo(0,0);
-->
</script>

<STYLE>
BODY {
  font-size: 10pt;
}
table {
  font-size: 8pt;
}
th {
  border-top: 1px solid black;
  border-bottom: 1px solid black;
}
</STYLE>
<BODY>

<?php
// Ambil Data
$khsid = $_REQUEST['khsid'];
$khs = GetFields('khs', 'KHSID', $khsid, '*');
$mhsw = GetFields("mhsw m
  left outer join program prg on m.ProgramID=prg.ProgramID
  left outer join prodi prd on m.ProdiID=prd.ProdiID", 
  'MhswID', $khs['MhswID'], 
  "m.*, prg.Nama as PRG, prd.Nama as PRD");
$tahun = GetFields('tahun', "KodeID='$khs[KodeID]' and TahunID='$khs[TahunID]' and ProdiID",
  $khs['ProdiID'], '*');
$PA = GetaField('dosen', 'Login', $mhsw['PenasehatAkademik'], "concat(Nama, ', ', Gelar)");
$ipslalu = ($khs['Sesi'] > 1)? GetaField('krs', "MhswID='$mhsw[MhswID]' and Sesi", $khs['Sesi']-1, "IP")+0 : '';
echo "<center><h3>Form Bimbingan Akademik</h3></center>";
echo "<table cellspacing=1 cellpadding=4>
  <tr><td><b>NPM</td><td>: $mhsw[MhswID]</td></tr>
  <tr><td><b>Mahasiswa</td><td>: $mhsw[Nama]</td></tr>
  <tr><td><b>Program</td><td>: $mhsw[PRG] ($mhsw[ProgramID])</td>
    <td><b>IP Semester Lalu</td><td>: $ipslalu</td></tr>
  <tr><td><b>Program Studi</td><td>: $mhsw[PRD] ($mhsw[ProdiID])</td>
    <td><b>Total SKS</td><td>: $mhsw[TotalSKS]</td></tr>
  <tr><td><b>Penasehat Akademik</td><td>: $PA</td>
    <td><b>IPK</td><td>: $mshw[IPK]</td></tr>
  </table>";

// Daftar Jadwal
$s = "select j.*, h.Nama as HR,
  time_format(j.JamMulai, '%H:%i') as JM,
  time_format(j.JamSelesai, '%H:%i') as JS
  from jadwal j
    left outer join hari h on j.HariID=h.HariID
  where j.KodeID='$khs[KodeID]'
    and j.TahunID='$khs[TahunID]'
    and INSTR(j.ProdiID, '.$mhsw[ProdiID].')>0
    and j.JadwalSer=0
  order by j.MKKode";
$r = _query($s);
$nomer = 0;
echo "<table cellspacing=0 cellpadding=4>";
echo "<tr><th>No.</th>
  <th>Kode</th>
  <th>Matakuliah</th>
  <th>Kelas</th>
  <th>SKS</th>
  <th>Mhsw</th>
  <th>Kaps</th>
  <th>Hari</th>
  <th>Jam</th>
  <th>Prasyarat</th>
  <th>Ambil?</th>
  </tr>";
while ($w = _fetch_array($r)) {
  $nomer++;
  $pra = GetArrayTable("select mk.MKKode as PRA 
      from mkpra
        left outer join mk on mkpra.PraID=mk.MKID
      where mkpra.MKID='$w[MKID]' ", 
      'PRA', 'PRA'); 
  echo "<tr>
    <td>$nomer</td>
    <td>$w[MKKode]</td>
    <td>$w[Nama]</td>
    <td>$w[NamaKelas]&nbsp;</td>
    <td align=right>$w[SKSAsli]</td>
    <td align=right>$w[JumlahMhsw]</td>
    <td align=right>$w[Kapasitas]</td>
    <td>$w[HR]</td>
    <td>$w[JM]-$w[JS]</td>
    <td>$pra</td>
    <td align=center>...</td>
    </tr>";
}
echo "<tr><th colspan=10 align=right>Jumlah :</th><th>...</th></table>";

include_once "disconnectdb.php";
?>
<SCRIPT LANGUAGE="javascript">
<!--window.print();-->
</SCRIPT>

</BODY>
</HTML>
