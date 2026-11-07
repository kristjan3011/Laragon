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
-- Table structure for table `loomad`
--

DROP TABLE IF EXISTS `loomad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loomad` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nimi` varchar(100) NOT NULL,
  `liik` varchar(50) NOT NULL,
  `sugu` enum('isane','emane') NOT NULL,
  `vanus` int unsigned NOT NULL,
  `sunnikuupaev` date DEFAULT NULL,
  `suurus` enum('v├ñike','keskmine','suur') NOT NULL,
  `varvus` varchar(100) NOT NULL,
  `asukoht` varchar(100) NOT NULL,
  `luhikirjeldus` varchar(255) NOT NULL,
  `kirjeldus` text NOT NULL,
  `pilt` varchar(255) NOT NULL,
  `staatus` enum('saadaval','broneeritud','hoolduses') NOT NULL DEFAULT 'saadaval',
  `steriliseeritud` tinyint(1) NOT NULL DEFAULT '0',
  `kiibistatud` tinyint(1) NOT NULL DEFAULT '0',
  `lisatud` date NOT NULL DEFAULT (curdate()),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loomad`
--

LOCK TABLES `loomad` WRITE;
/*!40000 ALTER TABLE `loomad` DISABLE KEYS */;
INSERT INTO `loomad` VALUES (1,'Bella','Koer','emane',5,'2021-03-01','keskmine','Must','Tallinn','S├Ábralik koer','S├Ábralik ja rahulik koer.','bella.jpg','saadaval',1,1,'2026-11-07'),(2,'Nurr','Kass','isane',1,'2025-02-01','v├ñike','Hall','Tartu','Noor kass','Noor ja m├ñnguhimuline kass.','nurr.jpg','saadaval',0,1,'2026-11-07'),(3,'Rex','Koer','isane',2,'2023-05-12','keskmine','Kuldne','Tallinn','Sõbralik koer','Energiat täis ja armastab jalutuskäike.','rex.jpg','saadaval',1,1,'2026-07-01'),(4,'Mimi','Kass','emane',6,'2020-08-14','','Hall-valge','Tartu','Rahulik kass','Väga sõbralik ning naudib inimese seltskonda.','mimi.jpg','saadaval',1,1,'2026-07-02'),(5,'Bruno','Koer','isane',5,'2021-01-22','suur','Pruun','Pärnu','Valvekoer','Rahulik ja ustav perekoer.','bruno.jpg','broneeritud',1,1,'2026-07-03'),(6,'Bella','Koer','emane',1,'2025-02-18','keskmine','Must','Haapsalu','Mänguhimuline','Noor ja aktiivne koer.','bella.jpg','saadaval',0,1,'2026-07-04'),(7,'Miisu','Kass','emane',4,'2022-06-30','','Triibuline','Viljandi','Uudishimulik kass','Armastab ronida ja mängida.','miisu.jpg','broneeritud',1,1,'2026-07-05'),(8,'Nurr','Kass','isane',3,'2023-03-10','','Hall','Rakvere','Vaikne kass','Sobib rahulikku koju.','nurr.jpg','hoolduses',1,0,'2026-07-06'),(9,'Musti','Koer','isane',3,'2022-04-01','keskmine','Pruun-must','Tallinn','Rõõmsameelne','Naudib pikki jalutuskäike.','musti.jpg','saadaval',1,1,'2026-07-07'),(10,'Lotte','Koer','emane',7,'2019-09-19','keskmine','Beež','Narva','Rahulik','Väga sõbralik vanem koer.','lotte.jpg','saadaval',1,1,'2026-07-08'),(11,'Pätu','Kass','isane',2,'2024-02-11','','Must','Jõhvi','Mänguline','Armastab pallidega mängida.','patu.jpg','saadaval',0,1,'2026-07-09'),(12,'Kribu','Küülik','emane',1,'2025-01-15','','Valge','Tallinn','Armas küülik','Harjunud inimestega.','kribu.jpg','saadaval',0,0,'2026-07-10'),(13,'Roki','Koer','isane',8,'2018-07-07','suur','Must','Tartu','Kogenud sõber','Rahulik ja kuulekas.','roki.jpg','hoolduses',1,1,'2026-07-11'),(14,'Lumi','Kass','emane',5,'2021-12-24','','Valge','Pärnu','Hellik','Naudib pai tegemist.','lumi.jpg','saadaval',1,1,'2026-07-12'),(15,'Marta','Koer','emane',4,'2022-10-09','keskmine','Pruun','Rapla','Sõbralik','Sobib lastega perre.','marta.jpg','broneeritud',1,1,'2026-07-13'),(16,'Simba','Kass','isane',2,'2024-01-08','','Oranž','Valga','Julge','Väga uudishimulik kass.','simba.jpg','saadaval',1,0,'2026-07-14'),(17,'Bobby','Koer','isane',6,'2020-05-03','suur','Must-valge','Võru','Truu kaaslane','Armastab õues joosta.','bobby.jpg','hoolduses',1,1,'2026-07-15'),(18,'Kiisu','Kass','emane',1,'2025-03-14','','Kilpkonn','Paide','Noor kass','Õpib alles maailma tundma.','kiisu.jpg','saadaval',0,1,'2026-07-16'),(19,'Rufi','Koer','isane',2,'2023-08-18','keskmine','Hall','Kuressaare','Aktiivne','Sobib sportlikule perele.','rufi.jpg','saadaval',0,1,'2026-07-17'),(20,'Muri','Koer','emane',9,'2017-11-11','suur','Must','Tallinn','Rahulik','Vajab rahulikku kodu.','muri.jpg','hoolduses',1,1,'2026-07-18'),(21,'Triibu','Kass','isane',7,'2019-04-20','','Triibuline','Põlva','Tasane','Meeldib aknal istuda.','triibu.jpg','broneeritud',1,1,'2026-07-19'),(22,'Sipsik','Kass','emane',3,'2023-06-06','','Must-valge','Keila','Seltskondlik','Naudib mängimist ja tähelepanu.','sipsik.jpg','saadaval',1,1,'2026-07-20');
/*!40000 ALTER TABLE `loomad` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-07 17:16:10
