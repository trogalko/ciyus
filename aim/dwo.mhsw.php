<?php
// Author: Emanuel Setio Dewo
// 26 May 2006
// http://www.sisfokampus.net

function LoginMhsw() {
  ResetLogin();
  echo "<p><br />
  <form action='?' name='frmLogin' method=POST>
  <input type=hidden name='slnt' value='prclogin'>
  <input type=hidden name='slntx' value='PrcLoginMhsw'>
  <input type=hidden name='mnux' value='mhsw'>
  <input type=hidden name='DM'>
  
  <table class=box align=center>
  <tr><th class=hdrlogin colspan=3>Login Mahasiswa</th></tr>
  <tr><td colspan=3>&nbsp;</td></tr>
  <tr><td align=center valign=top rowspan=4><img src='img/logo.jpg' hspace=10px></td>
    <td class=login>N P M</td>
    <td class=login><input type=text class=login name='Login' tabindex=1 size=15 maxlength=20> &nbsp;</td>
  </tr>
  <tr><td class=login>Password &nbsp;</td>
    <td class=login><input type=password class=login name='Password' tabindex=2 size=15 maxlength=10></td>
  </tr>
  <tr><td colspan=2 class=login>
    <hr size=1 color=silver>
    <input type=submit class=login name='Submit' value='Login' tabindex=3>
    <br />&nbsp;<br />
    <b><u>Catatan</u></b>:<br />
    Biasakan logout setelah usai <br />
    menggunakan Anjungan ini.
    <br />&nbsp;</td>
  </tr>
  </table></p>
  </form>";
}
function TampilkanPilihanTahunMhsw($mhsw, $mnux='', $sub='') {
  $optthn = GetOption2("khs", "TahunID", "TahunID", $_SESSION['__TahunID'], "MhswID='$mhsw[MhswID]'", "TahunID");
  echo "<p><table class=bsc>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='sub' value='$sub'>
  <tr><td class=inp>Pilih Tahun Akademik</td>
      <td class=ul><select name='__TahunID' onChange='javascript:this.form.submit()'>$optthn</select></td>
      </tr>
  </form></table></p>";
}

function TampilkanPilihanJadwalMhsw($mhsw, $mnux='', $sub='') {
  //$optthn = GetOption2("khs", "TahunID", "TahunID", $_SESSION['__TahunID'], "MhswID='$mhsw[MhswID]'", "TahunID");
  $JJadwal = array(0=>"Jadwal Prodi~fakultas", 1=>"Jadwal KRS~KRS");
  $a = '';
  for ($i=0; $i<sizeof($JJadwal); $i++) {
    $sel = ($i == $_SESSION['__JJadwal'])? 'selected' : '';
    $v = explode('~', $JJadwal[$i]);
    $_v = $v[0];
    $a .= "<option value='$i' $sel>$_v</option>\r\n";
	}
	echo "<p><table class=bsc>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='sub' value='$sub'>
  <tr><td class=inp>Pilih Jenis Jadwal</td>
      <td class=ul><select name='__JJadwal' onChange='javascript:this.form.submit()'>$a</select></td>
      </tr>
  </form></table></p>";
}

function HeaderAnjunganMhsw2($mhsw) {
	$foto = $mhsw['Foto'];
  $foto = (empty($foto))? "img/tux001.jpg" : $foto;
  echo "<p><table class=bsc cellspacing=1>
  <tr><td class=inp>NPM</td>
      <td class=ul>$mhsw[Nama]</td>
      <td class=inp>Program, Program Studi</td>
      <td class=ul>$mhsw[PRG], $mhsw[PRD]</td>
			<td class=bsc rowspan=20 valign=top><img src='../$foto' vspace=10 hspace=10 height=150 width=120></td>
      </tr>
  <tr><td class=inp>Angkatan</td>
      <td class=ul>$mhsw[TahunID] &nbsp;</td>
      <td class=inp>Penasehat Akademik</td>
      <td class=ul>$mhsw[PA] &nbsp;</td>
      </tr>
  <tr><td class=inp>Batas Studi</td>
      <td class=ul>$mhsw[BatasStudi] &nbsp;</td>
      <td class=inp>Status Mhsw</td>
      <td class=ul>$mhsw[STA] &nbsp;</td>
      </tr>
  </table></p>
  <hr size=1 color=gray>";
}

function HeaderAnjunganMhsw($mhsw) {
	$tglLahir = FormatTanggal($mhsw['TanggalLahir']);
  $foto = $mhsw['Foto'];
  $foto = (empty($foto))? "img/tux001.jpg" : $foto;
  $PA = GetaField('dosen', "Login", $mhsw['PenasehatAkademik'], "concat(Nama, ', ', Gelar)");
	echo "<p><table class=bsc>
  <tr><td class=ttl colspan=4>Data Pribadi</td></tr>
  <tr><td class=inp>Nomer Pokok Mhsw</td>
      <td class=ul>$mhsw[MhswID]</td>
      <td class=inp>Program</td>
      <td class=ul>$mhsw[ProgramID] - $mhsw[PRG]</td>
      <td class=bsc rowspan=8 valign=top><img src='../$foto' vspace=10 hspace=10 height=150 width=120></td>
      </tr>
  <tr><td class=inp>Nama Mhsw</td>
      <td class=ul>$mhsw[Nama]</td>
      <td class=inp>Program Studi</td>
      <td class=ul>$mhsw[ProdiID] - $mhsw[PRD]</td>
      </tr>
  <tr><td class=inp>Angkatan</td>
      <td class=ul>$mhsw[TahunID]</td>
      <td class=inp>Status Mhsw</td>
      <td class=ul>$mhsw[STA] &nbsp;</td>
      </tr>
  <tr><td class=inp>Batas Studi</td>
      <td class=ul>$mhsw[BatasStudi] &nbsp;</td>
      <td class=inp>Pembimbing Akademik</td>
      <td class=ul>$PA &nbsp;</td>
      </tr>
  <tr><td class=inp>Jenis Kelamin</td>
      <td class=ul>$mhsw[KEL] &nbsp;</td>
      <td class=inp>Agama</td>
      <td class=ul>$mhsw[AGM] &nbsp;</td>
      </tr>
  <tr><td class=inp>Tempat, Tgl Lahir</td>
      <td class=ul>$mhsw[TempatLahir], $tglLahir</td>
      <td class=inp>Status Perkawinan</td>
      <td class=ul>$mhsw[KWN] &nbsp;</td>
      </tr>
	<tr><td class=ttl colspan=4>&nbsp;</td></tr></table></p>";
}
?>
