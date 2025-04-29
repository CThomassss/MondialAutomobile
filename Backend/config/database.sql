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

ALTER TABLE voitures ADD COLUMN en_preparation TINYINT(1) DEFAULT 0;
ALTER TABLE voitures ADD COLUMN image_originale TEXT NULL;
-- Table : faq
CREATE TABLE IF NOT EXISTS faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    reponse TEXT NOT NULL
);

INSERT INTO faq (question, reponse) VALUES
-- QUESTIONS EXISTANTES CORRIGÉES
('Quels sont vos horaires d\'ouverture ?', 'Nous sommes ouverts du lundi au samedi de 10h à 18h.'),
('À quelle heure ouvrez-vous ?', 'Nous sommes ouverts du lundi au samedi de 10h à 18h.'),
('Quand êtes-vous ouverts ?', 'Nous sommes ouverts du lundi au samedi de 10h à 18h.'),
('Êtes-vous ouverts le samedi ?', 'Oui, nous sommes ouverts le samedi de 10h à 18h.'),
('Êtes-vous ouverts le dimanche ?', 'Non, nous sommes fermés le dimanche.'),
('Où vous situez-vous ?', 'Nous sommes situés au 31 Rue Motta Di Livenza, 32600 L\'Isle-Jourdain.'),
('Quelle est votre adresse ?', 'Nous sommes situés au 31 Rue Motta Di Livenza, 32600 L\'Isle-Jourdain.'),
('Dans quelle ville êtes-vous ?', 'Nous sommes situés à L\'Isle-Jourdain.'),
('Peut-on acheter une voiture chez vous ?', 'Oui, nous vendons des véhicules d\'occasion.'),
('Vendez-vous des voitures ?', 'Oui, nous proposons une large gamme de véhicules.'),
('Avez-vous des voitures d\'occasion ?', 'Oui, nous avons de nombreux véhicules d\'occasion avec garantie.'),
('Quel type de véhicules vendez-vous ?', 'Nous proposons des véhicules d\'occasion pour tous les budgets.'),
('Puis-je vendre ma voiture ici ?', 'Oui, nous rachetons les véhicules. Veuillez remplir le formulaire de reprise sur notre site.'),
('Reprenez-vous les véhicules ?', 'Oui, nous proposons un service de reprise de véhicules.'),
('Comment vendre mon véhicule chez vous ?', 'Il suffit de remplir notre formulaire de reprise sur le site.'),
('Proposez-vous du financement ?', 'Non, désolé.'),
('Est-ce possible de payer en plusieurs fois ?', 'Non, nous ne proposons pas le paiement en plusieurs fois.'),
('Faites-vous du crédit auto ?', 'Non, désolé.'),
('Peut-on faire un essai avant d\'acheter ?', 'Oui, nous proposons des essais sur rendez-vous.'),
('Puis-je essayer une voiture avant de l\'acheter ?', 'Oui, vous pouvez réserver un essai.'),
('Faites-vous les contrôles techniques ?', 'Oui, nous faisons les contrôles techniques.'),
('Vous occupez-vous du contrôle technique ?', 'Oui, nous faisons les contrôles techniques.'),
('Puis-je avoir un devis ?', 'Oui, veuillez nous contacter avec les détails et nous vous fournirons un devis.'),
('Je veux un prix pour une réparation.', 'Bien sûr, contactez-nous avec les informations sur la réparation et nous ferons un devis.'),
('Combien coûte une vidange ?', 'Le prix d\'une vidange varie selon le modèle du véhicule, contactez-nous !'),
('Quel est le tarif d\'entretien ?', 'Cela dépend du type d\'entretien. Contactez-nous pour un devis précis.'),
('Proposez-vous une garantie ?', 'Oui, tous nos véhicules d\'occasion ont une garantie minimale de 3 mois.'),
('Les voitures sont-elles garanties ?', 'Oui, elles sont garanties.'),
('Est-ce garanti si j\'achète une voiture ?', 'Oui, vous bénéficiez d\'une garantie de 3 mois minimum.'),
('Comment prendre rendez-vous ?', 'Vous pouvez prendre rendez-vous en nous contactant.'),
('Je veux réserver un créneau.', 'Prenez rendez-vous via notre page contact ou appelez-nous de préférence.'),
('Comment réserver un entretien ?', 'Via notre site ou en nous appelant directement de préférence.'),
('Faites-vous les cartes grises ?', 'Oui, nous nous occupons de toutes les démarches administratives.'),
('Vous chargez-vous de l\'immatriculation ?', 'Oui, nous faisons les cartes grises.'),
('Livrez-vous les voitures ?', 'Oui, nous livrons les véhicules à domicile.'),
('La livraison est-elle possible ?', 'Oui, nous proposons la livraison à domicile.'),
('Puis-je réserver un véhicule ?', 'Oui, vous pouvez bien sûr réserver un véhicule.'),
('Est-ce que je peux bloquer une voiture ?', 'Oui, appelez-nous !'),
('Quels sont vos délais de livraison ?', 'En moyenne, la livraison prend 5 à 10 jours ouvrés.'),
('Combien de temps pour livrer une voiture ?', 'Comptez environ une semaine pour la livraison.'),
('Faites-vous des remises ?', 'Nous proposons régulièrement des offres spéciales.'),
('Y a-t-il des promotions ?', 'Oui, consultez nos offres en cours sur le site.'),
('Avez-vous des tarifs pour étudiants ?', 'À voir directement avec les responsables de l\'entreprise.'),
('Offrez-vous des réductions pour entreprises ?', 'Oui, contactez-nous pour un devis entreprise.'),
('Que dois-je apporter pour acheter une voiture ?', 'Une pièce d\'identité, un justificatif de domicile et un moyen de paiement.'),
('Quels papiers faut-il pour acheter ?', 'Une carte d’identité, un justificatif de domicile et un moyen de paiement suffisent.'),
('Quels documents sont nécessaires ?', 'Justificatif de domicile, pièce d’identité et moyen de paiement.'),
('Puis-je vous appeler ?', 'Oui, vous pouvez nous joindre au numéro indiqué sur notre site.'),
('Comment vous contacter ?', 'Via le formulaire de contact ou par téléphone.'),
('Quel est votre numéro de téléphone ?', 'Vous pouvez nous appeler au 06 02 52 20 43.'),
('Quels services proposez-vous ?', 'Nous proposons des services de reprise, vente, entretien et immatriculation.'),
('Faites-vous des nettoyages de véhicules ?', 'Oui, nous proposons des services de nettoyage intérieur et extérieur.'),
('Quels sont vos tarifs pour le nettoyage ?', 'Les tarifs varient selon le type de nettoyage, contactez-nous.'),
('Puis-je obtenir un devis pour un nettoyage ?', 'Oui, contactez-nous pour un devis personnalisé.'),
('Quels sont vos services d\'entretien ?', 'Nous proposons des révisions, diagnostics électroniques et réparations mécaniques.'),
('Avez-vous des véhicules électriques ?', 'Oui, nous avons des véhicules électriques et hybrides disponibles.'),
('Puis-je échanger ma voiture contre une autre ?', 'Oui, nous proposons des services d\'échange de véhicules.'),
('Quels sont vos moyens de paiement ?', 'Nous acceptons les paiements par carte bancaire, virement et espèces.'),
('Puis-je payer en ligne ?', 'Non, pas de paiement en ligne.'),
('Quels sont vos délais pour les réparations ?', 'Les délais dépendent du type de réparation. Contactez-nous pour plus d\'informations.'),
('Faites-vous des diagnostics électroniques ?', 'Oui, nous proposons des diagnostics électroniques pour tous types de véhicules.'),
('Quels sont vos services pour les entreprises ?', 'Nous proposons beaucoup de services, regardez notre page dédiée.'),
('Avez-vous des véhicules utilitaires ?', 'Oui, nous avons une sélection de véhicules utilitaires disponibles.'),
('Quels sont vos horaires pendant les jours fériés ?', 'Nos horaires peuvent varier pendant les jours fériés. Veuillez nous contacter pour confirmation.'),
('Proposez-vous des véhicules hybrides ?', 'Oui, nous avons une sélection de véhicules hybrides disponibles.'),
('Quels sont les avantages d\'acheter un véhicule électrique ?', 'Les véhicules électriques sont économiques, écologiques et bénéficient d\'avantages fiscaux.'),
('Puis-je réserver un essai pour un véhicule électrique ?', 'Oui, vous pouvez réserver un essai pour un véhicule électrique.'),
('Quels documents sont nécessaires pour une reprise ?', 'Carte grise, justificatif de domicile et pièce d\'identité.'),
('Combien de temps prend une reprise ?', 'Une reprise peut être finalisée en 24 à 48 heures après validation.'),
('Quels sont vos tarifs pour les véhicules utilitaires ?', 'Les tarifs varient selon le modèle et l\'année. Contactez-nous pour un devis.'),
('Puis-je louer un véhicule chez vous ?', 'Non, nous ne proposons pas de service de location pour le moment.'),
('Quels sont vos délais pour les réparations mécaniques ?', 'En général entre 1 et 5 jours ouvrés, selon la complexité.'),
('Quels sont vos services de nettoyage ?', 'Nettoyages intérieurs, extérieurs et traitements spécifiques pour les sièges et tapis.'),
('Quels sont vos tarifs pour le nettoyage intérieur ?', 'Le nettoyage intérieur commence à partir de 50 €, selon le type de véhicule.'),
('Puis-je payer en plusieurs fois pour un entretien ?', 'Non, désolé.'),
('Quels sont vos délais pour obtenir une carte grise ?', 'Généralement de 3 à 5 jours ouvrés.'),
('Proposez-vous des garanties prolongées ?', 'Contactez-nous pour plus d\'informations à ce sujet.'),
('Quels sont vos services pour les pneus ?', 'Changement, équilibrage et stockage des pneus.'),
('Puis-je commander des pièces détachées chez vous ?', 'Non, désolé, nous ne vendons pas de pièces détachées.'),

-- QUESTIONS SUPPLÉMENTAIRES
('Proposez-vous des assurances auto ?', 'Non, nous ne proposons pas d\'assurance pour le moment.'),
('Puis-je réserver une visite en ligne ?', 'Oui, via notre formulaire de contact.'),
('Puis-je venir sans rendez-vous ?', 'Oui, mais nous recommandons de prendre rendez-vous pour un meilleur service.'),
('Est-ce que je peux faire reprendre un véhicule non roulant ?', 'Oui, sous certaines conditions. Contactez-nous pour évaluation.'),
('Les véhicules sont-ils révisés avant la vente ?', 'Oui, tous les véhicules sont inspectés et révisés.'),
('Puis-je voir l’historique d’entretien d’un véhicule ?', 'Oui, sur demande nous fournissons l\'historique disponible.'),
('Vos prix sont-ils négociables ?', 'Cela dépend du véhicule. Vous pouvez en discuter avec notre équipe.'),
('Les véhicules sont-ils accidentés ?', 'Non, nous ne vendons pas de véhicules accidentés.'),
('Acceptez-vous les échanges entre particuliers ?', 'Non, uniquement les échanges via notre service.'),
('Les véhicules sont-ils visibles en ligne ?', 'Oui, consultez notre catalogue en ligne.'),
('Avez-vous un parking sur place ?', 'Oui, un parking client est disponible.'),
('Vos véhicules sont-ils garantis partout en France ?', 'Oui, la garantie est nationale.'),
('Faites-vous des livraisons en dehors de la région ?', 'Oui, nous livrons '),
('Faites-vous le changement de propriétaire ?', 'Oui, nous nous occupons de tout.'),
('Avez-vous des véhicules automatiques ?', 'Oui, nous avons des modèles avec boîte automatique.'),
('Puis-je commander un modèle spécifique ?', 'Oui, nous pouvons chercher un modèle selon vos critères.'),
('Est-ce que vous installez des équipements (attelage, coffre de toit, etc.) ?', 'Oui, sur demande et devis.'),
('Puis-je venir tester plusieurs modèles ?', 'Oui, sur rendez-vous.'),
('Quels types de carburants proposez-vous ?', 'Essence, diesel, hybride et électrique.'),
('Avez-vous des véhicules neufs ?', 'Non, uniquement des véhicules d\'occasion.'),
('Puis-je connaître les émissions de CO2 d’un véhicule ?', 'Oui, cette information est disponible sur chaque fiche véhicule.'),
('Comment être informé de vos nouvelles offres ?', 'Abonnez-vous à notre newsletter ou suivez-nous sur les réseaux sociaux.'),
('Est-ce que je peux récupérer une voiture le jour même ?', 'Oui, selon disponibilité et documents fournis.'),
('Puis-je financer l’achat avec un crédit externe ?', 'Oui, vous pouvez utiliser votre propre organisme de crédit.'),
('Les véhicules sont-ils nettoyés avant livraison ?', 'Oui, un nettoyage complet est réalisé.'),
('Les prix sont-ils négociables ?', 'Les prix peuvent être discutés dans la limite du raisonnable, selon le véhicule.'),
('Faites-vous les démarches pour le changement de propriétaire ?', 'Oui, nous prenons en charge toutes les démarches administratives.'),
('Puis-je réserver un véhicule en ligne ?', 'Actuellement, la réservation se fait uniquement par téléphone ou en nous contactant.'),
('Combien coûte une carte grise ?', 'Le prix dépend de votre région et du véhicule. Contactez-nous pour une estimation.'),
('Quels documents dois-je fournir pour faire une carte grise ?', 'Carte d’identité, justificatif de domicile, certificat de cession, et ancienne carte grise.'),
('Est-ce que je peux venir sans rendez-vous ?', 'Oui, mais il est préférable de prendre rendez-vous pour un meilleur accueil.'),
('Puis-je faire reprendre ma voiture même si elle ne roule plus ?', 'Oui, nous étudions toutes les propositions, même pour les véhicules non roulants.'),
('Proposez-vous des véhicules sans permis ?', 'Non, nous ne vendons pas de véhicules sans permis.'),
('Est-ce que vos véhicules sont révisés avant la vente ?', 'Oui, tous nos véhicules sont révisés et contrôlés avant la mise en vente.'),
('Avez-vous des véhicules avec peu de kilomètres ?', 'Oui, nous proposons aussi des véhicules à faible kilométrage.'),
('Quels sont les modes de paiement acceptés ?', 'Nous acceptons carte bancaire, virement bancaire et espèces.'),
('Puis-je venir avec un expert automobile ?', 'Bien sûr, vous pouvez venir accompagné d’un expert si vous le souhaitez.'),
('Avez-vous un parking client ?', 'Oui, un parking gratuit est à disposition de nos clients.'),
('Les véhicules sont-ils assurés pour l’essai ?', 'Oui, tous nos véhicules sont assurés pour les essais sur route.'),
('Faites-vous le pré-contrôle technique ?', 'Oui, si nécessaire avant la vente, nous faisons un pré-contrôle technique.'),
('Peut-on venir voir les véhicules en semaine ?', 'Oui, nous sommes ouverts du lundi au samedi de 10h à 18h.'),
('Puis-je connaître l’historique d’un véhicule ?', 'Oui, nous fournissons l’historique du véhicule sur demande.'),
('Est-ce que les voitures sont propres à la livraison ?', 'Oui, chaque véhicule est soigneusement nettoyé avant la remise des clés.'),
('Vos véhicules ont-ils tous un carnet d’entretien ?', 'La majorité de nos véhicules disposent d’un carnet d’entretien à jour.'),
('Puis-je obtenir une facture détaillée ?', 'Oui, une facture vous est remise avec tous les détails de l’achat.'),
('Faites-vous des reprises même si j’ai un crédit en cours ?', 'Oui, contactez-nous pour évaluer les possibilités selon votre dossier.'),
('Puis-je réserver une visite ?', 'Oui, vous pouvez nous appeler ou utiliser notre page de contact.'),
('Quels véhicules sont disponibles actuellement ?', 'Veuillez consulter notre page de vente mise à jour régulièrement.'),
('Puis-je acheter un véhicule à distance ?', 'Oui, nous pouvons organiser la vente à distance et la livraison.'),
('Faites-vous des reprises en dehors de la région ?', 'Oui, nous pouvons étudier votre demande même hors région.'),
('Proposez-vous des véhicules récents ?', 'Oui, nous avons aussi des véhicules récents, parfois sous garantie constructeur.'),
('Peut-on avoir une attestation de reprise ?', 'Oui, une attestation vous est remise lors de la reprise.'),
('Proposez-vous un accompagnement pour l’assurance ?', 'Non, mais nous pouvons vous conseiller sur les démarches à suivre.'),
('Puis-je venir tester plusieurs véhicules ?', 'Oui, sur rendez-vous, vous pouvez tester plusieurs modèles.'),
('Les prix affichés sont-ils TTC ?', 'Oui, tous les prix affichés sont en TTC.'),
('Bonjour', 'Bonjours, comment je peux vous aider ? ');


