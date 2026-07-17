<?php
require 'config/auth.php';
require 'config/config.php';

$user_id = $_SESSION['user_id'];

// Configuration de la pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6; // Nombre d'éléments par page
$offset = ($page - 1) * $limit;

try {
    // Compter le nombre total de livres
    $countSql = 'SELECT COUNT(*) as total FROM livres WHERE user_id = ?';
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute([$user_id]);
    $totalLivres = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Calculer le nombre total de pages
    $totalPages = ceil($totalLivres / $limit);
    
    // Vérifier que la page demandée est valide
    if ($page < 1) $page = 1;
    if ($page > $totalPages && $totalPages > 0) $page = $totalPages;
    
    // Récupérer les livres avec pagination
    $sql = 'SELECT livres.*, categories.nom AS categorie_nom
            FROM livres
            LEFT JOIN categories ON categories.id = livres.categorie_id
            WHERE livres.user_id = ?
            ORDER BY livres.created_at DESC
            LIMIT ? OFFSET ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $limit, $offset]);
    $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur SQL: " . $e->getMessage());
    $livres = [];
    $totalPages = 0;
    $page = 1;
    $error_message = "Une erreur est survenue lors du chargement des cours.";
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
					<h1>All Course</h1>
					<ul>
						<li><a href="index.html">Home</a></li>
						<li> / Course</li>
					</ul>
				</div><!-- //.HERO-TEXT -->
			</div><!--- END COL -->
		</div><!--- END CONTAINER -->
	</section>
	<!-- END SECTION TOP -->

	<!-- START COURSE -->
	<section class="home_course section-padding">
        <div class="container">			
            <div class="row">
                <?php if (!empty($error_message)): ?>
                    <div class="col-12">
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                    </div>
                <?php elseif (empty($livres)): ?>
                    <div class="col-12 text-center">
                        <p>Aucun cours disponible pour le moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($livres as $livre): ?>
                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="single_course">
                            <div class="single_c_img">
                                <?php 
                                // Sécuriser le chemin de l'image
                                $imageName = !empty($livre['image']) ? basename($livre['image']) : 'default.jpg';
                                ?>
                                <img src="images/<?= htmlspecialchars($imageName, ENT_QUOTES, 'UTF-8') ?>" 
                                     class="img-fluid" 
                                     alt="<?= htmlspecialchars($livre['titre'] ?? 'Sans titre', ENT_QUOTES, 'UTF-8') ?>" />
                                <span><?= htmlspecialchars($livre['categorie_nom'] ?: 'Sans catégorie', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                            <h4><?= htmlspecialchars($livre['titre'] ?? 'Sans titre', ENT_QUOTES, 'UTF-8') ?></h4>
                            <p><?= nl2br(htmlspecialchars($livre['contenu'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
                            <div class="price">
                                Prix - <?= number_format((float) ($livre['prix'] ?? 0), 0, ',', ' ') ?> €
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- PAGINATION -->
                    <?php if ($totalPages > 1): ?>
                    <div class="col-12">
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <!-- Lien vers la première page -->
                                <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=1" aria-label="First">
                                        <span aria-hidden="true">&laquo;&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">&laquo;</span>
                                </li>
                                <?php endif; ?>
                                
                                <!-- Pages -->
                                <?php
                                // Afficher les pages avec une logique pour les pages intermédiaires
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                if ($startPage > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                                    if ($startPage > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                for ($i = $startPage; $i <= $endPage; $i++) {
                                    if ($i == $page) {
                                        echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                                    } else {
                                        echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                                    }
                                }
                                
                                if ($endPage < $totalPages) {
                                    if ($endPage < $totalPages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '">' . $totalPages . '</a></li>';
                                }
                                ?>
                                
                                <!-- Lien vers la dernière page -->
                                <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $totalPages ?>" aria-label="Last">
                                        <span aria-hidden="true">&raquo;&raquo;</span>
                                    </a>
                                </li>
                                <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">&raquo;</span>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        
                        <!-- Affichage du nombre total d'éléments -->
                        <div class="text-center text-muted mt-2">
                            <small>Affichage de <?= count($livres) ?> cours sur <?= $totalLivres ?> total</small>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>		
    </section>
	<!-- END COURSE -->

	<!-- START FOOTER -->
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