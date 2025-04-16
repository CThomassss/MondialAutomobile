-- Création de la base de données
CREATE DATABASE IF NOT EXISTS mondialautomobile
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE mondialautomobile;

-- Table : utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL, -- Mot de passe hashé
    role ENUM('admin', 'attente') DEFAULT 'attente', -- Rôle de l'utilisateur
    email VARCHAR(150) NOT NULL UNIQUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table : voitures
CREATE TABLE IF NOT EXISTS voitures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(100) NOT NULL,
    annee INT NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    kilometrage INT NOT NULL,
    carburant VARCHAR(50) DEFAULT NULL,
    boite VARCHAR(50) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    images TEXT DEFAULT NULL, -- Stockage des chemins d'images en JSON
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    est_vendu BOOLEAN DEFAULT FALSE,
    est_visible TINYINT(1) DEFAULT 0,
    vendeur_id INT DEFAULT NULL,
    FOREIGN KEY (vendeur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table : reprises
CREATE TABLE IF NOT EXISTS reprises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    marque VARCHAR(100) DEFAULT NULL,
    modele VARCHAR(100) DEFAULT NULL,
    annee INT DEFAULT NULL,
    kilometrage INT DEFAULT NULL,
    immatriculation VARCHAR(20) DEFAULT NULL,
    etat ENUM('neuf', 'occasion', 'accidente') DEFAULT 'occasion',
    historique TEXT DEFAULT NULL,
    description TEXT DEFAULT NULL,
    statut ENUM('En attente', 'Acceptée', 'Refusée') DEFAULT 'En attente',
    date_demande DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table : services
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_service VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    icone VARCHAR(255) DEFAULT NULL,
    prix DECIMAL(10,2) DEFAULT NULL
);

-- Table : messages_contact
CREATE TABLE IF NOT EXISTS messages_contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    sujet VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table : avis_google
CREATE TABLE IF NOT EXISTS avis_google (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    note INT NOT NULL CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT DEFAULT NULL,
    date_avis DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table : panier
CREATE TABLE IF NOT EXISTS panier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    voiture_id INT NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (voiture_id) REFERENCES voitures(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table : faq
CREATE TABLE IF NOT EXISTS faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    reponse TEXT NOT NULL
);


INSERT INTO faq (question, reponse) VALUES
('Quels sont vos horaires d\'ouverture ?', 'Nous sommes ouverts du lundi au vendredi de 9h à 18h, et le samedi de 10h à 16h.'),
('À quelle heure vous ouvrez ?', 'Nous sommes ouverts du lundi au vendredi de 9h à 18h, et le samedi de 10h à 16h.'),
('Quand êtes-vous ouverts ?', 'Nous sommes ouverts du lundi au vendredi de 9h à 18h, et le samedi de 10h à 16h.'),
('Est-ce que vous êtes ouverts le samedi ?', 'Oui, nous sommes ouverts le samedi de 10h à 16h.'),
('Vous êtes ouverts le dimanche ?', 'Non, nous sommes fermés le dimanche.'),
('Où vous trouvez-vous ?', 'Nous sommes situés au 123 Rue de l\'Automobile, 75000 Paris, France.'),
('Quelle est votre adresse ?', 'Nous sommes situés au 123 Rue de l\'Automobile, 75000 Paris, France.'),
('Vous êtes dans quelle ville ?', 'Nous sommes à Paris, 123 Rue de l\'Automobile.'),
('Peut-on acheter une voiture chez vous ?', 'Oui, nous vendons des véhicules neufs et d\'occasion.'),
('Vendez-vous des voitures ?', 'Oui, nous proposons une large gamme de véhicules.'),
('Avez-vous des voitures d\'occasion ?', 'Oui, nous avons de nombreux véhicules d\'occasion avec garantie.'),
('Quel type de véhicules vous vendez ?', 'Nous proposons des véhicules neufs et d\'occasion pour tous les budgets.'),
('Puis-je vendre ma voiture ici ?', 'Oui, nous rachetons les véhicules. Veuillez remplir le formulaire de reprise sur notre site.'),
('Vous reprenez les véhicules ?', 'Oui, nous proposons un service de reprise de véhicules.'),
('Comment vendre mon véhicule chez vous ?', 'Il suffit de remplir notre formulaire de reprise sur le site.'),
('Proposez-vous du financement ?', 'Oui, nous proposons des solutions de financement adaptées à vos besoins.'),
('Est-ce possible de payer en plusieurs fois ?', 'Oui, nous proposons le paiement en 3x ou 4x sans frais sous conditions.'),
('Vous faites du crédit auto ?', 'Oui, nous avons des offres de financement disponibles.'),
('Peut-on faire un essai avant d\'acheter ?', 'Oui, nous proposons des essais sur rendez-vous.'),
('Je peux essayer une voiture avant de l\'acheter ?', 'Oui, vous pouvez réserver un essai directement sur notre site.'),
('Faites-vous les contrôles techniques ?', 'Nous ne faisons pas les contrôles techniques mais nous pouvons vous conseiller un centre agréé.'),
('Vous vous occupez du contrôle technique ?', 'Non, mais nous pouvons vous orienter vers un partenaire agréé.'),
('Puis-je avoir un devis ?', 'Oui, veuillez nous contacter avec les détails et nous vous fournirons un devis.'),
('Je veux un prix pour une réparation', 'Bien sûr, contactez-nous avec les infos sur la réparation et nous ferons un devis.'),
('Combien coûte une vidange ?', 'Le prix d\'une vidange commence à partir de 79€, selon le modèle.'),
('Quel est le tarif d\'entretien ?', 'Cela dépend du type d\'entretien. Contactez-nous pour un devis précis.'),
('Proposez-vous une garantie ?', 'Oui, tous nos véhicules d\'occasion ont une garantie minimale de 6 mois.'),
('Les voitures sont garanties ?', 'Oui, elles sont garanties.'),
('C\'est garanti si j\'achète une voiture ?', 'Oui, vous bénéficiez d\'une garantie de 6 mois minimum.'),
('Comment prendre rendez-vous ?', 'Vous pouvez prendre rendez-vous en ligne ou par téléphone.'),
('Je veux réserver un créneau', 'Prenez rendez-vous via notre page contact.'),
('Comment réserver un entretien ?', 'Via notre site ou en nous appelant directement.'),
('Faites-vous les cartes grises ?', 'Oui, nous nous occupons de toutes les démarches administratives.'),
('Vous vous chargez de l\'immatriculation ?', 'Oui, nous faisons les cartes grises.'),
('Livrez-vous les voitures ?', 'Oui, nous livrons dans toute la France.'),
('La livraison est-elle possible ?', 'Oui, nous proposons la livraison à domicile.'),
('Puis-je réserver un véhicule ?', 'Oui, vous pouvez réserver en ligne.'),
('Est-ce que je peux bloquer une voiture ?', 'Oui, via notre service de réservation en ligne.'),
('Quels sont vos délais de livraison ?', 'En moyenne, la livraison prend 5 à 10 jours ouvrés.'),
('Combien de temps pour livrer une voiture ?', 'Comptez environ une semaine pour la livraison.'),
('Faites-vous des remises ?', 'Nous proposons régulièrement des offres spéciales.'),
('Y a-t-il des promotions ?', 'Oui, consultez nos offres en cours sur le site.'),
('Est-ce que vous avez des tarifs pour étudiants ?', 'Oui, nous avons des offres pour étudiants sur présentation d’un justificatif.'),
('Offrez-vous des réductions pour entreprises ?', 'Oui, contactez-nous pour un devis entreprise.'),
('Que dois-je apporter pour acheter une voiture ?', 'Une pièce d\'identité, un justificatif de domicile, et un moyen de paiement.'),
('Quels papiers faut-il pour acheter ?', 'Une carte d’identité, justificatif de domicile et moyen de paiement suffisent.'),
('Quels documents sont nécessaires ?', 'Justificatif de domicile, pièce d’identité, et moyen de paiement.'),
('Puis-je vous appeler ?', 'Oui, vous pouvez nous joindre au numéro indiqué sur notre site.'),
('Comment vous contacter ?', 'Via le formulaire de contact ou par téléphone.'),
('Quel est votre numéro de téléphone ?', 'Vous pouvez nous appeler au 01 23 45 67 89.');
