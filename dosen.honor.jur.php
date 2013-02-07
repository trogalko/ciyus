<?php
// Author: Emanuel Setio Dewo
// 06 May 2006
// www.sisfokampus.net

include_once "dosen.honor.lib.php";

// *** Functions ***
function DaftarHonorDosenProdi() {
  $prd = ($_SESSION['prodi'] == '99')? "and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0" : "and j.ProdiID='.$_SESSION[prodi].'";
  $s = "select d.Login, d.Nama, d.IkatanID, concat(d.Nama, ', ', d.Gelar) as DSN, d.GolonganID, d.KategoriID,
    sd.Nama as StatusDSN, prd.Nama as Homebase, ikt.Besar, format(ikt.Besar, 0) as IKT,
    concat(gol.GolonganID, '-', gol.KategoriID) as CekGol,
    format(hd.TunjanganSKS, 0) as TSKS, 
    format(hd.TunjanganTransport, 0) as TTrans,
    format(hd.TunjanganTetap, 0) as TTtp,
    format(hd.Tambahan, 0) as TTamb,
    format(hd.Potongan, 0) as Pot,
    hd.*, j.prodiID 
    from presensi prs 
      left outer join jadwal j on prs.JadwalID=j.JadwalID
      left outer join dosen d on prs.DosenID=d.Login
      left outer join statusdosen sd on d.StatusDosenID=sd.StatusDosenID
      left outer join prodi prd on d.Homebase=prd.ProdiID
      left outer join golongan gol on d.GolonganID=gol.GolonganID and d.KategoriID=gol.KategoriID and d.Homebase=gol.ProdiID
      left outer join ikatan ikt on d.IkatanID=ikt.IkatanID
      left outer join honordosen hd on d.Login=hd.DosenID and hd.prodiID='$_SESSION[prodi]'
    where sd.HonorMengajar='Y' and d.NA='N'
      and hd.Tahun='$_SESSION[PeriodeTahun]' 
      and hd.Bulan='$_SESSION[PeriodeBulan]'
      and hd.Minggu='$_SESSION[PeriodeMinggu]'
      and prs.TahunID='$_SESSION[tahun]' 
      $prd
    group by prs.DosenID";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='dosen.honor.cetak.php' method=POST target=_blank>
    <tr><th class=ttl># Hnr</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Nama</th>
    <th class=ttl>SKS<br />Hitung</th>
    <th class=ttl>Status</th>
    <th class=ttl>Golongan</th>
    <th class=ttl>Kat</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Trans</th>
    <th class=ttl>Tetap</th>
    <th class=ttl>Tambahan</th>
    <th class=ttl>Potongan</th>
    <th class=ttl>Pajak</th>
    <th class=ttl>SUB<br />TOTAL</th>
    <th class=ttl><input type=submit name='Cetak' value='Cetak'></th>
    </tr>";
  $Total = 0;
  while ($w = _fetch_array($r)) {
    $htg = GetaField("presensi p left outer join jadwal j on p.JadwalID=j.JadwalID", 
      "p.DosenID='$w[Login]' and j.TahunID='$_SESSION[tahun]' and p.HonorDosenID='$w[HonorDosenID]' and p.Hitung", 
      'Y', "count(PresensiID)")+0;
    $TSKS = number_format($w['TunjanganSKS']);
    $TTrans = number_format($w['TunjanganTransport']);
    $TTtp = number_format($w['TunjanganTetap']);
    $TTam = number_format($w['Tambahan']);
    $TPot = number_format($w['Potongan']);
    $subtotal = $w['TunjanganSKS'] + $w['TunjanganTransport'] + $w['TunjanganTetap']
      + $w['Tambahan'] - $w['Potongan'];
    $subtotal = $subtotal - ($subtotal * $w['Pajak']/100);
    $Total += $subtotal;
    $_sub = number_format($subtotal);
    echo "<tr><td class=inp>$w[HonorDosenID]</td>
    <td class=ul>$w[DosenID]</td>
    <td class=ul>$w[DSN]</td>
    <td class=ul align=right>$htg</td>
    <td class=ul>$w[StatusDSN]</td>
    <td class=ul align=center>$w[GolonganID]</td>
    <td class=ul align=center>$w[KategoriID]</td>
    <td class=ul align=right>$TSKS</td>
    <td class=ul align=right>$TTrans</td>
    <td class=ul align=right>$TTtp</td>
    <td class=ul align=right>$TTam</td>
    <td class=ul align=right>$TPot</td>
    <td class=ul align=right>$w[Pajak]%</td>
    <td class=ul align=right>$_sub</td>
    <td class=ul align=center><input type=checkbox name='Hondos[]' value='$w[HonorDosenID]' checked>
      <a href='dosen.honor.cetak.php?Hondos[]=$w[HonorDosenID]' target=_blank><img src='img/printer.gif'></a></td>
    </tr>";
  }
  $_Total = number_format($Total);
  echo "<tr><td colspan=12 align=right>Total :</td><td class=ul align=right>$_Total</td>
    <td class=ul><input type=submit name='Cetak' value='Cetak'></td></tr>";
  echo "</form></table>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$DosenID = GetSetVar('DosenID');
$prodi = GetSetVar('prodi');
$PeriodeMinggu = GetSetVar('PeriodeMinggu', 'M1');
$PeriodeBulan = GetSetVar('PeriodeBulan', date('m'));
$PeriodeTahun = GetSetVar('PeriodeTahun', date('Y'));
// Tanggal Mulai
$TglMulai_d = GetSetVar('TglMulai_d', 11);
$TglMulai_m = GetSetVar('TglMulai_m', date('m')-1);
$TglMulai_y = GetSetVar('TglMulai_y', date('Y'));
// Tanggal Selesai
$TglSelesai_d = GetSetVar('TglSelesai_d', 10);
$TglSelesai_m = GetSetVar('TglSelesai_m', date('m'));
$TglSelesai_y = GetSetVar('TglSelesai_y', date('Y'));

$TglMulai = "$TglMulai_y-$TglMulai_m-$TglMulai_d";
$_SESSION['TglMulai'] = $TglMulai;
$TglSelesai = "$TglSelesai_y-$TglSelesai_m-$TglSelesai_d";
$_SESSION['TglSelesai'] = $TglSelesai;

// *** Main ***
TampilkanJudul("Cetak Honor Dosen Honorer");
TampilkanHeaderHonorDosen('dosen.honor.jur');
DaftarHonorDosenProdi();
?>
