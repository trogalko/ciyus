<?php
// Author: Emanuel Setio Dewo, 17 Okt 2006
include_once "sisfokampus.php";
HeaderSisfoKampus("Proses Data Kelulusan Mhsw");

// *** Functions ***
function TampilkanPesanProses() {
  echo "<p>Anda akan memproses data kelulusan Mhsw.<br />
  Pastikan bahwa tabel <b>_mhswlulus</b> telah diisi data dari tabel <b>MSMHA</b>.<br />
  Proses ini akan memeriksa apakah data kelulusan seorang mhsw sudah diproses atau blm sehingga data tdk akan double.<br />
  <form action='?' method=GET>
  Tekan tombol berikut untuk memulai <input type=submit name='gos' value='ProsesSekarang'>
  </form></p>";
}
function FormatTglDB($tgl) {
  $thn = substr($tgl, 6, 4);
  $bln = substr($tgl, 3, 2);
  $tag = substr($tgl, 0, 2);
  return "$thn-$bln-$tag";
}
function ProsesSekarang() {
  $s = "select *
    from _mhswlulus
    where Sudah=0";
  $r = _query($s);
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    $ada = GetFields("ta", "MhswID", $w['NIMHSMSMHS'], '*');
    if (empty($ada)) {
      $mhsw = GetFields('mhsw', "MhswID", $w['NIMHSMSMHS'], '*');
      // 1. Tambahkan data di tabel TA
      $s1 = "insert into ta (MhswID, Judul, StatusLulusID, NA)
        values ('$w[NIMHSMSMHS]', '$w[JUDU1MSMHS] $w[JUDU2MSMHS] $w[JUDU3MSMHS] $w[JUDU4MSMHS] $w[JUDU5MSMHS]', '$w[STLLSMSMHS]', 'N')";
      $r1 = _query($s1);
      $TAID = GetLastID();
      // 2. Update tabel mhsw (masukkan ID TA di tabel MHSW, update data kelulusan, dll
      $TanggalLahir = FormatTglDB($w['TGLHRMSMHS']);
      $TglSKKeluar = FormatTglDB($w['TGLLSMSMHS']);
      $TglSKMasuk = FormatTglDB($w['TGLREMSMHS']);
      $TglIjazah = FormatTglDB($w['TGIJSMSMHS']);
      $s2 = "update mhsw set TAID=$TAID, 
        TempatLahir='$w[TPLHRMSMHS]',
        TanggalLahir='$TanggalLahir',
        Alamat='$w[ALJA1MSMHS] $w[ALJA2MSMHS]',
        Kota='$w[KOJAKMSMHS]',
        KodePos='$w[KPJAKMSMHS]',
        Telepon='$w[TELJAMSMHS]', Telephone='$w[TELJAMSMHS]',
        Agama='$w[KDAGMMSMHS]', WargaNegara='$w[KDWNGMSMHS]',
        TglSKKeluar='$TglSKKeluar',
        NoIdentitas='$w[NILUNMSMHS]',
        NoProdi='$w[NILJRMSMHS]',
        IPK='$w[NLIPKMSMHS]',
        TotalSKS='$w[SKSTTMSMHS]',
        TahunLulus='$w[LTSMAMSMHS]',
        IjazahSekolah='$w[NOIJAMSMHS]',
        SKMasuk='$w[NOSKRMSMHS]',
        TglSKMasuk='$TglSKMasuk',
        AsalSekolah='$w[KDSMAMSMHS]',
        JurusanSekolah='$w[KJSMAMSMHS]',
        NoIjazah='$w[NOIJSMSMHS]',
        TglIjazah='$TglIjazah'
        where MhswID='$w[NIMHSMSMHS]' ";
      $r2 = _query($s2);
      $str = "<font color=red>Dibuat</font><ol><li>$s1</li><li>$s2</li></ol>";
      // Set flag
      $s3 = "update _mhswlulus set Sudah=1 where NIMHSMSMHS='$w[NIMHSMSMHS]' ";
      $r3 = _query($s3);
    }
    else {
      $str = "<font color=gray>Sudah ada</font>";
    }
    echo "<li>$w[NIMHSMSMHS] &raquo; $str
    </li>";
  }
  echo "</ol>";
}

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "TampilkanPesanProses" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Proses Data Kelulusan Mhsw");
$gos();
?>
