<?php
// Author: Emanuel Setio Dewo
// 23 Feb 2006

// *** Functions ***
function TampilkanPencarianCAMAUbah($act='') {
  global $arrID, $MinLength;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$act'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>No. PMB/USM</td><td class=ul><input type=text name='crpmbid' value='$_SESSION[crpmbid]' size=20 maxlength=50>
    <input type=submit name='Cari' value='Cari'>
    Masukkan minimal <b>$MinLength</b> angka No. PMB</td></tr>
  </form></table></p>";
}
function TampilkanDaftarCAMA() {
  $s = "select p.PMBID, p.PMBPeriodID, p.Nama, pf.Nama as FRM, 
    pp.TglMulai, pp.TglSelesai,
    format(p.Harga, 0) as HRG
    from pmb p
    left outer join pmbformulir pf on p.PMBFormulirID=pf.PMBFormulirID
    left outer join pmbperiod pp on p.PMBPeriodID=pp.PMBPeriodID
    where p.PMBID like '$_SESSION[crpmbid]%'
    order by p.PMBID";
  $r = _query($s);
  $skrg = date('Y-m-d');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>No. PMB</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Periode</th>
    <th class=ttl>Jenis Formulir</th>
    <th class=ttl>Harga</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $c = 'class=ul';
    $_edt = ($w['TglSelesai'] < $skrg)? "<font title='Sudah tidak dapat diubah lagi karena periode PMB telah usai.'>$w[PMBID]</font>" :
      "<a href='?mnux=pmbform.ubah&pmbid=$w[PMBID]&gos=UbahFrm'><img src='img/edit.png'>
      $w[PMBID]</a>";
    echo "<tr>
      <td $c>$_edt</td>
      <td $c>$w[Nama]</td>
      <td $c>$w[PMBPeriodID]</td>
      <td $c>$w[FRM]</td>
      <td $c>$w[HRG]</td>
      </tr>";
  }
  echo "</table></p>";
}
function UbahFrm() {
  $w = GetFields('pmb', 'PMBID', $_REQUEST['pmbid'], '*');
  $nmfrm = GetaField('pmbformulir', 'PMBFormulirID', $w['PMBFormulirID'], "concat(Nama, ' (', JumlahPilihan, ' pil., Rp. ', Format(Harga, 0), ')')");
  $optfrm = GetOption2('pmbformulir', "concat(Nama, ' (', JumlahPilihan, ' pil, Rp. ', Format(Harga, 0), ')')",
    'Nama', $w['PMBFormulirID'], '', 'PMBFormulirID');
  $snm = session_name(); $sid = session_id();
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=500>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='pmbform.ubah'>
  <input type=hidden name='gos' value='UbahFrmSav'>
  <input type=hidden name='pmbid' value='$_REQUEST[pmbid]'>
  <input type=hidden name='pmbformuliridasli' value='$w[PMBFormulirID]'>
  <tr><th class=ttl colspan=2>Ubah Jenis Formulir</th></tr>
  <tr><td class=ul colspan=2><b>Catatan:</b> Pengubahan jenis formulir akan me-reset Prodi pilihan dari pendaftar. Untuk itu setelah mengubah jenis formulir, Anda harus mengisi kembali pilihan prodi pendaftar.</td></tr>
  <tr><td class=inp1>No. PMB</td><td class=ul><b>$w[PMBID]</b></td></tr>
  <tr><td class=inp1>Nama</td><td class=ul><b>$w[Nama]</b></td></tr>
  <tr><td class=inp1>Formulir yg dimiliki</td><td class=ul>$nmfrm</td></tr>
  <tr><td class=inp1>Ubah Formulir Menjadi</td><td class=ul><select name='PMBFormulirID'>$optfrm</select></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=pmbform.ubah&$snm=$sid'\"></td></tr>
  </form></table></p>";
}
function UbahFrmSav() {
  $pmbformuliridasli = $_REQUEST['pmbformuliridasli'];
  $PMBID = $_REQUEST['pmbid'];
  $PMBFormulirID = $_REQUEST['PMBFormulirID'];
  // Jika PMBFormulirID tidak diset
  if (empty($PMBFormulirID)) {
    echo ErrorMsg("Gagal Simpan",
      "Anda harus menentukan jenis formulir pengganti.<br />
      Formulir pengganti tidak boleh kosong.");
    UbahFrm();
  }
  else {
    if ($pmbformuliridasli == $PMBFormulirID) {
      echo ErrorMsg("Tidak Disimpan",
        "Formulir pengganti sama dengan formulir sebelumnya.<br />
        Karena tidak ada perubahan, maka data tidak disimpan.");
      TampilkanDaftarCAMA();
    }
    else {
      $s = "update pmb
        set PMBFormulirID='$PMBFormulirID', ProdiID='',
        Pilihan1='', Pilihan2='', Pilihan3=''
        where PMBID='$PMBID' ";
      $r = _query($s);
      echo Konfirmasi("Perubahan Telah Disimpan",
        "Formulir baru sudah menggantikan formulir lama untuk pendaftar dengan No PMB: <b>$PMBID</b>.<br />
        Anda harus segera mengubah Program Studi pilihan pendaftar karena telah direset oleh sistem.
        <hr size=1 color=silver />
        Pilihan: <a href='?mnux=pmbform&gos=PMBEdt0&md=0&pmbid=$PMBID&pmbfid=$PMBFormulirID'>Edit Data PMB</a> |
        <a href='?mnux=pmbform.ubah'>Kembali ke Ubah Formulir</a>");
    }
  }
}

// *** Parameters ***
$MinLength = 5;
$crpmbid = GetSetVar('crpmbid');
$gos = (empty($_REQUEST['gos']))? 'TampilkanDaftarCAMA' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Ubah Formulir Calon Mahasiswa");
TampilkanPencarianCAMAUbah('pmbform.ubah');
if (strlen($crpmbid) >= $MinLength) $gos();
?>