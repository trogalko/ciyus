<?php
// AUthor: Emanuel Setio Dewo
// Email: setio.dewo@gmail.com
// 06 Desember 2006

include_once "mhswbaru.lib.php";

// *** Parameters ***
$pmbid = GetSetVar('pmbid');
$pmb = GetFields("pmb p
  left outer join program prg on p.ProgramID=prg.ProgramID
  left outer join prodi prd on p.ProdiID=prd.ProdiID",
  'p.PMBID', $pmbid, 'p.*, prg.Nama as PRG, prd.Nama as PRD');

if (empty($pmb)) die(ErrorMsg("Data tidak Ditemukan", 
  "Data Calon Mahasiswa dengan No PMB: <b>$pmbid</b> tidak ditemukan."));
  
// *** Main ***
TampilkanJudul("Diskon Mahasiswa Baru");
echo HeaderCAMA($pmb);
TampilkanModulDiskonCAMA($pmb);
if (!empty($_REQUEST['gos'])) $_REQUEST['gos']($pmb);

// *** Functions ***
function TampilkanModulDiskonCAMA($pmb) {
  TampilkanBIPOTCAMA1($pmb);
  TampilkanFormulirDiskon($pmb);
}
function TampilkanFormulirDiskon($pmb) {
  JSHitungDiskon();
  CheckFormScript("Diskon,Besar");
  $optPot = GetOption2("bipotnama", "Nama", "Nama", 0, "TrxID=-1", "BIPOTNamaID");
  echo "<p><font size=+1>Formulir Diskon</font></p>";
  echo "<blockquote><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='DSC' method=POST onSubmit='return CheckForm(this)'>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='DISCSAV'>
  <input type=hidden name='pmbid' value='$pmb[PMBID]'>
  <input type=hidden name='BypassMenu' value=1>
  <tr><td class=inp>Account Diskon :</td>
    <td class=ul><select name='BIPOTNamaID'>$optPot</select></td></tr>
  <tr><td class=inp>Persentase Diskon :</td>
    <td class=ul><input type=text name='Diskon' size=3 maxlength=3 onChange='HitungDiskon(this.form)'> %</td></tr>
  <tr><td class=inp>Dari Nilai Biaya :</td>
    <td class=ul><input type=text name='Besar' size=20 maxlength=20 onChange='HitungDiskon(this.form)'></td></tr>
  <tr><td class=inp>Besarnya Diskon :</td>
    <td class=ul><input type=text readonly name='TotalDiskon' size=20 maxlength=20> <input type=button name='Hitung' value='Hitung' onClick='HitungDiskon(DSC)'></td></tr>
  <tr><td class=inp>Catatan Diskon :</td>
    <td class=ul><textarea name='Catatan' cols=30 rows=3></textarea></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?'\"></td></tr>
  </form></table></blockquote>";
}
function DISCSAV($pmb) {
  $BIPOTNamaID = $_REQUEST['BIPOTNamaID'];
  $NamaBipot = GetaField('bipotnama', "BIPOTNamaID", $BIPOTNamaID, "Nama");
  $Diskon = $_REQUEST['Diskon']+0;
  $Besar = $_REQUEST['Besar']+0;
  $BesarDiskon = $Besar * $Diskon / 100;
  $Catatan = sqling($_REQUEST['Catatan']);
  // Simpan
  $s = "insert into bipotmhsw
    (PMBMhswID, PMBID, TahunID, BIPOT2ID, BIPOTNamaID,
    Nama, TrxID, Jumlah, Besar, Dibayar, 
    Catatan, LoginBuat, TanggalBuat)
    values
    (0, '$pmb[PMBID]', '$pmb[PMBPeriodID]', 0, '$BIPOTNamaID', 
    '$NamaBipot', -1, 1, $BesarDiskon, 0,
    'Diskon: $Diskon % dari: $Besar. $Catatan', '$_SESSION[_Login]', now() 
    )";
  $r = _query($s);
  echo "<script>window.location = '?';</script>";
}
function JSHitungDiskon() {
  echo <<<EJS
  <SCRIPT>
  function formatCurrency(num) {
    num = num.toString().replace(/\$|\,/g,'');
    if (isNaN(num)) num = "0";
    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num*100+0.50000000001);
    cents = num%100;
    num = Math.floor(num/100).toString();
    if (cents<10) cents = "0" + cents;
    for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
      num = num.substring(0,num.length-(4*i+3))+','+
        num.substring(num.length-(4*i+3));
    return (((sign)?'':'-') + num + '.' + cents);
  }
  function HitungDiskon(frm, i) {
    var Diskon = frm.Diskon.value;
    var Besar = frm.Besar.value;
    if (Diskon > 0 && Besar > 0) {
      frm.TotalDiskon.value = formatCurrency(Besar * Diskon / 100);
    }
  }
  </SCRIPT>
EJS;
}
?>
