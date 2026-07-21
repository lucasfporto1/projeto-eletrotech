-- MySQL dump 10.13  Distrib 8.0.44, for macos12.7 (arm64)
--
-- Host: 127.0.0.1    Database: eletrotech
-- ------------------------------------------------------
-- Server version	8.0.44

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabela_eletricistas`
--

LOCK TABLES `tabela_eletricistas` WRITE;
/*!40000 ALTER TABLE `tabela_eletricistas` DISABLE KEYS */;
INSERT INTO `tabela_eletricistas` VALUES (1,'67125897037','Carlos Alberto','2026-05-28',NULL),(2,'07574900345','Lucas','2025-03-15',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabela_metas`
--

LOCK TABLES `tabela_metas` WRITE;
/*!40000 ALTER TABLE `tabela_metas` DISABLE KEYS */;
INSERT INTO `tabela_metas` VALUES (1,1,'2026-06',2000.00),(2,2,'2026-04',5000.00),(3,2,'2026-08',3000.00);
/*!40000 ALTER TABLE `tabela_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tabela_movimentacoes`
--

DROP TABLE IF EXISTS `tabela_movimentacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tabela_movimentacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_produto` int NOT NULL,
  `tipo` enum('entrada','saida') NOT NULL,
  `quantidade` int NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `data_mov` date NOT NULL,
  `origem` varchar(100) NOT NULL DEFAULT '',
  `id_os` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mov_produto` (`id_produto`),
  KEY `fk_mov_os` (`id_os`),
  CONSTRAINT `fk_mov_os` FOREIGN KEY (`id_os`) REFERENCES `tabela_ordens_servico` (`id`),
  CONSTRAINT `fk_mov_produto` FOREIGN KEY (`id_produto`) REFERENCES `tabela_produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabela_movimentacoes`
--

LOCK TABLES `tabela_movimentacoes` WRITE;
/*!40000 ALTER TABLE `tabela_movimentacoes` DISABLE KEYS */;
INSERT INTO `tabela_movimentacoes` VALUES (1,3,'saida',30,6.50,'2026-05-25','OS #00001',1),(2,1,'saida',50,120.50,'0026-05-10','OS #00002',2),(3,1,'saida',1,120.50,'0026-05-10','OS #00002',2),(4,5,'saida',1,10.00,'2026-07-06','OS #00003',3),(5,5,'saida',1,10.00,'2026-07-06','OS #00003',3),(6,2,'saida',20,18.90,'2026-03-20','OS #00006',6),(8,1,'entrada',51,120.50,'0026-05-10','Estoque inicial',NULL),(9,2,'entrada',20,18.90,'0026-05-10','Estoque inicial',NULL),(10,3,'entrada',100,6.50,'0026-05-10','Estoque inicial',NULL),(11,4,'entrada',50,10.90,'0026-05-10','Estoque inicial',NULL),(12,5,'entrada',2,10.00,'0026-05-10','Estoque inicial',NULL),(13,6,'entrada',3,9.90,'0026-05-10','Estoque inicial',NULL);
/*!40000 ALTER TABLE `tabela_movimentacoes` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabela_ordens_servico`
--

LOCK TABLES `tabela_ordens_servico` WRITE;
/*!40000 ALTER TABLE `tabela_ordens_servico` DISABLE KEYS */;
INSERT INTO `tabela_ordens_servico` VALUES (1,1,'2026-05-25'),(2,1,'0026-05-10'),(3,1,'2026-07-06'),(4,2,'2026-03-13'),(5,2,'2006-03-15'),(6,2,'2026-03-20');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabela_os_materiais`
--

LOCK TABLES `tabela_os_materiais` WRITE;
/*!40000 ALTER TABLE `tabela_os_materiais` DISABLE KEYS */;
INSERT INTO `tabela_os_materiais` VALUES (1,1,3,30),(2,2,1,50),(3,2,1,1),(4,3,5,1),(5,3,5,1),(6,6,2,20);
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
INSERT INTO `tabela_produtos` VALUES (1,'Cabo Flexível 2.5mm Preto',120.50,0),(2,'Disjuntor DIN 16A',18.90,0),(3,'Fita Isolante 3M',6.50,70),(4,'Fita',10.90,50),(5,'cabo',10.00,0),(6,'Cabo',9.90,3),(7,'Cabo de energia',100.00,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabela_usuarios`
--

LOCK TABLES `tabela_usuarios` WRITE;
/*!40000 ALTER TABLE `tabela_usuarios` DISABLE KEYS */;
INSERT INTO `tabela_usuarios` VALUES (26,'Lucas','$2y$10$1DnK3WE2RZ9gLKOP6HGF1uHR.A8jSc2FYFZ9ysUL95W/f5WlxfMvq');
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

-- Dump completed
