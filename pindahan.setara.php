<?php
// Author: Emanuel Setio Dewo
// 10 Juli 2006
// www.sisfokampus.net

// *** Functions ***
function TampilkanHeaderMPID($m) {
  echo "<p><table class=box cellspacing=1>
  <tr><td class=inp>Nama Calon</td>
    <td class=ul><font size=+1>$m[Nama]</font></td>
    <td class=inp>No Penyetaraan</td>
    <td class=ul><font size=+1>$m[MhswPindahanID]</td></tr>
  <tr><td class=inp>Asal Perguruan Tinggi</td>
    <td class=ul>$m[AsalPT]</td>
    <td class=inp>Program Studi Asal</td>
    <td class=ul>$m[ProdiAsalPT]</td></tr>
  <tr>
    <td class=inp>IPK Asal PT</td>
    <td class=ul>$m[IPKAsalPT]</td>
    <td class=inp>NPM Asal PT</td>
    <td class=ul>$m[MhswIDAsalPT]</td>
  </tr>
  <tr>
    <td class=inp>Program</td>
    <td class=ul>$m[ProgramID] - $m[NamaPRG]</td>
    <td class=inp>Akan masuk ke Prodi</td>
    <td class=ul>$m[ProdiID] - $m[NamaPRD]</td></tr>
  <tr>
    <td class=inp>Jumlah Penyetaraan</td>
    <td class=ul>$m[JumlahSetara]</td>
    <td class=inp>Tahun Masuk</td>
    <td class=ul>$m[TahunID]</td>
    </tr>
  <tr>
    <td class=inp>Pilihan</td>
    <td class=ul colspan=3><input type=button name='Kembali' value='Kembali ke Daftar' onClick=\"location='?mnux=pindahan'\">
    <input type=button name='Edit' value='Edit Data Calon' onClick=\"location='?mnux=pindahan&md=0&gos=PindEdt&MhswPindahanID=$m[MhswPindahanID]&MPID=$m[MhswPindahanID]'\">
    </td></tr>
  </table></p>";
}
function DaftarPenyetaraan($m) {
  $s = "select mps.*, mk.Nama as NamaMK
    from mhswpindahansetara mps
    left outer join mk mk on mps.MKID=mk.MKID
    where MhswPindahanID='$m[MhswPindahanID]'
    order by mps.SetaraKode";
  $r = _query($s);
  echo "<p><a href='?mnux=pindahan.setara&gos=MKSTREDT&md=1'>Tambah Matakuliah</a></p>";
  echo "<p><a href='pindahan.setara.cetak.php?pin=$m[MhswPindahanID]&tahun=$_SESSION[tahun]&prodi=$_SESSION[prodi]' target=_blank>Cetak</a></p>";
  echo "<p><table class=box cellspacing=1>
    <tr>
      <th class=ttl colspan=6>Matakuliah Asli</th>
      <th></th>
      <th class=ttl colspan=5>Matakuliah $_SESSION[KodeID] (Setara)</th>
    </tr>
    <tr><th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Nilai</th>
    <th class=ttl>Grade</th>
    <th class=ul><img src='img/kanan.gif'></th>
    <th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Nilai</th>
    </tr>";
  $n = 0; $m = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $m += ($w['MKID'] == 0)? 0 : 1;
    $_m = ($w['MKID'] == 0)? "&nbsp" : $m;
    echo "<tr>
      <td class=inp>$n</td>
      <td class=ul>$w[SetaraKode]</td>
      <td class=ul>$w[SetaraNama]</td>
      <td class=ul align=right>$w[SetaraSKS]</td>
      <td class=ul align=right>$w[NilaiAkhir]</td>
      <td class=ul>$w[SetaraGrade]</td>
      
      <td class=ul><a href='?mnux=pindahan.setara&gos=MKSTREDT&md=0&KRSID=$w[KRSID]' title='Edit'><img src='img/edit.png'></a></td>
      <td class=inp>$_m</td>
      <td class=ul>$w[MKKode]</td>
      <td class=ul>$w[NamaMK]</td>
      <td class=ul align=right>$w[SKS]</td>
      <td class=ul>$w[GradeNilai]</td>
      </tr>";
  }
  echo "</table></p>";
}
function MKSTREDT($m) {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('mhswpindahansetara', "KRSID", $_REQUEST['KRSID'], '*');
    $jdl = "Edit Matakuliah yg Disetarakan";
  }
  else {
    $w = array();
    $w['KRSID'] = 0;
    $w['MhswPindahanID'] = $_SESSION['MPID'];
    $w['TahunID'] = '00000';
    $w['MKID'] = 0;
    $w['MKKode'] = '';
    $w['SKS'] = -1;
    $w['GradeNilai'] = '';
    $w['SetaraKode'] = '';
    $w['SetaraGrade'] = '';
    $w['SetaraNama'] = '';
    $w['SetaraSKS'] = 0;
    $w['NilaiAkhir'] = 0;
    $jdl = "Tambah Matakuliah yg Disetarakan";
  }
  CheckFormScript('SetaraKode,SetaraGrade,SetaraNama');
  TampilkanJudul($jdl);
  $optn = GetOption2("nilai", "concat(Nama, ' (', Bobot, ')')", "Bobot desc", $w['GradeNilai'],
    "ProdiID='$m[ProdiID]'", 'Nama');
  $kurid = GetaField('kurikulum', "NA='N' and ProdiID", $m['ProdiID'], 'KurikulumID');
  $optm = GetOption2("mk", "concat(MKKode, ' - ', Nama, ' (', SKS, ')')", 'MKKode',
    $w['MKID'], "KurikulumID=$kurid", 'MKID');
  echo "<p><table class=box cellspacing=1>
  <form action='?' name='data' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='gos' value='MKSTRSAV'>
  <input type=hidden name='md' value=$md>
  <input type=hidden name='MPID' value='$w[MhswPindahanID]'>
  <input type=hidden name='KRSID' value='$w[KRSID]'>
  
  <tr><th class=ttl colspan=2>Matakuliah Asli</th>
    <th class=ul><img src='img/kanan.gif'></th>
    <th class=ttl colspan=2>Matakuliah $_SESSION[KodeID] (Setara)</th></tr>
  <tr><td class=inp>Kode Asli</td>
    <td class=ul><input type=text name='SetaraKode' value='$w[SetaraKode]' size=20 maxlength=30></td>
    <td class=ul><img src='img/kanan.gif'></td>
    <td class=inp>Kode MK</td>
    <td class=ul><select name='MKID'>$optm</select></td>
    </tr>
  <tr><td class=inp>Nama MK</td>
    <td class=ul><input type=text name='SetaraNama' value='$w[SetaraNama]' size=30 maxlength=50></td>
    <td></td>
    <td></td>
    <td></td>
    </tr>
  <tr><td class=inp>SKS Asli</td>
    <td class=ul><input type=text name='SetaraSKS' value='$w[SetaraSKS]' size=10 maxlength=5>
    <td></td>
    <td></td>
    <td></td>
    </tr>
  <tr><td class=inp>Nilai Asli</td>
    <td class=ul><input type=text name='NilaiAkhir' value='$w[NilaiAkhir]' size=10 maxlength=5>
    <td></td>
    <td></td>
    <td></td>
    </tr>
  <tr><td class=inp>Grade Nilai Asli</td>
    <td class=ul><input type=text name='SetaraGrade' value='$w[SetaraGrade]' size=10 maxlength=5>
    <td class=ul><img src='img/kanan.gif'></td>
    <td class=inp>Grade</td>
    <td class=ul><select name='GradeNilai'>$optn</select></td>
    </tr>
  <tr><td class=ul colspan=5>
    <input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=pindahan.setara'\"></td></tr>
  </form></table></p>";
}
function MKSTRSAV($m) {
  $md = $_REQUEST['md']+0;
  $MPID = $_REQUEST['MPID'];
  $KRSID = $_REQUEST['KRSID'] +0;
  $MKID = $_REQUEST['MKID']+0;
  if ($MKID == 0) {
    $MKKode = '';
    $SKS = 0;
    $GradeNilai = '';
  }
  else {
    $mk = GetFields('mk', 'MKID', $MKID, 'MKKode, SKS, Nama');
    $MKKode = $mk['MKKode'];
    $SKS = $mk['SKS'];
    $GradeNilai = $_REQUEST['GradeNilai'];
  }
  $SetaraKode = $_REQUEST['SetaraKode'];
  $SetaraGrade = $_REQUEST['SetaraGrade'];
  $SetaraNama = sqling($_REQUEST['SetaraNama']);
  $SetaraSKS = $_REQUEST['SetaraSKS']+0;
  $NilaiAkhir = $_REQUEST['NilaiAkhir']+0;
  if ($md == 0) {
    $ada = GetaField('mhswpindahansetara', "MhswPindahanID=$MPID and KRSID<>$KRSID and MKID",
      $MKID, "KRSID");
    if (empty($ada)) {
      $s = "update mhswpindahansetara set MKID=$MKID, MKKode='$MKKode',
        SKS='$SKS', NilaiAkhir=$NilaiAkhir, GradeNilai='$GradeNilai',
        SetaraKode='$SetaraKode', SetaraNama='$SetaraNama',
        SetaraGrade='$SetaraGrade', SetaraSKS='$SetaraSKS',
        LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
        where KRSID=$KRSID";
      $r = _query($s);
      JumlahSetara($m);
      echo "<script>window.location= '?mnux=pindahan.setara'</script>";
    }
    else echo ErrorMsg("Gagal Disimpan",
      "Matakuliah hasil penyetaraan: <b>$mk[Nama]</b> <font size=+1>($MKKode)</font> sudah disetarakan.<br />
      Anda tidak dapat memasukkan matakuliah ini lebih dari 2x.
      <hr size=1 color=silver>
      Pilihan: <input type=button name='Kembali' value='Kembali' onClick=\"location='?pindahan.setara'\">");
  }
  else {
    $ada = GetaField('mhswpindahansetara', "MhswPindahanID=$MPID and SetaraKode",
      $SetaraKode, "KRSID");
    if (empty($ada)) {
      $s = "insert into mhswpindahansetara
        (MhswPindahanID, TahunID, MKID, MKKode, SKS,
        NilaiAkhir, GradeNilai,
        Setara, SetaraKode, SetaraNama,
        SetaraGrade, SetaraSKS,
        LoginBuat, TanggalBuat)
        values
        ($MPID, '0000', $MKID, '$MKKode', $SKS,
        $NilaiAkhir, '$GradeNilai',
        'Y', '$SetaraKode', '$SetaraNama',
        '$SetaraGrade', '$SetaraSKS',
        '$_SESSION[_Login]', now())";
      $r = _query($s);
      JumlahSetara($m);
      echo "<script>window.location= '?mnux=pindahan.setara'</script>";
    }
    else echo ErrorMsg("Gagal Ditambahkan",
      "Matakuliah asli: <b>$SetaraNama</b> <font size=+1>($SetaraKode)</font> telah terdaftar.<br />
      Anda tidak dapat memasukkan matakuliah ini lebih dari 2x.
      <hr size=1 color=silver>
      Pilihan: <input type=button name='Kembali' value='Kembali' onClick=\"location='?pindahan.setara'\">");
  }
}
function JumlahSetara($m) {
  $jml = GetaField("mhswpindahansetara", "MKID>0 and MhswPindahanID", $m['MhswPindahanID'], "count(*)")+0;
  $s = "update mhswpindahan set JumlahSetara=$jml where MhswPindahanID=$m[MhswPindahanID]";
  $r = _query($s);
}
function ExportKRSSetara() {
  $mhswstrID = $_REQUEST['mhswstrID'];
  $s = "select *
    from mhswpindahansetara
    where sudah=0 and MhswPindahanID = $mhswstrID";
  $r = _query($s); $jml = _num_rows($r);
  echo "<p>Jumlah data: <font size=+1>$jml</font></p>";
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    //$ada = GetaField('krs', "MhswID='$w[MhswID]' and JadwalID", $w[JadwalID], 'KRSID');
    //if (empty($ada)) {
      echo "<li>$w[MhswID] &raquo; $w[MKKode]
      </li>";
      // Export
      $s1 = "insert into krs
        (KHSID, MhswID, TahunID, JadwalID, 
        MKID, MKKode, SKS, StatusKRSID, Final,
        Harga, HargaStandar,
        GradeNilai, BobotNilai,
        Catatan,
        LoginBuat, TanggalBuat)
        values ('$w[KHSID]', '$w[MhswID]', '00000', '$w[JadwalID]',
        '$w[MKID]', '$w[MKKode]', '$w[SKS]', 'A', 'Y', 
        $w[Harga], '$w[HargaStandar]',
        '$w[GradeNilai]', $w[BobotNilai],
        '$w[Catatan]',
        '$w[LoginBuat]', now())
      ";
      echo "<pre>$s1</pre>";
      $r1 = _query($s1);
    
      // Set bahwa sudah di export
      $s2 = "update mhswpindahansetara set sudah=1 where KRSID=$w[KRSID]";
      $r2 = _query($s2);
      echo "<pre>$s2</pre>";
    //}
    //else echo "<li>$w[MhswID] &raquo; $w[KRSID]</li>";
  }
  $s3 = "update mhswpindahan set Sudah=1 where MhswPindahanID=$mhswstrID";
  $r3 = _query($s3);
  echo "</ol>";
}

// *** Parameters ***
$MPID = GetSetVar('MPID');
$_mpid = GetFields("mhswpindahan mp 
  left outer join perguruantinggi pt on mp.AsalPT=pt.PerguruanTinggiID
  left outer join program prg on mp.ProgramID=prg.ProgramID
  left outer join prodi prd on mp.ProdiID=prd.ProdiID",
  "mp.MhswPindahanID", $MPID, "mp.*, pt.Nama as NamaPT, prg.Nama as NamaPRG, prd.Nama as NamaPRD");
$gos = (empty($_REQUEST['gos']))? "DaftarPenyetaraan" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Penyetaraan Matakuliah Mahasiswa");
if (!empty($_mpid)) {
  TampilkanHeaderMPID($_mpid);
  $gos($_mpid);
}
?>
