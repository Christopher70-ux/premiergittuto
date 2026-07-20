<?php
// Utilisation de la configuration centralisée (au lieu d'une connexion et
// d'une gestion de session dupliquées en local, source d'incohérences si
// les identifiants de connexion venaient à changer un jour).
require 'config/config.php';
require 'config/auth.php';

$user_id = (int) $_SESSION['id'];

// Configuration de la pagination
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 6; // Nombre d'éléments par page
$offset = ($page - 1) * $limit;

$livres = [];
$totalLivres = 0;
$totalPages = 1;
$error_message = null;

try {
    // Compter le nombre total de livres pour l'utilisateur connecté
    $countSql = 'SELECT COUNT(*) as total FROM livres WHERE user_id = :user_id';
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute([':user_id' => $user_id]);
    $result = $countStmt->fetch(PDO::FETCH_ASSOC);
    $totalLivres = $result ? (int) $result['total'] : 0;

    // Calculer le nombre total de pages
    $totalPages = $totalLivres > 0 ? (int) ceil($totalLivres / $limit) : 1;

    // Vérifier que la page demandée est valide
    if ($page < 1) $page = 1;
    if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

    // Recalculer l'offset après validation de la page
    $offset = ($page - 1) * $limit;

    // Récupérer les livres du user connecté, avec pagination
    $sql = 'SELECT livres.*, categories.nom AS categorie_nom
            FROM livres
            LEFT JOIN categories ON categories.id = livres.categorie_id
            WHERE livres.user_id = :user_id
            ORDER BY livres.created_at DESC
            LIMIT :limit OFFSET :offset';

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erreur SQL (meslivres.php) : " . $e->getMessage());
    $error_message = "Une erreur est survenue lors du chargement de vos livres. Merci de réessayer plus tard.";
    $livres = [];
    $totalPages = 1;
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
    <title>Mes livres - Eduleb</title>
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
                    <h1>Mes livres</h1>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <li> / Mes livres</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- END SECTION TOP -->

    <!-- START MES LIVRES -->
    <section class="livres_area section-padding">
        <div class="container">

            <div class="mes-livres-toolbar">
                <div class="section-title" style="margin-bottom:0;text-align:left;">
                    <h2>Mes livres <b>publiés</b></h2>
                    <p>Retrouvez ici tous les livres que vous avez publiés.</p>
                </div>
                <?php if ($totalLivres > 0): ?>
                    <span class="count-pill"><?= $totalLivres ?> livre<?= $totalLivres > 1 ? 's' : '' ?></span>
                <?php endif; ?>
            </div>

            <?php if ($error_message): ?>
                <div class="alert-floating alert-danger-custom">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div><?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            <?php elseif (empty($livres)): ?>
                <div class="text-center" style="padding:40px 20px;">
                    <p style="font-size:17px;margin-bottom:20px;">Vous n'avez pas encore publié de livre.</p>
                    <a href="ajoutlivres.php" class="book-form-submit"><i class="fa-solid fa-plus"></i> Publier mon premier livre</a>
                </div>
            <?php else: ?>
                <div class="row livre-grid">
                    <?php foreach ($livres as $livre):
                        // --- Gestion de l'image (identique à la page d'accueil) ---
                        $dossierImages = 'images/';
                        $imageDefaut   = $dossierImages . 'default.jpg';
                        $image         = !empty($livre['image']) ? basename($livre['image']) : 'default.jpg';
                        $image         = $dossierImages . $image;
                        if (!file_exists($image)) {
                            $image = $imageDefaut;
                        }
                    ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 livre-item">
                            <div class="single_livre">
                                <div class="livre_img">
                                    <img src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($livre['titre'] ?? 'Sans titre', ENT_QUOTES, 'UTF-8') ?>" class="img-fluid" />
                                    <span class="livre_cat_badge"><?= htmlspecialchars($livre['categorie_nom'] ?: 'Sans catégorie', ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="livre_info">
                                    <h4><?= htmlspecialchars($livre['titre'] ?? 'Sans titre', ENT_QUOTES, 'UTF-8') ?></h4>
                                    <?php if (!empty($livre['prix'])): ?>
                                        <p class="prix"><?= number_format((float) $livre['prix'], 2, ',', ' ') ?> €</p>
                                    <?php endif; ?>
                                    <p class="description">
                                        <?= htmlspecialchars(substr(strip_tags($livre['contenu'] ?? ''), 0, 120), ENT_QUOTES, 'UTF-8') ?>...
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- PAGINATION -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1" aria-label="First"><span aria-hidden="true">&laquo;&laquo;</span></a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
                        </li>
                        <?php else: ?>
                        <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
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
                            <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $totalPages ?>" aria-label="Last"><span aria-hidden="true">&raquo;&raquo;</span></a>
                        </li>
                        <?php else: ?>
                        <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <div class="text-center text-muted mt-2">
                    <small>Affichage de <?= count($livres) ?> livre(s) sur <?= $totalLivres ?> au total</small>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
    <!-- END MES LIVRES -->

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
