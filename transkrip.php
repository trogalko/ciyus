<?php
// Author: Emanuel Setio Dewo
// 19 April 2006

function GetOptionPrinter() {
  $arrPrinter = array('Dot matrix', 'Laser');
  $str = '<option>-</option>';
  for ($i = 0; $i < sizeof($arrPrinter); $i++) {
    $sel = ($i == $_SESSION['_Printer'])? 'selected' : '';
    $str .= "<option value='$i' $sel>$arrPrinter[$i]</option>";
  }
  return $str;
}

function GetJenisTranskrip(){
	$arrJns = array('Skripsi', 'Tugas Akhir');
	$str = '<option>-</option>';
	for($i=0;$i<sizeof($arrJns);$i++){
		$sel = ($i == $_SESSION['_Jns'])? 'selected' : '';
		$str .= "<option value='$i' $sel>$arrJns[$i]</option>";
	}
	return $str;
}
// *** Functions ***
function DataMhsw($mhsw) {
  $lls = ($mhsw['StatusMhswID'] != 'L')? "Transkrip Nilai (baru bisa dicetak kalau mhsw sudah lulus)" :
    "<a href='?mnux=transkrip&gos=TransNilai&md=1'>Transkrip Nilai Akhir</a>";
  $optbhs = GetOption2('bahasa', "concat(BahasaID, ' - ', Nama)", "Nama", $_SESSION['bhs'], '', 'BahasaID');
  $perolehan = ($mhsw['ProdiID'] == '10')? "<li><a href='?mnux=transkrip&gos=PerolehanSKS'>Daftar Perolehan SKS</a></li>" : '';
  // buat opsi printer
  $optPrinter = GetOptionPrinter();
  $optJns = GetJenisTranskrip();
  echo <<<END
  <p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=ul colspan=4><b>$mhsw[KodeID]</b></td></tr>
  <tr><td class=inp>NPM</td><td class=ul>$mhsw[MhswID]</td>
      <td class=inp>Nama Mahasiswa</td><td class=ul>$mhsw[Nama]</td></tr>
  <tr><td class=inp>Angkatan</td><td class=ul>$mhsw[TahunID]</td>
      <td class=inp>Status</td><td class=ul>$mhsw[SM]</td></tr>
  <tr><td class=inp>IPK</td><td class=ul>$mhsw[IPK]</td>
      <td class=inp>Total SKS</td><td class=ul>$mhsw[TotalSKS] SKS</td></tr>
  <form action='?' method=GET>
  <input type=hidden name='mnux' value='transkrip'>
  <tr><td class=inp>Bahasa</td><td class=ul><select name='bhs' onChange='this.form.submit()'>$optbhs</select></td>
      <td class=inp>Jenis Printer</td><td class=ul><select name='_Printer' onChange='this.form.submit()'>$optPrinter</select></td></tr>
	<tr><td class=inp>Jenis Transkrip</td><td class=ul colspan=3><select name='_Jns' onChange='this.form.submit()'>$optJns</select></td></tr>
  </form>
  </table></p>
  <p><b>Laporan</b></p>
  <ol>
    $perolehan
    <li><a href='?mnux=transkrip&gos=TransNilai&md=0'>Transkrip Sementara</a></li>
    <li>$lls</li>
  </ol>
END;
}
function PerolehanSKS($mhsw) {
  global $_lf, $_InitPrn;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, $_InitPrn . chr(27).chr(18));
  
  // Tampilkan Header
  $div = str_pad('-', 79, '-').$_lf;
  $hdr = str_pad("*** DAFTAR PEROLEHAN SKS ***", 79, ' ', STR_PAD_BOTH) .$_lf.$_lf.
    "NPM / NAMA      : " . $mhsw['MhswID'] . '  ' . $mhsw['Nama'] . $_lf.
    "FAK / JUR       : " . $mhsw['FAK'] . ' / ' . $mhsw['PRD'] . $_lf.
    //"IPK             : " . $mhsw['IPK'] . $_lf.
    "Masa Studi      : " . NamaTahun($mhsw['BatasStudi']) . $_lf.
    "Penasehat Akd.  : " . $mhsw['PA'] . $_lf.
    $div.
    "No.  Kode       Matakuliah                                       SKS   Nilai".$_lf.$div;
  fwrite($f, $hdr); 
  // matakuliah yg diambil
  $s = "select concat(LEFT(krs.MKKode, 3), ' ', SUBSTRING(krs.MKKode, 4, 3)) as MKKode, 
    LEFT(mk.Nama, 45) as NamaMK, LEFT(mk.Nama_en, 40) as NamaMK1,
    krs.BobotNilai, krs.GradeNilai, krs.SKS
    from krs krs
      left outer join mk mk on krs.MKID=mk.MKID
      left outer join nilai n on krs.GradeNilai=n.Nama and n.ProdiID = mk.ProdiID
    where krs.MhswID='$mhsw[MhswID]' 
      and krs.GradeNilai not in ('-', '')
      and n.Lulus='Y'
    order by krs.MKKode asc, krs.BobotNilai desc";
    
  $r = _query($s); 
  $n = 0; 
  $brs = 0;
  $maxbrs = 42;
  $hal = 0;
  $mk = '';
  $_sks = 0;
  $_bbt = 0;

  while ($w = _fetch_array($r)) {
    if ($mk != $w['MKKode']) {
      $mk = $w['MKKode'];
      $n++; $brs++;
      $NamaMK = ($_SESSION['bhs'] == 'id')? $w['NamaMK'] : $w['NamaMK1'];
      $_sks += $w['SKS'];
      $_bbt += $w['SKS'] * $w['BobotNilai'];
      fwrite($f, str_pad($n.'.', 4) . ' '.
        str_pad($w['MKKode'], 10) . ' '.
        str_pad($NamaMK, 45) . '  '.
        str_pad($w['SKS'], 4, ' ', STR_PAD_LEFT) . '    '.
        str_pad($w['GradeNilai'], 3, ' ') . $_lf);
      if ($brs >= $maxbrs) {
        $brs = 0;
        $hal++;
        fwrite($f, $div . str_pad("Hal. ".$hal, 79, ' ', STR_PAD_LEFT).$_lf);
        fwrite($f, chr(12));
        fwrite($f, $hdr);
      } 
    // $nxk = $w['SKS'] * $w['BobotNilai'];
     // $_nxk += $nxk;
     // $_sks += $w['SKS'];
	}
  }
  $_ipk = ($_sks > 0)? $_bbt / $_sks : 0;
  // $_ipk = ($_sks == 0)? 0 : $_nxk/$_sks;
 // $ipk = number_format($_ipk, 2);
 // $_ipk = $mhsw['IPK'];
  fwrite($f, $div);
  if ($maxbrs-10 <= $brs) {
    $hal++;
    fwrite($f, $div . str_pad("Hal. ".$hal, 79, ' ', STR_PAD_LEFT).$_lf);
    fwrite($f, chr(12));
    fwrite($f, $hdr);
  }
  fwrite($f, "Jumlah Kredit yang Telah Diambil: $_sks SKS, IPK: " . number_format($_ipk, 2). $_lf.$div);
  fwrite($f, "Keterangan : 1. Jumlah Perolehan SKS sebagai prasyarat pengambilan".$_lf.
             "                matakuliah adalah bobot SKS dari semua matakuliah ". $_lf.
             "                yang mendapat nilai >= D".$_lf.
             "             2. Jumlah Perolehan SKS sebagai kriteria kelulusan dan". $_lf.
             "                perhitungan jumlah SKS sebgai kriteria kelanjutan". $_lf.
             "                studi adalah jumlah bobot SKS dari semua matakuliah".$_lf.
             "                yang mendapat nilai >= C.".$_lf);
  fwrite($f, str_pad("Dicetak Oleh : " . $_SESSION['_Login'] . ', ' . Date("d-m-Y H:i"), 30, ' ').$_lf);
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "transkrip");
}
function TransNilai($mhsw) {
  global $_lf, $arrBulan;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  if ($mhsw['ProdiID'] != 61) {
	if ($_SESSION['_Jns'] == 1) {
		$jns = "Judul Tugas Akhir : ";
		$lbr = 20;
		$pnj = 40;
	} elseif ($_SESSION['_Jns'] == 0) {
		$jns = "Judul Skripsi : ";
		$lbr = 16;
		$pnj = 45;
	}
  } else {
	$jns = "Judul Thesis : ";
	$lbr = 15;
	$pnj = 45;
  }
  // deteksi printer
  if ($_SESSION['bhs'] == 'id') {
    $_d = substr($mhsw['TanggalLahir'], 8, 2);
    $_m = substr($mhsw['TanggalLahir'], 5, 2)+0;
    $_y = substr($mhsw['TanggalLahir'], 0, 4);
    $tglLahir = "$_d " . $arrBulan[$_m] . " $_y";
  }
  else $tglLahir = $mhsw['TGLLHR']; 
  if ($_SESSION['_Printer'] == 0) {
    fwrite($f, chr(27).chr(80).chr(15).chr(27).chr(67).chr(66));
    $mrg = str_pad(' ', 4);
    $mrg1 = str_pad(' ', 4);
    $mrghdr = str_pad(' ', 22);
    $mrghdr1 = str_pad(' ', 23);
    if ($mhsw['ProdiID'] == '31' or $mhsw['ProdiID'] == '32') {
      $mhsw['PRD'] = str_replace('Ekonomi ', '', $mhsw['PRD']);
    }
    $hdr = $_lf.$_lf.
      $mrg . $mrghdr . str_pad($mhsw['MhswID'], 30) .  $mrghdr1 . $mhsw['FAK'] . $_lf.
      $mrg . $mrghdr . str_pad($mhsw['Nama'], 30) .    $mrghdr1 . $mhsw['PRD'] . $_lf.
      $mrg . $mrghdr . $mhsw['TempatLahir'] . ', ' . $tglLahir.
      $_lf . $_lf . $_lf. $_lf; 
  }
  else {
    fwrite($f, 
      chr(27) . chr(38) . chr(107) . chr(50) . chr(83). // condensed
      chr(27) . chr(38) . chr(108) . chr(54) . chr(68). // 6 lines per inches
      chr(27) . chr(40) . chr(115) . chr(51) . chr(66)); // bold & 66 baris
    $mrg = str_pad(' ', 4);
    $mrg1 = str_pad(' ',4);
    $mrghdr = str_pad(' ', 22);
    $mrghdr1 = str_pad(' ', 23);
    if ($mhsw['ProdiID'] == '31' or $mhsw['ProdiID'] == '32') {
      $mhsw['PRD'] = str_replace('Ekonomi ', '', $mhsw['PRD']);
    }
    $hdr = $_lf.$_lf.$_lf.$_lf.$_lf.
      $mrg .
      str_pad("N.P.M : ", 21, ' ', STR_PAD_LEFT).str_pad($mhsw['MhswID'], 30) .  
      str_pad("Fakultas : ", 16, ' ', STR_PAD_LEFT) . $mhsw['FAK'] . $_lf.
      $mrg . 
      str_pad("Nama : ", 21, ' ', STR_PAD_LEFT).str_pad($mhsw['Nama'], 30).
      str_pad("Jurusan : ", 16, ' ', STR_PAD_LEFT).$mhsw['PRD'] . $_lf.
      $mrg . 
      str_pad("Tempat & Tgl.Lahir : ", 21, ' ', STR_PAD_LEFT).$mhsw['TempatLahir'] . ', ' . $tglLahir.
      $_lf . $_lf . $_lf. $_lf;
  }
  // Tampilkan Header
  $div = str_pad('-', 79, '-').$_lf;
  fwrite($f, $hdr); 
  
  // Isinya
  $isi = array();
  $maxbrs = 40;
  for ($i=0; $i<=$maxbrs; $i++) $isi[$i] = '';
  //if ($mhsw['ProdiID'] == 
  // Matakuliah
  $s0 = "select distinct krs.GradeNilai, krs.BobotNilai, krs.SKS,
    concat(LEFT(krs.MKKode, 3), ' ', SUBSTRING(krs.MKKode, 4, 3)) as MKKode,
    mk.MKKode as Kode,
    LEFT(mk.Nama, 40) as NamaMK, LEFT(mk.Nama_en, 40) as NamaMK1, mk.MKSetara
    from krs krs
      left outer join mk mk on krs.MKID=mk.MKID
      left outer join jadwal j on krs.JadwalID=j.JadwalID
      left outer join nilai ni on krs.GradeNilai=ni.Nama and ni.ProdiID = mk.ProdiID
    where krs.MhswID='$mhsw[MhswID]'
      and krs.Final='Y'
      and krs.GradeNilai not in ('-','')
      and (j.JenisJadwalID <> 'R' or j.JenisJadwalID is NULL)
      and krs.NA = 'N'
      and ni.Lulus ='Y'
    order by mk.Sesi asc, krs.MKKode asc, krs.BobotNilai desc";
  
  $s = "select krs.GradeNilai, krs.BobotNilai, krs.SKS,
    concat(LEFT(krs.MKKode, 3), ' ', SUBSTRING(krs.MKKode, 4, 3)) as MKKode,
    mk.MKKode as Kode,
    LEFT(mk.Nama, 40) as NamaMK, LEFT(mk.Nama_en, 40) as NamaMK1, mk.MKSetara
    from krsprc krs
      left outer join mk mk on krs.MKID=mk.MKID
      left outer join nilai n on krs.GradeNilai=n.Nama
    where krs.MhswID='$mhsw[MhswID]' 
      and krs.GradeNilai not in ('-', '')
      and n.Lulus='Y'
    order by mk.Sesi asc, krs.MKKode asc, krs.BobotNilai desc";
  
  $r = _query($s0);

  $mk = ''; $n = 0; $brs = 0;
  $_sks = 0;
  $_bbt = 0;
  $_nxk = 0;
  $col = 0;
  $brs = 0;
  $MKSetara = '';
  $sis = array();
  $cek = array();
  $isi_2 = array();
  while ($w = _fetch_array($r)) {
    $MKSetara .= ($w['MKSetara'] == '')? '' : $w['MKSetara'];  
	  $pos = strpos($MKSetara, ".$w[Kode].");
    
    if ($mk != $w['MKKode'] && $pos === false) {
      $cek = GetFields('krs left outer join mk on krs.MKID=mk.MKID', "INSTR(mk.MKSetara, '.$w[Kode].')>0 and MhswID", $mhsw['MhswID'], "krs.GradeNilai, krs.BobotNilai, krs.SKS,
        concat(LEFT(krs.MKKode, 3), ' ', SUBSTRING(krs.MKKode, 4, 3)) as MKKode,
        mk.MKKode as Kode,
        LEFT(mk.Nama, 40) as NamaMK, LEFT(mk.Nama_en, 40) as NamaMK1, mk.MKSetara");
        if (!empty($cek)){
          if ($w['BobotNilai'] > $cek['BobotNilai']){
          } else {
            $w['MKKode'] = $cek['MKKode'];
            $w['SKS'] = $cek['SKS'];
            $w['BobotNilai'] = $cek['BobotNilai'];
            $w['NamaMK'] = $cek['NamaMK'];
            $w['NamaMK1'] = $cek['NamaMK1'];
            $w['GradeNilai'] = $cek['GradeNilai'];
          }
        }
      $mk = $w['MKKode'];
      $n++;
      if ($n > $maxbrs) {
        $n = 1;
        $col++;
      }
      $nxk = $w['SKS'] * $w['BobotNilai'];
      $_nxk += $nxk;
      $_sks += $w['SKS'];
      $NamaMK = ($_SESSION['bhs'] == 'id')? $w['NamaMK'] : $w['NamaMK1'];
      $brs++;
      $isi[$n] .= $mrg . str_pad($w['MKKode'], 8) . ' '.
        str_pad($NamaMK, 40)  .
        str_pad($w['SKS'], 3, ' ', STR_PAD_LEFT) . '    '.
        str_pad($w['GradeNilai'], 5, ' ');
    }
  }
  echo $brs;
  $_ipk = ($_sks == 0)? 0 : $_nxk/$_sks;
  $ipk = number_format($_ipk, 2);
  
  // tampilkan footer
  $_i = ($n + 5 > $maxbrs)? 1 : $n+2;
  if ($brs >= 73) $_i = 1;
  $tglCetak = date('d'). ' ' . $arrBulan[date('n')] . ' '. date('Y');
  
  if ($mhsw['StatusMhswID'] == 'L') {
    $TglSKKeluar = GetaField('ta', "MhswID", $mhsw['MhswID'], 'TglSKYudisium');
    //echo $TglSKKeluar;
    $TglSKKeluar = FormatTanggal($TglSKKeluar, '-');
    if ($brs < 73) $isi[$_i] .= $mrg . "Lulus $mhsw[Gelar] tanggal: $TglSKKeluar";
    else $isi_2[$_i] .= $mrg . "Lulus $mhsw[Gelar] tanggal: $TglSKKeluar";
    if ($mhsw['ProdiID'] == '10' || $mhsw['ProdiID'] == '11') {
      $j = 0;
    }
    else {
      $ta = GetFields('ta', "NA = 'N' and TAID", $mhsw['TAID'], '*');
      $arrTA = PutuskanJudul($ta['Judul'], $pnj);
	  //echo $jdl;
	  //echo $pnj;
	  //echo $lbr;
      for ($j = 0; $j < sizeof($arrTA); $j++) {
        $kirinya = ($j == 0)? $jns : str_pad(' ', $lbr, ' '); 
        if ($brs < 73) $isi[$_i + $j + 1] .= $mrg . $kirinya . $arrTA[$j];
        else $isi_2[$_i + $j + 1] .= $mrg . $kirinya . $arrTA[$j];
      }
    }
    $Predikat = (empty($mhsw['Predikat']))? GetPredikatKelulusan($mhsw['ProdiID'], $ipk ,$mhsw['PRDID']) : $mhsw['Predikat'];
    if ($brs < 73) {
      $isi[$_i+$j+2] .= $mrg1 . "Jumlah Kredit Diperoleh  : $_sks";
      $isi[$_i+$j+3] .= $mrg1 . "Index Prestasi Kumulatif : $ipk";
      $isi[$_i+$j+4] .= $mrg1 . "Predikat Kelulusan       : $Predikat";
    } else {
      $isi_2[$_i+$j+2] .= $mrg1 . "Jumlah Kredit Diperoleh  : $_sks";
      $isi_2[$_i+$j+3] .= $mrg1 . "Index Prestasi Kumulatif : $ipk";
      $isi_2[$_i+$j+4] .= $mrg1 . "Predikat Kelulusan       : $Predikat";
    }
    // buat footer
    $Rektor = GetaField('pejabat', "KodeID='$_SESSION[KodeID]' and JabatanID", "REKTOR", "Nama");
    $Fakul = substr($mhsw['ProdiID'], 0, 1);
    //echo $Fakul;
    $Dekan  = GetPejabat($Fakul);
    $footer = $_lf.
      str_pad(' ', 82). "Jakarta, $tglCetak". $_lf.
      str_pad(' ', 25). str_pad("Rektor", 57). "Dekan".$_lf.$_lf.$_lf.$_lf.
      str_pad(' ', 25). str_pad($Rektor, 57). $Dekan;
  }
  else {
    $Maxsksnya = GetaField('prodi', 'ProdiID', $mhsw['ProdiID'], "TotalSKS");
    $sisa = $Maxsksnya - $_sks;
    $sisa = ($sisa < 0) ? 0 : $sisa;
    $BatasStudi = NamaTahun($mhsw['BatasStudi']);
    $isi[$_i] .= $mrg1 . "Belum Lulus $mhsw[Gelar]";
    $isi[$_i+1] .= $mrg1 . "Jumlah Kredit Diperoleh  : $_sks";
    $isi[$_i+2] .= $mrg1 . "Kredit Belum Ditempuh    : $sisa";
    $isi[$_i+3] .= $mrg1 . "Index Prestasi Kumulatif : $ipk";
    $isi[$_i+4] .= $mrg1 . "Masa studi berakhir pada semester $BatasStudi";
    // buat footer
    $KABAA = GetaField('pejabat', "KodeID='$_SESSION[KodeID]' and JabatanID", "KABAA", "Nama");
    $footer = $_lf.
      str_pad(' ', 82). "Jakarta, $tglCetak".$_lf.
      str_pad(' ', 82). "Biro Administrasi Akademik".$_lf.$_lf.$_lf.$_lf.
      str_pad(' ', 82). $KABAA;
  }
      
  for ($i=0; $i<=$maxbrs; $i++) fwrite($f, $isi[$i].$_lf);
  fwrite($f, $footer);
  if ($brs < 73) {
    
  } else {
    fwrite($f, chr(12));
    fwrite($f, $hdr);
    for ($h=0; $h<=$maxbrs; $h++) fwrite($f, $isi_2[$h].$_lf);
    fwrite($f, $_lf.
      str_pad(' ', 82). "Jakarta, $tglCetak". $_lf.
      str_pad(' ', 25). str_pad("Rektor", 57). "Dekan".$_lf.$_lf.$_lf.$_lf.
      str_pad(' ', 25). str_pad($Rektor, 57). $Dekan);
  }
  
  // Tutup file
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "transkrip");
}
function GetPredikatKelulusan($prodi, $ipk=0, $prodi) {
  $p = GetaField('predikat', 
    "IPKMin <= $ipk and $ipk <= IPKMax and ProdiID", $prodi, "Nama");
  return (empty($p))? "-" : $p;
}
function PutuskanJudul($judul = '', $max=45) {
  $max = $max;
  $judul = TRIM($judul);
  $judul = strtoupper($judul);
  $judul = rtrim($judul, '.');
  $len = strlen($judul);
  $arr = array();
  if ($len <= $max) $arr[] = $judul;
  else {
    $aw = 0;
    $ak = $max;
    $str = $judul;
    while (strlen($str) > 0) {
      $panjang = strlen($str);
      if ($panjang > $max) {
        $sub = substr($str, 0, $max);
        $sub = TRIM($sub);
        $pos = strrpos($sub, ' ');
        $ak = ($pos === false)? $ak : $pos;
        $sub = substr($str, 0, $ak);
        $sub = TRIM($sub);
        $arr[] = $sub;
            
        $str = substr($str, $ak+1, $len);
        $str = TRIM($str);
      }
      else {
        $arr[] = TRIM($str);
        $str = '';
      }
    }
  }
  return $arr;
}

function GetPejabat($fakultas){
  $w = GetaField('fakultas', 'FakultasID', $fakultas, "Pejabat");
  
  return $w;
}

// *** Parameters ***
$bhs = GetSetVar('bhs', 'id');
$crmhswid = GetSetVar('crmhswid');
$_Printer = GetSetVar('_Printer');
$_Jns = GetSetVar('_Jns');
$gos = (empty($_REQUEST['gos']))? "DataMhsw" : $_REQUEST['gos'];
echo $_SESSION['_Jns'];
// *** Main ***
TampilkanJudul("Transkrip Mahasiswa");
TampilkanPencarianMhsw('transkrip', 'DataMhsw', 1);
if (!empty($crmhswid)) {
  $mhsw = GetFields("mhsw m
    left outer join dosen d on m.PenasehatAkademik=d.Login
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join fakultas fak on prd.FakultasID=fak.FakultasID
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID",
    "m.MhswID", $crmhswid,
    "m.*, concat(d.Nama, ', ', d.Gelar) as PA,
    date_format(m.TanggalLahir, '%d %M %Y') as TGLLHR, 
    prg.Nama as PRG, prd.Nama as PRD, prd.Gelar, sm.Nama as SM, sm.Keluar, sm.Nilai,
    fak.FakultasID, fak.Nama as FAK, 
    fak.Pejabat, fak.Jabatan, prd.ProdiID as PRDID");
  if (!empty($mhsw)) $gos($mhsw);
}
?>
