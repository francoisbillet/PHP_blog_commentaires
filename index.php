<!DOCTYPE html>
<html lang="FR">
	<head>
		<meta charset="UTF-8" />
		<title> Blog </title>
		<link rel="stylesheet" href="style.css" />
	</head>

	<body>

		<h1> Mon super blog ! </h1>

		<p> Derniers billets du blog : </p>

		<?php	
			
			try {
				$db = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', '');
			}

			catch(Exception $e) {
				die('Erreur : ' . $e->getMessage());
			}

			if (!isset($_GET['page'])) {
						$_GET['page']=1;
			}

			// $request = $db->query('SELECT id, titre, contenu, DATE_FORMAT(date_ajout, "%d/%m/%Y à %Hh%imin%ss") AS date_modifiee FROM billets ORDER BY id DESC LIMIT 5');
			$request = $db->prepare('SELECT id, titre, contenu, DATE_FORMAT(date_ajout, "%d/%m/%Y à %Hh%imin%ss") AS date_modifiee FROM billets ORDER BY id DESC LIMIT ?, 5');
			$request->bindValue(1, $_GET['page']*5-5, PDO::PARAM_INT);
			$request->execute();

			while($data = $request->fetch()) {
				?>
					<div class="news">
						<h3>
							<?php echo htmlspecialchars($data['titre']); ?>
							<em> le <?php echo $data['date_modifiee']; ?> </em> 
						</h3>
						<p> 
							<?php echo nl2br(htmlspecialchars($data['contenu'])); ?> 
							<br />
							<em><a href="commentaires.php?id_billet=<?php echo $data['id']; ?>">Commentaires</a></em>
						</p>
					</div>
				<?php
			}

			$request->closeCursor();

			// On compte le nombre de billets
			$request = $db->query('SELECT COUNT(*) AS nb_billets FROM billets');

			$data = $request->fetch();
			$nb_billets = $data['nb_billets'];

			$request->closeCursor();

			if ($nb_billets % 5 == 0) {
				$nb_pages = $nb_billets / 5;
			}
			else {
				$nb_pages = (int) ($nb_billets / 5 + 1);
			}
		?>


			<p> Page :
				<?php
					for($i=1 ; $i<= $nb_pages ; $i++) {
						?>

						<a href="index.php?page=<?php echo $i; ?>"> <?php echo $i; ?> </a>

						<?php	
					}

				?>

			<!-- 2 méthodes différentes -->

			<!-- <?php
				for($i=1 ; $i<=$nb_pages ; $i++) {
					echo '<a href="index.php?page=' . $i . '">' . $i . '</a> ';
				}
			?>  -->
			</p>
	</body>
</html>