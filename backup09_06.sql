-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: localhost    Database: mercadonline
-- ------------------------------------------------------
-- Server version	8.0.42

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
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_BA388B7A76ED395` (`user_id`),
  KEY `IDX_BA388B74584665A` (`product_id`),
  CONSTRAINT `FK_BA388B74584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  CONSTRAINT `FK_BA388B7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Bebidas','Refrescos, zumos, agua, bebidas alcoh├│licas.'),(2,'Frutas y Verduras','Productos frescos de la huerta de cercan├¡as.'),(3,'Carnes y Aves','Todo tipo de carnes frescas y procesadas.'),(4,'Pescados y Mariscos','Productos frescos y congelados del mar.'),(5,'L├ícteos y Huevos','Leche, quesos, yogures, y huevos.'),(6,'Panader├¡a y Reposter├¡a','Pan, boller├¡a, pasteles y galletas.'),(7,'Despensa','Productos no perecederos como arroz, pasta, aceites, y conservas.'),(8,'Hogar y Limpieza','Productos para la limpieza del hogar, utensilios y art├¡culos para el cuidado de la ropa.');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20250427202250','2025-05-05 20:24:15',172),('DoctrineMigrations\\Version20250427203603','2025-05-05 20:24:15',24),('DoctrineMigrations\\Version20250427204527','2025-05-05 20:24:15',216),('DoctrineMigrations\\Version20250427205725','2025-05-05 20:24:16',114),('DoctrineMigrations\\Version20250427210353','2025-05-05 20:24:16',344),('DoctrineMigrations\\Version20250427212100','2025-05-05 20:24:16',281),('DoctrineMigrations\\Version20250501202641','2025-05-05 20:24:16',103),('DoctrineMigrations\\Version20250518063048','2025-05-18 06:31:18',63),('DoctrineMigrations\\Version20250518065916','2025-05-18 06:59:47',123),('DoctrineMigrations\\Version20250518193455','2025-05-24 09:58:07',128);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F5299398A76ED395` (`user_id`),
  CONSTRAINT `FK_F5299398A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` VALUES (1,1,7.50,'PAID','Direcci├│n de env├¡o predeterminada','2025-05-24 14:13:27','2025-05-24 20:43:05'),(2,1,10.30,'pending','Direcci├│n de env├¡o predeterminada','2025-05-24 20:44:59','2025-05-24 20:44:59'),(3,1,25.45,'PAID','Direcci├│n de env├¡o predeterminada','2025-06-05 14:50:26','2025-06-05 15:26:38'),(4,1,8.00,'PAID','Direcci├│n de env├¡o predeterminada','2025-06-06 06:48:58','2025-06-09 07:21:07'),(5,1,30.30,'pending','Direcci├│n de env├¡o predeterminada','2025-06-09 07:19:13','2025-06-09 07:19:13'),(6,12,35.35,'pending','Direcci├│n de env├¡o predeterminada','2025-06-09 07:27:23','2025-06-09 07:27:23'),(7,12,5.98,'pending','Direcci├│n de env├¡o predeterminada','2025-06-09 07:28:06','2025-06-09 07:28:06');
/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_item`
--

DROP TABLE IF EXISTS `order_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_ref_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_52EA1F09E238517C` (`order_ref_id`),
  KEY `IDX_52EA1F094584665A` (`product_id`),
  CONSTRAINT `FK_52EA1F094584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  CONSTRAINT `FK_52EA1F09E238517C` FOREIGN KEY (`order_ref_id`) REFERENCES `order` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_item`
--

LOCK TABLES `order_item` WRITE;
/*!40000 ALTER TABLE `order_item` DISABLE KEYS */;
INSERT INTO `order_item` VALUES (1,1,1,1,0.30),(2,1,72,2,3.00),(3,1,14,2,0.60),(4,2,72,1,3.00),(5,2,40,1,2.50),(6,2,1,1,1.80),(7,2,42,1,3.00),(8,3,2,1,0.85),(9,3,1,6,1.80),(10,3,6,1,0.80),(11,3,9,1,1.00),(12,3,59,1,8.00),(13,3,14,1,0.60),(14,3,61,2,1.70),(15,4,59,1,8.00),(16,5,27,1,18.50),(17,5,72,1,3.00),(18,5,9,1,1.00),(19,5,29,1,6.30),(20,5,15,3,0.50),(21,6,59,1,30.00),(22,6,2,1,0.85),(23,6,61,1,1.70),(24,6,7,1,2.20),(25,6,14,1,0.60),(26,7,24,2,2.99);
/*!40000 ALTER TABLE `order_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_last_four` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_6D28840D8D9F6D38` (`order_id`),
  CONSTRAINT `FK_6D28840D8D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment`
--

LOCK TABLES `payment` WRITE;
/*!40000 ALTER TABLE `payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `sales` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D34A04AD12469DE2` (`category_id`),
  CONSTRAINT `FK_D34A04AD12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (1,1,'Agua Cortes 6x1L','Proviene del monte de utilidad p├║blica de Sant Joan de Penyagolosa y su pico m├ís elevado (1.814 m de altitud), considerado uno de los techos de la Comunidad Valenciana por excelencia..',1.80,192,28,NULL,'https://i.postimg.cc/4xbcgTRt/agua-Cortes.webp'),(2,1,'Coca-Cola 330ml','Refresco de cola cl├ísico, ideal para acompa├▒ar tus comidas.',0.85,148,12,NULL,'https://i.postimg.cc/w3YKww8g/cocacola.png'),(3,1,'Zumo de Naranja Don Sim├│n 1L','Zumo de naranja exprimido, sin az├║cares a├▒adidos.',1.75,100,3,NULL,'https://i.postimg.cc/K8GG11H9/donsimon.webp'),(4,1,'Cerveza Mahou Cinco Estrellas 33cl','Cerveza lager espa├▒ola, de sabor equilibrado y refrescante.',0.95,120,4,NULL,'https://i.postimg.cc/K8NzgMBR/mahou.jpg'),(5,1,'Vino Tinto Rioja Crianza Marqu├®s de C├íceres','Vino tinto crianza de la Rioja, con 12 meses en barrica de roble.',8.50,60,2,NULL,'https://i.postimg.cc/3RMzD6wK/tintomarquescaceres.webp'),(6,1,'Fanta Naranja 330ml','Refresco de naranja con burbujas, sabor intenso y refrescante.',0.80,129,10,NULL,'https://i.postimg.cc/mDCfX4nz/fantanaranja.jpg'),(7,1,'Agua con Gas Perrier 750ml','Agua mineral natural con gas, conocida por su pureza y burbujas finas.',2.20,79,9,NULL,'https://i.postimg.cc/Y06PCjhv/perrier.jpg'),(8,2,'Manzanas Golden','Manzanas de color amarillo dorado, de sabor dulce y textura crujiente.',1.20,150,6,NULL,NULL),(9,2,'Naranjas Navel','Naranjas de ombligo, jugosas y dulces, ideales para zumo o consumo directo.',1.00,198,18,NULL,'https://i.postimg.cc/90B32pCc/navel.jpg'),(10,2,'Pl├ítanos de Canarias','Pl├ítanos de la variedad Cavendish, cultivados en Canarias, de sabor dulce e intenso.',1.50,180,8,NULL,NULL),(11,2,'Fresas','Fresas rojas y maduras, perfectas para postres o para comer solas.',2.50,120,7,NULL,NULL),(12,2,'Lechuga Romana','Lechuga de hojas alargadas y crujientes, ideal para ensaladas C├®sar.',0.80,100,4,NULL,NULL),(13,2,'Tomates','Tomates rojos y maduros, ideales para ensaladas, salsas o gazpacho.',1.00,200,7,NULL,NULL),(14,2,'Pepinos','Pepinos frescos, de piel verde y carne crujiente, perfectos para ensaladas o gazpacho.',0.60,146,15,NULL,'https://i.postimg.cc/GtC1XtFV/pepinos.png'),(15,2,'Zanahorias','Zanahorias naranjas, dulces y crujientes, ideales para ensaladas, guisos o zumos.',0.50,177,9,NULL,NULL),(16,2,'Cebollas','Cebollas blancas, de sabor suave, ideales para todo tipo de platos.',0.70,120,5,NULL,NULL),(17,2,'Patatas','Patatas de la variedad Kennebec, ideales para fre├¡r, cocer o asar.',0.80,200,7,NULL,NULL),(18,3,'Pechuga de Pollo Fresca','Pechuga de pollo sin piel, ideal para plancha, horno o guisos.',5.99,50,4,NULL,NULL),(19,3,'Muslos de Pollo Frescos','Muslos de pollo con piel, perfectos para asar o guisar.',3.99,60,3,NULL,NULL),(20,3,'Filetes de Ternera','Filetes de ternera tiernos, ideales para plancha o empanar.',8.99,40,2,NULL,NULL),(21,3,'Carne Picada de Ternera','Carne picada de ternera magra, perfecta para hamburguesas, alb├│ndigas o salsa bolo├▒esa.',7.50,55,1,NULL,NULL),(22,3,'Solomillo de Cerdo','Solomillo de cerdo tierno y jugoso, ideal para horno o plancha.',6.75,45,0,NULL,NULL),(23,3,'Costillas de Cerdo','Costillas de cerdo, ideales para asar a la parrilla o al horno.',4.50,70,1,NULL,NULL),(24,3,'Alitas de Pollo','Alitas de pollo, perfectas para aperitivos, barbacoas o al horno.',2.99,78,6,NULL,'https://i.postimg.cc/7LQK8p8q/Alas-Pollo.jpg'),(25,3,'Pavo Troceado','Pavo troceado, ideal para guisos, estofados o brochetas.',6.25,50,1,NULL,NULL),(26,3,'Hamburguesas de Pollo','Hamburguesas de pollo, una opci├│n m├ís ligera y saludable.',4.80,60,2,NULL,NULL),(27,4,'Langostinos de Vinar├│s','Langostinos frescos, conocidos por su calidad y sabor, capturados en las costas de Vinar├│s.',18.50,29,8,NULL,NULL),(28,4,'Sepia de la Lonja de Castell├│n','Sepia fresca, capturada por pescadores locales y tra├¡da directamente de la lonja de Castell├│n.',9.75,25,8,NULL,NULL),(29,4,'Boquerones del Mediterr├íneo','Boquerones frescos, pescados en el Mediterr├íneo y preparados seg├║n la tradici├│n local.',6.30,39,8,NULL,NULL),(30,4,'Sardinas de Castell├│n','Sardinas frescas, ideales para la parrilla, provenientes de la pesca local de Castell├│n.',4.80,50,4,NULL,NULL),(31,4,'Mejillones del Delta del Ebro','Mejillones frescos, cultivados en las aguas del Delta del Ebro, cercanos a Castell├│n, famosos por su sabor.',2.95,60,2,NULL,NULL),(32,4,'Pulpo de Roca','Pulpo de roca, pescado en las costas rocosas de la provincia, perfecto para preparar a la gallega.',12.00,20,4,NULL,NULL),(33,4,'Calamares de la Costa Azahar','Calamares frescos, capturados en la Costa Azahar, ideales para fre├¡r o a la plancha.',8.90,35,3,NULL,NULL),(34,4,'Gambas de Castell├│n','Gambas frescas, un marisco apreciado de la zona de Castell├│n, perfectas para arroces y paellas.',15.60,28,5,NULL,NULL),(35,4,'Dorada Fresca','Dorada fresca, pescado blanco de la zona, ideal para preparar al horno o a la sal.',7.20,45,2,NULL,NULL),(36,4,'Lubina Salvaje','Lubina salvaje, pescado azul de la costa de Castell├│n, de sabor intenso y carne firme.',9.50,30,1,NULL,NULL),(37,5,'Leche Entera','Leche entera de vaca, ideal para el consumo diario.',1.20,100,4,NULL,NULL),(38,5,'Yogur Natural','Yogur natural sin azucar, perfecto para un desayuno o merienda saludable.',0.80,80,5,NULL,NULL),(39,5,'Queso Curado','Queso curado de oveja, con un sabor intenso y una textura firme.',9.50,50,2,NULL,NULL),(40,5,'Huevos Camperos','Huevos de gallinas camperas, con la yema de color intenso.',2.50,119,7,NULL,NULL),(41,5,'Nata para Montar','Nata para montar con un 35% de materia grasa, ideal para postres.',2.00,60,2,NULL,NULL),(42,5,'Queso Fresco','Queso fresco de vaca, suave y cremoso, perfecto para ensaladas o untar.',3.00,89,7,NULL,NULL),(43,5,'Bebida de Soja','Bebida de soja, una alternativa vegetal a la leche.',1.80,70,2,NULL,NULL),(44,5,'Queso Parmesano','Queso Parmesano Italiano, ideal para rallar sobre pasta.',12.00,40,4,NULL,NULL),(45,5,'Yogur Griego','Yogur griego, cremoso y rico en prote├¡nas.',1.50,110,2,NULL,NULL),(46,5,'Mantequilla','Mantequilla sin sal, ideal para cocinar y hornear.',2.30,85,1,NULL,NULL),(47,6,'Pan de Trigo','Pan de trigo integral, elaborado con masa madre y horneado en horno de piedra.',1.50,100,1,NULL,NULL),(48,6,'Fogasa dels Ibarsos','fogasa de los Ibarsos, crujiente por fuera y tierno por dentro, ideal para el d├¡a a d├¡a.',0.80,200,0,NULL,'https://i.postimg.cc/d1kyf0Kj/fogasa-ibarsos.jpg'),(49,6,'Croissants','Cruasanes de mantequilla, hojaldrados y reci├®n hechos, perfectos para el desayuno o la merienda.',1.20,150,0,NULL,NULL),(50,6,'Ensaimada','Ensaimada tradicional, dulce y esponjosa, t├¡pica de Mallorca.',2.50,80,0,NULL,NULL),(51,6,'Magdalenas','Magdalenas caseras, elaboradas con aceite de oliva y un toque de lim├│n.',1.00,120,0,NULL,NULL),(52,6,'Pastel de Chocolate','Pastel de chocolate, bizcocho jugoso con cobertura de chocolate negro.',3.00,60,0,NULL,NULL),(53,6,'Galletas de Avena','Galletas de avena, elaboradas con ingredientes integrales y un toque de canela.',1.80,90,0,NULL,NULL),(54,6,'Rosquilletas','Rosquilletas crujientes, t├¡picas de la Comunidad Valenciana, ideales para acompa├▒ar embutidos o quesos.',2.00,110,0,NULL,NULL),(55,6,'Coca de Aceite','Coca de aceite, tradicional de la regi├│n, crujiente y sabrosa, perfecta para cualquier ocasi├│n.',2.20,70,0,NULL,NULL),(56,6,'Tarta de Almendras','Tarta de almendras, elaborada con almendra Marcona, un postre cl├ísico y delicioso.',9.00,40,1,NULL,NULL),(57,7,'Arroz Bomba','Arroz de la variedad Bomba, ideal para paellas y otros arroces melosos.',2.50,100,2,NULL,'https://i.postimg.cc/66RgN5Hq/arroz-bomba.jpg'),(58,7,'Pasta Integral','Pasta integral elaborada con trigo duro, rica en fibra.',1.80,120,4,NULL,NULL),(59,7,'Aceite de Oliva Virgen Extra','Aceite de oliva virgen extra de Penyagolosa, de primera presi├│n en fr├¡o, ideal para ali├▒os y cocina.',30.00,53,12,NULL,'https://i.postimg.cc/bNC8MwCM/aceite-oliva-5-L.jpg'),(60,7,'Lentejas Pardinas','Lentejas pardinas, peque├▒as y de sabor suave, perfectas para guisos y estofados.',1.50,150,3,NULL,NULL),(61,7,'Garbanzos Pedrosillano','Garbanzos de la variedad Pedrosillano, peque├▒os y de sabor intenso, ideales para cocido.',1.70,127,14,NULL,NULL),(62,7,'Tomate Frito Casero','Tomate frito casero, elaborado con tomates frescos y aceite de oliva.',2.20,90,0,NULL,NULL),(63,7,'At├║n en Aceite de Oliva 500g','At├║n claro en aceite de oliva, ideal para ensaladas, bocadillos o aperitivos.',7.00,110,0,NULL,'https://i.postimg.cc/kgzgDvSq/atun-aceite.jpg'),(64,7,'Esp├írragos Blancos','Esp├írragos blancos de Navarra, gruesos y tiernos, perfectos para acompa├▒ar cualquier plato.',4.50,70,0,NULL,NULL),(65,7,'Pimientos del Piquillo','Pimientos del piquillo asados, de sabor dulce y textura suave.',3.80,80,0,NULL,NULL),(66,7,'Caldo de Pollo','Caldo de pollo casero, elaborado con ingredientes naturales.',2.00,100,0,NULL,NULL),(67,8,'Detergente L├¡quido','Detergente l├¡quido para ropa, eficaz incluso en agua fr├¡a.',3.50,100,3,NULL,NULL),(68,8,'Suavizante Concentrado','Suavizante concentrado para ropa, deja la ropa suave y con un agradable aroma.',2.80,80,1,NULL,NULL),(69,8,'Limpiador Multiusos','Limpiador multiusos, ideal para la limpieza de todo tipo de superficies.',2.00,120,1,NULL,NULL),(70,8,'Lej├¡a','Lej├¡a, para la limpieza y desinfecci├│n profunda del hogar.',1.50,150,1,NULL,NULL),(71,8,'Papel de Cocina','Rollo de papel de cocina, absorbente y resistente.',1.00,200,3,NULL,'https://i.postimg.cc/nLXyNKj9/papel-cocina.jpg'),(72,8,'Papel Higi├®nico','Paquete de papel higi├®nico de doble capa, suave y resistente.',3.00,176,19,NULL,'https://i.postimg.cc/Vk85f6BN/papel-higienico.jpg'),(73,8,'Bolsas de Basura','Rollo de bolsas de basura resistentes, con cierre f├ícil.',2.50,130,0,NULL,NULL),(74,8,'Guantes de Limpieza','Par de guantes de limpieza, resistentes y c├│modos, para proteger tus manos.',1.80,90,0,NULL,NULL),(75,8,'Bayeta Microfibra','Bayeta de microfibra, ideal para la limpieza en seco y en h├║medo, sin dejar pelusa.',1.20,110,0,NULL,NULL),(76,8,'Ambientador','Ambientador fresco de aroma natural a menta, envase de 5 L con pulverizador de regalo.',42.30,40,0,NULL,'https://i.postimg.cc/ryCMcJmv/ambientador-menta-5-l.jpg');
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'jose@gmail.com','$2y$04$sAEhE.gwtZ.x5pbIHRXsCOpPwwdv5uvkBIi2aVIzGuGVZ0VD1mCMu','joseL','Calle Obispo Beltran n┬║1','674161540','ROLE_ADMIN','2025-05-05 20:26:16'),(3,'test3@gmail.com','$2y$13$v2nlMAjNnFDugJzQa2mdjuoVkSqEbWgUnI7.OxzVq4WQPNgT3CMXa','','','','ROLE_USER','2025-05-05 20:27:55'),(4,'test4@gmail.com','$2y$13$qPChjDTYA6tWA3YI2AkbGeoFRiahhmzsaBVVtre.5mfdvyQEwDI9W','','','','ROLE_USER','2025-05-05 20:28:17'),(5,'jose286@gmail.com','$2y$13$sb/vM.frFRSt2VF1/QR0xea2MQ2S9k.6LCgELY/BMpWs5HCOrRxnm','','','','ROLE_ADMIN','2025-05-05 20:28:54'),(6,'prueba11@gmail.com','$2y$13$gngONSkQmQHZloQKnh9BaejRqd7GzZuIO/07arfyUe70eyvFT77xO','prueba111','','','ROLE_USER','2025-05-17 10:03:15'),(7,'prueba12@gmail.com','$2y$13$7rPF87hrzJnKnCIPkY0XhOt3ozxfTRy22SjqS4rMSWuP6sd8bUlxe','probando122','probandoooo','333222111','ROLE_USER','2025-05-17 10:14:17'),(8,'test8@gmail.com','$2y$13$AKi2nlY03iYeayGXhZSnSeaJDYm5h0vOeiurlpIKdr2uzGfHOwHoi','test8',NULL,NULL,'ROLE_USER','2025-05-24 14:34:45'),(9,'test20@gmail.com','$2y$13$oulQM29s9nDvA48ttFvXMOIMEyY2BrRhTu./uh.YE9OnZ8KxPiO5i','test20','test20','202020202','ROLE_USER','2025-06-09 07:22:51'),(10,'test21@gmail.com','$2y$13$wMl/.44koLr6vzeIY/ZzUO6YqCXGiCRQx.p6QeQ7RyAIiZ9Hseg0u','test21',NULL,NULL,'ROLE_USER','2025-06-09 07:23:40'),(11,'test22@gmail.com','$2y$13$UJ5LfpHXyHMyngXLVpeo0uB3yajavPKBNFV97xGBeUKAAyL2lb6aC','test22','test22','222222222','ROLE_USER','2025-06-09 07:24:12'),(12,'test23@gmail.com','$2y$13$7Mb.HgwBUVeMtXhef12O/Owku8bczpKyiPuAdcXccPGIsaPKmuKtC','test23','test23',NULL,'ROLE_USER','2025-06-09 07:24:44');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-09  7:31:26
