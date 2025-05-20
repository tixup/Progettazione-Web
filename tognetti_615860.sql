-- Progettazione Web 
DROP DATABASE if exists tognetti_615860; 
CREATE DATABASE tognetti_615860; 
USE tognetti_615860; 
-- MySQL dump 10.13  Distrib 5.7.28, for Win64 (x86_64)
--
-- Host: localhost    Database: tognetti_615860
-- ------------------------------------------------------
-- Server version	5.7.28

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `prenotazioni`
--

DROP TABLE IF EXISTS `prenotazioni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prenotazioni` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_utente` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `data_prenotazione` date NOT NULL,
  `orario` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_utente` (`id_utente`),
  KEY `id_sala` (`id_sala`),
  CONSTRAINT `prenotazioni_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`),
  CONSTRAINT `prenotazioni_ibfk_2` FOREIGN KEY (`id_sala`) REFERENCES `sale` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prenotazioni`
--

LOCK TABLES `prenotazioni` WRITE;
/*!40000 ALTER TABLE `prenotazioni` DISABLE KEYS */;
INSERT INTO `prenotazioni` VALUES (50,9,10,'2025-04-14','mattina'),(51,9,9,'2025-04-14','sera'),(52,9,11,'2025-04-14','pomeriggio'),(53,9,11,'2025-04-14','mattina'),(54,9,10,'2025-04-14','pomeriggio'),(55,9,9,'2025-04-14','mattina'),(56,11,10,'2025-04-14','mattina'),(57,9,9,'2025-04-16','mattina'),(58,9,10,'2025-04-16','mattina'),(59,9,10,'2025-04-19','mattina'),(63,13,11,'2025-04-30','sera'),(64,14,10,'2025-05-02','pomeriggio'),(67,15,10,'2025-05-03','pomeriggio'),(68,15,9,'2025-05-03','mattina'),(69,15,10,'2025-05-03','mattina'),(70,15,11,'2025-05-03','pomeriggio'),(71,16,9,'2025-05-20','pomeriggio'),(73,16,9,'2025-05-30','pomeriggio'),(74,16,10,'2025-05-30','pomeriggio'),(75,16,11,'2025-05-30','pomeriggio'),(76,16,9,'2025-05-30','mattina'),(77,16,10,'2025-05-30','mattina'),(78,16,11,'2025-05-30','mattina'),(79,9,10,'2025-05-22','mattina'),(80,9,11,'2025-05-22','mattina'),(81,9,10,'2025-06-10','mattina'),(82,9,9,'2025-06-10','mattina'),(83,17,9,'2025-06-09','pomeriggio'),(84,17,10,'2025-06-10','pomeriggio'),(85,17,11,'2025-06-10','pomeriggio'),(86,18,10,'2025-06-05','pomeriggio'),(87,18,11,'2025-06-05','pomeriggio'),(88,18,9,'2025-06-09','pomeriggio'),(89,18,10,'2025-06-09','pomeriggio');
/*!40000 ALTER TABLE `prenotazioni` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recensioni`
--

DROP TABLE IF EXISTS `recensioni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recensioni` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_utente` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `voto` int(11) DEFAULT NULL,
  `data_recensione` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_utente` (`id_utente`),
  KEY `id_sala` (`id_sala`),
  CONSTRAINT `recensioni_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`),
  CONSTRAINT `recensioni_ibfk_2` FOREIGN KEY (`id_sala`) REFERENCES `sale` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recensioni`
--

LOCK TABLES `recensioni` WRITE;
/*!40000 ALTER TABLE `recensioni` DISABLE KEYS */;
INSERT INTO `recensioni` VALUES (1,13,9,4,'2025-04-22 10:23:42'),(2,13,10,5,'2025-04-22 10:24:26'),(3,13,11,2,'2025-04-22 10:24:29'),(4,9,9,4,'2025-04-22 10:25:11'),(5,9,10,5,'2025-04-22 10:25:18'),(6,14,10,2,'2025-05-02 09:06:43'),(7,15,9,3,'2025-05-02 16:59:23'),(8,15,10,5,'2025-05-02 16:59:29'),(9,15,11,4,'2025-05-02 16:59:34'),(10,16,9,5,'2025-05-20 09:08:58'),(11,17,10,4,'2025-05-20 11:34:43'),(12,17,9,2,'2025-05-20 11:34:46'),(13,17,11,5,'2025-05-20 11:34:52'),(14,18,9,2,'2025-05-20 11:36:12'),(15,18,11,5,'2025-05-20 11:36:18');
/*!40000 ALTER TABLE `recensioni` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale`
--

DROP TABLE IF EXISTS `sale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `posti_totali` int(11) NOT NULL,
  `attrezzature` text,
  `immagine` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale`
--

LOCK TABLES `sale` WRITE;
/*!40000 ALTER TABLE `sale` DISABLE KEYS */;
INSERT INTO `sale` VALUES (9,'Sala A',15,'PC, Proiettore','sala_a.jpeg'),(10,'Sala B',20,'Prese, Wi-Fi','sala_b.jpeg'),(11,'Sala C',10,'Prese, Proiettore','sala_c.jpeg');
/*!40000 ALTER TABLE `sale` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utenti`
--

DROP TABLE IF EXISTS `utenti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utenti` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `data_registrazione` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utenti`
--

LOCK TABLES `utenti` WRITE;
/*!40000 ALTER TABLE `utenti` DISABLE KEYS */;
INSERT INTO `utenti` VALUES (9,'marcello','marcello@mail.com','$2y$10$a0/P5q7q1hho2II/KqLhOeh96BFycSJ5MvLRQW6UMoo6OuMIdZHFe','2025-04-12 19:06:00'),(10,'prova','prahsds@sds.com','$2y$10$6a574QiyN68q9sEPqjfxQuq..JV3IXvHyVbaEZbYWrlfwF99eowxi','2025-04-12 19:19:24'),(11,'cristian','agnetti@mail.com','$2y$10$b7SiPJzRZjcDNe.CpUhzo.A9L8QACXIQcVSEKY2Xv/roFEUsYUA92','2025-04-14 18:12:49'),(12,'Franco','franco@mail.com','$2y$10$dMrbFb3EJARWVj1BBilY/.FTn0c3kvJtAXTZ8b6Rl9uZK9E.YWuOe','2025-04-19 11:17:57'),(13,'alessia','alessia@lavoro.it','$2y$10$rrWBOB3JcRUnvKAy48Q/g.diCY/nkwSqteYG/KWu6bdtZ3o.zuIQ.','2025-04-22 09:48:49'),(14,'aatta','00asiatta@mail.com','$2y$10$Gu1yTEkkL2jAO8pbMhw0BuHxXxp60zHbt.nw4hvkS1RwP9TyTrkZO','2025-05-02 09:03:13'),(15,'matteo7','matteol@mail.com','$2y$10$g1lUNVXBmKUzojCDDdOGF.tdM5Qj2.K4YcJYuuBDF6Q.tZ5lKyMWa','2025-05-02 16:52:12'),(16,'Paolastracci','pippofranco@studio.com','$2y$10$kzf6IvcfmGgg97hgRHoIeuRd/aLLQ15byqtXKTFhg5qBCA/p/jnb2','2025-05-20 09:07:02'),(17,'Carlo','carlo@mail.it','$2y$10$wIj65xsUgfMBkO/yU2VbjeNJn4Z3c0CS5DelR6pzdaT.XH3VxHLH6','2025-05-20 11:34:04'),(18,'Irene','iresav@libero.com','$2y$10$mvPyeGfZ5zDIMeHI0dGRNeijhlArSmncpi0v3pC2c4djtP9MuHZGS','2025-05-20 11:35:35');
/*!40000 ALTER TABLE `utenti` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-20 11:41:15
