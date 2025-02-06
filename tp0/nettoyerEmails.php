<?php
session_start();

function estEmailValide($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

$emails = file('Emails.txt', FILE_IGNORE_NEW_LINES);

$emailsValides = [];
$emailsNonValides = [];

foreach ($emails as $email) {
    if (estEmailValide($email)) {
        $emailsValides[] = $email;
    } else {
        $emailsNonValides[] = $email;
    }
}

if (!file_exists('adressesNonValides.txt') || filesize('adressesNonValides.txt') == 0) {
    file_put_contents('adressesNonValides.txt', implode(PHP_EOL, $emailsNonValides));
}

file_put_contents('Emails.txt', implode(PHP_EOL, $emailsValides));

echo "<p class='message'> Les adresses non valides ont été supprimées et enregistrées dans 'adressesNonValides.txt'. </p><br>\n";

$frequenceEmails = array_count_values($emailsValides);

$messageDoublons = '';

if (isset($_POST['supprimerDoublons'])) {
    $emailsValides = array_unique($emailsValides);
    file_put_contents('Emails.txt', implode(PHP_EOL, $emailsValides));
    $messageDoublons = "<p class='message'>Les doublons ont été supprimés.</p><br>\n";
    // Stocker le message dans la session
    $_SESSION['messageDoublons'] = "Les doublons ont été supprimés.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['trierEmails'])) {
    sort($emailsValides);

    file_put_contents('EmailsT.txt', implode(PHP_EOL, $emailsValides));

    // Recharger les emails triés depuis le fichier EmailsT.txt
    $emailsValides = file('EmailsT.txt', FILE_IGNORE_NEW_LINES);

    $frequenceEmails = array_count_values($emailsValides);

    // Stocker les emails triés dans la session
    $_SESSION['emailsTries'] = $emailsValides;

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Charger les emails triés depuis la session, s'ils existent
if (isset($_SESSION['emailsTries'])) {
    $emailsValides = $_SESSION['emailsTries'];
    unset($_SESSION['emailsTries']);
    $frequenceEmails = array_count_values($emailsValides);
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nettoyage des Emails</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <h2>Fréquence des emails valides</h2>
    <table border="1">
        <tr>
            <th>Email</th>
            <th>Fréquence</th>
        </tr>
        <?php

        foreach ($frequenceEmails as $email => $frequence) {
            echo "
        <tr>
            <td>" . htmlspecialchars($email) . "</td>
            <td>" . $frequence . "</td>
        </tr>";
        }
        ?>

    </table>

    <?php
    // Affichage du message de doublons supprimés, s'il existe
    if (isset($_SESSION['messageDoublons'])) {
        echo "<p class='message'>" . $_SESSION['messageDoublons'] . "</p><br>\n";
        unset($_SESSION['messageDoublons']);  // Supprimer le message après affichage
    }
    ?>

    <form method="post">
        <button type="submit" name="supprimerDoublons">Supprimer les doublons</button>
    </form>

    <form method="post">
        <button type="submit" name="trierEmails">Trier les emails</button>
    </form>


    <h3>Emails triés :</h3>

    <table>
        <tr>
            <th>Email</th>
            <th>Fréquence</th>
        </tr>
        <?php
        $frequenceEmailsTries = array_count_values($emailsValides);
        if (!empty($emailsValides)) {
            foreach ($emailsValides as $email) {
                echo "
            <tr>
                <td>" . htmlspecialchars($email) . "</td>
                <td>" . $frequenceEmailsTries[$email] . "</td>
            </tr>";
            }
        }
        ?>
    </table>

    <form method="post" action="separer_par_domaine.php">
        <button type="submit" name="separerParDomaine">Séparer les emails par domaine</button>
    </form>

    <?php
    if (isset($_SESSION['messageDomaine'])) {
        echo "<p class='message'>" . $_SESSION['messageDomaine'] . "</p><br>\n";
        unset($_SESSION['messageDomaine']);
    }
    ?>




</body>

</html>