<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un cours</title>
</head>
<body>
    <h1>Ajouter un cours</h1>
    <form action="ajouter_cours_etapes.php" method="post" enctype="multipart/form-data">
        <!-- Informations du cours -->
        <label for="nom">Nom du cours :</label>
        <input type="text" id="nom" name="nom" required><br><br>

        <label for="image">Image :</label>
        <input type="file" id="image" name="image" accept="image/*" required><br><br>

        <label for="description">Description :</label>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="duree">Durée (en heures) :</label>
        <input type="number" id="duree" name="duree" required><br><br>

        <label for="niveau">Niveau :</label>
        <select id="niveau" name="niveau" required>
            <option value="Débutant">Débutant</option>
            <option value="Intermédiaire">Intermédiaire</option>
            <option value="Avancé">Avancé</option>
        </select><br><br>

        <label for="formation_id">Formation :</label>
        <select id="formation_id" name="formation_id" required>
        <?php
            include('../admin/config.php');
            $stmt = $pdo->query("SELECT id, titre FROM formations");
            while ($row = $stmt->fetch()) {
                echo "<option value=\"" . htmlspecialchars($row['id']) . "\">" . htmlspecialchars($row['titre']) . "</option>";
            }
        ?>
        </select><br><br>

        <label for="formateur_id">Formateur :</label>
        <select id="formateur_id" name="formateur_id" required>
        <?php
            $stmt = $pdo->query("SELECT id, username FROM trainers");
            while ($row = $stmt->fetch()) {
                echo "<option value=\"" . htmlspecialchars($row['id']) . "\">" . htmlspecialchars($row['username']) . "</option>";
            }
        ?>
        </select><br><br>

        <!-- Étapes du cours -->
        <h2>Ajouter les étapes du cours</h2>
        <div id="steps-container">
            <div class="step-container">
                <label for="step_number_1">Numéro de l'étape :</label>
                <input type="number" id="step_number_1" name="step_number[]" required><br><br>

                <label for="content_1">Contenu de l'étape :</label>
                <textarea id="content_1" name="content[]" required></textarea><br><br>
            </div>
        </div>
        <button type="button" onclick="addStep()">Ajouter une autre étape</button><br><br>

        <input type="submit" value="Ajouter le cours et les étapes">
    </form>

    <script>
        let stepCount = 1;

        function addStep() {
            stepCount++;
            const container = document.getElementById('steps-container');
            const newStep = document.createElement('div');
            newStep.classList.add('step-container');
            newStep.innerHTML = `
                <label for="step_number_${stepCount}">Numéro de l'étape :</label>
                <input type="number" id="step_number_${stepCount}" name="step_number[]" required><br><br>

                <label for="content_${stepCount}">Contenu de l'étape :</label>
                <textarea id="content_${stepCount}" name="content[]" required></textarea><br><br>
            `;
            container.appendChild(newStep);
        }
    </script>
</body>
</html>
