<?php
// Author: Emanuel Setio Dewo
// 31/07/2006
// www.sisfokampus.net

// *** Fuctions ***
function KHSAkhir($mhswid) {
  $s = "select k.TahunID, k.StatusMhswID, sm.Nama as STATUS
    from khs k
      left outer join statusmhsw sm on k.StatusMhswID=sm.StatusMhswID
    where k.MhswID='$mhswid'
    order by k.TahunID desc limit 1";
  $r = _query($s);
  return _fetch_array($r);
}

function GetTindakan($mhswid){
  global $_lf;
  $s = "Select * from prestasi where mhswid = '$mhswid' and JenisPrestasi = -1 order by PrestasiID ";
  $r = _query($s);
  $n = 0;
  $Prestasi_ = "Tindakan";
  while($w =  _fetch_array($r)){
    $n++;
    if ($n == 1) {
      $pres = $Prestasi_;
    } else $pres = '';
    $Prestasi .= str_pad("$pres : ",22,' ',STR_PAD_LEFT).str_pad($n.'.',4,' ') . str_pad($w['Judul'],30,' ',STR_PAD_RIGHT).$_lf;
  }
  return $Prestasi;
}

function GetTindakan2($mhswid){
  global $_lf;
  $s = "Select * from prestasi where mhswid = '$mhswid' and JenisPrestasi = 1 order by PrestasiID ";
  $r = _query($s);
  $n = 0;
  $Prestasi_ = "Prestasi";
  while($w =  _fetch_array($r)){
    $n++;
    if ($n == 1) {
      $pres = $Prestasi_;
    } else $pres = '';
    $Prestasi .= str_pad("$pres : ",22,' ',STR_PAD_LEFT).str_pad($n.'.',4,' ') . str_pad($w['Judul'],30,' ',STR_PAD_RIGHT).$_lf;
  }
  return $Prestasi;
}
 
function FormDataMhsw($m) {
  global $_lf;
  $TglLahir = FormatTanggal($m['TanggalLahir']);
  $BatasStudi = NamaTahun($m['BatasStudi']);
  $PA = GetaField('dosen', 'Login', $m['PenasehatAkademik'], "concat(Nama, ', ', Gelar)");
  $KHSAkhir = KHSAkhir($m['MhswID']);
  $StatusAkhir = $KHSAkhir['STATUS'];
  $GetTindakan = GetTindakan($m['MhswID']);
  $GetTindakan2 = GetTindakan2($m['MhswID']);
  $Agama = GetaField('agama', 'Agama', $m['Agama'], 'Nama');
  $AgamaAyah = GetaField('agama','Agama',$m['AgamaAyah'],'Nama');
  $AgamaIbu  = GetaField('agama','Agama',$m['AgamaIbu'],'Nama');
  $HidupAyah = GetaField('hidup','Hidup',$m['HidupAyah'],'Nama');
  $HidupIbu = GetaField('hidup','Hidup',$m['HidupIbu'],'Nama');
  $PekerjaanAyah = GetaField('pekerjaanortu','Pekerjaan',$m['PekerjaanAyah'],'Nama');
  $PekerjaanIbu = GetaField('pekerjaanortu','Pekerjaan',$m['PekerjaanIbu'],'Nama');
  $PendidikanAyah = GetaField('pendidikanortu','Pendidikan',$m['PendidikanAyah'],'Nama');
  $PendidikanIbu = GetaField('pendidikanortu','Pendidikan',$m['PendidikanIbu'],'Nama');
  $cutikuliah = GetArrayTable("select TahunID from khs where StatusMhswID = 'C' and MhswID = '$m[MhswID]'", 'TahunID', 'TahunID');
  // *** Mulai ***
  $mxc = 114;
  $grs = str_pad("-", $mxc, "-").$_lf;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(8));
  // HEADER
  fwrite($f, str_pad("*** Formulir Perubahan Data Mahasiswa ***", $mxc, ' ', STR_PAD_BOTH).$_lf . $_lf);
  fwrite($f, str_pad("N P M : ", 50, ' ', STR_PAD_LEFT).
    str_pad($m['MhswID'], 60).$_lf);
  fwrite($f, str_pad("Nama Mahasiswa : ", 50, ' ', STR_PAD_LEFT).
    $m['Nama'].$_lf);
  fwrite($f, str_pad("Tempat, Tgl Lahir : ", 50, ' ', STR_PAD_LEFT).
    $m['TempatLahir'] . ', ' . $TglLahir.$_lf);
  fwrite($f, str_pad("Batas Waktu Studi : ", 50, ' ', STR_PAD_LEFT).
    $BatasStudi.$_lf);
  fwrite($f, str_pad("Status Terakhir : ", 50, ' ', STR_PAD_LEFT).
    $StatusAkhir. $_lf);
  fwrite($f, str_pad("Penasehat Akademik : ", 50, ' ', STR_PAD_LEFT) .
    $PA . $_lf . $grs);
  fwrite($f, "|     KETERANGAN    |               DATA YANG ADA                   |              PERUBAHAN DATA                |".$_lf.$grs);
  
  // *** Detail data ***
  $grs2 = str_pad('.', 46, '.').$_lf;
  $Almt = str_replace(chr(13).chr(10), ", ", $m['Alamat']);
  fwrite($f, str_pad("Nama (Sesuai Akte) : ", 22, ' ', STR_PAD_LEFT) . 
    str_pad($m['Nama'], 46) . $grs2);
  fwrite($f, str_pad("Tempat, Tgl Lahir : ", 22, ' ', STR_PAD_LEFT).
    str_pad($m['TempatLahir'] . ', ' . $TglLahir, 46).$grs2);
  fwrite($f, str_pad("Alamat : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($Almt, 46) . $grs2);
  fwrite($f, str_pad("RT/RW : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($m['RT'].'/'.$m['RW'], 46) . $grs2);
  fwrite($f, str_pad("Kode Pos : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($m['KodePos'], 46) . $grs2);
  fwrite($f, str_pad("Kota : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($m['Kota'], 46). $grs2);
  fwrite($f, str_pad("Agama : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($Agama, 46). $grs2);
  $wn = GetaField('warganegara', "WargaNegara", $m['WargaNegara'], 'Nama');
  fwrite($f, str_pad("Kewarganegaraan : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($wn, 46). $grs2);
  $AsalSekolah = GetaField('asalsekolah', 'SekolahID', $m['AsalSekolah'], 'Nama');
  fwrite($f, str_pad("Asal Sekolah : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($m['AsalSekolah'] . '-' . $AsalSekolah, 46). $grs2);
  $Jurusan = GetaField('jurusansekolah', "JurusanSekolahID", $m['JurusanSekolah'], 'Nama');
  fwrite($f, str_pad("Jurusan/Lulus : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($Jurusan. '/'.$m['TahunLulus'], 46). $grs2);
  fwrite($f, str_pad("Nomer Ijazah : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($m['IjazahSekolah'], 46). $grs2);
  fwrite($f, str_pad("Anak ke/dari : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($m['AnakKe'].'/'.$m['JumlahSaudara'], 46). $grs2);
  fwrite($f, str_pad("Cuti Kuliah : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($cutikuliah, 46). $grs2);
    
  //if ($Tindakan == )
  //fwrite($f, str_pad("Tindakan : ", 22, ' ', STR_PAD_LEFT) .
  //  str_pad('', 46). $_lf);
  //$Prestasi = GetTindakan($m['MhswID']);
  fwrite($f, $GetTindakan);
  //fwrite($f, str_pad("Prestasi : ", 22, ' ', STR_PAD_LEFT) .
  //  str_pad("", 46). $_lf);
  fwrite($f, $GetTindakan2);
  fwrite($f, $_lf.str_pad("Nama Ayah : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($m['NamaAyah'], 46). $grs2);
  fwrite($f, str_pad("Agama : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($AgamaAyah, 46). $grs2);
  fwrite($f, str_pad("Status : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($HidupAyah, 46). $grs2);
  fwrite($f, str_pad("Pekerjaan : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($PekerjaanAyah, 46). $grs2);
  fwrite($f, str_pad("Pendidikan : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($PendidikanAyah, 46). $grs2. $_lf);
  fwrite($f, str_pad("Nama Ibu : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($m['NamaIbu'], 46). $grs2);
  fwrite($f, str_pad("Agama : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($AgamaIbu, 46). $grs2);
  fwrite($f, str_pad("Status : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($HidupIbu, 46). $grs2);
  fwrite($f, str_pad("Pekerjaan : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($PekerjaanIbu, 46). $grs2);
  fwrite($f, str_pad("Pendidikan : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($PendidikanIbu, 46). $grs2. $_lf);
  fwrite($f, str_pad("Alamat Orang Tua : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($m['AlamatOrtu'], 46). $grs2);
  fwrite($f, str_pad("No. Tes : ", 22, ' ', STR_PAD_LEFT) .
    str_pad($m['PMBID'], 46). $_lf);
  fwrite($f, $grs.$_lf.$_lf);
  $Tanda  = str_pad("+-------------------------------------------------------------+",$mxc,' ',STR_PAD_BOTH).$_lf;
  $Tanda .= str_pad("|        LENGKAPI DAN PERBAIKI DATA ANDA YANG SALAH.          |",$mxc,' ',STR_PAD_BOTH).$_lf;
  $Tanda .= str_pad("|          KEMBALIKAN BERSAMA PENYERAHAN KRS ANDA             |",$mxc,' ',STR_PAD_BOTH).$_lf;
  $Tanda .= str_pad("|                                                             |",$mxc,' ',STR_PAD_BOTH).$_lf;
  $Tanda .= str_pad("|        UNTUK PERUBAHAN NAMA, TEMPAT & TANGGAL LAHIR         |",$mxc,' ',STR_PAD_BOTH).$_lf;
  $Tanda .= str_pad("|             HARAP MENYERTAKAN AKTE KELAHIRAN                |",$mxc,' ',STR_PAD_BOTH).$_lf;
  $Tanda .= str_pad("+-------------------------------------------------------------+",$mxc,' ',STR_PAD_BOTH).$_lf;
  fwrite($f, $Tanda.$_lf);
  fwrite($f, str_pad("Tanggal Cetak : ".date("d-m-Y H:i"),$mxc,' ',STR_PAD_LEFT));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf);
}

// *** Parameters ***
$crmhswid = GetSetVar('crmhswid');

// *** Main ***
TampilkanJudul("Form Data Pribadi Mahasiswa");
TampilkanPencarianMhsw('mhsw.pribadi', 'FormDataMhsw', 1);
if (!empty($crmhswid)) {
  $mhsw = GetFields("mhsw m 
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID", 
    "m.MhswID", $crmhswid, 
    "m.*, prg.Nama as PRG, prd.Nama as PRD");
  if (!empty($mhsw)) {
    if (!empty($_REQUEST['gos'])) {
      $gos = $_REQUEST['gos'];
      $gos($mhsw);
    }
  }
  else echo ErrorMsg("Mahasiswa Tidak Ditemukan",
  "Mahasiswa dengan NPM <b>$crmhswid</b> tidak ditemukan.");
}
?>
