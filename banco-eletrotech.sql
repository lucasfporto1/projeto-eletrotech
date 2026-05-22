-- MySQL dump 10.13  Distrib 8.0.45, for macos15 (arm64)
--
-- Host: localhost    Database: eletrotech
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tabela_eletricistas`
--

DROP TABLE IF EXISTS `tabela_eletricistas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tabela_eletricistas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cpf` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_contratacao` date NOT NULL,
  `data_demissao` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabela_eletricistas`
--

LOCK TABLES `tabela_eletricistas` WRITE;
/*!40000 ALTER TABLE `tabela_eletricistas` DISABLE KEYS */;
INSERT INTO `tabela_eletricistas` VALUES (1,'12345678910','Lucas','2010-03-12',NULL),(2,'07574900345','Lucas','2005-05-12',NULL),(3,'32132112321','Lucas Porto','2026-03-15',NULL);
/*!40000 ALTER TABLE `tabela_eletricistas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tabela_metas`
--

DROP TABLE IF EXISTS `tabela_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tabela_metas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `eletricista_meta` int NOT NULL,
  `mes_meta` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vlr_meta` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `eletricista_meta` (`eletricista_meta`),
  CONSTRAINT `tabela_metas_ibfk_1` FOREIGN KEY (`eletricista_meta`) REFERENCES `tabela_eletricistas` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabela_metas`
--

LOCK TABLES `tabela_metas` WRITE;
/*!40000 ALTER TABLE `tabela_metas` DISABLE KEYS */;
INSERT INTO `tabela_metas` VALUES (7,3,'2024-01',2000.00),(8,3,'2026-05',2000.00);
/*!40000 ALTER TABLE `tabela_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tabela_ordens_servico`
--

DROP TABLE IF EXISTS `tabela_ordens_servico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tabela_ordens_servico` (
  `id` int NOT NULL AUTO_INCREMENT,
  `eletricista_os` int NOT NULL,
  `data_os` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `eletricista_os` (`eletricista_os`),
  CONSTRAINT `tabela_ordens_servico_ibfk_1` FOREIGN KEY (`eletricista_os`) REFERENCES `tabela_eletricistas` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabela_ordens_servico`
--

LOCK TABLES `tabela_ordens_servico` WRITE;
/*!40000 ALTER TABLE `tabela_ordens_servico` DISABLE KEYS */;
INSERT INTO `tabela_ordens_servico` VALUES (8,1,'2005-03-15'),(9,3,'2010-03-15'),(10,1,'2025-04-15');
/*!40000 ALTER TABLE `tabela_ordens_servico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tabela_os_materiais`
--

DROP TABLE IF EXISTS `tabela_os_materiais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tabela_os_materiais` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_os` int NOT NULL,
  `id_produto` int NOT NULL,
  `qtd_utilizada` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_os` (`id_os`),
  KEY `id_produto` (`id_produto`),
  CONSTRAINT `tabela_os_materiais_ibfk_1` FOREIGN KEY (`id_os`) REFERENCES `tabela_ordens_servico` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tabela_os_materiais_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `tabela_produtos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabela_os_materiais`
--

LOCK TABLES `tabela_os_materiais` WRITE;
/*!40000 ALTER TABLE `tabela_os_materiais` DISABLE KEYS */;
INSERT INTO `tabela_os_materiais` VALUES (1,8,3,2),(2,9,4,10),(3,10,8,5);
/*!40000 ALTER TABLE `tabela_os_materiais` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tabela_produtos`
--

DROP TABLE IF EXISTS `tabela_produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tabela_produtos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_produto` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vlr_unitario` decimal(10,2) NOT NULL,
  `qtd_estoque` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabela_produtos`
--

LOCK TABLES `tabela_produtos` WRITE;
/*!40000 ALTER TABLE `tabela_produtos` DISABLE KEYS */;
INSERT INTO `tabela_produtos` VALUES (3,'Cabo',9.00,0),(4,'Cabo de energia',12.00,0),(8,'Cabo PP 2,5mm2',8.99,95);
/*!40000 ALTER TABLE `tabela_produtos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tabela_usuarios`
--

DROP TABLE IF EXISTS `tabela_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tabela_usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabela_usuarios`
--

LOCK TABLES `tabela_usuarios` WRITE;
/*!40000 ALTER TABLE `tabela_usuarios` DISABLE KEYS */;
INSERT INTO `tabela_usuarios` VALUES (5,'Lucas Farias','$2y$10$BG1gVttLEP9M97b5YBMuCeMQhjPvWj7an1PoJRN9ZaSQ5F/lcuwFG');
/*!40000 ALTER TABLE `tabela_usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-22 16:42:43
