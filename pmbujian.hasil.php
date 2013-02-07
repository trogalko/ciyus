<?
// Author: Emanuel Setio Dewo, setio_dewo@sisfokampus.net
// 2006-01-02

// *** Functions ***
function TampilkanFilterHasilUSM() {
  $opt = GetOption2('pmbformulir', "concat(Nama, ' (', JumlahPilihan, ' pilihan) : Rp. ', format(Harga, 0))",
    'PMBFormulirID', $_SESSION['pmbfid'], '', 'PMBFormulirID');
  $opt1 = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['pmbpil1'], '', 'ProdiID');
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['pmbprodi'], '', 'ProdiID');
  $optgrade = GetOption2('pmbgrade', "GradeNilai", 'GradeNilai', $_SESSION['pmbhasilgrade'], '', 'GradeNilai');
  $c = 'class=ul';
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='pmbujian.hasil'>
  <tr><td $c>Periode</td><td $c><input type=text name=pmbtahun size=15 value=$_SESSION[pmbtahun]><b>$_SESSION[pmbaktif]</td></tr>
  <tr><td $c>Jenis Formulir</td><td $c><select name='pmbfid' onChange='this.form.submit()'>$opt</select></td></tr>
  <tr><td $c>Pilihan 1</td><td $c><select name='pmbpil1' onChange='this.form.submit()'>$opt1</select></td></tr>
  <tr><td $c>Program Studi</td><td $c><select name='pmbprodi' onChange='this.form.submit()'>$optprodi</select></td></tr>
  </form>
  
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='pmbujian.hasil'>
  <tr><td $c>Luluskan jika nilai</td><td $c>>= <input type=text name='pmbnilailulus' value='$_SESSION[pmbnilailulus]' size=5 maxlength=5> 
    <input type=submit name='gos' value='Luluskan'>
    <font color=red>*) Hanya untuk data yg tampil</td></tr>
  </form>
  
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='pmbujian.hasil'>
  <tr><td $c>Set Grade:</td><td $c><select name='pmbhasilgrade'>$optgrade</select> 
    Dari #: <input type=text name='pmbhasildari' value='$_SESSION[pmbhasildari]' size=4 maxlength=4>
    Sampai #: <input type=text name='pmbhasilsampai' value='$_SESSION[pmbhasilsampai]' size=4 maxlength=4>
    <input type=submit name='gos' value='SetGrade'>
    </td></tr>
  </form>
  </table></p>";
}
function SetGrade() {
  // PERHATIKAN QUERY!!! "WHERE" harus sama dengan fungsi TampilkanDaftarUSM agar sesuai!!!
  // Filter: COPY & PASTE dari TampilkanDaftarUSM
  $whr = array();
  $whr[] = "PMBPeriodID='$_SESSION[pmbtahun]'";
  if (!empty($_SESSION['pmbfid'])) $whr[] = "PMBFormulirID='$_SESSION[pmbfid]'";
  if (!empty($_SESSION['pmbpil1'])) $whr[] = "Pilihan1='$_SESSION[pmbpil1]'";
  if (!empty($_SESSION['pmbprodi'])) $whr[] = "ProdiID='$_SESSION[pmbprodi]'";
  $where = implode(" and ", $whr);
  if (!empty($where)) $where = "where $where";
  // Tambahkan order yg sama dengan fungsi TampilkanDaftarUSM
  $order = "order by NilaiUjian desc, PMBID asc";
  
  // Buat query
  $pmbhasildari = $_REQUEST['pmbhasildari']-1;
  $pmbhasilsampai = $_REQUEST['pmbhasilsampai']+0;
  $rentang = $pmbhasilsampai - $pmbhasildari;
  $s = "select PMBID
    from pmb
    $where $order limit $pmbhasildari, $rentang";
  $r = _query($s);
  $_pmbid = array();
  while ($w = _fetch_array($r)) $_pmbid[] = "'" . $w['PMBID'] . "'";
  $pmbid = implode(', ', $_pmbid);
  //echo $pmbid;
  // *** Set Grade Nilai ***
  $_s1 = "update pmb set GradeNilai='$_REQUEST[pmbhasilgrade]' where PMBID in ($pmbid)";
  $_r1 = _query($_s1);
  DftrHasilUSM();
}
function TampilkanDaftarUSM() {
  if ($_SESSION['Cari'] != 'All') {
    $_cari2 = (!empty($_SESSION['pmbcari']))? " and p.$_SESSION[Cari] like '%$_SESSION[pmbcari]%' " : '';
  } else $_cari2 = '';

  // Daftar Test
  $_stest = "select PMBUSMID, Nama from pmbusm order by PMBUSMID";
  $_rtest = _query($_stest);
  
  $co = _num_rows($_rtest);
  
  if ($co > 0) {
      $_arrTest = array();
      $_jtest = _num_rows($_rtest);
      $_arrJenisTest = array();
      while ($_wtest = _fetch_array($_rtest)) {
        $_arrTest[] = $_wtest['PMBUSMID'];
        $_arrNamaTest[] = $_wtest['PMBUSMID'];
        $_arrJenisTest[] = "<b>".$_wtest['PMBUSMID']. "</b>: ".$_wtest['Nama'];
      }
      $_hdtest = implode("</th><th class=ttl>", $_arrNamaTest);
      $_hdtest = "<th class=ttl>$_hdtest</th>";
      $_strJenisTest = implode(", ", $_arrJenisTest);
      $_strJenisTest = "<p>Ujian Saringan Masuk: $_strJenisTest</p>";
  }
  // Filter
  $whr = array();
  $whr[] = "p.PMBPeriodID='$_SESSION[pmbtahun]'";
  if (!empty($_SESSION['pmbfid'])) $whr[] = "p.PMBFormulirID='$_SESSION[pmbfid]'";
  if (!empty($_SESSION['pmbpil1'])) $whr[] = "p.Pilihan1='$_SESSION[pmbpil1]'";
  if (!empty($_SESSION['pmbprodi'])) $whr[] = "p.ProdiID='$_SESSION[pmbprodi]'";
  $where = implode(" and ", $whr);
  if (!empty($where)) $where = "and $where";
  // query
  $s = "select p.PMBID, p.Nama, p.NA, p.JenisSekolahID, p.NilaiUjian, p.LulusUjian, p.GradeNilai,
      p.Pilihan1, p.Pilihan2, p.Pilihan3, p.Catatan, DetailNilai,
      sa.Nama as STT,
      concat(p.ProdiID, '. ', p0.Nama) as Prodi, 
      concat(p.Pilihan1, '. ', p1.Nama) as Pil1, 
      concat(p.Pilihan2, '. ', p2.Nama) as Pil2, 
      concat(p.Pilihan3, '. ', p3.Nama) as Pil3, 
      format(Harga, 2) as HRG 
    from pmb p
    left outer join statusawal sa on p.StatusAwalID=sa.StatusAwalID
    left outer join prodi p0 on p.ProdiID=p0.ProdiID
    left outer join prodi p1 on p.Pilihan1=p1.ProdiID
    left outer join prodi p2 on p.Pilihan2=p2.ProdiID
    left outer join prodi p3 on p.Pilihan3=p3.ProdiID
    where sa.TanpaTest='N' $where
    order by p.NilaiUjian desc, p.PMBID asc";
  $r = _query($s); $n = 0;
  TuliskanUpdatePilihan();
  
  echo $_strJenisTest;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl rowspan=2>#</th>
    <th class=ttl rowspan=2>PMB ID</th>
    <th class=ttl rowspan=2>Nama</th>
    
    <th class=ttl rowspan=2>Program Studi</th>
    <th class=ttl colspan=2 rowspan=2>Pilihan</th>
    <th class=ttl colspan=4>Nilai USM <input type=button name='Refresh' value='Refresh' onClick=\"location='?mnux=pmbujian.hasil'\"></th>
    <th class=ttl colspan=$_jtest>Detail Test</th>
    </tr>
    <tr><th class=ttl>Grade</th>
    <th class=ttl>Nilai</th><th class=ttl>Lulus</th><th class=ttl>Catatan</th>
    $_hdtest</tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $optpil = GetOpsiPilihan($w);
    $cklls = ($w['LulusUjian'] == 'Y')? 'checked' : '';
    $c = "class=cnn$w[LulusUjian]";
    
    // Detail Nilai
    $_DetailNilai = array();
    $w['DetailNilai'] = trim($w['DetailNilai'], '.');
    $_arrDetailNilai = explode(".", $w['DetailNilai']);
    for ($i=0; $i<sizeof($_arrDetailNilai); $i++) {
      $_det = explode(':', $_arrDetailNilai[$i]);
      $key = array_search($_det[0], $_arrTest);
      $_DetailNilai[$key] = $_det[1];
    }
    $_strDetailNilai = '';
    for ($i=0; $i<$_jtest; $i++) $_strDetailNilai .= "<td class=ul align=right>$_DetailNilai[$i]&nbsp;</td>";
    
    echo "<tr><td class=inp1>$n</td>
    <td $c>$w[PMBID]</td>
    <td $c title='$w[STT]'><font style='border-bottom: 1px dotted maroon'>$w[Nama]</a></td>

    <form action='pmbujian.hasil.pilihan.php' target=_blank onSubmit=\"return UpdatePilihan(this);\">
    <input type=hidden name='PMBID' value='$w[PMBID]'>
    <input type=hidden name='Pesan' value='Pilihan telah disimpan.'>
    <td class=ul><input type=text name='Prodi' value='$w[Prodi]' style='border: 0px'></td>
    <td class=ul>$optpil</td>
    <td class=ul><input type=submit name='Simpan' value='Pilih'></td>
    </form>
    
    <td $c align=center><b>$w[GradeNilai]</td>
    <form action='pmbujian.hasil.nilai.php' target=_blank width=600 height=600>
    <input type=hidden name='PMBID' value='$w[PMBID]'>
    <input type=hidden name='Pesan' value='Perubahan Nilai telah disimpan.'>
    <td $c><input type=text name='NilaiUjian' value='$w[NilaiUjian]' size=3 maxlength=4></td>
    <td $c><input type=checkbox name='LulusUjian' value='Y' title='Lulus Ujian?' $cklls></td>
    <td $c><input type=text name='Catatan' value='$w[Catatan]' size=20 maxlength=100>
    <input type=submit name='Simpan' value='Simpan'></td>
    </form>
    
    $_strDetailNilai
    </tr>";
  }
  echo "</table></p>";
  // Tampilkan yg tidak ikut ujian
  $arrTidakUjian = array();
  $s1 = "select * from statusawal where TanpaTest='Y' order by StatusAwalID";
  $r1 = _query($s1);
  while ($w1 = _fetch_array($r1)) {
    $arrTidakUjian[] = $w1['Nama'];
  }
  $strTidakUjian = implode(', ', $arrTidakUjian);
  if (!empty($strTidakUjian)) {
    echo "<p><b>Catatan: </b><br />
    Pendaftar dengan status: <b>$strTidakUjian</b> tidak perlu mengikuti test masuk.</p>";
  }
}
function TuliskanUpdatePilihan() {
  echo <<<END
  <SCRIPT LANGUAGE="javascript1.2">
  <!--
  function UpdatePilihan(form) {
    form.Prodi.value = form.Pilihanku.value;
    return true;
  }
  -->
  </SCRIPT>
END;
}
function GetOpsiPilihan($w) {
  global $_PMBMaxPilihan;
  $pil = '';
  for ($i=1; $i<=$_PMBMaxPilihan; $i++) {
    if (!empty($w["Pilihan$i"])) $pil .= "<option value='".$w["Pil$i"]."'>".$w["Pil$i"]."</option>";
  }
  return "<select name='Pilihanku'>$pil</select>";
}
function TampilkanDaftarUSM_x() {
  global $_defmaxrow;
  include_once "class/lister.class.php";
  if ($_SESSION['Cari'] != 'All') {
    $_cari2 = (!empty($_SESSION['pmbcari']))? " and p.$_SESSION[Cari] like '%$_SESSION[pmbcari]%' " : '';
  } else $_cari2 = '';

  // Filter
  $whr = array();
  $whr[] = "p.PMBPeriodID='$_SESSION[pmbtahun]'";
  if (!empty($_SESSION['pmbfid'])) $whr[] = "p.PMBFormulirID='$_SESSION[pmbfid]'";
  if (!empty($_SESSION['pmbpil1'])) $whr[] = "p.Pilihan1='$_SESSION[pmbpil1]'";
  if (!empty($_SESSION['pmbprodi'])) $whr[] = "p.ProdiID='$_SESSION[pmbprodi]'";
  $where = implode(" and ", $whr);
  if (!empty($where)) $where = "where $where";
  
  $pagefmt = "<a href='?mnux=pmbujian.hasil&SRHSL==STARTROW='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";
  
  $lister = new lister;
  $lister->tables = "pmb p
    left outer join prodi p0 on p.ProdiID=p0.ProdiID
    left outer join prodi p1 on p.Pilihan1=p1.ProdiID
    left outer join prodi p2 on p.Pilihan2=p2.ProdiID
    left outer join prodi p3 on p.Pilihan3=p3.ProdiID
    $where
    order by p.NilaiUjian desc";
	//echo $lister->tables;
    $lister->fields = "p.PMBID, p.Nama, p.NA, p.JenisSekolahID, p.NilaiUjian, p.LulusUjian, p.GradeNilai,
      p.Pilihan1, p.Pilihan2, p.Pilihan3,
      p0.Nama as Prodi, p1.Nama as Pil1, p2.Nama as Pil2, p3.Nama as Pil3, 
      format(Harga, 2) as HRG ";
    $lister->startrow = $_REQUEST['SRHSL']+0;
    $lister->maxrow = $_defmaxrow;
    $lister->headerfmt = "<table class=box cellspacing=1 cellpadding=4>
      <tr>
	  <th class=ttl>#</th><th class=ttl>Kode</th>
	  <th class=ttl>Nama</th>
	  <th class=ttl>Nilai USM</th>
	  <th class=ttl>Program Studi</th>
	  <th class=ttl>Pilihan1</th>
	  <th class=ttl>Pilihan2</th>
	  <th class=ttl>Pilihan3</th>
      <th class=ttl>Asal</th>
	  <th class=ttl>Lulus</th>
	  <th class=ttl>Grade</th>
      </tr>";
    $lister->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
      <td class=cnn=LulusUjian= nowrap><a href=\"?mnux=pmbform&gos=PMBEdt0&md=0&pmbid==PMBID=\"><img src='img/edit.png' border=0>
      =PMBID=</a></td>
	  <td class=cnn=LulusUjian= nowrap>=Nama=</a></td>
	  <form action='?' method=POST>
	    <input type=hidden name='mnux' value='pmbujian.hasil'>
	    <input type=hidden name='gos' value='PMBHasilSav'>
	    <input type=hidden name='PMBID' value='=PMBID='>
	    <input type=hidden name='SRHSL' value='$_REQUEST[SRHSL]'>
	    <td class=cnn=LulusUjian= nowrap><input type=text name='NilaiUjian' value='=NilaiUjian=' size=4 maxlength=4>
	    <input type=submit name='Simpan' value='Simpan'></td>
	  </form>
	  <td class=cnn=LulusUjian= nowrap>=Prodi=&nbsp;</td>
	  
	  <form action='?' method=POST>
	    <input type=hidden name='mnux' value='pmbujian.hasil'>
	    <input type=hidden name='gos' value='PMBHasilPil'>
	    <input type=hidden name='PMBID' value='=PMBID='>
	    <input type=hidden name='SRHSL' value='$_REQUEST[SRHSL]'>
	    <td class=cnn=LulusUjian= nowrap><input type=checkbox name='PIL' value='=Pilihan1=' onClick='this.form.submit()'>
	    =Pil1=&nbsp;</td>
	    </form>
	    
	  <form action='?' method=POST>
	    <input type=hidden name='mnux' value='pmbujian.hasil'>
	    <input type=hidden name='gos' value='PMBHasilPil'>
	    <input type=hidden name='PMBID' value='=PMBID='>
	    <input type=hidden name='SRHSL' value='$_REQUEST[SRHSL]'>
	    <td class=cnn=LulusUjian= nowrap><input type=checkbox name='PIL' value='=Pilihan2=' onClick='this.form.submit()'>
	    =Pil2=&nbsp;</td>
	    </form>

      <form action='?' method=POST>
	    <input type=hidden name='mnux' value='pmbujian.hasil'>
	    <input type=hidden name='gos' value='PMBHasilPil'>
	    <input type=hidden name='PMBID' value='=PMBID='>
	    <input type=hidden name='SRHSL' value='$_REQUEST[SRHSL]'>
	    <td class=cnn=LulusUjian= nowrap><input type=checkbox name='PIL' value='=Pilihan3=' onClick='this.form.submit()'>
	    =Pil3=&nbsp;</td>
	    </form>

      <td class=cnn=LulusUjian=>=JenisSekolahID=</td>
	  <td class=cnn=LulusUjian=><center><a href='?mnux=pmbujian.hasil&SRHSL=$_REQUEST[SRHSL]&gos=PMBHasilLulus&Lulus==LulusUjian=&PMBID==PMBID='>
	    <img src='img/=LulusUjian=.gif' border=0></a></td>
	  <td class=cnn=LulusUjian=><a href='?mnux=pmbujian.hasil&gos=EdtGrade&SRHSL=$_REQUEST[SRHSL]&PMBID==PMBID='><img src='img/edit.png' border=0> =GradeNilai=</a></td>
	  </tr>";
    $lister->footerfmt = "</table>";
    $halaman = $lister->WritePages ($pagefmt, $pageoff);
    $TotalNews = $lister->MaxRowCount;
    $usrlist = $lister->ListIt () .
	  "<br>Halaman: $halaman<br>
	  Total: $TotalNews";
    echo $usrlist;
}
function EdtGrade() {
  $arr = GetFields("pmb p left outer join prodi pr on p.ProdiID=pr.ProdiID", 
    'p.PMBID', $_REQUEST['PMBID'], "p.PMBID, p.Nama, pr.Nama as PROD, p.NilaiUjian, p.GradeNilai, p.JenisSekolahID");
  $opt = GetOption2('pmbgrade', "GradeNilai", 'GradeNilai', $arr['GradeNilai'], '', 'GradeNilai');
  echo "<table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='pmbujian.hasil'>
  <input type=hidden name='gos' value='GradeSav'>
  <input type=hidden name='SRHSL' value='$_REQUEST[SRHSL]'>
  <input type=hidden name='PMBID' value='$arr[PMBID]'>
  <tr><th class=ttl colspan=2>Edit Grade Nilai</th></tr>
  <tr><td class=inp1>PMB ID</td><td class=ul><b>$arr[PMBID]</td></tr>
  <tr><td class=inp1>Nama</td><td class=ul><b>$arr[Nama]</td></tr>
  <tr><td class=inp1>Jenis Sekolah</td><td class=ul><b>$arr[JenisSekolahID]</td></tr>
  <tr><td class=inp1>Grade Nilai</td><td class=ul><select name='PMBGrade'>$opt</select></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=pmbujian.hasil&SRHSL=$_REQUEST[SRHSL]'\"></td></tr>
  </form></table>";
}
function GradeSav() {
  $PMBID = $_REQUEST['PMBID'];
  $PMBGrade = $_REQUEST['PMBGrade'];
  $s = "update pmb set GradeNilai='$PMBGrade' where PMBID='$PMBID'";
  $r = _query($s);
  DftrHasilUSM();
}
function PMBHasilSav() {
  $PMBID = $_REQUEST['PMBID'];
  $NilaiUjian = $_REQUEST['NilaiUjian']+0;
  $s = "update pmb set NilaiUjian='$NilaiUjian' where PMBID='$PMBID'";
  $r = _query($s);
  DftrHasilUSM();
}
function PMBHasilPil() {
  $PMBID = $_REQUEST['PMBID'];
  $Pil = $_REQUEST['PIL'];
  $Pil = trim($Pil, " ");
  if (!empty($Pil)) {
    $s = "update pmb set ProdiID='$Pil' where PMBID='$PMBID'";
    $r = _query($s);
  }
  DftrHasilUSM();
}
function PMBHasilLulus() {
  $PMBID = $_REQUEST['PMBID'];
  $Lulus = $_REQUEST['Lulus'];
  $_Lulus = ($Lulus == 'Y')? 'N' : 'Y';
  $s = "update pmb set LulusUjian='$_Lulus' where PMBID='$PMBID'";
  $r = _query($s);
  DftrHasilUSM();
}
function Luluskan() {
  $pmbnilailulus = $_REQUEST['pmbnilailulus']+0;
  if ($pmbnilailulus > 0) {
    $whr = array();
    $whr[] = "PMBPeriodID='$_SESSION[pmbtahun]'";
    $whr[] = "NilaiUjian >= $pmbnilailulus";
    if (!empty($_SESSION['pmbfid'])) $whr[] = "PMBFormulirID='$_SESSION[pmbfid]'";
    if (!empty($_SESSION['pmbpil1'])) $whr[] = "Pilihan1='$_SESSION[pmbpil1]'";
    if (!empty($_SESSION['pmbprodi'])) $whr[] = "ProdiID='$_SESSION[pmbprodi]'";
    $where = implode(" and ", $whr);
    if (!empty($where)) $where = "where $where";
    $s = "update pmb set LulusUjian='Y' $where";
    $r = _query($s);
  }
  DftrHasilUSM();
}
function DftrHasilUSM() {
  TampilkanFilterHasilUSM();
  TampilkanDaftarUSM();
}

// *** Parameters ***
$pmbperiod = GetSetVar("pmbperiod");
$pmbfid = GetSetVar('pmbfid');
$pmbpil1 = GetSetVar('pmbpil1');
$pmbprodi = GetSetVar('pmbprodi');
$pmbnilailulus = GetSetVar('pmbnilailulus', 80);
$_pmbhasilgrade = GetSetVar('pmbhasilgrade', 'A');
$_pmbhasildari = GetSetVar('pmbhasildari', 1);
$_pmbhasilsampai = GetSetVar('pmbhasilsampai', 10);
$pmbtahun = GetSetVar('pmbtahun', $pmbperiod);
if (empty($pmbtahun)) {
  $pmbperiod = GetaField("pmbperiod", "NA", 'N', "PMBPeriodID");
  $_SESSION['pmbperiod'] = $pmbperiod;
}
$gos = (empty($_REQUEST['gos']))? 'DftrHasilUSM' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Hasil USM");
$gos();
?>
