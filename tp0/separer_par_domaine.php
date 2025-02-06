<?php
session_start();

function getDomaine($email)
{
    $parts = explode('@', $email);
    return count($parts) == 2 ? $parts[1] : 'invalide';
}

if (isset($_POST['separerParDomaine'])) {
    $emails = file('Emails.txt', FILE_IGNORE_NEW_LINES);
    $emailsParDomaine = [];

    foreach ($emails as $email) {
        $domaine = getDomaine($email);
        if ($domaine !== 'invalide') {
            $emailsParDomaine[$domaine][] = $email;
        }
    }

    foreach ($emailsParDomaine as $domaine => $emails) {
        file_put_contents("emails_$domaine.txt", implode(PHP_EOL, $emails));
    }

    $_SESSION['messageDomaine'] = "Les emails ont été séparés par domaine.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>