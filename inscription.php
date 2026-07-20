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
            $successes[] = 'Votre compte a été créé avec succès.';
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
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/themify-icons.css">
    <link rel="stylesheet" href="assets/css/jquery-simple-mobilemenu.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { overflow-x: hidden; }
        .section-top p { color: #5f6d89; max-width: 720px; margin: 0 auto 24px; line-height: 1.75; }
        #navigation.navbar-fixed {
            position: static !important;
            width: 100% !important;
            top: auto !important;
            opacity: 1 !important;
            box-shadow: none !important;
        }
        .navbar-fixed .site-navigation, .navbar-fixed .header-white, .navbar-fixed .header {
            position: static !important;
        }
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

    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title">
                    <h1>Inscription</h1>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <li> / Inscription</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding">
        <div class="container">
            <div class="row justify-content-center">   
                <div class="col-lg-8 col-sm-12">
                    <div class="contact">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($successes)): ?>
                            <div class="alert alert-success">
                                <?php foreach ($successes as $success): ?>
                                    <div><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="inscription.php">   
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="nom">Nom *</label>
                                    <input type="text" id="nom" name="nom" class="form-control" placeholder="Votre nom" value="<?php echo htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="email">Email *</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="Votre adresse email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="password">Mot de passe *</label>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Mot de passe" required>
                                </div>
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn_one">S'inscrire</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
