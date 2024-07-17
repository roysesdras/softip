<!DOCTYPE html>
<html lang="fr" data-bs-theme="auto">
<head><script src="./assets/js/color-modes.js"></script>
    <meta charset="UTF-8">
    <!-- meta for SEO -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="description" content=" Soft IP Technology offre des formations pointues en technologie et informatique, vous préparant à exceller dans les domaines techniques et professionnels les plus demandés.">
    <meta property="og:title" content="Soft IP Technology : l'expertise technique à portée de main !" />
    <meta property="og:description" content="Soft IP Technology offre des formations pointues en technologie et informatique, vous préparant à exceller dans les domaines techniques et professionnels les plus demandés." />
    <!-- Favicons -->
    <link href="./assets/img/favicon.png" rel="icon">
    <link href="./assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <!-- meta for og.graph -->
    <meta property="og:image" content="https://i.postimg.cc/MHHjkcG5/SOFT-IP.png" />
    <!-- <meta property="og:url" content="https://sternaafrica.org/" /> -->
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="sternaafrica" />
    <title>Inscription - Soft IP Technology</title>
    <!-- all css -->
    <!-- <link rel="canonical" href="https://sternaafrica.org/"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link rel="stylesheet" href="style.css">
    <link href="./assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>
    <?php include_once ('inclusion/mode_theme.php'); ?>
    <?php include_once ('inclusion/navbar.php'); ?>

     <div class="bg-primary-subtle">
        <h1 class="text-center p-3">Inscription</h1>
    </div>

    <!-- <div class="container-fluid">
        <h3 class="p-4 fst-italic border-bottom" style="color:#305196;">Membres du Bureau Internationale</h3>
        <?php //include_once('./inclusion/bureau_inter.php'); ?>
    </div> -->

    <div class="container">
        <div class="row">
            <p class="text-center">NB: Veuillez ne pas remplir ce formulaire si vous n'êtes pas prêt à payer. <br>Ce formulaire d'inscription doit être complété avec des informations exactes. <br>Veuillez noter que tous les champs sont obligatoires et que des frais d'inscription de 7 000 F CFA s'appliquent.</p>
            <div class="col-md-3"></div>
            
            <div class="col-md-6 pt-2">
                        <form action="mail_inscription.php" method="post" class="mb-4 p-2 shadow p-3" id="forms">
                            <!-- Nom -->
                            <div class="mb-3">
                                <label>Nom complet </label>
                                <input type="text" class="form-control" name="full_name" required>
                            </div>

                            <!-- E-mail -->
                            <div class="mb-4">
                                <label>E-Mail</label>
                                <input type="email" class="form-control" name="email" placeholder="test@example.com" required>
                            </div>

                            <!-- Numero de telephone/whatsap -->
                            <div class="mb-4">
                                <label>N. Télephone (whatsapp)</label>
                                <input type="tel" class="form-control" name="phone" max="15" placeholder="(+XXX) xxxxxx" required>
                            </div>

                            <!-- Adresse complète -->
                            <div class="mb-3">
                                <label>Adresse</label>
                                <input type="text" class="form-control" name="adresse" required placeholder="votre adresse complete.">
                            </div>

                            <!-- Option d'escorte choisie -->
                            <select class="form-select mb-3 form-control" name="formation_choisi" required>
                                <option selected>Formation ou domaine d'intérêt</option>
                                <option value="cybersécurité">Cybersécurité</option>
                                <option value="dévellepement_web">Développement Web</option>
                                <option value="graphisme">Graphisme</option>
                                <option value="gsm">GSM</option>
                                <option value="informtique_bureau">Informatique Bureau</option>
                                <option value="audiovisuel">Audiovisuel</option>
                                <option value="markrting_digital">Marketing Digital</option>
                                <option value="maintenance_reseau">Maintenance & Réseaux Informatique</option>
                                <option value="antenne_parabolique">Installation parabolique</option>
                                <option value="electricité_batiment">Electricité Bâtiment</option>
                                <option value="montage_camera">Montage des caméras de surveillance</option>
                            </select>

                            <div class="col-md-12">
                                <div class="mb-3">
                                <label>Votre méssage</label>
                                <textarea class="form-control" rows="3" name="message" required placeholder="parlez nous un plus de vous; sur votre niveau d'éducation, vos expérience proffessionnel......"></textarea>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="gridCheck" required>
                                <label class="form-check-label" for="gridCheck">
                                En soumettant ce formulaire, vous confirmez votre inscription à la formation.
                                </label>
                            </div>
    
                            <input type="submit" name="submit" class="button p-1" value="Je m'inscris maintenant">
                        </form>
            
            
            </div>
            <div class="col-md-3"></div>
            
        </div> 
        </div>
    </div> 




    <?php include_once('./inclusion/footer.php'); ?>
    <script src="./assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

