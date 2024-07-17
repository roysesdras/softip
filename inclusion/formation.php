<style>
        a{
            text-decoration: none;
        }
        .formations-container {
            display: flex;
            overflow-x: auto;
            white-space: nowrap;
            padding: 1em 0;
        }

        .formations-container::-webkit-scrollbar {
            height: 8px;
        }

        .formations-container::-webkit-scrollbar-thumb {
            background-color:#A9A9A9;
            border-radius: 4px;
        }

        .formations-container::-webkit-scrollbar-track {
            background-color: #f1f1f1;
        }

        .actu {
            box-shadow: rgba(0, 0, 0, 0.45) 0px 25px 20px -20px;
            padding: 0.1em;
            margin-bottom: 1em;
            display: inline-block;
            width: 300px; /* Largeur de chaque élément pour assurer le défilement horizontal */
            margin-right: 1em; /* Espace entre les éléments */
            transition: transform 0.3s ease-in-out;
        }

        .actu:hover {
            transform: scale(1.05);
        }
    </style>
        
        <div class="formations-container">
            <?php foreach ($formations as $formation): ?>
                <div class=" p-3 actu">
                    <div class="">
                        <a href="./formations/formation_details.php?id=<?php echo $formation['id']; ?>">
                            <img src="<?php echo $formation['image_url']; ?>" class="w-100" alt="<?php echo $formation['titre']; ?>">
                        </a>
                        <div class="">
                            <h5 class="card-title"><?php echo $formation['titre']; ?></h5>
                            <?php
                            // Récupérer la description courte de maximum 100 mots
                            $short_description = strip_tags($formation['description']); // Supprime les balises HTML
                            $short_description = substr($short_description, 0, 35); // Limite à 100 caractères
                            $short_description = rtrim($short_description, " \t\n\r\0\x0B.,"); // Supprime les derniers caractères non alphanumériques
                            ?>
                            <p class="card-text"><?php echo $short_description; ?>...</p>
                            <a href="./formations/formation_details.php?id=<?php echo $formation['id']; ?>" class="">
                                En savoir +
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>      