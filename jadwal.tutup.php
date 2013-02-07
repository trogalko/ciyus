<?php
// Author: Emanuel Setio Dewo
// 02 Mei 2006
// www.sisfokampus.net

$_LevelAksesTutupJadwal = ".1.2.40.";
include_once "mhswkeu.lib.php";
include_once "mhswkeu.sav.php";

// *** Functions ***
function DftrJdwl() {
  global $arrID;
  $arrVld = GetFields('tahun',
    "ProdiID='$_SESSION[prodi]' and ProgramID='$_SESSION[prid]' and TahunID",
    $_SESSION['tahun'], '*');
  if (empty($arrVld)) {
    echo ErrorMsg("Tahun Akademik Belum Dibuat",
    "Tahun Akademik <b>$_SESSION[tahun]</b> untuk Program <b>$_SESSION[prid]</b> dan Program Studi <b>$_SESSION[prodi]</b> belum dibuat.<br />
    Hubungi Kepala Akademik/Jurusan.");
  }
  else {
    //TampilkanMenuJadwal();
    TampilkanJadwal();
  }
}
function TampilkanJadwal() {
  $hdrjdwl = "<tr><th class=ttl>ID</th>
    <th class=ttl>Waktu</th>
    <th class=ttl>Ruang</th>
    <th class=ttl>Kode MK</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>Jen</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Dosen</th>
    <th class=ttl>Jml<br />Mhsw</th>
    <th class=ttl title='Kelas Serial'>Serial</th>
    <th class=ttl>Hrg<br />Std?</th>
    <th class=ttl title='Presensi'>Pres</th>
    <th class=ttl title='Prasyarat'>Pra</th>
    <th class=ttl title='Tutup Jadwal'>Tutup</th>
    </tr>
  ";
  $s = "select j.*, r.KampusID,
    time_format(j.JamMulai, '%H:%i') as Mulai,
    time_format(j.JamSelesai, '%H:%i') as Selesai
    from jadwal j
      left outer join mk mk on j.MKID=mk.MKID
      left outer join ruang r on j.RuangID=r.RuangID
    where j.NamaKelas<>'KLINIK'
      and j.KodeID='$_SESSION[KodeID]' and j.TahunID='$_SESSION[tahun]'
      and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0
      and INSTR(j.ProgramID, '.$_SESSION[prid].')>0
    order by j.HariID, j.JamMulai, j.MKKode, j.NamaKelas";
  $r = _query($s);
  // Tampilkan daftar jadwal
  $hari = -1;
  $gotohari = DftrHari();
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=100%>";
  while ($w = _fetch_array($r)) {
    if ($hari != $w['HariID']) {
      $hari = $w['HariID'];
      $NamaHari = GetaField('hari', 'HariID', $hari, 'Nama');
      echo "<tr><td class=ul colspan=12><b><a name='$hari'></a>$NamaHari</b>
       <a href='#Atas' title='Kembali ke atas'>^</a> $gotohari</td></tr>";
      echo $hdrjdwl;
    }
    $c = ($w['NA'] == 'N')? "class=ul" : "class=nac";
    // Kelas Serial
    $ser = ($w['JadwalSer'] == 0)? '' : "<abbr title='Serial dgn Jadwal: $w[JadwalSer]'>» ".$w['JadwalSer']."</abbr>";
    $jumlahser = ($w['JumlahKelasSerial'] >0)? 'Ada: '.$w['JumlahKelasSerial'] : '&nbsp;';

    $arrdosen = explode('.', TRIM($w['DosenID'], '.'));
    $strdosen = implode(',', $arrdosen);
    $dosen = (empty($strdosen))? '' : GetArrayTable("select Nama from dosen where Login in ($strdosen) order by Nama",
      "Login", "Nama", '<br />');
    $hrg = ($w['HargaStandar'] == 'Y')? "<img src='img/$w[HargaStandar].gif'>" : number_format($w['Harga']);
    $arrpra = GetArrayTable("select concat(mk.MKKode, ' - ', mk.Nama, ' (SKS min: ', mk.SKSMin, ', IPK min: ', mk.IPKMin, ')') as PRA 
      from mkpra
        left outer join mk on mkpra.PraID=mk.MKID
      where mkpra.MKID='$w[MKID]' ", 
      'PRA', 'PRA', $_lf);
    $strpra = (empty($arrpra))? '&nbsp;' : "<a name='$w[JadwalID]' onClick=\"javascript:alert('$arrpra')\"><img src='img/check.gif'></a>";
    $ttp1 = ($w['NA'] == 'Y')? $w['JadwalID'] : "<a href='?mnux=jadwal&gos=JdwlEdt&md=0&JadwalID=$w[JadwalID]'><img src='img/edit.png'>
        $w[JadwalID]</a>";
    $ttp2 = ($w['NA'] == 'Y')? "&times;" : "<a href='?mnux=jadwal.tutup&gos=JdwlTtp&JadwalID=$w[JadwalID]'><img src='img/del.gif'></a>";
    echo "<tr>
      <td class=inp1 nowrap>$ttp1</td>
      <td $c>$w[Mulai]-$w[Selesai]</td>
      <td $c>$w[KampusID]-$w[RuangID]</td>
      <td $c>$w[MKKode]</td>
      <td $c>$w[Nama]</td>
      <td $c>$w[NamaKelas]&nbsp;</td>
      <td $c align=center>$w[JenisJadwalID]</td>
      <td $c>$w[SKS] ($w[SKSAsli])</td>
      <td $c>$dosen&nbsp;</td>
      <td $c align=right>$w[JumlahMhsw]/$w[Kapasitas]</td>
      <td $c align=right title='Jumlah Kelas Serial'>$jumlahser $ser</td>
      <td $c align=center>$hrg</td>
      <td $c title='Presensi'><a href='?mnux=jadwal.pres&JadwalID=$w[JadwalID]'><img src='img/check.gif'></a> $w[Kehadiran]</td>
      <td $c title='Matakuliah prasyarat'>$strpra</td>
      <td $c align=center title='Tutup'>$ttp2</td></tr>
      </tr>";
  }
  echo "</table></p>";
  // Tampilkan pesan
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=100%>
  <tr><td class=ul nowrap><b>Jadwal Serial</b></td>
    <td class=ul>Jadwal Serial adalah jadwal matakuliah yang dipecah menjadi beberapa kali
    pertemuan dalam 1 minggu. Karena sebenarnya adalah 1 jadwal matakuliah,
    maka mahasiswa wajib hadir di setiap pertemuan
    dan masing-masing pertemuan memiliki isian presensi sendiri.
    Nilai akan diperhitungkan dengan jumlah SKS-nya.</td></tr>
  <tr><td class=ul nowrap><b>Pres (Presensi)</b></td>
    <td class=ul>Memasukkan presensi dosen dan mahasiswa.</td></tr>
  </table></p>";
}
function DftrHari() {
  $s = "select HariID, Nama from hari order by HariID";
  $r = _query($s);
  $a = array();
  while ($w = _fetch_array($r)) {
    $a[] = "<a href='#$w[HariID]'>$w[Nama]</a>";
  }
  return implode(', ', $a);
}
function JdwlTtp() {
  $JadwalID = $_REQUEST['JadwalID'];
  $jdwl = GetFields('Jadwal', 'JadwalID', $JadwalID, '*');
  $jj = GetaField('jenisjadwal', 'JenisJadwalID', $jdwl['JenisJadwalID'], 'Nama');
  $hr = GetaField('hari', 'HariID', $jdwl['HariID'], 'Nama');
  // Ambil nama dosen
  $arrdosen = explode('.', TRIM($jdwl['DosenID'], '.'));
  $strdosen = implode(',', $arrdosen);
  $dosen = (empty($strdosen))? '' : GetArrayTable("select Nama from dosen where Login in ($strdosen) order by Nama",
      "Login", "Nama", ', ');
  $tgl = GetDateOption(date('Y-m-d'), 'Tgl');
  echo Konfirmasi('Tutup Kelas Kuliah',
    "<p>Benar Anda akan menutup kelas ini?</p>
    <p><table class=box cellspacing=1 cellpadding=4>
    <tr><td class=inp>Matakuliah</td><td class=ul>$jdwl[MKKode] - $jdwl[Nama] $jdwl[NamaKelas]</td></tr>
    <tr><td class=inp>Jenis</td><td class=ul>$jj</td></tr>
    <tr><td class=inp>Hari, Jam</td><td class=ul>$hr, $jdwl[JamMulai] ~ $jdwl[JamSelesai]</td></tr>
    <tr><td class=inp>Dosen Pengampu</td><td class=ul>$dosen</td></tr>
    </table></p>
    <p>Jika ya, maka masukkan nomer surat penutupan dari Purek 1 di bawah ini:</p>
    
    <p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='jadwal.tutup'>
    <input type=hidden name='JadwalID' value='$JadwalID'>
    <input type=hidden name='gos' value='JdwlTtp1'>
    <tr><td class=inp>Nomer Surat</td><td class=ul><input type=text name='NoSurat' size=30 maxlength=50></td></tr>
    <tr><td class=inp>Tanggal</td><td class=ul>$tgl</td></tr>
    <tr><td class=inp>Alasan Penutupan</td><td class=ul><textarea name='Keterangan' cols=30 rows=5></textarea></td></tr>
    <tr><td class=ul colspan=2><input type=button name='Batal' value='Batal Tutup' onClick=\"location='?mnux=jadwal.tutup'\">
      <input type=reset name='Reset' value='Reset'>
      <input type=submit name='Simpan' value='Tutup Kelas ini'></td></tr>
    </form></table></p>
    ");
}
function JdwlTtp1() {
  $JadwalID = $_REQUEST['JadwalID'];
  $jdwl = GetFields('jadwal', 'JadwalID', $JadwalID, '*');
  // Parameter
  $NoSurat = sqling($_REQUEST['NoSurat']);
  $Tgl = "$_REQUEST[Tgl_y]-$_REQUEST[Tgl_m]-$_REQUEST[Tgl_d]";
  $Keterangan = sqling($_REQUEST['Keterangan']);
  // Tutup
  $st = "insert into jadwaltutup (TahunID, ProgramID, ProdiID,
    JadwalID, MKID, MKKode, Nama, DosenID,
    NamaKelas, JenisJadwalID, 
    HariID, JamMulai, JamSelesai,
    NoSurat, Tanggal, Keterangan,
    LoginBuat, TanggalBuat)
    values ('$jdwl[TahunID]', '$jdwl[ProgramID]', '$jdwl[ProdiID]',
    '$JadwalID', '$jdwl[MKID]', '$jdwl[MKKode]', '$jdwl[Nama]', '$jdwl[DosenID]',
    '$jdwl[NamaKelas]', '$jdwl[JenisJadwalID]',
    '$jdwl[HariID]', '$jdwl[JamMulai]', '$jdwl[JamSelesai]',
    '$NoSurat', '$Tgl', '$Keterangan',
    '$_SESSION[_Login]', now())";
  $rt = _query($st);
  // set semua krs menjadi tutup
  echo "<p>KRS Mahasiswa yg dibatalkan:</p>";
  echo "<ol>";
  $s = "select * from krs where JadwalID=$JadwalID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    // batalkan KRS
    $sk = "update krs set StatusKRSID='M', NA='Y', CatatanError='Jadwal dihapus'
      where KRSID=$w[KRSID] ";
    $rk = _query($sk);
    // update pembayaran
    $_REQUEST['khsid'] = $w['KHSID'];
    $_REQUEST['mhswid'] = $w['MhswID'];
    $_REQUEST['pmbmhswid'] = 1;
    PrcBIPOTSesi();
    echo "<li>$w[MhswID]</li>";
  }
  echo "</ol>";
  // Hapus
  $sh = "update jadwal set NA='Y' where JadwalID=$JadwalID ";
  $rh = _query($sh);
  DftrJdwl();
}


// *** Parameters ***
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$tahun = GetSetVar('tahun');
$gos = (empty($_REQUEST['gos']))? 'DftrJdwl' : $_REQUEST['gos'];

// *** Main ***
$NTahun = NamaTahun($tahun);
TampilkanJudul("Tutup Jadwal Kuliah $NTahun");
TampilkanTahunProdiProgram('jadwal.tutup', '');
if (!empty($_SESSION['prodi']) && !empty($_SESSION['prid']) && !empty($tahun)) {
  $gos();
}
?>
