<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title> Commentaires </title>

		<style>

			h1, h3
			{
			    text-align:center;
			}

			h3
			{
			    background-color:black;
			    color:white;
			    font-size:0.9em;
			    margin-bottom:0px;
			}

			.news p
			{
			    background-color:#CCCCCC;
			    margin-top:0px;
			}

			.news
			{
			    width:70%;
			    margin:auto;
			}

			a
			{
			    text-decoration: none;
			    color: blue;
			}

			form p{
				padding: 5px;
			}

		</style>

	</head>

	<body>
		
		<h1> Mon super blog ! </h1>

		<p> <a href="index.php">Retour à la liste des billets</a> </p>

		<?php 

			try {
				$db = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', '');
			}

			catch(Exception $e) {
				die('Erreur : ' . $e->getMessage());
			}


			// $total->closeCursor();

			// On affiche le billet si l'id donné correspond à un billet			
			$request = $db->prepare('SELECT titre, contenu, DATE_FORMAT(date_ajout, "%d/%m/%Y à %Hh%imin%ss") AS date_modifiee FROM billets WHERE id = ?');
			$request->execute(array($_GET['id_billet']));

			// On affiche le billet correspondant à l'ID donné
			$data = $request->fetch();

			if (!empty($data)) { //Si la requête n'est pas vide
				?>
				<div class="news">
					<h3> 
						<?php echo htmlspecialchars($data['titre']); ?>
						<em>le <?php echo $data['date_modifiee']; ?> </em> 
					</h3>
					<p> 
						<?php echo nl2br(htmlspecialchars($data['contenu'])); ?>
					</p>
				</div>

				<h2> Commentaires </h2>

				<?php

				$request->closeCursor();

				// On affiche les commentaires correspondant à ce billet
				// $request = $db->prepare('SELECT auteur, contenu, DATE_FORMAT(date_ajout, "%d/%m/%Y à %Hh%imin%ss") AS date_modifiee FROM commentaires WHERE id_billet = ? ORDER BY date_ajout DESC');
				// $request->execute(array($_GET['id_billet']));

				$request = $db->prepare('SELECT auteur, contenu, DATE_FORMAT(date_ajout, "%d/%m/%Y à %Hh%imin%ss") AS date_modifiee FROM commentaires WHERE id_billet = ? ORDER BY date_ajout DESC LIMIT ?, 5');
				$request->bindValue(1, $_GET['id_billet'], PDO::PARAM_STR);
				if (!isset($_GET['page'])) {
					$_GET['page'] = 1;
				}

				$request->bindValue(2, $_GET['page']*5-5, PDO::PARAM_INT);
				$request->execute();

				while ($data = $request->fetch()) {
					?>
						<p> <strong> <?php echo htmlspecialchars(($data['auteur'])); ?> </strong> le <?php echo $data['date_modifiee']; ?> </p>
						<p> <?php echo nl2br(htmlspecialchars($data['contenu'])); ?> </p>
					<?php
				}

				$request->closeCursor();

				// On compte le nombre de commentaires
				$request = $db->prepare('SELECT COUNT(*) AS nb_commentaires FROM commentaires WHERE id_billet = ?');
				$request->execute(array($_GET['id_billet']));

				$data = $request->fetch();
				$nb_commentaires = $data['nb_commentaires'];

				// On compte le nombre de pages de commentaires
				if ($nb_commentaires % 5 == 0) {
					$nb_pages = $nb_commentaires / 5;
				}

				else {
					$nb_pages = (int) ($nb_commentaires / 5 + 1);
				}

				?>

				 <p> Page : 
				 	<?php
				 		for($i=1;$i<=$nb_pages;$i++) {
				 			?>
				 			<a href="commentaires.php?id_billet=<?php echo $_GET['id_billet']; ?>&page=<?php echo $i; ?>"> <?php echo $i; ?> </a>
				 			<?php
				 		}
					?>
				</p>


				

				<div class="news"> Ajouter un commentaire : 
					<form method="post" action="commentaires_post.php?id_billet=
						<?php echo $_GET['id_billet']; ?>">
						<p>
							<label for="pseudo"> Pseudo : </label>
							<input type="text" name="pseudo" id="pseudo"  />
							<br />
							<label for="commentaire"> Commentaire : </label> <br />
							<textarea name="commentaire" id="commentaire" rows=5 cols=40> Entrez votre commentaire ici </textarea>
							<br />
							<input type="submit" value="Envoyer" />
						</p>
					</form>
				</div>

				<?php

				}

			else {
				echo 'Erreur, ce billet n\'existe pas !';
			}

		?>			

	</body>
</html>