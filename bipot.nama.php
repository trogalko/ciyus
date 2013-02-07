<?php
// Author: Emanuel Setio Dewo
// 10 Feb 2006

function DftrBipotNama() {
  global $mnux, $tok;
  $ki = AmbilBipot(1);
  $ka = AmbilBipot(-1);
  // Tampilkan
  echo "<p><a href='?sub=BipotNamaEdt&md=1&mnux=$mnux&tok=$tok'>Tambahkan Nama Biaya/Potongan</a></p>";
  echo "<p><table class=bsc cellspacing=1 cellpadding=4 width=100%>
  <tr><td width=50% valign=top>$ki</td>
  <td valign=top>$ka</td></tr>
  </table></p>";
  echo CatatanBipotNama();
}
function AmbilBipot($TrxID=1) {
  global $mnux, $tok;
  $s = "select bn.*, format(bn.DefJumlah, 0) as DEFJML, format(bn.DefBesar, 0) as DEFBSR, r.Nama as REK
    from bipotnama bn
    left outer join rekening r on bn.RekeningID=r.RekeningID
    where bn.TrxID=$TrxID and bn.KodeID='$_SESSION[KodeID]'
    order by bn.TrxID, bn.Urutan, bn.Nama";
  $r = _query($s);
  $Jdl = GetaField('trx', "TrxID", $TrxID, 'Nama');
  
  $n = 0;
  $a = "<p><table class=box cellspacing=1 cellpadding=4 width=100%>
    <tr><td class=ul colspan=6><b>$Jdl</b></td></tr>
    <tr><th class=ttl title='Nomer Urut dlm BPM'>Urutan</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Rekening</th>
    <th class=ttl>Baris</th>
	  <th class=ttl>Detil</th>
	  <th class=ttl>Denda</th>
    <th class=ttl>NA</th></tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    $a .= "<tr><td class=inp1>$w[Urutan]</td>
      <td $c><a href='?mnux=$mnux&tok=$tok&sub=BipotNamaEdt&md=0&bnid=$w[BIPOTNamaID]'><img src='img/edit.png'>
      $w[Nama]</a></td>
      <td $c><abbr title='$w[REK]'>$w[RekeningID]</abbr>&nbsp;</td>
      <td $c align=right>$w[Baris]</td>
	    <td $c align=right><img src='img/$w[Detil].gif'></td>
	    <td $c align=right><img src='img/$w[KenaDenda].gif'></td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  return "$a</table></p>";
}
function BipotNamaEdt() {
  global $mnux, $tok;

  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('bipotnama', 'BIPOTNamaID', $_REQUEST['bnid'], '*');
    $Jdl = "Edit Nama Biaya dan Potongan";
  }
  else {
    $w = array();
    $w['BIPOTNamaID'] = 0;
    $w['Urutan'] = 0;
    $w['Nama'] = '';
    $w['RekeningID'] = '';
    $w['TrxID'] = 1;
    $w['Baris'] = 0;
    $w['Detil'] = 'N';
    $w['KenaDenda'] = 'N';
    $w['DefJumlah'] = 0;
	  $w['DefBesar'] = 0;
	  $w['DipotongBeasiswa'] = 'N';
    $w['Catatan'] = '';
    $w['NA'] = 'N';
    $Jdl = "Tambah Nama Biaya & Potongan";
  }
  $NA = ($w['NA'] == 'Y')? 'checked' : '';
  $Detil = ($w['Detil'] == 'Y')? 'checked' : '';
  $Denda = ($w['KenaDenda'] == 'Y')? 'checked' : '';
  $DipotongBeasiswa = ($w['DipotongBeasiswa'] == 'Y')? 'checked' : '';
  $opttrx = GetOption2('trx', "Concat(TrxID, '. ', Nama)", 'TrxID, Nama', $w['TrxID'], '', 'TrxID');
  $optrek = GetOption2('rekening', "concat(RekeningID, ' - ', Nama)",
    'RekeningID', $w['RekeningID'], "KodeID='$_SESSION[KodeID]'", 'RekeningID');
  // Tampilkan
  CheckFormScript("Nama,TrxID");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='tok' value='$tok'>
  <input type=hidden name='sub' value='BipotNamaSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='bnid' value='$w[BIPOTNamaID]'>
  <tr><th class=ttl colspan=2>$Jdl</th></tr>
  <tr><td class=inp1>Urutan</td><td class=ul><input type=text name='Urutan' value='$w[Urutan]' size=3 maxlength=3> <font color=red>*)</font></td></tr>
  <tr><td class=inp1>Nama</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>Masukkan ke rekening</td><td class=ul><select name='RekeningID'>$optrek</select></td></tr>
  <tr><td class=inp1>Jenis Transaksi</td><td class=ul><select name='TrxID'>$opttrx</select></td></tr>
  <tr><td class=inp1>Di baris</td>
    <td class=ul><input type=text name='Baris' value='$w[Baris]' size=3 maxlength=3> Pada baris ke berapa dalam cetakan.</td></tr>
  <tr><td class=inp1>Tampilkan detail?</td>
    <td class=ul><input type=checkbox name='Detil' value='Y' $Detil> Detail jumlah x besar</td></tr>
  <tr><td class=inp1>Kena denda?</td>
    <td class=ul><input type=checkbox name='KenaDenda' value='Y' $Denda></td></tr>
  <tr><td class=inp1>Jumlah Default</td><td class=ul><input type=text name='DefJumlah' value='$w[DefJumlah]' size=15 maxlength=20></td></tr>
  <tr><td class=inp1>Besar Default</td><td class=ul><input type=text name='DefBesar' value='$w[DefBesar]' size=15 maxlength=20></td></tr>
  <tr><td class=inp1>Dipotong Beasiswa</td>
    <td class=ul><input type=checkbox name='DipotongBeasiswa' value='Y' $DipotongBeasiswa> Apakah dapat dipotong beasiswa?</td></tr>
  <tr><td class=inp1>Catatan</td><td class=ul><textarea name='Catatan' cols=30 rows=2>$w[Catatan]</textarea></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&tok=$tok&sub='\"></td></tr>
  </form></table></p>";
  echo CatatanBipotNama();
}
function BipotNamaSav() {
  $md = $_REQUEST['md']+0;
  $Urutan = $_REQUEST['Urutan']+0;
  $Nama = sqling($_REQUEST['Nama']);
  $RekeningID = $_REQUEST['RekeningID'];
  $TrxID = $_REQUEST['TrxID']+0;
  $Baris = $_REQUEST['Baris']+0;
  $Detil = (empty($_REQUEST['Detil']))? 'N' : $_REQUEST['Detil'];
  $KenaDenda = (empty($_REQUEST['KenaDenda']))? 'N' : $_REQUEST['KenaDenda'];
  $DefJumlah = $_REQUEST['DefJumlah']+0;
  $DefBesar = $_REQUEST['DefBesar']+0;
  $Catatan = sqling($_REQUEST['Catatan']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  $DipotongBeasiswa = (empty($_REQUEST['DipotongBeasiswa']))? 'N' : $_REQUEST['DipotongBeasiswa'];
  // Simpan
  if ($md == 0) {
    $s = "update bipotnama set Urutan='$Urutan', Nama='$Nama', RekeningID='$RekeningID',
	    DefJumlah='$DefJumlah', DefBesar='$DefBesar',
      TrxID='$TrxID', Baris=$Baris, Detil='$Detil', KenaDenda='$KenaDenda',
      DipotongBeasiswa='$DipotongBeasiswa', 
      Catatan='$Catatan', NA='$NA',
      LoginEdit='$_SESSION[_Login]', TglEdit=now()
      where BipotNamaID='$_REQUEST[bnid]' ";
  }
  else {
    $s = "insert into bipotnama (Urutan, Nama, RekeningID, KodeID, 
	    Baris, Detil, KenaDenda, DefJumlah, DefBesar,
	    DipotongBeasiswa,
	    TrxID, Catatan, NA, TglBuat, LoginBuat)
      values ('$Urutan', '$Nama', '$RekeningID', '$_SESSION[KodeID]', 
	    $Baris, '$Detil', '$KenaDenda', '$DefJumlah', '$DefBesar',
	    '$DipotongBeasiswa',
	    '$TrxID', '$Catatan', '$NA',
      now(), '$_SESSION[_Login]')";
  }
  $r = _query($s);
  
  DftrBipotNama();
}

function CatatanBipotNama() {
  return "<p>Catatan: <br />
  <table class=box cellspacing=1 cellpadding=4>
  <tr><td class=ul><b>Urutan</b>
    <td class=ul>Tampilan urutan dalam pencetakan BPM (Bukti Pembayaran Mahasiswa).<br />
    Jika ada 2 atau lebih biaya/potongan mahasiswa yang memiliki nomer urut sama,
    maka pada pencetakan BPM biaya/potongan tersebut akan dijumlahkan.</td></tr>
  </table></tr>";
}
?>
