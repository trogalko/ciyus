<?php
// Author: Emanuel Setio Dewo
// 18 Oktober 2006

include_once "carimhsw.php";
// *** Functions ***

// *** Parameters ***
$crmhswkey = GetSetVar('crmhswkey');
$crmhswval = GetSetVar('crmhswval');

// *** Main ***
TampilkanJudul("Skorsing");
CariMhsw('skorsing');
if (!empty($_SESSION['crmhswval'])) DaftarMhswSkorsing('skorsing.det', "gos=");


// ** Functions **
function DaftarMhswSkorsing($mnux='', $gos='') {
  $inqMhswPage = GetSetVar('inqMhswPage');
  $arrKey = array('NPM'=>'MhswID', 'Nama'=>'Nama', 'Semua'=>'');
  $whr = '';

  if (!empty($arrKey[$_SESSION['crmhswkey']]) && !empty($_SESSION['crmhswval']))
    $whr = "m." . $arrKey[$_SESSION['crmhswkey']] . " like '%" . $_SESSION['crmhswval'] . "%' ";
  $whr = (empty($whr))? '' : "where " . $whr;
  $maxdata = 40;
  // Data
  $s = "select m.MhswID, m.Nama, m.ProgramID, m.ProdiID,
    prg.Nama as PRG, prd.Nama as PRD, sm.Nama as SM, sm.Keluar
    from mhsw m
      left outer join program prg on m.ProgramID=prg.ProgramID
      left outer join prodi prd on m.ProdiID=prd.ProdiID
      left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    $whr
    order by m.MhswID
    limit $maxdata";
  $r = _query($s);
  // Tampilkan
  $jmldata = _num_rows($r);
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>#</th>
    <th class=ttl>NPM</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Program</th>
    <th class=ttl>Status</th>
    <th class=ttl>Skorsing</th>
    </tr>";
  $n = 0;
  while ($w = _fetch_array($r)) {
    if ($w['Keluar'] == "Y") {
      $c = "class=nac";
      $skr = '';
      $strMhswID = $w['MhswID'];
    }
    else {
      $c = "class=ul";
      $strMhswID = "<a href='?mnux=$mnux&mhswid=$w[MhswID]'>$w[MhswID]</a>";
      $sqlskorsing = "select TahunID
        from khs
        where MhswID='$w[MhswID]'
          and StatusMhswID='S'
        order by TahunID";
      $skr = GetArrayTable($sqlskorsing, 'TahunID', 'TahunID', ', ', '');
    }
    $n++;
    echo "<tr>
      <td class=inp>$n</td>
      <td $c>$strMhswID</td>
      <td $c>$w[Nama]</td>
      <td $c>$w[PRG]</td>
      <td $c>$w[SM]</td>
      <td $c>$skr &nbsp;</td>
    </tr>";
  }
  echo "</table></p>";
  if ($jmldata >= $maxdata)
    echo "<p>*) Data yang ditampilkan dibatasi <font size=+1>$maxdata</font> mhsw.</p>";
}
?>
