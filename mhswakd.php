<?php
// Author: Emanuel Setio Dewo
// 23 Feb 2006

// *** Functions ***
function CariMhsw() {
  if (!empty($_SESSION['crmhswid']) && !empty($_SESSION['crmhsw'])) CariMhsw1();
}
function CariMhsw1() {
  $arrkey = array('NPM'=>'MhswID', 'Nama'=>'Nama');
  $_key = $_SESSION['crmhsw'];
  $s = "select m.MhswID, m.Nama, m.ProdiID,
    sm.Nama as STT, sm.Nilai, sm.Keluar, sm.Def
    from mhsw m
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    where m.$arrkey[$_key] like '%$_SESSION[crmhswid]%'
    order by $arrkey[$_key] ";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>NIM</th>
    <th class=ttl>Nama Mhsw</th>
    <th class=ttl>Prodi</th>
    <th class=ttl>Status</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    if (strpos(",".$_SESSION['_ProdiID'], ",$w[ProdiID],") === false) {
      $c = 'class=nac';
      $lnk = $w['MhswID'];
    }
    else {
      $c = "class=ul";
      $lnk = "<a href='?mnux=mhswakd&mhswid=$w[MhswID]&gos=MhswAkdEdt'><img src='img/edit.png'> $w[MhswID]</a>";
    }
    if ($w['Keluar'] == 'Y') {
      $k = 'class=wrn';
      $lnk = $w['MhswID'];
    }
    else {
      $k = $c;
    }
    echo "<tr>
    <td $k align=right>$lnk</td>
    <td $k>$w[Nama]</td>
    <td $k>$w[ProdiID]</td>
    <td $k>$w[STT]</td>
    </tr>";
  }
  echo "</table></p>";
}
function HeaderMhsw($w) {
  // ambil tahun aktif
  $TahunAktif = GetaField('tahun',
    "KodeID='$_SESSION[KodeID]' and NA='N' and ProgramID='$w[ProgramID]' and ProdiID",
    $w['ProdiID'], "max(TahunID)");
  $prg = GetaField('program', 'ProgramID', $w['ProgramID'], 'Nama');
  $prd = GetaField('prodi', 'ProdiID', $w['ProdiID'], 'Nama');
  // tampilkan data mshw
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp>N.P.M.</td><td class=ul><b>$w[MhswID]</td></tr>
  <tr><td class=inp>Nama</td><td class=ul><b>$w[Nama]</td></tr>
  <tr><td class=inp>Program</td><td class=ul><b>$w[ProgramID]- $prg</td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul><b>$w[ProdiID]- $prd</td></tr>
  <tr><td class=inp>Tahun Akademik Aktif</td><td class=ul><b>$TahunAktif</td></tr>
  </table></p>";
  return $TahunAktif;
}
function MhswAkdEdt() {
  $w = GetFields('mhsw', 'MhswID', $_SESSION['mhswid'], '*');
  // Tampilkan Data Mhsw
  $TahunAktif = HeaderMhsw($w);
  // Tampilkan tambah sesi
  $NextSesi = GetaField("khs", "MhswID", $w['MhswID'], "max(Sesi)")+1;
  $DefStatus = GetaField('statusmhsw', 'Def', 'Y', 'StatusMhswID');
  $optstt = GetOption2('statusmhsw', "concat(StatusMhswID, ' - ', Nama)", 'StatusMhswID', $DefStatus, '', 'StatusMhswID');
  $_modTambahTahunAkd = "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=ul colspan=7><b>Tambahkan Sesi</b></td></tr>
  
  <form action='?' method=GET>
  <input type=hidden name='mnux' value='mhswakd'>
  <input type=hidden name='gos' value='MhswAkdAdd'>
  <input type=hidden name='mhswid' value='$w[MhswID]'>
  <tr><td class=inp1>Tahun Akd. :</td>
    <td class=ul><input type=text name='TahunID' value='$TahunAktif' size=8 maxlength=10></td>
    <td class=inp1>Sesi :</td>
    <td class=ul><input type=text name='Sesi' value='$NextSesi' size=5 maxlength=5></td>
    <td class=inp1>Status :</td>
    <td class=ul><select name='StatusMhswID'>$optstt</select></td>
    <td class=ul><input type=submit name='Tambahkan' value='Tambahkan'></td>
  </tr>
  </form></table></p>";
  DaftarSesiMhsw($w, $TahunAktif);
}
function DaftarSesiMhsw($m, $TahunAktif) {
  $s = "select k.*, sm.Nama as STT, sm.Nilai,
    format(k.IP, 2) as IP,
    format(k.Biaya, 0) as BIA,
    format(k.Potongan, 0) as POT,
    format(k.Bayar, 0) as BYR,
    format(k.Tarik, 0) as TRK
    from khs k
    left outer join statusmhsw sm on k.StatusMhswID=sm.StatusMhswID
    where k.MhswID='$m[MhswID]'
    order by k.TahunID";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><th class=ttl colspan=2>Sesi</th>
  <th class=ttl>Tahun</th>
  <th class=ttl>Status</th>
  <th class=ttl>Ubah Status</th>
  <th class=ttl>Max SKS</th>
  <th class=ttl title='Cetak Kartu Studi Semester'>KSS</th>
  <th class=ttl>KRS</th>
  <th class=ttl>IP</th>
  <th class=ttl>Biaya</th>
  <th class=ttl>Potongan</th>
  <th class=ttl>Bayar</th>
  <th class=ttl>Tarikan</th>
  <th class=ttl>Hitung<br />Ulang</th>
  </tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['Nilai'] <= 0)? 'class=nac' : 'class=ul';
    $_where = (strpos(".1.20.50.40.", '.'.$_SESSION['_LevelID'].'.') === false)? "Nilai=1" : '';
    $optstt = GetOption2('statusmhsw', "concat(StatusMhswID, ' - ', Nama)", 'StatusMhswID',
      $w['StatusMhswID'], $_where, 'StatusMhswID');
    if ($TahunAktif == $w['TahunID']) {
      $edit = "<form action='?' method=POST>
      <input type=hidden name='mnux' value='mhswakd'>
      <input type=hidden name='gos' value='UbahStatus'>
      <input type=hidden name='crmhswid' value='$m[MhswID]'>
      <input type=hidden name='khsid' value='$w[KHSID]'>
      <td $c>$w[STT]</td>
      <td $c><select name='StatusMhswID'>$optstt</select></td>
      <td $c><input type=text name='MaxSKS' value='$w[MaxSKS]' size=2 maxlength=2>
      <input type=submit name='Simpan' value='Simpan'></td>
      </form>";
      $krs = "<a href='?mnux=krs&mhswid=$w[MhswID]&tahun=$w[TahunID]'><img src='img/check.gif'></a>";
      $ctk1 = "<a href='?mnux=kss&gos=cekkss&tahun=$w[TahunID]&mhswid=$w[MhswID]&khsid=$w[KHSID]'>
        <img src='img/printer.gif'></a>";
    }
    else {
      $stt = GetaField('statusmhsw', 'StatusMhswID', $w['StatusMhswID'], 'Nama');
      $edit = "<td $c>$stt</td><td $c align=center>&times;</td><td $c>$w[MaxSKS]</td>";
      $krs = "&nbsp;";
      $ctk1 = "&times;";
    }
    echo "<tr><td class=ul><a href='?mnux=mhswakd&gos=MhswSesiDel&mhswid=$w[MhswID]&khsid=$w[KHSID]'><img src='img/del.gif'></a></td>
    <td class=ul><b>$w[Sesi]</b></td>
    <td $c>$w[TahunID]</td>
    $edit
    <td class=ul align=center>$ctk1</td>
    <td $c align=center>$krs</td>
    <td $c align=right>$w[IP]</td>
    <td $c align=right>$w[BIA]</td>
    <td $c align=right>$w[POT]</td>
    <td $c align=right>$w[BYR]</td>
    <td $c align=right>$w[TRK]</td>
    <td class=ul align=center><a href='?mnux=mhswakd&gos=MhswAkdEdt&mhswid=$w[MhswID]&khsid=$w[KHSID]&slnt=mhswkeu.lib&slntx=HitungBiayaBayarMhsw'><img src='img/check.gif'></a></td>
    </tr>";
  }
  echo "</table></p>";
}
function UbahStatus() {
  $khsid = $_REQUEST['khsid'];
  $StatusMhswID = $_REQUEST['StatusMhswID'];
  $MaxSKS = $_REQUEST['MaxSKS']+0;
  if (!empty($StatusMhswID)) {
    $s = "update khs set StatusMhswID='$StatusMhswID', MaxSKS=$MaxSKS where KHSID='$khsid' ";
    $r = _query($s);
  }
  MhswAkdEdt();
}
function MhswAkdAdd() {
  $ada = GetFields('khs', "TahunID='$_REQUEST[TahunID]' and MhswID", $_REQUEST['mhswid'], '*');
  if (empty($ada)) {
    // Cek apakah statusnya kini menjadi "T" (Tunggu ujian)?
    if ($_REQUEST['StatusMhswID'] == 'T') {
      $tunggu = GetFields('khs', "StatusMhswID='T' and MhswID", $_REQUEST['mhswid'], '*');
      if (empty($tunggu)) {
        $w = GetFields('mhsw', 'MhswID', $_REQUEST['mhswid'], '*');
        $s = "insert into khs (TahunID, KodeID, ProgramID, ProdiID, 
          MhswID, Sesi, StatusMhswID,
          TanggalBuat, LoginBuat)
          values ('$_REQUEST[TahunID]', '$_SESSION[KodeID]', '$w[ProgramID]', '$w[ProdiID]',
          '$w[MhswID]', '$_REQUEST[Sesi]', '$_REQUEST[StatusMhswID]',
          now(), '$_SESSION[_LoginID]')";
        $r = _query($s);
        echo "<script>window.location='?mnux=mhswakd&gos=MhswAkdEdt';</script>";
      }
      else echo ErrorMsg("Gagal Disimpan",
        "Mahasiswa telah berstatus <b>Tunggu Ujian (T)</b> pada tahun $_REQUEST[TahunID].<br />
        Sistem hanya memperbolehkan 1x status Tunggu Ujian.
        <hr size=1 color=silver>
        Pilihan: <a href='?mnux=mhswakd&gos=MhswAkdEdt'>Kembali</a>");
    }
  }
  else {
    echo ErrorMsg("Gagal Disimpan",
      "Mahasiswa telah mengikuti tahun ajaran <b>$_REQUEST[TahunID]</b>.
      <hr size=1>
      Pilihan: <a href='?mnux=mhswakd&gos=MhswAkdEdt'>Kembali</a>");
  }
  
}
function MhswSesiDel() {
  $w = GetFields('mhsw', 'MhswID', $_REQUEST['mhswid'], '*');
  $khs = GetFields('khs', 'KHSID', $_REQUEST['khsid'], '*');
  if ($khs['Biaya'] + $khs['Bayar'] + $khs['Potongan'] + $khs['Tarik'] > 0) {
    echo ErrorMsg("Sesi Tidak Dapat Dihapus",
    "Anda tidak dapat menghapus Sesi <b>$khs[TahunID]</b> dari mahasiswa <b>$w[Nama]</b> ($w[MhswID]) karena telah ada
    transaksi keuangan.
    <hr size=1>
    Pilihan: <a href='?mnux=mhswakd&mhswid=$w[MhswID]&gos=MhswAkdEdt'>Batal</a>");
  }
  else echo Konfirmasi("Konfirmasi Hapus Sesi",
    "Apakah Anda yakin akan menghapus Sesi mahasiswa ini?<br />
    <table class=bsc cellspacing=1 cellpadding=4 width=100%>
    <tr><td class=ul>NIM :</td><td class=ul><b>$w[MhswID]</b></td></tr>
    <tr><td class=ul>Nama :</td><td class=ul><b>$w[Nama]</b></td></tr>
    <tr><td class=ul>Sesi :</td><td class=ul><b>$khs[Sesi]</b></td></tr>
    <tr><td class=ul>Tahun Akd. :</td><td class=ul><b>$khs[TahunID]</b></td></tr>
    </table>
    <hr size=1>
    Pilihan: <a href='?mnux=mhswakd&gos=MhswSesiDel1&mhswid=$w[MhswID]&khsid=$_REQUEST[khsid]'>Hapus</a> |
    <a href='?mnux=mhswakd&mhswid=$w[MhswID]&gos=MhswAkdEdt'>Batal</a>");
}
function MhswSesiDel1() {
  $s = "delete from khs where KHSID='$_REQUEST[khsid]' ";
  $r = _query($s);
  MhswAkdEdt();
}

// *** Parameters ***
$crmhsw = GetSetVar('crmhsw', 'NPM');
$crmhswid = GetSetVar('crmhswid');
$gos = (empty($_REQUEST['gos']))? 'CariMhsw' : $_REQUEST['gos'];
$mhswid = GetSetVar('mhswid');

// *** Main ***
TampilkanJudul("Data Akademik Mahasiswa");
TampilkanPencarianMhsw('mhswakd', 'CariMhsw');
$gos();
?>
