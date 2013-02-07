<?php
// Author: Emanuel Setio Dewo
// 26 April 2006
// www.sisfokampus.net

// *** Functions ***
function DftrBea() {
  if (empty($_SESSION['tahun']))
    echo ErrorMsg("Gagal Ambil Data",
      "Tentukan terlebih dahulu Tahun Akademik untuk daftar penerima beasiswa.");
  else {
    TampilkanTambahBeasiswaMhsw();
    DftrBea1();
  }
}
function DftrBea1() {
  global $pref, $token;
  // Ambil header
  $s0 = "select bn.BIPOTNamaID, bn.Nama
    from bipotnama bn
    where bn.DipotongBeasiswa='Y'
    order by bn.Nama";
  $r0 = _query($s0); $arrBN = array(); $arrNama = array();
  while ($w0 = _fetch_array($r0)) {
    $arrBN[] = $w0['BIPOTNamaID'];
    $arrNama[] = $w0['Nama'];
  }
  // Prasyarat
  $hdrprs = '';
  if (!empty($_SESSION['BeasiswaID'])) {
    $beas = GetFields('beasiswa', 'BeasiswaID', $_SESSION['BeasiswaID'], '*');
    $prs = TRIM($beas['Prasyarat'], '~');
    if (!empty($prs)) {
      $hdrprs .= "<td class=ul>&raquo;</td>";
      $_prs = explode('~', $prs);
      foreach ($_prs as &$v) {
        $v = trim($v);
        $_v = str_replace(' ', "<br />", $v);
        $hdrprs .= "<th class=ttl>$_v</th>";
      }
    }
  }
  // Data
  $whr = array();
  if (!empty($_SESSION['prid'])) $whr[] = "m.ProgramID='$_SESSION[prid]'";
  if (!empty($_SESSION['prodi'])) $whr[] = "m.ProdiID='$_SESSION[prodi]'";
  if (!empty($_SESSION['BeasiswaID'])) $whr[] = "bm.BeasiswaID='$_SESSION[BeasiswaID]'";
  $_whr = (empty($whr))? '' : ' and '. implode(' and ', $whr);
  $s = "select bm.BeasiswaMhswID, bm.Proses, bm.BeasiswaID, bm.Prasyarat, bm.NA,
    m.MhswID, m.Nama, m.ProdiID, format(bm.Besar, 0) as BSR, 
    bm.IPS, bm.IPK, bm.Hutang
    from beasiswamhsw bm
      left outer join mhsw m on bm.MhswID=m.MhswID
    where bm.TahunID='$_SESSION[tahun]' and bm.KodeID='$_SESSION[KodeID]'
      $_whr
    order by m.ProdiID, m.Nama";
  $r = _query($s); $n = 0; $prd = '';
  // Tampilkan
  $btn = (_num_rows($r)>0)? "<input type=submit name='Proses' value='Proses'>" : '';
  $hdrbn = '';
  for ($i = 0; $i < sizeof($arrNama); $i++) $hdrbn .= "<th class=ttl>". $arrNama[$i] ."</th>";
  $hdr = "<tr><th class=ttl>#</th>
    <th class=ttl>NPM</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Beasiswa</th>
    <th class=ttl>IPS</th>
    <th class=ttl>IPK</th>
    <th class=ttl>Hutang<br />Smt Lalu</th>
    <th class=ttl>Wanprestasi</th>
    <th class=ttl>Total<br />Permohonan</th>
    <th class=ttl>Proses</th>
    <th class=ttl>Hapus</th>
    <th class=ttl>Edit</th>
    $hdrbn
    $hdrprs
    </tr>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  while ($w = _fetch_array($r)) {
    if ($prd != $w['ProdiID']) {
      $prd = $w['ProdiID'];
      $nmprd = GetaField('prodi', 'ProdiID', $prd, 'Nama');
      echo "<tr><td class=ul colspan=6><b>$w[ProdiID] - $nmprd</b></td></tr>" . $hdr;
    }
    if ($w['NA'] == 'Y') {
      $hps = "&nbsp;";
      $prs = "&times;";
      $edt = "&nbsp;";
    }
    else {
      if ($w['Proses'] == 'Y') {
        $hps = "&nbsp;";
        $prs = "<img src='img/Y.gif'>";
        $edt = "&nbsp;";
      }
      else {
        $hps = "<a href='?mnux=beasiswa&$pref=$token&sub=HpsBeaMhsw&id=$w[BeasiswaMhswID]'><img src='img/del.gif'></a>";
        $prs = "<input type=checkbox name='PRCID' value='$w[BeasiswaMhswID]' onChange=\"location='?mnux=beasiswa&$pref=$token&sub=BeaPrc&BMID=$w[BeasiswaMhswID]#$w[BeasiswaMhswID]'\">";
        $edt = "<a href='?mnux=beasiswa&$pref=$token&sub=EditDetailBeasiswaMhsw&beaBMID=$w[BeasiswaMhswID]'><img src='img/edit.png'></a>";
      }
    }
    $n++;
    $DetailBeasiswa = AmbilDetailBeasiswa($arrBN, $w['BeasiswaMhswID']);
    $Prasyarat = AmbilPrasyaratBeasiswa($_prs, $w);
    $c = ($w['NA'] == 'Y')? "class=nac" : "class=ul";
    $Hutang = number_format($w['Hutang']);
    $wanp = GetaField('prestasi',"JenisPrestasi = -1 and MhswID", $w['MhswID'],'Judul');
    $_wanp = (empty($wanp)) ? "&nbsp;" : $wanp;
    echo "<tr><td class=inp><a name='$w[BeasiswaMhswID]'>$n</a></td>
    <td $c>$w[MhswID]</td>
    <td $c>$w[Nama]</td>
    <td $c>$w[BeasiswaID]</td>
    <td $c align=right>$w[IPS]</td>
    <td $c align=right>$w[IPK]</td>
    <td $c align=right>$Hutang</td>
    <td $c align=center>$_wanp</td>
    <td $c align=right>$w[BSR]</td>
    <td $c align=center>$prs</td>
    <td $c align=center>$hps</td>
    <td $c align=center>$edt</td>
    $DetailBeasiswa
    $Prasyarat
    </tr>";
  }
  echo "<tr><td colspan=5></td><td class=ul></td></tr>
    </table></p>";
}
function AmbilPrasyaratBeasiswa($arr, $w) {
  global $pref, $token;
  $ret = "";
  if (!empty($arr)) {
    $jml = sizeof($arr);
    $ret .= "<form action='beasiswa.prasyarat.php' method=POST target=_blank>
    <input type=hidden name='BMID' value='$w[BeasiswaMhswID]'>
    <input type=hidden name='JML' value='$jml'>
    <td class=ul>&raquo;</td>";
    foreach ($arr as $i=>$v) {
      $idx = $v[$i];
      $ada = $w['Prasyarat'][$i];
      $_y = ($ada == 'Y')? 'selected' : '';
      $_n = ($ada == 'Y')? '' : 'selected';
      $sta = "<select name='$i'><option value='N' $_n>N</option>
        <option value='Y' $_y>Y</option></select>";
      $sta1 = ($ada == 'Y')? 
        "<a href='?mnux=beasiswa&$pref=$token&sub=Prsyrt&s=N&BMID=$w[BeasiswaMhswID]&n=$i'><img src='img/Y.gif'></a>" : 
        "<a href='?mnux=beasiswa&$pref=$token&sub=Prsyrt&s=Y&BMID=$w[BeasiswaMhswID]&n=$i'><img src='img/N.gif'></a>";
      $ret .= "<td class=ul align=center>$sta</td>";
    }
    $ret .= "<td class=ul><input type=submit name='Simpan' value='Simpan'></td></form>";
  }
  return $ret;
}
function Prsyrt_xxx() {
  $BMID = $_REQUEST['BMID'];
  $n = $_REQUEST['n']+0;
  $s = $_REQUEST['s'];
  $BM = GetFields('beasiswamhsw', 'BeasiswaMhswID', $BMID, '*');
  $str = '';
  $m = (strlen($BM['Prasyarat']) < $n)? $n : strlen($BM['Prasyarat']);
  for ($i = 0; $i <= $m; $i++) {
    if ($i == $n) $str .= $s;
    else {
      $_status = $BM['Prasyarat'][$i];
      $str .= (empty($_status))? 'N' : $_status;
      //echo 
    }
  }
  // simpan
  $s = "update beasiswamhsw set Prasyarat='$str' where BeasiswaMhswID=$BMID";
  $r = _query($s);
  DftrBea();
}
function AmbilDetailBeasiswa($arrBN, $BMID) {
  $det = '';
  $arr = array();
  if (!empty($arrBN)) {
    $in = implode(',', $arrBN);
    $s = "select BIPOTNamaID, BeasiswaMhswDetailID, sum(Beasiswa) as JML
      from beasiswamhswdetail
      where BeasiswaMhswID=$BMID
      group by BIPOTNamaID";
    $r = _query($s);
    while ($w = _fetch_array($r)) {
      $key = array_search($w['BIPOTNamaID'], $arrBN);
      $arr[$key] = $w['JML'];
    }
    for ($i = 0; $i < sizeof($arrBN); $i++)
      $det .= "<td class=ul align=right>" . number_format($arr[$i]) . "</td>";
  }
  return $det;
}
function JSCetakDaftarPemohon() {
?>

  <SCRIPT>
  <!--
  function CetakDaftarPemohon(frm) {
    var v = frm.BeasiswaID.value;
    if (v == "") alert("Tentukan jenis beasiswa dulu sebelum dicetak.");
    else CetakDaftarPemohonDong();
  }
  function CetakDaftarPemohonDong() {
    lnk = "beasiswa.dftr.cetak.php";
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  //-->
  </SCRIPT>
<?php
}
function TampilkanTambahBeasiswaMhsw() {
  global $pref, $token;
  $optbea = GetOption2('beasiswa', "Nama", "Nama", $_SESSION['BeasiswaID'],
    "KodeID='$_SESSION[KodeID]'", 'BeasiswaID');
  JSCetakDaftarPemohon();
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='beaid' method=POST>
  <input type=hidden name='mnux' value='beasiswa'>
  <input type=hidden name='$pref' value='$token'>
  <tr><td class=inp>Beasiswa</td><td class=ul colspan=3><select name='BeasiswaID' onChange='this.form.submit()'>$optbea</select>
  <input type=button name='CetakDaftarPemohon1' value='Cetak Daftar Pemohon' onClick=\"CetakDaftarPemohon(beaid);\">
  <input type=button name='RekapBeasiswa' value='Rekap Beasiswa' onClick=\"window.open('beasiswa.rekap.php');\"></td></tr>
  </form>
  
  <form action='?' method=beaidadd' method=POST>
  <input type=hidden name='mnux' value='beasiswa'>
  <input type=hidden name='$pref' value='$token'>
  <input type=hidden name='sub' value='TambahkanBeasiswaMahasiswa'>
  <tr><td class=inp>Tambahkan NPM</td><td class=ul><input type=text name='beaMhswID' value='$_SESSION[beaMhswID]' size=20 maxlength=50>
    <input type=submit name='Tambah' value='Tambahkan'></td></tr>
  </form>
  </table></p>";
}
function TambahkanBeasiswaMahasiswa() {
  $MhswID = $_REQUEST['beaMhswID'];
  $mhsw = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join bipot bpt on m.BIPOTID=bpt.BIPOTID", 
    'm.MhswID', $MhswID, 
    "m.*, prg.Nama as PRG, prd.Nama as PRD, bpt.Nama as BPT");
  $sdh = GetFields('beasiswamhsw', "TahunID='$_SESSION[tahun]' and MhswID",
    $MhswID, "BeasiswaMhswID, BeasiswaID");
  // Apakah sudah terdaftar?
  $beas = GetFields('beasiswa', "BeasiswaID", $_SESSION['BeasiswaID'], "*");
  if (!empty($sdh)) {
    echo ErrorMsg("Tidak Dapat Ditambahkan",
      "Mahasiswa <b>$mhsw[Nama] ($MhswID)</b> tidak dapat ditambahkan karena sudah terdaftar di beasiswa:
      <font size=+1>$beas[Nama]</font>.
      <hr size=1 color=silver>
      Pilihan: <input type=submit name='Kembali' value='Kembali' onClick=\"location='?mnux=beasiswa'\">");
  }
  else {
    // Apakah IPS sudah memenuhi?
    $s_khs = "select * from khs where MhswID='$mhsw[MhswID]' and TahunID < '$_SESSION[tahun]' order by TahunID desc limit 1";
    $r_khs = _query($s_khs);
    $w_khs = _fetch_array($r_khs);
    if ($w_khs['IPS'] >= $beas['IPSMin']) {
      $AccountHutangSmtLalu = 30;
      $Htg = GetaField("bipotmhsw", "BIPOTNamaID=$AccountHutangSmtLalu and MhswID='$MhswID' and TahunID", $_SESSION['tahun'], "sum(Jumlah * Besar)")+0;
      $s = "insert into beasiswamhsw (TahunID, KodeID, BeasiswaID, IPS, IPK, Hutang, MhswID, Besar, LoginBuat, TanggalBuat)
        values ('$_SESSION[tahun]', '$_SESSION[KodeID]', '$_SESSION[BeasiswaID]', '$w_khs[IPS]', '$mhsw[IPK]', $Htg, '$MhswID', 0, '$_SESSION[_Login]', now())";
      $r = _query($s);
      //DftrBea();
      $_REQUEST['beaBMID'] = GetLastID();
      EditDetailBeasiswaMhsw();
    }
    else echo ErrorMsg("Tidak Dapat Ditambahkan",
      "Mahasiswa <b>$mhsw[Nama] ($MhswID)</b> tidak dapat ditambahkan karena IPS hanya <b>$w_khs[IPS]</b>, <br />
      Sedangkan beasiswa <b>$beas[Nama]</b> mensyaratkan IPS minimal: <b>$beas[IPSMin]</b>
      <hr size=1 color=silver>
      Pilihan: <input type=submit name='Kembali' value='Kembali' onClick=\"location='?mnux=beasiswa'\">");
  }
}
function BeaPrc() {
  $BMID = $_REQUEST['BMID'];
  $beas = GetFields('beasiswamhsw', 'BeasiswaMhswID', $BMID, '*');
  if ($beas['Proses'] == 'N') {
    $b0 = GetFields('beasiswa', "BeasiswaID", $beas['BeasiswaID'], "*");
    $khsid = GetaField('khs', "TahunID='$beas[TahunID]' and MhswID", $beas['MhswID'], 'KHSID');
    // buat akun potongan
    $sdh = GetaField("bipotmhsw", "TahunID='$beas[TahunID]' and BIPOTNamaID", $b0['BIPOTNamaID'], "BIPOTMhswID");
    if (empty($sdh)) {
      $sb = "insert into bipotmhsw (MhswID, TahunID, BIPOTNamaID, Nama, TrxID,
        Jumlah, Besar, Dibayar, Catatan,
        LoginBuat, TanggalBuat)
        values ('$beas[MhswID]', '$beas[TahunID]', '$b0[BIPOTNamaID]', '$b0[Nama]', -1,
        1, '$beas[Besar]', '$beas[Besar]', '$b0[Nama]',
        '$_SESSION[_Login]', now())";
      $rb = _query($sb);
    }
    else {
      $sb = "update bipotmhsw set Besar='$beas[Besar]', LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
        where BIPOTMhswID=$sdh";
      $rb = _query($sb);
    }
    //echo "<pre>$sb</pre>";
    // update data
    include_once "mhswkeu.lib.php";
    HitungBiayaBayarMhsw($beas['MhswID'], $khsid);
    // set flag bahwa sudah diproses
    $s = "update beasiswamhsw set Proses='Y' where BeasiswaMhswID='$BMID' ";
    $r = _query($s);
  } 
  DftrBea();
}
function BeaPrc_xx() {
  $PRCID = array();
  $PRCID = $_REQUEST['PRCID'];
  for ($i=0; $i < sizeof($PRCID); $i++) {
    $id = $PRCID[$i];
    $beas = GetFields('beasiswamhsw', "BeasiswaMhswID", $id, "*");
    $b0 = GetFields('beasiswa', "BeasiswaID", $beas['BeasiswaID'], "*");
    $khsid = GetaField('khs', "TahunID='$beas[TahunID]' and MhswID", $beas['MhswID'], 'KHSID');
    // buat akun potongan
    $sdh = GetaField("bipotmhsw", "TahunID='$beas[TahunID]' and BIPOTNamaID", $b0['BIPOTNamaID'], "BIPOTMhswID");
    if (empty($sdh)) {
      $sb = "insert into bipotmhsw (MhswID, TahunID, BIPOTNamaID, TrxID,
        Jumlah, Besar, Dibayar, Catatan,
        LoginBuat, TanggalBuat)
        values ('$beas[MhswID]', '$beas[TahunID]', '$b0[BIPOTNamaID]', -1,
        1, '$beas[Besar]', '$beas[Besar]', '$b0[Nama]',
        '$_SESSION[_Login]', now())";
      //$rb = _query($sb);
      // update data
      include_once "mhswkeu.lib.php";
      HitungBiayaBayarMhsw($beas['MhswID'], $khsid);
      // set flag bahwa sudah diproses
      $s = "update beasiswamhsw set Proses='Y' where BeasiswaMhswID='$id' ";
      //$r = _query($s);
    } else echo $sdh . "<br />";
  }
  DftrBea();
}
function HpsBeaMhsw() {
  $id = $_REQUEST['id'];
  $beas = GetFields("beasiswamhsw bm
    left outer join mhsw m on bm.MhswID=m.MhswID
    left outer join beasiswa b on bm.BeasiswaID=b.BeasiswaID", 
    "bm.BeasiswaMhswID", $id, 
    "bm.*, b.Nama as BEAS, m.Nama as NamaMhsw");
  $BSR = number_format($beas['Besar']);
  echo Konfirmasi("Konfirmasi Hapus",
    "<p>Benar Anda akan menghapus data beasiswa ini?</p>
    <p><table class=box cellspacing=1 cellpadding=4>
      <tr><td class=inp>Mahasiswa</td><td class=ul>$beas[NamaMhsw] ($beas[MhswID])</td></tr>
      <tr><td class=inp>Beasiswa</td><td class=ul>$beas[BEAS]</td></tr>
      <tr><td class=inp>Potongan</td><td class=ul>Rp. $BSR</td></tr>
    </table><p>
    <hr size=1 color=silver>
    Pilihan: <input type=button name='Hapus' value='Hapus' onClick=\"location='?mnux=beasiswa&$pref=$token&sub=HpsBeaMhswOke&id=$id'\">
      <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=beasiswa'\">");
}
function HpsBeaMhswOke() {
  $id = $_REQUEST['id']+0;
  // Hapus header
  $s = "delete from beasiswamhsw where BeasiswaMhswID='$id' ";
  $r = _query($s);
  // Hapus detail
  $s = "delete from beasiswamhswdetail where BeasiswaMhswID='$id' ";
  $r = _query($s);
  DftrBea();
}
function EditDetailBeasiswaMhsw() {
  global $pref, $token;
  $beaBMID = $_REQUEST['beaBMID'];
  $dat = GetFields("beasiswamhsw bm
    left outer join mhsw m on bm.MhswID=m.MhswID
    left outer join prodi p on m.ProdiID=p.ProdiID
    left outer join fakultas f on p.FakultasID=p.FakultasID
    left outer join dosen d on m.PenasehatAkademik=d.Login",
    "bm.BeasiswaMhswID", $beaBMID,
    "bm.*, bm.TahunID, m.Nama, m.TotalSKS, m.IPK, m.TahunID as Angkatan, p.Nama as PRD, f.Nama as FAK,
    m.Telepon, m.Handphone, concat(d.Nama, ', ', d.Gelar) as PA");
  // Tampilkan header beasiswa
  $kembali = "<input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=beasiswa'\">";
  echo "<p><table class=box cellspacing=1 cellpadding=2>
    <tr>
      <td class=inp>NPM</td><td class=ul>$dat[MhswID]</td>
      <td class=inp>Nama Pemohon Beasiswa</td>
      <td class=ul>$dat[Nama]</td>
      <td class=inp>Fakultas/Prodi</td>
      <td class=ul>$dat[FAK]/$dat[PRD]</td></tr>
    <tr>
      <td class=inp>Angkatan</td> <td class=ul>$dat[Angkatan]</td>
      <td class=inp>IPK</td> <td class=ul>$dat[IPK]</td>
      <td class=inp>Total SKS</td> <td class=ul>$dat[TotalSKS]</td></tr>
    <tr>
      <td class=inp>Telp/HP</td> <td class=ul>$dat[Telepon] / $dat[Handphone]</td>
      <td class=inp>Penasehat Akademik</td> <td class=ul>$dat[PA]</td>
      <td class=ul colspan=2>$kembali</td></tr>
    </table></p>";
  
  // Detail Beasiswa Mhsw
  $s = "select bn.BIPOTNamaID as BNID, bn.Nama, bmd.* 
    from bipotnama bn
      left outer join beasiswamhswdetail bmd on bn.BIPOTNamaID=bmd.BIPOTNamaID and bmd.BeasiswaMhswID='$beaBMID'
    where bn.DipotongBeasiswa = 'Y'
    order by bn.Nama";
  $r = _query($s); $n = 0;
  echo "<p><font size=+1>Detail Pemohonan Beasiswa</font></p>";
  echo "<p><table class=box cellspacing=1 cellpadding=2>
    <tr><th class=ttl>#</th>
    <th class=ttl>Jenis Potongan</th>
    <th class=ttl>Biaya Mhsw</th>
    <th class=ttl>Jumlah Diajukan</th>
    <th class=ttl>Jumlah Disetujui</th>
    </tr>";
  echo "<form action='?' method=POST>
    <input type=hidden name='beaBMID' value='$beaBMID'>
    <input type=hidden name='mnux' value='beasiswa'>
    <input type=hidden name='$pref' value='$token'>
    <input type=hidden name='sub' value='SimpanDetailBeasiswaMhsw'>
    ";
  while ($w = _fetch_array($r)) {
    $n++;
    $w['Jumlah'] = (empty($w['BeasiswaMhswDetailID']))? 
      GetaField('bipotmhsw', "MhswID='$dat[MhswID]' and BIPOTNamaID=$w[BNID] and TahunID",
      $dat['TahunID'], "sum(Jumlah * Besar)") : $w['Jumlah'];
    $w['Jumlah'] = number_format($w['Jumlah']);
    $w['Beasiswa'] = $w['Beasiswa']+0;
    $w['Disetujui'] = number_format($w['Disetujui']);
    echo "<tr><input type=hidden name='beaBMID_$w[BNID]' value='$w[BIPOTMhswID]'>
      <input type=hidden name='BNID[]' value='$w[BNID]'>
      <input type=hidden name='beaBMDID_$w[BNID]' value='$w[BeasiswaMhswDetailID]'>
      <td class=inp>$n</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul align=right>$w[Jumlah]</td>
      <td class=ul><input type=text name='Beasiswa_$w[BNID]' value='$w[Beasiswa]' style='text-align:right' size=15 maxlength=15></td>
      <td class=ul align=right>$w[Disetujui]</td>
      </tr>";
  }
  echo "<tr><td class=ul colspan=5><input type=submit name='Simpan' value='Simpan'></td></tr>";
  echo "</form></table></p>";
}
function SimpanDetailBeasiswaMhsw() {
  $BNID = array();
  $BNID = $_REQUEST['BNID'];
  $beaBMID = $_REQUEST['beaBMID'];
  $bm = GetFields('beasiswamhsw', 'BeasiswaMhswID', $beaBMID, '*');
  //echo "<font size=+1>$bm[MhswID]</font> &raquo; $bm[TahunID]<br />";
  for ($i =0; $i < sizeof($BNID); $i++) {
    $_bnid = $BNID[$i];
    $beaBMDID = $_REQUEST['beaBMDID_'.$BNID[$i]];
    $Beasiswa = $_REQUEST['Beasiswa_'.$BNID[$i]]+0;
    // Jika sdh ada data, maka edit
    if (!empty($beaBMDID)) {
      $s = "update beasiswamhswdetail set Beasiswa=$Beasiswa, LoginEdit='$_SESSION[_Login]',
        TanggalEdit=now()
        where BeasiswaMhswDetailID=$beaBMDID";
      $r = _query($s);
    }
    // Jika belum ada data, maka insert
    else {
      if ($Beasiswa > 0) {
        $jml = GetaField('bipotmhsw', "MhswID='$bm[MhswID]' and TahunID='$bm[TahunID]' and BIPOTNamaID",
          $_bnid, "sum(Jumlah * Besar)")+0;
        $s = "insert into beasiswamhswdetail
          (BeasiswaMhswID, MhswID, BIPOTNamaID, Jumlah, Beasiswa,
          LoginBuat, TanggalBuat)
          values ($beaBMID, '$bm[MhswID]', $_bnid, $jml, $Beasiswa,
          '$_SESSION[_Login]', now())";
        $r = _query($s);
      }
    }
    //echo "$i. " . $BNID[$i] . " &raquo; $beaBMDID <br />";
  }
  UpdateBesarBeasiswaMhsw($beaBMID);
  DftrBea();
}
function UpdateBesarBeasiswaMhsw($BMID) {
  $jml = GetaField('beasiswamhswdetail', 'BeasiswaMhswID', $BMID, "sum(Beasiswa)")+0;
  $s = "update beasiswamhsw set Besar=$jml where BeasiswaMhswID=$BMID";
  $r = _query($s);
}

// *** Parameters ***
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$tahun = GetSetVar('tahun');
$BeasiswaID = GetSetVar('BeasiswaID');

// *** Main ***
$NTahun = NamaTahun($tahun);
TampilkanJudul("Daftar Penerima Beasiswa $NTahun");
TampilkanTahunProdiProgram('beasiswa', '');
?>
