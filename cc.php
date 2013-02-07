<?php
function daftar (){
  $s = "SELECT GradeNilai, mk.MKKode, mk.Nama, count(GradeNilai) 
        from krs left outer join mk on krs.MKID = mk.MKID 
        where krs.tahunid = $_SESSION[tahun] and 
        krs.gradenilai in (select nama from nilai where prodiid = $_SESSION[prodi]) 
        and mk.prodiID = $_SESSION[prodi] group by krs.MKKode, krs.GradeNilai order by Gradenilai ASC";
  $r = _query($s);
  $arrGrade = GetArrayNilai("select concat(ProdiID, '~', Nama) as Grade from Nilai where ProdiID = $_SESSION[prodi] order by Nama", "Grade");
  $banyakGrade = sizeof($arrGrade);
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl rowspan=2>Kode</th>
    <th class=ttl rowspan=2>Nama</th>
    <th class=ttl colspan=$banyakGrade>Grade</th>
    </tr>";
  for ($i=0; $i< $banyakGrade; $i++) {
    $str = explode('~', $arrGrade[$i]);
    $hdrGrd .= "<th class=ttl title='$str[1]'>$str[0]</th>";
  }
  echo "<tr>$hdrGrd</tr>";
  
  while ($w = _fetch_array($r)){
    echo "<tr><td class=ul>$w[MKKode]</td><td class=ul>$w[Nama]</td></tr>";
  }
  echo "</table></p>"
}

function Filter(){
  if (!empty($_SESSION['prodi'])) $whr[] = "m.ProdiID='$_SESSION[prodi]'";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='akd.lap.rekapgrade'>
  <input type=hidden name='gos' value='daftar'>
  <tr><td class=wrn>$_SESSION[KodeID]</td>
    <td class=inp>Tahun Akademik : </td>
    <td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=50></td>
    <td class=inp colspan=2>Program</td><td class=ul><select name='prid' onChange='this.form.submit()'>$optprg</select></td>
    <td class=inp>Program Studi</td><td class=ul><select name='prodi' onChange='this.form.submit()'>$optprd</select></td></tr>
  </form></table></p>";
}
?>
