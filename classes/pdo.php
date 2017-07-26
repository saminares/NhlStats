<?php

try{
  $pdo = new PDO(
      "mysql:host=localhost;dbname=nhlstats",
      "root",
      "vagrant"
  );
}catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}

?>
