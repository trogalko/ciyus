<?php
// Author: Emanuel Setio Dewo
// 14 March 2006
// www.sisfokampus.net

// *** Functions ***
function HeaderKRSMhsw($mhsw, $datatahun, $khs) {
  global $_LevelPaketKRS, $_LevelMundurKRS;

  $KRSMulai = FormatTanggal($datatahun['TglKRSMulai']);
  $KRSSelesai = FormatTanggal($datatahun['TglKRSSelesai']);
  $UbahKRSMulai = FormatTanggal($datatahun['TglUbahKRSMulai']);
  $UbahKRSSelesai = FormatTanggal($datatahun['TglUbahKRSSelesai']);
  $Cuti = FormatTanggal($datatahun['TglCuti']);
  $Mundur = FormatTanggal($datatahun['TglMundur']);
  $KuliahMulai = FormatTanggal($datatahun['TglKuliahMulai']);
  $KuliahSelesai = FormatTanggal($datatahun['TglKuliahSelesai']);
  $BayarMulai = FormatTanggal($datatahun['TglBayarMulai']);
  $BayarSelesai = FormatTanggal($datatahun['TglBayarSelesai']);
  $PA = GetaField('dosen', "Login", 
    $mhsw['PenasehatAkademik'], "concat(Nama, ', ', Gelar)");
  
  // Dapat mengakses Paket KRS?
  if (strpos($_LevelPaketKRS, '.'.$_SESSION['_LevelID'].'.') === false) {
    $btn = '';
    $bnt1 = '';
  }
  else {
    $btn = "<input type=button name='Paket' value='Paket KRS' onClick=\"location='?mnux=krs&gos=PaketKRS'\">";
    $btn1 = "<input type=button name='Cetak' value='Cetak KRS' onClick=\"location='krs.cetak.php?khsid=$khs[KHSID]&prn=1'\">";
  }
	
	//if ($datatahun['TglKRSSelesai'] < date("Y-m-d")) {
		//''if ($_SESSION['_LevelID'] != 1) {
			//''$btn1 ='';
		/*} else*/ //$btn1 = "<input type=button name='Cetak' value='Cetak KRS' onClick=\"location='krs.cetak.php?khsid=$khs[KHSID]&prn=1'\">";
	//}
	
	if ((($datatahun['TglKRSMulai'] <= date('Y-m-d')) && (date('Y-m-d') <= $datatahun['TglKRSSelesai'])) ||
     (($datatahun['TglUbahKRSMulai'] <= date('Y-m-d')) && (date('Y-m-d') <= $datatahun['TglUbahKRSSelesai'])) || 
	   (($datatahun['TglKRSOnlineMulai'] <= date('Y-m-d')) && (date('Y-m-d') <= $datatahun['TglKRSOnlineSelesai']) || ($_SESSION['_LevelID'] == 41)) || 
	   ($_SESSION['_LevelID'] == 1)) {
	     $btn1 = "<input type=button name='Cetak' value='Cetak KRS' onClick=\"location='krs.cetak.php?khsid=$khs[KHSID]&prn=1'\">";
  } else $btn1 = '';
	
  $SesiLalu = ($khs['Sesi'] <= 1)? 1 : $khs['Sesi']-1;
  $khslalu = GetFields('khs', "MhswID='$mhsw[MhswID]' and Sesi", $SesiLalu, "*");
  $ipslalu = number_format($khslalu['IPS'], 2);
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp2><b>Mhsw</b></td>
    <td class=inp>NPM</td><td class=ul>$mhsw[MhswID]</td>
    <td class=inp>Nama</td><td class=ul>$mhsw[Nama]</td>
    <td class=inp>Program</td><td class=ul>$mhsw[PRG] ($mhsw[ProgramID])</td>
    <td class=inp>Prodi</td><td class=ul>$mhsw[PRD] ($mhsw[ProdiID])</td>
  </tr>
  <tr><td class=inp2><b>Sesi</b></td>
    <td class=inp>Tahun Akd</td><td class=ul>$khs[TahunID] Smt: $khs[Sesi]</td>
    <td class=inp>Status</td><td class=ul>$khs[SM] ($khs[StatusMhswID])</td>
    <td class=inp>Tot SKS</td><td class=ul>$khs[TotalSKS] <abbr title='dari maksimal'>dr</abbr> $khs[MaxSKS] SKS, $khs[JumlahMK] MK</td>
    <td class=inp>IPS Lalu</td><td class=ul>$ipslalu, IPK: $mhsw[IPK]</td>
  </tr>
  <tr><td class=inp2><b>Batas</b></td>
    <td class=inp>Isi KRS</td><td class=ul>$KRSMulai~$KRSSelesai</td>
    <td class=inp>Ubah KRS</td><td class=ul>$UbahKRSMulai~$UbahKRSSelesai</td>
    <td class=inp>Pengajuan Cuti & Mundur</td><td class=ul>$Cuti & $Mundur</td>
    <td class=inp>Kuliah</td><td class=ul>$KuliahMulai~$KuliahSelesai</td></tr>
  </tr>
  <tr><td class=inp2><b>Pilihan</b></td>
    <td class=ul colspan=2 nowrap>
    $btn $btn1
    <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=krs'\">
    </td>
    <td class=inp>Batas Studi</td><td class=ul>$mhsw[BatasStudi]&nbsp;</td>
    <td class=inp>Pembayaran</td><td class=ul>$BayarMulai~$BayarSelesai</td>
    <td class=inp>Penasehat</td><td class=ul>$PA&nbsp;</td>
    </tr>
  </table>";
}
function TuliskanScriptLihatIsiPaket() {
  echo <<<END
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function LihatIsiPaket(frm) {
    if (frm.MKPaketID.value == '') alert("Anda harus memilih salah satu paket.");
    else {
      lnk = "lihatmkpaket.php?MKPaketID="+frm.MKPaketID.value+"&TahunID="+frm.tahun.value+"&ProgramID="+frm.ProgramID.value+"&ProdiID="+frm.ProdiID.value;
      win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
      win2.creator = self;
    }
  }
  //-->
  </SCRIPT>
END;
}
function PaketKRS($mhsw, $datatahun, $khs) {
  // Administrasi paket matakuliah
  TuliskanScriptLihatIsiPaket();
  $optpkt = GetOption2("mkpaket mp
    left outer join kurikulum kur on mp.KurikulumID=kur.KurikulumID", 
    "concat(mp.Nama, ' (', kur.Nama, ')')", "mp.KurikulumID desc, mp.MKPaketID",
    $_SESSION['MKPaketID'], "mp.KodeID='$_SESSION[KodeID]' and mp.ProdiID='$mhsw[ProdiID]'", "MKPaketID", 1);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=ul colspan=5><b>Daftar Paket KRS</td></tr>
  <tr>
    <form action='?' name='PKT' method=POST>
    <td class=inp>Nama Paket :</td>
    <input type=hidden name='mnux' value='krs'>
    <input type=hidden name='gos' value=''>
    <input type=hidden name='slnt' value='krs.lib'>
    <input type=hidden name='slntx' value='PaketKRSAmbil'>
    <input type=hidden name='mhswid' value='$mhsw[MhswID]'>
    <input type=hidden name='tahun' value='$khs[TahunID]'>
    <input type=hidden name='khsid' value='$khs[KHSID]'>
    <input type=hidden name='ProdiID' value='$datatahun[ProdiID]'>
    <input type=hidden name='ProgramID' value='$datatahun[ProgramID]'>
    <td class=ul><select name='MKPaketID'>$optpkt</select></td>
    <td class=inp>Kelas :</td><td class=ul><input type=text name='NamaKelas' value='$_SESSION[NamaKelas]' size=5 maxlength=10></td>
    <td class=ul>
    <input type=submit name='Ambil' value='Ambil Paket'>
    <input type=button name='Lihat' value='Lihat Isi Paket' onClick=\"javascript:LihatIsiPaket(PKT)\">
  </td></tr></form>
  
  <tr><td class=inp>Catatan</td><td class=ul colspan=4><ul>
    <li>Cek apakah matakuliah dalam paket telah terjadwal? Klik tombol <font color=navy><b>[Lihat Isi Paket]</b></font>.</li>
    <li>Matakuliah dalam paket yang tidak terjadwal tidak akan dimasukkan ke KRS.</li>
    <li>Jika mahasiswa telah mengambil matakuliah tersebut, maka matakuliah paket tidak ditambahkan.</li>
  </ul></td></tr>
  </table></p>";
  //DftrKRS($mhsw, $datatahun, $khs);
}
// Simpan paket kRS
function PaketKRSAmbil() {
  $MKPaketID = $_REQUEST['MKPaketID'];
  $NamaKelas = $_REQUEST['NamaKelas'];
  $mhswid = $_REQUEST['mhswid'];
  $mhsw = GetFields('mhsw', 'MhswID', $mhswid, "MhswID, Nama, ProdiID, ProgramID, StatusAwalID, StatusMhswID");
  $tahun = $_REQUEST['tahun'];
  $khsid = $_REQUEST['khsid'];
  // Ambil isi paket
  $s = "select mpi.*, mk.MKKode, mk.Nama 
    from mkpaketisi mpi
      left outer join mk mk on mpi.MKID=mk.MKID
    where mpi.MKPaketID='$MKPaketID' ";
  $r = _query($s);
  $psn = '';
  while ($w = _fetch_array($r)) {
    // Apakah sudah diambil?
    $sdh = GetFields("krstemp k left outer join jadwal j on k.JadwalID=j.JadwalID", 
      "k.KHSID='$khsid' and k.MhswID='$mhswid' and j.MKKode", $w['MKKode'], '*');
    if (empty($sdh)) {
      $psn .= TambahkanPaketKRSMhsw($w, $MKPaketID, $tahun, $NamaKelas, $mhsw, $khsid);
    }
    else $psn .= "<li>Matakuliah <b>$w[MKKode] - $w[Nama]</b> telah diambil mahasiswa.</li>";
  }
  UpdateJumlahKRSMhsw($mhswid, $khsid);
  if (!empty($psn)) {
    $pesankesalahan = "<h3>Berikut adalah pesan kesalahan yang terjadi:</h3><hr size=1 color=red>";
    $pesankesalahan .= "<ul>".$psn."</ul>";
    $pesankesalahan .= "<hr size=1 color=red><input type=button name='Tutup' value='Tutup' onClick=\"window.close()\">";
    $filesalah = "tmp/$_SESSION[_Login].KRS.html";
    $hndsalah = fopen($filesalah, 'w');
    fwrite($hndsalah, $pesankesalahan);
    fclose($hndsalah);
    PopupMsg($filesalah);
  }
}
function TambahkanPaketKRSMhsw($PaketIsi, $MKPaketID, $tahun, $NamaKelas, $mhsw, $khsid) {
  // Daftar matakuliah
  $s = "select j.*
    from jadwal j
    where INSTR(j.ProgramID, '.$mhsw[ProgramID].') >0
      and INSTR(j.ProdiID, '.$mhsw[ProdiID].') >0
      and TahunID='$tahun'
      and j.MKKode='$PaketIsi[MKKode]'
      order by j.NamaKelas, j.JenisJadwalID DESC";
   $r = _query($s);
   $JmlKelas = _num_rows($r);
   $psn = '';
   $khs = GetFields('khs', 'KHSID', $khsid, '*');
   if ($JmlKelas == 0) {
     $jdwl = GetFields('mk', 'MKID', $PaketIsi['MKID'], 'MKKode, Nama');
     $psn .= "<li>Matakuliah <b>$jdwl[MKKode] - $jdwl[Nama]</b> belum dijadwalkan. Matakuliah tidak ditambahkan pada KRS.</li>";
   }
   // Jika hanya ada 1 kelas, maka langsung ambil saja
   else if (_num_rows($r) == 1) {
     $jdwl = _fetch_array($r);
     if ($jdwl['JumlahMhswKRS']+0 >= $jdwl['Kapasitas']+0)  $psn .= "<li>Kapasitas untuk matakuliah <b>$jdwl[MKKode] - $jdwl[Nama]</b> sudah penuh.</li>";
     else SimpanKRSMhsw($mhsw, $tahun, $khs, $jdwl);
   }
   // Jika ada banyak kelas, maka cari kelas yg sesuai. Jika tidak ada, maka ambil default
   else {
     $n = 0; $KelasKetemu = false; $KelasResKetemu = false;
     while ($w = _fetch_array($r)) {
       if ($n == 0) $KelasDef = $w;
       // Jika kelas Kuliah belum ditemukan
       if ($w['JenisJadwalID'] == 'K'){
          if (!$KelasKetemu) {
            if ($NamaKelas == $w['NamaKelas']) {
              $KelasKetemu = true;
              if ($w['JumlahMhswKRS']+0 >= $w['Kapasitas']+0)  $psn .= "<li>Kapasitas untuk matakuliah <b>$w[MKKode] - $w[Nama]</b> sudah penuh.</li>";
              else SimpanKRSMhsw($mhsw, $tahun, $khs, $w);
            } 
          }
        } else {
          if (!$KelasResKetemu) {
            if ($NamaKelas == $w['NamaKelas']) {
              $KelasResKetemu = true;
              if ($w['JumlahMhswKRS']+0 >= $w['Kapasitas']+0)  $psn .= "<li>Kapasitas untuk matakuliah <b>$w[MKKode] - $w[Nama]</b> sudah penuh.</li>";
              else SimpanKRSMhsw($mhsw, $tahun, $khs, $w);
            } 
          }
        }
     } 
     // Jika tidak ketemu
     if (!$KelasKetemu) {
       $psn .= "<li>Kelas tidak ditemukan: $PaketIsi[MKKode]. Diambil kelas yg ada, yaitu: $KelasDef[MKKode] - $KelasDef[Nama] $KelasDef[NamaKelas]).</li>";
     }
   } // end if-else
   return $psn;
}

// ****************
// *** KRS MHSW ***
// ****************

function DftrKRS($mhsw, $datatahun, $khs) {
  global $_LevelPaketKRS, $_LevelMundurKRS;
  $s = "select k.*, j.*, jj.Nama as JJ,
    sk.Nama as SK, sk.Ikut, sk.Hitung, k.SKS as SKSnya,
    time_format(j.JamMulai, '%H:%i') as JM,
    time_format(j.JamSelesai, '%H:%i') as JS
    from krstemp k
      left outer join jadwal j on k.JadwalID=j.JadwalID
      left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
      left outer join statuskrs sk on k.StatusKRSID=sk.StatusKRSID
    where k.KHSID='$khs[KHSID]'
      and k.NA='N'
    order by j.HariID, j.JamMulai, j.MKKode ";
  $r = _query($s);
  
  if ((($datatahun['TglKRSMulai'] <= date('Y-m-d')) && (date('Y-m-d') <= $datatahun['TglKRSSelesai'])) ||
      (($datatahun['TglUbahKRSMulai'] <= date('Y-m-d')) && (date('Y-m-d') <= $datatahun['TglUbahKRSSelesai'])) || 
	  (($datatahun['TglKRSOnlineMulai'] <= date('Y-m-d')) && (date('Y-m-d') <= $datatahun['TglKRSOnlineSelesai']) || ($_SESSION['_LevelID'] == 41)) || 
	  ($_SESSION['_LevelID'] == 1)) {
	   if ($khs['Blok'] == 'N') echo "<p><a href='?mnux=krs&gos=KRSAdd'><img src='img/check.gif'> Tambah KRS</a></p>"; 
     else echo ErrorMsg("KRS Blok",
      "Anda tidak diperkenankan mengisi KRS karena Anda tercatat <b>belum mengembalikan buku ke perpustakaan.</b> <br />
      Silakan hubungi bagian BAA agar Anda dapat mencetak KRS.
      <hr size=1 color=silver>
      Pilihan: <a href='?mnux=$_SESSION[mnux]&gos='>Kembali</a>"); 
  } else {
    echo Konfirmasi("Masa KRS/KPRS telah lewat", "Masa pengisian KRS/KPRS telah lewat. Silakan hubungi KaBAA untuk memperpanjang masa 
                    pengisian KRS/KPRS.");
  }
  // $blanko = "<a href='krs.pa.php?khsid=$khs[KHSID]' target=_blank>Blanko Bimbingan Akademik</a>";
  // Tampilkan
  $nomer = 0;
  $hari = -1;
  $totsks = 0;
  $hdrkrs = "<tr><th class=ttl colspan=2>No</th>
    <th class=ttl>Jam</th>
    <th class=ttl>Ruang</th>
    <th class=ttl>Kode MK</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl title='Kelas'>Kls</th>
    <th class=ttl title='Jenis Kuliah'>Jen</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Dosen</th>
    <th class=ttl>Hrg<br />Std?</td>
    <th class=ttl>Mundur</th>
    <th class=ttl>Status</th>
    </tr>";
  echo "<p><table class=box cellspacing=1 cellpadding=4";
  while ($w = _fetch_array($r)) {
    if ($hari != $w['HariID']) {
      $hari = $w['HariID'];
      $namahari = GetaField('hari', 'HariID', $hari, 'Nama');
      echo "<tr><td class=ul colspan=12><b>$namahari</b></td></tr>";
      echo $hdrkrs;
    }
    $hps = ((($datatahun['TglKRSMulai'] <= date('Y-m-d')) && (date('Y-m-d') <= $datatahun['TglKRSSelesai'])) || 
      (($datatahun['TglUbahKRSMulai'] <= date('Y-m-d')) && (date('Y-m-d') <= $datatahun['TglUbahKRSSelesai'])) || 
	  (($datatahun['TglKRSOnlineMulai'] <= date('Y-m-d')) && (date('Y-m-d') <= $datatahun['TglKRSOnlineSelesai']) || ($_SESSION['_LevelID'] == 41)) ||
	  ($_SESSION['_LevelID'] == 1))? "<a href='?mnux=krs&gos=KRSDel&krsid=$w[KRSID]'><img src='img/del.gif'></a>" : '&nbsp;';
    $hps = ($w['StatusKRSID'] == 'S')? '&raquo;' : $hps;
    if (($w['Hitung'] == 'Y') && ($datatahun['TglKRSMulai'] <= date('Y-m-d')) && (date('Y-m-d') <= $datatahun['TglMundur']) || (($datatahun['TglKRSOnlineMulai'] <= date('Y-m-d')) && (date('Y-m-d') <= $datatahun['TglKRSOnlineSelesai']) || ($_SESSION['_LevelID'] == 41)) || ($_SESSION['_LevelID'] == 1)) {
      if (strpos($_LevelMundurKRS, '.'.$_SESSION['_LevelID'].'.') === false) $mun = "&nbsp;";
      else {
        if ($w['StatusKRSID'] == 'A') $mun = "<a href='?mnux=krs&gos=KRSMundur&krsid=$w[KRSID]'>Mundur</a>";
        else $mun = "&nbsp;";
      }
    }
    else {
      $mun = "&nbsp;";
    }
    $nomer++;
    $totsks += ($w['JenisJadwalID'] == 'K' && $w['StatusKRSID'] == 'A')? $w['SKSnya'] : 0;
    // Daftar Dosen
    $arrdosen = explode('.', TRIM($w['DosenID'], '.'));
    $strdosen = implode(',', $arrdosen);
    $dosen = (empty($strdosen))? '' : GetArrayTable("select concat(Nama, ', ', Gelar) as NM from dosen where Login in ($strdosen) order by Nama",
      "Login", "NM", '<br />');
    $c = ($w['Hitung'] == 'Y')? 'class=ul' : 'class=nac';
    $hrg = ($w['HargaStandar'] == 'Y')? "<img src='img/$w[HargaStandar].gif'>" : number_format($w['Harga']);
    echo "<tr>
      <td class=inp1>$nomer</td>
      <td $c>$hps</td>
      <td $c>$w[JM]-$w[JS]</td>
      <td $c>$w[RuangID]</td>
      <td $c>$w[MKKode]</td>
      <td $c>$w[Nama]</td>
      <td $c>$w[NamaKelas]&nbsp;</td>
      <td $c align=center title='$w[JJ]'>$w[JenisJadwalID]</td>
      <td $c align=right>$w[SKS]</td>
      <td $c>$dosen&nbsp;</td>
      <td $c align=center>$hrg</td>
      <td $c>$mun</td>
      <td $c>($w[StatusKRSID]) $w[CatatanError]</td>
      </tr>";
  }
  echo "
  <tr><td colspan=8 align=right>Total SKS :</td><td class=ul align=right><b>$totsks</b></td></tr>
  </table></p>";
  GagalKRSMhsw($mhsw, $datatahun, $khs);
}
function JadwalKRSMhsw($mhsw, $datatahun, $khs) {
  $s = "select JadwalID
    from krstemp
    where KHSID='$khs[KHSID]' and NA='N' order by JadwalID";
  $r = _query($s);
  $hasil = array();
  while ($w = _fetch_array($r)) $hasil[] = $w['JadwalID'];
  return (empty($hasil))? '' : implode(', ', $hasil);
}
function KRSAdd($mhsw, $datatahun, $khs) {
  if ((($datatahun['TglKRSMulai'] <= date('Y-m-d')) && (date('Y-m-d') <= $datatahun['TglKRSSelesai'])) || 
      (($datatahun['TglUbahKRSMulai'] <= date('Y-m-d')) && (date('Y-m-d') <= $datatahun['TglUbahKRSSelesai'])) || ($_SESSION['_LevelID'] == 1)) {}
  else die("KRS sudah tidak dapat diubah");
  // Ambil data KRS
  $kecuali = JadwalKRSMhsw($mhsw, $datatahun, $khs);
  $sqlkecuali = (empty($kecuali))? '' : "and not (j.JadwalID in ($kecuali))";
  // Jadwal
  $whr = '';
  if (($mhsw['ProdiID']=='31' || $mhsw['ProdiID']=='32') && (substr($mhsw['MhswID'], 2, 4)<='2003')) {
    $whr = "";
  }
  elseif (($mhsw['ProdiID']=='31' || $mhsw['ProdiID']=='32') && (substr($mhsw['MhswID'], 2, 4)=='2004') && ($mhsw['StatusAwalID']=='P')) {
    $whr = "";
  }
  else {
    $whr = "and INSTR(j.ProgramID, '.$mhsw[ProgramID].')>0";
  }
  $s = "select j.*, jj.Nama as JJ,
      time_format(j.JamMulai, '%H:%i') as JM,
      time_format(j.JamSelesai, '%H:%i') as JS
    from jadwal j
      left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
    where j.TahunID='$khs[TahunID]'
      and j.NA<>'Y'
      and INSTR(j.ProdiID, '.$mhsw[ProdiID].')>0
      $whr
      $sqlkecuali
    order by j.HariID, j.JamMulai, j.MKKode ";
  $r = _query($s);

  // Tampilkan
  TampilkanJudul("<a href='#Atas'></a>Tambah KRS");
  $hari = -1;
  $kehari = DftrHari();
  $btn = "<tr><td class=ul colspan=12><input type=submit name='Ambil' value='Ambil Matakuliah'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=krs'\">
    </td></tr>";
  $hdrjdwl = "<tr>
    <th class=ttl>ID</th>
    <th class=ttl>Ambil</th>
    <th class=ttl>Jam</th>
    <th class=ttl>Ruang</th>
    <th class=ttl title='Kapasitas Kelas'>Kaps</th>
    <th class=ttl>Kode MK</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl title='Jenis Kuliah'>Jen</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Dosen</th>
    <th class=ttl title='Apakah harganya standar?'>Hrg<br />Std?</td>
    <th class=ttl title='Matakuliah prasyarat'>Pra</td>
    </tr>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' name='krs' method=POST>
    <input type=hidden name='mnux' value='krs'>
    <input type=hidden name='gos' value=''>
    <input type=hidden name='slnt' value='krs.lib'>
    <input type=hidden name='slntx' value='KRSSav'>
    <input type=hidden name='mhswid' value='$mhsw[MhswID]'>
    <input type=hidden name='tahun' value='$khs[TahunID]'>
    <input type=hidden name='khsid' value='$khs[KHSID]'>";
  while ($w = _fetch_array($r)) {
    if ($hari != $w['HariID']) {
      if ($hari > 0) {
        echo $btn;
      }
      $hari = $w['HariID'];
      $namahari = GetaField('hari', 'HariID', $hari, 'Nama');
      echo "<tr><td class=ul colspan=12><a name='$hari'></a><b>$namahari</b>
      <a href='#Atas'>^</a> $kehari</td></tr>";
      echo $hdrjdwl;
    }
    // Daftar Dosen
    $arrdosen = explode('.', TRIM($w['DosenID'], '.'));
    for ($i=0; $i < sizeof($arrdosen); $i++) {
      $arrdosen[$i] = "'". $arrdosen[$i]."'";
    }
    $strdosen = implode(',', $arrdosen);
    $dosen = (empty($strdosen))? '' : GetArrayTable("select concat(Nama, ', ', Gelar) as NM from dosen where Login in ($strdosen) order by Nama",
      "Login", "NM", '<br />');
    $hrg = ($w['HargaStandar'] == 'Y')? "<img src='img/$w[HargaStandar].gif'>" : number_format($w['Harga']);
    // Tampilkan
    $cb = ($w['JadwalSer'] ==0)? "<input type=checkbox name='JDWL[]' value='$w[JadwalID]'>" : "» $w[JadwalSer]";
    // Disable jika sudah memenuhi kapasitas
    $cb = ($w['JumlahMhswKRS'] >= $w['Kapasitas'])? '&nbsp' : $cb;
    $c = ($w['JumlahMhswKRS'] >= $w['Kapasitas'])? "class=wrn" : "class=ul";
    $arrpra = GetArrayTable("select mk.MKKode
      from mkpra
        left outer join mk on mkpra.PraID=mk.MKID
      where mkpra.MKID='$w[MKID]' ", 
      'MKKode', 'MKKode');
    $arrpra = (empty($arrpra))? "&nbsp;" : $arrpra;
    echo "<tr>
    <td class=inp1>$w[JadwalID]</td>
    <td $c align=center>$cb</td>
    <td $c>$w[JM]-$w[JS]</td>
    <td $c>$w[RuangID]&nbsp;</td>
    <td $c align=right>$w[JumlahMhswKRS]/$w[Kapasitas]</td>
    <td $c>$w[MKKode]</td>
    <td $c>$w[Nama]</td>
    <td $c>$w[NamaKelas]&nbsp;</td>
    <td $c align=center title='$w[JJ]'>$w[JenisJadwalID]</td>
    <td $c align=right>$w[SKS] ($w[SKSAsli])</td>
    <td $c>$dosen&nbsp;</td>
    <td $c align=center>$hrg</td>
    <td $c>$arrpra</td>
    </tr>";
  }
  echo $btn;
  echo "</table></p>";
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
function KRSSav() {
  // Data mahasiswa
  $mhswid = $_REQUEST['mhswid'];
  $mhsw = GetFields("mhsw
    left outer join prodi on mhsw.ProdiID=prodi.ProdiID", 
    'MhswID', $mhswid, 'mhsw.*, prodi.CekPrasyarat');
  // Data khs
  $khsid = $_REQUEST['khsid'];
  $khs = GetFields('khs', 'KHSID', $khsid, '*');
  // Data tahun
  $tahun = $_REQUEST['tahun'];
  $datatahun = GetFields('tahun', "KodeID='$_SESSION[KodeID]' and ProgramID='$mhsw[ProgramID]'
    and ProdiID='$mhsw[ProdiID]' and TahunID", $tahun, '*');

  $jdwl = array();
  $jdwl = $_REQUEST['JDWL'];
  if (!empty($jdwl)) {
    $salah = 0;
    $pesan = '';
    // Cek jadwal yg diambil 1 per 1
    for ($i = 0; $i < sizeof($jdwl); $i++) {
      $jad = GetFields("jadwal j
        left outer join hari h on j.HariID=h.HariID",
        'j.JadwalID', $jdwl[$i],
        "j.*, h.Nama as HR");
      // Validasi KRS
      $adasalah = CekKRSMhsw($mhsw, $datatahun, $khs, $jad, $_pesan);
      $adaserial = 0;
      if ($adasalah == 0) {
        // Apakah ada jadwal serialnya?
        $adaserial = GetaField('jadwal', "JadwalSer", $jad['JadwalID'], "count(*)")+0;
        //echo "<h1>$adaserial</h1>";
        if ($adaserial > 0)
          $adasalah += CekKRSSerial($mhsw, $datatahun, $khs, $jad, $_pesan);
      }
      //echo "<h1>$adasalah</h1>";
      // Proses KRS
      if ($adasalah == 0) SimpanKRSMhsw($mhsw, $datatahun, $khs, $jad, 'N', '', $adaserial);
      else {
        $salah += $adasalah;
        $pesan .= $_pesan;
        $sudahada = GetaField('krstemp', "MhswID='$mhsw[MhswID]' and JadwalID", $jdwl[$i], 'JadwalID')+0;
        if ($sudahada == 0) SimpanKRSMhsw($mhsw, $datatahun, $khs, $jad, 'Y', $_pesan, $adaserial);
      }
    }
    if ($adasalah >0) BuatPesanKesalahan($mhsw, $datatahun, $khs, $salah, $pesan);
    UpdateJumlahKRSMhsw($mhsw['MhswID'], $khs['KHSID']);
    ValidasiKuliahResponsi($khs);
  }
  else echo ErrorMsg("Tidak Disimpan",
    "Tidak ada jadwal kuliah yang diambil.");
}
function CekKRSSerial($mhsw, $datatahun, $khs, $jad, &$_pesan) {
  $pesan = '';
  $salah = 0;
  // Ambil Serialnya
  $s = "select j.*
    from jadwal j
    where j.JadwalSer='$jad[JadwalID]'
    order by j.HariID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $salah += Cek_JadwalBentrok($mhsw, $datatahun, $khs, $w, $pesan, 1);
    $_pesan .= $pesan; 
  }
  return $salah;
}

function CekKRSMhsw($mhsw, $datatahun, $khs, $jad, &$_pesan) {
  $pesan = '';
  // Cek 4: Kapasitas kelas
  // Catatan: Cek kapasitas kelas dinaikkan prioritas pemeriksaannya.
  if ($salah == 0) {
    $salah += Cek_Kapasitaskelas($mhsw, $datatahun, $khs, $jad, $_psn);
    $pesan .= $_psn;
  }
  // Cek 0: Lebih dari MaxSKS nggak?
  $salah = 0;
  if ($jad['JenisJadwalID'] == 'K') {
    $salah += Cek_MaxSKS($mhsw, $datatahun, $khs, $jad, $_psn);
    $pesan .= $_psn;
  }
  // Cek 0: FK --> Jika FK, maka lakukan beberapa pengecekan:
  if ($salah == 0 && $mhsw['ProdiID'] == '10') {
    $salah += Cek_KRSFK($mhsw, $datatahun, $khs, $jad, $_psn);
    $pesan .= $_psn;
  }
  // Cek 1: Sudah diambil belum?
  if ($salah == 0) {
    $salah += Cek_SudahDiambil($mhsw, $datatahun, $khs, $jad, $_psn);
    $pesan .= $_psn;
  }
  // Cek 2: Prasyarat pengambilan Matakuliah
  if ($salah == 0) {
    $salah += Cek_PrasyaratPengambilanMK($mhsw, $datatahun, $khs, $jad, $_psn);
    $pesan .= $_psn;
  }
  // Cek 2A : Ada Matakuliah prasyarat tidak?
  if ($salah == 0) {
    $salah += Cek_MatakuliahPrasyarat($mhsw, $datatahun, $khs, $jad, $_psn);
    $pesan .= $_psn;
  }
  // Cek 3: Bentrok jadwal nggak?
  if ($salah == 0) {
    $salah += Cek_JadwalBentrok($mhsw, $datatahun, $khs, $jad, $_psn);
    $pesan .= $_psn;
  }
  $_pesan = $pesan;
  //echo "<h1>$_pesan</h1>";
  return $salah;
}
function Cek_Kapasitaskelas($mhsw, $datatahun, $khs, $jad, &$psn) {
  $err = 0;
  if ($jad['JumlahMhswKRS'] >= $jad['Kapasitas']) {
    $err += 1;
    $psn .= "MK $jad[MKKode] melebihi kapasitas kelas";
  }
  return ($err >0)? 1 : 0;
}
function Cek_KRSFK($mhsw, $datatahun, $khs, $jad, &$psn) {
  // Ambil data KRS-MK, cukup ambil 1 MK saja
  $s = "select * 
    from krs
    where MhswID='$mhsw[MhswID]' and MKID=$jad[MKID]
    order by BobotNilai desc
    limit 1";
  $r = _query($s);
  $w = _fetch_array($r);
  $err = 0;
  // Jika belum pernah ambil:
  if ($jad['NamaKelas'] == 'U') {
    if ($w['GradeNilai']{0} == 'E' || empty($w)) {
      $err += 1;
      $psn .= "MK $jad[MKKode] harus kelas BARU";
    }
  }
  if ($jad['NamaKelas'] == 'B') {
    $iya = strpos("ABCD", $w['GradeNilai']{0});
    if ($iya === false) {}
    else { 
      $err += 1; 
      $psn .= "MK $jad[MKKode] harus kelas ULANG"; 
    }
  }
  return ($err > 0)? 1 : 0;
}
function Cek_MaxSKS($mhsw, $datatahun, $khs, $jad, &$psn) {
  $n = 0;
  $sksasli = ($jad['JenisJadwalID'] == 'K')? $jad['SKSAsli'] : 0;
  $jml = GetaField('krstemp krs left outer join jadwal on krs.JadwalID=jadwal.JadwalID', 
    "krs.NA='N' and jadwal.JenisJadwalID='K' and krs.KHSID", $khs['KHSID'], "sum(krs.SKS)") + $jad['SKS'];
  if ($jml > $khs['MaxSKS']) {
    $n++;
    $psn .= "$jad[MKKode]: Akan lebih SKS: $khs[MaxSKS].";
  }
  return $n;
}
function Cek_SudahDiambil($mhsw, $datatahun, $khs, $jad, &$psn) {
  $data = GetaField('krstemp', "MhswID='$mhsw[MhswID]' and KHSID=$khs[KHSID] and NA='N' and JadwalID",
    $jad['JadwalID'], 'KRSID');
  if (!empty($data)) {
    $psn .= "$jad[MKKode]-$jad[NamaKelas] telah diambil.";
  }
  return (empty($data))? 0 : 1;
}
function Cek_PrasyaratPengambilanMK($mhsw, $datatahun, $khs, $jad, &$_psn) {
  $mk = GetFields('mk', 'MKID', $jad['MKID'], '*');
  //echo "<h1>$mk[SKSMin]</h1>";
  // apakah Jumlah SKS mencukupi?
  $psn = '';
  if ($mhsw['TotalSKS'] < $mk['SKSMin']) $psn .= "Jml SKS kurang ($mhsw[JumlahSKS]).";
  // apakah IPK tdk mencukupi?
  if ($mhsw['IPK'] < $mk['IPKMin']) $psn .= "IPK kurang.";
  $_psn .= $psn;
  return (empty($psn))? 0 : 1;
}
function Cek_MatakuliahPrasyarat($mhsw, $datatahun, $khs, $jad, &$_psn) {
  $pesanpra = '';
  $salah = 0;
  //$prd = GetFields('prodi', "ProdiID", $mhsw['ProdiID'], "ProdiID, CekPrasyarat");
  if ($mhsw['CekPrasyarat'] == 'Y') {
    $spr = "select mkpra.*, 
      krs.KRSID, krs.GradeNilai, krs.BobotNilai, 
      mk.Nama, mk.MKKode 
      from mkpra 
        left outer join krs on mkpra.MKPra=krs.MKKode
          and krs.MhswID='$mhsw[MhswID]'
        left outer join mk on mkpra.PraID=mk.MKID
      where mkpra.MKID='$jad[MKID]'
      order by mk.MKKode asc, krs.BobotNilai desc";
    //echo "<pre>$spr</pre>";
    $rpr = _query($spr); $n=0;
    // Cek 1: apa ada yg ganda? Jika ada, ambil nilai yg tertinggi
    $arrpra = array();
    $kdpra = 'abcdefghijklmnopqrstuvwxyz';
    while ($wpr = _fetch_array($rpr)) {
        if ($kdpra != $wpr['MKKode'] && !empty($wpr['MKKode'])) {
          $kdpra = $wpr['MKKode'];
          $arrpra[] = "$wpr[MKKode]~$wpr[Bobot]~#$wpr[KRSID]~$wpr[BobotNilai]~$wpr[GradeNilai]~$wpr[MKAlternatif]";
          //           0            1           2            3                4                5
        }
    }
    $jmlpra = sizeof($arrpra)+0;
    if (!empty($arrpra)) {
      // Cek 2: Apakah belum diambil? Apakah nilainya kurang?
      for ($i = 0; $i < sizeof($arrpra); $i++) {
        $_pra = explode('~', $arrpra[$i]);
        // belum diambil?
        $KRSID = TRIM($_pra[2], '#');
        $Alt = strpos($_pra[5], $jad['MKKode']);
        // Pecahkan MKAlternatif
        $Alt = TRIM($Alt, '.');
        if (!empty($Alt)) {
          $arrAlt = explode('.', $Alt);
          $strAlt = '';
          for ($i = 0; $i < sizeof($arrAlt); $i++) {
            $strAlt .= "'".$arrAlt[$i]."',";
          }
          $strAlt = TRIM($arrAlt, ',');
          $AdaAlt = GetaField('krs', "MKKode in ($strAlt) and MhswID", $mhsw['MhswID'], "count(*)")+0;
          $Ketemu = ($AdaAlt > 0)? TRUE : FALSE;  
        }
        else $Ketemu = FALSE;
        
        if (empty($KRSID) && $Alt == FALSE) {
            $salah++;
            $pesanpra .= "Prasyarat: ". $_pra[0];
        }
        // Nilai kurang?
        else if ($_pra[3] < $_pra[1]) {
          $salah++;
          $pesanpra .= "Nilai prasyarat: $_pra[0] ($_pra[4]).";
        }
      }
    }
  } // end-if check prasyarat
  $_psn .= $pesanpra;
  return $salah;
}
function Cek_JadwalBentrok($mhsw, $datatahun, $khs, $jad, &$_psn, $serial=0) {
  $bentrok = '';
  $s = "select k.*,
    j.JadwalID, j.HariID, j.JamMulai, j.JamSelesai,
    j.MKKode, j.Nama, j.SKS, j.NamaKelas, j.JenisJadwalID,
    h.Nama as HR
    from krstemp k
      left outer join jadwal j on k.JadwalID=j.JadwalID
      left outer join hari h on j.HariID=h.HariID
    where k.KHSID=$khs[KHSID] and k.NA='N'
      and j.HariID=$jad[HariID]
      and ((j.JamMulai <= '$jad[JamMulai]' and '$jad[JamMulai]' < j.JamSelesai)
      or (j.JamMulai < '$jad[JamSelesai]' and '$jad[JamSelesai]' < j.JamSelesai))
    limit 1";
  // Query diberi limit, maksudnya 1 saja jadwal bentrok sudah dapat membuat error.
  $r = _query($s);
  //echo "<pre>$s</pre>";
  $salah = (_num_rows($r) > 0)? 1 : 0;
  if ($salah > 0) {
    $w = _fetch_array($r);
    $KK = ($w['JenisJadwalID'] == 'K')? '(K)' : "(R)";
    $ser = ($serial > 0)? "Serial" : '';
    $bentrok .= "$ser Bentrok: $w[MKKode]-$w[NamaKelas] $KK.";
  }
  $_psn .= $bentrok;
  return $salah;
}
function SimpanKRSMhsw($mhsw, $datatahun, $khs, $jad, $NA='N', $PesanGagal='', $adaserial='') {
  if ($NA=='N') {
    $PesanGagal = (empty($PesanGagal))? "Diterima" : $PesanGagal;
  }
  // Simpan KRS
  $s = "insert into krstemp (KHSID, MhswID,
    TahunID, JadwalID,
    MKID, MKKode, SKS, HargaStandar, Harga,
    NA, CatatanError,
    LoginBuat, TanggalBuat)
    values ('$khs[KHSID]', '$mhsw[MhswID]',
    '$khs[TahunID]', '$jad[JadwalID]',
    '$jad[MKID]', '$jad[MKKode]', '$jad[SKS]', '$jad[HargaStandar]', '$jad[Harga]',
    '$NA', '$PesanGagal',
    '$_SESSION[_Login]', now())";
  $r = _query($s);
  // Cek apakah ada serialnya?
  if ($adaserial > 0) {
    $s1 = "select * from jadwal where JadwalSer='$jad[JadwalID]' order by HariID";
    $r1 = _query($s1);
    while ($w = _fetch_array($r1)) {
      $s2 = "insert into krstemp (KHSID, MhswID,
        TahunID, JadwalID,
        MKID, MKKode, SKS, HargaStandar, Harga,
        StatusKRSID, NA, CatatanError,
        LoginBuat, TanggalBuat)
        values ('$khs[KHSID]', '$mhsw[MhswID]',
        '$khs[TahunID]', '$w[JadwalID]',
        '$w[MKID]', '$w[MKKode]', 0, '$w[HargaStandar]', '$w[Harga]',
        'S', '$NA', 'SERIAL',
        '$_SESSION[_Login]', now())";
      $r2 = _query($s2);
      //echo "<pre>$s2</pre>";
      UpdateJumlahMhsw($w['JadwalID']);
    }
  }
  UpdateJumlahMhsw($jad['JadwalID']);
}
function ValidasiKuliahResponsi($khs) {
  $arrKR = array('K'=>"Responsi harus diambil", "R"=>"Kuliah harus diambil", "S"=>"SERIAL");
  $s = "select krs.*, j.JenisJadwalID
    from krstemp krs
      left outer join jadwal j on krs.JadwalID=j.JadwalID
    where krs.KHSID=$khs[KHSID] 
      and krs.NA='N' ";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    // Jika ada K/R tapi belum ambil K/R:
    $adaerr = CekKuliahResponsi($khs, $w);
    if ( $adaerr > 0) {
      $s0 = "update krstemp set CatatanError='". $arrKR[$w['JenisJadwalID']] . "'
        where KRSID=$w[KRSID]";
      $r0 = _query($s0);
    }
    else {
      // Jika sdh beres
      if ($w['CatatanError'] <> 'DITERIMA') {
        $psnggl = ($w['StatusKRSID'] == 'S')? "SERIAL" : "DITERIMA";
        $s1 = "update krstemp set CatatanError='$psnggl'
          where KRSID=$w[KRSID] ";
        $r1 = _query($s1);
      }
    }
  }
}
function CekKuliahResponsi($khs, $w) {
  $arrKR = array("K"=>"R", "R"=>"K");
  // apakah ada matakuliah responsinya?
  $ada = GetaField("jadwal", 
    "TahunID='$khs[TahunID]' and INSTR(ProdiID, '$khs[ProdiID]')>0 and MKKode='$w[MKKode]' and JenisJadwalID",
    $arrKR[$w['JenisJadwalID']], "count(*)")+0;
  $n = 0;
  $arrKR[$w['JenisJadwalID']] . "</p>";
  if ($ada > 0) {
    // Jika ada matakuliah responsinya, cek apakah sudah ada diambil?
    $ambil = GetaField("krstemp krs left outer join jadwal on krs.JadwalID=jadwal.JadwalID",
      "krs.KHSID=$khs[KHSID] and krs.MKKode='$w[MKKode]' and jadwal.JenisJadwalID",
      $arrKR[$w['JenisJadwalID']], "count(*)")+0;
    $n = ($ambil == 0)? 1 : 0;
  }
  return $n;
}
function BuatPesanKesalahan($mhsw, $datatahun, $khs, $salah, $pesan) {
  // Ambil file CSS
  $filecss = "css.php";
  $hndcss = fopen($filecss, 'r');
  $isicss = fread($hndcss, filesize($filecss));
  $pesan = TRIM($pesan, '.');
  $_pesan = explode('.', $pesan);
  for ($i = 0; $i < sizeof($_pesan); $i++) 
    $_pesan[$i] = "<li>".$_pesan[$i]."</li>";
  $pesan = implode('', $_pesan);
  fclose($hndcss);
  // Buat pesan kesalahan lengkap
  $pesankesalahan = "$isicss
    <p><center><h3 class=wrn>Ada kesalahan</h3></center></p>
    <p>Ada <b>$salah</b> kesalahan yang terjadi. Karena ada beberapa kesalahan,
    maka beberapa matakuliah di bawah ini tidak ditambahkan dalam KRS mahasiswa:</p>
    <ol>$pesan</ol>
    <hr size=1 color=maroon>
    <input type=button name='Tutup' value='Tutup Pesan' onClick=\"javascript:window.close()\">";
  // Buat file kesalahan
  $filesalah = "tmp/$_SESSION[_Login].KRS.html";
  $hndsalah = fopen($filesalah, 'w');
  fwrite($hndsalah, $pesankesalahan);
  fclose($hndsalah);
  PopupMsg($filesalah);
}
function KRSDel($mhsw, $datatahun, $khs) {
  if ($_SESSION['_LevelID'] != 1) { 
    if ((($datatahun['TglKRSMulai'] <= date('Y-m-d')) && (date('Y-m-d') <= $datatahun['TglKRSSelesai'])) || 
      (($datatahun['TglUbahKRSMulai'] <= date('Y-m-d')) && (date('Y-m-d') <= $datatahun['TglUbahKRSSelesai']))) {}
    else die(ErrorMsg("KRS Sudah Tidak Dapat Diubah",
      "Anda sudah tidak dapat mengubah KRS karena batas waktu sudah lewat"));
  }
  $krsid = $_REQUEST['krsid'];
  $krs = GetFields("krstemp k
    left outer join jadwal j on k.JadwalID=j.JadwalID
    left outer join hari h on j.HariID=h.HariID",
    "k.KRSID", $krsid, "k.*, j.MKKode, j.Nama, j.NamaKelas, j.SKS, j.SKSAsli, j.JenisJadwalID,
    j.JamMulai, j.JamSelesai, j.HariID, j.DosenID, h.Nama as HR");
  // Daftar Dosen
  $arrdosen = explode('.', TRIM($krs['DosenID'], '.'));
  $strdosen = implode(',', $arrdosen);
  $dosen = (empty($strdosen))? '' : GetArrayTable("select concat(Nama, ', ', Gelar) as NM from dosen where Login in ($strdosen) order by Nama",
    "Login", "NM", '<br />');

  echo Konfirmasi("Konfirmasi Hapus KRS",
  "<p>Anda akan menghapus KRS yang telah Anda ambil, yaitu:</p>
  <p><table class=box cellspacing=1 cellpadding=4>
  <tr>
    <td class=inp1>Hari</td><td class=ul>$krs[HR]</td>
    <td class=inp1>Jam</td><td class=ul>$krs[JamMulai]-$krs[JamSelesai]</td></tr>
  <tr>
    <td class=inp1>Kode MK</td><td class=ul>$krs[MKKode]</td>
    <td class=inp1>Matakuliah</td><td class=ul>$krs[Nama]</td></tr>
  <tr>
    <td class=inp1>Kelas</td><td class=ul>$krs[NamaKelas]</td>
    <td class=inp1>SKS</td><td class=ul>$krs[SKSAsli]</td></tr>
  <tr>
    <td class=inp1>Jenis</td><td class=ul>$krs[JenisJadwalID]</td>
    <td class=inp1>Dosen pengampu</td><td class=ul>$dosen</td></tr>
  </table</p>
  <hr size=1 color=silver>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='krs'>
  <input type=hidden name='krsid' value='$krsid'>
  <input type=hidden name='gos' value=''>
  <input type=hidden name='slnt' value='krs.lib'>
  <input type=hidden name='slntx' value='KRSDel1'>
  Pilihan: <input type=submit name='Hapus' value='Hapus'>
    <input type=button name='Batal' value='Batal Hapus' onClick=\"location='?mnux=krs'\">
  </form>");
}
function KRSDel1() {
  // Hapus KRS
  $krsid = $_REQUEST['krsid'];
  $krs = GetFields("krstemp", "KRSID", $krsid, "MhswID, KHSID, JadwalID");
  $jdwlid = $krs['JadwalID'];
  $s = "delete from krstemp where KRSID='$krsid' ";
  $r = _query($s);
  // Jika ada serialnya, maka hapus KRS serialnya juga
  $js = "select JadwalID from jadwal where JadwalSer=$jdwlid";
  $rs = _query($js);
  while ($ws = _fetch_array($rs)) {
    $sdel = "delete from krstemp where MhswID='$krs[MhswID]' and JadwalID='$ws[JadwalID]' ";
    $rdel = _query($sdel);
    UpdateJumlahMhsw($ws['JadwalID']);
  }
  
  UpdateJumlahKRSMhsw($krs['MhswID'], $krs['KHSID']);
  UpdateJumlahMhsw($krs['JadwalID']);
  // validasi semua MK
  $khs = GetFields('khs', 'KHSID', $krs['KHSID'], '*');
  ValidasiKuliahResponsi($khs);
}
function KRSMundur1() {
  $krsid = $_REQUEST['krsid'];
  $krs = GetFields("krstemp", "KRSID", $krsid, "MhswID, KHSID, JadwalID");
  //$mundur = GetaField('statuskrs', 'Hitung', 'N', 'StatusKRSID');
  $mundur = 'M';
  $s = "update krstemp set StatusKRSID='$mundur', GradeNilai='$mundur' where KRSID='$krsid' ";
  $r = _query($s);
  UpdateJumlahKRSMhsw($krs['MhswID'], $krs['KHSID']);
  UpdateJumlahMhsw($krs['JadwalID']);
}
function KRSMundur($mhsw, $datatahun, $khs) {
  if ((date('Y-m-d') > $datatahun['TglMundur']) and ($_SESSION['_LevelID'] != 1)) die("Sudah tidak dapat mundur");
  $krsid = $_REQUEST['krsid'];
  $krs = GetFields("krstemp k
    left outer join jadwal j on k.JadwalID=j.JadwalID
    left outer join hari h on j.HariID=h.HariID",
    "k.KRSID", $krsid, "k.*, j.MKKode, j.Nama, j.SKS, j.SKSAsli,
    j.JamMulai, j.JamSelesai, j.HariID, j.DosenID, h.Nama as HR");
  // Daftar Dosen
  $arrdosen = explode('.', TRIM($krs['DosenID'], '.'));
  $strdosen = implode(',', $arrdosen);
  $dosen = (empty($strdosen))? '' : GetArrayTable("select concat(Nama, ', ', Gelar) as NM from dosen where Login in ($strdosen) order by Nama",
    "Login", "NM", '<br />');

  echo Konfirmasi("Konfirmasi Mundur Matakuliah",
  "<p>Anda akan mengundurkan diri dari matakuliah ini?</p>
  <p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp1>Hari</td><td class=ul>$krs[HR]</td></tr>
  <tr><td class=inp1>Jam</td><td class=ul>$krs[JamMulai]-$krs[JamSelesai]</td></tr>
  <tr><td class=inp1>Kode MK</td><td class=ul>$krs[MKKode]</td></tr>
  <tr><td class=inp1>Matakuliah</td><td class=ul>$krs[Nama]</td></tr>
  <tr><td class=inp1>SKS</td><td class=ul>$krs[SKSAsli]</td></tr>
  <tr><td class=inp1>Dosen pengampu</td><td class=ul>$dosen</td></tr>
  </table</p>
  <hr size=1 color=silver>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='krs'>
  <input type=hidden name='krsid' value='$krsid'>
  <input type=hidden name='gos' value=''>
  <input type=hidden name='slnt' value='krs.lib'>
  <input type=hidden name='slntx' value='KRSMundur1'>
  Pilihan: <input type=submit name='Mundur' value='Mundur'>
    <input type=button name='Batal' value='Batal Mundur' onClick=\"location='?mnux=krs'\">
  </form>");

}
function UpdateJumlahKRSMhsw($mhswid, $khsid) {
  $arrjml = GetFields("krstemp k
    left outer join jadwal j on k.JadwalID=j.JadwalID",
    "k.NA='N' and k.StatusKRSID='A' and j.JenisJadwalID='K' and KHSID", 
    $khsid, "sum(k.SKS) as JumlahSKS, count(k.KRSID) as JumlahMK");
  $JumlahSKS = $arrjml['JumlahSKS']+0;
  $s = "update khs set TotalSKS='$JumlahSKS', JumlahMK='$arrjml[JumlahMK]'
    where KHSID='$khsid' ";
  $r = _query($s);
  return $arrjml;
}
function UpdateJumlahMhsw($jadwalid) {
  $jml = GetaField('krs', "JadwalID", $jadwalid, "count(KRSID)")+0;
  $jmlkrs = GetaField('krstemp', "JadwalID", $jadwalid, "count(KRSID)")+0;
  $s = "update jadwal set JumlahMhsw=$jml, JumlahMhswKRS=$jmlkrs where JadwalID=$jadwalid ";
  $r = _query($s);
}
function GagalKRSMhsw($mhsw, $datatahun, $khs) {
  $_LevelByPass = ".1.40.41.50.";
  $s = "select krs.KRSID, krs.KHSID, krs.CatatanError, j.*, h.Nama as HR
    from krstemp krs
      left outer join jadwal j on krs.JadwalID=j.JadwalID
      left outer join hari h on j.HariID=h.HariID
    where krs.NA='Y'
      and krs.MhswID='$mhsw[MhswID]'
      and krs.KHSID='$khs[KHSID]'
    order by j.MKKode";
  $r = _query($s);
  if (_num_rows($r) > 0) {
    echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><td colspan=7><img src='img/kali.png'> <b>KRS yang Gagal</b> &nbsp;&nbsp;&nbsp;
      <input type=button name='Hapus' value='Hapus Semua KRS Gagal' onClick=\"location='?mnux=krs&gos=GagalKRSMhswDel&khsid=$khs[KHSID]'\">
    </td></tr>
    <tr><th class=ttl>#</th>
      <th class=ttl title='Berikan Dispensasi'>Disp</th>
      <th class=ttl>Kode</th>
      <th class=ttl>Matakuliah</th>
      <th class=ttl>Kls</th>
      <th class=ttl>Jen</th>
      <th class=ttl>Hari</th>
      <th class=ttl>Jam</th>
      <th class=ttl>Catatan</th>
      </tr>";
    $n = 0;
    while ($w = _fetch_array($r)) {
      $n++;
      $byps = (strpos($_LevelByPass, '.'.$_SESSION['_LevelID'].'.') === false)? '&nbsp;' :
        "<input type=checkbox name='Dispens' value='Dispensasi' onClick=\"location='?mnux=krs&gos=GagalKRSDisp&krsid=$w[KRSID]'\">";
      echo "<tr><td class=inp>$n</td>
      <td class=ul align=center>$byps</td>
      <td class=ul nowrap>$w[MKKode]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul>$w[NamaKelas]</td>
      <td class=ul>$w[JenisJadwalID]</td>
      <td class=ul>$w[HR]</td>
      <td class=ul>$w[JamMulai] ~ $w[JamSelesai]</td>
      <td class=ul>$w[CatatanError]</td>
      </tr>";
    }
    echo "</table></p>";
  }
}
function GagalKRSMhswDel($mhsw, $datatahun, $khs) {
  $khsid = $_REQUEST['khsid'];
  $s = "delete from krstemp where KHSID='$khsid' and NA='Y' ";
  $r = _query($s);
  DftrKRS($mhsw, $datatahun, $khs);
}
function GagalKRSDisp($mhsw, $datatahun, $khs) {
  $krsid = $_REQUEST['krsid'];
  $krs = GetFields("krstemp krs
    left outer join jadwal j on krs.JadwalID=j.JadwalID",
    "KRSID", $krsid,
    "krs.*, j.JadwalID, j.Nama, j.NamaKelas, j.MKKode, j.JenisJadwalID, j.HariID, 
    j.JamMulai, j.JamSelesai, j.SKS, j.SKSAsli");
  $hr = GetaField('hari', 'HariID', $krs['HariID'], 'Nama');
  $tgldisp = GetDateOption(date('Y-m-d'), 'TanggalDispensasi');
  echo Konfirmasi("Dispensasi KRS",
    "<p>Benar mahasiswa ini mendapatkan dispensasi untuk matakuliah ini?</p>
    <p><table class=box cellspacing=1 cellpadding=4>
    <tr><td class=inp>Matakuliah</td><td class=ul>$krs[MKKode] - $krs[Nama] $krs[NamaKelas]</td></tr>
    <tr><td class=inp>SKS</td><td class=ul>$krs[SKS] ($krs[SKSAsli])</td></tr>
    <tr><td class=inp>Hari, Jam</td><td class=ul>$hr, $krs[JamMulai] ~ $krs[JamSelesai]</td></tr>
    </table></p>
    <p>Jika mahasiswa ini mendapat dispensasi, masukkan formulir di bawah ini.<br />
    Jika tidak, batalkan dengan menekan tombol <b>Batal</b>.</p>
    <p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='krs'>
    <input type=hidden name='gos' value='GagalKRSDisp1'>
    <input type=hidden name='krsid' value='$krsid'>
    <input type=hidden name='BypassMenu' value=1>
    <tr><th class=ttl colspan=2>DISPENSASI</th></tr>
    <tr><td class=inp>Diberikan oleh</td><td class=ul><input type=text name='DispensasiOleh' value='$krs[DispensasiOleh]' size=50 maxlength=50></td></tr>
    <tr><td class=inp>Tanggal Diberikan</td><td class=ul>$tgldisp</td></tr>
    <tr><td class=inp>Catatan</td><td class=ul><textarea name='CatatanDispensasi' cols=40 rows=5>$krs[CatatanDispensasi]</textarea></td></tr>
    <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Berikan Dispensasi'>
      <input type=reset name='Reset' value='Reset'>
      <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=krs'\"></td></tr>
    </form></table>");
}
function GagalKRSDisp1($mhsw, $datatahun, $khs) {
  $krsid = $_REQUEST['krsid'];
  $JadwalID = GetaField("krstemp k", "k.KRSID", $krsid, "JadwalID");
  $DispensasiOleh = sqling($_REQUEST['DispensasiOleh']);
  $CatatanDispensasi = sqling($_REQUEST['CatatanDispensasi']);
  $TanggalDispensasi = "$_REQUEST[TanggalDispensasi_y]-$_REQUEST[TanggalDispensasi_m]-$_REQUEST[TanggalDispensasi_d]";
  $s = "update krstemp set NA='N', Dispensasi='Y', DispensasiOleh='$DispensasiOleh',
    TanggalDispensasi='$TanggalDispensasi', CatatanError=concat('Disp: ', CatatanError), CatatanDispensasi='$CatatanDispensasi'
    where KRSID='$krsid' ";
  $r = _query($s);
  // Cek apakah ada serialnya
  $adaserial = GetaField('jadwal', 'JadwalSer', $JadwalID, "Count(*)")+0;
  if ($adaserial > 0) {
    $s1 = "select * from jadwal where JadwalSer='$JadwalID' order by HariID";
    $r1 = _query($s1);
    while ($w = _fetch_array($r1)) {
      $s2 = "insert into krstemp (KHSID, MhswID,
        TahunID, JadwalID,
        MKID, MKKode, SKS, HargaStandar, Harga,
        StatusKRSID, NA, CatatanError,
        LoginBuat, TanggalBuat)
        values ('$khs[KHSID]', '$mhsw[MhswID]',
        '$khs[TahunID]', '$w[JadwalID]',
        '$w[MKID]', '$w[MKKode]', 0, '$w[HargaStandar]', '$w[Harga]',
        'S', 'N', 'SERIAL',
        '$_SESSION[_Login]', now())";
      $r2 = _query($s2);
      //echo "<pre>$s2</pre>";
      UpdateJumlahMhsw($w['JadwalID']);
    }
  }
  
  DftrKRS($mhsw, $datatahun, $khs);
  // Update jumlah KRS
  UpdateJumlahKRSMhsw($khs['MhswID'], $khs['KHSID']);
  UpdateJumlahMhsw($JadwalID);
  echo "<script>window.location='?mnux=$_SESSION[mnux]';</script>";
}
?>
