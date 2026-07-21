<?php
// Détermine la page active pour le mettre en surbrillance dans le menu
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- START NAVBAR (visiteur non connecté) -->
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
						<!--li><a href="profil.php" class="<?= $current_page == 'profil.php' ? 'active' : '' ?>">Profil</a></li-->
						<li><a href="ajoutlivres.php" class="<?= $current_page == 'ajoutlivres.php' ? 'active' : '' ?>">Publier</a></li>
					</ul>
				</nav>
			</div><!--- END Col -->

			<div class="col-20 d-none d-xl-block text-end align-self-center">
				<a href="login.php" class="header-btn">Se connecter</a>
				<a href="inscription.php" class="btn_one">S'inscrire</a>
			</div><!--- END Col -->

			<ul class="mobile_menu">
				<li><a href="index.php">Accueil</a></li>
				<li><a href="meslivres.php">Mes Livres</a></li>
				<li><a href="profil.php">Profil</a></li>
				<li><a href="ajoutlivres.php">Publier</a></li>
				<li><a href="login.php">Se connecter</a></li>
				<li><a href="inscription.php">S'inscrire</a></li>
			</ul>
		</div><!--- END ROW -->
	</div><!--- END CONTAINER -->
</div>
<!-- END NAVBAR -->
