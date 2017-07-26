<?php


class Team {
    public $team_id = '';
    public $name = '';
    public $ref_id = '';
    function get_id($ref_name,$pdo){
      $getTeam = $pdo->prepare("SELECT id,name from Teams where hockey_reference_id	=:ref_name ");
      $getTeam->bindParam(':ref_name', $ref_name);
      try{
        $getTeam->execute();
      }catch (PDOException $e) {
        die("VIRHE: " . $e->getMessage());
      }
      $team = $getTeam->fetch(PDO::FETCH_ASSOC);
    }
}
?>
