<?php
include 'config/config.php';


// Requête avec jointure pour récupérer le nom de la catégorie
$query = "
    SELECT l.titre, l.contenu, l.image, l.prix, c.nom AS categorie_nom
    FROM livres l
    LEFT JOIN categories c ON l.categorie_id = c.id
    ORDER BY l.titre ASC
";
$stmt = $pdo->query($query);
$livres = $stmt->fetchAll();
?>

<section class="livres_area section-padding">
    <div class="container">
        <div class="section-title text-center">
            <h2>Nos livres <b>publiés</b></h2>
            <p>Parcourez notre collection et filtrez par catégorie.</p>
        </div>

        <?php if (count($livres) > 0): ?>
            <!-- Filtres dynamiques -->
            <div class="livre-filters text-center mb-4">
                <button class="filter-btn active" data-filter="all">Tous</button>
                <?php
                // Récupère les noms de catégories uniques
                $categories = array_unique(array_column($livres, 'categorie_nom'));
                sort($categories);
                foreach ($categories as $cat):
                    $slug = strtolower(trim($cat));
                    $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
                    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
                ?>
                    <button class="filter-btn" data-filter="<?= $slug ?>"><?= htmlspecialchars($cat) ?></button>
                <?php endforeach; ?>
            </div>

            <!-- Grille des livres -->
            <div class="row livre-grid">
                <?php $livreIndex = 0; foreach ($livres as $livre):
                    $livreIndex++;
                    // Génération du slug de catégorie pour cette carte
                    $categorieSlug = strtolower(trim($livre['categorie_nom']));
                    $categorieSlug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $categorieSlug);
                    $categorieSlug = preg_replace('/[^a-z0-9]+/', '-', $categorieSlug);

                    // --- Gestion de l'image (corrigée) ---
                    $dossierImages = 'images/'; 
                    $imageDefaut   = $dossierImages . 'default.jpg';
                    $image         = $livre['image'];

                    // Ajouter le préfixe si l'image n'est pas une URL complète
                    if (!filter_var($image, FILTER_VALIDATE_URL) && strpos($image, $dossierImages) !== 0) {
                        $image = $dossierImages . $image;
                    }
                    // Vérifier l'existence physique du fichier
                    if (!file_exists($image)) {
                        $image = $imageDefaut;
                    }

                    // Léger décalage d'apparition en cascade (max 5 paliers, puis on boucle)
                    $revealDelay = 0.1 + (($livreIndex - 1) % 5) * 0.1;
                ?>
                    <div class="col-lg-4 col-md-6 col-sm-12 livre-item wow fadeInUp" data-category="<?= $categorieSlug ?>" data-wow-duration="0.8s" data-wow-delay="<?= $revealDelay ?>s" data-wow-offset="30">
                        <div class="single_livre">
                            <div class="livre_img">
                                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($livre['titre']) ?>" class="img-fluid" loading="lazy" />
                                <span class="livre_cat_badge"><?= htmlspecialchars($livre['categorie_nom']) ?></span>
                            </div>
                            <div class="livre_info">
                                <h4><?= htmlspecialchars($livre['titre']) ?></h4>
                                <?php if (!empty($livre['prix'])): ?>
                                    <p class="prix"><?= number_format($livre['prix'], 2, ',', ' ') ?> €</p>
                                <?php endif; ?>
                                <p class="description">
                                    <?= htmlspecialchars(substr(strip_tags($livre['contenu']), 0, 120)) ?>...
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center" style="padding:30px 20px;">
                <i class="fa-solid fa-book-open" style="font-size:34px;color:#c9cdf2;margin-bottom:14px;display:block;"></i>
                <p>Aucun livre publié pour le moment. Revenez bientôt !</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Le style de cette section (cartes, badges, filtres) vit désormais dans
     assets/css/style.css, afin d'être partagé avec meslivres.php et toute
     autre page affichant des livres. -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterButtons = document.querySelectorAll('.livre-filters .filter-btn');
        const livreItems = document.querySelectorAll('.livre-item');

        filterButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                filterButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const filterValue = this.getAttribute('data-filter');

                livreItems.forEach(item => {
                    if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    });
</script>