-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: bd_kusi
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `bd_kusi`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `bd_kusi` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `bd_kusi`;

--
-- Table structure for table `ayudas_entregadas`
--

DROP TABLE IF EXISTS `ayudas_entregadas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ayudas_entregadas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `beneficiario_id` bigint(20) unsigned NOT NULL,
  `tipo_ayuda_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `fecha_entrega` date NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `beneficiario_id` (`beneficiario_id`),
  KEY `tipo_ayuda_id` (`tipo_ayuda_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_ayudas_fecha` (`fecha_entrega`),
  CONSTRAINT `ayudas_entregadas_ibfk_1` FOREIGN KEY (`beneficiario_id`) REFERENCES `beneficiarios` (`id`),
  CONSTRAINT `ayudas_entregadas_ibfk_2` FOREIGN KEY (`tipo_ayuda_id`) REFERENCES `tipos_ayuda` (`id`),
  CONSTRAINT `ayudas_entregadas_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ayudas_entregadas`
--

LOCK TABLES `ayudas_entregadas` WRITE;
/*!40000 ALTER TABLE `ayudas_entregadas` DISABLE KEYS */;
INSERT INTO `ayudas_entregadas` VALUES (2,1,2,1,'2026-10-22','una caja',NULL,'2026-10-22 22:13:40','2026-10-22 22:13:40');
/*!40000 ALTER TABLE `ayudas_entregadas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `beneficiarios`
--

DROP TABLE IF EXISTS `beneficiarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `beneficiarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dni` varchar(15) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` enum('M','F','Otro') DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `num_familiares` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `situacion` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `registrado_por` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dni` (`dni`),
  KEY `registrado_por` (`registrado_por`),
  KEY `idx_benef_apellidos` (`apellidos`,`nombres`),
  CONSTRAINT `beneficiarios_ibfk_1` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `beneficiarios`
--

LOCK TABLES `beneficiarios` WRITE;
/*!40000 ALTER TABLE `beneficiarios` DISABLE KEYS */;
INSERT INTO `beneficiarios` VALUES (1,'75987763','liliana','quillca','2013-06-04','F','jr.nicolas de ppierola','937472904',5,'adulto mayor','ya no puede trabajar','activo',1,'2026-09-30 06:41:19','2026-10-22 22:12:10'),(3,'73987702','Maria Elena','Rojas Huamani','2022-02-08','F','jr. miguel grau','945684934',5,'desempleo',NULL,'activo',1,'2026-10-22 21:57:43','2026-10-22 21:57:43'),(5,'73628308','Brenda','Retamozo Merino','2018-08-08','F','jr.nicolas de pierola','958395028',10,'desempleo',NULL,'activo',1,'2026-10-22 22:11:12','2026-10-22 22:11:12');
/*!40000 ALTER TABLE `beneficiarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias_producto`
--

DROP TABLE IF EXISTS `categorias_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias_producto` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_producto`
--

LOCK TABLES `categorias_producto` WRITE;
/*!40000 ALTER TABLE `categorias_producto` DISABLE KEYS */;
INSERT INTO `categorias_producto` VALUES (1,'Alimentos no perecibles','2026-09-30 00:19:44','2026-09-30 00:19:44'),(2,'Alimentos perecibles','2026-09-30 00:19:44','2026-09-30 00:19:44'),(3,'Ropa','2026-09-30 00:19:44','2026-09-30 00:19:44'),(4,'Medicinas','2026-09-30 00:19:44','2026-09-30 00:19:44'),(5,'Útiles y otros','2026-09-30 00:19:44','2026-09-30 00:19:44'),(6,'No perecibles','2026-10-03 22:49:15','2026-10-03 22:49:15');
/*!40000 ALTER TABLE `categorias_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_donaciones`
--

DROP TABLE IF EXISTS `detalle_donaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_donaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `donacion_id` bigint(20) unsigned NOT NULL,
  `producto_id` bigint(20) unsigned NOT NULL,
  `cantidad` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `donacion_id` (`donacion_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `detalle_donaciones_ibfk_1` FOREIGN KEY (`donacion_id`) REFERENCES `donaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detalle_donaciones_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `chk_cant_don` CHECK (`cantidad` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_donaciones`
--

LOCK TABLES `detalle_donaciones` WRITE;
/*!40000 ALTER TABLE `detalle_donaciones` DISABLE KEYS */;
INSERT INTO `detalle_donaciones` VALUES (1,1,1,0.02,'2026-10-03 22:58:35','2026-10-03 22:58:35'),(2,2,2,1.00,'2026-10-22 22:22:19','2026-10-22 22:22:19'),(3,3,3,5.00,'2026-11-16 14:23:23','2026-11-16 14:23:23');
/*!40000 ALTER TABLE `detalle_donaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donaciones`
--

DROP TABLE IF EXISTS `donaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `donaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `donante_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `fecha_donacion` date NOT NULL,
  `estado` enum('registrada','anulada') NOT NULL DEFAULT 'registrada',
  `observacion` text DEFAULT NULL,
  `motivo_anulacion` varchar(255) DEFAULT NULL,
  `anulada_at` timestamp NULL DEFAULT NULL,
  `anulada_por` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `donante_id` (`donante_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_don_fecha` (`fecha_donacion`),
  KEY `fk_donaciones_anulada_por` (`anulada_por`),
  CONSTRAINT `donaciones_ibfk_1` FOREIGN KEY (`donante_id`) REFERENCES `donantes` (`id`),
  CONSTRAINT `donaciones_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_donaciones_anulada_por` FOREIGN KEY (`anulada_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donaciones`
--

LOCK TABLES `donaciones` WRITE;
/*!40000 ALTER TABLE `donaciones` DISABLE KEYS */;
INSERT INTO `donaciones` VALUES (1,1,1,'2026-06-16','anulada',NULL,'nada','2026-10-03 23:02:57',1,'2026-10-03 22:58:35','2026-10-03 23:02:57'),(2,2,1,'2026-10-22','registrada',NULL,NULL,NULL,NULL,'2026-10-22 22:22:19','2026-10-22 22:22:19'),(3,2,1,'2026-11-02','registrada',NULL,NULL,NULL,NULL,'2026-11-16 14:23:23','2026-11-16 14:23:23');
/*!40000 ALTER TABLE `donaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donantes`
--

DROP TABLE IF EXISTS `donantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `donantes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tipo_persona` enum('natural','juridica') NOT NULL DEFAULT 'natural',
  `nombre_razon_social` varchar(150) NOT NULL,
  `documento` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donantes`
--

LOCK TABLES `donantes` WRITE;
/*!40000 ALTER TABLE `donantes` DISABLE KEYS */;
INSERT INTO `donantes` VALUES (1,'natural','ayuda','73829948','983642762','melicamedina@gmail.com','jr. miguel grau','2026-10-03 22:53:49','2026-10-03 22:53:49'),(2,'juridica','municipalidad','203748462','967834677','muni@gmail.com','plaza de armas','2026-10-22 22:17:58','2026-10-22 22:17:58');
/*!40000 ALTER TABLE `donantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entregas_alimentos`
--

DROP TABLE IF EXISTS `entregas_alimentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entregas_alimentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `beneficiario_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `tipo_entrega` enum('almuerzo','desayuno','cena','canasta','otro') NOT NULL,
  `raciones` smallint(5) unsigned NOT NULL DEFAULT 1,
  `observacion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `beneficiario_id` (`beneficiario_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_entregas_fecha` (`fecha`),
  CONSTRAINT `entregas_alimentos_ibfk_1` FOREIGN KEY (`beneficiario_id`) REFERENCES `beneficiarios` (`id`),
  CONSTRAINT `entregas_alimentos_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entregas_alimentos`
--

LOCK TABLES `entregas_alimentos` WRITE;
/*!40000 ALTER TABLE `entregas_alimentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `entregas_alimentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimientos_inventario`
--

DROP TABLE IF EXISTS `movimientos_inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_inventario` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `tipo` enum('entrada','salida') NOT NULL,
  `cantidad` decimal(12,2) NOT NULL,
  `fecha` date NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `donacion_id` bigint(20) unsigned DEFAULT NULL,
  `ayuda_entregada_id` bigint(20) unsigned DEFAULT NULL,
  `entrega_alimento_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  KEY `user_id` (`user_id`),
  KEY `donacion_id` (`donacion_id`),
  KEY `ayuda_entregada_id` (`ayuda_entregada_id`),
  KEY `entrega_alimento_id` (`entrega_alimento_id`),
  KEY `idx_mov_fecha` (`fecha`),
  CONSTRAINT `movimientos_inventario_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `movimientos_inventario_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `movimientos_inventario_ibfk_3` FOREIGN KEY (`donacion_id`) REFERENCES `donaciones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `movimientos_inventario_ibfk_4` FOREIGN KEY (`ayuda_entregada_id`) REFERENCES `ayudas_entregadas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `movimientos_inventario_ibfk_5` FOREIGN KEY (`entrega_alimento_id`) REFERENCES `entregas_alimentos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `chk_cant_mov` CHECK (`cantidad` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientos_inventario`
--

LOCK TABLES `movimientos_inventario` WRITE;
/*!40000 ALTER TABLE `movimientos_inventario` DISABLE KEYS */;
INSERT INTO `movimientos_inventario` VALUES (1,1,1,'entrada',10.00,'2026-10-03','Stock inicial',NULL,NULL,NULL,'2026-10-03 22:52:22','2026-10-03 22:52:22'),(2,1,1,'entrada',0.02,'2026-06-16','Donación #1',1,NULL,NULL,'2026-10-03 22:58:35','2026-10-03 22:58:35'),(3,1,1,'salida',0.08,'2026-10-01','entrega',NULL,NULL,NULL,'2026-10-03 23:00:02','2026-10-03 23:00:02'),(4,1,1,'salida',0.02,'2026-10-03','Anulación de donación #1',1,NULL,NULL,'2026-10-03 23:02:57','2026-10-03 23:02:57'),(5,2,1,'entrada',1.00,'2026-10-22','Stock inicial',NULL,NULL,NULL,'2026-10-22 22:21:25','2026-10-22 22:21:25'),(6,2,1,'entrada',1.00,'2026-10-22','Donación #2',2,NULL,NULL,'2026-10-22 22:22:19','2026-10-22 22:22:19'),(7,3,1,'entrada',10.00,'2026-10-26','Stock inicial',NULL,NULL,NULL,'2026-10-26 06:47:40','2026-10-26 06:47:40'),(8,3,1,'entrada',5.00,'2026-11-02','Donación #3',3,NULL,NULL,'2026-11-16 14:23:23','2026-11-16 14:23:23');
/*!40000 ALTER TABLE `movimientos_inventario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `necesidades_beneficiario`
--

DROP TABLE IF EXISTS `necesidades_beneficiario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `necesidades_beneficiario` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `beneficiario_id` bigint(20) unsigned NOT NULL,
  `tipo_ayuda_id` bigint(20) unsigned NOT NULL,
  `fecha_registro` date NOT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `estado` enum('pendiente','atendida') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `beneficiario_id` (`beneficiario_id`),
  KEY `tipo_ayuda_id` (`tipo_ayuda_id`),
  CONSTRAINT `necesidades_beneficiario_ibfk_1` FOREIGN KEY (`beneficiario_id`) REFERENCES `beneficiarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `necesidades_beneficiario_ibfk_2` FOREIGN KEY (`tipo_ayuda_id`) REFERENCES `tipos_ayuda` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `necesidades_beneficiario`
--

LOCK TABLES `necesidades_beneficiario` WRITE;
/*!40000 ALTER TABLE `necesidades_beneficiario` DISABLE KEYS */;
INSERT INTO `necesidades_beneficiario` VALUES (1,1,5,'2026-09-30',NULL,'atendida','2026-09-30 06:43:44','2026-09-30 06:43:54'),(4,1,2,'2026-10-22',NULL,'atendida','2026-10-22 22:14:14','2026-10-25 04:53:01');
/*!40000 ALTER TABLE `necesidades_beneficiario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permiso_rol`
--

DROP TABLE IF EXISTS `permiso_rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permiso_rol` (
  `rol_id` bigint(20) unsigned NOT NULL,
  `permiso_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`rol_id`,`permiso_id`),
  KEY `permiso_id` (`permiso_id`),
  CONSTRAINT `permiso_rol_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permiso_rol_ibfk_2` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permiso_rol`
--

LOCK TABLES `permiso_rol` WRITE;
/*!40000 ALTER TABLE `permiso_rol` DISABLE KEYS */;
INSERT INTO `permiso_rol` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(1,12),(1,13),(1,14),(1,15),(1,16),(1,17),(1,18),(1,19),(1,20),(1,21),(1,22),(1,23),(1,24),(1,25),(2,3),(2,4),(2,5),(2,6),(2,7),(2,8),(2,9),(2,10),(2,11),(2,12),(2,13),(2,14),(2,15),(2,16),(2,17),(3,4),(3,9),(3,11),(3,13),(3,16),(3,17);
/*!40000 ALTER TABLE `permiso_rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos`
--

DROP TABLE IF EXISTS `permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permisos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos`
--

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
INSERT INTO `permisos` VALUES (1,'usuarios.gestionar','Gestionar usuarios','2026-09-30 00:19:44','2026-09-30 00:19:44'),(2,'roles.gestionar','Gestionar roles y permisos','2026-09-30 00:19:44','2026-09-30 00:19:44'),(3,'beneficiarios.crear','Registrar beneficiarios','2026-09-30 00:19:44','2026-09-30 00:19:44'),(4,'beneficiarios.ver','Consultar beneficiarios','2026-09-30 00:19:44','2026-09-30 00:19:44'),(5,'beneficiarios.editar','Modificar beneficiarios','2026-09-30 00:19:44','2026-09-30 00:19:44'),(6,'tipos_ayuda.crear','Registrar tipo de ayuda','2026-09-30 00:19:44','2026-09-30 00:19:44'),(7,'ayudas.crear','Registrar ayudas entregadas','2026-09-30 00:19:44','2026-09-30 00:19:44'),(8,'alimentos.crear','Registrar alimentos / almuerzos','2026-09-30 00:19:44','2026-09-30 00:19:44'),(9,'historial.ver','Consultar historial de ayudas','2026-09-30 00:19:44','2026-09-30 00:19:44'),(10,'donaciones.crear','Registrar donaciones','2026-09-30 00:19:44','2026-09-30 00:19:44'),(11,'donaciones.ver','Consultar donaciones','2026-09-30 00:19:44','2026-09-30 00:19:44'),(12,'productos.crear','Registrar productos en inventario','2026-09-30 00:19:44','2026-09-30 00:19:44'),(13,'inventario.ver','Consultar existencias','2026-09-30 00:19:44','2026-09-30 00:19:44'),(14,'inventario.entrada','Registrar entradas de productos','2026-09-30 00:19:44','2026-09-30 00:19:44'),(15,'inventario.salida','Registrar salidas de productos','2026-09-30 00:19:44','2026-09-30 00:19:44'),(16,'reportes.generar','Generar reportes','2026-09-30 00:19:44','2026-09-30 00:19:44'),(17,'estadisticas.ver','Visualizar estadísticas','2026-09-30 00:19:44','2026-09-30 00:19:44'),(18,'beneficiarios.eliminar','Eliminar beneficiarios','2026-10-02 23:27:55','2026-10-02 23:27:55'),(19,'ayudas.eliminar','Eliminar ayudas entregadas','2026-10-02 23:27:55','2026-10-02 23:27:55'),(20,'alimentos.eliminar','Eliminar entregas de alimentos','2026-10-02 23:27:55','2026-10-02 23:27:55'),(21,'necesidades.eliminar','Eliminar necesidades de ayuda','2026-10-02 23:27:55','2026-10-02 23:27:55'),(22,'tipos_ayuda.eliminar','Eliminar tipos de ayuda sin uso','2026-10-02 23:27:55','2026-10-02 23:27:55'),(23,'productos.eliminar','Eliminar productos y categorías sin uso','2026-10-03 17:29:28','2026-10-03 17:29:28'),(24,'donantes.eliminar','Eliminar donantes sin donaciones','2026-10-03 17:29:28','2026-10-03 17:29:28'),(25,'donaciones.anular','Anular donaciones y revertir stock','2026-10-03 17:29:28','2026-10-03 17:29:28');
/*!40000 ALTER TABLE `permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoria_id` bigint(20) unsigned DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `unidad_medida` varchar(30) NOT NULL DEFAULT 'unidad',
  `stock_actual` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock_minimo` decimal(12,2) NOT NULL DEFAULT 0.00,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_producto` (`id`) ON DELETE SET NULL,
  CONSTRAINT `chk_stock_no_negativo` CHECK (`stock_actual` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,6,'Arroz',NULL,'kg',9.92,0.05,1,'2026-10-03 22:52:22','2026-10-03 23:02:57'),(2,2,'papa',NULL,'kg',2.00,5.00,1,'2026-10-22 22:21:25','2026-10-22 22:22:19'),(3,1,'atun con aceite vegetal','echo de caballa','unidad',15.00,12.00,1,'2026-10-26 06:47:40','2026-11-16 14:23:23');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador','Gestiona usuarios, roles, permisos y supervisa toda la información','2026-09-30 00:19:44','2026-09-30 00:19:44'),(2,'Personal de Beneficencia','Registra y gestiona beneficiarios, ayudas, entregas, donaciones e inventario','2026-09-30 00:19:44','2026-09-30 00:19:44'),(3,'Consulta / Supervisor','Consulta información, reportes y estadísticas sin modificar registros','2026-09-30 00:19:44','2026-09-30 00:19:44');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_ayuda`
--

DROP TABLE IF EXISTS `tipos_ayuda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipos_ayuda` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_ayuda`
--

LOCK TABLES `tipos_ayuda` WRITE;
/*!40000 ALTER TABLE `tipos_ayuda` DISABLE KEYS */;
INSERT INTO `tipos_ayuda` VALUES (1,'Alimentos','Canastas o raciones de alimentos',1,'2026-09-30 00:19:44','2026-09-30 00:19:44'),(2,'Ropa y abrigo','Prendas de vestir y frazadas',1,'2026-09-30 00:19:44','2026-09-30 00:19:44'),(3,'Medicinas','Medicamentos y apoyo en salud',1,'2026-09-30 00:19:44','2026-09-30 00:19:44'),(4,'Útiles escolares','Material educativo',1,'2026-09-30 00:19:44','2026-09-30 00:19:44'),(5,'Apoyo económico','Ayuda monetaria',1,'2026-09-30 00:19:44','2026-09-30 00:19:44');
/*!40000 ALTER TABLE `tipos_ayuda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `rol_id` bigint(20) unsigned NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `rol_id` (`rol_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Liliana','admin','liliquillcamedina@gmail.com','2026-10-26 15:12:34',NULL,'u1_1793021564.png','$2y$12$ef//0XRij7Tsgu2lXtvO0eg1Su/tiKQq5p9rPIGxnnL6/.Q2TBZVC',1,1,'MViNONTILUTAqw0s6KrWhSb8xnf5stxbaqdet6FA3tMGZZjIxTgVV3pXKmGR','2026-09-30 00:19:44','2026-10-27 17:01:18',NULL),(2,'Personal Prueba','personal','liliquillcamedina+prueba@gmail.com','2026-10-27 01:49:28',NULL,NULL,'$2y$12$8X3B08RpjK/AAeVmSTPuvOobbs9VlH1DjSjGTxPXtnjQITFJEN21y',2,1,NULL,'2026-09-30 01:05:06','2026-10-27 01:49:28',NULL),(5,'Guadalupe Retamozo','guadalupe','liliquillcamedinareta@gmail.com',NULL,NULL,NULL,'$2y$12$AR6pTro7nCvyLSsh3d0bR.Qye1sdm1QMb8CVvNfEi7TWJxvRmmnRy',2,0,NULL,'2026-10-27 17:48:21','2026-10-27 17:56:20',NULL),(6,'Guadalupe Retamozo Merino','retamozo','liliquillcamedina+guadalupe@gmail.com','2026-10-27 17:55:18','987654321',NULL,'$2y$12$BjM4Wb0f2nPTI2hySCHTC.y1rwMd.lpQWnhGsCL1mhA7d8txKFS4q',2,1,NULL,'2026-10-27 17:55:01','2026-10-27 17:55:18',NULL),(7,'Anibar Carrion','carrion','liliquillcamedina+carrion@gmail.com','2026-11-12 16:52:03',NULL,NULL,'$2y$12$q59H67EUUfppwAC5EXCfCeNgeD9.BuR1Qku.Pq4UBAxmkD6dMALke',3,1,NULL,'2026-11-12 16:45:34','2026-11-12 16:52:03',NULL),(8,'Ana Quispe Huaman','ana','liliquillcamedina+ana@gmail.com','2026-11-13 16:26:04',NULL,NULL,'$2y$12$RArU3cR8D4wJPi0C6wDoY.4fDOdbjasDRNTJ8xY2s9pyAKz0972tC',2,1,NULL,'2026-11-13 16:25:22','2026-11-13 16:26:04',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'bd_kusi'
--

--
-- Dumping routines for database 'bd_kusi'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-24 12:29:20
