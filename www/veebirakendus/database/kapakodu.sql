-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: kapakodu
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `liigid`
--

DROP TABLE IF EXISTS `liigid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `liigid` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nimetus` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liigid`
--

LOCK TABLES `liigid` WRITE;
/*!40000 ALTER TABLE `liigid` DISABLE KEYS */;
INSERT INTO `liigid` VALUES (1,'Koer'),(2,'Kass'),(3,'Lind'),(4,'Näriline'),(5,'Küülik');
/*!40000 ALTER TABLE `liigid` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loomad`
--

DROP TABLE IF EXISTS `loomad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loomad` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nimi` varchar(100) NOT NULL,
  `sugu` enum('isane','emane') NOT NULL,
  `vanus` int unsigned NOT NULL,
  `sunnikuupaev` date DEFAULT NULL,
  `suurus` enum('väike','keskmine','suur') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `varvus` varchar(100) NOT NULL,
  `asukoht` varchar(100) NOT NULL,
  `luhikirjeldus` varchar(255) NOT NULL,
  `kirjeldus` text NOT NULL,
  `pilt` varchar(255) NOT NULL,
  `staatus` enum('saadaval','broneeritud','hoolduses') NOT NULL DEFAULT 'saadaval',
  `steriliseeritud` tinyint(1) NOT NULL DEFAULT '0',
  `kiibistatud` tinyint(1) NOT NULL DEFAULT '0',
  `lisatud` date NOT NULL DEFAULT (curdate()),
  `liik_id` int unsigned NOT NULL,
  `toug_id` int unsigned NOT NULL,
  `tootaja_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_loomad_liik` (`liik_id`),
  KEY `fk_loomad_toug` (`toug_id`),
  KEY `fk_loomad_tootaja` (`tootaja_id`),
  CONSTRAINT `fk_loomad_liik` FOREIGN KEY (`liik_id`) REFERENCES `liigid` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_loomad_tootaja` FOREIGN KEY (`tootaja_id`) REFERENCES `tootajad` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_loomad_toug` FOREIGN KEY (`toug_id`) REFERENCES `toud` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loomad`
--

LOCK TABLES `loomad` WRITE;
/*!40000 ALTER TABLE `loomad` DISABLE KEYS */;
INSERT INTO `loomad` VALUES (1,'Bella','emane',5,'2021-03-01','keskmine','Must','Tallinn','Sõbralik koer','Sõbralik ja rahulik koer.','rex.png','broneeritud',1,1,'2026-11-07',1,3,1),(2,'Nurr','isane',1,'2025-02-01','väike','Hall','Tartu','Noor kass','Noor ja mänguhimuline kass.','mimi.png','saadaval',0,1,'2026-11-07',2,6,2),(3,'Rex','isane',2,'2023-05-12','keskmine','Kuldne','Tallinn','Sõbralik koer','Energiat täis ja armastab jalutuskäike.','musti.png','saadaval',1,1,'2026-07-01',1,1,3),(4,'Mimi','emane',6,'2020-08-14','väike','Hall-valge','Tartu','Rahulik kass','Väga sõbralik ning naudib inimese seltskonda.','triibik.png','saadaval',1,1,'2026-07-02',2,5,4),(5,'Bruno','isane',5,'2021-01-22','suur','Pruun','Pärnu','Valvekoer','Rahulik ja ustav perekoer.','rex.png','broneeritud',1,1,'2026-07-03',1,2,1),(6,'Bella','emane',1,'2025-02-18','keskmine','Must','Haapsalu','Mänguhimuline','Noor ja aktiivne koer.','mimi.png','saadaval',0,1,'2026-07-04',1,3,2),(7,'Miisu','emane',4,'2022-06-30','väike','Triibuline','Viljandi','Uudishimulik kass','Armastab ronida ja mängida.','musti.png','broneeritud',1,1,'2026-07-05',2,4,3),(8,'Nurr','isane',3,'2023-03-10','väike','Hall','Rakvere','Vaikne kass','Sobib rahulikku koju.','triibik.png','hoolduses',1,0,'2026-07-06',2,6,4),(9,'Musti','isane',3,'2022-04-01','keskmine','Pruun-must','Tallinn','Rõõmsameelne','Naudib pikki jalutuskäike.','rex.png','saadaval',1,1,'2026-07-07',1,1,1),(10,'Lotte','emane',7,'2019-09-19','keskmine','Beež','Narva','Rahulik','Väga sõbralik vanem koer.','mimi.png','saadaval',1,1,'2026-07-08',1,1,2),(11,'Pätu','isane',2,'2024-02-11','väike','Must','Jõhvi','Mänguline','Armastab pallidega mängida.','musti.png','saadaval',0,1,'2026-07-09',2,4,3),(12,'Kribu','emane',1,'2025-01-15','väike','Valge','Tallinn','Armas küülik','Harjunud inimestega.','triibik.png','saadaval',0,0,'2026-07-10',5,11,4),(13,'Roki','isane',8,'2018-07-07','suur','Must','Tartu','Kogenud sõber','Rahulik ja kuulekas.','rex.png','hoolduses',1,1,'2026-07-11',1,1,1),(14,'Lumi','emane',5,'2021-12-24','väike','Valge','Pärnu','Hellik','Naudib pai tegemist.','mimi.png','saadaval',1,1,'2026-07-12',2,4,2),(15,'Marta','emane',4,'2022-10-09','keskmine','Pruun','Rapla','Sõbralik','Sobib lastega perre.','musti.png','broneeritud',1,1,'2026-07-13',1,1,3),(16,'Simba','isane',2,'2024-01-08','väike','Oranž','Valga','Julge','Väga uudishimulik kass.','triibik.png','saadaval',1,0,'2026-07-14',2,4,4),(17,'Bobby','isane',6,'2020-05-03','suur','Must-valge','Võru','Truu kaaslane','Armastab õues joosta.','rex.png','hoolduses',1,1,'2026-07-15',1,1,1),(18,'Kiisu','emane',1,'2025-03-14','väike','Kilpkonn','Paide','Noor kass','Õpib alles maailma tundma.','mimi.png','saadaval',0,1,'2026-07-16',2,4,2),(19,'Rufi','isane',2,'2023-08-18','keskmine','Hall','Kuressaare','Aktiivne','Sobib sportlikule perele.','musti.png','saadaval',0,1,'2026-07-17',1,1,3),(20,'Muri','emane',9,'2017-11-11','suur','Must','Tallinn','Rahulik','Vajab rahulikku kodu.','triibik.png','hoolduses',1,1,'2026-07-18',1,1,4),(21,'Triibu','isane',7,'2019-04-20','väike','Triibuline','Põlva','Tasane','Meeldib aknal istuda.','rex.png','broneeritud',1,1,'2026-07-19',2,4,1);
/*!40000 ALTER TABLE `loomad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nimetus` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ikoon` varchar(100) DEFAULT NULL,
  `jarjekord` int NOT NULL DEFAULT '0',
  `aktiivne` tinyint(1) NOT NULL DEFAULT '1',
  `loodud` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,'Avaleht','index.php','house',1,1,'2026-11-07 15:51:17'),(2,'Kõik loomad','all_animals.php','heart',2,1,'2026-11-07 15:51:17'),(3,'Kontakt','contact.php','envelope',3,1,'2026-11-07 15:51:17');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paringud`
--

DROP TABLE IF EXISTS `paringud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paringud` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `loom_id` int unsigned DEFAULT NULL,
  `nimi` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `teema` varchar(255) NOT NULL,
  `sonum` text NOT NULL,
  `staatus` enum('uus','menetluses','lopetatud') NOT NULL DEFAULT 'uus',
  `lisatud` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_paringud_loom` (`loom_id`),
  CONSTRAINT `fk_paringud_loom` FOREIGN KEY (`loom_id`) REFERENCES `loomad` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paringud`
--

LOCK TABLES `paringud` WRITE;
/*!40000 ALTER TABLE `paringud` DISABLE KEYS */;
INSERT INTO `paringud` VALUES (1,1,'Karl Kask','karl@example.com','Huvi looma vastu','Soovin tulla loomaga tutvuma.','uus','2026-11-07 17:19:42'),(2,3,'Mari Mets','mari@example.com','Broneerimine','Kas loom on veel saadaval?','menetluses','2026-11-07 17:19:42'),(3,5,'Jaan Saar','jaan@example.com','Lisainfo','Kas loom sobib lastega perre?','uus','2026-11-07 17:19:42'),(4,7,'Liis Põld','liis@example.com','Külastus','Soovin külastusaja kokku leppida.','lopetatud','2026-11-07 17:19:42'),(5,9,'Anne Oja','anne@example.com','Huvi looma vastu','Kas loom on kiibistatud?','uus','2026-11-07 17:19:42'),(6,12,'Rasmus Kivi','rasmus@example.com','Broneerimine','Palun broneerida nädalavahetuseni.','menetluses','2026-11-07 17:19:42'),(7,15,'Kristi Hein','kristi@example.com','Lisainfo','Kas loom saab teiste loomadega läbi?','uus','2026-11-07 17:19:42'),(8,18,'Tarmo Lepp','tarmo@example.com','Küsimus','Kas loom on vaktsineeritud?','lopetatud','2026-11-07 17:19:42'),(9,20,'Eve Nurm','eve@example.com','Huvi looma vastu','Soovin adopteerimise kohta infot.','uus','2026-11-07 17:19:42');
/*!40000 ALTER TABLE `paringud` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tootajad`
--

DROP TABLE IF EXISTS `tootajad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tootajad` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nimi` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `parool` varchar(255) NOT NULL,
  `roll` enum('admin','tootaja') NOT NULL DEFAULT 'tootaja',
  `lisatud` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tootajad`
--

LOCK TABLES `tootajad` WRITE;
/*!40000 ALTER TABLE `tootajad` DISABLE KEYS */;
INSERT INTO `tootajad` VALUES (1,'Mari Maasikas','mari@kapakodu.ee','55510001','$2y$10$1nmQsdQ52HQCfArFRsabfeij6a2rxbOCzJuQn84WRwqWDrImLbkKS','admin','2026-11-07 17:19:42'),(2,'Jaan Tamm','jaan@kapakodu.ee','55510002','$2y$10$1nmQsdQ52HQCfArFRsabfeij6a2rxbOCzJuQn84WRwqWDrImLbkKS','tootaja','2026-11-07 17:19:42'),(3,'Kati Kask','kati@kapakodu.ee','55510003','$2y$10$1nmQsdQ52HQCfArFRsabfeij6a2rxbOCzJuQn84WRwqWDrImLbkKS','tootaja','2026-11-07 17:19:42'),(4,'Peeter Pihl','peeter@kapakodu.ee','55510004','$2y$10$1nmQsdQ52HQCfArFRsabfeij6a2rxbOCzJuQn84WRwqWDrImLbkKS','tootaja','2026-11-07 17:19:42');
/*!40000 ALTER TABLE `tootajad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `toud`
--

DROP TABLE IF EXISTS `toud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `toud` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `liik_id` int unsigned NOT NULL,
  `nimetus` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_toud_liik` (`liik_id`),
  CONSTRAINT `fk_toud_liik` FOREIGN KEY (`liik_id`) REFERENCES `liigid` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `toud`
--

LOCK TABLES `toud` WRITE;
/*!40000 ALTER TABLE `toud` DISABLE KEYS */;
INSERT INTO `toud` VALUES (1,1,'Labrador'),(2,1,'Saksa lambakoer'),(3,1,'Kuldne retriiver'),(4,2,'Maine Coon'),(5,2,'Briti lühikarvaline'),(6,2,'Siiami'),(7,3,'Viirpapagoi'),(8,3,'Kanaarilind'),(9,4,'Merisiga'),(10,4,'Hamster'),(11,5,'Tundmatu');
/*!40000 ALTER TABLE `toud` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-07 18:22:22
