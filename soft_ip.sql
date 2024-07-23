-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 23 juil. 2024 à 21:48
-- Version du serveur : 10.4.27-MariaDB
-- Version de PHP : 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `soft_ip`
--

-- --------------------------------------------------------

--
-- Structure de la table `abonnements`
--

CREATE TABLE `abonnements` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `formation_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('Actif','Expiré') NOT NULL,
  `payment_method` enum('KKiaPay','FedaPay') NOT NULL,
  `transaction_details` text NOT NULL,
  `auto_renewal` tinyint(1) NOT NULL DEFAULT 0,
  `notifications_sent` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `abonnements`
--

INSERT INTO `abonnements` (`id`, `student_id`, `formation_id`, `start_date`, `end_date`, `price`, `status`, `payment_method`, `transaction_details`, `auto_renewal`, `notifications_sent`) VALUES
(2, 21, 2, '2024-07-23', '2024-08-22', '5.00', 'Actif', 'KKiaPay', 'Paiement réussi via KKiaPay', 0, 0),
(3, 21, 3, '2024-07-23', '2024-08-22', '3.00', 'Actif', 'KKiaPay', 'Paiement réussi via KKiaPay', 0, 0),
(4, 21, 3, '2024-07-23', '2024-08-22', '3.00', 'Actif', 'KKiaPay', 'Paiement réussi via KKiaPay', 0, 0),
(5, 21, 4, '2024-07-23', '2024-08-22', '2.00', 'Actif', 'KKiaPay', 'Paiement réussi via KKiaPay', 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `administrators`
--

CREATE TABLE `administrators` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `administrators`
--

INSERT INTO `administrators` (`id`, `username`, `password`) VALUES
(4, 'roys', '$2y$10$Dr9Bi9mlErnqOHJIAKxXzeGm/K5Bs5m..cI.p6uC3g/qyW1okw1De');

-- --------------------------------------------------------

--
-- Structure de la table `annonces`
--

CREATE TABLE `annonces` (
  `id` int(11) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `annonces`
--

INSERT INTO `annonces` (`id`, `description`) VALUES
(1, 'Le 3 Août 2024 nous débutons la 1ere session des évaluation et le 21 septembre, il y a la fête de Septembre ah oui');

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

CREATE TABLE `cours` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `duree` int(11) DEFAULT NULL,
  `niveau` enum('Débutant','Intermédiaire','Avancé') NOT NULL,
  `formation_id` int(11) NOT NULL,
  `formateur_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `cours`
--

INSERT INTO `cours` (`id`, `nom`, `image`, `description`, `duree`, `niveau`, `formation_id`, `formateur_id`) VALUES
(2, 'Excel', 'CPA-9266.jpg', 'faire es calcul est excellent moyen de ....', 2, 'Intermédiaire', 4, 9);

-- --------------------------------------------------------

--
-- Structure de la table `course_steps`
--

CREATE TABLE `course_steps` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `step_number` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `course_steps`
--

INSERT INTO `course_steps` (`id`, `course_id`, `step_number`, `content`) VALUES
(78, 2, 1, 'gratuitementfffffffffffffffffffffffffffffff et le papa'),
(79, 2, 2, 'je suis avec toi'),
(80, 2, 3, 'fonce'),
(81, 2, 4, 'vas'),
(82, 2, 5, 'aussi bonne mlecteur'),
(83, 2, 6, 'aller');

-- --------------------------------------------------------

--
-- Structure de la table `discussions`
--

CREATE TABLE `discussions` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `trainer_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `discussions`
--

INSERT INTO `discussions` (`id`, `topic_id`, `student_id`, `message`, `created_at`, `trainer_id`, `admin_id`) VALUES
(1, 1, 6, '<p>les enfant ont droit a tout le meilleur de la vie</p>', '2024-07-13 13:28:38', NULL, NULL),
(9, 1, 13, '<p>je le pense aussi</p>', '2024-07-13 16:17:35', NULL, NULL),
(19, 2, 21, 'bonjour ce suijet m\'teresse bien\r\n', '2024-07-17 16:28:55', NULL, NULL),
(20, 2, 21, 'bonjour vannessa tes en quel section??', '2024-07-17 16:30:02', NULL, NULL),
(21, 2, 21, 'oui', '2024-07-17 16:30:25', NULL, NULL),
(22, 2, 20, 'je voir moi je fais la B', '2024-07-17 16:32:22', NULL, NULL),
(23, 2, 20, 'je trouve que les cours sont très bien assez dispensées', '2024-07-17 16:32:57', NULL, NULL),
(24, 2, 20, 'c\'est une tres belle initiative\r\n', '2024-07-17 16:37:07', NULL, NULL),
(25, 2, 20, 'oui', '2024-07-17 16:37:44', NULL, NULL),
(26, 2, NULL, 'tu peux le fair\r\n', '2024-07-17 16:39:41', 9, NULL),
(27, 1, NULL, 'la vitesse', '2024-07-17 16:54:16', 9, NULL),
(28, 2, NULL, '<p>les minuteurs sont là 😊</p>', '2024-07-20 19:17:34', 9, NULL),
(29, 2, NULL, '<p>😂😂😂😂😂😂😂</p>', '2024-07-20 19:28:06', 9, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `evaluations`
--

CREATE TABLE `evaluations` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `evaluation` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `rating` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `session_id`, `student_id`, `feedback`, `created_at`, `rating`) VALUES
(1, 1, 6, 'tres bon eleve, avec un avenir tres promettant en iformatique tres brillant', '2024-07-04 19:40:44', 0);

-- --------------------------------------------------------

--
-- Structure de la table `formations`
--

CREATE TABLE `formations` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `formations`
--

INSERT INTO `formations` (`id`, `titre`, `description`, `image_url`, `created_at`, `price`) VALUES
(1, 'cyber sécurité', 'la sécurité avant tout est tout est toutest toutest toutest toutest toutest toutest toutest toutest toutest toutest toutest toutest toutest toutest toutest toutest toutest toutest tout un', 'https://i.postimg.cc/VkwgqjMg/OIP-3.jpg', '2024-07-02 12:32:50', '10.00'),
(2, 'Développement Web', '<p style=\"text-align: left;\">Cette formation complète en développement web vous guide à travers les bases jusqu\'aux techniques avancées, idéale pour les débutants et ceux cherchant à approfondir leurs compétences.</p>\r\n\r\n        <h4 class=\"section-title\" style=\"\"><font color=\"#083139\"><b>Objectifs de la Formation</b></font></h4>\r\n        <ul>\r\n            <li style=\"text-align: left;\">Créer des sites web réactifs et interactifs.</li>\r\n            <li style=\"text-align: left;\">Utiliser les principaux langages de programmation web : HTML, CSS, JavaScript.</li>\r\n            <li style=\"text-align: left;\">Maîtriser les frameworks populaires comme Bootstrap, React et Angular.</li>\r\n            <li style=\"text-align: left;\">Développer des applications web backend avec PHP, MySQL, Node.js et Express.</li>\r\n        </ul>\r\n\r\n        <h4 class=\"section-title\" style=\"text-align: left;\"><font color=\"#083139\"><b>Programme de la Formation</b></font></h4>\r\n        <h5 style=\"text-align: left;\"><i><u>Module 1</u> : Introduction au Développement Web</i></h5>\r\n        <ul>\r\n            <li style=\"text-align: left;\">HTML et CSS de base</li>\r\n            <li style=\"text-align: left;\">Principes de la conception web</li>\r\n        </ul>\r\n        <h5 style=\"text-align: left;\"><i><u>Module 2</u> : JavaScript et Programmation Orientée Objet</i></h5>\r\n        <ul>\r\n            <li style=\"text-align: left;\">JavaScript de base et avancé</li>\r\n            <li style=\"text-align: left;\">Manipulation du DOM</li>\r\n        </ul>\r\n        <h5 style=\"text-align: left;\"><i><u>Module 3</u> : Frameworks Frontend</i></h5>\r\n        <ul>\r\n            <li style=\"text-align: left;\">Introduction à React</li>\r\n            <li style=\"text-align: left;\">Utilisation d\'Angular</li><li style=\"text-align: left;\">Utilisation Bootstrap</li>\r\n        </ul>\r\n        <h5 style=\"text-align: left;\"><i><u>Module 4</u> : Développement Backend</i></h5>\r\n        <ul>\r\n            <li style=\"text-align: left;\">PHP</li><li style=\"text-align: left;\">Node.js et Express</li>\r\n            <li style=\"text-align: left;\">Gestion de bases de données avec MySQL</li>\r\n        </ul>\r\n        <h5 style=\"text-align: left;\"><i><u>Module 5</u> : Déploiement et Maintenance</i></h5>\r\n        <ul>\r\n            <li style=\"text-align: left;\">Déploiement d\'applications web</li>\r\n            <li style=\"text-align: left;\">Optimisation et maintenance</li>\r\n        </ul>\r\n\r\n        <h5 class=\"section-title\" style=\"text-align: left;\"><b><font color=\"#083139\"><span style=\"font-size: 24px;\">Prérequis</span></font></b></h5>\r\n        <p style=\"text-align: left;\">Aucune expérience préalable en développement web n\'est requise. Une familiarité avec l\'utilisation de l\'ordinateur et la navigation sur le web est souhaitable.</p>\r\n\r\n        <h4 class=\"section-title\" style=\"text-align: left;\"><b><font color=\"#083139\">Durée et Modalités</font></b></h4>\r\n        <p style=\"text-align: left;\"><i>Durée</i> : 12 semaines (3 mois +1)</p><p style=\"text-align: left;\"><span style=\"background-color: var(--bs-body-bg); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\"><u><i>Modalité</i></u> : </span></p><ol><li style=\"text-align: left;\"><span style=\"background-color: var(--bs-body-bg); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">Formation en ligne avec sessions en direct et accès à des ressources vidéos préenregistrées, des tutoriels et des PDFs.</span></li><li style=\"text-align: left;\"><span style=\"background-color: var(--bs-body-bg); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);\">Formation en présentiel avec session exercices et réalisation</span></li></ol>\r\n\r\n        <h4 class=\"section-title\" style=\"text-align: left;\"><font color=\"#083139\"><b>Évaluation et Certification</b></font></h4>\r\n        <p style=\"text-align: left;\"><i>Évaluation </i>: Projets pratiques, quizz en ligne, examen final.</p>\r\n        <p style=\"text-align: left;\"><i>Certification</i> : Certificat de Compétence en Développement Web décerné à l\'issue de la formation.</p>\r\n\r\n        <h4 class=\"section-title\" style=\"text-align: left;\"><b><font color=\"#083139\">Ressources et Matériel Fournis</font></b></h4>\r\n        <ul>\r\n            <li style=\"text-align: left;\">Accès à une plateforme d\'apprentissage en ligne.</li>\r\n            <li style=\"text-align: left;\">Ressources téléchargeables : e-books, présentations, exercices pratiques, vidéos.</li>\r\n            <li style=\"text-align: left;\">Accès à un forum de discussion pour interaction avec les formateurs et autres étudiants.</li>\r\n        </ul>\r\n\r\n        <h4 class=\"section-title\" style=\"text-align: left;\"><b><font color=\"#083139\">Prix, Modalité et Inscription</font></b></h4>\r\n        <p style=\"text-align: left;\"><i>Prix </i>: 500 € pour l\'ensemble de la formation.</p><p style=\"text-align: left;\"><i>Modalité</i> : payable par tranche (100 000 / mois)</p>', 'https://i.postimg.cc/c4HN9hrq/developpement-web-featured.jpg', '2024-07-02 13:01:34', '5.00'),
(3, 'GSM Mobil', 'le Gssm est le fait de reparer les telephone prtable pour leur', 'https://i.postimg.cc/RF83Dh4K/reparation-smartphone-a-wazemmes-lille-centre.jpg', '2024-07-13 20:19:29', '3.00'),
(4, 'Bureautique', 'l\'informatique de bureau est une suite d\'enssemble de beaucoup de logicile ', 'https://i.postimg.cc/mkHcf4gq/informatique.jpg', '2024-07-13 20:21:18', '2.00'),
(5, 'Audio visuel', 'la formation en audio visuel et l\'un des plus gra', 'https://i.postimg.cc/qRghwyVp/tournage-audiovisuel-preparer-concours.jpg', '2024-07-13 20:22:31', '100.00'),
(6, 'Antenne Parabilique', 'l\'installation des antenne parabolique est un sheme', 'https://i.postimg.cc/76ZDjJ4h/parabole1.jpg', '2024-07-13 20:36:10', '0.00');

-- --------------------------------------------------------

--
-- Structure de la table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `date_taken` date DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `grade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `grades`
--

INSERT INTO `grades` (`id`, `quiz_id`, `student_id`, `score`, `date_taken`, `session_id`, `grade`) VALUES
(1, NULL, 6, NULL, NULL, NULL, 0),
(2, NULL, 6, NULL, NULL, NULL, 0),
(3, NULL, 6, NULL, NULL, NULL, 0),
(4, NULL, 6, NULL, NULL, NULL, 0),
(5, NULL, 6, NULL, NULL, NULL, 0),
(6, 14, 6, NULL, NULL, NULL, 0),
(7, 12, 11, NULL, NULL, NULL, 1),
(8, 14, 11, NULL, NULL, NULL, 0),
(9, 13, 11, NULL, NULL, NULL, 1),
(10, 12, 6, NULL, NULL, NULL, 1),
(11, 15, 21, NULL, NULL, NULL, 1),
(12, 15, 21, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `inscriptions`
--

CREATE TABLE `inscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `formation_id` int(11) NOT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `inscriptions`
--

INSERT INTO `inscriptions` (`id`, `user_id`, `formation_id`, `date_inscription`) VALUES
(9, 6, 1, '2024-07-05 23:46:45'),
(10, 6, 2, '2024-07-05 23:47:41'),
(11, 11, 1, '2024-07-10 15:56:17'),
(12, 11, 2, '2024-07-14 21:24:04'),
(13, 20, 3, '2024-07-17 16:22:51'),
(14, 21, 4, '2024-07-17 16:25:47'),
(15, 6, 6, '2024-07-18 14:40:50');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `student_id`, `message`, `is_read`, `created_at`, `updated_at`) VALUES
(2, 6, 'administrateur', 0, '2024-07-06 12:24:44', '2024-07-17 21:39:27');

-- --------------------------------------------------------

--
-- Structure de la table `notif_all`
--

CREATE TABLE `notif_all` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notif_all`
--

INSERT INTO `notif_all` (`id`, `student_id`, `message`, `is_read`, `created_at`) VALUES
(1, 6, 'du pain', 0, '2024-07-12 21:42:04'),
(2, 6, 'cureao', 0, '2024-07-12 21:42:34'),
(3, 6, 'oui\r\n', 0, '2024-07-13 00:29:10'),
(4, 21, 'demanin a 163 rer', 0, '2024-07-17 19:46:33');

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `created_at`) VALUES
(1, 'marry@gmail.com', '634281438e32e18f9c41e8701814b02c5e8d32ccadda50504bf380913d727c713c587a42701c9ea28a34f2b4bf441262f5b8', '2024-07-15 19:27:28'),
(2, 'etonakambio@gmail.com', 'a6381c8fd320db10288167a49efb82e5b4f386cccaf47b8518870ae1a9d539339b0a76b3f18698884ba387cf251bca4074e3', '2024-07-15 19:38:42');

-- --------------------------------------------------------

--
-- Structure de la table `progress`
--

CREATE TABLE `progress` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `current_step` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `correct_option` char(1) NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `trainer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `correct_option`, `option_a`, `option_b`, `option_c`, `option_d`, `created_at`, `trainer_id`) VALUES
(8, 12, 'Qui est tu vraiment, et quel est ta mission sur terre ?', 'A', 'je suis un enfant de Dieu et ma mission est de suivre Jesus et faire son oeuvre', 'marché dans les pas de jesus', 'Dieu aime les pecheurs', 'Dieu est Dieu', '2024-07-12 17:13:21', 1),
(9, 13, 'Qui est le Saint Esprit', 'C', 'Ange', 'Colombe', 'Jésus', 'Feu', '2024-07-12 17:14:55', 1),
(10, 14, 'Quel est le premier reseau ', 'B', 'MOOV', 'MTN', 'ORANGE', 'CELTIS', '2024-07-12 17:48:46', 1),
(11, 15, 'quel est le rôle du curseur main?', 'C', 'chargement', 'texte', 'validation', 'rien du tout', '2024-07-17 17:40:44', 9),
(12, 16, 'quest ce que la fich I permet de faire', 'C', 'couplé deux antennes', 'couplé trois anrennes', 'coupler le cable coaxial', 'couplé 2 decodeur', '2024-07-17 17:44:16', 9);

-- --------------------------------------------------------

--
-- Structure de la table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `formation_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `trainer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `quizzes`
--

INSERT INTO `quizzes` (`id`, `titre`, `description`, `formation_id`, `created_at`, `trainer_id`) VALUES
(12, 'Spiritualité', 'tout ce qui touche les profondeur humain', 2, '2024-07-12 17:10:05', 1),
(13, 'Rligieon', 'connaitre DIEU', 1, '2024-07-12 17:10:32', 1),
(14, 'MTN', 'reseau', 2, '2024-07-12 17:47:34', 1),
(15, 'Word', 'comment reconnaitre le curseur I', 4, '2024-07-17 17:38:32', 9),
(16, 'Antenne ', 'quiz sur le foncion', 6, '2024-07-17 17:42:23', 9);

-- --------------------------------------------------------

--
-- Structure de la table `quiz_answers`
--

CREATE TABLE `quiz_answers` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `score` int(11) DEFAULT 0,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `quiz_answers`
--

INSERT INTO `quiz_answers` (`id`, `student_id`, `quiz_id`, `question_id`, `answer`, `score`, `submitted_at`, `archived`) VALUES
(25, 6, 12, NULL, NULL, 1, '2024-07-12 17:15:37', 1),
(26, 6, 13, NULL, NULL, 1, '2024-07-12 17:15:58', 1),
(27, 6, 12, 8, 'D', 0, '2024-07-12 17:22:03', 0),
(28, 6, 12, 8, 'D', 0, '2024-07-12 17:44:06', 1),
(29, 6, 14, 10, 'B', 1, '2024-07-12 17:49:23', 1),
(30, 6, 14, 10, 'C', 0, '2024-07-12 18:12:08', 0),
(31, 11, 12, 8, 'A', 1, '2024-07-14 21:08:05', 1),
(32, 11, 14, 10, 'C', 0, '2024-07-14 21:08:20', 1),
(33, 11, 13, 9, 'C', 1, '2024-07-14 21:08:36', 1),
(34, 6, 12, 8, 'A', 1, '2024-07-15 16:09:56', 1),
(35, 21, 15, 11, 'C', 1, '2024-07-17 17:48:21', 0),
(36, 21, 15, NULL, NULL, 1, '2024-07-17 19:25:10', 0);

-- --------------------------------------------------------

--
-- Structure de la table `replies`
--

CREATE TABLE `replies` (
  `id` int(11) NOT NULL,
  `discussion_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `replies`
--

INSERT INTO `replies` (`id`, `discussion_id`, `student_id`, `message`, `created_at`) VALUES
(1, 1, 6, '<p>et le dis encore</p>', '2024-07-13 13:30:39'),
(2, 1, 11, '<p><i>je pense que les parents doit etre plus eveiller</i></p>', '2024-07-13 13:34:46'),
(3, 1, 13, '<p>vraimernt</p>', '2024-07-13 15:28:10'),
(4, 1, 6, '<p>la fouine</p>', '2024-07-13 15:48:45'),
(5, 1, 6, '<p>hermine</p>', '2024-07-13 15:51:35'),
(6, 9, 13, '<p>ah oui</p>', '2024-07-13 16:18:49'),
(7, 9, 6, '<p>et oui oui</p><p><br></p>', '2024-07-13 16:19:52'),
(8, 9, 6, '', '2024-07-13 16:21:54'),
(9, 9, 6, 'ouioui', '2024-07-13 16:35:13');

-- --------------------------------------------------------

--
-- Structure de la table `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL,
  `formation_id` int(11) DEFAULT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `lien` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `resources`
--

INSERT INTO `resources` (`id`, `formation_id`, `titre`, `description`, `lien`) VALUES
(8, 2, 'html et css', 'création de site internet, statique ou dynamique', 'https://fr.wikihow.com/parler-%C3%A0-une-fille-que-l%27on-ne-connait-pas-par-message-priv%C3%A9');

-- --------------------------------------------------------

--
-- Structure de la table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `date_taken` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reviews`
--

INSERT INTO `reviews` (`id`, `course_id`, `student_id`, `rating`, `comment`, `created_at`) VALUES
(7, 2, 21, 4, 'tres bien mr', '2024-07-20 12:01:01'),
(8, 2, 21, 5, 'banane plantin', '2024-07-20 12:10:28'),
(9, 2, 21, 3, 'je penses que vou devrirez revoir unpeu votre maniere  d\'expliquer', '2024-07-20 13:30:16');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `formation_id` int(11) DEFAULT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `formation_id`, `trainer_id`, `date`, `time`, `location`, `title`) VALUES
(6, 2, 1, '2024-07-20', '07:14:00', 'Adiaké', ''),
(7, 1, 1, '2024-07-19', '00:33:00', 'Vêdoko', '');

-- --------------------------------------------------------

--
-- Structure de la table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `avatar` mediumblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `students`
--

INSERT INTO `students` (`id`, `username`, `password`, `email`, `phone_number`, `avatar`) VALUES
(6, 'Nego', '$2y$10$ef2yGzmRyxAklsuBTniGi.Z1kWueDqFEWqpayS18Ygjga6vJdES3W', 'nego@gmail.com', '61418976', NULL),
(11, 'alex', '$2y$10$7OQlHP7MyTbZynTroEsBz.Mrfo5yU5zau2//w98axUu9oRJjos/3S', 'alex@gmail.com', '5578996541', NULL),
(13, 'fiacre', '$2y$10$MLTjvXCoVfQ0SJL.eOLfGOHDUssjCJFV4Cpadaf97DqompgoBdFxK', 'fiacre@gmail.com', '96575623', 0x2e2e2f6176617461725f696d672f53637265656e73686f745f32303234303731332d3131353532325f47616c6c6572792e6a7067),
(20, 'Juniore NATAN', '$2y$10$I4VcsQ67sithzSrVPYOlDehFVIE1lxq3Yycjd9mF8pD2Uh.ODx7mO', 'juniorz@gmail.com', '96545623', 0x2e2e2f6176617461725f696d672f522e6a7067),
(21, 'vanessa', '$2y$10$22Z/Ze8tQkQK4ZRqaL/DFuXATpHkFOP1qWEFZCrakehCaTsggBcb2', 'vanessa@gmail.com', '2121210000', 0x2e2e2f6176617461725f696d672f4350412d393336332e6a7067);

-- --------------------------------------------------------

--
-- Structure de la table `student_answers`
--

CREATE TABLE `student_answers` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` char(1) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `student_answers`
--

INSERT INTO `student_answers` (`id`, `student_id`, `quiz_id`, `question_id`, `answer`, `submitted_at`) VALUES
(42, 6, 12, 8, 'A', '2024-07-12 17:15:37'),
(43, 6, 13, 9, 'C', '2024-07-12 17:15:58'),
(49, 11, 12, 8, 'A', '2024-07-14 21:08:05'),
(51, 11, 13, 9, 'C', '2024-07-14 21:08:36'),
(53, 21, 15, 11, 'C', '2024-07-17 17:48:21');

-- --------------------------------------------------------

--
-- Structure de la table `student_resources`
--

CREATE TABLE `student_resources` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `date_acquired` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `topics`
--

CREATE TABLE `topics` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `topics`
--

INSERT INTO `topics` (`id`, `title`, `description`, `created_by`, `created_at`) VALUES
(1, 'les droit de l\'enfant', 'que pensez vous des droits des enfants', 6, '2024-07-13 13:25:33'),
(2, 'le cours sur le technique', 'le chapitre de la guerre des trans', 6, '2024-07-13 16:56:05'),
(3, 'CSI Mobile', 'Chantier de solidarité internatial', NULL, '2024-07-20 20:14:35');

-- --------------------------------------------------------

--
-- Structure de la table `trainers`
--

CREATE TABLE `trainers` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `trainers`
--

INSERT INTO `trainers` (`id`, `username`, `password`, `email`, `phone_number`, `bio`, `avatar`) VALUES
(1, 'igor', '$2y$10$KFfPKK9HNHGCsPyIU7p4K.kod/4Hdw6vDrcy/KUD15I55mCx9c2ey', NULL, NULL, NULL, NULL),
(9, 'Boris', '$2y$10$qAAOcpr9v.CjHqjLmtKIHu7LP9nLKPtzXh7qx6ECve2HIr8v7Km1G', 'boris@gmail.com', '6000000000', 'je suis un entrepreneur', '../avatar_img/O.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subscription_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `transaction_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `abonnements`
--
ALTER TABLE `abonnements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `formation_id` (`formation_id`);

--
-- Index pour la table `administrators`
--
ALTER TABLE `administrators`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `formation_id` (`formation_id`),
  ADD KEY `formateur_id` (`formateur_id`);

--
-- Index pour la table `course_steps`
--
ALTER TABLE `course_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Index pour la table `discussions`
--
ALTER TABLE `discussions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Index pour la table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Index pour la table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Index pour la table `formations`
--
ALTER TABLE `formations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `idx_grade` (`grade`);

--
-- Index pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `formation_id` (`formation_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Index pour la table `notif_all`
--
ALTER TABLE `notif_all`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Index pour la table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Index pour la table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `formation_id` (`formation_id`),
  ADD KEY `fk_trainer_id` (`trainer_id`);

--
-- Index pour la table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Index pour la table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discussion_id` (`discussion_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Index pour la table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `formation_id` (`formation_id`);

--
-- Index pour la table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Index pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `formation_id` (`formation_id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Index pour la table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_answer` (`student_id`,`quiz_id`,`question_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Index pour la table `student_resources`
--
ALTER TABLE `student_resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `resource_id` (`resource_id`);

--
-- Index pour la table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Index pour la table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `subscription_id` (`subscription_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `abonnements`
--
ALTER TABLE `abonnements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `administrators`
--
ALTER TABLE `administrators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `annonces`
--
ALTER TABLE `annonces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `course_steps`
--
ALTER TABLE `course_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT pour la table `discussions`
--
ALTER TABLE `discussions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `formations`
--
ALTER TABLE `formations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `notif_all`
--
ALTER TABLE `notif_all`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `progress`
--
ALTER TABLE `progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT pour la table `replies`
--
ALTER TABLE `replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT pour la table `student_resources`
--
ALTER TABLE `student_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `abonnements`
--
ALTER TABLE `abonnements`
  ADD CONSTRAINT `abonnements_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `abonnements_ibfk_2` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`);

--
-- Contraintes pour la table `cours`
--
ALTER TABLE `cours`
  ADD CONSTRAINT `cours_ibfk_1` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cours_ibfk_2` FOREIGN KEY (`formateur_id`) REFERENCES `trainers` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `course_steps`
--
ALTER TABLE `course_steps`
  ADD CONSTRAINT `course_steps_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `cours` (`id`);

--
-- Contraintes pour la table `discussions`
--
ALTER TABLE `discussions`
  ADD CONSTRAINT `discussions_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`),
  ADD CONSTRAINT `discussions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Contraintes pour la table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`),
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `grades_ibfk_3` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`),
  ADD CONSTRAINT `grades_ibfk_4` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `grades_ibfk_5` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`);

--
-- Contraintes pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD CONSTRAINT `inscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `inscriptions_ibfk_2` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`);

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Contraintes pour la table `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `progress_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `progress_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `cours` (`id`);

--
-- Contraintes pour la table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_quiz_id` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `fk_trainer_id` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`),
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD CONSTRAINT `quiz_answers_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `quiz_answers_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`),
  ADD CONSTRAINT `quiz_answers_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Contraintes pour la table `replies`
--
ALTER TABLE `replies`
  ADD CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`id`),
  ADD CONSTRAINT `replies_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Contraintes pour la table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `resources_ibfk_1` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`),
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Contraintes pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `cours` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Contraintes pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`),
  ADD CONSTRAINT `sessions_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`);

--
-- Contraintes pour la table `student_answers`
--
ALTER TABLE `student_answers`
  ADD CONSTRAINT `student_answers_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `student_answers_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`),
  ADD CONSTRAINT `student_answers_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Contraintes pour la table `student_resources`
--
ALTER TABLE `student_resources`
  ADD CONSTRAINT `student_resources_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_resources_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `topics`
--
ALTER TABLE `topics`
  ADD CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `students` (`id`);

--
-- Contraintes pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`subscription_id`) REFERENCES `abonnements` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
