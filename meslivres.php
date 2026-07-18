<?php
// Activation des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification de la connexion utilisateur
if (!isset($_SESSION['id'])) {
    die("Erreur: Vous devez être connecté pour voir cette page.");
}

$user_id = (int)$_SESSION['id'];

// Configuration de la pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6; // Nombre d'éléments par page
$offset = ($page - 1) * $limit;

try {
    // Connexion directe à la base de données
    $host = '127.0.0.1:3306';
    $dbname = 'premiergituto';
    $username = 'root'; // Remplacez par votre utilisateur MySQL
    $password = ''; // Remplacez par votre mot de passe MySQL
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

try {
    // Compter le nombre total de livres pour l'utilisateur
    $countSql = 'SELECT COUNT(*) as total FROM livres WHERE user_id = ?';
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute([$user_id]);
    $result = $countStmt->fetch(PDO::FETCH_ASSOC);
    $totalLivres = $result ? (int)$result['total'] : 0;
    
    // Calculer le nombre total de pages
    $totalPages = $totalLivres > 0 ? ceil($totalLivres / $limit) : 1;
    
    // Vérifier que la page demandée est valide
    if ($page < 1) $page = 1;
    if ($page > $totalPages && $totalPages > 0) $page = $totalPages;
    
    // Recalculer l'offset après validation de la page
    $offset = ($page - 1) * $limit;
    
    // Récupérer les livres avec pagination - CORRECTION ICI
    $sql = 'SELECT livres.*, categories.nom AS categorie_nom
            FROM livres
            LEFT JOIN categories ON categories.id = livres.categorie_id
            WHERE livres.user_id = :user_id
            ORDER BY livres.created_at DESC
            LIMIT :limit OFFSET :offset';
    
    $stmt = $pdo->prepare($sql);
    
    // Liaison des paramètres avec PDO::PARAM_INT pour LIMIT et OFFSET
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Erreur SQL: " . $e->getMessage());
    $error_message = "Erreur de base de données: " . $e->getMessage();
    $livres = [];
    $totalPages = 0;
    $page = 1;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Eduleb - Education HTML Template">
    <meta name="keywords" content="agency, business, corporate, creative, html5, modern, multipurpose, One Page, parallax, startup">
    <title>Eduleb - Education HTML Template</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/themify-icons.css">
    <link rel="stylesheet" href="assets/owlcarousel/css/owl.carousel.css">
    <link rel="stylesheet" href="assets/owlcarousel/css/owl.theme.css">
    <link rel="stylesheet" href="assets/css/jquery-simple-mobilemenu.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body data-spy="scroll" data-offset="80">

    <?php
    if (isset($_SESSION['id'])) {
        require_once 'layout/navbarcon.php';
    } else {
        require_once 'layout/navbar.php';
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
                </div>
            </div>
        </div>
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
                        <div class="alert alert-info">
                            <p>Vous n'avez pas encore de cours. Commencez à en créer !</p>
                            <a href="ajouter_livre.php" class="btn btn-primary">Ajouter votre premier cours</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($livres as $livre): ?>
                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="single_course">
                            <div class="single_c_img">
                                <?php 
                                $imageName = !empty($livre['image']) ? basename($livre['image']) : 'default.jpg';
                                $imagePath = 'images/' . $imageName;
                                
                                // Vérifier si l'image existe
                                if (!file_exists($imagePath)) {
                                    $imagePath = 'images/default.jpg';
                                }
                                ?>
                                <img src="<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8') ?>" 
                                     class="img-fluid" 
                                     alt="<?= htmlspecialchars($livre['titre'] ?? 'Sans titre', ENT_QUOTES, 'UTF-8') ?>" />
                                <span><?= htmlspecialchars($livre['categorie_nom'] ?: 'Sans catégorie', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                            <h4><?= htmlspecialchars($livre['titre'] ?? 'Sans titre', ENT_QUOTES, 'UTF-8') ?></h4>
                            <p><?= nl2br(htmlspecialchars(substr($livre['contenu'] ?? '', 0, 150) . '...', ENT_QUOTES, 'UTF-8')) ?></p>
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
                                
                                <?php
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

    <script src="assets/js/jquery-1.12.4.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/modernizr-2.8.3.min.js"></script>
    <script src="assets/js/jquery-simple-mobilemenu.js"></script>
    <script src="assets/owlcarousel/js/owl.carousel.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/jquery.inview.min.js"></script>
    <script src="assets/js/scrolltopcontrol.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/scripts.js"></script>
</body>

</html>