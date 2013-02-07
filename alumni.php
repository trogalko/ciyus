<?php
// Author: Emanuel Setio Dewo
// 31 May 2006
// www.sisfokampus.net

// *** Functions ***
function TampilkanCariAlumni($mnux='alumni', $gos='DftrAlumni') {
  echo "<p><table class=box>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <tr><td class=ul colspan=2><b>$_SESSION[KodeID]</b></td></tr>
  <tr><td class=inp>Cari alumni</td>
      <td class=ul><input type=text name='_alumnival' value='$_SESSION[_alumnival]' size=20 maxlength=50>
      <input type=submit name='_alumnikey' value='Nama'>
      <input type=submit name='_alumnikey' value='NPM'>
      <input type=button name='Reset' value='Reset' onClick=\"location='?mnux=$mnux&gos=&_alumnival=&_alumnikey='\">
      </td></tr>
  </form></table></p>";
}
function DftrAlumni() {
  $order = (empty($_SESSION['_alumnikey']))? 'MhswID' : ($_SESSION['_alumnikey'] == 'NPM')? 'MhswID' : $_SESSION['_alumnikey'];
  $_whr = (empty($_SESSION['_alumnival']))? '' : "and m.$order like '%$_SESSION[_alumnival]%' "; 
  // tampilkan
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['_alumnipage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=alumni&_alumnipage==PAGE='>=PAGE=</a>";

  $lst->tables = "mhsw m
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join program prg on m.ProgramID=prg.ProgramID
    where m.StatusMhswID='L'
      $_whr
    order by m.$order";
  $lst->fields = "m.MhswID, m.Nama, m.StatusAwalID, m.StatusMhswID,
    m.Telepon, m.Handphone, m.Email,
    m.ProgramID, m.ProdiID, m.Alamat, m.Kota,
    m.TahunKeluar, prd.Nama as PRD";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>No.</th>
    <th class=ttl>NPM</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Tahun<br />Lulus</th>
    <th class=ttl>Program<br />Studi</th>
    <th class=ttl>Telp, HP</th>
    <th class=ttl>Alamat</th>
    </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=inp1>=NOMER=</td>
    <td class=ul><a href='?mnux=alumni.det&AlumniID==MhswID='><img src='img/edit.png'>
    =MhswID=</a></td>
    <td class=ul nowrap>=Nama=</td>
    <td class=ul>=TahunKeluar=&nbsp;</td>
    <td class=ul>=ProgramID= - =ProdiID=</td>
    <td class=ul>=Telepon=, =Handphone=</td>
    <td class=ul>=Alamat=, =Kota=</td>
    </tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";  
}

// *** Parameters ***
$_alumnikey = GetSetVar('_alumnikey', 'Nama');
$_alumnival = GetSetVar('_alumnival');
$_alumnipage = GetSetVar('_alumnipage', 1);
$gos = (empty($_REQUEST['gos']))? "DftrAlumni" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Alumni");
TampilkanCariAlumni();
$gos();
?>
