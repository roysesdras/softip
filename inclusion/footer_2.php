<style>
    html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            margin-top: 30px;
            flex: 1; /* Permet au conteneur de prendre tout l'espace disponible */
        }

        .linkFooter {
            background-color: #033e60;
            color: #fff;
            text-align: center;
        }

        .linkFooter p {
            margin: 0;
            padding: 10px 0;
        }


        .back-to-top {
    position     : fixed;
    visibility   : hidden;
    opacity      : 0;
    right        : 15px;
    /* Change from right to left */
    bottom       : 15px;
    z-index      : 996;
    background   : #033e60;
    width        : 40px;
    height       : 40px;
    border-radius: 50px;
    transition   : all 0.4s;
    box-shadow   : rgba(0, 0, 0, 0.35) 0px 5px 15px;
}

.back-to-top i {
    font-size  : 28px;
    color      : #fff;
    line-height: 0;
}

.back-to-top:hover {
    background: #033e60;
    color     : #fff;
}

.back-to-top.active {
    visibility: visible;
    opacity   : 1;
}
</style>

<footer class="linkFooter">
        <p>&copy; <strong><span>Soft IP Technology</span></strong>. Tout droit réservé</p>
    </footer>

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