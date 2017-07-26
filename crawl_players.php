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



function crawl_page($url, $depth = 5){
  /*
    static $seen = array();
    if (isset($seen[$url]) || $depth === 0) {
        return;
    }0
    $seen[$url] = true;
    */
    $players = Array();
    $dom = new DOMDocument('1.0');
   @$dom->loadHTMLFile($url);
    $xpath = new DOMXpath($dom);
    $nodes = $xpath->query("//table[@id ='roster']//tbody//tr");
    $i = 0;
    foreach($nodes as $node){
      foreach($node->childNodes as $row){
        if($row->tagName == "td"){
          $data = $row->getAttribute("data-append-csv");
          if(strlen($data) > 0 ){
          $players[$i]['hr_id'] = $row->getAttribute("data-append-csv");
          $hr_player_full_name = explode(",",$row->getAttribute("csk"));
          $players[$i]['last_name'] = $hr_player_full_name[0];
          $players[$i]['first_name'] = $hr_player_full_name[1];
          $i ++;
          }
        }
      }


    }
    return  $players;
/*
    foreach ($team_row as $column){
        if($column->tagName == "th"){

          foreach($column->childNodes as $asd){
            if($asd->tagName == "a"){
                $id = $asd->getAttribute('href');
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
    */
}


  $getTeams = $pdo->prepare("SELECT * from Teams");
  try{
    $getTeams->execute();
  }catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
  }

  $teams = $getTeams->fetchAll(PDO::FETCH_ASSOC);


foreach ($teams as $team){
      $players = array();
      $players = crawl_page('http://www.hockey-reference.com/teams/'.$team['hockey_reference_id'].'/2017.html#all_roster', 1);

      foreach ($players as $player){
        $insert = $pdo->prepare("INSERT INTO Players (team_id, first_name,last_name,hockey_reference_id) VALUES(:team_id, :first_name, :last_name, :hockey_reference_id)");
        $insert->bindParam(':team_id', $team['id']);
        $insert->bindParam(':first_name', $player['first_name']);
        $insert->bindParam(':last_name', $player['last_name']);
        $insert->bindParam(':hockey_reference_id', $player['hr_id']);
        try{
          $insert->execute();
        }catch (PDOException $e) {
          die("VIRHE: " . $e->getMessage());
        }
      }
}






//print_r($all_names);
