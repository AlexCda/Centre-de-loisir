<?php
/**
 * Établit une connexion à la base de données MySQL.
 *
 * Cette fonction tente de créer une nouvelle connexion PDO à une base de données MySQL.
 * Elle utilise des paramètres de connexion codés en dur pour se connecter à la base 'centre_de_loisir'.
 * En cas d'échec de la connexion, elle affiche un message d'erreur et termine le script.
 *
 * @return PDO L'objet PDO représentant la connexion à la base de données.
 * @throws PDOException Si la connexion échoue, une exception PDO est levée.
 */
function testConnexion() {
    try {
        // Tentative de création d'une nouvelle connexion PDO
        $mysqlClient = new PDO(
            'mysql:host=localhost;dbname=centre_de_loisir;charset=utf8', // DSN (Data Source Name)
            'mariadb',                                                   // Nom d'utilisateur
            'mariadb*1',                                                 // Mot de passe
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]                // Options PDO
        );
    } catch (Exception $e) {
        // En cas d'erreur, affiche le message d'erreur et termine le script
        die('Erreur : ' . $e->getMessage());
    } 
    // Retourne l'objet de connexion PDO
    return $mysqlClient;
}
?>