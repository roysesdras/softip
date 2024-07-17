<?php
// Inclure le fichier de configuration pour initialiser la connexion à la base de données
include('admin/config.php');

// Récupérer les formations depuis la base de données
$stmt = $pdo->query("SELECT * FROM formations");
$formations = $stmt->fetchAll();

// Récupérer les annonces depuis la base de données
$stmtAnnonces = $pdo->query("SELECT * FROM annonces");
$annonces = $stmtAnnonces->fetchAll();
?>

<?php
  //variable a utiliser sure l'index
  $title="Soft IP Technology : l'expertise technique à portée de main !";
?>

<!DOCTYPE html>

<!-- En-tête de page -->
<html lang="fr" data-bs-theme="auto">
<?php include ('inclusion/head.php'); ?>
<body>
    <?php //include_once ('inclusion/mode_theme.php'); ?>
    <?php include_once ('inclusion/navbar.php'); ?>
    <img src="assets/img/banner.svg" alt="" class="w-100 banner mb-4" loading="lazy" style="box-shadow: rgba(17, 12, 46, 0.15) 0px 48px 100px 0px;">

    <div class="container">
        <div class="row">
            <!-- les actualités -->
            <div class="col-md-9 order-md-1 order-2 pb-4">
                <h3 class="fst-italic border-bottom bg-secondary-subtle rounded p-3" style="color:#033e60;">Nos Formations</h3>
                
                    <?php include_once ('inclusion/formation.php'); ?>
                
                    <?php include_once ('inclusion/nos_valeurs.php'); ?>
   
                    <?php include_once ('inclusion/vitrine_visuelle.php'); ?>
            </div>
              

            <!-- Les Evenement -->
            <div class="col-md-3 order-md-2 order-1">
                <div class="position-sticky" style="top: 2rem">
                    <div class="p-2 bg-primary-subtle rounded">
                        <h4 class="fst-italic evenement" style="color:#ed0505;">Annonces</h4>
                    </div>
                    <div class="row">
                        <?php include_once ('inclusion/annonce.php'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
        <h4 class="text-start" style="color:#033e60;">Contact</h4>
        <p>
          Pour toute question ou préoccupation, veuillez nous contacter via le formulaire ou directement.</p>
          <div class="col-md-8 shadow p-3">
          <form method="post" action="contact.php">
                <div class="row">
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label>Nom complet</label>
                        <input type="text" class="form-control" name="fullname" required placeholder="veuillez entrer votre Prénom et Nom">
                      </div>

                      <!-- mail adress -->
                      <div class="col-md-6 mb-3">
                        <label>Email address</label>
                        <input type="email" class="form-control" name="email" required placeholder="votre adresse e-mail valide">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="mb-3">
                      <label>Votre méssage</label>
                      <textarea class="form-control" rows="3" name="message" required placeholder="parlez moi de votre préoccupation"></textarea>
                    </div>
                  </div>
                </div>
                <button type="submit" class="button">Envoyer</button>
              </form>
          </div>

          <div class="col-md-4">
          <ul class="list-unstyled mb-0">
                      <li><i class="bi bi-geo-alt-fill mt-4 fa-2x" style="font-size: 2rem;"></i>
                          <p>Ouidah, Bénin</p>
                      </li>

                      <li><i class="bi bi-whatsapp mt-4 fa-2x" style="font-size: 2rem;"></i>
                          <p>+229-967-350-00</p>
                      </li>

                      <li><i class="bi bi-envelope mt-4 fa-2x" style="font-size: 2rem;"></i></i>
                          <p>info@softiptechnology.com</p>
                      </li>
                  </ul>
          </div>

        </div>
    </div>


    <?php include_once('inclusion/footer.php'); ?>
</body>
</html>