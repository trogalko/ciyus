<?php
function FilterMundur(){
  global $arrID;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='KodeID' value='$arrID[Kode]'>
  <input type=hidden name='mnux' value='inq.mhswbaru.mundur'>
  <input type=hidden name='gos' value='daftar'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>Gelombang :</td>
    <td class=ul><input type=text name='periode' value='$_SESSION[periode]' size=15></td></tr>
  <tr><td class=inp1>Cari Calon Mhsw:</td>
    <td class=ul><input type=text name='pmbid' value='$_SESSION[pmbid]' size=20 maxlength=50>
    <input type=submit name='Cari' value='PMBID'>
    <input type=submit name='Cari' value='Nama'></td></tr>
  </form></table><p>";
}
  
function daftar(){
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['mhswpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=inq.mhswbaru.mundur&mhswpage==PAGE='>=PAGE=</a>";

  $lst->tables = "pmbmundur 
                  left outer join pmb on pmbmundur.PMBID = pmb.PMBID
                  left outer join prodi p on pmb.ProdiID = p.ProdiID
                  left outer join StatusAwal sa on pmb.StatusAwalID = sa.StatusAwalID
                  where pmb.PMBPeriodID = '$_SESSION[periode]' and 
                  pmb.$_SESSION[Cari] like '%$_SESSION[pmbid]%'
                  order by pmb.$_SESSION[Cari]";
  $lst->fields = "pmbmundur.*, pmb.Nama as MHSW, p.Nama as PRD, sa.Nama as SA";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>No.</th>
    <th class=ttl>PMBID</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Program Studi</th>
    <th class=ttl>Status</th>
    <th class=ttl>No. Surat</th>
    <th class=ttl>Tanggal Proses</th>
    </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=inp1>=NOMER=</td>
    <td class=ul><a href='javascript:DetailPMB(=PMBID=)'>=PMBID=</a></td>
    <td class=ul nowrap>=MHSW=</td>
    <td class=ul>=PRD=</td>
    <td class=ul>=SA=</td>
    <td class=ul>=NoSurat=</td>
    <td class=ul>=TglProses=</td>
    </tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
     echo "<SCRIPT LANGUAGE='JavaScript1.2'>
      <!--
      function DetailPMB(PMBID){
        lnk = \"pmb.inq.det.php?PMBID=\"+PMBID;
        win2 = window.open(lnk, '', 'width=600, height=600, scrollbars, status');
        win2.creator = self;
      }
      -->
      </script>
      ";
}

$pmbid = GetSetVar('pmbid');
$Cari = GetSetVar('Cari');
$TahunNIM = GetSetVar('TahunNIM');
$periode = GetSetVar('periode');

TampilkanJudul("Inquiry Mahasiswa Baru Mundur");
FilterMundur();
if (!empty($periode)) daftar();
?>
