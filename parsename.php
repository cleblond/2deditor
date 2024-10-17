<?php



//$smiles_clean=str_replace('#' ,'%23' , $smiles );


//var_dump($_POST);



$response = array();
$smiles = explode(".", $_POST['smiles']);


foreach ($smiles as $smile) {


$url = "http://cactus.nci.nih.gov/chemical/structure/".$smile."/iupac_name";
//$url="http://cactus.nci.nih.gov/chemical/structure/C1%3DCC%3DCC%3DC1/iupac_name";

$response[] = file_get_contents($url);



}




$string = implode("\n",$response);

echo $string;



