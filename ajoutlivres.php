<?php
require 'config/config.php';
require 'config/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['titre'], $_POST['contenu'], $_POST['prix'], $_POST['categorie_id'])) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header('Location: ajoutlivres.php');
        exit();
    }

    $titre = trim(htmlspecialchars($_POST['titre']));
    $contenu = trim(htmlspecialchars($_POST['contenu']));
    $prix = filter_var($_POST['prix'], FILTER_VALIDATE_FLOAT);
    $categorie_id = filter_var($_POST['categorie_id'], FILTER_VALIDATE_INT);
    $user_id = $_SESSION['id'];
    $created_at = date('Y-m-d H:i:s');

    $errors = [];

    if (empty($titre)) $errors[] = "Le titre est obligatoire.";
    if (empty($contenu)) $errors[] = "Le contenu est obligatoire.";
    if ($prix === false || $prix < 0) $errors[] = "Le prix doit être un nombre valide et positif.";
    if ($categorie_id === false || $categorie_id < 1) $errors[] = "La catégorie est invalide.";

    // Gestion de l'image (optionnelle)
    $nom_image = null;
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image'];
        $extensions_autorisees = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        $types_mime_autorises = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($extension, $extensions_autorisees)) {
            $errors[] = "Format d'image non autorisé.";
        }
        if (!in_array($image['type'], $types_mime_autorises)) {
            $errors[] = "Type de fichier non autorisé.";
        }
        if ($image['size'] > 5 * 1024 * 1024) {
            $errors[] = "L'image ne doit pas dépasser 5 Mo.";
        }

        if (empty($errors)) {
            $dossier_images = 'images/';
            if (!is_dir($dossier_images)) {
                mkdir($dossier_images, 0777, true);
            }
            $nom_image = time() . '_' . uniqid() . '.' . $extension;
            $chemin_image = $dossier_images . $nom_image;
            
            if (!move_uploaded_file($image['tmp_name'], $chemin_image)) {
                $errors[] = "Erreur lors du téléchargement de l'image.";
            }
        }
    }

    if (empty($errors)) {
        $sql = "INSERT INTO livres (titre, contenu, image, prix, categorie_id, created_at, user_id) 
                VALUES (:titre, :contenu, :image, :prix, :categorie_id, :created_at, :user_id)";

        try {
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([
                ':titre' => $titre,
                ':contenu' => $contenu,
                ':image' => $nom_image,
                ':prix' => $prix,
                ':categorie_id' => $categorie_id,
                ':created_at' => $created_at,
                ':user_id' => $user_id
            ]);

            if ($success) {
                $_SESSION['success'] = "Livre ajouté avec succès !";
                header('Location: meslivres.php');
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
        }
    } else {
        $_SESSION['errors'] = $errors;
    }

    header('Location: ajoutlivres.php');
    exit();
}
?>









<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<meta name="description" content="Eduleb - Education HTML Template">
	<meta name="keywords" content="agency, business, corporate, creative, html5, modern, multipurpose, One Page, parallax, startup">
	<!-- SITE TITLE -->
	<title>Eduleb - Education HTML Template</title>
	<!-- Latest Bootstrap min CSS -->
	<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<!-- Font Awesome CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
	<link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
	<link rel="stylesheet" href="assets/fonts/themify-icons.css">
	<!--- owl carousel Css-->
	<link rel="stylesheet" href="assets/owlcarousel/css/owl.carousel.css">
	<link rel="stylesheet" href="assets/owlcarousel/css/owl.theme.css">
	<!--jquery-simple-mobilemenu Css-->
	<link rel="stylesheet" href="assets/css/jquery-simple-mobilemenu.css">
	<!-- MAGNIFIC CSS -->
	<link rel="stylesheet" href="assets/css/magnific-popup.css">
	<!-- animate CSS -->
	<link rel="stylesheet" href="assets/css/animate.css">
	<!-- Style CSS -->
	<link rel="stylesheet" href="assets/css/style.css">
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
</head>

<body data-spy="scroll" data-offset="80">


	<?php
	if (isset($_SESSION['id'])) {
		require_once 'layout/navbarcon.php';   // Navbar connecté
	} else {
		require_once 'layout/navbar.php';      // Navbar visiteur
	}
	?>

	<!-- START SECTION TOP -->
	<section class="section-top">
		<div class="container">
			<div class="col-lg-10 offset-lg-1 text-center">
				<div class="section-top-title wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.3s" data-wow-offset="0">
					<h1>Get In Touch</h1>
					<ul>
						<li><a href="index.html">Home</a></li>
						<li> / Contact</li>
					</ul>
				</div><!-- //.HERO-TEXT -->
			</div><!--- END COL -->
		</div><!--- END CONTAINER -->
	</section>
	<!-- END SECTION TOP -->

	<!-- START ADDRESS -->
	<section class="address_area section-padding">
		<div class="container">
			<div class="row text-center">
				<div class="col-lg-4 col-sm-4 col-xs-12 no-padding wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s" data-wow-offset="0">
					<div class="single_address sa_one">
						<i class="ti-map"></i>
						<h4>Our Location</h4>
						<p>3481 Melrose Place, Beverly Hills <br /> CA 90210</p>
					</div>
				</div><!-- END COL -->
				<div class="col-lg-4 col-sm-4 col-xs-12 no-padding wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s" data-wow-offset="0">
					<div class="single_address sa_two">
						<i class="ti-mobile"></i>
						<h4>Telephone</h4>
						<p>(+1) 517 397 7100</p>
						<p>(+1) 411 315 8138</p>
					</div>
				</div><!-- END COL -->
				<div class="col-lg-4 col-sm-4 col-xs-12 no-padding wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s" data-wow-offset="0">
					<div class="single_address sa_three">
						<i class="ti-email"></i>
						<h4>Send email</h4>
						<p>Info@example.com</p>
						<p>admin@example.com</p>
					</div>
				</div><!-- END COL -->
			</div><!--- END ROW -->
		</div><!--- END CONTAINER -->
	</section>
	<!-- END ADDRESS -->

	<!-- CONTACT -->
	<div id="contact" class="contact_area section-padding">
		<div class="container">
			<div class="row">
				<div class="col-lg-7 col-sm-12 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s" data-wow-offset="0">
					<div class="contact">
						<form class="form" name="enq" method="post" action="" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-md-6">
									<label for="">Titre du livre</label>
									<input type="text" name="titre" class="form-control" required="required">
								</div>
								<div class="form-group col-md-6">
									<label for="">Prix</label>
									<input type="number" name="prix" class="form-control" step="0.01" min="0" required="required">
								</div>
								<div class="form-group col-md-12">
									<label for="">Catégorie</label>
									<select name="categorie_id" class="form-control" required="required">
										<option value="">Sélectionnez une catégorie</option>
										<?php
										try {
											$stmt = $pdo->query("SELECT id, nom FROM categories ORDER BY nom");
											while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
												echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['nom']) . '</option>';
											}
										} catch (PDOException $e) {
											echo '<option value="">Erreur de chargement des catégories</option>';
										}
										?>
									</select>
								</div>
								<div class="form-group col-md-12">
									<label for="">Image du livre</label>
									<input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp" required="required">
									<small class="form-text text-muted">Formats autorisés : JPG, PNG, GIF, WEBP (max 5 Mo)</small>
								</div>
								<div class="form-group col-md-12">
									<label for="">Contenu / Description</label>
									<textarea rows="6" name="contenu" class="form-control" required="required" placeholder="Décrivez votre livre..."></textarea>
								</div>
								<div class="col-md-12 text-center">
									<button type="submit" value="Ajouter un livre" name="submit" id="submitButton" class="btn_one" title="Ajouter votre livre!">Ajouter le livre</button>
								</div>
							</div>
						</form>
					</div>
				</div><!-- END COL  -->
				<!-- END COL  -->
			</div><!-- END ROW -->
		</div><!--- END CONTAINER -->
	</div>
	<!-- END CONTACT -->

	<?php require_once 'layout/footer.php'; ?>

	<!-- Latest jQuery -->
	<script src="assets/js/jquery-1.12.4.min.js"></script>
	<!-- Latest compiled and minified Bootstrap -->
	<script src="assets/bootstrap/js/bootstrap.min.js"></script>
	<!-- modernizer JS -->
	<script src="assets/js/modernizr-2.8.3.min.js"></script>
	<!-- jquery-simple-mobilemenu.min -->
	<script src="assets/js/jquery-simple-mobilemenu.js"></script>
	<!-- owl-carousel min js  -->
	<script src="assets/owlcarousel/js/owl.carousel.min.js"></script>
	<!-- magnific-popup js -->
	<script src="assets/js/jquery.magnific-popup.min.js"></script>
	<!-- countTo js -->
	<script src="assets/js/jquery.inview.min.js"></script>
	<!-- scrolltopcontrol js -->
	<script src="assets/js/scrolltopcontrol.js"></script>
	<!-- WOW - Reveal Animations When You Scroll -->
	<script src="assets/js/wow.min.js"></script>
	<!-- scripts js -->
	<script src="assets/js/scripts.js"></script>
</body>

</html>