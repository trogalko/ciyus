<?php
// Author: Emanuel Setio Dewo
// www.sisfokampus.net
// 28 November 2006
// setio.dewo@gmail.com
// Desc: mencetak daftar perolehan SKS mahasiswa secara massal

// *** Parameters ***
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');
$tahun = GetSetVar('tahun');

// *** Main ***
TampilkanJudul("Daftar Perolehan SKS Mahasiswa (Cetak Massal)");
TampilkanHeaderPerolehanSKS();
if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

// *** functions ***
function TampilkanHeaderPerolehanSKS() {
  CheckFormScript("DariNPM,SampaiNPM");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='CetakPerolehanSKS'>
  <tr><td class=wrn>$_SESSION[KodeID]</td>
    <td class=inp>Dari NPM :</td>
    <td class=ul><input type=text name='DariNPM' value='$_SESSION[DariNPM]' size=20 maxlength=50></td>
    <td class=inp>Sampai NPM :</td>
    <td class=ul><input type=text name='SampaiNPM' value='$_SESSION[SampaiNPM]' size=20 maxlength=50></td>
    <td class=ul><input type=submit name='Cetak' value='Cetak'></td>
  </form></table></p>";
}
function CetakPerolehanSKS() {
  $s = "select m.MhswID, m.Nama, sm.Nama as SM
    from mhsw m
      left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    where ('$_SESSION[DariNPM]' <= m.MhswID)
      and (m.MhswID <= '$_SESSION[SampaiNPM]')
      and sm.Nilai=1
    order by m.MhswID";
  $r = _query($s);
  $jml = _num_rows($r);
  if ($jml == 0) echo ErrorMsg("Tidak ada Data",
    "Tidak ada mahasiswa dalam rentang NPM: <b>$_SESSION[DariNPM]</b> s/d <b>$_SESSION[SampaiNPM]</b>.");
  else {
    echo "<p>Akan diproses: <font size=+1>$jml</font> mahasiswa.
    Tunggu sampai proses selesai. Setelah selesai baru akan dicetak.</p>";
    // Simpan data ke memori
    $n = 0;
    while ($w = _fetch_array($r)) {
      $n++;
      $_SESSION["PERO-MhswID-$n"] = $w['MhswID'];
    }
    $_SESSION["PERO-POS"] = 0;
    $_SESSION["PERO-MAX"] = $jml;
    $_SESSION["PERO-FILE"] = "tmp/$_SESSION[_Login].PerolehanSKS.dwoprn";
    // init file
    $f = fopen($_SESSION["PERO-FILE"], 'w');
    fwrite($f, chr(27));
    fclose($f);
    // IFRAME
    echo "<p><IFRAME src='akd.lap.perolehansks.go.php' frameborder=0 height=100% width=100%>
    </IFRAME></p>";
  }
}
?>
