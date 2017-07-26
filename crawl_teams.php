<?php
//use PDO;
try{
  $pdo = new PDO(
      "mysql:host=localhost;dbname=nhlstats",
      "root",
      "vagrant"
  );
}catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}



function crawl_page($url, $depth = 5)
{
    static $seen = array();
    if (isset($seen[$url]) || $depth === 0) {
        return;
    }
    $seen[$url] = true;

    //OLD DOM
    $dom = new DOMDocument('1.0');
    @$dom->loadHTMLFile($url);
    $xpath = new DOMXpath($dom);

    $team_row = $xpath->query('//th[contains(@class, "left")]');
    $i = 1;
    $names = array();
    foreach ($team_row as $column){
        if($column->tagName == "th"){

          foreach($column->childNodes as $asd){
            if($asd->tagName == "a"){
                $id = (string)$asd->getAttribute('href');
                $id = substr($id,7,3);
                $names[$i]['id'] = $id;
            }

          }

          if((string)$column->textContent == "Lg" ||(string)$column->textContent == "Franchise"){
            continue;
          }
          $names[$i]['name'] = (string)$column->textContent;
          if((string)$column->textContent == "Winnipeg Jets"){
            break;
          }

        }
        $i ++;

    }
    return $names;
}

$all_names = array();
$teams = crawl_page("http://www.hockey-reference.com/teams/", 1);
var_dump($teams);

foreach ($teams as $team){

  $hri = $team['id'];
  $name = $team['name'];

  $insert = $pdo->prepare("INSERT INTO Teams (name, hockey_reference_id) VALUES(:name, :hockey_reference_id)");
  $insert->bindParam(':name', $name);
  $insert->bindParam(':hockey_reference_id', $hri);
  try{
    $insert->execute();
  }catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
  }

}


//print_r($all_names);
