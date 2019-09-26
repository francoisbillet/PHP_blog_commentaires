<?php

	try {
		$db = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', '');
	}

	catch(Exception $e) {
		die('Erreur : ' . $e->getMessage());
	}

	if (isset($_POST['pseudo']) && isset($_POST['commentaire'])) {
		$request = $db->prepare('INSERT INTO commentaires(id_billet, auteur, date_ajout, contenu) VALUES(?, ?, NOW() , ?)');
		$request->execute(array($_GET['id_billet'], $_POST['pseudo'], $_POST['commentaire']));

		$request->closeCursor();
	}

	$id_billet = $_GET['id_billet'];
	header("Location: commentaires.php?id_billet=$id_billet");

?>