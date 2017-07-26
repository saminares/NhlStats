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



  $getTeams = $pdo->prepare("SELECT id,hockey_reference_id from Teams");
  try{
    $getTeams->execute();
  }catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
  }

  $teams = $getTeams->fetchAll(PDO::FETCH_ASSOC);


  $getPlayers = $pdo->prepare("SELECT id,hockey_reference_id from Players");
  try{
    $getPlayers->execute();
  }catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
  }

  $players = $getPlayers->fetchAll(PDO::FETCH_ASSOC);






function crawl_page($url){

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
}






foreach ($players as $player){
      $stats = array();
      $url_folder = substr($player['hockey_reference_id'],0,1);
      $full_url = 'http://www.hockey-reference.com/players/'.$url_folder.'/'.$player['hockey_reference_id'].'/gamelog/2017';
      echo $full_url;
      exit();
      $stats = crawl_page($full_url, 1);

      //TODO give date and team names to function, return game id
      $game_id = getGameId($date,$home_team, $away_team);

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
