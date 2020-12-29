<?php
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\Yaml\Yaml; 
// activation du système d'autoloading de Composer
require_once __DIR__.'/../vendor/autoload.php';
// instanciation du chargeur de templates
$loader = new FilesystemLoader(__DIR__.'/../templates');
// instanciation du moteur de template
$twig = new Environment($loader, [
    // activation du mode debug
    'debug' => true,
    // activation du mode de variables strictes
    'strict_variables' => true,
]);
// chargement de l'extension DebugExtension
$twig->addExtension(new DebugExtension());
$config = Yaml::parseFile(__DIR__.'/config.yaml');


// traitement des données


$formData = [
    'identifiant' => '',
    'password' => '',
];
$errors = [];



if ($_POST) {
    foreach ($formData as $key => $value) {
        if (isset($_POST[$key])) {
            $formData[$key] = $_POST[$key];
        }
    }
    if (empty($_POST['identifiant'])){
        $errors['identifiant'] = 'Ce champ est requis';

    } elseif (strlen($_POST['identifiant']) >= 190) {
        $errors['identifiant'] = "Merci de bien vouloir rentrer une longueur de 190 caractères inclus";

    } elseif ($config['login'] != $_POST['identifiant']) {
        $errors['identifiant'] = 'Votre mot de passe ou login est invalide';
        $errors['password'] = 'Votre mot de passe ou login est invalide';
    }

$minLength = 8;
$maxLength = 32;

    if (empty($_POST['password'])) {
        $errors['password'] = 'Le champ password est requis';

    } elseif (strlen($_POST['password']) < 8 || strlen($_POST['password']) > 32) {
        $errors['password'] = "merci de renseigner un mot de passe dont la longueur est comprise
        entre {$minLength} et {$maxLength} inclus";

    } elseif (preg_match('/[0-9]/',  $_POST['password']) !== 1) {
        $errors['password'] = 'Votre mot de passe doit contenir au moins un chiffre';

    } elseif (preg_match('/[^A-Za-z]/', $_POST['password']) !== 1) {
        $errors['password'] = 'Votre mot de passe doit contenir au moins une lettre';

    } elseif (preg_match('/[^A-Za-z0-9]/', $_POST['password']) !== 1) {
        $errors['password'] = 'Votre mot de passe doit contenir au moins un caractère spécial';

    } elseif(!password_verify($_POST['password'], $config['password'])) {
        $errors['identifiant'] = 'Votre mot de passe ou login est invalide';
        $errors['password'] = 'Votre mot de passe ou login est invalide';
        
    }
    
}
dump($_POST);
dump($config);
dump($formData);


// affichage du rendu d'un template
echo $twig->render('login.html.twig', [
    // transmission de données au template
    'errors' => $errors,
    'formData' => $formData,
]);