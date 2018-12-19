<link rel="stylesheet" href="style.css" type="text/css">
<?php
$requete1 = 'SELECT id_distributeur,nom FROM distributeurs';
function requete($attr){
    try {
        $db = new PDO('mysql:host=localhost;dbname=cinema', "root", "root");
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }
    $requete = $db->query($attr)->fetchAll(PDO::FETCH_ASSOC);
    $retour='<table border="1" id="accueil">';
    $a=0;
    foreach($requete as $value){
      $retour=$retour.'<tr>';
        foreach($value as $key => $valeur){
            if($a<sizeof($value)){
                if($key =='nom'){
                    $retour=$retour.'<th>'.$key.'</th>';
                }
            }
            $a++;   
        }
        $retour=$retour.'</tr><tr>';
        $i=0;
       foreach($value as $key2 =>$val){
         if($i==0){
             $prec=$val;
         }
         else if($key2=='nom'){
            $retour=$retour.'<td><a href=arbre.php?id='.$prec.'>'.$val.'</td>';
         }
            $i++;           
       }
      $retour=$retour.'</tr>';
    }
    echo($retour.'</table>');
}

function requeteSeul(){    
    try {
        $db = new PDO('mysql:host=localhost;dbname=cinema', "root", "root");
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }

    $genre_requete='SELECT g.nom FROM genres as g INNER JOIN films as f on f.id_genre = g.id_genre INNER JOIN distributeurs as d on d.id_distributeur=f.id_distributeur WHERE d.id_distributeur LIKE "'.$_GET['id'].'" GROUP BY g.nom';
    $genre= $db->query($genre_requete)->fetchAll(PDO::FETCH_ASSOC);
    $retour='<table border="1">';
    $a=0;
    foreach($genre as $unite_genre){ 
        $retour=$retour.'<tr><th colspan="3">'.$unite_genre['nom'].'</th></tr>';   
       $requete_nom="SELECT YEAR(f.date_debut_affiche) as annee,f.id_film,f.titre,g.nom FROM distributeurs as d INNER JOIN films as f on f.id_distributeur=d.id_distributeur INNER JOIN genres as g ON f.id_genre=g.id_genre WHERE d.id_distributeur LIKE '".$_GET['id']."' AND g.nom LIKE '".$unite_genre['nom']."' ORDER BY g.nom,annee,f.titre";
        $requete = $db->query($requete_nom)->fetchAll(PDO::FETCH_ASSOC);
        foreach($requete as $value){
            $retour=$retour.'<tr>';
              $j=0;
             foreach($value as $key_2 =>$val){
                
                 if($key_2=='id_film'){
                    $precid=$val;
                 }
                  if($key_2=='annee'){
                    $retour=$retour.'<td>'.$val.'</td>';
                  }
                 else if($key_2=='titre'){    
                  $retour=$retour.'<td><a href="arbre.php?film='.$precid.'&prec='.$_GET['id'].'">'.$val.'</td>';
                 }
             }
            $retour=$retour.'</tr>';
            
          }
    }
   
  
    echo($retour.'</table>');
}

function requeteFilm($attr){
    try {
        $db = new PDO('mysql:host=localhost;dbname=cinema', "root", "root");
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }

    $requete = $db->query($attr)->fetchAll(PDO::FETCH_ASSOC);
    foreach($requete as $value){
    foreach($value as $key_film =>$val){
        if($key_film=='titre'){
            echo('<title>'.$val.'</title>');
            $meta=$val;
           }  
        if($key_film=='titre'){
         $meta=$val;
        }
        else if($key_film=='genre' && !(empty($val))){
         $meta=$meta.','.$val;
        }
        else if($key_film=='resume'){
         $pieces = explode(" ", $val);
         $rendu_keyword='';
         foreach($pieces as $mots){
             if(strlen($mots)>5 && strlen($mots)<10){
                 if(substr($mots, -1, 1)==','){
                     $rendu_keyword=$rendu_keyword.$mots;
                 }
                 else{
                     $rendu_keyword=$rendu_keyword.$mots.',';
                 }
             }
         }
        }
        else if($key_film=='distrib' && !(empty($val))){
         $meta=$meta.','.$val;
        }
        else if($key_film=='annee' && !(empty($val))){
         $meta=$meta.','.$val;
        }
        
    }
}

    ?>
    <meta name="description" content="<?php echo($meta)?>">
    <meta name="keywords" content="<?php echo($rendu_keyword)?>">
    <?php

    echo('<button><a href=arbre.php?id='.$_GET['prec'].'>Retour arrière</a></button>');
    $retour='<table border="1">';
    $a=0;
    
    foreach($requete as $value){
      $retour=$retour.'<tr>';
        foreach($value as $key => $valeur){
            if($a<sizeof($value)){
                $retour=$retour.'<th>'.$key.'</th>';
            }
            $a++;   
        }
        $retour=$retour.'</tr><tr>';
        $meta='';
        
       foreach($value as $key_film =>$val){
           $retour=$retour.'<td>'.$val.'</td>';         
       }
      $retour=$retour.'</tr>';
    }
    echo($retour.'</table>');
}

if(isset($_GET['id'])){
    requeteSeul();
}
else if(isset($_GET['film'])){
    $requete_nom="SELECT f.titre,f.resum as resume,g.nom as genre,d.nom as distrib,YEAR(f.date_debut_affiche),f.annee_production as annee FROM distributeurs as d RIGHT JOIN films as f on f.id_distributeur=d.id_distributeur RIGHT JOIN genres as g ON g.id_genre=f.id_genre WHERE f.id_film LIKE '".$_GET['film']."'";
    requeteFilm($requete_nom);
}
 else{
    requete($requete1);
 }