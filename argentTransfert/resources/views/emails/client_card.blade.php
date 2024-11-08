<!DOCTYPE html>
<html>
<head>
    <title>Votre Carte Client</title>
</head>
<body>
    <h1>Bonjour, {{ $client->prenom }} {{ $client->nom }}</h1>

    <p>Merci de vous être inscrit ! Voici votre carte client contenant un QR code unique. 
       Veuillez le garder précieusement, car il vous permettra d'accéder aux services de notre application.</p>

    <p>Votre numéro de téléphone enregistré : {{ $client->telephone }}</p>

    <p>La carte est jointe à cet email. N'hésitez pas à nous contacter pour toute question.</p>

    <p>Cordialement,<br>L'équipe de Support</p>
</body>
</html>
