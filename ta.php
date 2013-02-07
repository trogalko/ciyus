<?php
// Author: Emanuel Setio Dewo
// 23 April 2006

// *** Functions ***
function DftrTA($mhsw) {
  // Tampilkan menu tambah
  
  if ($mhsw['ProdiID'] != '11' && $mhsw['ProdiID'] != '10') {
    echo "<p><a href='?mnux=ta&gos=TAEdt&md=1&crmhswid=$mhsw[MhswID]'><img src='img/edit.png'>
      Daftarkan tugas akhir</a></p>";
  
    $s = "select t.*, concat(d.Nama, ', ', d.Gelar) as DSN
      from ta t
        left outer join dosen d on t.Pembimbing=d.Login
      where t.MhswID='$mhsw[MhswID]'
      order by t.TglMulai desc";
    $r = _query($s); $n = 0;
    if (_num_rows($r) > 0) {
      $n++;
      echo "<p><table class=box cellspacing=1 cellpadding=4>
        <tr><th class=ttl>#</th>
        <th class=ttl>Tahun</th>
        <th class=ttl>Waktu</th>
        <th class=ttl>Judul</th>
        <th class=ttl colspan=2>Pembimbing</th>
        <th class=ttl>Batal?</th>
        <th class=ttl>Ujian</th>
        <th class=ttl colspan=2>Penguji</th>
        <th class=ttl title='Cetak Undangan Dosen Penguji'>Und</th>
        <th class=ttl>Lulus?</th>
        </tr>";
      while ($w = _fetch_array($r)) {
         // data TA
        $TM = FormatTanggal($w['TglMulai']);
        $TS = FormatTanggal($w['TglSelesai']);
        $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
        $pemb2 = AmbilPembimbing($w['TAID'], 0);
        // data ujian
        $TU = FormatTanggal($w['TglUjian']);
        $_TU = ($TU == '00/00/0000')? "&nbsp;" : $TU;
        $Penguji = GetaField('dosen', "Login", $w['Penguji'], "concat(Nama, ', ', Gelar)");
        $Penguji = (empty($Penguji))? "&nbsp;" : $Penguji;
        $peng2 = AmbilPembimbing($w['TAID'], 1);
        // Jika batal
        if ($w['NA'] == 'Y') {
          $lls = "<img src='img/kali.png'>";
        }
        else {
          $lls = ($w['Lulus'] == 'Y')? "<img src='img/Y.gif'>" : 
            "<a href='?mnux=ta&gos=TALLS&taid=$w[TAID]&crmhswid=$mhsw[MhswID]'><img src='img/$w[Lulus].gif'><br />Nilai</a>";
        }
      
        echo "<tr><td class=inp rowspan=2>$n</td>
          <td $c rowspan=2><a href='?mnux=ta&gos=TAEdt&taid=$w[TAID]&crmhswid=$mhsw[MhswID]&md=0'><img src='img/edit.png'>
            $w[TahunID]</a></td>
          <td $c rowspan=2>$TM <br /> $TS</td>
          <td $c rowspan=2>$w[Judul]</td>
          <td $c colspan=2>$w[DSN]</td>
        
          <td class=ul align=center rowspan=2><img src='img/book$w[NA].gif'></td>
          <td $c rowspan=2><a href='?mnux=ta&gos=TAUjn&taid=$w[TAID]&crmhswid=$mhsw[MhswID]'><img src='img/check.gif'> Set Ujian</a><br />
          $_TU</td>
          <td $c colspan=2>$Penguji</td>
          <td class=ul rowspan=2 align=center>
            <a href='ta.und.php?taid=$w[TAID]'><img src='img/printer.gif'></a></td>
          <td $c rowspan=2 align=center>$lls</td>
          </tr>
        
          <tr>
          <td class=inp1 title='Edit Pembimbing Pendamping'><a href='?mnux=ta.dsn&taid=$w[TAID]&tipta=0'><img src='img/edit.png'></a></td>
          <td $c nowrap>$pemb2&nbsp;</td>
          <td class=inp1 title='Edit Penguji Pendamping'><a href='?mnux=ta.dsn&taid=$w[TAID]&tipta=1'><img src='img/edit.png'></a></td>
          <td $c nowrap>$peng2&nbsp;</td>
          </tr>";
      }
      echo "</table></p>";
    }
  } else {
    CheckMhswFK($mhsw);
  }
}
// Cek apakah mhsw sudah ambil TA/Skripsi/Tesis
function CheckAmbilTA($mhsw) {
  // Ambil jenis matakuliah
  $_ta = GetArrayTable("select JenisPilihanID 
    from jenispilihan where TA='Y' and ProdiID='$mhsw[ProdiID]' 
    order by JenisPilihanID",
    "JenisPilihanID", "JenisPilihanID");
  //$_ta = (empty($_ta))? '0' : $_ta;
  // Apakah matakuliah sudah pernah diambil?
  /*$sdh = GetaField("krs krs
    left outer join mk mk on krs.MKID=mk.MKID",
    "mk.JenisPilihanID in ($_ta) and krs.MhswID", $mhsw['MhswID'], "count(krs.KRSID)")+0; */
  $sdh = GetaField("krs krs
    left outer join mk mk on krs.MKID=mk.MKID
    left outer join jenispilihan jp on mk.JenisPilihanID=jp.JenisPilihanID",
    "jp.TA='Y' and krs.MhswID", $mhsw['MhswID'], "count(krs.KRSID)")+0;
  //echo "<h1>$sdh</h1>";
  if ($sdh==0) {
    echo "<p><table class=box cellspacing=1 cellpadding=4 width=100%>
    <tr><td class=wrn>Mahasiswa belum mengambil matakuliah TA/Skripsi/Tesis/Disertasi.</td></tr>
    </table></p>";
    return false;
  }
  else {
    $a = "<p><table class=box cellspacing=1 cellpadding=4 width=100%>
      <tr><td class=ul>Mahasiswa telah mengambil matakuliah TA/Skripsi/Tesis/Disertasi.
      Mahasiswa boleh mendaftarkan tugas akhir.</td></tr>
      </table></p>";
    return true;
  }
}

function CheckMhswFK($mhsw){
  $s = "select krs.SKS
    from krsprc krs
      left outer join mk mk on krs.MKID=mk.MKID
      left outer join nilai n on krs.GradeNilai=n.Nama
    where krs.MhswID='$mhsw[MhswID]' 
      and krs.GradeNilai not in ('-', '')
      and n.Lulus = 'Y'
    group by krs.MKKode";
	
	$r = _query($s);
	
	while ($w = _fetch_array($r)){
	 $krs += $w['SKS'];
	}
	
	$SKSLULUS = GetaField('prodi', 'ProdiID', $mhsw['ProdiID'], 'TotalSKS')+0;
	if ($krs >= $SKSLULUS) {
	 $ada = GetFields('ta', 'MhswID', $mhsw['MhswID'], '*');
	 if (empty($ada))
	   echo Konfirmasi("Proses Mahasiswa", "Mahasiswa dengan NIM : <b>$mhsw[MhswID]</b> telah memenuhi SKS Minimal Kelulusan. </br>
                      Proses mahasiswa untuk SK Yudisium : <a href=?mnux=ta&gos=prsFK&mhsw=$mhsw[MhswID]><img src=./img/gear.gif style=position:relative;top:8;></img> Proses</a>");
     else echo ErrorMsg("Data sudah diProses", "Data untuk Mahasiswa dengan NIM : $mhsw[MhswID] sudah pernah diproses.</br>
                    Pilihan : <a href=?mnux=ta>Kembali</a>");
  } else {
    echo "<p><table class=box cellspacing=1 cellpadding=4 width=100%>
    <tr><td class=wrn>Mahasiswa dengan NIM : $mhsw[MhswID] belum memenuhi SKS Minimal Kelulusan. </br>
    </td></tr>
    </table></p>";
    //return FALSE;
  }
}

function prsFK($mhsw){
  $mhsw = $_REQUEST['mhsw'];
  
  $s = "insert into ta (TahunID, TglDaftar, TglMulai, TglSelesai, MhswID, Lulus,
      Judul, Keterangan, Pembimbing, NA,
      LoginBuat, TanggalBuat)
      values ('$TahunID', now(), NOW(), NOW(), '$mhsw', 'Y',
      '$Judul', 'Mahasiswa FK Tanpa Tugas Akhir', '$Pembimbing', 'N',
      '$_SESSION[_Login]', now())";
  $r = _query($s);
  $taid = GetLastID();
    
  $s2 = "update mhsw set TAID='$taid' where MhswID='$mhsw[MhswID]' ";
  $r2 = _query($s2);
  
  echo Konfirmasi("Data berhasil disimpan", "Data TA untuk Mahasiswa dengan NIM : <b>$mhsw</b> telah berhasil disimpan. <br />
                  Pilihan : <a href=?mnux=ta>Kembali</a>");
}

function AmbilPembimbing($taid, $tp=0) {
  $s = "select td.*, concat(d.Nama, ', ', d.Gelar) as DSN
    from tadosen td
      left outer join dosen d on td.DosenID=d.Login
    where td.TAID='$taid' and td.Tipe='$tp'
    order by d.Nama";
  $r = _query($s);
  $a = array();
  while ($w = _fetch_array($r)) {
    $a[] = "- ". $w['DSN'];
  }
  return implode('<br />', $a);
}
function TAEdt($mhsw) {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('ta', 'TAID', $_REQUEST['taid'], '*');
    $jdl = "Edit Tugas Akhir";
  }
  else {
    $w = array();
    $w['TahunID'] = '';
    $w['MhswID'] = $mhsw['MhswID'];
    $w['TglDaftar'] = date('Y-m-d');
    $w['TglMulai'] = date('Y-m-d');
    $w['TglSelesai'] = date('Y-m-d');
    $w['Judul'] = '';
    $w['Pembimbing'] = '';
    $w['Keterangan'] = '';
    $w['NA'] = 'N';
    $jdl = "Daftarkan Tugas Akhir";
  }
  $TM = GetDateOption($w['TglMulai'], 'TM');
  $TS = GetDateOption($w['TglSelesai'], 'TS');
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  $pemb = GetOption2('dosen', "concat(Nama, ', ', Gelar, ' (', Login, ')')", 'Nama', $w['Pembimbing'], 
    "INSTR(ProdiID, '.$mhsw[ProdiID].')>0", 'Login');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='ta'>
  <input type=hidden name='gos' value='TASav'>
  <input type=hidden name='crmhswid' value='$mhsw[MhswID]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='taid' value='$w[TAID]'>
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Tahun Akd</td><td class=ul><input type=text name='TahunID' value='$w[TahunID]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>Tgl Mulai</td><td class=ul>$TM</td></tr>
  <tr><td class=inp>Tgl Harus Selesai</td><td class=ul>$TS</td></tr>
  <tr><td class=inp>Judul</td><td class=ul><textarea name='Judul' cols=30 rows=2>$w[Judul]</textarea></td></tr>
  <tr><td class=inp>Keterangan</td><td class=ul><textarea name='Keterangan' cols=30 rows=4>$w[Keterangan]</textarea></td></tr>
  <tr><td class=inp>Pembimbing Utama</td><td class=ul><select name='Pembimbing'>$pemb</select></td></tr>
  <tr><td class=inp>Batalkan?</td><td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=ta&gos='\"></td></tr>
  </form></table></p>";
}
function TASav($mhsw) {
  $md = $_REQUEST['md']+0;
  $TahunID = $_REQUEST['TahunID'];
  $TglMulai = "$_REQUEST[TM_y]-$_REQUEST[TM_m]-$_REQUEST[TM_d]";
  $TglSelesai = "$_REQUEST[TS_y]-$_REQUEST[TS_m]-$_REQUEST[TS_d]";
  $Judul = sqling($_REQUEST['Judul']);
  $Judul = strtoupper($Judul);
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $Pembimbing = $_REQUEST['Pembimbing'];
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  // Simpan
  if ($md == 0) {
    $taid = $_REQUEST['taid'];
    $s = "update ta set TahunID='$TahunID', TglMulai='$TglMulai', TglSelesai='$TglSelesai',
      Judul='$Judul', Keterangan='$Keterangan', Pembimbing='$Pembimbing', NA='$NA',
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where TAID='$taid' ";
    $r = _query($s);
  }
  else {
    $s = "insert into ta (TahunID, TglDaftar, TglMulai, TglSelesai, MhswID,
      Judul, Keterangan, Pembimbing, NA,
      LoginBuat, TanggalBuat)
      values ('$TahunID', now(), '$TglMulai', '$TglSelesai', '$mhsw[MhswID]',
      '$Judul', '$Keterangan', '$Pembimbing', '$NA',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
    $taid = GetLastID();
  }
  // update yang lain menjadi tidak aktif
  if ($NA == 'N') {
    $s1 = "update ta set NA='Y' where MhswID='$mhsw[MhswID]' and TAID<>$taid";
    $r1 = _query($s1);
    // update data mshw
    $s2 = "update mhsw set TAID='$taid' where MhswID='$mhsw[MhswID]' ";
    $r2 = _query($s2);
  }
  DftrTA($mhsw);
}
function TAUjn($mhsw) {
  $w = GetFields('ta', 'TAID', $_REQUEST['taid'], '*');
  //$w['TglUjian'] = ($w['TglUjian'] == "0000-00-00")? date('Y-m-d') : $w['TglUjian'];
  $TU = GetDateOption($w['TglUjian'], 'TU');
  $peng = GetOption2('dosen', "concat(Nama, ', ', Gelar)", 'Nama', $w['Penguji'], 
    "INSTR(ProdiID, '.$mhsw[ProdiID].')>0", 'Login');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='taujn' method=POST>
  <input type=hidden name='mnux' value='ta'>
  <input type=hidden name='gos' value='TAUjnSav'>
  <input type=hidden name='taid' value='$_REQUEST[taid]'>
  
  <tr><th class=ttl colspan=2>Jadwal Ujian</th></tr>
  <tr><td class=inp>Tanggal Ujian</td><td class=ul>$TU</td></tr>
  <tr><td class=inp>Penguji Utama</td><td class=ul><select name='Penguji'>$peng</select></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=ta'\"></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='BatalUjian' value='Batalkan Ujian'></td></tr>
  </form></table></p>";
}
function TAUjnSav($mhsw) {
  $taid = $_REQUEST['taid'];
  $TU = "$_REQUEST[TU_y]-$_REQUEST[TU_m]-$_REQUEST[TU_d]";
  $Penguji = $_REQUEST['Penguji'];
  $s = "update ta set TglUjian='$TU', Penguji='$Penguji' where TAID='$taid' ";
  $r = _query($s);

  DftrTA($mhsw);
}
function TALLS($mhsw) {
  $taid = $_REQUEST['taid'];
  $ta = GetFields('ta', "TAID", $taid, "*");
  $mk = GetMKTAMhsw($mhsw);
  $optnil = GetOption2("nilai", "concat(Nama, ' (', Bobot, ')')", "Bobot desc", $ta['Nama'], 
    "ProdiID='$mhsw[ProdiID]'", 'Nama');
  $optlls = GetOption2("statuslulus", "concat(StatusLulusID, ' - ', Nama)", "Nama",
    $ta['StatusLulusID'], '', "StatusLulusID"); 
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='talls' method=POST>
  <input type=hidden name='mnux' value='ta'>
  <input type=hidden name='taid' value='$taid'>
  <input type=hidden name='crmhswid' value='$mhsw[MhswID]'>
  <input type=hidden name='gos' value='TALLSSav'>
  
  <tr><th class=ttl colspan=2>Kelulusan Ujian Akhir</th></tr>
  <tr><td class=inp>Mahasiswa</td><td class=ul>$mhsw[MhswID], $mhsw[Nama]</td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul>$mhsw[PRG], $mhsw[PRD]</td></tr>
  <tr><td class=inp>Status Kelulusan</td><td class=ul><select name='StatusLulusID'>$optlls</select></td></tr>
  <tr><td class=inp>Matakuliah</td><td class=ul>$mk</td></tr>
  <tr><td class=inp>Nilai Ujian</td><td class=ul><select name='GradeNilai'>$optnil</select></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=ta'\"></td></tr>
  </form></table></p>";
}
function GetMKTAMhsw($mhsw) {
  $s = "select krs.KRSID, mk.MKKode, mk.Nama, mk.SKS
    from krs krs
      left outer join mk mk on krs.MKID=mk.MKID
      left outer join jenispilihan jp on mk.JenisPilihanID=jp.JenisPilihanID
    where krs.MhswID='$mhsw[MhswID]'
      and krs.NA='N' and krs.StatusKRSID='A' and jp.TA='Y'
      and jp.ProdiID='$mhsw[ProdiID]'
    order by mk.MKKode";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  $a = '';
  if (_num_rows($r) == 1) {
    $w = _fetch_array($r);
    $a = "<input type=hidden name='KRSID' value='$w[KRSID]'>$w[MKKode], $w[Nama] ($w[SKS] SKS)";
  }
  else {
    while ($w = _fetch_array($r)) {
      $a .= "<option value='$w[KRSID]'>$w[MKKode], $w[Nama] ($w[SKS])</option>";
    }
    $a = "<select name='KRSID'>$a</select>";
  }
  return $a;
}
function TALLSSav($mhsw) {
  $taid = $_REQUEST['taid'];
  $KRSID = $_REQUEST['KRSID'];
  if (!empty($KRSID)) {
    $StatusLulusID = $_REQUEST['StatusLulusID'];
    $Lulus = GetaField("statuslulus", "StatusLulusID", $StatusLulusID, "Lulus");
    $GradeNilai = $_REQUEST['GradeNilai'];
    $Bobot = GetaField("nilai", "ProdiID='$mhsw[ProdiID]' and Nama", $GradeNilai, "Bobot");
    // Simpan
    $s = "update ta set StatusLulusID='$StatusLulusID', Lulus='$Lulus', 
      GradeNilai='$GradeNilai', BobotNilai='$Bobot'
      where TAID='$taid' ";
    $r = _query($s);
    // Update krs
    $s1 = "update krs set GradeNilai='$GradeNilai', BobotNilai='$Bobot', Final='Y'
      where KRSID='$KRSID' ";
    $r1 = _query($s1);
    // Tampilkan konfirmasi
  echo Konfirmasi("Koreksi Nilai Telah Dilakukan",
    "Koreksi nilai sudah dilakukan.<br />
    Di bawah ini akan dilakukan proses perhitungan IPK/IPS bagi mahasiswa yang bersangkutan.");
  // PROSES
  $prd = $mhsw['ProdiID'];
  $_SESSION["IPK$prd"] = 1;
  $_SESSION["IPK-MhswID$prd"."0"] = $MhswID;
  $_SESSION["IPK-KHSID$prd"."0"] = $krs['KHSID'];
  $max = $_SESSION['IPK'.$prodi];
  $_SESSION["IPK$prd".'POS'] = 0;
  echo "<p><IFRAME SRC='prc.ipk.go.php?gos=PRC2&tahun=$TahunID&prodi=$prd&prid=$mhsw[ProgramID]' width=300 height=75 frameborder=0>
  </IFRAME></p>";
  }
  else echo ErrorMsg("Gagal Simpan",
    "Tidak dapat menyimpan data kelulusan.");
  DftrTA($mhsw);
}

// *** Parameters ***
$crmhswid = GetSetVar('crmhswid');
$gos = (empty($_REQUEST['gos']))? 'DftrTA' : $_REQUEST['gos'];
$tipta = GetSetVar('tipta', 0);

// *** Main ***
TampilkanJudul("Pendaftaran Tugas Akhir/Skripsi/Tesis/Disertasi");
TampilkanPencarianMhsw('ta', 'DftrTA', 1);
if (!empty($crmhswid)) {
  $mhsw = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join bipot bpt on m.BIPOTID=bpt.BIPOTID",
    'm.MhswID', $crmhswid,
    "m.*, prg.Nama as PRG, prd.Nama as PRD, bpt.Nama as BPT");
  
  if ($mhsw['ProdiID'] != '11' && $mhsw['ProdiID'] != '10') {
    $sdh = CheckAmbilTA($mhsw);
    if ($sdh) {
      include_once "mhsw.hdr.php";
      TampilkanHeaderBesar($mhsw, 'ta', '', 0);
      $gos($mhsw);
    } 
  } else {
      include_once "mhsw.hdr.php";
      TampilkanHeaderBesar($mhsw, 'ta', '', 0);
      $gos($mhsw);
  }
}
?>
