<?php

require_once('classes/Team.php');
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



function crawl_page($url,$pdo){

  $games = Array();
  $dom = new DOMDocument('1.0');
  @$dom->loadHTMLFile($url);
  $xpath = new DOMXpath($dom);
  $rows = $xpath->query("//table[@class ='sortable stats_table now_sortable sliding_cols']");
  $i = 0;
  echo $url;
foreach ($rows as $row){
  echo $row->nodeValue;
}
/*
    foreach($rows as $td){
        $td_data = $td->getAttribute("data-stat");
        if($td_data == "game_location" && (string)$td->textContent == "@" ){
          continue 1;
        } else if($td_data == "date_game"){
          $games[$i]['date'] = (string)$td->textContent;
        } else if($td_data == "opp_name"){
          $hr_team_id = substr((string)$td->getAttribute("csk"),0,3);
          $games[$i]['opponent_id'] = Team::get_id($hr_team_id,$pdo);
        } else if($td_data == "goals"){
          $games[$i]['goals'] = (string)$td->textContent;
        } else if($td_data == "opp_goals"){
          $games[$i]['opponent_goals'] =(string)$td->textContent;
        } else if($td_data == "overtimes" && (string)$td->textContent == "OT"){
          $games[$i]['ot'] = 1;
        } else if($td_data == "overtimes" && (string)$td->textContent == "SO"){
          $games[$i]['so'] = 1;
        }
    }
    $i++;
  **/
  return  $games;
}






foreach ($teams as $team){
      //$team_class = new Team;
      $games = array();
      $url_folder = substr($team['hockey_reference_id'],0,1);
      $full_url = 'http://www.hockey-reference.com/teams/'.$team['hockey_reference_id'].'/2017_games.html';
      $games = crawl_page($full_url, $pdo);
      var_dump($games);
      exit();

/*
        $insert = $pdo->prepare("INSERT INTO Games (team_id, first_name,last_name,hockey_reference_id) VALUES(:team_id, :first_name, :last_name, :hockey_reference_id)");
        $insert->bindParam(':team_id', $team['id']);
        $insert->bindParam(':first_name', $player['first_name']);
        $insert->bindParam(':last_name', $player['last_name']);
        $insert->bindParam(':hockey_reference_id', $player['hr_id']);
        try{
          $insert->execute();
        }catch (PDOException $e) {
          die("VIRHE: " . $e->getMessage());
        }
        */
}







//print_r($all_names);
