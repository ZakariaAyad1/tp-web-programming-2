<?php
// Fonction pour lire les emails depuis un fichier
function readEmails($filename) {
    return file_exists($filename) ? file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
}

// Fonction pour vérifier la validité d'un email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Fonction pour traiter les emails
function processEmails($inputFile, $invalidEmailsFile) {
    $emails = readEmails($inputFile); // Lire les emails du fichier
    $validEmails = [];
    $invalidEmails = [];

    foreach ($emails as $email) {
        if (isValidEmail($email)) {
            $validEmails[] = $email; // Garder les emails valides
        } else {
            $invalidEmails[] = $email; // Ajouter à la liste des emails non valides
        }
    }

    // Enregistrer les emails non valides dans un fichier
    file_put_contents($invalidEmailsFile, implode("\n", $invalidEmails));

    // Mettre à jour Emails.txt en supprimant les emails non valides
    file_put_contents($inputFile, implode("\n", $validEmails));

    return [
        'total' => count($emails),
        'valid' => count($validEmails),
        'invalid' => count($invalidEmails)
    ];
}

// Fichiers utilisés
$emailsFile = "Emails.txt";
$invalidEmailsFile = "adressesNonValides.txt";

// Exécuter le traitement
$stats = processEmails($emailsFile, $invalidEmailsFile);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat du Traitement</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Résultat du Traitement</h2>
        <p><strong>Total des emails :</strong> <?php echo $stats['total']; ?></p>
        <p><strong>Emails valides restants :</strong> <?php echo $stats['valid']; ?></p>
        <p><strong>Emails non valides supprimés :</strong> <?php echo $stats['invalid']; ?></p>
        <a href="adressesNonValides.txt" class="btn" target="_blank">Voir les emails non valides</a>
        <br>
        <a href="Emails.txt" class="btn" target="_blank">Voir les emails valides</a>
        <br>
        <a href="index.html" class="btn">Retour</a>
    </div>

    <script>
        function afficherEmails() {
            fetch('getEmails.php')
                .then(response => response.text())
                .then(data => {
                    let emailsDiv = document.getElementById('emailsValides');
                    emailsDiv.style.display = 'block';
                    emailsDiv.innerHTML = `<strong>Emails Valides :</strong><br><pre>${data}</pre>`;
                })
                .catch(error => console.error('Erreur:', error));
        }
    </script>
</body>
</html>
