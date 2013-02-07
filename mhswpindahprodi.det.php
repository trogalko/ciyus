<?php
// Author: Emanuel Setio Dewo
// 07 March 2006

// *** Functions ***
function KonfirmasiPindah($mhsw) {
  $dapatpindah = GetaField('prodi', 'ProdiID', $mhsw['ProdiID'], 'DapatPindahProdi');
  if (empty($dapatpindah)) {
    echo ErrorMsg("Tidak Dapat Pindah Prodi",
    "Mahasiswa dari Program Studi <b>$mhsw[ProdiID]</b> tidak dapat pindah ke prodi lain.
    Anda hanya dapat memindah Program-nya saja.");
    $nmprodi = GetaField('prodi', 'ProdiID', $mhsw['ProdiID'], 'Nama');
    $optprodi = "<input type=hidden name='PindahProdiID' value='$mhsw[ProdiID]'><b>$mhsw[ProdiID] - $nmprodi</b>";
  }
  else {
    $dapatpindah = TRIM($dapatpindah, '.');
    $arrpindah = explode('.', $dapatpindah);
    $whr = implode(', ', $arrpindah) . ', '. $mhsw['ProdiID'];
    $optprodi = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)",
      'ProdiID', $mhsw['ProdiID'], "ProdiID in ($whr)", 'ProdiID');
    $optprodi = "<select name='PindahProdiID'>$optprodi</select>";
  }
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)",
    'ProgramID', $mhsw['ProgramID'], '', 'ProgramID');
  $statuslama = GetOption2('statusmhsw', "concat(StatusMhswID, ' - ', Nama)", 'StatusMhswID',
    $mhsw['StatusMhswID'], "Keluar=1", 'StatusMhswID');
  $statusbaru = GetOption2('statusawal', "concat(StatusAwalID, ' - ', Nama)", 'StatusAwalID',
    '', '', 'StatusAwalID');
  CheckFormScript("PindahProgramID,Angkatan,StatusLama,StatusBaru");
  echo Konfirmasi("Pemindahan Prodi",
  "Benar Anda akan memindahkan mahasiswa ini ke Program Studi lain?
  <p><table class=box cellspacing=1 cellpadding=4 width=100%>
  <tr><td class=ul>NPM</td><td class=ul><b>$mhsw[MhswID]</b></td></tr>
  <tr><td class=ul>Nama Mhsw</td><td class=ul><b>$mhsw[Nama]</b></td></tr>
  <tr><td class=ul>Program</td><td class=ul><b>$mhsw[PRG]</b> ($mhsw[ProgramID])</td></tr>
  <tr><td class=ul>Program Studi</td><td class=ul><b>$mhsw[PRD]</b> ($mhsw[ProdiID])</td></tr>
  <tr><td class=ul>Status Mahasiswa</td><td class=ul><b>$mhsw[SM]</b></td></tr>
  </table></p>
  <p>Jika ya, maka akan dipindah ke mana?</p>

  <p><table class=box cellspacing=1 cellpadding=4 width=100%>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='mhswpindahprodi.det'>
  <input type=hidden name='gos' value='PindahSav'>
  <input type=hidden name='mhswid' value='$mhsw[MhswID]'>
  <tr><td class=ul>Pindahkan ke Program</td><td class=ul><select name='PindahProgramID'>$optprg</select></td></tr>
  <tr><td class=ul>Pindahkan ke Prodi</td><td class=ul>$optprodi</td></tr>
  <tr><td class=ul>Ke Tahun Akademik</td><td class=ul><input type=text name='Angkatan' value='$_SESSION[tahun]' size=10 value=10></td></tr>
  <tr><td class=ul>Set status pada data lama</td><td class=ul><select name='StatusLama'>$statuslama</select></td></tr>
  <tr><td class=ul>Set status pada data baru</td><td class=ul><select name='StatusBaru'>$statusbaru</select></td></tr>
  <tr><td class=ul colspan=2>
    <input type=submit name='Pindah' value='Pindah'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=mhswpindahprodi'\"></td></tr>
  </form></table></p>
  ");
}
function PindahSav($mhsw) {
  $PindahProgramID = $_REQUEST['PindahProgramID'];
  $PindahProdiID = $_REQUEST['PindahProdiID'];
  $Angkatan = $_REQUEST['Angkatan'];
  if (($mhsw['ProgramID'] == $PindahProgramID) && ($mhsw['ProdiID'] == $PindahProdiID)) {
    Echo ErrorMsg('Gagal Pindah',
      "Program dan Program Studi sama dengan data mahasiswa yang lama.<br />
      Berarti mahasiswa tidak dipindahkan.
      <hr size=1 color=silver>
      Pilihan: <a href='?mnux=mhswpindahprodi'>Batal Pindah</a>");
  }
  else {
    // Jika hanya pindah program
    if ($mhsw['ProdiID'] == $PindahProdiID) {
      $s = "update mhsw set ProgramID='$PindahProgramID'
        where MhswID='$mhsw[MhswID]' ";
      $r = _query($s);
      // Tampilkan pesan pemindahan program
      echo Konfirmasi("Pemindahan Program Berhasil",
        "Pemindahan program terhadap mahasiswa <b>$mhsw[MhswID]</b> - <b>$mhsw[Nama]</b>
        telah berhasil dilakukan.<hr size=1 color=silver>
        Pilihan: <a href='?mnux=mhswakd&mhswid=$mhsw[MhswID]&gos=MhswAkdEdt'>Data Akademik</a> |
        <a href='?mnux=mhswpindahprodi'>Kembali ke Pemindahan</a>");
    }
    else {
      // Tahap 1: Copy data dari mhsw lama
      $MhswPindah = $mhsw;
      $MhswPindah['ProdiID'] = $PindahProdiID;
      $MhswPindah['ProgramID'] = $PindahProgramID;
      $MhswID = GetNextNIM($Angkatan, $MhswPindah);
      $StatusAwalID = $_REQUEST['StatusBaru'];
      $BIPOTID = GetaField('bipot', "Def='Y' and ProgramID='$PindahProgramID' and ProdiID", $PindahProdiID, 'BIPOTID');
      $s = "insert into mhsw (MhswID, PMBID, PMBFormJualID,
        BuktiSetoran, TahunID, KodeID, 
        BIPOTID, Autodebet,
        Nama, Foto, StatusAwalID, StatusMhswID,
        ProgramID, ProdiID, PenasehatAkademik,
        Kelamin, WargaNegara, Kebangsaan,
        TempatLahir, TanggalLahir,
        Agama, StatusSipil,

        Alamat, Kota, RT, RW,
        KodePos, Propinsi, Negara,
        Telepon, Handphone, Email,

        AlamatAsal, KotaAsal, RTAsal, RWAsal,
        KodePosAsal, PropinsiAsal, NegaraAsal,
        TeleponAsal,

        NamaAyah, AgamaAyah,
        PendidikanAyah, PekerjaanAyah, HidupAyah,

        NamaIbu, AgamaIbu,
        PendidikanIbu, PekerjaanIbu, HidupIbu,

        AlamatOrtu, KotaOrtu, RTOrtu, RWOrtu,
        KodePosOrtu, PropinsiOrtu, NegaraOrtu,
        TeleponOrtu, HandphoneOrtu, EmailOrtu,

        AsalSekolah, JenisSekolahID,
        AlamatSekolah, KotaSekolah,
        JurusanSekolah, NilaiSekolah, TahunLulus,

        Pilihan1, Pilihan2, Pilihan3,
        Harga, SudahBayar, NA,

        TanggalUjian, LulusUjian, RuangID,
        NomerUjian, NilaiUjian, GradeNilai,
        Syarat, SyaratLengkap,
        BuktiSetoranMhsw, TanggalSetoranMhsw,
        TotalBiayaMhsw, TotalSetoranMhsw,
        Dispensasi, DispensasiID,
        JudulDispensasi, CatatanDispensasi,

        NamaBank, NomerRekening,
        LoginBuat, TanggalBuat)
        
        values ('$MhswID', '$mhsw[PMBID]', '$mhsw[PMBFormJualID]',
        '$mhsw[BuktiSetoran]', '$mhsw[TahunID]', '$mhsw[KodeID]',
        '$BIPOTID', '$mhsw[Autodebet]',
        '$mhsw[Nama]', '$mhsw[Foto]', '$StatusAwalID', '$mhsw[StatusMhswID]',
        '$PindahProgramID', '$PindahProdiID', '$mhsw[PenasehatAkademik]',
        '$mhsw[Kelamin]', '$mhsw[WargaNegara]', '$mhsw[Kebangsaan]',
        '$mhsw[TempatLahir]', '$mhsw[TanggalLahir]',
        '$mhsw[Agama]', '$mhsw[StatusSipil]',

        '$mhsw[Alamat]', '$mhsw[Kota]', '$mhsw[RT]', '$mhsw[RW]',
        '$mhsw[KodePos]', '$mhsw[Propinsi]', '$mhsw[Negara]',
        '$mhsw[Telepon]', '$mhsw[Handphone]', '$mhsw[Email]',

        '$mhsw[AlamatAsal]', '$mhsw[KotaAsal]', '$mhsw[RTAsal]', '$mhsw[RWAsal]',
        '$mhsw[KodePosAsal]', '$mhsw[PropinsiAsal]', '$mhsw[NegaraAsal]',
        '$mhsw[TeleponAsal]',
        
        '$mhsw[NamaAyah]', '$mhsw[AgamaAyah]',
        '$mhsw[PendidikanAyah]', '$mhsw[PekerjaanAyah]', '$mhsw[HidupAyah]',
        
        '$mhsw[NamaIbu]', '$mhsw[AgamaIbu]',
        '$mhsw[PendidikanIbu]', '$mhsw[PekerjaanIbu]', '$mhsw[HidupIbu]',
        
        '$mhsw[AlamatOrtu]', '$mhsw[KotaOrtu]', '$mhsw[RTOrtu]', '$mhsw[RWOrtu]',
        '$mhsw[KodePosOrtu]', '$mhsw[PropinsiOrtu]', '$mhsw[NegaraOrtu]',
        '$mhsw[TeleponOrtu]', '$mhsw[HandphoneOrtu]', '$mhsw[EmailOrtu]',
        
        '$mhsw[AsalSekolah]', '$mhsw[JenisSekolahID]',
        '$mhsw[AlamatSekolah]', '$mhsw[KotaSekolah]',
        '$mhsw[JurusanSekolah]', '$mhsw[NilaiSekolah]', '$mhsw[TahunLulus]',
        
        '$mhsw[Pilihan1]', '$mhsw[Pilihan2]', '$mhsw[Pilihan3]',
        '$mhsw[Harga]', '$mhsw[SudahBayar]', '$mhsw[NA]',
        '$mhsw[TanggalUjian]', '$mhsw[LulusUjian]', '$mhsw[RuangID]',
        '$mhsw[NomerUjian]', '$mhsw[NilaiUjian]', '$mhsw[GradeNilai]',
        '$mhsw[Syarat]', '$mhsw[SyaratLengkap]',
        '$mhsw[BuktiSetoranMhsw]', '$mhsw[TanggalSetoranMhsw]',
        '$mhsw[TotalBiayaMhsw]', '$mhsw[TotalSetoranMhsw]',
        '$mhsw[Dispensasi]', '$mhsw[DispensasiID]',
        '$mhsw[JudulDispensasi]', '$mhsw[CatatanDispensasi]',
        
        '$mhsw[NamaBank]', '$mhsw[NomerRekening]',
        '$_SESSION[_Login]', now()
        )";
      $r = _query($s);

      // Tahap 2: Set Status Lama menjadi Pindah
      $StatusMhswID = $_REQUEST['StatusLama'];
      $s2 = "update mhsw set StatusMhswID='$StatusMhswID', NA='N'
        where MhswID='$mhsw[MhswID]' ";
      $r2 = _query($s2);
      
      //Tahap 3: Buat KHS Mahasiswa
      $MaxSKS = GetaField("prodi", "ProdiID", $PindahProdiID, "TotalSKS")+0;
      
      $sp = "insert into khs (TahunID, KodeID, ProgramID, ProdiID,
        MhswID, StatusMhswID, Sesi, MaxSKS,
        LoginBuat, TanggalBuat)
        values ('$_SESSION[tahun]', '$_SESSION[KodeID]', '$PindahProgramID', '$PindahProdiID',
        '$MhswID', 'P', '1', '$MaxSKS',
        '$_SESSION[_Login]', now()  )";
      
      $rsp = _query($sp);
      // Tahap 4 Copy Hutang Prodi Lama ke Keuangan Prodi Baru
      $Jumlah = GetBipot($mhsw['MhswID'], $_SESSION['tahun'])+0;
      
      if ($Jumlah > 0) {
        $in = "insert into bipotmhsw (MhswID, TahunID, Jumlah, Besar, TrxID, BipotNamaID, Catatan, TanggalBuat, LoginBuat)
                values ('$MhswID', '$_SESSION[tahun]', 1, '$Jumlah', 1, '30', 'Hutang Pindah Prodi', NOW(), '$_SESSION[_Login]')";
        $rin = _query($in);
      }
      // Tampilkan pesan
      echo Konfirmasi("Proses Pemindahan Berhasil",
        "Mahasiswa telah dipindahkan ke Program/Program Studi baru dengan NPM
        baru, yaitu: <b>$MhswID</b>.
        <hr size=1 color=silver>
        Pilihan: <a href='?mnux=mhswakd&mhswid=$MhswID&gos=MhswAkdEdt'>Data Akademik</a> |
        <a href='?mnux=mhswpindahprodi'>Kembali ke Pemindahan</a>");
    }

  }
}

function GetBipot($MhswID, $TahunID){
  $s = "Select BipotNamaID, (Jumlah * Besar) as Biaya, TrxID, Dibayar
        From bipotmhsw
        where MhswID = '$MhswID' 
        and TahunID = '$TahunID'
        order By BipotNamaID";
  $r = _query($s);
  
  while ($w = _fetch_array($r)) {
    if ($w['TrxID'] > 0) {
      $Balance = $w['Biaya'] - $w['Dibayar'];
    } else {
      $Pot     = $w['Biaya'];
    }
    
    $Jumlah = $Balance - $Pot;
    $Jumlah += $Jumlah;
  }
  if ($Jumlah > 0) {
    $in = "insert into bipotmhsw (MhswID, TahunID, Jumlah, Besar, TrxID, BipotNamaID, Catatan, TanggalBuat, LoginBuat)
                values ('$MhswID', '$TahunID', 1, '$Jumlah', -1, '31', 'Transfer Pindah Prodi', NOW(), '$_SESSION[_Login]')";
    $rin = _query($in);
  }
  return $Jumlah;
}

// *** Parameters ***
$mhswid = GetSetVar('mhswid');
$gos = (empty($_REQUEST['gos']))? 'KonfirmasiPindah' : $gos;


// *** Main ***
TampilkanJudul("Mahasiswa Pindah Prodi");
if (!empty($mhswid)) {
  $mhsw = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID",
    'm.MhswID', $mhswid,
    "m.*, prg.Nama as PRG, prd.Nama as PRD, sm.Nama as SM, sm.Keluar");
  if ($mhsw['Keluar'] == 'Y')
    echo ErrorMsg("Tidak Dapat Dipindahkan",
      "Status Mahasiswa: <b>$mhsw[SM]</b> yang berarti sudah tidak
      dapat dipindah lagi.
      <hr size=1 color=silver>
      Pilihan: <a href='?mnux=mhswpindahprodi'>Kembali</a>");
  else $gos($mhsw);
}
?>
