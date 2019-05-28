-- MySQL dump 10.13  Distrib 8.0.16, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: yzkj
-- ------------------------------------------------------
-- Server version	8.0.16

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 SET NAMES utf8mb4 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `admin_menu`
--

LOCK TABLES `admin_menu` WRITE;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;
INSERT INTO `admin_menu` VALUES (1,0,1,'首页','fa-bar-chart','/',NULL,NULL,'2019-05-07 09:23:14'),(2,0,5,'系统管理','fa-tasks',NULL,NULL,NULL,'2019-05-22 10:08:44'),(3,2,6,'管理员','fa-user-secret','auth/users',NULL,NULL,'2019-05-28 15:10:27'),(4,2,7,'角色','fa-tags','auth/roles',NULL,NULL,'2019-05-28 16:17:59'),(5,2,8,'权限','fa-ban','auth/permissions',NULL,NULL,'2019-05-22 10:08:44'),(6,2,9,'菜单','fa-bars','auth/menu',NULL,NULL,'2019-05-22 10:08:44'),(7,2,10,'操作日志','fa-history','auth/logs',NULL,NULL,'2019-05-22 10:08:44'),(8,0,2,'用户管理','fa-users','/users',NULL,'2019-05-07 09:30:15','2019-05-28 15:10:14'),(9,0,3,'商品管理','fa-cubes','/products',NULL,'2019-05-20 04:07:17','2019-05-20 04:07:22'),(10,0,4,'售货机管理','fa-building','/vending_machines',NULL,'2019-05-22 10:08:27','2019-05-22 10:11:35');
/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_permissions`
--

LOCK TABLES `admin_permissions` WRITE;
/*!40000 ALTER TABLE `admin_permissions` DISABLE KEYS */;
INSERT INTO `admin_permissions` VALUES (1,'All permission','*','','*',NULL,NULL),(2,'Dashboard','dashboard','GET','/',NULL,NULL),(3,'Login','auth.login','','/auth/login\r\n/auth/logout',NULL,NULL),(4,'User setting','auth.setting','GET,PUT','/auth/setting',NULL,NULL),(5,'Auth management','auth.management','','/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs',NULL,NULL);
/*!40000 ALTER TABLE `admin_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_menu`
--

LOCK TABLES `admin_role_menu` WRITE;
/*!40000 ALTER TABLE `admin_role_menu` DISABLE KEYS */;
INSERT INTO `admin_role_menu` VALUES (1,2,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_permissions`
--

LOCK TABLES `admin_role_permissions` WRITE;
/*!40000 ALTER TABLE `admin_role_permissions` DISABLE KEYS */;
INSERT INTO `admin_role_permissions` VALUES (1,1,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_users`
--

LOCK TABLES `admin_role_users` WRITE;
/*!40000 ALTER TABLE `admin_role_users` DISABLE KEYS */;
INSERT INTO `admin_role_users` VALUES (1,1,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_roles`
--

LOCK TABLES `admin_roles` WRITE;
/*!40000 ALTER TABLE `admin_roles` DISABLE KEYS */;
INSERT INTO `admin_roles` VALUES (1,'Administrator','administrator','2019-05-07 09:06:49','2019-05-07 09:06:49');
/*!40000 ALTER TABLE `admin_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_user_permissions`
--

LOCK TABLES `admin_user_permissions` WRITE;
/*!40000 ALTER TABLE `admin_user_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'admin','$2y$10$YRfD6vPUway.ZJQwnjzL/erBiNDTUr/P8./Uvbv4otq5Ws/zBqIJ2','管理员','images/role_administor.png','Il4Zq9B2kbH38jLCJd3CseJivA4CRXatICV16C5ryAsRAD39IjZY7LS06kmQ','2019-05-07 09:06:49','2019-05-20 06:21:50');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'红牛','images/红牛.jpg',4.88,5.49,18,60,'2020-09-19',0,0.00,0.00,'2019-05-20 06:31:14','2019-05-28 14:12:23'),(2,'雀巢咖啡','images/2019052109492476766_看图王.jpg',4.00,5.49,12,24,'2020-04-07',0,0.00,0.00,'2019-05-23 03:17:23','2019-05-24 02:51:21');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `product_pes`
--

LOCK TABLES `product_pes` WRITE;
/*!40000 ALTER TABLE `product_pes` DISABLE KEYS */;
INSERT INTO `product_pes` VALUES (1,'2019-03-19','2020-09-19',24,1,'2019-05-20 09:26:40','2019-05-21 09:19:39'),(2,'2019-04-03','2020-10-03',24,1,'2019-05-20 09:26:40','2019-05-20 09:26:40'),(6,'2019-05-22','2020-11-22',12,1,'2019-05-22 08:31:44','2019-05-22 08:31:44'),(7,'2019-04-07','2020-04-07',24,2,'2019-05-23 03:17:23','2019-05-23 03:17:23');
/*!40000 ALTER TABLE `product_pes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `vending_machines`
--

LOCK TABLES `vending_machines` WRITE;
/*!40000 ALTER TABLE `vending_machines` DISABLE KEYS */;
INSERT INTO `vending_machines` VALUES (1,'114766','114766','通宝','123456',1,1,0,'2019-05-22 10:12:11','2019-05-23 01:47:52');
/*!40000 ALTER TABLE `vending_machines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `vending_machine_aisles`
--

LOCK TABLES `vending_machine_aisles` WRITE;
/*!40000 ALTER TABLE `vending_machine_aisles` DISABLE KEYS */;
INSERT INTO `vending_machine_aisles` VALUES (7,1,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(8,2,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(9,3,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(10,4,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(11,5,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(12,6,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(13,7,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(14,8,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(15,9,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(16,10,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(17,11,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(18,12,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(19,13,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(20,14,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(21,15,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(22,19,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(23,20,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(24,21,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(25,22,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(26,23,5,5,0.00,0,1,1,1,'2019-05-24 01:57:01','2019-05-24 02:13:12'),(27,24,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:12'),(28,28,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:12'),(29,29,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:12'),(30,30,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:12'),(31,31,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:12'),(32,32,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(33,33,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(34,34,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(35,35,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(36,36,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(37,37,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(38,38,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(39,39,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(40,40,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(41,41,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(42,42,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(43,43,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(44,44,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(45,45,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(46,46,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(47,47,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(48,48,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(49,49,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(50,50,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(51,51,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(52,52,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13'),(53,53,5,5,0.00,0,1,1,1,'2019-05-24 01:57:02','2019-05-24 02:13:13');
/*!40000 ALTER TABLE `vending_machine_aisles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-05-29  3:23:59
