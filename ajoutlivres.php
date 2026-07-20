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

// Récupération + nettoyage des messages flash (session) à afficher une seule fois
$flash_success = $_SESSION['success'] ?? null;
$flash_error   = $_SESSION['error'] ?? null;
$flash_errors  = $_SESSION['errors'] ?? null;
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['errors']);
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
					<h1>Publier un livre</h1>
					<ul>
						<li><a href="index.php">Accueil</a></li>
						<li> / Publier</li>
					</ul>
				</div><!-- //.HERO-TEXT -->
			</div><!--- END COL -->
		</div><!--- END CONTAINER -->
	</section>
	<!-- END SECTION TOP -->

	<!-- START AJOUT LIVRE -->
	<div id="contact" class="contact_area section-padding">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-9 col-xl-8 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s" data-wow-offset="0">

					<div class="section-title text-center">
						<h2>Ajouter un <b>nouveau livre</b></h2>
						<p>Remplissez les informations ci-dessous pour publier votre livre sur la plateforme.</p>
					</div>

					<!-- Messages de succès / erreur -->
					<?php if ($flash_success): ?>
						<div class="alert-floating alert-success-custom">
							<i class="fa-solid fa-circle-check"></i>
							<div><?= htmlspecialchars($flash_success, ENT_QUOTES, 'UTF-8') ?></div>
						</div>
					<?php endif; ?>

					<?php if ($flash_error): ?>
						<div class="alert-floating alert-danger-custom">
							<i class="fa-solid fa-circle-exclamation"></i>
							<div><?= htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8') ?></div>
						</div>
					<?php endif; ?>

					<?php if (!empty($flash_errors) && is_array($flash_errors)): ?>
						<div class="alert-floating alert-danger-custom">
							<i class="fa-solid fa-circle-exclamation"></i>
							<div>
								<strong>Merci de corriger les points suivants :</strong>
								<ul>
									<?php foreach ($flash_errors as $err): ?>
										<li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>

					<div class="book-form-card">
						<form class="form" name="enq" method="post" action="" enctype="multipart/form-data" id="bookForm">
							<div class="row">
								<div class="col-md-6 form-field">
									<label for="titre"><i class="ti-book"></i> Titre du livre <span class="required-star">*</span></label>
									<div class="input-icon-wrap">
										<span class="field-icon"><i class="fa-solid fa-heading"></i></span>
										<input type="text" id="titre" name="titre" class="form-control" placeholder="Ex. Le Seigneur des Anneaux" required>
									</div>
								</div>

								<div class="col-md-6 form-field">
									<label for="prix"><i class="ti-wallet"></i> Prix (€) <span class="required-star">*</span></label>
									<div class="input-icon-wrap">
										<span class="field-icon"><i class="fa-solid fa-euro-sign"></i></span>
										<input type="number" id="prix" name="prix" class="form-control" step="0.01" min="0" placeholder="Ex. 15.00" required>
									</div>
								</div>

								<div class="col-md-12 form-field">
									<label for="categorie_id"><i class="ti-tag"></i> Catégorie <span class="required-star">*</span></label>
									<select id="categorie_id" name="categorie_id" class="form-control" required>
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

								<div class="col-md-12 form-field">
									<label for="image"><i class="ti-image"></i> Image du livre <span class="required-star">*</span></label>
									<label class="book-upload-zone" id="uploadZone">
										<i class="fa-solid fa-cloud-arrow-up"></i>
										<span class="upload-text-main">Cliquez ou déposez une image ici</span>
										<span class="upload-text-sub">JPG, PNG, GIF ou WEBP — 5 Mo maximum</span>
										<input type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.gif,.webp" required>
									</label>
									<div class="book-upload-preview" id="uploadPreview">
										<img id="uploadPreviewImg" src="" alt="Aperçu de l'image">
									</div>
								</div>

								<div class="col-md-12 form-field">
									<label for="contenu"><i class="ti-align-left"></i> Contenu / Description <span class="required-star">*</span></label>
									<textarea rows="6" id="contenu" name="contenu" class="form-control" required placeholder="Décrivez votre livre : résumé, thèmes abordés, ce qui le rend unique..."></textarea>
								</div>

								<div class="col-md-12 text-center mt-2">
									<button type="submit" value="Ajouter un livre" name="submit" id="submitButton" class="book-form-submit" title="Ajouter votre livre !">
										<i class="fa-solid fa-paper-plane"></i> Publier le livre
									</button>
								</div>
							</div>
						</form>
					</div>

				</div><!-- END COL  -->
			</div><!-- END ROW -->
		</div><!--- END CONTAINER -->
	</div>
	<!-- END AJOUT LIVRE -->

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

	<!-- Aperçu de l'image + retour visuel du champ de dépôt de fichier -->
	<script>
		(function () {
			var input = document.getElementById('image');
			var zone = document.getElementById('uploadZone');
			var preview = document.getElementById('uploadPreview');
			var previewImg = document.getElementById('uploadPreviewImg');

			if (!input) return;

			input.addEventListener('change', function () {
				if (input.files && input.files[0]) {
					var reader = new FileReader();
					reader.onload = function (e) {
						previewImg.src = e.target.result;
						preview.style.display = 'block';
						zone.querySelector('.upload-text-main').textContent = input.files[0].name;
					};
					reader.readAsDataURL(input.files[0]);
				}
			});

			['dragenter', 'dragover'].forEach(function (evt) {
				zone.addEventListener(evt, function (e) {
					e.preventDefault();
					zone.classList.add('dragover');
				});
			});
			['dragleave', 'drop'].forEach(function (evt) {
				zone.addEventListener(evt, function (e) {
					e.preventDefault();
					zone.classList.remove('dragover');
				});
			});

			// Désactive le bouton pendant l'envoi pour éviter les doubles soumissions
			var form = document.getElementById('bookForm');
			var submitBtn = document.getElementById('submitButton');
			if (form && submitBtn) {
				form.addEventListener('submit', function () {
					submitBtn.disabled = true;
					submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Publication en cours...';
				});
			}
		})();
	</script>
</body>

</html>
