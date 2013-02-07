<?php
// Author: Emanuel Setio Dewo
// 11 Sept 2006
$arrTag = array(
  "TGSUMMSTAG~3",
  "TGSEMMSTAG~11",
  "TGKOKMSTAG~8",
  "TGSKSMSTAG~5",
  "TGPRAMSTAG~6",
  "TGSKIMSTAG~38",
  "TGDNSMSTAG~34",
  "TGDENMSTAG~35",
  "TGOPSMSTAG~12",
  "TGADSMSTAG~40",
  "TGADKMSTAG~41",
  "TGUJSMSTAG~39",
  "TGHUTMSTAG~30",
  "PMPINMSTAG~32"
  );
$arrBay = array(
  "PMSUMMSTAG~3",
  "PMSEMMSTAG~11",
  "PMKOKMSTAG~8",
  "PMSKSMSTAG~5",
  "PMPRAMSTAG~6",
  "PMSKIMSTAG~38",
  "PMDNSMSTAG~34",
  "PMDENMSTAG~35",
  "PMOPSMSTAG~12",
  "PMADSMSTAG~40",
  "PMADKMSTAG~41",
  "PMUJSMSTAG~39",
  "PMHUTMSTAG~30",
  "PMPINMSTAG~32"
  );
function TemukanKey($arr, $nama) {
  $key = '';
  $i = 0;
  $ktm = false;
  while ($i <= sizeof($arr) && !$ktm) {
    $str = explode('~', $arr[$i]);
    if ($str[0] == $nama) {
      $ktm = TRUE;
      $key = $str[1];
    }
    $i++;
  }
  return $key;
}
function TemukanNama($arr, $keynya) {
  $key = '';
  $i = 0;
  $ktm = false;
  while ($i <= sizeof($arr) && !$ktm) {
    $str = explode('~', $arr[$i]);
    if ($str[1] == $keynya) {
      $ktm = TRUE;
      $key = $str[0];
    }
    $i++;
  }
  return $key;
}
function ProsesSekarang() {
  global $arrTag, $arrBay;
  $s = "select * from _tagihan";
  $r = _query($s);
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    $_test = '';
    //$m = GetField('mhsw', "MhswID", $w['NIMHSMSTAG'], "BIPOTID");
    for ($i = 0; $i < sizeof($arrTag); $i++) {
      $field = explode('~', $arrTag[$i]);
      $jumlah = $w[$field[0]]+0;
      if ($jumlah <> 0) {
        $keyBay = TemukanNama($arrBay, $field[1]);
        $bayar = $w[$keyBay]+0;
        //$_test .= " -$field[0]:$keyBay- ";
        // Cek apakah sudah ada tagihannya?
        $ada = GetFields("bipotmhsw",
          "MhswID='$w[NIMHSMSTAG]' and TahunID='$w[THSMSMSTAG]' and BIPOTNamaID",
          $field[1], "*");
        $bn = GetFields("bipotnama", "BIPOTNamaID", $field[1], "Nama");
        // Jika tidak ada insert
        $TRXID = ($jumlah > 0)? 1 : -1;
        if (empty($ada)) {
          $_s = "insert into bipotmhsw (PMBMhswID, MhswID, TahunID, BIPOTNamaID, Nama, TrxID,
            Jumlah, Besar, Dibayar, LoginBuat, TanggalBuat)
            values (1, '$w[NIMHSMSTAG]', '$w[THSMSMSTAG]', $field[1], '$bn[Nama]', $TRXID,
            1, $jumlah, $bayar, 'IMPORT-20061', now())";
          $_r = _query($_s); 
        }
        // Jika sudah ada maka edit
        else {
          $_s = "update bipotmhsw set Besar=$jumlah, Dibayar=$bayar,
            LoginEdit='IMPORT-20061', TanggalEdit=now()
            where BIPOTMhswID=$ada[BIPOTMhswID] ";
          $_r = _query($_s);
        }
        $_test .= "$_s<br />";
      } 
    }
    echo "<li>
    $w[THSMSMSTAG].
    $w[NIMHSMSTAG].
    $_test
    </li>";
  }
  echo "</ol>";
}
function TanyaDulu() {
  echo "<p>Script ini akan mengimport tabel MSTAG dari program lama ke Sisfo Kampus.<br />
  Harap diperhatikan bahwa tabel temporary <b>_tagihan</b> harus sudah terisi dari tabel MSTAG.<br />
  Tekan tombol berikut ini untuk memulai proses:
  <input type=button name='Proses' value='Proses Tagihan' onClick=\"location='?gos=ProsesSekarang'\">";
}

// *** Main ***
$gos = (empty($_REQUEST['gos']))? "TanyaDulu" : $_REQUEST['gos'];
include_once "sisfokampus.php";
HeaderSisfoKampus("Import Tagihan Dari MSTAG");
TampilkanJudul("Import Tagihan Dari MSTAG");
$gos();
?>
