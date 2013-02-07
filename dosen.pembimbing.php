<?php
// Author: Emanuel Setio Dewo
// 21 June 2006
// www.sisfokampus.net

// *** Functions ***
function TampilkanPARentangNPM() {
  echo "<p><font size=+1>&raquo; Tentukan PA untuk rentang NPM</font></p>";
  echo "<script language='javascript1.2'>
  <!--
  function caridosen(frm) {
    lnk = \"caridosen.php?DosenID=\"+frm.DosenID.value+\"&NamaDosen=\"+frm.NamaDosen.value+\"&prodi=\"+frm.prodi.value;
    win2 = window.open(lnk, \"\", \"width=600, height=600, scrollbars, status\");
    win2.creator = self;
  }
  //-->
  </script>";
  echo "<blockquote>
  <table class=box cellspacing=1 cellpadding=4>
  <form name='data' action='?' method=POST>
  <input type=hidden name='prodi' value=''>
  <input type=hidden name='gos' value='RentangNPM'>
  <tr><td class=ul colspan=2>Untuk melakukan penentuan pembimbing akademik dari rentang NPM tertentu.</td></tr>
  <tr><td class=inp>Rentang NPM</td>
    <td class=ul><input type=text name='DariNPM' value='$_SESSION[DariNPM]' size=20 maxlength=30>
    s/d
    <input type=text name='SampaiNPM' value='$_SESSION[SampaiNPM]' size=20 maxlength=30>
    </td></tr>
  <tr><td class=inp>Pembimbing Akademik</td>
    <td class=ul><input type=text name='DosenID' value='$_SESSION[DosenID]' size=20 maxlength=30>
    <input type=text name='NamaDosen' size=30 maxlength=30>
    <a href='javascript:caridosen(data)'>Cari Dosen</a>
    </td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan Pembimbing Akademik'>
    </td></tr>
  </form></table>
  </blockquote>";
}
function RentangNPM() {
  $DariNPM = $_REQUEST['DariNPM'];
  $SampaiNPM = $_REQUEST['SampaiNPM'];
  $DosenID = $_REQUEST['DosenID'];
  if (empty($DariNPM) || empty($SampaiNPM)) 
    echo ErrorMsg("Rentang NPM Harus Diisi",
    "Tidak dapat memproses karena rentang NPM belum diisi.
    <hr size=1 color=silver>
    Pilihan: <a href='?mnux=dosen.pembimbing'>Kembali</a>");
  else {
    $dsn = GetFields('dosen', "Login", $DosenID, "Nama, Gelar");
    if (empty($dsn))
      echo ErrorMsg("Dosen Tidak Ada",
      "Dosen dengan Kode: <font size=+1>$DosenID</font> tidak ditemukan.<br />
      Data tidak disimpan.<hr size=1 color=silver>
      Pilihan: <a href='?mnux=dosen.pembimbing'>Kembali</a>");
    else RentangNPMSav($DariNPM, $SampaiNPM, $DosenID, $dsn);
  } 
}
function RentangNPMSav($DariNPM, $SampaiNPM, $DosenID, $dsn) {
  $s = "select m.MhswID, m.Nama, concat(d.Nama, ', ', d.Gelar) as PA,
    prd.Nama as PRD, prg.Nama as PRG
    from mhsw m
      left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
      left outer join dosen d on m.PenasehatAkademik=d.Login
      left outer join prodi prd on m.ProdiID=prd.ProdiID
      left outer join program prg on m.ProgramID=prg.ProgramID
    where sm.Keluar='N' and m.NA='N'
      and '$DariNPM' <= m.MhswID and m.MhswID <= '$SampaiNPM'
    order by MhswID";
  $r = _query($s); $n = 0;
  $jml = _num_rows($r);
  $_npm = '';
  $a = "<p><table class=box cellspacing=1>
    <tr><th class=ttl>#</th>
    <th class=ttl>NPM</th>
    <th class=ttl>Nama Mahasiswa</th>
    <th class=ttl>Program</th>
    <th class=ttl>Program Studi</th>
    <th class=ttl>Pembimbing Akademik</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $_npm .= "&_NPM[]=$w[MhswID]";
    $a .= "<tr><td class=inp>$n</td>
    <td class=ul>$w[MhswID]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[PRG]</td>
    <td class=ul>$w[PRD]</td>
    <td class=ul>$w[PA] &nbsp;</td>
    </tr>";
  }
  $a .= "</table></p>";
  echo Konfirmasi("Konfirmasi Set Penasehat Akademik",
    "Di bawah ini adalah daftar mahasiswa dalam rentang yang Anda tentukan.<br />
    Terdapat: <font size=+1>$jml</font> mahasiswa.<br />
    Apakah Anda akan mengubah Penasehat Akademik mereka menjadi: <b>$dsn[Nama]</b> ($DosenID)?
    <hr size=1 color=silver>
    Pilihan: <input type=button name='Ubah' value='Ubah PA'
      onClick=\"location='?mnux=dosen.pembimbing&gos=RentangNPMSav1&DariNPM=$DariNPM&SampaiNPM=$SampaiNPM&DosenID=$DosenID$_npm'\">
      <input type=button name='Batal' value='Batalkan' onClick=\"location='?mnux=dosen.pembimbing'\">");
  echo $a;
}
function RentangNPMSav1() {
  $_NPM = array();
  $_NPM = $_REQUEST['_NPM'];
  for ($i = 0; $i < sizeof($_NPM); $i++) {
    $isi = $_NPM[$i];
    $_NPM[$i] = "'$isi'";
  }
  $__npm = implode(',', $_NPM);
  $DosenID = $_REQUEST['DosenID'];
  $s = "update mhsw set PenasehatAkademik='$DosenID'
    where MhswID in ($__npm)";
  $r = _query($s);
  echo Konfirmasi("Pengubahan PA Berhasil",
    "Berikut adalah mahasiswa yang berhasil diubah PA-nya:<br />
    $__npm <hr size=1 color=silver>
    Pilihan: <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=dosen.pembimbing'\">");
}

// --- Pengubahan PA ---
function TampilkanUbahPA() {
  echo "<p><font size=+1>&raquo; Ubah Pembimbing Akademik</font></p>
  <p></p>";
  echo "<blockquote>
  <table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='gos' value='UbahPASav'>
  <tr><td class=ul colspan=2>Untuk melakukan pengubahan Pembimbing Akademik mahasiswa ke dosen yang lain.</td></tr>
  <tr><td class=inp>Tahun Akademik</td>
    <td class=ul><input type=text name='thnsms' value='$_SESSION[thnsms]' size=15 maxlength=5></td></tr>
  <tr><td class=inp>Dari Kode Dosen</td>
    <td class=ul><input type=text name='DariDosen' value='$_SESSION[DariDosen]' size=20 maxlength=30></td></tr>
  <tr><td class=inp>Menjadi</td>
    <td class=ul><input type=text name='MenjadiDosen' value='$_SESSION[MenjadiDosen]' size=20 maxlength=30></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Ubah' value='Ubah Pembimbing Akademik'>
    </td></tr>
  </form></table>
  </blocquote>";
}

function TuliskanCentangSemua() {
  echo "<script>
  function CentangSemua(frm, ck) {
    for (i=1; i <= frm.jmlawal.value; i++) {
      document.getElementById(\"MhswID_\"+i).checked = ck;
    }
  }
  </script>";
}

function UbahPASav() {
  TuliskanCentangSemua();
  $Tahun = $_REQUEST['thnsms'];
  $DariDosen = $_REQUEST['DariDosen'];
  $MenjadiDosen = $_REQUEST['MenjadiDosen'];
  $jmlawal = GetaField('mhsw', "PenasehatAkademik", $DariDosen, "count(PenasehatAkademik)")+0;
  $btl = "<hr size=1 color=silver>Pilihan: <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=dosen.pembimbing'\">";
  if ($jmlawal == 0)
    echo ErrorMsg("Gagal Proses",
    "Tidak ada mahasiswa di bawah bimbingan dosen dengan kode: <font size=+1>$DariDosen</font><br />
    Proses pengubahan pembimbing akademik dibatalkan.$btl");
  else {
    $drdsn = GetFields('dosen', 'Login', $DariDosen, "Nama, Gelar");
    $kedsn = GetFields('dosen', 'Login', $MenjadiDosen, "Nama, Gelar");
    if (empty($kedsn))
      echo ErrorMsg("Dosen Tidak Ditemukan",
      "Dosen dengan kode <font size=+1>$MenjadiDosen</font> tidak ditemukan.<br />
      Anda tidak dapat mengubah PA dari <font size=+1>$drdsn[Nama], $drdsn[Gelar]</font> ($DariDosen) ke
      dosen yang tidak terdaftar dalam database.<br />
      Hubungi MIS/IT untuk informasi lebih lanjut.$btl");
    else {
      $s = "select m.MhswID, m.Nama, prd.Nama as PRD, prg.Nama as PRG
        from mhsw m
          left outer join khs k on k.MhswID = m.MhswID
          left outer join prodi prd on m.ProdiID=prd.ProdiID
          left outer join program prg on m.ProgramID=prg.ProgramID
        where 
        m.StatusMhswID not in ('L', 'D', 'K')
        and m.NA='N' 
        and PenasehatAkademik='$DariDosen'
        and k.TahunID = '$Tahun'
        order by m.MhswID";
      $r = _query($s); $n = 0; $_npm = '';
      //echo "<pre>$s</pre>";
      $jmlawal = _num_rows($r);
      $fb = "<form action='?' method=post name=PAG>
            <input type=hidden name=mnux value=dosen.pembimbing>
            <input type=hidden name=gos value=UbahPASav1>
            <input type=hidden name=DariDosen value='$DariDosen'>
            <input type=hidden name=MenjadiDosen value='$MenjadiDosen'>
            <input type=hidden name=jmlawal value='$jmlawal'>";
      
      $a = "<p><table class=box cellspacing=1><tr><td class=ul colspan=6>Centang Mhsw yg akan diganti PA-nya 
            <input type=button name='CentangAll' value='Centang Semua' onClick=\"CentangSemua(PAG, true);\">
            <input type=button name='UnCentangAll' value='Kosongkan Semua' onClick=\"CentangSemua(PAG, false);\"></td></tr>";
      $a .= "
      
        <tr><th class=ttl>#</th>
        <th class=ttl>NPM</th>
        <th class=ttl>Nama</th>
        <th class=ttl>Program</th>
        <th class=ttl>Program Studi</th>
        <th class=ttl>Centang</th>
        </tr>";
      while ($w = _fetch_array($r)) {
        $n++;
        //$_npm .= "&_NPM[]=$w[MhswID]";
        $a .= "<tr><td class=inp>$n</td>
          <td class=ul>$w[MhswID]</td>
          <td class=ul>$w[Nama]</td>
          <td class=ul>$w[PRG]</td>
          <td class=ul>$w[PRD]</td>
          <td class=ul align=center><input type=checkbox id='MhswID_$n' name='MhswID_$n' value='$w[MhswID]' checked></td>
          </tr>";
      }
      $a .= "</table></p></form>";
      echo $fb;
      echo Konfirmasi("Konfirmasi Pengubahan PA",
        "Anda akan mengubah Pembimbing Akademik mahasiswa dari <font size=+1>$drdsn[Nama], $drdsn[Gelar]</font><br />
        menjadi <font size=+1>$kedsn[Nama], $kedsn[Gelar]</font>?<br />
        Anda akan mengubah PA dari sebanyak: <font size=+1>$jmlawal</font> mahasiswa.<br />
        Di bawah adalah daftar mahasiswa yang akan diganti PA-nya.
        <hr size=1 color=silver>
        Pilihan: <input type=submit name='GantiPA' value='Ganti Penasehat Akademik'>
          <input type=button name='Batal' value='Batalkan Penggantian PA' onClick=\"location='?mnux=dosen.pembimbing'\">");
      echo $a;
    }
  }
}
function UbahPASav1() {
  /*$_NPM = array();
  $_NPM = $_REQUEST['_NPM'];
  for ($i = 0; $i < sizeof($_NPM); $i++) {
    $isi = $_NPM[$i];
    $_NPM[$i] = "'$isi'";
  }
  $__npm = implode(',', $_NPM);*/
  $jmlawal = $_REQUEST['jmlawal'];
  $MenjadiDosen = $_REQUEST['MenjadiDosen'];
  $n = 0;
  for ($i = 1; $i <= $jmlawal; $i++) {
    $MhswID = $_REQUEST['MhswID_'.$i];
    if (!empty($MhswID)){
      $n++;
      // simpan
      $s = "update mhsw set PenasehatAkademik='$MenjadiDosen' where MhswID = $MhswID";
      $r = _query($s);
    }
  }
  echo Konfirmasi("Penggantian PA Berhasil",
  "Penggantian Pembimbing Akademik telah berhasil.<br />
  Berikut adalah daftar NPM mahasiswa yang berhasil diubah PA-nya:<br />
  <b>$n</b> Mahasiswa
  <hr size=1 color=silver>
  Pilihan: <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=dosen.pembimbing'\">");
} 

function PAAwal() {
  TampilkanPARentangNPM();
  TampilkanUbahPA();
}

// *** Parameters ***
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');
$DariDosen = GetSetVar('DariDosen');
$thnsms = GetSetVar('thnsms');
$MenjadiDosen = GetSetVar('MenjadiDosen');
$DosenID = GetSetVar('DosenID');
$gos = (empty($_REQUEST['gos']))? "PAAwal" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Set Pembimbing Akademik");
$gos();

?>
