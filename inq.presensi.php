<?php

function FilterInqMhswPerMK(){
	global $arrID;
	echo "<p class=noprint><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='inq.pesertakuliah'>
  <input type=hidden name='gos' value='TampilkanDaftarMhsw'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>Tahun Akademik</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10>
    <input type=submit name='Tentukan' value='Tentukan'></td></tr>
	<tr><td class=inp1>Kode MK :</td><td class=ul><input type=text name=KodeMK value='$_SESSION[KodeMK]' size=15></td></tr>
  <tr><td class=inp1>Seksi/Kelas :</td><td class=ul><input type=text name=Kelas value='$_SESSION[Kelas]' size=15></td></tr>
	<td class=inp1>Filter: </td><td class=ul><select name='jenjad' onChange='this.form.submit()'>$optjenjad</select></td></tr>
	</form></table></p>";
}

function InqPresPerMK(){

	include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['mhswpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=mhsw&mhswpage==PAGE='>=PAGE=</a>";
	$lst->tables = "jadwal j left outer join dosen d on d.Login = j.DosenID 
									where j.TahunID = '$_SESSION[tahun]'
									and j.NameKelas <> 'KLINIK'
									Order by j.MKKode, j.NamaKelas";
  $lst->fields = "j.*, d.Nama as Dosen";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>No.</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Mata Kuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>Jenis</th>
    <th class=ttl>Dosen</th>
    <th class=ttl>Persen</th>
    </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=inp1>=NOMER=</td>
    <td class=cna>=MKKode=</td>
    <td class=cna>=Nama=</td>
    <td class=cna>=NamaKelas=</td>
    <td class=cna>=SM=</td>
    <td class=cna>=Telepon=/=Handphone=</td>
    <td class=cna>=Alamat=, =Kota=</td>
    </tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
	
	$s = "select j.*, LEFT(j.Nama, 30) as NM
    from jadwal j
    where j.TahunID='$tahun'
      and INSTR(j.ProgramID, '.$prid.')>0
      and INSTR(j.ProdiID, '.$prodi.')>0
      and j.NamaKelas <> 'KLINIK'
    order by j.MKKode, j.NamaKelas";
  $r = _query($s);
}

?>