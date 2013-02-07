<?php


function GetDataMahasiswaKlinik(){
  $s = "select MhswID, KlinikID from klinik order by MhswID";
  $r = _query($s);
  
  while ($d = _fetch_array($r)){
    $w = GetFields('mhsw', 'MhswID', $d['MhswID'], '*');
    
    $s0 = "insert into mhsw (MhswID, Login, Password,
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

    values ('$d[KlinikID]', '$d[KlinikID]', PASSWORD('$w[TanggalLahir]'),
    '$w[PMBID]', '$StatusMhswID',
    '$w[PMBFormJualID]', '$w[BuktiSetoran]', '2006', '$w[KodeID]',
    '$w[BIPOTID]', '$w[Nama]', '$w[StatusAwalID]', '$w[ProgramID]', '11',
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
    
    $r0 = _query($s0);
  }
} 

function BuatKHS() {
  //global $KodeID;
  $s = "select MhswID, KlinikID from klinik order by MhswID";
  $r = _query($s);
  
  while ($d = _fetch_array($r)){
  $Program = GetaField('Mhsw', 'MhswID', $d['KlinikID'], 'ProgramID');
  $MaxSKS = GetaField("prodi", "ProdiID", '11', "TotalSKS")+0;
  $s0 = "insert into khs
    (TahunID, KodeID, ProgramID, 
    ProdiID, MhswID, StatusMhswID,
    Sesi, MaxSKS)
    values ('20061', 'UKRIDA', '$Program',
    '11', '$d[KlinikID]', 'A',
    1, '$MaxSKS')";
  $r0 = _query($s0);
  }
}

include "sisfokampus.php";
HeaderSisfoKampus("Cek Data Mhsw Baru");
TampilkanJudul("Cek Data Mahasiswa Baru");
//GetDataMahasiswaKlinik();
BuatKHS()
?>
