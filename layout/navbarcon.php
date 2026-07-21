<?php
// Détermine la page active + récupère le nom de l'utilisateur connecté pour l'afficher
$current_page = basename($_SERVER['PHP_SELF']);
$nav_username = $_SESSION['username'] ?? '';
$nav_initial  = $nav_username !== '' ? mb_strtoupper(mb_substr($nav_username, 0, 1)) : '?';
?>
<!-- START NAVBAR (utilisateur connecté) -->
<div id="navigation" class="navbar-light bg-faded site-navigation">
	<div class="container-fluid">
		<div class="row">
			<div class="col-20 align-self-center">
				<div class="site-logo">
					<a href="index.php"><img src="assets/img/logo.png" alt="Logo"></a>
				</div>
			</div><!--- END Col -->

			<div class="col-60 d-flex">
				<nav id="main-menu">
					<ul>
						<li><a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Accueil</a></li>
						<li><a href="meslivres.php" class="<?= $current_page == 'meslivres.php' ? 'active' : '' ?>">Mes Livres</a></li>
						<li><a href="profil.php" class="<?= $current_page == 'profil.php' ? 'active' : '' ?>">Profil</a></li>
						<!--li><a href="ajoutlivres.php" class="<?= $current_page == 'ajoutlivres.php' ? 'active' : '' ?>">Publier</a></li-->
					</ul>
				</nav>
			</div><!--- END Col -->

			<div class="col-20 d-none d-xl-block text-end align-self-center">
				<a href="ajoutlivres.php" class="btn_one"><i class="ti-plus"></i> Publier</a>
				<a href="profil.php" class="nav_user_chip" title="<?= htmlspecialchars($nav_username, ENT_QUOTES, 'UTF-8') ?>">
					<span class="nav_user_avatar"><?= htmlspecialchars($nav_initial, ENT_QUOTES, 'UTF-8') ?></span>
				</a>
				<a href="layout/logout.php" class="header-btn" title="Déconnexion"><i class="ti-power-off"></i></a>
			</div><!--- END Col -->

			<ul class="mobile_menu">
				<li><a href="index.php">Accueil</a></li>
				<li><a href="meslivres.php">Mes Livres</a></li>
				<!--li><a href="profil.php">Profil</a></li>
				<li><a href="ajoutlivres.php">Publier</a></li-->
				<li><a href="layout/logout.php">Déconnexion</a></li>
			</ul>
		</div><!--- END ROW -->
	</div><!--- END CONTAINER -->
</div>
<!-- END NAVBAR -->
