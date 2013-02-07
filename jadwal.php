<?php
// Author: Emanuel Setio Dewo
// 02 Feb 2006

// *** Yg boleh akses harga ***
$LevelAksesHarga = ".1.20.50.";
$LevelUpdateDeleteTambah = ".1.";
// *** Functions ***
function DftrJdwl() {
  global $arrID;
  $arrVld = GetFields('tahun',
    "ProdiID='$_SESSION[prodi]' and ProgramID='$_SESSION[prid]' and TahunID",
    $_SESSION['tahun'], '*');
  if (empty($arrVld)) {
    echo ErrorMsg("Tahun Akademik Belum Dibuat",
    "Tahun Akademik <b>$_SESSION[tahun]</b> untuk Program <b>$_SESSION[prid]</b> dan Program Studi <b>$_SESSION[prodi]</b> belum dibuat.<br />
    Hubungi Kepala Akademik/Jurusan.");
  }
  else {
    HitMhswDaftarJadwal();
    TampilkanMenuJadwal();
    TampilkanJadwal();
  }
}
function TampilkanMenuJadwal(){
  $optjenjad = GetOption2('jenisjadwal', "concat(JenisJadwalID, ' - ', Nama)", "JenisJadwalID", $_SESSION['jenjad'], '', "JenisJadwalID");
  echo "<p><a name='Atas'></a>
  <p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp>Filter Jenis</td>
  <form action='?'>
  <td class=ul>Filter: <select name='jenjad' onChange='this.form.submit()'>$optjenjad</select> Kosongkan jika ingin melihat semua.</td></tr>
  </form>
  </table></p>
  
  <a href='?mnux=jadwal&md=1&gos=JdwlEdt&md=1' accesskey='T'>Tambah Jadwal [Alt+T]</a> |  
  <a href='jadwal.rpt.php?gos=SemuaJadwal&tahun=$_SESSION[tahun]&prodi=$_SESSION[prodi]&prid=$_SESSION[prid]' target=_blank><img src='img/printer.gif'> Jadwal utk Mhsw</a> |
  <a href='jadwal.rpt.php?gos=JdwlperDosenCetak&tahun=$_SESSION[tahun]&prodi=$_SESSION[prodi]&prid=$_SESSION[prid]' target=_blank><img src='img/printer.gif'> Urut per Dosen</a> |
  <a href='jadwal.rpt.php?gos=JdwlResponsiCetak&tahun=$_SESSION[tahun]&prodi=$_SESSION[prodi]&prid=$_SESSION[prid]' target=_blank><img src='img/printer.gif'> MK Responsi</a> |
  <a href='jadwal.rpt.php?gos=JdwlKuliahCetak&tahun=$_SESSION[tahun]&prodi=$_SESSION[prodi]&prid=$_SESSION[prid]' target=_blank><img src='img/printer.gif'> Hanya Matakuliah</a> |
  <a href='jadwal.rpt.php?gos=JdwlSemuaCetak&tahun=$_SESSION[tahun]&prodi=$_SESSION[prodi]&prid=$_SESSION[prid]' target=_blank><img src='img/printer.gif'> Kuliah/Responsi</a>
  </p>";
}
function BuatSelectCetak($jdwl){
	 $s = "select jd.DosenID, jd.JenisDosenID, jd.JadwalDosenID, d.Nama, d.Gelar, concat(d.Nama, ', ', d.Gelar) as DSN
    from jadwaldosen jd
      left outer join dosen d on jd.DosenID=d.Login
    where jd.JadwalID='$jdwl[JadwalID]'
    order by d.Nama";
  $r = _query($s);
	$sel .= "<form action='jadwal.cetakdh.php' method=post>
				 <input type=hidden name='JadwalID' value='$jdwl[JadwalID]'>
				 <input type=hidden name='ctk' value='0'>
				 <select name=DSN onChange='this.form.submit()'>";
  while ($w = _fetch_array($r)) {
		$sel .= "<option value=$w[DosenID]>$w[DSN]</option>";
  }
	$sel .= "</select></form>"; 
	return $sel;
}

function TampilkanJadwal() {
  global $thn;
  $hdrjdwl = "<tr><th class=ttl>ID</th>
    <th class=ttl>Waktu</th>
    <th class=ttl>Ruang</th>
    <th class=ttl>Kode MK</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>Jen</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Dosen</th>
    <th class=ttl>Jml<br />Mhsw</th>
    <th class=ttl>Jml<br />Mhsw<br />KRS</th>
    <th class=ttl colspan=2 title='Kelas Serial'>Serial</th>
    <th class=ttl>Hrg<br />Std?</th>
    <th class=ttl><img src='img/printer.gif'></th>
		<th class=ttl Title='Cetak Label'><img src='img/printer.gif'></th>
    <th class=ttl title='Presensi'>Pres</th>
    <th class=ttl title='Prasyarat'>Pra</th>
    <th class=ttl title='Hapus Jadwal'>Hapus</th>
    </tr>
  ";
  $_jj = (empty($_SESSION['jenjad']))? '' : "and j.JenisJadwalID='$_SESSION[jenjad]' ";
  $s = "select j.*, r.KampusID, d.Nama as NamaDosen, concat(d.Nama, ', ', d.Gelar) as DSN,
    time_format(j.JamMulai, '%H:%i') as Mulai,
    time_format(j.JamSelesai, '%H:%i') as Selesai
    from jadwal j
      left outer join mk mk on j.MKID=mk.MKID
      left outer join dosen d on j.DosenID=d.Login
      left outer join ruang r on j.RuangID=r.RuangID
    where j.NamaKelas<>'KLINIK' and j.NA<>'Y' $_jj
      and j.KodeID='$_SESSION[KodeID]' and j.TahunID='$_SESSION[tahun]'
      and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0
      and INSTR(j.ProgramID, '.$_SESSION[prid].')>0
    order by j.HariID, j.JamMulai, j.MKKode, j.NamaKelas";
  $r = _query($s);

  // Tampilkan daftar jadwal
  $hari = -1;
  $gotohari = DftrHari();
  echo "<p><table class=box cellspacing=1 width=100%>";
  while ($w = _fetch_array($r)) {
    if ($hari != $w['HariID']) {
      $hari = $w['HariID'];
      $NamaHari = GetaField('hari', 'HariID', $hari, 'Nama');
      echo "<tr><td class=ul colspan=12><b><a name='$hari'></a>$NamaHari</b>
       <a href='#Atas' title='Kembali ke atas'>^</a> $gotohari&nbsp;&nbsp;&nbsp;&nbsp;<<&nbsp;<a href='?mnux=jadwal&md=1&gos=JdwlEdt&md=1&hari=$w[HariID]'>Tambah Jadwal</a> >></td></tr>";
      echo $hdrjdwl;
    }
    $c = ($w['Final'] == 'Y')? "class=inp1" : "class=ul";
    // assisten dosen
    $assisten = GetAssistenDosen($w);
		//$func = (!empty($assisten)) ? BuatSelectCetak($w) : '';
		// Kelas Serial
    $ser = ($w['JadwalSer'] == 0)? '' : "<abbr title='Serial dgn Jadwal: $w[JadwalSer]'>".$w['JadwalSer']."</abbr>";
    $tambahser = ($w['JadwalSer'] == 0)? "<a href='?mnux=jadwal&gos=JdwlEdt&md=1&JadwalSer=$w[JadwalID]&MKID=$w[MKID]'><img src='img/share.gif'></a>" : '';
    $jumlahser = ($w['JumlahKelasSerial'] >0)? $w['JumlahKelasSerial'] : '&nbsp;';
    // Harga standar
    $hrg = ($w['HargaStandar'] == 'Y')? "<img src='img/$w[HargaStandar].gif'>" : number_format($w['Harga']);
    // ambil prasyarat
    $arrpra = GetArrayTable("select concat(mk.MKKode, ' - ', mk.Nama, ' (SKS min: ', mk.SKSMin, ', IPK min: ', mk.IPKMin, ')') as PRA 
      from mkpra
        left outer join mk on mkpra.PraID=mk.MKID
      where mkpra.MKID='$w[MKID]' ", 
      'PRA', 'PRA', $_lf);
    $strpra = (empty($arrpra))? '&nbsp;' : "<a name='$w[JadwalID]' onClick=\"javascript:alert('$arrpra')\"><img src='img/check.gif'></a>";
    // Validasi cetak Daftar Mhsw
    //$dftrmhsw = ($thn['TglUbahKRSSelesai'] < date('Y-m-d'))? '' : "<a href='jadwal.cetak.mhsw.php?RincianMhsw&JadwalID=$w[JadwalID]' Title='Daftar Mahasiswa' target=_blank>Dftr</a>";
    if ($w['Final'] == 'Y') {
      $edit = '&nbsp;';
      $hps = '&nbsp;';
    }
    else {       
        $edit = "<a href='?mnux=jadwal&gos=JdwlEdt&md=0&JadwalID=$w[JadwalID]'><img src='img/edit.png'><a name='$w[JadwalID]'></a>";
        $hps = "<a href='?mnux=jadwal&gos=JdwlDel&JadwalID=$w[JadwalID]'><img src='img/del.gif'></a>";
    }
    
    if ($thn['TglKRSMulai'] < date('Y-m-d')) {
      if ($_SESSION['_LevelID'] == 1 || $_SESSION['_LevelID'] == 20 || $_SESSION['_LevelID'] == 41) {
        $edit = "<a href='?mnux=jadwal&gos=JdwlEdt&md=0&JadwalID=$w[JadwalID]'><img src='img/edit.png'><a name='$w[JadwalID]'></a>";
        $hps = "<a href='?mnux=jadwal&gos=JdwlDel&JadwalID=$w[JadwalID]'><img src='img/del.gif'></a>";
      } else {
        $edit = "";
        $hps = "&nbsp;";
      }  
    }
		
		if (($thn['TglAutodebetSelesai'] >= date('Y-m-d')) || $_SESSION['_LevelID'] == 1 || $_SESSION['_LevelID'] == 20) {
				$dhksem = "<a href='jadwal.cetak.mhsw.php?RincianMhsw&JadwalID=$w[JadwalID]' Title='Daftar Mahasiswa' target=_blank>DHK Sem</a>";
			} else {
			  $dhksem = '';
			}
		
    //$edit = ($thn['TglKRSMulai']) > date('Y-m-d')? '' : "<a href='?mnux=jadwal&gos=JdwlEdt&md=0&JadwalID=$w[JadwalID]'><img src='img/edit.png'><a name='$w[JadwalID]'></a>";

    echo "<tr>
      <td class=inp1 nowrap>$edit $w[JadwalID]</td>
      <td $c>$w[Mulai]-$w[Selesai]</td>
      <td $c>$w[KampusID]-$w[RuangID]</td>
      <td $c>$w[MKKode]</td>
      <td $c>$w[Nama]</td>
      <td $c>$w[NamaKelas]&nbsp;</td>
      <td $c align=center>$w[JenisJadwalID]</td>
      <td $c>$w[SKS]/$w[SKSHonor]</td>
      <td $c><a href='?mnux=jadwal&gos=AssDsnEdt&JadwalID=$w[JadwalID]' title='Tambah Dosen Pengampu'><img src='img/share.gif'></a>
        <abbr title='$w[DSN]'>$w[NamaDosen]</abbr>
      $assisten</td>
      <td $c align=right>$w[JumlahMhsw]/$w[Kapasitas]</td>
      <td $c align=right>$w[JumlahMhswKRS]</td>
      <td $c align=center title='Kelas Serial'>&nbsp;$ser$tambahser</td>
      <td $c align=right title='Jumlah Kelas Serial'>$jumlahser</td>
      <td $c align=center>$hrg</td>
      <td $c><a href='jadwal.cetakdh.php?JadwalID=$w[JadwalID]&ctk=0' title='Daftar Hadir Kuliah' target=_blank>DHK</a>$func $dhksem</td>
			<td $c><a href='cetak.label.jdwl.php?JadwalID=$w[JadwalID]&tahun=$_SESSION[tahun]&prodi=$_SESSION[prodi]&prid=$_SESSION[prid]&asal=1' title='Cetak Label Map'>LABEL</a></td>
      <td $c title='Presensi'><a href='?mnux=jadwal.pres&dosen=$w[DosenID]&JadwalID=$w[JadwalID]'><img src='img/check.gif'></a> $w[Kehadiran]</td>
      <td $c title='Matakuliah prasyarat'>$strpra</td>
      <td $c align=center title='Hapus'>$hps</td></tr>
      </tr>";
  }
  echo "</table></p>";
  // Tampilkan pesan
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=100%>
  <tr><td class=ul nowrap><b>Jadwal Serial</b></td>
    <td class=ul>Jadwal Serial adalah jadwal matakuliah yang dipecah menjadi beberapa kali
    pertemuan dalam 1 minggu. Karena sebenarnya adalah 1 jadwal matakuliah,
    maka mahasiswa wajib hadir di setiap pertemuan
    dan masing-masing pertemuan memiliki isian presensi sendiri.
    Nilai akan diperhitungkan dengan jumlah SKS-nya.</td></tr>
  <tr><td class=ul nowrap><b>Pres (Presensi)</b></td>
    <td class=ul>Memasukkan presensi dosen dan mahasiswa.</td></tr>
  </table></p>";
}
function GetAssistenDosen($jdwl) {
  $s = "select jd.DosenID, jd.JenisDosenID, jd.JadwalDosenID, d.Nama, d.Gelar, concat(d.Nama, ', ', d.Gelar) as DSN
    from jadwaldosen jd
      left outer join dosen d on jd.DosenID=d.Login
    where jd.JadwalID='$jdwl[JadwalID]'
    order by d.Nama";
  $r = _query($s);
  $a = array();
  while ($w = _fetch_array($r)) {
//<a href=''><img src='img/del.gif'>
		$ctkdhk = ($w['JenisDosenID'] == 'DSN') ? "<a href='jadwal.cetakdh.php?JadwalID=$jdwl[JadwalID]&DSN=$w[DosenID]&ctk=0' title='Daftar Hadir Kuliah' target=_blank>[CTK]</a>" : '';
    $a[] = "<a href='?mnux=jadwal&gos=DftrJdwl&slnt=jadwal.lib&slntx=DelAssDsn&JDID=$w[JadwalDosenID]' title='Hapus Dosen Pengampu'><img src='img/del.gif'></a>
      <abbr title='$w[DSN]'>$w[Nama] $ctkdhk</abbr>";
  }
  $ret = implode('<br />', $a);
  return (empty($ret))? '' : "<br />" . $ret;
}
function DftrHari() {
  $s = "select HariID, Nama from hari order by HariID";
  $r = _query($s);
  $a = array();
  while ($w = _fetch_array($r)) {
    $a[] = "<a href='#$w[HariID]'>$w[Nama]</a>";
  }
  return implode(', ', $a);
}
function ResetArrJadwal() {
  $w = array();
  $w['JadwalID'] = 0;
  $w['JadwalPar'] = (empty($_REQUEST['JadwalPar']))? 0 : $_REQUEST['JadwalPar'];
  $w['JadwalSer'] = (empty($_REQUEST['JadwalSer']))? 0 : $_REQUEST['JadwalSer'];
  $w['KodeID'] = $_SESSION['KodeID'];
  $w['TahunID'] = $_SESSION['tahun'];
  $w['ProdiID'] = '.'.$_SESSION['prodi'].'';
  $w['ProgramID'] = '.'.$_SESSION['prid'].'.';
  $w['NamaKelas'] = '';
  $w['JenisJadwalID'] = 'K';
  $w['MKID'] = (empty($_REQUEST['MKID']))? 0 : $_REQUEST['MKID'];
  $w['JadwalJenisID'] = 0;
  $w['MKKode'] = '';
  $w['Nama'] = '';
  $w['HariID'] = '';
  $w['JamMulai'] = '08:00';
  $w['JamSelesai'] = '09:59';
  $w['SKSAsli'] = 0;
  $w['SKS'] = -1;
  $w['SKSHonor'] = -1;
  $w['DosenID'] = '';
  $w['RencanaKehadiran'] = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'DefKehadiran');
  $w['Kehadiran'] = 0;
  $w['KehadiranMin'] = 0;
  $w['JumlahMhsw'] = 0;
  $w['Kapasitas'] = -1;
  $w['RuangID'] = '';
  $w['HargaStandar'] = 'Y';
  $w['Harga'] = 0;
  $w['NA'] = 'N';
  return $w;
}
function AmbilArrJadwal() {
  $w = array();
  $w['JadwalID'] = $_REQUEST['JadwalID'];
  $w['JadwalPar'] = $_REQUEST['JadwalPar'];
  $w['JadwalSer'] = $_REQUEST['JadwalSer'];
  $w['KodeID'] = $_SESSION['KodeID'];
  $w['TahunID'] = $_SESSION['tahun'];
  $ProdiID = $_REQUEST['ProdiID'];
  $w['ProdiID'] = (empty($ProdiID))? '' : '.'.TRIM(implode('.', $ProdiID), '.').'.';
  $ProgramID = $_REQUEST['ProgramID'];
  $w['ProgramID'] = (empty($ProgramID))? '' : '.'.TRIM(implode('.', $ProgramID), '.').'.';
  $w['NamaKelas'] = $_REQUEST['NamaKelas'];
  $w['JenisJadwalID'] = $_REQUEST['JenisJadwalID'];
  $w['MKID'] = $_REQUEST['MKID'];
  $w['JadwalJenisID'] = $_REQUEST['JadwalJenisID'];
  $w['MKKode'] = $_REQUEST['MKKode'];
  $w['Nama'] = $_REQUEST['Nama'];
  $w['HariID'] = $_REQUEST['HariID'];
  $w['JamMulai'] = $_REQUEST['JamMulai'];
  $w['JamSelesai'] = $_REQUEST['JamSelesai'];
  $w['SKSAsli'] = $_REQUEST['SKSAsli']+0;
  $w['SKS'] = $_REQUEST['SKS']+0;
  $w['SKSHonor'] = $_REQUEST['SKSHonor']+0;
  //$DosenID = $_REQUEST['DosenID'];
  $w['DosenID'] = $_REQUEST['DosenID'];
  $w['RencanaKehadiran'] = $_REQUEST['RencanaKehadiran']+0;
  $w['Kehadiran'] = $_REQUEST['Kehadiran']+0;
  $w['KehadiranMin'] = $_REQUEST['KehadiranMin']+0;
  $w['JumlahMhsw'] = $_REQUEST['JumlahMhsw'];
  $w['Kapasitas'] = $_REQUEST['Kapasitas'];
  $w['RuangID'] = $_REQUEST['RuangID'];
  $w['HargaStandar'] = $_REQUEST['HargaStandar'];
  $w['Harga'] = $_REQUEST['Harga'];
  $w['NA'] = $_REQUEST['NA'];
  return $w;
}
function CariMKScript() {
  echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function carimk(frm) {
    lnk = "carimk.php?MKKode="+frm.MKKode.value+"&Nama="+frm.Nama.value+"&prodi="+frm.prodi.value;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  function caridosen(frm) {
    lnk = "caridosen.php?DosenID="+frm.DosenID.value+"&NamaDosen="+frm.NamaDosen.value+"&prodi="+frm.prodi.value;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  -->
  </script>
EOF;
}

function CariDosenAjax(){
//  echo <<<EOF
//  <SCRIPT LANGUAGE="JavaScript1.2">
//  <!--
//    function caridosenajax(frm) {
//      $.ajax({  
//        type: "POST",
//        url: "caridosenajax.php?DosenID="+frm.DosenID.value+"&NamaDosen="+frm.NamaDosen.value+"&prodi="+frm.prodi.value,
//        data: "DosenID="+frm.DosenID.value+"&NamaDosen="+frm.NamaDosen.value+"&prodi="+frm.prodi.value,
//        success: function(msg){
//          $('#NamaDosen').val(msg);
//        }
//      });
//    }
//    
//    function carimkajax(frm) {
//      $.ajax({
//        type: "POST",
//        url: "carimkajax.php?MKKode="+frm.MKKode.value+"&Nama="+frm.Nama.value+"&prodi="+frm.prodi.value,
//        data: "MKKode="+frm.MKKode.value+"&Nama="+frm.Nama.value+"&prodi="+frm.prodi.value,
//        success: function(msg) {
//          var parsed = msg.split("|");
//          for (var i=0; i<parsed.length; i++) {
//            $('#NamaMK').val(parsed[0]);
//            $('#MKID').val(parsed[1]);
//          }
//        }
//      });
//    }
//  -->
//  </script>
//EOF;
  
  
  echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript">
  
    function caridosenajax(frm) {
      $.ajax({  
        type: "POST",
        url: "caridosenajax.php?DosenID="+frm.DosenID.value+"&NamaDosen="+frm.NamaDosen.value+"&prodi="+frm.prodi.value,
        data: "DosenID="+frm.DosenID.value+"&NamaDosen="+frm.NamaDosen.value+"&prodi="+frm.prodi.value,
        success: function(msg){
          $('#NamaDosen').val(msg);
        }
      });
    }
    
    function carimkajax(frm) {
      $.ajax({
        type: "POST",
        url: "carimkajax.php?MKKode="+frm.MKKode.value+"&Nama="+frm.Nama.value+"&prodi="+frm.prodi.value,
        data: "MKKode="+frm.MKKode.value+"&Nama="+frm.Nama.value+"&prodi="+frm.prodi.value,
        success: function(msg) {
          var parsed = msg.split("|");
          for (var i=0; i<parsed.length; i++) {
            $('#NamaMK').val(parsed[0]);
            $('#MKID').val(parsed[1]);
          }
        }
      });
    }
  
  </script>
EOF;
}

function PhpAutocomplete($prodi){
	echo "<script type=\"text/javascript\">
		function findValue(li) {
			if( li == null ) return alert(\"No match!\");
			// if coming from an AJAX call, let's use the CityId as the value
			if( li.extra ) var sValue = li.extra[0];
			// otherwise, let's just display the value in the text box
			//else var sValue = li.selectValue;
			//alert(\"The value you selected was: \" + li.extra.length);
			if (li.extra.length > 1) {
        $('#MKKode').val(li.extra[0]);
        $('#MKID').val(li.extra[1]);
      }
			else $('#DosenID').val(li.extra[0]);
		}

		function selectItem(li) {
			findValue(li);
		}

		function formatItem(row) {
			return row[1] + \"<br /><i>\" + row[0] + '</i><br />';
		}

		$(document).ready(function() {
		  $
			$(\"#NamaDosen\").autocomplete(
				\"caridosenajax.php?prodi=$prodi\",
				{
					delay:1000,
					minChars:4,
					matchSubset:1,
					matchContains:1,
					cacheLength:10,
					onItemSelect:selectItem,
					onFindValue:findValue,
					formatItem:formatItem,
					autoFill:true,
					height:200
				})
				
				$(\"#NamaMK\").autocomplete(
				\"carimkajax.php?prodi=$prodi\",
				{ 
					delay:1000,
					minChars:4,
					matchSubset:1,
					matchContains:1,
					cacheLength:10,
					onItemSelect:selectItem,
					onFindValue:findValue,
					formatItem:formatItem,
					autoFill:true,
					height:200
				})
		});
		</script>";
}

function JdwlEdt() {
  global $LevelAksesHarga;
  CheckFormScript('MKKode,NamaKelas,DosenID,HariID,JamMulai,JamSelesai');
  CariMKScript();
  CariDosenAjax();
  PhpAutocomplete($_SESSION['prodi']);
  $md = $_REQUEST['md']+0;
	$hari = $_REQUEST['hari'];
  if ($md == 0) {
    $w = ($_REQUEST['GAGAL'] == 1)? AmbilArrJadwal() : GetFields('jadwal', "JadwalID", $_REQUEST['JadwalID'], '*');
    $jdl = "Edit Jadwal";
  }
  else {
    $w = ($_REQUEST['GAGAL'] == 1)? AmbilArrJadwal() : ResetArrJadwal();
    $jdl = "Tambah Jadwal";
    $w['DosenID'] = 5000;
    $w['RencanaKehadiran'] = 14;
		$w['HariID'] = $hari;
  }
  // cek jika kelas serial
  if (!empty($w['JadwalSer'])) {
    $serial = GetFields('jadwal', 'JadwalID', $w['JadwalSer'], 'JadwalID, MKID, MKKode, Nama, NamaKelas, JenisJadwalID, DosenID');
    $w['MKID'] = $serial['MKID'];
    $w['MKKode'] = $serial['MKKode'];
    $w['Nama'] = $serial['Nama'];
    $w['NamaKelas'] = $serial['NamaKelas'];
    $w['JenisJadwalID'] = $serial['JenisJadwalID'];
    $w['DosenID'] = $serial['DosenID'];
  }
  if ($_SESSION['prodi'] == '99') {
    // Daftar prodi yg bisa diakses
    $_ProdiID = trim($_SESSION['_ProdiID'], ',');
    //echo $_ProdiID;
    $arrProdi = explode(',', $_ProdiID);
    $_prodi = '';
    for ($i = 0; $i<sizeof($arrProdi); $i++) $_prodi .= ",'".$arrProdi[$i]."'";
    $_prodi = trim($_prodi, ',');
    $_prodi = (empty($arrProdi))? '-1' : $_prodi; //implode(', ', $arrProdi);
  }
  else $_prodi = "'$_SESSION[prodi]'";
  
  // sekarang hanya 1 prodi saja kecuali prodi -99
  $optprodi = GetCheckboxes("prodi", "ProdiID",
    "concat(ProdiID, ' - ', Nama) as NM", "NM", $w['ProdiID'], '.', "ProdiID in ($_prodi)");
  $optprid = GetCheckboxes("program", "ProgramID",
    "concat(ProgramID, ' - ', Nama) as NM", "NM", $w['ProgramID'], '.');
  $opthari = GetOption2('hari', "Nama", "HariID", $w['HariID'], '', 'HariID');
  //$optmk = GetOption2('mk', "concat(MKKode, ' - ', Nama, ' (', SKS, ' SKS)')",
  //  'MKKode', $w['MKID'], "ProdiID='$_SESSION[prodi]'", 'MKID');
  //$optdsn = GetOption2('dosen', "concat(Nama, ', ', Gelar)",
  //  'Login', $w['DosenID'], "INSTR(ProdiID, '.$_SESSION[prodi].')", 'Login');
  $NamaDosen = (empty($w['DosenID']))? '' : GetaField('dosen', "Login", $w['DosenID'], 'Nama');
  $optrg = GetOption2('ruang', "concat(RuangID, ' - ', Nama)", 'KampusID, RuangID',
    $w['RuangID'], '', 'RuangID');
  $ckHargaStandar = ($w['HargaStandar'] == 'Y')? 'checked' : '';
  $optjenjad = GetOption2('jenisjadwal', "concat(JenisJadwalID, ' - ', Nama)",
    "JenisJadwalID", $w['JenisJadwalID'], '', 'JenisJadwalID');
  // Dapatkah mengedit harga matakuliah?
  if (strpos($LevelAksesHarga, ".$_SESSION[_LevelID].")===false) {
    $EdtHrg = "<input type=hidden name='HargaStandar' value='$w[HargaStandar]'>
      <input type=hidden name='Harga' value='$w[Harga]'>";
  }
  else {
    $EdtHrg = "<tr><td class=inp1>Harga :</td><td class=ul><input type=checkbox name='HargaStandar' value='Y' $ckHargaStandar> Apakah harga standar?<hr size=1 color=silver />
    Jika tidak tidak standar, harganya adalah: <br />
    Rp. <input type=text name='Harga' value='$w[Harga]' size=15 maxlength=15></td></tr>";
  }
  //<tr><td class=inp1>Matakuliah :</td><td class=ul><select name='MKID'>$optmk</select></td></tr>
  // Tampilkan form
  GabungkanScript();
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='data' method=POST onSubmit=\"return CheckForm(this);\">
  <input type=hidden name='JadwalID' value='$w[JadwalID]'>
  <input type=hidden name='JadwalPar' value='$w[JadwalPar]'>
  <input type=hidden name='JadwalSer' value='$w[JadwalSer]'>
  <input type=hidden name='TahunID' value='$w[TahunID]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='mnux' value='jadwal'>
  <input type=hidden name='gos' value='JdwlSav'>
  <input type=hidden id='prodi' name='prodi' value='$_SESSION[prodi]'>
  <input type=hidden id='MKID' name='MKID' value='$w[MKID]'>
  <input type=hidden name='bypass' value=0>

  <tr><th class=ttl colspan=2>$jdl</th></tr>

  <tr><td class=inp1>Berlaku untuk<br />Program :</td><td class=ul>$optprid</td></tr>
  <tr><td class=inp1>Berlaku untuk<br />Program Studi :</td><td class=ul>$optprodi</td></tr>
  <tr><td class=inp1>Hari :</td><td class=ul><select name='HariID'>$opthari</select></td></tr>
  <tr><td class=inp1>Jam Kuliah :</td><td class=ul>
    <input type=text name='JamMulai' value='$w[JamMulai]' size=5 maxlength=5> s/d
    <input type=text name='JamSelesai' value='$w[JamSelesai]' size=5 maxlength=5>
    </td></tr>
  <tr><td class=inp1>Mata kuliah :</td><td class=ul><input type=text id='MKKode' name='MKKode' value='$w[MKKode]' size=10 maxlength=20>
    <a href=\"javascript:carimkajax(data)\">Cari Matakuliah</a><br />
    <input type=text name='Nama' autocomplete='off' id='NamaMK' value='$w[Nama]' size=50 maxlength=50></td></tr>
  <tr><td class=inp1>Nama Kelas :</td><td class=ul><input type=text name='NamaKelas' value='$w[NamaKelas]' size=5 maxlength=1></td></tr>
  <tr><td class=inp1>Jenis Jadwal :</td><td class=ul><select name='JenisJadwalID'>$optjenjad</select></td></tr>
  <tr><td class=inp1>SKS :</td><td class=ul><input type=text name='SKS' value='$w[SKS]' size=3 maxlength=3>
    Isikan dengan -1 jika jumlah SKS menggunakan SKS asli dari matakuliah.</td></tr>
  <tr><td class=inp1>SKS Honor :</td>
    <td class=ul><input type=text name='SKSHonor' value='$w[SKSHonor]' size=3 maxlength=3>
    Isikan dengan -1 jika jumlah SKS honor menggunakan SKS asli dari matakuliah.</td></tr>
  <tr><td class=inp1>Ruang Kuliah :</td><td class=ul>
    <select name='RuangID'>$optrg</select></td></tr>
  <tr><td class=inp1>Kapasitas/Target :</td><td class=ul>
    <input type=text name='Kapasitas' value='$w[Kapasitas]' size=5 maxlength=4>
    Jika diisi dengan nilai -1, maka nilai kapasitas akan diambil dari nilai kapasitas ruang.</td></tr>
  <tr><td class=inp1>Dosen Pengampu :</td><td class=ul><input type=text name='DosenID' id='DosenID' value='$w[DosenID]' size=10 maxlength=20>
    <a href='javascript:caridosenajax(data)'>Cari Dosen</a><br />
    <input type=text autocomple='off' id='NamaDosen' name='NamaDosen' value='$NamaDosen' size=50 maxlength=50></td></tr>
  <tr><td class=inp1>Rencana Tatap Muka :</td><td class=ul><input type=text name='RencanaKehadiran' value='$w[RencanaKehadiran]' size=3 maxlength=3></td></tr>
  <tr><td class=inp1>Minimal Kehadiran Mhsw :</td><td class=ul><input type=text name='KehadiranMin' value='$w[KehadiranMin]' size=3 maxlength=3></td></tr>
  $EdtHrg
  <tr><td colspan=2 class=ul><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=jadwal'\"></td></tr>
  </table></p>";
}

function JdwlSav() {
  $w = array();
  $w['md'] = $_REQUEST['md'];
  $w['JadwalID'] = $_REQUEST['JadwalID'];
  $w['JadwalPar'] = $_REQUEST['JadwalPar'];
  $w['JadwalSer'] = $_REQUEST['JadwalSer'];
  $w['KodeID'] = $_SESSION['KodeID'];
  $w['TahunID'] = $_REQUEST['TahunID'];
  // array prodi
  $arrProdiID = $_REQUEST['ProdiID'];
  $w['ProdiID'] = (empty($arrProdiID))? '' : '.'.implode('.', $arrProdiID).'.';
  // array program
  $arrProgramID = $_REQUEST['ProgramID'];
  $w['ProgramID'] = (empty($arrProgramID))? '' : '.'.implode('.', $arrProgramID).'.';

  $w['NamaKelas'] = strtoupper($_REQUEST['NamaKelas']);
  $w['JenisJadwalID'] = $_REQUEST['JenisJadwalID'];
  $w['MKID'] = $_REQUEST['MKID'];
  $matakuliah = GetFields('mk', 'MKID', $w['MKID'], '*');
  $w['JadwalJenisID'] = $_REQUEST['JadwalJenisID'];
//  $w['MKKode'] = $matakuliah['MKKode'];
//  $w['Nama'] = $matakuliah['Nama'];
  $w['MKKode'] = $_REQUEST['MKKode'];
  $w['Nama'] = $_REQUEST['Nama'];
  $w['HariID'] = $_REQUEST['HariID'];
  $w['JamMulai'] = $_REQUEST['JamMulai'];
  $w['JamSelesai'] = $_REQUEST['JamSelesai'];
  $w['SKSAsli'] = $matakuliah['SKS'];
  $w['SKS'] = ($_REQUEST['SKS'] == -1)? $w['SKSAsli'] : $_REQUEST['SKS']+0;
  $w['SKSHonor'] = ($_REQUEST['SKSHonor'] == -1)? $w['SKSAsli'] : $_REQUEST['SKSHonor']+0;

  $w['DosenID'] = $_REQUEST['DosenID'];
  $w['RencanaKehadiran'] = $_REQUEST['RencanaKehadiran']+0;
  $w['Kehadiran'] = $_REQUEST['Kehadiran']+0;
  $w['KehadiranMin'] = $_REQUEST['KehadiranMin']+0;
  $w['JumlahMhsw'] = $_REQUEST['JumlahMhsw'];
  $w['RuangID'] = $_REQUEST['RuangID'];
  $w['Kapasitas'] = $_REQUEST['Kapasitas']+0;
  $w['Kapasitas'] = ($w['Kapasitas'] == -1)? GetaField('ruang', 'RuangID', $w['RuangID'], 'Kapasitas') : $w['Kapasitas'];
  
  $w['HargaStandar'] = (empty($_REQUEST['HargaStandar']))? 'N' : $_REQUEST['HargaStandar'];
  $w['Harga'] = $_REQUEST['Harga'];
  $w['NA'] = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];

  // Cek semua kondisi
  $bp = $_REQUEST['bypass']+0;
  if ($bp == 0) $GAGAL = CekSemuaJadwal($w);
  else $GAGAL = 0;
  if ($GAGAL > 0) {
    // Jika Gagal
    $_REQUEST['GAGAL'] = 1;
    echo ErrorMsg("Gagal",
      "Matakuliah gagal dijadwalkan karena ada kesalahan. <hr size=1 color=silver>
      Pilihan: <a href='javascript:gabungkan()'>Gabungkan Jadwal / Jadwalkan Paksa</a>");
    JdwlEdt();
  }
  else {
    $w['JamMulai'] = str_replace('.', ':', $w['JamMulai']);
    $w['JamSelesai'] = str_replace('.', ':', $w['JamSelesai']);
    // Jika Berhasil, maka Simpan!
    if ($w['md'] == 0) {
      $s = "update jadwal set JadwalPar='$w[JadwalPar]', JadwalSer='$w[JadwalSer]',
      ProdiID='$w[ProdiID]', ProgramID='$w[ProgramID]', NamaKelas='$w[NamaKelas]', 
      JenisJadwalID='$w[JenisJadwalID]', MKID='$w[MKID]',
      MKKode='$w[MKKode]', Nama='$matakuliah[Nama]',
      HariID='$w[HariID]', JamMulai='$w[JamMulai]', JamSelesai='$w[JamSelesai]',
      SKSAsli='$w[SKSAsli]', SKS='$w[SKS]', SKSHonor='$w[SKSHonor]', DosenID='$w[DosenID]',
      RencanaKehadiran='$w[RencanaKehadiran]', KehadiranMin='$w[KehadiranMin]', 
      RuangID='$w[RuangID]', Kapasitas='$w[Kapasitas]',
      HargaStandar='$w[HargaStandar]', Harga='$w[Harga]', NA='$w[NA]',
      LoginEdit='$_SESSION[_Login]', TglEdit=now()
      where JadwalID='$w[JadwalID]' ";
      $r = _query($s);
      // Jika ada perubahan SKS, maka update semua KRS
      $s1 = "update krstemp set SKS=$w[SKS] where JadwalID=$w[JadwalID]";
      $r1 = _query($s1);
      // Update juga KRS
      $s2 = "update krs set SKS=$w[SKS] where JadwalID=$w[JadwalID]";
      $r2 = _query($s2);
      echo "<script>window.location = '?mnux=jadwal#$w[JadwalID]';</script>";
    }
    else {
      $s = "insert into jadwal (JadwalPar, JadwalSer,
      ProdiID, ProgramID, KodeID, TahunID,
      NamaKelas, JenisJadwalID, 
      MKID, MKKode, Nama,
      HariID, JamMulai, JamSelesai,
      SKSAsli, SKS, SKSHonor, DosenID,
      RencanaKehadiran, KehadiranMin, RuangID, Kapasitas,
      HargaStandar, Harga, NA,
      LoginBuat, TglBuat
      )
      values ('$w[JadwalPar]', '$w[JadwalSer]',
      '$w[ProdiID]', '$w[ProgramID]', '$w[KodeID]', '$w[TahunID]',
      '$w[NamaKelas]', '$w[JenisJadwalID]',
      '$w[MKID]', '$w[MKKode]', '$w[Nama]',
      '$w[HariID]', '$w[JamMulai]', '$w[JamSelesai]',
      '$w[SKSAsli]', '$w[SKS]', '$w[SKSHonor]', '$w[DosenID]',
      '$w[RencanaKehadiran]', '$w[KehadiranMin]', '$w[RuangID]', '$w[Kapasitas]',
      '$w[HargaStandar]', '$w[Harga]', '$w[NA]',
      '$w[_Login]', now()) ";
      $r = _query($s);
      $w['JadwalID'] = GetLastID();
      // Hitung kelas Serial
      if ($w['JadwalSer'] >0) {
        $jmlser = GetaField('jadwal', "JadwalSer", $w['JadwalSer'], "count(JadwalID)")+0;
        $sser = "update jadwal set JumlahKelasSerial=$jmlser where JadwalID=$w[JadwalSer] ";
        $rser = _query($sser);
      }
      //DftrJdwl();
      echo "<script>window.location = '?mnux=jadwal#$w[JadwalID]';</script>";
    }
  }
}
function GabungkanScript() {
  echo <<<END
  <SCRIPT>
  function gabungkan() {
    data.bypass.value=1;
    data.submit();
  }
  </SCRIPT>
END;
}
function CekSemuaJadwal($w) {
  $pesanruang = '';
  $pesandosen = '';
  $pesandosentidakaktif = '';
  $pesansudah = '';
  if (empty($w['JadwalSer'])) $_sudah = CekJadwalSudahAda($w, $pesansudah);
  if (!empty($w['RuangID'])) $_ruang = CekJadwalRuang($w, $pesanruang);
  $_dosentidakaktif = CekDosenTidakAktif($w, $pesandosentidakaktif);
  $_dosen = CekJadwalDosen($w, $pesandosen);
  $adaerror = $_sudah + $_ruang + $_dosentidakaktif + $_dosen;
  if ($adaerror) {
    $filecss = "css.php";
    $hndcss = fopen($filecss, 'r');
    $css = fread($hndcss, filesize($filecss));
    fclose($hndcss);
    $isi = "$css
      <p><font class=Judul>Jadwal Gagal Disimpan</font></p>" . $pesansudah . $pesanruang . $pesandosen .
      "<hr size=1 color=silver>
      <center><input type=button name='Tutup' value='Tutup window ini' onClick='javascript:window.close()'></center>";
    $nm = "tmp/$_SESSION[_Login].jdwl.html";
    $hnd = fopen($nm, 'w');
    fwrite($hnd, $isi);
    fclose($hnd);
    PopupMsg($nm);
  }
  return $adaerror;
}
function CekJadwalSudahAda($w, &$psn) {
  // Jika matakuliah serial, maka tidak perlu dicek
  $w['JumlahKelasSerial'] = ($w['md'] == 1)? 0 : GetaField('jadwal', 'JadwalID', $w['JadwalID'], 'JumlahKelasSerial')+0;
  if ($w['JadwalSer']>0 || $w['JumlahKelasSerial']>0) {
    return 0;
  }
  else {
    // Mengecek double MKID, NamaKelas, JenisKuliahID
    $ada = GetFields("jadwal", "JadwalID<>$w[JadwalID] and TahunID='$w[TahunID]' and MKKode='$w[MKKode]' and NamaKelas='$w[NamaKelas]' and JenisJadwalID",
      $w['JenisJadwalID'], '*');
    if (!empty($ada)) {
      $psn = ErrorMsg("Gagal Simpan", "Matakuliah ini sudah dijadwalkan.");
      return 1;
    } 
    else {
      $psn = '';
      return 0;
    }
  }
}
function CekJadwalRuang($jdwl, &$pesan) {
  $s = "select j.*
    from jadwal j
    where j.TahunID='$jdwl[TahunID]'
      and j.HariID='$jdwl[HariID]'
      and ('$jdwl[JamMulai]:00' <= j.JamMulai and j.JamMulai <= '$jdwl[JamSelesai]:00')
      and ('$jdwl[JamMulai]:00' <= j.JamSelesai and j.JamSelesai <= '$jdwl[JamSelesai]:00')
      and j.RuangID='$jdwl[RuangID]'
      and j.JadwalID <> $jdwl[JadwalID] ";
  $r = _query($s);
  // Jika ada bentrok
  if (_num_rows($r) > 0) {
    $pesan = ExtractJadwalError($r, "Jadwal Ruang Bentrok",
      "Ruang kuliah pada hari dan jam ini telah digunakan oleh jadwal:");
    return 1;
  }
  else {
    $pesan = '';
    return 0;
  }
  return 1;
}
function CekDosenTidakAktif($w, &$psn) {
  $_NA = GetaField('dosen', 'Login', $w['DosenID'], 'NA');
  if ($_NA == 'Y') {
    $psn = ErrorMsg("Dosen Tidak Aktif", "Data tidak bisa disimpan karena dosen <b>$w[DosenID]</b> tidak aktif.");
    return 1;
  }
  else {
    $psn = '';
    return 0;
  }
}
function CekJadwalDosen($jdwl, &$pesan) {
  $strdosen = TRIM($jdwl['DosenID'], '.');
  $arrdosen = explode('.', $strdosen);
  $error = 0;
  $pesan = '';
  for ($i = 0; $i < sizeof($arrdosen); $i++) {
    $error += CekJadwalDosen1($jdwl, $pesandosen, $arrdosen[$i]);
    $pesan .= $pesandosen;
  }
  return $error;
}
function CekJadwalDosen1($jdwl, &$pesan, $dsn) {
  $s = "select j.*
    from jadwal j
    where j.TahunID='$jdwl[TahunID]'
      and j.HariID='$jdwl[HariID]'
      and ('$jdwl[JamMulai]:00' <= j.JamMulai and j.JamMulai <= '$jdwl[JamSelesai]:00')
      and ('$jdwl[JamMulai]:00' <= j.JamSelesai and j.JamSelesai <= '$jdwl[JamSelesai]:00')
      and INSTR(j.DosenID, '.$dsn.') >0
      and j.JadwalID <> $jdwl[JadwalID] ";
  $r = _query($s);
  // Jika ada bentrok
  if (_num_rows($r) > 0) {
    $nmdsn = GetaField('dosen', 'Login', $dsn, "concat(Nama, ', ', Gelar)");
    $pesan = ExtractJadwalError($r, "Jadwal Dosen Bentrok",
      "Dosen <b>$nmdsn</b> ($dsn) telah mengajar pada jadwal di bawah ini sehingga tidak
      dapat dijadwalkan pada hari dan jam yang sama.");
    return 1;
  }
  // Jika tidak bentrok
  else {
    $pesan = '';
    return 0;
  }
}
function ExtractJadwalError($r, $hdr, $msg) {
  $a = "<p><table class=box cellspacing=1 cellpadding=4 width=100%>
    <tr><th class=wrn colspan=7>$hdr</th></tr>
    <tr><td class=ul colspan=7>$msg</td></tr>
    <tr><th class=ttl>No. Jadwal</th>
    <th class=ttl>Ruang</th>
    <th class=ttl>Waktu</th>
    <th class=ttl>Kode MK</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>Pengampu</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $a .= "<tr>
      <td class=inp1>$w[JadwalID]</td>
      <td class=ul>$w[RuangID]</td>
      <td class=ul>$w[JamMulai]-$w[JamSelesai]</td>
      <td class=ul>$w[MKKode]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul>$w[NamaKelas]</td>
      <td class=ul>$w[DosenID]</td>
      </tr>";
  }
  return $a . "</table></p>";

}
function JdwlDel() {
  $JadwalID = $_REQUEST['JadwalID'];
  $jdwl = GetFields('jadwal left outer join jenisjadwal jj on jadwal.JenisJadwalID = jj.JenisJadwalID', 'JadwalID', $JadwalID, 'jadwal.*, jj.Nama as jNama');
  $hari = GetaField('hari', 'HariID', $jdwl['HariID'], 'Nama');
  
  // Apakah sudah ada mhsw yang ambil?
  $jmlmhsw = GetaField('krs', "JadwalID", $JadwalID, "count(*)");
  if ($jmlmhsw > 0) {
    echo ErrorMsg("Jadwal Tidak Dapat Dihapus",
      "<p>Jadwal tidak dapat dihapus karena sudah ada <b>$jmlmhsw</b> mahasiswa yang mengambil matakuliah ini.<br />
      Anda harus mengkonfirmasikan kepada mahasiswa dahulu kalau matakuliah ini dihapus.</p>
      <hr size=1 color=silver>
      Pilihan: <a href='?mnux=jadwal'>Kembali</a>");
  }
  else {
    $dosen = '&nbsp;';
    if (!empty($jdwl['DosenID'])) {
      $arrdosen = explode('.', TRIM($jdwl['DosenID'], '.'));
      $strdosen = implode(',', $arrdosen);
      $dosen = GetArrayTable("select Nama from dosen where Login in ($strdosen) order by Nama",
      'Login', 'Nama');
    }
    // Paralel
    $par = GetaField('jadwal', "JadwalPar", $jdwl['JadwalID'], "count(JadwalID)")+0;
    $paralel = ($par > 0)? "<tr><td class=wrn>Kelas Paralel</td>
      <td class=ul>Jadwal ini memiliki <b>$par</b> kelas paralel.<br />
      Jika Anda menghapus jadwal ini, maka kelas paralelnya akan dihapus juga.</td></tr>" : '';
    // Serial
    $ser = GetaField('jadwal', "JadwalSer", $jdwl['JadwalID'], "count(JadwalID)")+0;
    $serial = ($ser > 0)? "<tr><td class=wrn>Kelas Serial</td>
      <td class=ul>Jadwal ini memiliki <b>$ser</b> kelas serial.<br />
      Jika Anda menghapus jadwal ini, maka kelas serialnya akan dihapus juga.</td></tr>" : '';

    echo "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='jadwal'>
    <input type=hidden name='gos' value='JdwlDel1'>
    <input type=hidden name='JadwalID' value='$JadwalID'>

    <tr><th class=ttl colspan=2>Konfirmasi Hapus Jadwal</th></td></tr>
    <tr><td class=inp1>No Jadwal</td><td class=ul>$JadwalID</td></tr>
    <tr><td class=inp1>Matakuliah</td><td class=ul>$jdwl[MKKode] - $jdwl[Nama]</td></tr>
    <tr><td class=inp1>Jenis Jadwal</td><td class=ul>$jdwl[jNama]</td></tr>
    <tr><td class=inp1>Kelas</td><td class=ul>$jdwl[NamaKelas]</td></tr>
    <tr><td class=inp1>Ruang & Waktu</td><td class=ul>$jdwl[RuangID] - $hari: $jdwl[JamMulai]-$jdwl[JamSelesai]</td></tr>
    <tr><td class=inp1>Dosen</td><td class=ul>$dosen</td></tr>
    $paralel
    <tr><td class=ul colspan=2><input type=submit name='Hapus' value='Hapus'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=jadwal'\"></td></tr>
    </form></table></p>";
  }
}
function JdwlDel1() {
  $JadwalID = $_REQUEST['JadwalID'];
  // Hapus Jadwal
  $s = "delete from jadwal where JadwalID='$JadwalID' or JadwalPar='$JadwalID' or JadwalSer='$JadwalID' ";
  $r = _query($s);
  
  // Hapus Jadwal serialnya
  $s0 = "delete from jadwal where JadwalSer = '$JadwalID'";
  $r0 = _query($s0);
  DftrJdwl();
}
function JdwlTutup() {
  $JadwalID = $_REQUEST['JadwalID'];
  $jdwl = GetFields("jadwal", "JadwalID", $JadwalID, "*");
  $hr = GetaField('hari', 'HariID', $jdwl['HariID'], 'Nama');
  $jk = GetaField('jenisjadwal', 'JenisJadwalID', $jdwl['JenisJadwalID'], 'Nama');
  echo Konfirmasi("Konfirmasi Penutupan Kelas",
    "<p>Benar Anda akan menutup kelas ini?</p>
    <p><table class=box cellspacing=1 cellpadding=4>
    <tr><td class=inp>Matakuliah</td><td class=ul>$jdwl[MKKode] - $jdwl[Nama] $jdwl[NamaKelas] ($jdwl[SKS] SKS)</td></tr>
    <tr><td class=inp>Jenis</td><td class=ul>$jk</td></tr>
    <tr><td class=inp>Hari, Jam</td><td class=ul>$hr, $jdwl[JamMulai] ~ $jdwl[JamSelesai]</td></tr> 
    </table></p>
    <hr size=1 color=silver>
    Pilihan : <input type=submit name='Batal' value='Batal' onClick=\"location='?mnux=jadwal'\">
    <input type=submit name='Tutup' value='Tutup Kelas' onClick=\"location='?mnux=jadwal&gos=JdwlTutup1&JadwalID=$JadwalID'\">
    ");
}
function JdwlTutup1() {
  echo "TUTUP";
  DftrJdwl();
}
function AssDsnEdt() {
  $JadwalID = $_REQUEST['JadwalID'];
  $jdwl = GetFields("jadwal j 
    left outer join dosen d on j.DosenID=d.Login
    left outer join hari h on j.HariID=h.HariID
    left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID", 
    "JadwalID", 
    $JadwalID, "j.*, h.Nama as HR, 
    jj.Nama as JenisJadwal, concat(d.Nama, ', ', d.Gelar) as DSN");
  // Tampilkan header
  TampilkanHeaderAssDsnEdt($jdwl);
  TampilkanTambahAssDsn($jdwl);
}
function TampilkanHeaderAssDsnEdt($jdwl) {
  echo "<p><table class=box cellspacing=1>
  <tr><td class=ul colspan=4><b>Jadwal Matakuliah</b></td></tr>
  <tr><td class=inp># Jadwal</td><td class=ul>$jdwl[JadwalID]</td>
      <td class=inp>SKS</td><td class=ul>$jdwl[SKS] ($jdwl[SKSAsli])</td></tr>
  <tr><td class=inp>Matakuliah</td><td class=ul>$jdwl[MKKode] - $jdwl[Nama]</td>
      <td class=inp>Dosen Pengampu</td><td class=ul>$jdwl[DSN]</td></tr>
  <tr><td class=inp>Kelas</td><td class=ul>$jdwl[NamaKelas] ($jdwl[JenisJadwal])</td>
      <td class=inp>Waktu Kuliah</td><td class=ul>$jdwl[HR], $jdwl[JamMulai]~$jdwl[JamSelesai]</tr>
  <tr>
  </table></p>";
}
function TampilkanTambahAssDsn($jdwl) {
  CariMKScript();
  $s = "select jd.DosenID, jd.JenisDosenID,
    jd.JadwalDosenID, concat(d.Nama, ', ', d.Gelar) as DSN
    from jadwaldosen jd
      left outer join dosen d on jd.DosenID=d.Login
    where jd.JadwalID='$jdwl[JadwalID]'
    order by d.Nama";
  $r = _query($s); $n = 0;
  
  $optdsn = GetOption2('jenisdosen', "Nama", 'Nama', 'DSN', '', 'JenisDosenID');
  echo "<p><table class=box>
    <form action='?' name='data' method=POST>
    <input type=hidden name='mnux' value='jadwal'>
    <input type=hidden name='gos' value='AssDsnSav'>
    <input type=hidden name='prodi' value='$_SESSION[prodi]'>
    <input type=hidden name='JadwalID' value='$jdwl[JadwalID]'>
    <tr><td class=ul colspan=5><b>Tambah Dosen Pengampu</b></td></tr>
    <tr><td class=inp1>Kode</td>
      <td class=ul><input type=text name='DosenID' size=10 maxlength=20></td>
      <td class=inp1>Nama</td>
      <td class=ul><input type=text name='NamaDosen' size=20 maxlength=50>
      <a href='javascript:caridosen(data)'>Cari Dosen</a></td>
      </tr>
    <tr><td class=inp1>Jenis Pengampu</td>
      <td class=ul><select name='JenisDosenID'>$optdsn</select></td>
      <td class=ul colspan=2><input type=submit name='Tambah' value='Tambahkan'>
        <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=jadwal'\">
      </td></tr>
      </form></table><p>";
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Jenis</th>
    <th class=ttl title='Delete'>Del</th></tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr><td class=inp>$n</td>
    <td class=ul>$w[DosenID]</td>
    <td class=ul>$w[DSN]</td>
    <td class=ul>$w[JenisDosenID]</td>
    <td class=ul><a href='?mnux=jadwal&gos=AssDsnEdt&JadwalID=$jdwl[JadwalID]&slnt=jadwal.lib&slntx=DelAssDsn&JDID=$w[JadwalDosenID]'><img src='img/del.gif'></a></td>
    </tr>";
  }
  echo "</table></p>";
}
function AssDsnSav() {
  $JadwalID = $_REQUEST['JadwalID'];
  $_DosenID = GetaField('jadwal', 'JadwalID', $JadwalID, 'DosenID');
  $DosenID = $_REQUEST['DosenID'];
  $JenisDosenID = $_REQUEST['JenisDosenID'];
  if ($_DosenID == $DosenID) {
    echo ErrorMsg("Tidak Dapat Ditambahkan",
      "Dosen <b>$DosenID</b> tidak dapat ditambahkan karena dosen ybs merupakan dosen utama pengampu matakuliah ini");
  }
  else {
    $ada = GetaField('jadwaldosen', "JadwalID='$JadwalID' and DosenID", $DosenID, "JadwalDosenID");
    if (!empty($ada)) {
      echo ErrorMsg("Tidak Dapat Ditambahkan",
        "Dosen <b>$DosenID</b> sudah ada di dalam daftar dosen pengampu matakuliah ini.");
    }
    else {
      $s = "insert into jadwaldosen (JadwalID, DosenID, JenisDosenID, TglBuat, LoginBuat)
        values ('$JadwalID', '$DosenID', '$JenisDosenID', now(), '$_SESSION[_Login]')";
      $r = _query($s);
    }
  }
  AssDsnEdt();
}
function HitMhswDaftarJadwal() {
  $s = "select JadwalID, MKKode, Nama, NamaKelas
    from jadwal 
    where INSTR(ProdiID, '.$_SESSION[prodi].')>0
      and TahunID='$_SESSION[tahun]'
    order by MKKode";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $jml = GetaField('krs', "StatusKRSID='A' and JadwalID", $w['JadwalID'], "count(*)")+0;
    $jmlkrs = GetaField('krstemp', "StatusKRSID='A' and JadwalID", $w['JadwalID'], "Count(*)")+0;
    $sx = "update jadwal set JumlahMhsw=$jml, JumlahMhswKRS=$jmlkrs where JadwalID=$w[JadwalID] ";
    $rx = _query($sx);
  }
}

// *** Parameters ***
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$tahun = GetSetVar('tahun');
$jenjad = GetSetVar('jenjad');
//$tahun = GetaField('tahun', "KodeID='$_SESSION[KodeID]' and ProgramID='$prid' and NA='N' and ProdiID", $prodi, 'TahunID');
//$_SESSION['tahun'] = $tahun;
$gos = (empty($_REQUEST['gos']))? 'DftrJdwl' : $_REQUEST['gos'];


// *** Main ***
$NTahun = NamaTahun($tahun);
TampilkanJudul("Penjadwalan Kuliah $NTahun");
TampilkanTahunProdiProgram('jadwal', '');
if (!empty($_SESSION['prodi']) && !empty($_SESSION['prid']) && !empty($_SESSION['KodeID']) && !empty($tahun)) {
  $thn = GetFields('tahun', "ProgramID='$prid' and ProdiID='$prodi' and TahunID", $tahun, '*');
  $gos();
}
?>
