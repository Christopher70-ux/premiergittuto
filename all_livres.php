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
                <?php foreach ($livres as $livre):
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
                ?>
                    <div class="col-lg-4 col-md-6 col-sm-12 livre-item" data-category="<?= $categorieSlug ?>">
                        <div class="single_livre">
                            <div class="livre_img">
                                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($livre['titre']) ?>" class="img-fluid" />
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
            <p class="text-center">Aucun livre pour le moment.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Styles complets -->
<style>
    /* ============================================
   Section Livres – Design amélioré
   ============================================ */

/* --- Grille et espacement --- */
.livres_area {
    background: #fcfcfd; /* fond très légèrement grisé pour faire ressortir les cartes */
}

/* --- Filtres --- */
.livre-filters {
    margin-bottom: 50px;
}

.livre-filters .filter-btn {
    background: transparent;
    border: 2px solid #e0e0e6;
    color: #4a5355;
    padding: 10px 28px;
    margin: 0 6px 12px;
    border-radius: 40px;
    font-family: 'Jost', sans-serif;
    font-weight: 600;
    font-size: 15px;
    letter-spacing: 0.3px;
    transition: all 0.35s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.livre-filters .filter-btn::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 40px;
    background: #525fe1;
    transform: scale(0.8);
    opacity: 0;
    transition: all 0.35s ease;
    z-index: -1;
}

.livre-filters .filter-btn.active,
.livre-filters .filter-btn:hover {
    color: #fff;
    background: #525fe1;
    border-color: #525fe1;
}

.livre-filters .filter-btn.active::after,
.livre-filters .filter-btn:hover::after {
    opacity: 1;
    transform: scale(1);
}

.livre-filters .filter-btn.active {
    box-shadow: 0 8px 20px rgba(82, 95, 225, 0.25);
}

/* --- Carte d’un livre --- */
.single_livre {
    background: #ffffff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(23, 23, 36, 0.05);
    margin-bottom: 35px;
    transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1.2),
                box-shadow 0.4s ease;
    border: 1px solid rgba(0,0,0,0.03);
}

.single_livre:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(23, 23, 36, 0.12);
}

/* --- Image et overlay --- */
.livre_img {
    position: relative;
    overflow: hidden;
    height: 260px; /* hauteur fixe pour cohérence */
}

.livre_img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease, filter 0.4s ease;
}

.single_livre:hover .livre_img img {
    transform: scale(1.08);
    filter: brightness(0.9);
}

/* Overlay subtil pour améliorer la lisibilité du badge */
.livre_img::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 40%;
    background: linear-gradient(to top, rgba(0,0,0,0.4), transparent);
    z-index: 1;
    pointer-events: none;
    transition: opacity 0.3s;
}

/* --- Badge de catégorie --- */
.livre_cat_badge {
    position: absolute;
    top: 18px;
    left: 18px;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    color: #0b104a;
    font-family: 'Jost', sans-serif;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.5px;
    padding: 6px 18px;
    border-radius: 30px;
    text-transform: uppercase;
    z-index: 2;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    border: 1px solid rgba(255,255,255,0.6);
}

/* --- Informations du livre --- */
.livre_info {
    padding: 25px 22px 30px;
}

.livre_info h4 {
    font-family: 'Jost', sans-serif;
    font-size: 21px;
    font-weight: 600;
    line-height: 1.4;
    color: #0b104a;
    margin-bottom: 12px;
    transition: color 0.2s ease;
}

.single_livre:hover .livre_info h4 {
    color: #525fe1;
}

.livre_info .prix {
    font-family: 'Jost', sans-serif;
    font-weight: 700;
    font-size: 22px;
    color: #f26b65;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Description */
.livre_info .description {
    font-family: 'DM Sans', sans-serif;
    font-size: 15px;
    line-height: 1.7;
    color: #5a6366;
    margin-bottom: 20px;
}

/* Optionnel : bouton "En savoir plus" (si vous l'ajoutez plus tard) */
.livre_info .btn_detail {
    display: inline-block;
    margin-top: auto;
    font-family: 'Jost', sans-serif;
    font-weight: 600;
    color: #525fe1;
    font-size: 15px;
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: border-color 0.3s, padding 0.3s;
}

.livre_info .btn_detail i {
    margin-left: 6px;
    transition: transform 0.3s;
}

.livre_info .btn_detail:hover {
    border-bottom-color: #525fe1;
    padding-bottom: 2px;
}

.livre_info .btn_detail:hover i {
    transform: translateX(4px);
}

/* --- Responsive --- */
@media (max-width: 768px) {
    .livre_img {
        height: 220px;
    }
    .livre-filters .filter-btn {
        padding: 8px 20px;
        font-size: 14px;
        margin: 0 4px 10px;
    }
    .livre_info h4 {
        font-size: 18px;
    }
    .livre_info .prix {
        font-size: 20px;
    }
}
</style>

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