<?php
// Author: Emanuel Setio Dewo
// 16 April 2006
// Happy Easter (Selamat Paskah)

// *** Functions ***
function TampilkanHeaderWisuda($wsd) {
  $TM = FormatTanggal($wsd['TglMulai']);
  $TS = FormatTanggal($wsd['TglSelesai']);
  $TW = FormatTanggal($wsd['TglWisuda']);
  if (date('Y-m-d') <= $wsd['TglSelesai']) {
    $TglSKKeluar = GetDateOption($_SESSION['TglSKKeluar'], 'TglSKKeluar');
    $frm = "<form action='?' name='frmWisudawanAdd' method=POST>
    <input type=hidden name='gos' value='WisudawanAdd'>
    <input type=hidden name='WisudaID' value='$wsd[WisudaID]'>
    <tr><td class=inp>Tambahkan Wisudawan</td><td class=ul colspan=4>
      NPM: <input type=text name='MhswID' value='$_SESSION[MhswID]' size=30 maxlength=50>
      <input type=submit name='Tambahkan' value='Tambahkan'>
      <input type=button name='Wisudawan' value='Daftar Para Wisudawan' onClick=\"location='?mnux=wisuda.daftar&gos=CetakDaftarWisuda'\">
      <input type=button name='Ukuran' value='Rekap Jumlah & Ukuran' onClick=\"location='?mnux=wisuda.daftar&gos=RkpUkrn'\">
      <input type=button name='Csv' value='Buat Untuk Excel' onClick=\"location='?mnux=wisuda.daftar&gos=BuatCSV'\">
      
    </td></tr></form>
    
    <form action='?' method=POST name='frmSetSKLulus'>
    <input type=hidden name='mnux' value='wisuda.daftar'>
    <input type=hidden name='gos' value='SetSKLulus'>
    <tr><td class=inp>Set No SK Kelulusan</td>
      <td class=ul colspan=3><input type=text name='SKKeluar' value='$_SESSION[SKKeluar]' size=30 maxlength=100>
      Tanggal SK :
      $TglSKKeluar <input type=submit name='Simpan' value='Set Mhsw yang Dicentang'>
      <input type=reset name='Reset' value='Reset'></td></tr>
    ";
  
  } else $frm = '';
  echo <<<END
  <p><table class=box cellspacing=1>
  
  <tr><td class=inp>Nama</td><td class=ul>$wsd[Nama] ($wsd[WisudaID])</td>
    <td class=inp>Institusi</td><td class=ul><b>$_SESSION[KodeID]</b></td></tr>
  <tr><td class=inp>Tgl Pendaftaran</td><td class=ul>$TM s/d $TS</td>
    <td class=inp>Tgl Wisuda</td><td class=ul>$TW</td></tr>
  $frm
  </table></p>
END;
}
function DftrWisudawan($wsd) {
  // filter
  $_whr = array();
  if (!empty($_SESSION['prid'])) $_whr[] = "m.ProgramID='$_SESSION[prid]'";
  if (!empty($_SESSION['prodi'])) $_whr[] = "m.ProdiID='$_SESSION[prodi]'";
  $whr = (empty($_whr))? '' : " and " . implode(' and ', $_whr);
  // Tombol delete
  $del = (date('Y-m-d') < $wsd['TglSelesai'])? "<a href='?mnux=wisuda.daftar&gos=WisudawanDel&MhswID==MhswID='><img src='img/del.gif'></a>" : "&nbsp;";
  // Tampilkan
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['wsdpage1']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=wisuda.daftar&wsdpage1==PAGE='>=PAGE=</a>";

  $lst->tables = "mhsw m
    left outer join wisudawan wn on m.MhswID=wn.MhswID
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join ta ta on m.TAID=ta.TAID 
    where m.KodeID='$_SESSION[KodeID]' and m.WisudaID='$wsd[WisudaID]'
    $whr
    order by m.MhswID";
  $lst->fields = "m.MhswID, m.Nama, m.TotalSKS, m.IPK, m.SKKeluar, m.TglSKKeluar, wn.*,
    date_format(m.TglSKKeluar, '%d-%m-%Y') as TGLSK,
    prg.Nama as PRG, prd.Nama as PRD, ta.Judul, ta.GradeNilai, ta.BobotNilai";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>NPM<br />Nama</th>
    <th class=ttl>IPK</th>
    <th class=ttl>Judul</th>
    <th class=ttl>Nilai</th>
    <th class=ttl title='Hapus Mhsw Dari Daftar Wisuda'>Del</th>
    <th class=ttl colspan=2>SK Lulus</th>
    <th class=ul>&raquo;</th>
    <th class=ttl>Toga</th>
    <th class=ttl>Topi</th>
    <th class=ttl>Kalung</th>
    <th class=ttl>Surat Undangan</th>
    <th class=ttl>Foto</th>
    <th class=ttl>Ijazah</th>
    </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=inp>=NOMER=</td>
    <td class=ul nowrap><a href='?mnux=mhsw.inq.det&mhswid==MhswID=' title='Inquiry'>=MhswID=</a><br />=Nama=</td>
    <td class=ul align=right>=IPK=</td>
    <td class=ul>=Judul=</td>
    <td class=ul align=center>=GradeNilai=</td>
    <td class=ul align=center>$del</td>
    <td class=ul><input type=checkbox name='SKL[]' value==MhswID=></td>
    <td class=ul>=SKKeluar=<br />=TGLSK=</td>
    
    <td class=ul align=center><a href='?mnux=wisuda.daftar&gos=WsdProp&mhswid==MhswID='><img src='img/edit.png'></a></td>
    <td class=ul align=center><img src='img/=Toga=.gif'><br />=UkuranToga=</td>
    <td class=ul align=center><img src='img/=Topi=.gif'><br />=UkuranTopi=</td>
    <td class=ul align=center><img src='img/=Kalung=.gif'></td>
    <td class=ul align=center><img src='img/=Undangan=.gif'></td>
    <td class=ul align=center><img src='img/=Foto=.gif'></td>
    <td class=ul align=center><img src='img/=Ijazah=.gif'><br />=NomerIjazah=</td>
    </tr>";
  echo $lst->TampilkanData();
  echo "</form>";
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />";
  echo "Total : ". $lst->MaxRowCount . "</p>";
}
function WisudawanAdd($wsd) {
  $mhsw = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join bipot bpt on m.BIPOTID=bpt.BIPOTID", 
    'MhswID', $_SESSION['MhswID'], 
    "m.*, prg.Nama as PRG, prd.Nama as PRD, bpt.Nama as BPT");
  if (!empty($mhsw)) {
    // Jika telah terdaftar di periode wisuda lain
    if ($mhsw['WisudaID'] > 0) {
      $_wsd = GetFields('wisuda', 'WisudaID', $mhsw['WisudaID'], "*");
      echo ErrorMsg("Gagal Didaftarkan",
        "Mahasiswa telah terdaftar di periode wisuda: <b>$_wsd[Nama]</b> ($_wsd[WisudaID]). <br />
        Mahasiswa tidak ditambahkan pada periode wisuda ini.");
    }
    // Jika belum terdaftar
    else {
      include_once "mhsw.hdr.php";
      TampilkanHeaderBesar($mhsw, 'wisuda.daftar', '', 0);
      $pesan = CekKelulusan($mhsw);
      if (empty($pesan)) {
        // Tambahkan peserta
        $s = "update mhsw set WisudaID='$wsd[WisudaID]' where MhswID='$mhsw[MhswID]' ";
        $r = _query($s);
        // Apakah sudah ada di table wisudawan?
        $ada = GetaField('wisudawan', 'MhswID', $mhsw['MhswID'], "count(MhswID)")+0;
        if ($ada == 0) {
          $s = "insert into wisudawan (MhswID, LoginBuat, TanggalBuat) 
            values ('$mhsw[MhswID]', '$_SESSION[_Login]', now())";
          $r = _query($s);
        }
        $mhsw['WisudaID'] = $wsd['WisudaID'];
        DftrWisudawan($wsd);
      }
      else echo ErrorMsg("Tidak dapat mendaftarkan",
        "Mahasiswa <b>$mhsw[Nama]</b> ($mhsw[MhswID]) tidak dapat didaftarkan wisuda karena: <br />
        $pesan <hr size=1 color=silver>
        Pilihan: <a href='?mnux=mhsw.inq.det&mhswid=$mhsw[MhswID]'>Lihat Data Mahasiswa</a>");
    }
  }
  else {
    echo ErrorMsg("Data Tidak Ditemukan",
      "Mahasiswa dengan NPM <b>$_SESSION[MhswID]</b> tidak ditemukan.<br />
      Tidak ditambahkan dalam daftar wisudawan.");
  }  
}
function CekKelulusan($mhsw) {
  $ta = GetaField("ta", "Lulus='Y' and MhswID", $mhsw['MhswID'], "count(TAID)")+0;
  if (empty($ta)) 
    return "Mahasiswa belum lulus matakuliah Tugas Akhir/Skripsi/Tesis/Disertasi.";
  else return '';
}
function WisudawanDel($wsd) {
  $MhswID = $_REQUEST['MhswID'];
  $Nama = GetaField('mhsw', "MhswID", $MhswID, "Nama");
  echo Konfirmasi("Konfirmasi Hapus",
    "Benar Anda akan menghapus <b>$Nama ($MhswID)</b> dari daftar wisudawan?
    <hr size=1 color=silver>
    Pilihan: <input type=button name='Hapus' value='Hapus Wisudawan ini' onClick=\"location='?mnux=wisuda.daftar&gos=WsdDel&MhswID=$MhswID'\">
    <input type=button name='Batal' value='Batal Hapus' onClick=\"location='?mnux=wisuda.daftar&gos=DftrWisudawan'\"> 
    ");
}
function WsdDel($wsd) {
  $MhswID = $_REQUEST['MhswID'];
  $s = "update mhsw set WisudaID=0 where MhswID='$MhswID' ";
  $r = _query($s);
  // Kembali ke daftar
  DftrWisudawan($wsd);
}
function WsdProp($wsd) {
  $mhswid = $_REQUEST['mhswid'];
  $mhsw = GetFields('mhsw', 'MhswID', $mhswid, '*');
  $wn = GetFields('wisudawan', 'MhswID', $mhswid, '*');
  $t = ($wn['Toga'] == 'Y')? 'checked' : '';
  $p = ($wn['Topi'] == 'Y')? 'checked' : '';
  $f = ($wn['Foto'] == 'Y')? 'checked' : '';
  $i = ($wn['Ijazah'] == 'Y')? 'checked' : '';
  $u = ($wn['Undangan'] == 'Y') ? 'checked' :'';
  $k = ($wn['Kalung'] == 'Y') ? 'checked' : '';
  $tu = GetUkuran($wn['UkutanToga']);
  $pu = GetUkuran($wn['UkuranTopi']);
  // Tampilkan
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mhswid' value='$mhswid'>
  <input type=hidden name='mnux' value='wisuda.daftar'>
  <input type=hidden name='gos' value='WsdPropSav'>
  
  <tr><td class=inp1>NPM</td><td class=ul colspan=2>$mhsw[MhswID]</td></tr>
  <tr><td class=inp1>Nama Mhsw</td><td class=ul colspan=2>$mhsw[Nama]</td></tr>
  <tr><td class=inp1 rowspan=2>Toga</td><td class=inp>Sdh diambil?</td>
    <td class=ul><input type=checkbox name='Toga' value='Y' $t></td></tr>
    <td class=inp>Ukuran</td>
    <td class=ul><select name='UkuranToga'>$tu</select></td></tr>
  <tr><td class=inp1 rowspan=2>Topi</td><td class=inp>Sdh diambil?</td>
    <td class=ul><input type=checkbox name='Topi' value='Y' $t></td></tr>
    <td class=inp>Ukuran</td>
    <td class=ul><select name='UkuranTopi'>$pu</select></td></tr>
  <tr><td class=inp1>Kalung (Kerah)</td><td class=inp>Sdh diambil?</td>
    <td class=ul><input type=checkbox name='Kalung' value='Y' $k></td></tr>
  <tr><td class=inp1>Surat Undangan</td><td class=inp>Sdh diambil?</td>
    <td class=ul><input type=checkbox name='Undangan' value='Y' $u></td></tr>
  <tr><td class=inp1>Foto</td><td class=inp>Mengumpulkan?</td>
    <td class=ul><input type=checkbox name='Foto' value='Y' $f></td></tr>
  <tr><td class=inp1 rowspan=2>Ijazah</td><td class=inp>Sdh diambil?</td>
    <td class=ul><input type=checkbox name='Ijazah' value='Y' $i></td></tr>
    <td class=inp>Nomer</td>
    <td class=ul><input type=text name='NomerIjazah' value='$wn[NomerIjazah]' size=30 maxlength=50></td></tr>
  <tr><td class=ul colspan=3>
    <input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=wisuda.daftar'\"></td></tr>
  </form></table></p>";
}
function WsdPropSav($wsd) {
  $mhswid = $_REQUEST['mhswid'];
  $Toga = (empty($_REQUEST['Toga']))? 'N' : $_REQUEST['Toga'];
  $Topi = (empty($_REQUEST['Topi']))? 'N' : $_REQUEST['Topi'];
  $Foto = (empty($_REQUEST['Foto']))? 'N' : $_REQUEST['Foto'];
  $Ijazah = (empty($_REQUEST['Ijazah']))? 'N' : $_REQUEST['Ijazah'];
  $Kalung = (empty($_REQUEST['Kalung'])) ? 'N' : $_REQUEST['Kalung'];
  $Undangan = (empty($_REQUEST['Undangan'])) ? 'N' : $_REQUEST['Undangan']; 
  $UkuranToga = $_REQUEST['UkuranToga'];
  $UkuranTopi = $_REQUEST['UkuranTopi'];
  $NomerIjazah = sqling($_REQUEST['NomerIjazah']);
  if (!empty($mhswid)) {
    $s = "update wisudawan set Toga='$Toga', UkuranToga='$UkuranToga',
      Topi='$Topi', UkuranTopi='$UkuranTopi',
      Foto='$Foto', Ijazah='$Ijazah', NomerIjazah='$NomerIjazah', Kalung = '$Kalung', Undangan='$Undangan'
      where MhswID='$mhswid' ";
    $r = _query($s);
    //echo "<pre>$s</pre>";
  }
  DftrWisudawan($wsd);
}
function GetUkuran($uk) {
  global $arrUkuran;
  $str = '';
  for ($i = 0; $i < sizeof($arrUkuran); $i++) {
    $sel = ($uk == $arrUkuran[$i])? 'selected' : '';
    $nl = $arrUkuran[$i];
    $str .= "<option value='$nl' $sel>$nl</option>";
  }
  return $str;
}
function RkpUkrn($wsd) {
  global $arrUkuran;
  $arr = array('Toga', 'Topi');
  $isi = array();
  $tot = array();
  
  for ($i = 0; $i <= 1; $i++) {
    $nm = $arr[$i];
    $s = "select wn.Ukuran$nm, count(wn.MhswID) as JML
      from mhsw m
        left outer join wisudawan wn on m.MhswID=wn.MhswID
      where m.WisudaID='$wsd[WisudaID]'
      group by wn.Ukuran$nm";
    $r = _query($s);
  
    while ($w = _fetch_array($r)) {
      $idx = array_search($w['Ukuran'.$nm], $arrUkuran);
      $isi[$i][$idx] += $w['JML'];
    }
  }
  //print_r(array_values($isi));
  $hdr = '';
  for ($i = 0; $i < sizeof($arrUkuran); $i++) $hdr .= "<th class=ttl>".$arrUkuran[$i]."</th>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  echo "<tr><th class=ttl>Jenis</th>$hdr<th class=ttl>Total</th></tr>";
  
  for ($i = 0; $i <= 1; $i++) {
    $_nah = '';
    for ($j = 0; $j < sizeof($arrUkuran); $j++) {
      $jml = $isi[$i][$j]+0;
      $_jml = number_format($jml);
      $tot[$i] += $jml;
      $_nah .= "<td class=ul align=right>$_jml</td>";
    }
    $_tot = number_format($tot[$i]);
    echo "<tr><td class=inp><b>" . $arr[$i] . "</b></td>" . $_nah . 
      "<td class=inp>$_tot</td></tr>";
  }
  echo "</table></p>";
}
function SetSKLulus($wsd) {
  $SKL = array();
  $SKL = $_REQUEST['SKL'];
  $jml = sizeof($SKL);
  if ($jml > 0) {
    for ($i = 0; $i < $jml; $i++) {
      $mid = $SKL[$i];
      $s = "update mhsw set SKKeluar='$_SESSION[SKKeluar]', TglSKKeluar='$_SESSION[TglSKKeluar]'
        where MhswID='$mid' ";
      $r = _query($s);
    }
    echo Konfirmasi("SK Lulus Sudah Diset",
      "SK Kelulusan <b>$_SESSION[SKLulus]</b> telah diset kepada <b>$jml</b> Mhsw.");
  } 
  else echo ErrorMsg("Tidak Ada yg Diset",
    "Tentukan Mhsw yg akan diset SK Kelulusannya dengan mencentang Mhsw dari daftar wisudawan berikut ini.");
  DftrWisudawan($wsd);
}

function CetakDaftarWisuda($wsd){
  global $_lf;
  $_whr = array();
  if (!empty($_SESSION['prid'])) $_whr[] = "m.ProgramID='$_SESSION[prid]'";
  if (!empty($_SESSION['prodi'])) $_whr[] = "m.ProdiID='$_SESSION[prodi]'";
  $whr = (empty($_whr))? '' : " and " . implode(' and ', $_whr);
  $TM = FormatTanggal($wsd['TglMulai']);
  $TS = FormatTanggal($wsd['TglSelesai']);
  $TW = FormatTanggal($wsd['TglWisuda']);
  $s = "select m.*
    from mhsw m
    left outer join wisudawan wn on m.MhswID=wn.MhswID 
    where m.KodeID='$_SESSION[KodeID]' and m.WisudaID='$wsd[WisudaID]'
    $whr
    order by m.MhswID";
  $r = _query($s);
  $maxcol = 114; 
	$nmf = "tmp/$_SESSION[_Login].dwoprn";
	$f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(5));
  $div = str_pad('-', $maxcol, '-').$_lf;
  		// parameter2
  $n = 0; $hal = 1;
  $brs = 0;
  $maxbrs = 46;
  $Njur = GetFields("prodi p left outer join Fakultas f on f.FakultasID = p.FakultasID", "p.ProdiID", $_SESSION['prodi'], "p.Nama as pnama, f.Nama as fnama");
  $NamaFakJur = (!empty($Njur)) ? $Njur['fnama'] .'/'. $Njur['pnama'] : "Semua Prodi"; 
  $hdr = str_pad("** DAFTAR PESERTA WISUDA **", $maxcol, ' ', STR_PAD_BOTH) . $_lf . $_lf . $_lf .
         "Tanggal Wisuda : " . $TW . $_lf .
         "Fak/Jur        : " . $NamaFakJur . $_lf .
         $div . 
         "No   NIM       NAMA                             TEMPAT-TGL LAHIR              SEX    ASAL SMU" . $_lf .
         $div . $_lf;
  $jump = 0; $jumw = 0;
  fwrite($f, $hdr);
  while($w = _fetch_array($r)) {
    $n++; $brs++;
			if($brs > $maxbrs){
			  fwrite($f,$div);
				fwrite($f,str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
				$hal++; $brs = 1;
				fwrite($f, chr(12));
				fwrite($f, $hdr.$_lf);
			}
		if ($w['Kelamin'] == 'P')  $jump++;
    else $jumw++;
     
		$SEX = GetaField ('kelamin', "Kelamin", $w['Kelamin'], 'Nama');	
		$Sekolah = GetaField('asalsekolah', 'SekolahID',$w['AsalSekolah'], 'Nama');
		$isi = str_pad($n.'.', 4, ' ') . 
           str_pad($w['MhswID'], 11, ' ') . 
           str_pad($w['Nama'], 33, ' ') .
           str_pad($w['TempatLahir'].", ".$w['TanggalLahir'], 30, ' ') .
           str_pad($SEX, 7, ' ') . 
           str_pad($Sekolah, 30, ' ') . $_lf.$_lf;
    fwrite($f, $isi);	
  }      
  $jumtotP = GetaField('wisudawan w left outer join mhsw m on w.MhswID = m.MhswID',"m.Kelamin = 'P' and WisudaID", $wsd['WisudaID'], "count(m.MhswID)"); 
  $jumtotW = GetaField('wisudawan w left outer join mhsw m on w.MhswID = m.MhswID',"m.Kelamin = 'W' and WisudaID", $wsd['WisudaID'], "count(m.MhswID)"); 
  fwrite($f, $div);
  fwrite($f, "Jumlah Seluruh Peserta/Jurusan : - Pria = $jump  - Wanita = $jumw" . $_lf);
  fwrite($f, $div);
  fwrite($f, "Jumlah Seluruh Peserta Seluruhnya : - Pria = $jumtotP  - Wanita = $jumtotW" . $_lf);
  fwrite($f, $div);
  fwrite($f, str_pad("Dicetak oleh : $_SESSION[_Login], ". date("d-m-Y H:i"),50,' ') . str_pad("Akhir Laporan",60,' ',STR_PAD_LEFT));
  fwrite($f, chr(12));
  fclose($f); 
  TampilkanFileDWOPRN($nmf, "wisuda.daftar");
}

function BuatCSV($wsd){
  global $_lf;
  $_whr = array();
  if (!empty($_SESSION['prid'])) $_whr[] = "m.ProgramID='$_SESSION[prid]'";
  if (!empty($_SESSION['prodi'])) $_whr[] = "m.ProdiID='$_SESSION[prodi]'";
  $whr = (empty($_whr))? '' : " and " . implode(' and ', $_whr);
  $TM = FormatTanggal($wsd['TglMulai']);
  $TS = FormatTanggal($wsd['TglSelesai']);
  $TW = FormatTanggal($wsd['TglWisuda']);
  $s = "select m.*, ta.Judul, prd.Nama as Pnama, prd.FakultasID
    from mhsw m
    left outer join wisudawan wn on m.MhswID=wn.MhswID 
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join ta ta on m.TAID=ta.TAID
    where m.KodeID='$_SESSION[KodeID]' and m.WisudaID='$wsd[WisudaID]'
    $whr
    order by m.MhswID";
  $r = _query($s);
  $Num = _num_rows($r);
  $n = 0;
	$nmf = "tmp/DataMhsw.csv";
	$f = fopen($nmf, 'w');
  //$Njur = GetFields("prodi p left outer join Fakultas f on f.FakultasID = p.FakultasID", "p.ProdiID", $_SESSION['prodi'], "p.Nama as pnama, f.Nama as fnama");
  //$NamaFakJur = (!empty($Njur)) ? $Njur['fnama'] .'/'. $Njur['pnama'] : "Semua Prodi"; 
  $hdr = "No;NIM;Nama;Tempat/Tanggal Lahir;Sex;Alamat Tinggal;Kota;Telepon;Judul Skripsi;Fakultas;Jurusan;Asal Sekolah;\n";
  fwrite($f, $hdr);
  while($w = _fetch_array($r)){     
    $n++;
    $fakultas = GetaField('fakultas', "FakultasID", $w['FakultasID'],'Nama');
		$SEX = GetaField ('kelamin', "Kelamin", $w['Kelamin'], 'Nama');	
		$Sekolah = GetaField('asalsekolah', 'SekolahID',$w['AsalSekolah'], 'Nama');
		$isi = $n .";".
		       $w['MhswID'] .';'.
		       $w['Nama'] .';'.
		       $w['TempatLahir'].', '.$w['TanggalLahir'] .';'.
		       $SEX.';'.
		       $w['Alamat'].';'.
		       $w['Kota'].';'.
		       $w['Telephone'].';'.
		       $w['Judul'].';'.
		       $fakultas.';'.
		       $w['Pnama'].';'.
		       $Sekolah.';'.
		       "\n";
    fwrite($f, $isi);	
    echo "Proses : $w[MhswID] Dari $Num Mahasiswa</br>";
    }
    fclose($f); 
   //echo "<iframe src='downloadexcel?fn=$nmf' height=0 width=0 frameborder=0>
   // </iframe>";
  echo "Proses Selesai, Klik Tombol Download <br><br>";  
  echo "<input type=button name='Wisudawan' value='Download' onClick=\"location='downloadexcel.php?fn=$nmf'\">";
}

// *** Parameters ***
$arrUkuran = array('XS', 'S', 'M', 'L', 'XL', 'XXL');

$SKKeluar = GetSetVar('SKKeluar');
$TglSKKeluar_y = GetSetVar('TglSKKeluar_y', date('Y'));
$TglSKKeluar_m = GetSetVar('TglSKKeluar_m', date('m'));
$TglSKKeluar_d = GetSetVar('TglSKKeluar_d', date('d'));
$TglSKKeluar = "$TglSKKeluar_y-$TglSKKeluar_m-$TglSKKeluar_d";
$_SESSION['TglSKKeluar'] = $TglSKKeluar;

$wsdpage1 = GetSetVar('wsdpage1');
$MhswID = GetSetVar('MhswID');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$gos = (empty($_REQUEST['gos']))? 'DftrWisudawan' : $_REQUEST['gos'];
$wsd = GetFields('wisuda', "KodeID='$_SESSION[KodeID]' and NA", 'N', '*');

// *** Main ***
TampilkanJudul("Pendaftaran Wisuda");
TampilkanPilihanProdiProgram('wisuda.daftar', '');
if (!empty($wsd)) {
  TampilkanHeaderWisuda($wsd);
  $gos($wsd);
}
?>
