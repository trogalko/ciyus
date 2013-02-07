<?php
// Author: Emanuel Setio Dewo
// 15 March 2006

function NilaiSav() {
  $jadwalid = $_REQUEST['jadwalid'];
  $jdwl = GetFields('jadwal', 'JadwalID', $jadwalid, "Final");
  if ($jdwl['Final'] == 'Y') {
    $pesan = "<center><h1><font color=red>Perubahan Tidak Disimpan</font></h1><hr>
      Perubahan yang Anda lakukan tidak akan disimpan karena data nilai sudah difinalisasi.<hr>
      <input type=button name='Tutup' value='Tutup Pesan' onClick='window.close()'></center>";
    $nmf = "tmp/$_SESSION[_Login].html";
    $hnd = fopen($nmf, 'w');
    fwrite($hnd, $pesan);
    fclose($hnd);
    PopupMsg($nmf);
  }
  else NilaiSav1($jadwalid, $jdwl);
}

function NilaiSav1($jadwalid, $jdwl) {
  $TugasMandiri = $_REQUEST['TugasMandiri']+0;
  $Tugas1 = $_REQUEST['Tugas1']+0;
  $Tugas2 = $_REQUEST['Tugas2']+0;
  $Tugas3 = $_REQUEST['Tugas3']+0;
  $Tugas4 = $_REQUEST['Tugas4']+0;
  $Tugas5 = $_REQUEST['Tugas5']+0;
  $Presensi = $_REQUEST['Presensi']+0;
  $UTS = $_REQUEST['UTS']+0;
  $UAS = $_REQUEST['UAS']+0;
  $Responsi = $_REQUEST['Responsi']+0;
  if ($TugasMandiri == 0)
    $tot = $Tugas1 + $Tugas2 + $Tugas3 + $Tugas4 + $Tugas5 
      +$Presensi + $UTS + $UAS;
  else {
    $tot = $TugasMandiri + $Presensi + $UTS + $UAS;
    $Tugas1 = 0;
    $Tugas2 = 0;
    $Tugas3 = 0;
    $Tugas4 = 0;
    $Tugas5 = 0;
  }
  if ($tot > 100) {
    $pesan = "<center><h1>Terjadi Kesalahan</h1>
      <hr>
      <p>Jumlah total bobot melebihi 100%, yaitu: <b>$tot</b>%.<br />
      Data tidak dapat disimpan.</p>
      <hr>
      <input type=button name='Tutup' value='Tutup Pesan' onClick='javascript:window.close()'>
      </center>";
    $nmf = "tmp/$_SESSION[_Login].html";
    $hnd = fopen($nmf, 'w');
    fwrite($hnd, $pesan);
    fclose($hnd);
    PopupMsg($nmf);
  }
  else {
    $s = "update jadwal set TugasMandiri='$TugasMandiri',
      Tugas1='$Tugas1', Tugas2='$Tugas2', Tugas3='$Tugas3', Tugas4='$Tugas4', Tugas5='$Tugas5',
      Presensi='$Presensi', UTS='$UTS', UAS='$UAS', Responsi='$Responsi'
      where JadwalID=$jadwalid ";
    $r = _query($s);
  }
}
function HitungNilai() {
  // Ambil data
  $jadwalid = $_REQUEST['jadwalid'];
  $jdwl = GetFields('jadwal', 'JadwalID', $jadwalid, '*');
  $TOTAL = $jdwl['Tugas1']+$jdwl['Tugas2']+$jdwl['Tugas3']+$jdwl['Tugas4']+$jdwl['Tugas5']+$jdwl['Presensi']+$jdwl['UTS']+$jdwl['UAS']+$jdwl['Responsi'];
  
  if ($jdwl['Final'] == 'Y') {
    $pesan = "<center><h1>Tidak Dapat Diproses</h1><hr size=1 color=silver />
      Nilai sudah tidak dapat diproses karena sudah difinalisasi.<br />
      Hubungi SIM/Ka BAA untuk informasi lebih lanjut tentang finalisasi nilai.
      <hr size=1 color=silver />
      <input type=button name='Tutup' value='Tutup Pesan' onClick='window.close()'>";
    $nmf = "tmp/$_SESSION[_Login].html";
    $f = fopen($nmf, 'w');
    fwrite($f, $pesan);
    fclose($f);
    PopupMsg($nmf);
  }
  elseif ($TOTAL == 0){
    $pesan = "<center><h1>Tidak Dapat Diproses</h1><hr size=1 color=silver />
      Nilai tidak dapat diproses karena <u><b>Bobot Nilai</b></u> untuk mata kuliah ini belum diisi.<br />
      Isi terlebih dahulu Bobot Nilai untuk mata kuliah ini.
      <hr size=1 color=silver />
      <input type=button name='Tutup' value='Tutup Pesan' onClick='window.close()'>";
    $nmf = "tmp/$_SESSION[_Login].html";
    $f = fopen($nmf, 'w');
    fwrite($f, $pesan);
    fclose($f);
    PopupMsg($nmf);
  }
  else HitungNilai1($jadwalid, $jdwl);
}
function HitungNilai1($jadwalid, $jdwl) {
  // lihat persentase Tugas Mandiri
  if ($jdwl['TugasMandiri'] > 0) {
    $TGS = GetFields('krs', 'JadwalID', $jadwalid,
      "sum(Tugas1) as T1, sum(Tugas2) as T2, sum(Tugas3) as T3, sum(Tugas4) as T4, sum(Tugas5) as T5");
    $_T1 = ($TGS['T1'] > 0)? 1 : 0;
    $_T2 = ($TGS['T2'] > 0)? 1 : 0;
    $_T3 = ($TGS['T3'] > 0)? 1 : 0;
    $_T4 = ($TGS['T4'] > 0)? 1 : 0;
    $_T5 = ($TGS['T5'] > 0)? 1 : 0;
    $JumlahTugas = $_T1 + $_T2 + $_T3 + $_T4 + $_T5;
    // Distribusikan persentase tugas
    $PersenTugas = $jdwl['TugasMandiri'] / $JumlahTugas;
    $SisaTugas = $jdwl['TugasMandiri'] % $JumlahTugas;
    $_fld = array();
    for ($i = 1; $i <= 5; $i++) {
      $fld = "_T$i";
      $PersenTugas = ($$fld == 1)? $PersenTugas : 0;
      $jdwl["Tugas$i"] = $PersenTugas;
      //$persen = ($i == 1)? $PersenTugas + $SisaTugas : $PersenTugas;
      $_fld[] = "Tugas$i=$PersenTugas";
    }
    $fld = implode(', ', $_fld);
    $s0 = "update jadwal set $fld where JadwalID=$jadwalid";
    $r0 = _query($s0);
  }
  // Proses
  $s = "select * from krs where JadwalID=$jadwalid order by MhswID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $nilai = ($w['Tugas1'] * $jdwl['Tugas1']) +
      ($w['Tugas2'] * $jdwl['Tugas2']) +
      ($w['Tugas3'] * $jdwl['Tugas3']) +
      ($w['Tugas4'] * $jdwl['Tugas4']) +
      ($w['Tugas5'] * $jdwl['Tugas5']) +
      ($w['Presensi'] * $jdwl['Presensi']) +
      ($w['UTS'] * $jdwl['UTS']) +
      ($w['UAS'] * $jdwl['UAS'])
      ;
    $nilai = ($nilai / 100) +0;
    if ($jdwl['Responsi'] > 0) {
      $nilai = ($nilai * (100 - $jdwl['Responsi'])/100) +
        ($w['Responsi'] * ($jdwl['Responsi'])/100);
    }
    $ProdiID = GetaField('mhsw', "MhswID", $w['MhswID'], "ProdiID");
    $arrgrade = GetFields('nilai', 
      "KodeID='$_SESSION[KodeID]' and NilaiMin <= $nilai and $nilai <= NilaiMax and ProdiID",
      $ProdiID, "Nama, Bobot");
    // Simpan
    $s1 = "update krs set NilaiAkhir='$nilai', GradeNilai='$arrgrade[Nama]', BobotNilai='$arrgrade[Bobot]'
      where KRSID=$w[KRSID] ";
    $r1 = _query($s1);
  }
}
function FinalisasiSav() {
  $jadwalid = $_REQUEST['jadwalid'];
  $jdwl = GetFields('jadwal', "JadwalID", $jadwalid, "*");
  // Jika merupakan responsi
  if ($jdwl['JenisJadwalID'] == 'R') {
    $s0 = "select krs.KRSID, krs.MhswID, krs.NilaiAkhir
      from krs krs
      where krs.JadwalID='$jadwalid'
      order by krs.MhswID";
    $r0 = _query($s0);
    while ($w0 = _fetch_array($r0)) {
      $s1 = "update krs set Responsi='$w0[NilaiAkhir]'
        where krs.MKKode='$jdwl[MKKode]' and krs.MhswID='$w0[MhswID]'
          and krs.TahunID='$jdwl[TahunID]' 
          and krs.KRSID<>$w0[KRSID]";
      $r1 = _query($s1);
      //echo "<pre>$s1</pre>";
    }
  }
  // Simpan finalisasi
  $s = "update jadwal set Final='Y', Gagal='N' where JadwalID='$jadwalid' ";
  $r = _query($s);
  // Finalisasi KRS
  $s1 = "update krs set Final='Y' where JadwalID='$jadwalid' ";
  $r1 = _query($s1);
}
function DefinalisasiSav() {
  $jadwalid = $_REQUEST['jadwalid'];
  // Update Jadwal
  $s = "update jadwal set Final='N' where JadwalID='$jadwalid' ";
  $r = _query($s);
  // Update KRS
  $s1 = "update krs set Final='N' where JadwalID='$jadwalid' ";
  $r1 = _query($s1);
}
function GagalisasiSav() {
  $jadwalid = $_REQUEST['jadwalid'];
  $NilaiGagal = $_REQUEST['NilaiGagal'];
  if (empty($NilaiGagal))
    echo ErrorMsg("Nilai Belum Diset",
	  "Anda harus mengeset nilai gagal penilaian dosen untuk matakuliah ini.<br>
	  Proses Gagal Nilai Dosen tidak dilakukan.");

  else {
	// Gagalkan jadwal
	$Nilai = GetFields('nilai', 'NilaiID', $NilaiGagal, '*');
	$BobotNilai = $Nilai['Bobot'];
	$GradeNilai = $Nilai['Nama'];
	$CatatanGagal = sqling($_REQUEST['CatatanGagal']);
	$s = "update jadwal set Gagal='Y', CatatanGagal='$CatatanGagal', NilaiGagal='$GradeNilai'
	  where JadwalID='$jadwalid' ";
	$r = _query($s);
	// Set semua nilai mahasiswa
	$s1 = "update krs set GradeNilai='$GradeNilai', BobotNilai=$BobotNilai, Final='Y'
	  where JadwalID='$jadwalid' ";
	$r1 = _query($s1);
	// Finalisasi
	$s2 = "update jadwal set Final='Y' where JadwalID='$jadwalid' ";
	$r2 = _query($s2);
  }
}
?>
