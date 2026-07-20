<?php
require_once 'config/config.php';

$errors = [];
$successes = [];
$nom = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim(strip_tags($_POST['nom'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if ($nom === '') {
        $errors[] = 'Veuillez saisir votre nom.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Veuillez saisir une adresse email valide.';
    }

    if ($password === '' || mb_strlen($password) < 6) {
        $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }

    if (empty($errors)) {
        // ✅ Correction : utiliser execute avec tableau
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Cette adresse email est déjà utilisée.';
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // ✅ Correction : execute avec tableau
        $stmt = $pdo->prepare('INSERT INTO users (nom, email, password) VALUES (?, ?, ?)');
        
        if ($stmt->execute([$nom, $email, $hash])) {
            $successes[] = 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.';
            $nom = '';
            $email = '';
        } else {
            $errors[] = 'Impossible d\'enregistrer votre compte.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Premier Gittuto - Inscription">
    <meta name="keywords" content="inscription, compte, formation, ebook">
    <title>Inscription - Premier Gittuto</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <!-- Font Awesome CSS (CDN) : indispensable pour les icônes "fa-solid" / "fa-brands"
         utilisées notamment par le footer (réseaux sociaux). Cette page ne chargeait
         auparavant que l'ancienne version locale, incompatible avec ces classes. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/themify-icons.css">
    <link rel="stylesheet" href="assets/css/jquery-simple-mobilemenu.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .section-top p { color: #5f6d89; max-width: 720px; margin: 0 auto 24px; line-height: 1.75; }
    </style>
</head>

<body>
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
                    <h1>Inscription</h1>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <li> / Inscription</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- END SECTION TOP -->

    <!-- START INSCRIPTION -->
    <section class="contact_area section-padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 col-xl-7 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s" data-wow-offset="0">

                    <div class="section-title text-center">
                        <h2>Créer un <b>compte</b></h2>
                        <p>Rejoignez la communauté pour publier et découvrir des livres.</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="alert-floating alert-danger-custom">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <div>
                                <strong>Merci de corriger les points suivants :</strong>
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($successes)): ?>
                        <div class="alert-floating alert-success-custom">
                            <i class="fa-solid fa-circle-check"></i>
                            <div>
                                <?php foreach ($successes as $success): ?>
                                    <div><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endforeach; ?>
                                <a href="login.php" style="display:inline-block;margin-top:8px;font-weight:700;color:#525fe1;">Se connecter maintenant <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="book-form-card">
                        <form method="post" action="inscription.php" id="registerForm">
                            <div class="row">
                                <div class="col-md-6 form-field">
                                    <label for="nom"><i class="ti-user"></i> Nom <span class="required-star">*</span></label>
                                    <div class="input-icon-wrap">
                                        <span class="field-icon"><i class="fa-solid fa-user"></i></span>
                                        <input type="text" id="nom" name="nom" class="form-control" placeholder="Votre nom" value="<?php echo htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-6 form-field">
                                    <label for="email"><i class="ti-email"></i> Email <span class="required-star">*</span></label>
                                    <div class="input-icon-wrap">
                                        <span class="field-icon"><i class="fa-solid fa-envelope"></i></span>
                                        <input type="email" id="email" name="email" class="form-control" placeholder="Votre adresse email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-12 form-field">
                                    <label for="password"><i class="ti-lock"></i> Mot de passe <span class="required-star">*</span></label>
                                    <div class="input-icon-wrap">
                                        <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
                                        <input type="password" id="password" name="password" class="form-control" placeholder="6 caractères minimum" minlength="6" required>
                                    </div>
                                    <small class="form-text text-muted" style="display:block;margin-top:8px;">Utilisez au moins 6 caractères, en mélangeant idéalement lettres et chiffres.</small>
                                </div>

                                <div class="col-md-12 text-center mt-2">
                                    <button type="submit" class="book-form-submit">
                                        <i class="fa-solid fa-user-plus"></i> Créer mon compte
                                    </button>
                                </div>

                                <div class="col-md-12 text-center" style="margin-top:22px;">
                                    <p style="margin:0;">Déjà inscrit ? <a href="login.php" style="color:#525fe1;font-weight:700;">Se connecter</a></p>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- END INSCRIPTION -->

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

    <script>
        // Désactive le bouton pendant l'envoi pour éviter les doubles soumissions
        var regForm = document.getElementById('registerForm');
        if (regForm) {
            regForm.addEventListener('submit', function () {
                var btn = regForm.querySelector('.book-form-submit');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Création en cours...';
                }
            });
        }
    </script>
</body>

</html>
