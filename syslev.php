<?php
// Author: Emanuel Setio Dewo
// 2005-12-27

// *** Functions ***
function DftrLevel() {
  $s = "select * from level where Accounting = 'N' order by LevelID";
  $r = _query($s);
  echo "<table class=box cellspacing=1 cellpadding=4>
    <tr><td class=ul colspan=6>Read Only</td></tr>
    <tr><th class=ttl>ID</th>
    <th class=ttl>Level</th><th class=ttl>Tabel User</th>
    <th class=ttl>Super</th><th class=ttl>Tampak</th><th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td $c>$w[LevelID]</td>
      <td $c>$w[Nama]</td>
      <td $c>$w[TabelUser]</td>
      <td $c align=center><img src='img/$w[Superuser].gif'></td>
      <td $c align=center><img src='img/$w[Tampak].gif'></td>
      <td $c align=center><img src='img/$w[NA].gif'></td>
      </tr>";
  }
  echo "</table>";
}

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? 'DftrLevel' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Level User");
$gos();
?>
