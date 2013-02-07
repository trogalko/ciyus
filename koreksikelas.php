<?php
// Author: Emanuel Setio Dewo
// 26 Sept 2006
// www.sisfokampus.net

// *** Functions ***
function DftrJdwl() {
  $s = "select j.*, h.Nama as HR, concat(d.Nama, ', ', d.Gelar) as DSN, 
    time_format(j.JamMulai, '%H:%i') as JM,
    time_format(j.JamSelesai, '%H:%i') as JS
    from jadwal j
      left outer join hari h on j.HariID=h.HariID
      left outer join dosen d on j.DosenID=d.Login
    where j.TahunID='$_SESSION[tahun]'
      and INSTR(j.ProgramID, '.$_SESSION[prid].')>0
      and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0
    order by j.HariID, j.JamMulai";
  $r = _query($s); $n = 0; $hr = '1234560987';
  $hdr = "<tr><th class=ttl>ID</th>
    <th class=ttl>Ruang</th>
    <th class=ttl>Jam</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>Jenis</th>
    <th class=ttl>Dosen</th>
    <th class=ttl title='Jumlah Mhsw/Target Peserta'>Mhsw/<br />Kaps</th>
    <th class=ttl>Koreksi</th>
    </tr>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  while ($w = _fetch_array($r)) {
    $n++;
    if ($hr != $w['HariID']) {
      $hr = $w['HariID'];
      echo "<tr><td class=ul colspan=15><font size=+1>$w[HR]</td></tr>";
      echo $hdr;
    }
    if ($w['Final'] == 'Y') {
      $koreksi = "&times;";
    }
    else {
      $koreksi = "<a href='?mnux=koreksikelas&gos=KoreksiKelas&jid=$w[JadwalID]'><img src='img/check.gif'></a>";
    }
    echo "<tr><td class=inp>$w[JadwalID]</td>
      <td class=ul>$w[RuangID]&nbsp;</td>
      <td class=ul>$w[JM]-$w[JS]</td>
      <td class=ul>$w[MKKode]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul>$w[NamaKelas]</td>
      <td class=ul>$w[JenisJadwalID]</td>
      <td class=ul>$w[DSN]</td>
      <td class=ul align=right>$w[JumlahMhsw]/$w[Kapasitas]</td>
      <td class=ul align=center>$koreksi</td>
    </tr>";
  }
  echo "</table></p>";
}
function TampilkanHeaderKoreksiKelas($jdwl) {
  CheckFormScript('Ke_Kelas');
  echo "<form action='?' name='Korek' method=POST onSubmit=\"return CheckForm(this)\">
    <input type=hidden name='mnux' value='koreksikelas'>
    <input type=hidden name='gos' value='KoreksiKelasSav'>
    <input type=hidden name='jid' value='$jdwl[JadwalID]'>";
  //GetOption2($_table, $_field, $_order='', $_default='', $_where='', $_value='', $not=0) {
  $optjdwl = GetOption2("jadwal j 
    left outer join dosen d on j.DosenID=d.Login
    left outer join hari h on j.HariID=h.HariID", 
    "concat(j.JadwalID, '. ', h.Nama, ' ', j.MKKode, ' - ', j.Nama, ' ', j.NamaKelas, ' (', j.JenisJadwalID, ') - ', d.Nama)",
    "j.HariID, j.JamMulai", '', 
    "INSTR(j.ProgramID, '.$_SESSION[prid].')>0 and j.TahunID = '$_SESSION[tahun]' and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0 and MKKode='$jdwl[MKKode]' and JadwalID<>$jdwl[JadwalID] and JenisJadwalID = '$jdwl[JenisJadwalID]'",
    "JadwalID", 1);
  TuliskanCentangSemua();
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=ul colspan=4><font size=+1>Jadwal Matakuliah Asal</td></tr>
  <tr><td class=inp>Jadwal</td><td class=ul>$jdwl[JadwalID]</td>
    <td class=inp>Ruang</td><td class=ul>$jdwl[RuangID]&nbsp;</td></tr>
  <tr><td class=inp>Waktu</td><td class=ul>$jdwl[HR], $jdwl[JM]-$jdwl[JS]</td>
    <td class=inp>Kelas</td><td class=ul>$jdwl[NamaKelas], SKS: $jdwl[SKS]</td></tr>
  <tr><td class=inp>Matakuliah</td><td class=ul>$jdwl[MKKode] - $jdwl[Nama] ($jdwl[JenisJadwalID])</td>
    <td class=inp>Dosen Utama</td><td class=ul>$jdwl[DSN]</td></tr>
  
  <tr><td class=ul colspan=4><font size=+1>Pindahkan ke Kelas:</td></tr>
  <tr><td class=inp>Kelas</td><td class=ul colspan=3><select name='Ke_Kelas' style='font-family: courier new'>$optjdwl</select></td></tr>
  <tr><td class=ul colspan=4><input type=submit name='Pindah' value='Pindahkan Mhsw'> 
  Centang Mhsw yg akan dipindahkan kelas-nya 
  <input type=button name='CentangAll' value='Centang Semua' onClick=\"CentangSemua(Korek, true);\">
  <input type=button name='UnCentangAll' value='Kosongkan Semua' onClick=\"CentangSemua(Korek, false);\">
  </td></tr>
  </table></p>";
}
function TuliskanCentangSemua() {
  echo "<script>
  function CentangSemua(frm, ck) {
    for (i=1; i <= frm.JmlMhsw.value; i++) {
      document.getElementById(\"KRSID_\"+i).checked = ck;
    }
  }
  </script>";
}
function KoreksiKelas() {
  $jid = $_REQUEST['jid'];
  $jdwl = GetFields("jadwal j 
    left outer join dosen d on j.DosenID=d.Login
    left outer join hari h on j.HariID=h.HariID", 
    "j.JadwalID", $jid, 
    "j.*, h.Nama as HR, concat(d.Nama, ', ', d.Gelar) as DSN,
    time_format(j.JamMulai, '%H:%i') as JM,
    time_format(j.JamSelesai, '%H:%i') as JS");
  TampilkanHeaderKoreksiKelas($jdwl);
  TampilkanDaftarMhsw($jdwl);
}
function TampilkanDaftarMhsw($jdwl) {
  $s = "select k.KRSID, k.MhswID, m.Nama
    from krs k
      left outer join mhsw m on k.MhswID=m.MhswID
    where k.JadwalID='$jdwl[JadwalID]'
    order by k.MhswID";
  $r = _query($s); $n = 0;
  $jml = _num_rows($r)+0;
  echo "<input type=hidden name='JmlMhsw' value=$jml>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  $hdr = "<tr><th class=ttl>#</th>
    <th class=ttl>N.P.M</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Centang</th>
    </tr>";
  echo $hdr;
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr><td class=inp>$n</td>
      <td class=ul>$w[MhswID]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul align=center><input type=checkbox id='KRSID_$n' name='KRSID_$n' value='$w[KRSID]' checked></td>
      </tr>";
  }
  echo "</table></form>";
}
function KoreksiKelasSav() {
  $jid = $_REQUEST['jid'];
  $Ke_Kelas = $_REQUEST['Ke_Kelas'];
  $JmlMhsw = $_REQUEST['JmlMhsw'];
  $n = 0;
  for ($i = 1; $i <= $JmlMhsw; $i++) {
    $krsid = $_REQUEST['KRSID_'.$i];
    if (!empty($krsid)) {
      $n++;
			//update KRS
      $s = "update krs
        set JadwalID=$Ke_Kelas, Catatan='Pindah Jadwal dari $jid', LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
        where KRSID=$krsid";
      $r = _query($s);
			//update KRSTEMP
			$s1 = "update krstemp
				set JadwalID=$Ke_Kelas, Catatan='Pindah Jadwal dari $jid', LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
				where KRSID=$krsid";
			$r1 = _query($s1);	
			//update Jadwal
			$s0 = "update jadwal
				set JumlahMhsw=JumlahMhsw-1 where JadwalID = '$Ke_kelas'";
			$r0 = _query($s0);
    }
  }
  HitungJumlahMhsw($jid);
  HitungJumlahMhsw($Ke_Kelas);
  echo Konfirmasi("Telah Dipindahkan",
    "Sebanyak <font size=+1>$n</font> mhsw telah dipindah kelasnya.
    <hr size=1 color=silver>
    Option: <a href='?mnux=koreksikelas'>Kembali</a>");
}
function HitungJumlahMhsw($jid) {
  $mhswkrs = GetaField('krstemp', 'JadwalID', $jid, "count(KRSID)")+0;
  $mhsw    = GetaField('krs', 'JadwalID', $jid, "count(KRSID)")+0;
  $s = "update jadwal set JumlahMhsw = '$mhsw', JumlahMhswKRS = '$mhswkrs' where JadwalID = '$jid' ";
  $r = _query($s);
}

// *** Parameters ***
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$tahun = GetSetVar('tahun');
$gos = (empty($_REQUEST['gos']))? 'DftrJdwl' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Koreksi Kelas");
TampilkanTahunProdiProgram('koreksikelas', '');
if (!empty($prid) && !empty($prodi)) $gos();
?>
