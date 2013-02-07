<?php
// Author: Emanuel Setio Dewo
// 06 Feb 2006

// *** Functions ***
function DftrLapPMB() {
  global $_arrpmblap;
  $n=0;
  echo "<table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th><th class=ttl>Jenis Laporan</th></tr>";
  for ($i=0; $i<sizeof($_arrpmblap); $i++) {
    $n++;
    $lap = explode('->', $_arrpmblap[$i]);
    echo "<tr><td class=inp1>$n</td>
    <td class=ul><a href='?mnux=pmb.cetak&gos=$lap[1]'>$lap[0]</a>
    </td></tr>";
  }
  echo "</table>";
}
function DHU() {
  TampilkanDaftarRuang('Cetak Daftar Hadir Ujian', "pmb.cetak.dhu");
}
function LabelUjian() {
  TampilkanDaftarRuang('Cetak Label Meja Ujian', "pmb.cetak.label");
}
function TampilkanDaftarRuang($jdl='', $lnk='', $kmbl='pmb.cetak') {
  global $pmbaktif;
  $s = "select count(PMBID) as JML, r.RuangID
    from pmb p
    right outer join ruang r on p.RuangID=r.RuangID and r.UntukUSM='Y'
    where p.PMBPeriodID='$pmbaktif'
    group by r.RuangID";
  $r = _query($s);
  $n = 0; $snm = session_name(); $sid = session_id();
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><td class=ul colspan=4><b>$jdl</b></td></tr>
    <tr><td class=ul colspan=4>Pilihan: <a href='?mnux=$kmbl&$snm=$sid'>Kembali</a></td>
    <tr><th class=ttl>#</th>
    <th class=ttl>Ruang</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Cetak</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr>
    <td class=inp1>$n</td>
    <td class=ul>$w[RuangID]</td>
    <td class=ul align=right>$w[JML]</td>
    <td class=ul align=center><a href='$lnk.php?rid=$w[RuangID]&jml=$w[JML]&pmbaktif=$pmbaktif&$snm=$sid'>Cetak</a></td>
    </tr>";
  }
  echo "</table></p>";
}

// *** Parameters ***
$prodi = GetSetVar('prodi');
$pmbaktif = GetaField('pmbperiod', 'NA', 'N', 'PMBPeriodID');
$pmbaktif = GetSetVar('pmbaktif', $pmbaktif);
$pmbfid = GetSetVar('pmbfid');
$_arrpmblap = array(
  "Daftar Hadir Ujian->DHU",
  "Label Meja Ujian->LabelUjian"
  );
$gos = (empty($_REQUEST['gos']))? 'DftrLapPMB' : $_REQUEST['gos'];


// *** Main ***
TampilkanJudul("Cetakan PMB - $pmbaktif");
$gos();
?>