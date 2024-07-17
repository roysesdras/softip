
<style>
  a{
    color: #033e60;
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
          <a href="./formation/dev_web.php">Développement Web</a> <br>
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
<script src="./assets/dist/js/bootstrap.bundle.min.js"></script>