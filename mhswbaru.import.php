<?php
// Author: Emanuel Setio Dewo
// 19 March 2006

function SetPasswordMhsw($tgl) {
  $tmp = explode('-', $tgl);
  
  $tanggal = '';
  $bulan   = '';
  $tahun   = '';
  
  $tanggal = $tmp[2];
  $bulan   = $tmp[1];
  $tahun   = $tmp[0];
  
  $thn2digit = substr($tahun, -2);
  
  $pass = "$tanggal" . "$bulan" . "$thn2digit";
  
  return $pass;
}

function ImportPMB($w, $TahunID='') {
  $TahunID = trim($TahunID);
  $TahunID = (strlen($TahunID) <= 4) ? $TahunID . '1' : $TahunID;
  $untukNim = substr($TahunID, 0, 4);
  $MhswID  = '';
  $MhswID = GetNextNIM($untukNim, $w);
  //$StatusMhswID = GetaField('statusmhsw', 'Def', 'Y', 'StatusMhswID');
  $StatusMhswID = 'A';
  if (empty($TahunID)) {
    $TahunID = GetaField('tahun',
      "KodeID='$_SESSION[KodeID]' and ProgramID='$w[ProgramID]' and ProdiID='$w[ProdiID]' and NA",
      'N', 'TahunID');
  }
  // Hitung tahun batas tahun
  $BatasStudi = HitungBatasStudi($TahunID, $w['ProdiID']);
  $Password = SetPasswordMhsw($w['TanggalLahir']);
  
  // Oh, iya, status mahasiswa selalu diset "AKTIF"
  $w['StatusMhswID'] = 'A';
  // Fase 1: import data
  $s = "insert into mhsw (MhswID, Login, Password,
    PMBID, StatusMhswID,
    PMBFormJualID, BuktiSetoran, TahunID, KodeID,
    BIPOTID, Nama, StatusAwalID, ProgramID, ProdiID,
    Kelamin, WargaNegara, Kebangsaan,
    TempatLahir, TanggalLahir,
    Agama, StatusSipil,
    Alamat, Kota, RT, RW,
    KodePos, Propinsi, Negara,
    Telepon, Handphone, Email,
    AlamatAsal, KotaAsal,
    RTAsal, RWAsal, TeleponAsal,
    KodePosAsal, PropinsiAsal, NegaraAsal,
    NamaAyah, AgamaAyah, PendidikanAyah, PekerjaanAyah, HidupAyah,
    NamaIbu, AgamaIbu, PendidikanIbu, PekerjaanIbu, HidupIbu,
    AlamatOrtu, KotaOrtu, RTOrtu, RWOrtu,
    KodePosOrtu, PropinsiOrtu, NegaraOrtu,
    TeleponOrtu, HandphoneOrtu, EmailOrtu,
    AsalSekolah, JenisSekolahID,
    AlamatSekolah, KotaSekolah,
    NilaiSekolah, JurusanSekolah, TahunLulus,
    AsalPT, ProdiAsalPT, LulusAsalPT, TglLulusAsalPT,
    Pilihan1, Pilihan2, Pilihan3,
    Harga, SudahBayar, NA, TanggalUjian, LulusUjian,
    RuangID, NomerUjian,
    NilaiUjian, GradeNilai, BatasStudi,
    BuktiSetoranMhsw, TanggalSetoranMhsw, TotalSetoranMhsw, TotalBiayaMhsw,
    Dispensasi, DispensasiID, JudulDispensasi, CatatanDispensasi,
    LoginBuat, TanggalBuat)

    values ('$MhswID', '$MhswID', PASSWORD('$Password'),
    '$w[PMBID]', '$StatusMhswID',
    '$w[PMBFormJualID]', '$w[BuktiSetoran]', '$TahunID', '$w[KodeID]',
    '$w[BIPOTID]', '$w[Nama]', '$w[StatusAwalID]', '$w[ProgramID]', '$w[ProdiID]',
    '$w[Kelamin]', '$w[WargaNegara]', '$w[Kebangsaan]',
    '$w[TempatLahir]', '$w[TanggalLahir]',
    '$w[Agama]', '$w[StatusSipil]',
    '$w[Alamat]', '$w[Kota]', '$w[RT]', '$w[RW]',
    '$w[KodePos]', '$w[Propinsi]', '$w[Negara]',
    '$w[Telepon]', '$w[Handphone]', '$w[Email]',
    '$w[AlamatAsal]', '$w[KotaAsal]',
    '$w[RTAsal]', '$w[RWAsal]', '$w[TeleponAsal]',
    '$w[KodePosAsal]', '$w[PropinsiAsal]', '$w[NegaraAsal]',
    '$w[NamaAyah]', '$w[AgamaAyah]', '$w[PendidikanAyah]', '$w[PekerjaanAyah]', '$w[HidupAyah]',
    '$w[NamaIbu]', '$w[AgamaIbu]', '$w[PendidikanIbu]', '$w[PekerjaanIbu]', '$w[HidupIbu]',
    '$w[AlamatOrtu]', '$w[KotaOrtu]', '$w[RTOrtu]', '$w[RWOrtu]',
    '$w[KodePosOrtu]', '$w[PropinsiOrtu]', '$w[NegaraOrtu]',
    '$w[TeleponOrtu]', '$w[HandphoneOrtu]', '$w[EmailOrtu]',
    '$w[AsalSekolah]', '$w[JenisSekolahID]',
    '$w[AlamatSekolah]', '$w[KotaSekolah]',
    '$w[NilaiSekolah]', '$w[JurusanSekolah]', '$w[TahunLulus]',
    '$w[AsalPT]', '$w[ProdiAsalPT]', '$w[LulusAsalPT]', '$w[TglLulusAsalPT]',
    '$w[Pilihan1]', '$w[Pilihan2]', '$w[Pilihan3]',
    '$w[Harga]', '$w[SudahBayar]', '$w[NA]',
    '$w[TanggalUjian]', '$w[LulusUjian]',
    '$w[RuangID]', '$w[NomerUjian]',
    '$w[NilaiUjian]', '$w[GradeNilai]', '$BatasStudi',
    '$w[BuktiSetoranMhsw]', '$w[TanggalSetoranMhsw]', '$w[TotalSetoranMhsw]', '$w[TotalBiayaMhsw]',
    '$w[Dispensasi]', '$w[DispensasiID]', '$w[JudulDispensasi]', '$w[CatatanDispensasi]',
    '$_SESSION[_Login]', now())";
  $r = _query($s);

  // Fase 2: update data PMB
  $s1 = "update pmb set NIM='$MhswID', BIPOTID='$w[BIPOTID]' where PMBID='$w[PMBID]' ";
  $r1 = _query($s1);

  // Fase 3: Import BIPOT ISI
  ImportBIPOT($w, $MhswID, $TahunID);
  // Fase 4: check Cicilan & Import cicilan
  ImportCicilan($w, $MhswID, $TahunID);
  // Fase 5: Import Pembayaran
  ImportPembayaran($w, $MhswID, $TahunID);
  // Fase 6: Buat KHS
  BuatKHS($w, $MhswID, $TahunID);

  // Kembalikan ID
  return $MhswID;
  //$s = "insert into mhsw
}
function ImportBIPOT($w, $MhswID, $TahunID) {
  $s = "update bipotmhsw set MhswID='$MhswID', TahunID='$TahunID',
    PMBMhswID=1,
    LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
    where PMBID='$w[PMBID]'";
  $r = _query($s);
}
function ImportCicilan($w, $MhswID, $TahunID='') {
  $s = "update cicilanmhsw set MhswID='$MhswID', TahunID='$TahunID',
    PMBMhswID=1,
    LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
    where PMBID='$w[PMBID]'";
  $r = _query($s);
}
function ImportPembayaran($w, $MhswID, $TahunID) {
  $s = "update bayarmhsw set MhswID='$MhswID', TahunID='$TahunID',
    PMBMhswID=1,
    LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
    where PMBID='$w[PMBID]'";
  $r = _query($s);
}
function BuatKHS($w, $MhswID, $TahunID) {
  global $KodeID;
  $MaxSKS = GetaField("prodi", "ProdiID", $w['ProdiID'], "TotalSKS")+0;
  $s = "insert into khs
    (TahunID, KodeID, ProgramID, 
    ProdiID, MhswID, StatusMhswID,
    Sesi, MaxSKS)
    values ('$TahunID', '$KodeID', '$w[ProgramID]',
    '$w[ProdiID]', '$MhswID', 'P',
    1, '$MaxSKS')";
  $r = _query($s);
}

?>
