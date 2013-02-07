<?php
  function Headerxx($tahun, $prodi, $div, $maxcol, &$hal){
    global $_lf;
		$hal++;
	  $hdr = str_pad('*** DAFTAR MAHASISWA BARU **', $maxcol, ' ', STR_PAD_BOTH) . $_lf. $_lf. $_lf;
		$hdr .= "Tahun Akademik :" . NamaTahun($tahun) . $_lf;
		$hdr .= "Prodi          : $prodi" . str_pad('Halaman : ' . $hal, 42, ' ', STR_PAD_LEFT) . $_lf;
		$hdr .= $div;
		$hdr .= str_pad("NO", 5) . 
            str_pad("PMBID", 12) . 
            str_pad("NIM/NPM", 12) . 
            str_pad('NAMA', 35) . 
            str_pad("PROGRAM", 12) .
            str_pad("STATUS", 10) . 
            $_lf;
		$hdr .= $div;
		
		return $hdr;
  }
  
  function Daftar(){
    global $_lf;
    if(empty($_SESSION['prodi'])){
      $__prodi = '';
    } else $__prodi = "and prd.ProdiID = '$_SESSION[prodi]'";
    $__prg = (empty($_SESSION['prid'])) ? '' : "and prg.ProgramID = '$_SESSION[prid]'";
    $s = "select p.PMBID, p.Nama, p.NIM, p.ProdiID, p.LulusUjian, p.NilaiUjian,
      prg.Nama as PRG, prd.Nama as PRODI, sa.Nama as STT, sa.TanpaTest,
      bp.Nama as BPT
      from pmb p
      left outer join program prg on p.ProgramID=prg.ProgramID
      left outer join prodi prd on p.ProdiID=prd.ProdiID
      left outer join statusawal sa on p.StatusAwalID=sa.StatusAwalID
      left outer join bipot bp on p.BIPOTID=bp.BIPOTID
      where p.PMBPeriodID like '%$_SESSION[tahun]%' and p.NIM <> 0 $__prodi $__prg
      order by prd.ProdiID, p.NIM";     
    $r = _query($s);
    $maxcol = 90;
    $maxbrs = 53;
    $hal = 0;
    $brs = 54;
    $first = 1;
    $nmf = "tmp/$_SESSION[_Login].dwoprn";
    $f = fopen($nmf, 'w');
    fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(10));
    $div = str_pad('-', $maxcol, '-').$_lf;
    $n = 0;
    while($w = _fetch_array($r)){
      
      $_prodi = GetaField('prodi', 'ProdiID', $w['ProdiID'], 'Nama');
		  $n++; $brs++;
		
		  if ($brs > $maxbrs) {
			 if ($first == 0) {
				  fwrite($f, $div.chr(12));
			 }
			   $hd = Headerxx($_SESSION['tahun'], $_prodi, $div, $maxcol, $hal);
			   fwrite($f, $hd);
			   $brs = 0;
			   $first = 0;
			   $prodi = $w['ProdiID'];
		  } 		
		  elseif ($prodi != $w['ProdiID']) {
        $prodi = $w['ProdiID'];
				if ($first == 0){
					fwrite($f, $div);
				}
				fwrite($f, chr(12));
				fwrite($f, Headerxx($_SESSION['tahun'], $_prodi, $div, $maxcol, $hal));
				$brs=0;
				$n=1;
      } 
      
        $isi =  str_pad($n . '.', 5) .
                str_pad($w['PMBID'], 12) .
                str_pad($w['NIM'], 12) . 
                str_pad($w['Nama'], 35) .
                str_pad($w['PRG'], 12) . 
                str_pad($w['STT'], 10) . $_lf;
        fwrite($f, $isi);
    }
    fwrite($f, $div); 
    //fwrite($f, str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
    fwrite($f, str_pad("Dicetak oleh : " . $_SESSION['_Login'], 20, ' ') . str_pad("Dicetak Tgl : " . date("d-m-Y H:i"), 70,' ', STR_PAD_LEFT).$_lf.$_lf); 
    fwrite($f, str_pad("Akhir laporan", 90, ' ', STR_PAD_LEFT));
    fwrite($f, chr(12));
    fclose($f);
    TampilkanFileDWOPRN($nmf, "baa.lap");     
  }

$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$tahun = GetSetVar('tahun');  
TampilkanJudul("Laporan Jumlah Mahasiswa Generate NIM");  
TampilkanTahunProdiProgram('baa.lap.jumlahmhswnim', 'Daftar');
Daftar();  
?>
                                                                       
