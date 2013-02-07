<?php
// Author: Emanuel Setio Dewo
// 31 May 2006
// www.sisfokampus.net

// *** functions ***
function TampilkanMenuAlumni() {
  $arrMenuAlumni = array('Data Pribadi->AlumniEdt',
    'Akademik->AlumniAkd',
    'Pekerjaan->AlumniKrj'
  );
  
  echo "<p><table class=menu cellspacing=1 cellpadding=4><tr>";
  for ($i = 0; $i < sizeof($arrMenuAlumni); $i++) {
    $mn = explode('->', $arrMenuAlumni[$i]);
    $c = ($mn[1] == $_SESSION['alumnisub'])? 'class=menuaktif' : 'class=menuitem';
    echo "<td $c><a href='?mnux=alumni.det&alumnisub=$mn[1]&AlumniID=$_SESSION[AlumniID]'>$mn[0]</a></td>";
  }
  echo "</tr></table></p>";
}
// Header untuk alumni
function TampilkanHeaderAlumni($w) {
  $TGLLLS = FormatTanggal($w['TanggalLulus']);
  echo "<p><table class=box>
  <tr><td class=ul colspan=2><b>$w[KodeID]</td></tr>
  <tr><td class=inp>NPM</td>
      <td class=ul>$w[MhswID]</td>
      <td class=inp>Program, Program Studi</td>
      <td class=ul>$w[ProgramID]-$w[PRG], $w[ProdiID]-$w[PRD]</td>
      </tr>
  <tr><td class=inp>Tanggal Lulus</td>
      <td class=ul>$TGLLLS &nbsp;</td>
      <td class=inp>Index Prestasi Kumulatif</td>
      <td class=ul>$w[IPK] &nbsp;</td>
      </tr>
  </table></p>";
}
// Edit data pribadi Alumni
function AlumniEdt($w) {
  $a = GetFields('alumni', 'MhswID', $w['MhswID'], '*');
  if (empty($a)) {
    $s = "insert into alumni (MhswID, Gelar,
      Alamat, Kota, KodePos, RT, RW,
      Propinsi, Negara,
      Telepon, Handphone, Email,
      TanggalBuat, LoginBuat)
      values ('$w[MhswID]', '',
      '$w[Alamat]', '$w[Kota]', '$w[KodePos]', '$w[RT]', '$w[RW]',
      '$w[Propinsi]', '$w[Negara]',
      '$w[Telepon]', '$w[Handphone]', '$w[Email]',
      now(), '$_SESSION[_Login]')";
    $r = _query($s);
    $a = GetFields('alumni', 'MhswID', $w['MhswID'], '*');
  }
  // Tampilkan data
  echo "<p><table class=bsc>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='alumni.det'>
  <input type=hidden name='slnt' value='alumni.sav'>
  <input type=hidden name='slntx' value='AlumniEdtSav'>
  <input type=hidden name='alumnisub' value='AlumniEdt'>
  <input type=hidden name='AlumniID' value='$w[MhswID]'>
  <tr><td class=ul colspan=2><b>Alamat Sekarang</b></td></tr>
  <tr><td class=inp>Alamat</td>
      <td class=ul><textarea name='Alamat' cols=30 rows=2>$a[Alamat]</textarea></td>
      </tr>
  <tr><td class=inp>RT/RW</td>
      <td class=ul><input type=text name='RT' value='$a[RT]' size=5 maxlength=5>/
      <input type=text name='RW' value='$a[RW]' size=5 maxlength=5></td>
      </tr>
  <tr><td class=inp>Kota</td>
      <td class=ul><input type=text name='Kota' value='$a[Kota]' size=30 maxlength=30></td>
      </tr>
  <tr><td class=inp>Propinsi</td>
      <td class=ul><input type=text name='Propinsi' value='$a[Propinsi]' size=30 maxlength=30></td>
      </tr>
  <tr><td class=inp>Negara</td>
      <td class=ul><input type=text name='Negara' value='$a[Negara]' size=30 maxlength=30></td>
      </tr>
  
  <tr><td class=ul colspan=2><b>Contact</b></td></tr>
  <tr><td class=inp>Email Pribadi</td>
      <td class=ul><input type=text name='Email' value='$a[Email]' size=50 maxlength=100></td>
      </tr>
  <tr><td class=inp># Telepon</td>
      <td class=ul><input type=text name='Telepon' value='$a[Telepon]' size=30 maxlength=50></td>
      </tr>
  <tr><td class=inp># Handphone</td>
      <td class=ul><input type=text name='Handphone' value='$a[Handphone]' size=30 maxlength=50></td>
      </tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
      <input type=reset name='Reset' value='Reset'></td></tr>
  </table></p>";
}
// Data akademik alumni
function AlumniAkd($w) {
  $ta = GetFields('ta', "Lulus='Y' and MhswID", $w['MhswID'], "*");
  $PA = GetaField("dosen", "Login", $w['PenasehatAkademik'], "concat(Nama, ', ', Gelar)"); 
  echo "<p><table class=box>
  <tr><td class=inp>Program</td>
    <td class=ul>$w[ProgramID] - $w[PRG]</td></tr>
  <tr><td class=inp>Program Studi</td>
    <td class=ul>$w[ProdiID] - $w[PRD]</td></tr>
  <tr><td class=inp>Pembimbing Akademik</td>
    <td class=ul>$PA &nbsp;</td></tr>
  <tr><td class=inp>Index Prestasi Kumulatif</td>
    <td class=ul>$w[IPK]</td></tr>
  <tr><td class=inp>Judul Tugas Akhir</td>
    <td class=ul>$ta[Judul] &nbsp;</td></tr>
  <tr><td class=inp>Nilai</td>
    <td class=ul>$ta[GradeNilai] ($ta[GradeNilai])</td></tr>
  </table></p>";
}
function AlumniKrj($a) {
  $gos = (empty($_REQUEST['gos']))? 'AlumniKrjDaftar' : $_REQUEST['gos'];
  $gos($a);
}
function AlumniKrjDaftar($a) {
  echo "<p>Berikut adalah riwayat pekerjaan alumni:</p>";
  $s = "select ak.*
    from alumnikerja ak
    where ak.MhswID='$a[MhswID]'
    order by ak.MulaiKerja desc";
  $r = _query($s); $n = 0;
  echo "<p><a href='?mnux=alumni.det&alumnisub=AlumniKrj&gos=AlumniKrjEdt&AlumniID=$a[MhswID]&md=1'>Pekerjaan Baru</a></p>";
  echo "<p><table class=box>
    <tr><th class=ttl>#</th>
    <th class=ttl>Perusahaan</th>
    <th class=ttl>Pekerjaan/Jabatan</th>
    <th class=ttl>Masa Kerja</th>
    <th class=ttl>Telepon</th>
    <th class=ttl>Fax</th>
    <th class=ttl>Website</th>
    <th class=ttl>Status</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $MK = FormatTanggal($w['MulaiKerja']);
    $KK = ($w['NA'] == 'Y')? '- ' . FormatTanggal($w['KeluarKerja']) : '';
    echo "<tr><td class=inp nowrap>$n
      <a href='?mnux=alumni.det&alumnisub=AlumniKrj&gos=AlumniKrjEdt&AlumniID=$a[MhswID]&md=0&AlumniKerjaID=$w[AlumniKerjaID]'><img src='img/edit.png'></a></td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[Jabatan]</td>
    <td class=ul>$MK $KK</td>
    <td class=ul>$w[Telepon] &nbsp;</td>
    <td class=ul>$w[Facsimile] &nbsp;</td>
    <td class=ul>$w[Website]</td>
    <td class=ul align=center><img src='img/book$w[NA].gif'></td>
    </tr>";
  }
  echo "</table></p>";
}
function AlumniKrjEdt($a) {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $AlumniKerjaID = $_REQUEST['AlumniKerjaID'];
    $k = GetFields('alumnikerja', 'AlumniKerjaID', $AlumniKerjaID, '*');
    $jdl = "Edit Pekerjaan";
  }
  else {
    $k = array();
    $k['AlumniKerjaID'] = 0;
    $k['Nama'] = '';
    $k['Jabatan'] = '';
    $k['MulaiKerja'] = date('Y-m-d');
    $k['KeluarKerja'] = date('Y-m-d');
    $k['Alamat'] = '';
    $k['Kota'] = '';
    $k['KodePos'] = '';
    $k['Propinsi'] = '';
    $k['Negara'] = '';
    $k['Telepon'] = '';
    $k['Facsimile'] = '';
    $k['Website'] = '';
    $k['NA'] = 'N';
    $jdl = "Tambah Pekerjaan Baru";
  }
  $MK = GetDateOption($k['MulaiKerja'], 'MK');
  $KK = GetDateOption($k['KeluarKerja'], 'KK');
  $na = ($k['NA'] == 'Y')? 'checked' : '';
  // Tampilkan form
  echo "<p><table class=box>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='alumni.det'>
  <input type=hidden name='AlumniID' value='$a[MhswID]'>
  <input type=hidden name='alumnisub' value='AlumniKrj'>
  <input type=hidden name='slnt' value='alumni.sav'>
  <input type=hidden name='slntx' value='AlumniKrjSav'>
  <input type=hidden name='AlumniKerjaID' value='$k[AlumniKerjaID]'>
  <input type=hidden name='md' value='$md'>
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Nama Perusahaan</td>
    <td class=ul><input type=text name='Nama' value='$k[Nama]' size=40 maxlength=50></td>
    </tr>
  <tr><td class=inp>Jabatan/Pekerjaan</td>
    <td class=ul><input type=text name='Jabatan' value='$k[Jabatan]' size=40 maxlength=50></td>
    </tr>
  <tr><td class=inp>Alamat Prsh</td>
    <td class=ul><textarea name='Alamat' cols=40 rows=3>$k[Alamat]</textarea></td>
    </tr>
  <tr><td class=inp>Kota</td>
    <td class=ul><input type=text name='Kota' value='$k[Kota]' size=20 maxlength=20></td>
    </tr>
  <tr><td class=inp>Kode Pos</td>
    <td class=ul><input type=text name='KodePos' value='$k[KodePos]' size=20 maxlength=20></td>
    </tr>
  <tr><td class=inp>Propinsi</td>
    <td class=ul><input type=text name='Propinsi' value='$k[Propinsi]' size=20 maxlength=50></td>
    </tr>
  <tr><td class=inp>Negara</td>
    <td class=ul><input type=text name='Negara' value='$k[Negara]' size=20 maxlength=30></td>
    </tr>
  <tr><td class=inp>Telepon</td>
    <td class=ul><input type=text name='Telepon' value='$k[Telepon]' size=40 maxlength=40></td></tr>
  <tr><td class=inp>Facsimile</td>
    <td class=ul><input type=text name='Facsimile' value='$k[Facsimile]' size=40 maxlength=40></td></tr>
  <tr><td class=inp>Website</td>
    <td class=ul><input type=text name='Website' value='$k[Website]' size=40 maxlength=50></td></tr>
  
  <tr><td class=inp>Mulai Kerja</td>
    <td class=ul>$MK</td></tr>
  <tr><td class=inp>Keluar Kerja</td>
    <td class=ul>$KK</td></tr>
  <tr><td class=inp>Sudah keluar?</td>
    <td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=alumni.det&alumnisub=AlumniKrj'\">
    </td></tr>
  </form>
  </table></p>";
}

// *** Parameters ***
$AlumniID = GetSetVar('AlumniID');
$AlumniSUB = GetSetVar('alumnisub', 'AlumniEdt');

// *** Main ***
TampilkanJudul("Detail Alumni");
$alumni = GetFields("mhsw m
  left outer join program prg on m.ProgramID=prg.ProgramID
  left outer join prodi prd on m.ProdiID=prd.ProdiID", 
  "m.MhswID", $AlumniID, 
  "m.*, prg.Nama as PRG, prd.Nama as PRD");
if (!empty($alumni)) {
  TampilkanMenuAlumni();
  TampilkanHeaderAlumni($alumni);
  $AlumniSUB($alumni);
}
else echo ErrorMsg("Data Tidak Ditemukan",
  "Data alumni tidak ditemukan. Hubungi bagian BAA atau SIM untuk informasi lebih lanjut");
?>
