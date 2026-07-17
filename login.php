<?php
session_start();
require_once __DIR__ . '/config/config.php';

$errors = [];
$username = '';

if (!empty($_SESSION['username'])) {
    header('Location: profil.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '') {
        $errors[] = 'Le nom d’utilisateur est requis.';
    }
    if ($password === '') {
        $errors[] = 'Le mot de passe est requis.';
    }

    if (empty($errors)) {
        if (!isset($pdo) || !($pdo instanceof PDO)) {
            $errors[] = 'La connexion à la base de données est indisponible.';
        } else {
            $stmt = $pdo->prepare('SELECT id, nom, email, password, created_at FROM users WHERE nom = :username');
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password'])) {
                $errors[] = 'Nom d’utilisateur ou mot de passe invalide.';
            } else {
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['nom'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['registration_date'] = $user['created_at'];
                header('Location: profil.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <section class="login-section">
        <div class="login-box">
            <h2>Connexion</h2>
            <p class="login-subtitle">Connectez-vous pour accéder à votre espace personnel.</p>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" style="margin-bottom: 20px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form class="login-form" action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn-login">Se connecter</button>
            </form>
            <div class="login-footer">
                <p>Pas encore de compte ? <a href="inscription.php">Inscrivez-vous</a></p>
            </div>
        </div>
    </section>
</body>
</html>
