<?php
session_start();
include('../admin/config.php');

// Vérifier si l'identifiant de la formation est passé dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Rediriger vers une page d'erreur ou retourner à la liste des formations
    header('Location: ../index.php');
    exit;
}

// Récupérer l'identifiant de la formation depuis l'URL
$formation_id = $_GET['id'];

// Préparer la requête pour récupérer les détails de la formation
$stmt = $pdo->prepare("SELECT * FROM formations WHERE id = ?");
$stmt->execute([$formation_id]);
$formation = $stmt->fetch();

// Vérifier si la formation existe
if (!$formation) {
    // Rediriger vers une page d'erreur ou retourner à la liste des formations
    header('Location: ../index.php');
    exit;
}
?>

<?php 
    $title = $formation['titre'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <script src="../assets/js/color-modes.js"></script>
    <meta charset="UTF-8">
    <!-- meta for SEO -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="description" content=" Soft IP Technology offre des formations pointues en technologie et informatique, vous préparant à exceller dans les domaines techniques et professionnels les plus demandés.">
    <meta property="og:title" content="Soft IP Technology : l'expertise technique à portée de main !" />
    <meta property="og:description" content="" />
    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <!-- meta for og.graph -->
    <meta property="og:image" content="<?php echo $formation['image_url']; ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="sternaafrica" />
    <title><?php echo $title; ?></title>

    <!-- all css -->
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
<body>
<?php include_once ('../inclusion/navbar.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-8 entry">

                <!-- section recupération de titre -->
                <h2 class=""><?php echo $formation['titre']; ?> : Du Débutant à l'Expert</h2>

                <!-- section recupération de l'image -->
                <div class="mb-3">
                    <img src="<?php echo $formation['image_url']; ?>" class="w-100" alt="<?php echo $formation['titre']; ?>">
                </div>

                <!-- section recupération d'escription -->
                <div class="">
                  <p><?php echo $formation['description']; ?></p>
                </div>
               
                <!-- boutton -->
                <div class="mb-3">
                  <input onclick="redirectToPage()" type="button" value="Je m'inscrire" />
                </div>
                
            </div>

            <div class="col-md-4">
                <div class="position-sticky entry" style="top: 2rem">
                    <div class="">
                        <img src="../avatar_img/registration-online-vector-illustration-interface-260nw-2411357033.jpg" alt="" class="w-100">
                         <p class="pt-2">
                          Votre inscription vous confère un accès privilégié à : 
                            <ul>
                              <li>Programmes de formation en ligne et en présentiel</li>
                              <li>Espace étudiant personnalisé</li>
                              <li>Accès intégral à une bibliothèque de ressources variées</li>
                              <li>Participation active au forum de partage de connaissances</li>
                              <li>Suivi personnalisé par un expert de votre domaine</li>
                            </ul>
                         </p>

                         <div class="div p-3">
                            <input onclick="redirectToPage()" type="button" value="Réserver une place" />
                         </div>
                         
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <script>
    function redirectToPage() {
        window.location.href = '../students/inscription_formation.php';
    }
    </script>


<style>
  input{
    background-color: #033e60;
    color: #ffffff;
    padding: 5px;
    border          : solid transparent;
    font-size: 20px
  }
  input:hover{
    background-color: transparent;
    color: #033e60;
    padding: 5px;
    border          : solid 1px #033e60;
  }
  a{
    color: #033e60;
    text-decoration:none;
  }

</style>

<div class="footer">
  <div class="container-fluid">
    
    <div class="row">

      <div class="col-lg-3 col-md-6 footer-contact pt-3">
        <img src="https://i.postimg.cc/MHHjkcG5/SOFT-IP.png" alt="sterna-africa" class="w-25">
        <p>
          Adiaké  <br>
          Côte d'Ivoire<br>
          Afrique <br><br>
          
        </p>
      </div>

      <div class="col-lg-3 col-md-6 footer-links pt-3">
        <h4>Nos Formations</h4> 
        <!-- mettre le liens absolue qui redirige vers chaque page de formation -->
          <a href="#">Développement Web</a> <br>
          <a href="https://sternaafrica.org/antenne/burkinaFaso.php">Graphisme</a> <br>
          <a href="https://sternaafrica.org/antenne/congoBrazza.php">Gsm</a> <br>
          <a href="https://sternaafrica.org/antenne/CotedIvoire.php">Marketing Digital</a> <br>
          <a href="https://sternaafrica.org/antenne/togo.php" translate="no">Audiovisuel</a> <br><br>
      </div>

  
      <div class="col-lg-3 col-md-6 footer-links pt-3">
          <h4>Liens utiles</h4>
          <!-- mettre le liens absolue qui redirige vers chaque page de formation -->
          <a href="https://sternaafrica.org/pages/about.php">Cuisine</a><br>
          <a href="https://sternaafrica.org/ils_parlent.php">Coiffure</a><br>
          <a href="https://sternaafrica.org/rapport/annee_2023">Couture</a><br>
          <a href="https://sternaafrica.org/rapport/annee_2023">Décoration</a><br><br>
      </div>

      <div class="col-lg-3 col-md-6 footer-links pt-2">
        <h4>Contact</h4>
        <strong>Email:</strong> <a href="mailto:contact@softiptechnology.com">contact@softiptechnology.com</a><br>
          <strong>Contact:</strong> +229-967-350-00<br>
      </div>
     

    </div>
  </div>
</div>

<div class=" d-md-flex py-4 linkFooter">
    <div class="px-2 copyright">
      <p>
        &copy; <strong><span>Soft IP Technology</span></strong>. Tout droit réservé 
      </p>
    </div>
</div>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <script>
    // Sélectionne l'élément .back-to-top
    let backToTopButton = document.querySelector('.back-to-top');

    // Ajoute un écouteur d'événement au défilement de la fenêtre
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
        backToTopButton.classList.add('active');
        } else {
        backToTopButton.classList.remove('active');
        }
    });

    // Ajoute un écouteur d'événement pour cliquer sur le bouton
    backToTopButton.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({top: 0, behavior: 'smooth'});
    });
    </script>

<!-- bas / pied de page -->
    <script src='https://code.jquery.com/jquery-2.2.4.min.js'></script>
    <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
