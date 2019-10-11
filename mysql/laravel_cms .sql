/*
 Navicat Premium Data Transfer

 Source Server         : local
 Source Server Type    : MySQL
 Source Server Version : 50723
 Source Host           : localhost:3306
 Source Schema         : new_cms

 Target Server Type    : MySQL
 Target Server Version : 50723
 File Encoding         : 65001

 Date: 11/10/2019 10:36:42
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin_desktop
-- ----------------------------
DROP TABLE IF EXISTS `admin_desktop`;
CREATE TABLE `admin_desktop` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Название блока',
  `type` enum('graph','panel','table') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Тип блока',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT 'Сортировка',
  `data` json NOT NULL COMMENT 'Дополнительные параметры',
  `site_id` char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '000',
  `user_id` int(11) NOT NULL COMMENT 'Идентификатор пользователя',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=172 COMMENT='Блоки опубликованные на главной странице раздела администрирования';

-- ----------------------------
-- Table structure for banners
-- ----------------------------
DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `depth` varchar(255) DEFAULT NULL,
  `state` tinyint(4) DEFAULT '1',
  `name` varchar(255) DEFAULT NULL,
  `start_published_at` datetime DEFAULT NULL,
  `end_published_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` date DEFAULT NULL,
  `data` longblob,
  `site_id` char(3) DEFAULT '000',
  `view_counter` int(11) unsigned DEFAULT '0',
  `published_at` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_banners` (`parent_id`,`lft`,`rgt`),
  KEY `IDX_banners_site_id` (`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=4096 COMMENT='Баннеры';

-- ----------------------------
-- Table structure for banners_catalog_item
-- ----------------------------
DROP TABLE IF EXISTS `banners_catalog_item`;
CREATE TABLE `banners_catalog_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banner_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `sort` int(11) DEFAULT '100',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_banners_catalog_item_banner_id` (`banner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1112 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=165;

-- ----------------------------
-- Table structure for banners_statistic_views
-- ----------------------------
DROP TABLE IF EXISTS `banners_statistic_views`;
CREATE TABLE `banners_statistic_views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=207 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=24 COMMENT='Статистика просмотров';

-- ----------------------------
-- Table structure for calendar
-- ----------------------------
DROP TABLE IF EXISTS `calendar`;
CREATE TABLE `calendar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `to` varchar(5) DEFAULT NULL,
  `from` varchar(5) DEFAULT NULL,
  `state` tinyint(1) DEFAULT '1',
  `site_id` char(3) DEFAULT '001',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_calendar_site_id` (`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1024;

-- ----------------------------
-- Table structure for calendar_templates
-- ----------------------------
DROP TABLE IF EXISTS `calendar_templates`;
CREATE TABLE `calendar_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `data` blob,
  `site_id` char(3) DEFAULT '001',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `state` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=8192;

-- ----------------------------
-- Table structure for calendar_templates_times
-- ----------------------------
DROP TABLE IF EXISTS `calendar_templates_times`;
CREATE TABLE `calendar_templates_times` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL,
  `from` varchar(10) DEFAULT NULL,
  `to` varchar(10) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_calendar_templates_times_template_id` (`template_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=4096;

-- ----------------------------
-- Table structure for catalog_categories
-- ----------------------------
DROP TABLE IF EXISTS `catalog_categories`;
CREATE TABLE `catalog_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `alias` varchar(200) DEFAULT NULL,
  `state` int(1) DEFAULT '1',
  `sort` int(11) DEFAULT '0',
  `root` int(10) unsigned DEFAULT '0',
  `lft` int(10) unsigned DEFAULT NULL,
  `rgt` int(10) unsigned DEFAULT NULL,
  `depth` smallint(5) unsigned DEFAULT '0',
  `site_id` char(3) DEFAULT '000',
  `parent_id` int(11) DEFAULT '0',
  `data` json DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `counter` int(11) unsigned DEFAULT '0',
  `introtext` text,
  `fulltext` text,
  PRIMARY KEY (`id`),
  KEY `IDX_catalog_categories_site_id` (`site_id`),
  KEY `level` (`depth`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`),
  KEY `root` (`root`)
) ENGINE=InnoDB AUTO_INCREMENT=391 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=819 COMMENT='Кол-во товаров в категории';

-- ----------------------------
-- Table structure for catalog_categories_properties
-- ----------------------------
DROP TABLE IF EXISTS `catalog_categories_properties`;
CREATE TABLE `catalog_categories_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `external_id` char(36) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `grouping_id` int(11) DEFAULT '0',
  `name` varchar(100) NOT NULL,
  `code` varchar(100) DEFAULT NULL,
  `type` varchar(20) DEFAULT 'string',
  `sort` int(11) DEFAULT '100',
  `state` tinyint(1) unsigned DEFAULT '1',
  `req` tinyint(1) unsigned DEFAULT '0',
  `is_search` tinyint(1) unsigned DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `data` longblob,
  `is_system` tinyint(1) DEFAULT '0',
  `is_calculate` tinyint(1) DEFAULT '0' COMMENT 'Используется при расчетах в формулах корректировки цены',
  `site_id` char(3) DEFAULT '000',
  PRIMARY KEY (`id`),
  KEY `IDX_catalog_categories_propert` (`site_id`),
  KEY `IDX_catalog_properties_code` (`code`),
  KEY `IDX_catalog_properties_uid` (`external_id`),
  KEY `FK_catalog_categories_properti` (`category_id`),
  CONSTRAINT `FK_catalog_categories_properti` FOREIGN KEY (`category_id`) REFERENCES `catalog_categories` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5815 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=3120 COMMENT='Свойства категории';

-- ----------------------------
-- Table structure for catalog_categories_properties_grouping
-- ----------------------------
DROP TABLE IF EXISTS `catalog_categories_properties_grouping`;
CREATE TABLE `catalog_categories_properties_grouping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `state` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  PRIMARY KEY (`id`),
  KEY `IDX_catalog_categories_propert` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1365;

-- ----------------------------
-- Table structure for catalog_categories_table
-- ----------------------------
DROP TABLE IF EXISTS `catalog_categories_table`;
CREATE TABLE `catalog_categories_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_tbl_category_table_category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблицы товаров в описаниях категорий - Устаревшее';

-- ----------------------------
-- Table structure for catalog_config
-- ----------------------------
DROP TABLE IF EXISTS `catalog_config`;
CREATE TABLE `catalog_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `value` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `priority` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_catalog_config_alias` (`alias`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=2030 COMMENT='Параметры модуля каталог';

-- ----------------------------
-- Table structure for catalog_delivery
-- ----------------------------
DROP TABLE IF EXISTS `catalog_delivery`;
CREATE TABLE `catalog_delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `sort` mediumint(9) DEFAULT NULL,
  `state` tinyint(4) DEFAULT NULL,
  `data` blob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  PRIMARY KEY (`id`),
  KEY `IDX_catalog_delivery_site_id` (`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Службы доставки';

-- ----------------------------
-- Table structure for catalog_items
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items`;
CREATE TABLE `catalog_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT '0',
  `state` tinyint(1) unsigned DEFAULT '0',
  `sort` int(11) DEFAULT '0' COMMENT 'индекс сортировки по умолчанию',
  `introtext` text,
  `fulltext` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `data` longblob,
  `site_id` char(3) DEFAULT '000',
  PRIMARY KEY (`id`),
  KEY `IDX_catalog_items_alias` (`alias`,`id`),
  KEY `IDX_catalog_items_code` (`code`),
  KEY `IDX_catalog_items_parent_id` (`parent_id`),
  KEY `IDX_catalog_items_published` (`state`),
  KEY `IDX_catalog_items_series_id` (`category_id`),
  KEY `IDX_catalog_items_site_id` (`site_id`),
  FULLTEXT KEY `IDX_catalog_items_title` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1024 COMMENT='Позиции каталога';

-- ----------------------------
-- Table structure for catalog_items_availability
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_availability`;
CREATE TABLE `catalog_items_availability` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `store_id` int(11) DEFAULT NULL,
  `value` mediumint(9) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_catalog_items_availability` (`item_id`,`store_id`),
  KEY `IDX_catalog_items_availability` (`store_id`),
  CONSTRAINT `FK_catalog_items_availability_` FOREIGN KEY (`item_id`) REFERENCES `catalog_items` (`id`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Остатки\\Наличие товаров';

-- ----------------------------
-- Table structure for catalog_items_comments
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_comments`;
CREATE TABLE `catalog_items_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_catalog_items_comments_item` (`item_id`),
  CONSTRAINT `FK_catalog_items_comments_item` FOREIGN KEY (`item_id`) REFERENCES `catalog_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for catalog_items_discount
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_discount`;
CREATE TABLE `catalog_items_discount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `type` enum('fixed','dinamic') DEFAULT NULL,
  `rules` json DEFAULT NULL,
  `check` json DEFAULT NULL,
  `data` json DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `valid_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for catalog_items_documents
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_documents`;
CREATE TABLE `catalog_items_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_id` int(11) DEFAULT '0',
  `store_id` int(11) DEFAULT NULL,
  `value` mediumint(9) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `data` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_catalog_items_documents` (`store_id`),
  KEY `IDX_catalog_items_documents_item_id_store_id` (`store_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Документооборот по товарам';

-- ----------------------------
-- Table structure for catalog_items_documents_item_id
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_documents_item_id`;
CREATE TABLE `catalog_items_documents_item_id` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `document_id` int(11) DEFAULT NULL,
  `store_id` int(11) DEFAULT NULL,
  `value` mediumint(9) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_catalog_items_documents_it` (`item_id`,`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for catalog_items_documents_status
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_documents_status`;
CREATE TABLE `catalog_items_documents_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `data` json DEFAULT NULL,
  `sort` int(11) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `state` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for catalog_items_favorites
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_favorites`;
CREATE TABLE `catalog_items_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `item_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_catalog_items_favorites_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Закладки пользователей';

-- ----------------------------
-- Table structure for catalog_items_history
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_history`;
CREATE TABLE `catalog_items_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `event_type` enum('update_price','update_data','deleted','restored','change_sort','change_sort_sales_amount','change_sort_sales_number','change_ordered','change_availability','add_certificate','delete_certificate','add_image') DEFAULT NULL,
  `old_value` float DEFAULT NULL,
  `new_value` float DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_catalog_items_history_item_id` (`item_id`,`event_type`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=23;

-- ----------------------------
-- Table structure for catalog_items_like
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_like`;
CREATE TABLE `catalog_items_like` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_catalog_items_like` (`item_id`,`user_id`),
  CONSTRAINT `FK_catalog_items_like_item_item` FOREIGN KEY (`item_id`) REFERENCES `catalog_items` (`id`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Положительные оценки от пользователей';

-- ----------------------------
-- Table structure for catalog_items_price
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_price`;
CREATE TABLE `catalog_items_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL COMMENT 'id позиции каталога',
  `store_id` int(11) DEFAULT NULL,
  `price` decimal(19,2) DEFAULT NULL COMMENT 'Цена',
  `currency` char(3) DEFAULT 'RUB' COMMENT 'Базовая валюта товара',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `data` longblob,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_catalog_items_price_item_id` (`item_id`),
  UNIQUE KEY `UK_catalog_items_price_istore_id_item_id` (`item_id`,`store_id`),
  CONSTRAINT `FK_catalog_items_price_item_id` FOREIGN KEY (`item_id`) REFERENCES `catalog_items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1024 COMMENT='Цены на товары';

-- ----------------------------
-- Table structure for catalog_items_properties_html_values
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_properties_html_values`;
CREATE TABLE `catalog_items_properties_html_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `value` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for catalog_items_properties_index
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_properties_index`;
CREATE TABLE `catalog_items_properties_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  `facet_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UIDX_catalog_items_properties_index` (`property_id`,`value`,`item_id`),
  KEY `IDX_catalog_items_properties_i` (`facet_id`),
  KEY `IDX_catalog_items_properties_index_property_id` (`property_id`),
  KEY `FK_catalog_items_properties_item` (`item_id`),
  CONSTRAINT `FK_catalog_items_properties_item` FOREIGN KEY (`item_id`) REFERENCES `catalog_items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=189 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=88 COMMENT='Поисковый индекс по свойствам позиций каталога (устарело и временно не используется)';

-- ----------------------------
-- Table structure for catalog_items_properties_list_values
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_properties_list_values`;
CREATE TABLE `catalog_items_properties_list_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_catalog_items_properties_l` (`property_id`),
  CONSTRAINT `FK_catalog_items_properties_list_values_property_id` FOREIGN KEY (`property_id`) REFERENCES `catalog_categories_properties` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=468 COMMENT='Варианты значений свойст с типом - список значений';

-- ----------------------------
-- Table structure for catalog_items_properties_string_values
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_properties_string_values`;
CREATE TABLE `catalog_items_properties_string_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_item_id` (`item_id`),
  KEY `FK_property_id` (`property_id`),
  CONSTRAINT `FK_item_id` FOREIGN KEY (`item_id`) REFERENCES `catalog_items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `FK_property_id` FOREIGN KEY (`property_id`) REFERENCES `catalog_categories_properties` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=4096;

-- ----------------------------
-- Table structure for catalog_items_properties_values
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_properties_values`;
CREATE TABLE `catalog_items_properties_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `property_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `value` varchar(255) NOT NULL,
  `sort` int(11) DEFAULT '100',
  `data` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_catalog_items_properties_values_value` (`value`),
  KEY `FK_catalog_items_properties_property_id` (`property_id`),
  KEY `FK_catalog_items_properties_va` (`item_id`),
  CONSTRAINT `FK_catalog_items_properties_property_id` FOREIGN KEY (`property_id`) REFERENCES `catalog_categories_properties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_catalog_items_properties_va` FOREIGN KEY (`item_id`) REFERENCES `catalog_items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=402 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=80;

-- ----------------------------
-- Table structure for catalog_items_related
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_related`;
CREATE TABLE `catalog_items_related` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `related_id` int(11) NOT NULL,
  `sort` mediumint(9) DEFAULT '100',
  `state` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `type` enum('M','A','N') DEFAULT 'N' COMMENT 'как был создан элемент - A - в автоматическом режиме, M - вручную через раздел администрирования, N - хз как, по умолчанию)',
  `compare_manufacturer` tinyint(1) unsigned DEFAULT '1' COMMENT 'сравнивать производителя при автоматическом добавление - по умолчанию: ДА',
  `compare_type` tinyint(1) unsigned DEFAULT '1' COMMENT 'сравнивать тип нанесения - по умолчанию: ДА',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `related_id` (`related_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Сопутствующие товары, автозаполнение таблицы по алгоритму...';

-- ----------------------------
-- Table structure for catalog_items_review
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_review`;
CREATE TABLE `catalog_items_review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `text` text,
  `description` tinyint(2) DEFAULT NULL,
  `communication` tinyint(2) DEFAULT NULL,
  `purity` tinyint(2) DEFAULT NULL,
  `position` tinyint(2) DEFAULT NULL,
  `settling` tinyint(2) DEFAULT NULL,
  `price_quality` tinyint(2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `user_name` varchar(45) DEFAULT NULL,
  `lang` char(2) DEFAULT NULL,
  `approve` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_catalog_items_review_parent_id` (`item_id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for catalog_items_sku
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_sku`;
CREATE TABLE `catalog_items_sku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  `sku` varchar(100) NOT NULL COMMENT 'артикул',
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT '0',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(11) DEFAULT '0' COMMENT 'индекс сортировки по умолчанию',
  `introtext` text,
  `fulltext` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `data` longblob,
  `site_id` char(3) DEFAULT '000',
  PRIMARY KEY (`id`),
  KEY `IDX_catalog_items_alias` (`alias`),
  KEY `IDX_catalog_items_code` (`code`),
  KEY `IDX_catalog_items_published` (`state`),
  KEY `IDX_catalog_items_series_id` (`category_id`),
  KEY `IDX_catalog_items_site_id` (`site_id`),
  KEY `IDX_catalog_items_sku` (`sku`),
  FULLTEXT KEY `IDX_catalog_items_title` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Позиции каталога: Торговые предложения - Устарела';

-- ----------------------------
-- Table structure for catalog_items_statistic_published
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_statistic_published`;
CREATE TABLE `catalog_items_statistic_published` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1170 COMMENT='Общая cтатистика публикации объявлений';

-- ----------------------------
-- Table structure for catalog_items_statistic_unpublished
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_statistic_unpublished`;
CREATE TABLE `catalog_items_statistic_unpublished` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1365 COMMENT='Общая cтатистика отклоненных объявлений';

-- ----------------------------
-- Table structure for catalog_items_statistic_views
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_statistic_views`;
CREATE TABLE `catalog_items_statistic_views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=315 COMMENT='Общая cтатистика просмотров по дням';

-- ----------------------------
-- Table structure for catalog_items_view
-- ----------------------------
DROP TABLE IF EXISTS `catalog_items_view`;
CREATE TABLE `catalog_items_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `count` mediumint(9) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_catalog_items_view_item_id` (`item_id`),
  CONSTRAINT `FK_catalog_items_view_item_id` FOREIGN KEY (`item_id`) REFERENCES `catalog_items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=512 COMMENT='кол-во просмотров позиций каталога';

-- ----------------------------
-- Table structure for catalog_payments
-- ----------------------------
DROP TABLE IF EXISTS `catalog_payments`;
CREATE TABLE `catalog_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `data` longblob,
  `state` tinyint(4) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  PRIMARY KEY (`id`),
  KEY `IDX_catalog_payments_site_id` (`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=8192 COMMENT='Доступные системы оплаты';

-- ----------------------------
-- Table structure for catalog_store
-- ----------------------------
DROP TABLE IF EXISTS `catalog_store`;
CREATE TABLE `catalog_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `data` blob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `state` tinyint(4) unsigned DEFAULT '0',
  `introtext` text,
  `fulltext` text,
  PRIMARY KEY (`id`),
  KEY `IDX_catalog_store_site_id` (`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=5461 COMMENT='Склады';

-- ----------------------------
-- Table structure for config_domain
-- ----------------------------
DROP TABLE IF EXISTS `config_domain`;
CREATE TABLE `config_domain` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Название',
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'HTTP адрес домена в формате http://{name}.{com}',
  `code` char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '000' COMMENT 'Дополнительные параметры',
  `data` json DEFAULT NULL COMMENT 'Дополнительные параметры',
  `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Состояние',
  `site_id` char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '000',
  `lang` char(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=86 COMMENT='Доступные домены';

-- ----------------------------
-- Table structure for config_help
-- ----------------------------
DROP TABLE IF EXISTS `config_help`;
CREATE TABLE `config_help` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `text` text,
  `state` tinyint(4) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `data` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `IDX_config_help_text` (`text`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=3715;

-- ----------------------------
-- Table structure for config_translate
-- ----------------------------
DROP TABLE IF EXISTS `config_translate`;
CREATE TABLE `config_translate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` char(3) DEFAULT '000',
  `code` varchar(255) DEFAULT NULL,
  `key` text,
  `value` text,
  `state` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_config_translate` (`code`),
  KEY `IDX_config_translate_site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=63 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=156;

-- ----------------------------
-- Table structure for content
-- ----------------------------
DROP TABLE IF EXISTS `content`;
CREATE TABLE `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `favorites` tinyint(1) DEFAULT '0',
  `view_counter` int(11) DEFAULT '0',
  `sort` mediumint(9) DEFAULT '100',
  `introtext` text,
  `fulltext` text,
  `state` int(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `data` longblob,
  `site_id` char(3) DEFAULT '000',
  `count_comment` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_content_alias` (`alias`),
  KEY `IDX_content_category_id` (`category_id`),
  KEY `IDX_content_site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=684 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=14520;

-- ----------------------------
-- Table structure for content_canonical
-- ----------------------------
DROP TABLE IF EXISTS `content_canonical`;
CREATE TABLE `content_canonical` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_index` tinyint(1) DEFAULT '0',
  `item_id` int(11) DEFAULT NULL,
  `type` tinyint(4) unsigned DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_content_canonical` (`item_id`,`type`,`link`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=862;

-- ----------------------------
-- Table structure for content_canonical_check_result
-- ----------------------------
DROP TABLE IF EXISTS `content_canonical_check_result`;
CREATE TABLE `content_canonical_check_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `code` char(3) DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_content_canonical_check_r2` (`site_id`),
  KEY `IDX_content_canonical_check_re` (`item_id`,`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=409;

-- ----------------------------
-- Table structure for content_category
-- ----------------------------
DROP TABLE IF EXISTS `content_category`;
CREATE TABLE `content_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `data` longblob,
  `state` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `depth` mediumint(9) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_content_category_alias` (`alias`,`site_id`),
  KEY `IDX_content_category_site_id` (`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=4096;

-- ----------------------------
-- Table structure for content_comments
-- ----------------------------
DROP TABLE IF EXISTS `content_comments`;
CREATE TABLE `content_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `depth` varchar(255) DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `state` tinyint(4) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `text` text,
  `data` json DEFAULT NULL,
  `parent_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=3276;

-- ----------------------------
-- Table structure for content_config
-- ----------------------------
DROP TABLE IF EXISTS `content_config`;
CREATE TABLE `content_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `value` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `priority` tinyint(4) DEFAULT '0',
  `state` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_content_config_alias` (`alias`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=2295;

-- ----------------------------
-- Table structure for content_statistic_views
-- ----------------------------
DROP TABLE IF EXISTS `content_statistic_views`;
CREATE TABLE `content_statistic_views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=237 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=83 COMMENT='Статистика просмотров материалов';

-- ----------------------------
-- Table structure for content_tag
-- ----------------------------
DROP TABLE IF EXISTS `content_tag`;
CREATE TABLE `content_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varchar(255) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=248;

-- ----------------------------
-- Table structure for conversations
-- ----------------------------
DROP TABLE IF EXISTS `conversations`;
CREATE TABLE `conversations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_one` int(11) NOT NULL,
  `user_two` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for currency
-- ----------------------------
DROP TABLE IF EXISTS `currency`;
CREATE TABLE `currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(3) DEFAULT NULL,
  `value` double DEFAULT NULL,
  `symbol_right` varchar(10) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=5461;

-- ----------------------------
-- Table structure for data_source
-- ----------------------------
DROP TABLE IF EXISTS `data_source`;
CREATE TABLE `data_source` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `data` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `state` tinyint(1) DEFAULT '0',
  `site_id` char(3) DEFAULT '000',
  `sort` int(11) DEFAULT '100',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_data_source_alias` (`alias`),
  KEY `IDX_data_source_site_id` (`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=2340 COMMENT='Список доступных справочников, не реализует хранение данных';

-- ----------------------------
-- Table structure for data_source_booking_tour
-- ----------------------------
DROP TABLE IF EXISTS `data_source_booking_tour`;
CREATE TABLE `data_source_booking_tour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `state` int(1) DEFAULT '1',
  `data` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `sort` int(11) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Справочники: Заказывая тур';

-- ----------------------------
-- Table structure for data_source_brands_mobile
-- ----------------------------
DROP TABLE IF EXISTS `data_source_brands_mobile`;
CREATE TABLE `data_source_brands_mobile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `state` int(1) DEFAULT '1',
  `data` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `sort` int(11) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `IDX_data_source_brands_mobile_name` (`name`),
  KEY `IDX_data_source_manufacturer_site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=347 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=53 COMMENT='Справочники: производители мобильных телефонв';

-- ----------------------------
-- Table structure for data_source_colors
-- ----------------------------
DROP TABLE IF EXISTS `data_source_colors`;
CREATE TABLE `data_source_colors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `state` int(1) DEFAULT '1',
  `data` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `sort` int(11) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `IDX_data_source_colors_site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=225 COMMENT='Справочники: цвета';

-- ----------------------------
-- Table structure for data_source_cpu
-- ----------------------------
DROP TABLE IF EXISTS `data_source_cpu`;
CREATE TABLE `data_source_cpu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `state` int(1) DEFAULT '1',
  `data` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `sort` int(11) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `IDX_data_source_cpu_site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=213 COMMENT='Справочники: CPU';

-- ----------------------------
-- Table structure for data_source_gallery
-- ----------------------------
DROP TABLE IF EXISTS `data_source_gallery`;
CREATE TABLE `data_source_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `state` int(1) DEFAULT '1',
  `data` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `sort` int(11) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Справочники: Заказывая тур';

-- ----------------------------
-- Table structure for data_source_gpu
-- ----------------------------
DROP TABLE IF EXISTS `data_source_gpu`;
CREATE TABLE `data_source_gpu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `state` int(1) DEFAULT '1',
  `data` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `sort` int(11) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `IDX_data_source_cpu_site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=132 COMMENT='Справочники: GPU';

-- ----------------------------
-- Table structure for data_source_manufacturer
-- ----------------------------
DROP TABLE IF EXISTS `data_source_manufacturer`;
CREATE TABLE `data_source_manufacturer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `state` int(1) DEFAULT '1',
  `data` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `sort` int(11) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `IDX_data_source_manufacturer_s` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=38840 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=60 COMMENT='Справочники: глобальный список производителей';

-- ----------------------------
-- Table structure for data_source_os
-- ----------------------------
DROP TABLE IF EXISTS `data_source_os`;
CREATE TABLE `data_source_os` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `state` int(1) DEFAULT '1',
  `data` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `sort` int(11) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `IDX_data_source_manufacturer_s` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for data_source_sezon
-- ----------------------------
DROP TABLE IF EXISTS `data_source_sezon`;
CREATE TABLE `data_source_sezon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `state` int(1) DEFAULT '1',
  `data` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `sort` int(11) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `IDX_data_source_colors_site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Справочники: сезоны';

-- ----------------------------
-- Table structure for data_source_tour_description
-- ----------------------------
DROP TABLE IF EXISTS `data_source_tour_description`;
CREATE TABLE `data_source_tour_description` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `state` int(1) DEFAULT '1',
  `data` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `sort` int(11) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Справочники: сезоны';

-- ----------------------------
-- Table structure for forms
-- ----------------------------
DROP TABLE IF EXISTS `forms`;
CREATE TABLE `forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` char(3) NOT NULL DEFAULT '000',
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `data` longblob,
  `state` tinyint(4) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_site_id` (`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=8192 COMMENT='формы доступные в проекте, данные таблицы не определяют список форм а хранят их параметры';

-- ----------------------------
-- Table structure for forms_config
-- ----------------------------
DROP TABLE IF EXISTS `forms_config`;
CREATE TABLE `forms_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `value` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `priority` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_content_config_alias` (`alias`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=712;

-- ----------------------------
-- Table structure for forms_questions
-- ----------------------------
DROP TABLE IF EXISTS `forms_questions`;
CREATE TABLE `forms_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `alias` varchar(50) DEFAULT NULL,
  `data` json DEFAULT NULL,
  `sort` mediumint(9) DEFAULT '100',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `state` tinyint(1) DEFAULT '1',
  `site_id` char(3) DEFAULT '000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=2730;

-- ----------------------------
-- Table structure for forms_result
-- ----------------------------
DROP TABLE IF EXISTS `forms_result`;
CREATE TABLE `forms_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT '0',
  `site_id` char(3) DEFAULT '000',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `read` tinyint(1) DEFAULT '0',
  `data` longblob,
  PRIMARY KEY (`id`),
  KEY `IDX_forms_result_form_id` (`form_id`),
  KEY `IDX_forms_result_site_id` (`site_id`),
  KEY `IDX_forms_result_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Результаты заполнения формы';

-- ----------------------------
-- Table structure for forms_statistic_submit
-- ----------------------------
DROP TABLE IF EXISTS `forms_statistic_submit`;
CREATE TABLE `forms_statistic_submit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=2340;

-- ----------------------------
-- Table structure for location_place
-- ----------------------------
DROP TABLE IF EXISTS `location_place`;
CREATE TABLE `location_place` (
  `id` bigint(20) NOT NULL COMMENT 'ID населенного пункта.',
  `fias_aoguid` varchar(36) NOT NULL COMMENT 'ФИАС GUID',
  `custom` smallint(6) DEFAULT '0' COMMENT 'Пометка о том, что запись создана вручную. 0 - запись из базы ФИАС, 1 - запись созданная нашим модулем.',
  `name` varchar(128) NOT NULL COMMENT 'Название населенного пункта.',
  `level` smallint(6) NOT NULL COMMENT 'Уровень адресного объекта',
  `type` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Тип населенного пункта: 1 - регион/край, 2 - район, 3 - город, 4 - населенный пункт, 5 - улица, 6 - дом',
  `place_type_name_id` int(11) NOT NULL COMMENT 'Id названия типа объекта: город, село, пгт и т.п.',
  `parent_place_id` bigint(20) DEFAULT NULL COMMENT 'Родительский элемент',
  `region_place_id` bigint(20) DEFAULT NULL COMMENT 'Родительский регион элемента',
  `postal_code` int(11) DEFAULT NULL COMMENT 'Почтовый индекс',
  `is_region_center` smallint(6) DEFAULT '0' COMMENT 'Населенный пункт является центром региона',
  `is_center` smallint(6) DEFAULT '0' COMMENT 'Населенный пункт является центром региона или района',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `state` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `bazac_rf_place_fias_aoguid_key` (`fias_aoguid`),
  KEY `bazac_rf_place_custom_idx` (`custom`),
  KEY `bazac_rf_place_fias_aoguid_idx` (`fias_aoguid`),
  KEY `bazac_rf_place_id_idx` (`id`),
  KEY `bazac_rf_place_level_idx` (`level`),
  KEY `bazac_rf_place_parent_center_idx` (`parent_place_id`,`is_center`),
  KEY `bazac_rf_place_parent_idx` (`parent_place_id`),
  KEY `bazac_rf_place_parent_name_idx` (`parent_place_id`,`name`),
  KEY `bazac_rf_place_parent_postal_idx` (`parent_place_id`,`postal_code`),
  KEY `bazac_rf_place_parent_type_idx` (`parent_place_id`,`type`),
  KEY `bazac_rf_place_postal_idx` (`postal_code`),
  KEY `bazac_rf_place_region_center_idx` (`region_place_id`,`is_region_center`),
  KEY `bazac_rf_place_type_idx` (`type`),
  KEY `bazac_rf_place_type_name_idx` (`place_type_name_id`),
  KEY `bazac_rf_place_type_name_postal_idx` (`place_type_name_id`,`postal_code`),
  KEY `bazac_rf_place_type_postal_idx` (`type`,`postal_code`),
  KEY `IDX_bazac_rf_place_name` (`name`),
  KEY `IDX_location_place_state` (`state`),
  KEY `place_type_name_id` (`place_type_name_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Справочник данных о населенных пунктах России';

-- ----------------------------
-- Table structure for location_place_coordinates
-- ----------------------------
DROP TABLE IF EXISTS `location_place_coordinates`;
CREATE TABLE `location_place_coordinates` (
  `place_coordinates_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID записи.',
  `place_id` bigint(20) NOT NULL COMMENT 'ID населенного пункта',
  `latitude` varchar(15) NOT NULL COMMENT 'Широта',
  `longitude` varchar(15) NOT NULL COMMENT 'Долгота',
  `ym_geocode` varchar(1024) NOT NULL COMMENT 'Атрибут GeocoderMetaData->text объекта, который вернул API Яндекс.Карт',
  PRIMARY KEY (`place_coordinates_id`),
  UNIQUE KEY `bazac_rf_place_coordinates_key` (`place_id`),
  KEY `bazac_rf_place_coordinates_place_id_idx` (`place_id`),
  KEY `place_id` (`place_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for location_place_type_name
-- ----------------------------
DROP TABLE IF EXISTS `location_place_type_name`;
CREATE TABLE `location_place_type_name` (
  `id` int(11) NOT NULL COMMENT 'ID записи.',
  `level` smallint(6) NOT NULL COMMENT 'Уровень типа н.п. согласно ФИАС',
  `code` smallint(6) NOT NULL COMMENT 'Код типа н.п. согласно ФИАС',
  `name` varchar(32) NOT NULL COMMENT 'Сокращенное название типа н.п.',
  `full_name` varchar(128) DEFAULT NULL COMMENT 'Полное название типа н.п.',
  `alt_name` varchar(32) DEFAULT NULL COMMENT 'Альтернативный вариант сокращенного названия типа н.п.',
  `after_place_name` smallint(6) NOT NULL DEFAULT '0' COMMENT 'В названии н.п. указывать тип после имени н.п.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `bazac_rf_place_place_type_name_code_key` (`code`),
  KEY `bazac_rf_place_place_type_name_id_idx` (`id`),
  KEY `bazac_rf_place_place_type_name_name_idx` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Справочник данных о почтовых индексах городов и населенных п';

-- ----------------------------
-- Table structure for location_postal_codes
-- ----------------------------
DROP TABLE IF EXISTS `location_postal_codes`;
CREATE TABLE `location_postal_codes` (
  `place_postal_code_id` int(11) DEFAULT NULL,
  `postal_code` int(11) DEFAULT NULL,
  `place_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for location_region
-- ----------------------------
DROP TABLE IF EXISTS `location_region`;
CREATE TABLE `location_region` (
  `place_id` int(11) DEFAULT NULL,
  `fias_aoguid` varchar(50) DEFAULT NULL,
  `custom` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `place_type_name_id` int(11) DEFAULT NULL,
  `parent_place_id` varchar(255) DEFAULT NULL,
  `region_place_id` varchar(255) DEFAULT NULL,
  `postal_code` int(11) DEFAULT NULL,
  `is_region_center` int(11) DEFAULT NULL,
  `is_center` int(11) DEFAULT NULL,
  KEY `IDX_location_region_parent_pla` (`parent_place_id`),
  KEY `IDX_location_region_place_id` (`place_id`),
  KEY `IDX_location_region_place_type` (`place_type_name_id`),
  KEY `IDX_location_region_region_pla` (`region_place_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for location_streets
-- ----------------------------
DROP TABLE IF EXISTS `location_streets`;
CREATE TABLE `location_streets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fias_aoguid` varchar(50) DEFAULT NULL,
  `custom` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `place_type_name_id` int(11) DEFAULT NULL,
  `parent_place_id` int(11) DEFAULT NULL,
  `region_place_id` int(11) DEFAULT NULL,
  `postal_code` int(11) DEFAULT NULL,
  `is_region_center` int(11) DEFAULT NULL,
  `is_center` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_location_streets_parent_pl` (`parent_place_id`),
  KEY `IDX_location_streets_place_typ` (`place_type_name_id`),
  KEY `IDX_location_streets_region_pl` (`region_place_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for media_config
-- ----------------------------
DROP TABLE IF EXISTS `media_config`;
CREATE TABLE `media_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `value` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `priority` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_media_config_alias` (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for media_gallery_item
-- ----------------------------
DROP TABLE IF EXISTS `media_gallery_item`;
CREATE TABLE `media_gallery_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `parent_type` tinyint(4) unsigned NOT NULL,
  `sort` tinyint(4) unsigned DEFAULT '100',
  `hash` char(32) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `data` longblob,
  `state` tinyint(1) DEFAULT '1',
  `site_id` char(3) DEFAULT '000',
  PRIMARY KEY (`id`),
  KEY `IDX_media_gallery_item` (`parent_id`,`parent_type`,`site_id`),
  KEY `IDX_media_gallery_item_hash` (`hash`),
  KEY `IDX_media_gallery_item_parent_id` (`parent_id`),
  KEY `IDX_media_gallery_item_parent_id_parent_type` (`parent_id`,`parent_type`),
  KEY `IDX_media_gallery_item_parent_type` (`parent_type`),
  KEY `IDX_media_gallery_item_site_id` (`site_id`),
  KEY `IDX_media_gallery_item_state` (`state`),
  KEY `IDX_media_gallery_item_user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=122 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=772 COMMENT='Все что загружается на сайт....';

-- ----------------------------
-- Table structure for media_gallery_item_history
-- ----------------------------
DROP TABLE IF EXISTS `media_gallery_item_history`;
CREATE TABLE `media_gallery_item_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `type` tinyint(4) unsigned DEFAULT NULL,
  `data` blob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='История изменения файлов';

-- ----------------------------
-- Table structure for menus
-- ----------------------------
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `site_id` char(3) COLLATE utf8_unicode_ci DEFAULT '000',
  `depth` int(11) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `route` text COLLATE utf8_unicode_ci,
  `alias` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` tinyint(1) DEFAULT '1',
  `deleted_at` datetime DEFAULT NULL,
  `data` json DEFAULT NULL,
  `stat_success` int(11) unsigned DEFAULT '0',
  `stat_redirect` int(11) unsigned DEFAULT '0',
  `stat_error` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_menus_alias` (`alias`),
  KEY `menus_lft_index` (`lft`),
  KEY `menus_parent_id_index` (`parent_id`),
  KEY `menus_rgt_index` (`rgt`)
) ENGINE=InnoDB AUTO_INCREMENT=289 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=2925 COMMENT='навигация по сайту';

-- ----------------------------
-- Table structure for menus_filters_properties_html_values
-- ----------------------------
DROP TABLE IF EXISTS `menus_filters_properties_html_values`;
CREATE TABLE `menus_filters_properties_html_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_menus_filter_properties_h2` (`property_id`),
  KEY `IDX_menus_filter_properties_ht` (`item_id`),
  CONSTRAINT `FK_menus_filter_properties_html_item_id` FOREIGN KEY (`item_id`) REFERENCES `menus` (`id`) ON DELETE NO ACTION,
  CONSTRAINT `FK_menus_filter_properties_html_property_id` FOREIGN KEY (`property_id`) REFERENCES `catalog_categories_properties` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for menus_filters_properties_string_values
-- ----------------------------
DROP TABLE IF EXISTS `menus_filters_properties_string_values`;
CREATE TABLE `menus_filters_properties_string_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_menus_items_properties_sting_values_property_id` (`property_id`),
  KEY `IDX_menus_items_properties_string_values_item_id` (`item_id`),
  CONSTRAINT `FK_menus_items_properties_striing_values_item_id` FOREIGN KEY (`item_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `FK_menus_items_properties_string_values_property_id` FOREIGN KEY (`property_id`) REFERENCES `catalog_categories_properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for menus_filters_properties_values
-- ----------------------------
DROP TABLE IF EXISTS `menus_filters_properties_values`;
CREATE TABLE `menus_filters_properties_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `property_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `value` varchar(255) NOT NULL,
  `sort` int(11) DEFAULT '100',
  `data` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_menus_filters_properties_2` (`property_id`),
  KEY `IDX_menus_filters_properties_v` (`item_id`),
  CONSTRAINT `FK_menus_filters_properties_values_item_id` FOREIGN KEY (`item_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_menus_filters_properties_values_property_id` FOREIGN KEY (`property_id`) REFERENCES `catalog_categories_properties` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for menus_router_check_result
-- ----------------------------
DROP TABLE IF EXISTS `menus_router_check_result`;
CREATE TABLE `menus_router_check_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `code` char(3) DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_menus_router_check_result` (`item_id`,`site_id`),
  KEY `IDX_menus_router_check_result_` (`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=496;

-- ----------------------------
-- Table structure for menus_sitemap
-- ----------------------------
DROP TABLE IF EXISTS `menus_sitemap`;
CREATE TABLE `menus_sitemap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` char(3) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `priority` smallint(6) DEFAULT NULL,
  `changefreq` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `state` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=3276;

-- ----------------------------
-- Table structure for menus_statistic
-- ----------------------------
DROP TABLE IF EXISTS `menus_statistic`;
CREATE TABLE `menus_statistic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `success` mediumint(9) unsigned DEFAULT '0',
  `errors` mediumint(9) unsigned DEFAULT '0',
  `redirect` mediumint(9) unsigned DEFAULT '0',
  `site_id` char(3) DEFAULT '000',
  PRIMARY KEY (`id`),
  KEY `IDX_menus_statistic_site_id` (`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=281 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=64;

-- ----------------------------
-- Table structure for messages
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_seen` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_from_sender` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_from_receiver` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=8192;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=8192;

-- ----------------------------
-- Table structure for modules
-- ----------------------------
DROP TABLE IF EXISTS `modules`;
CREATE TABLE `modules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Название модуля',
  `data` json NOT NULL COMMENT 'Дополнительные параметры',
  `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Состояние',
  `version` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Версия модуля',
  `priority` int(11) DEFAULT NULL COMMENT 'Приоритет отображения в меню',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_modules_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=5461 COMMENT='Установленные модули';

-- ----------------------------
-- Table structure for order
-- ----------------------------
DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `data` longblob,
  `status` tinyint(4) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  PRIMARY KEY (`id`),
  KEY `IDX_order_site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for order_items
-- ----------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `catalog_item_id` int(11) DEFAULT NULL,
  `price` decimal(19,2) DEFAULT NULL,
  `quantity` decimal(19,2) DEFAULT NULL,
  `data` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for payment
-- ----------------------------
DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `status` tinyint(4) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `sum` float(19,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Автоматизированные платежи созданные при оплате заказов';

-- ----------------------------
-- Table structure for permission_role
-- ----------------------------
DROP TABLE IF EXISTS `permission_role`;
CREATE TABLE `permission_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_role_permission_id_index` (`permission_id`),
  KEY `permission_role_role_id_index` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1632 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=144;

-- ----------------------------
-- Table structure for permission_user
-- ----------------------------
DROP TABLE IF EXISTS `permission_user`;
CREATE TABLE `permission_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_user_permission_id_index` (`permission_id`),
  KEY `permission_user_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inherit_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` blob NOT NULL,
  `description` tinyblob,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `site_id` char(3) COLLATE utf8_unicode_ci DEFAULT '000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_permissions_name` (`name`),
  KEY `IDX_permissions_site_id` (`site_id`),
  KEY `permissions_inherit_id_foreign` (`inherit_id`),
  CONSTRAINT `permissions_inherit_id_foreign` FOREIGN KEY (`inherit_id`) REFERENCES `permissions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1286 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=574 COMMENT='Разрешения ACL';

-- ----------------------------
-- Table structure for role_user
-- ----------------------------
DROP TABLE IF EXISTS `role_user`;
CREATE TABLE `role_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_user_role_id_index` (`role_id`),
  KEY `role_user_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=2730;

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `site_id` char(3) COLLATE utf8_unicode_ci DEFAULT '001',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`,`site_id`),
  UNIQUE KEY `roles_slug_unique` (`slug`,`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=1365;

-- ----------------------------
-- Table structure for search_index
-- ----------------------------
DROP TABLE IF EXISTS `search_index`;
CREATE TABLE `search_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `type` enum('content','catalog') DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_search_index` (`item_id`,`type`),
  KEY `IDX_search_index_type` (`type`),
  FULLTEXT KEY `IDX_search_index_text` (`text`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=2860;

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8_unicode_ci,
  `payload` text COLLATE utf8_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  UNIQUE KEY `sessions_id_unique` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=4587 COMMENT='Сессии';

-- ----------------------------
-- Table structure for system_emails
-- ----------------------------
DROP TABLE IF EXISTS `system_emails`;
CREATE TABLE `system_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(45) DEFAULT NULL,
  `text` text,
  `data` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `state` tinyint(4) DEFAULT '0',
  `site_id` char(3) DEFAULT '000',
  PRIMARY KEY (`id`),
  KEY `IDX_system_email_name` (`name`,`alias`),
  KEY `IDX_system_emails_alias` (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=3276;

-- ----------------------------
-- Table structure for system_error_log
-- ----------------------------
DROP TABLE IF EXISTS `system_error_log`;
CREATE TABLE `system_error_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data` json NOT NULL COMMENT 'Дополнительные параметры',
  `site_id` char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '000',
  `user_id` int(11) NOT NULL COMMENT 'Идентификатор пользователя',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=335 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=343 COMMENT='Ошибки зафиксированные скриптом';

-- ----------------------------
-- Table structure for system_media_store
-- ----------------------------
DROP TABLE IF EXISTS `system_media_store`;
CREATE TABLE `system_media_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `media_id` int(11) DEFAULT NULL,
  `model_id` int(11) DEFAULT NULL,
  `sort` int(11) DEFAULT '100',
  `data` json DEFAULT NULL,
  `hash` char(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_system_media_store` (`item_id`,`model_id`,`media_id`),
  KEY `IDX_system_media_store2` (`item_id`,`model_id`,`hash`)
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=963 COMMENT='Привязка файлов к объектам';

-- ----------------------------
-- Table structure for system_messages
-- ----------------------------
DROP TABLE IF EXISTS `system_messages`;
CREATE TABLE `system_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(45) DEFAULT NULL,
  `text` text,
  `data` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `state` tinyint(4) DEFAULT '0',
  `site_id` char(3) DEFAULT '000',
  PRIMARY KEY (`id`),
  KEY `IDX_system_email_name` (`name`,`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for system_notifications
-- ----------------------------
DROP TABLE IF EXISTS `system_notifications`;
CREATE TABLE `system_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `data` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `read` tinyint(1) DEFAULT '0',
  `type` mediumint(9) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=527 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=682 COMMENT='Сообщения от cms';

-- ----------------------------
-- Table structure for system_properties
-- ----------------------------
DROP TABLE IF EXISTS `system_properties`;
CREATE TABLE `system_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `model` int(11) DEFAULT NULL COMMENT 'HEX строки название модели переведенный в INT ',
  `sort` int(11) DEFAULT '100',
  `value` varchar(255) DEFAULT NULL,
  `data` json DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_system_properties_model` (`model`)
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=420;

-- ----------------------------
-- Table structure for system_properties_select_values
-- ----------------------------
DROP TABLE IF EXISTS `system_properties_select_values`;
CREATE TABLE `system_properties_select_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `alias` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_system_properties_select_va` (`property_id`),
  CONSTRAINT `FK_system_properties_select_va` FOREIGN KEY (`property_id`) REFERENCES `system_properties` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8 COMMENT='Варианты значений для параметров с типом - список';

-- ----------------------------
-- Table structure for system_properties_store
-- ----------------------------
DROP TABLE IF EXISTS `system_properties_store`;
CREATE TABLE `system_properties_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) DEFAULT NULL,
  `model_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT '0',
  `value` varchar(255) DEFAULT NULL,
  `value_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_system_properties_store` (`property_id`,`model_id`,`item_id`),
  KEY `IDX_system_properties_store_model_id` (`model_id`,`item_id`),
  CONSTRAINT `FK_system_properties_store_pro` FOREIGN KEY (`property_id`) REFERENCES `system_properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for system_service
-- ----------------------------
DROP TABLE IF EXISTS `system_service`;
CREATE TABLE `system_service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `introtext` text,
  `fulltext` text,
  `data` json DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_system_service_alias` (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Доступные оплачиваемые сервисы\\услуги и т.д. предоставляющие или запрещающие(лимитирующие) доступ к функционалу скрипта';

-- ----------------------------
-- Table structure for system_site_modules
-- ----------------------------
DROP TABLE IF EXISTS `system_site_modules`;
CREATE TABLE `system_site_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `data` longblob,
  `view` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `state` tinyint(1) DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_modules_name` (`name`,`site_id`),
  KEY `IDX_site_modules_site_id` (`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=3855;

-- ----------------------------
-- Table structure for system_table
-- ----------------------------
DROP TABLE IF EXISTS `system_table`;
CREATE TABLE `system_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `data` json DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1024;

-- ----------------------------
-- Table structure for system_table_filter
-- ----------------------------
DROP TABLE IF EXISTS `system_table_filter`;
CREATE TABLE `system_table_filter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_id` int(11) DEFAULT NULL,
  `data` json DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_table_filter_table_id2` (`table_id`),
  CONSTRAINT `FK_table_filter_table_id2` FOREIGN KEY (`table_id`) REFERENCES `system_table` (`id`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(80) COLLATE utf8_unicode_ci NOT NULL COMMENT 'email',
  `login` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `orders` int(11) NOT NULL DEFAULT '0' COMMENT 'КОл-во заказов',
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Пароль',
  `remember_token` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` enum('admin','corporate','dealer','user') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user' COMMENT 'Тип аккаунта',
  `status` enum('active','not-confirmed','restore-password','banned') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'not-confirmed' COMMENT 'Состояние',
  `data` json NOT NULL COMMENT 'Дополнительные параметры',
  `group_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Идентификатор роли ACL',
  `site_id` char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '001' COMMENT 'Код сайта',
  `lang` enum('ru','en') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ru' COMMENT 'Код языка',
  `hash` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `last_visit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `UK_users_login` (`login`),
  KEY `IDX_users_group_id` (`group_id`),
  KEY `IDX_users_site_id` (`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=331 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=256 COMMENT='Учетные записи пользователей';

-- ----------------------------
-- Table structure for users_config
-- ----------------------------
DROP TABLE IF EXISTS `users_config`;
CREATE TABLE `users_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `value` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `priority` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_content_config_alias` (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=8192;

-- ----------------------------
-- Table structure for users_conversations
-- ----------------------------
DROP TABLE IF EXISTS `users_conversations`;
CREATE TABLE `users_conversations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_one` int(11) NOT NULL,
  `user_two` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `new_messages` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_conversations_users_id` (`user_one`,`user_two`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for users_emails
-- ----------------------------
DROP TABLE IF EXISTS `users_emails`;
CREATE TABLE `users_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `text` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_users_emails_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for users_favorites
-- ----------------------------
DROP TABLE IF EXISTS `users_favorites`;
CREATE TABLE `users_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'Идентификатор владельца',
  `item_id` int(11) DEFAULT NULL COMMENT 'Идентификатор пользователя',
  PRIMARY KEY (`id`),
  KEY `IDX_users_favorites_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=5461 COMMENT='Избранные пользователи';

-- ----------------------------
-- Table structure for users_mailing
-- ----------------------------
DROP TABLE IF EXISTS `users_mailing`;
CREATE TABLE `users_mailing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `state` tinyint(1) DEFAULT '0',
  `subject` varchar(255) DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `text` text,
  `start_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_users_mailing_site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for users_mailing_process
-- ----------------------------
DROP TABLE IF EXISTS `users_mailing_process`;
CREATE TABLE `users_mailing_process` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mailing_id` int(11) DEFAULT NULL,
  `state` tinyint(1) DEFAULT '0',
  `current_step` mediumint(9) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_users_mailing_process_mailing_id` (`mailing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for users_mailing_report
-- ----------------------------
DROP TABLE IF EXISTS `users_mailing_report`;
CREATE TABLE `users_mailing_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `process_id` int(11) DEFAULT NULL,
  `mailing_id` int(11) DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `state` tinyint(1) DEFAULT '0',
  `data` blob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_users_mailing_process_user_id` (`user_id`),
  KEY `IDX_users_mailing_process_mailing_id` (`mailing_id`),
  KEY `IDX_users_mailing_report_process_id` (`process_id`),
  CONSTRAINT `FK_users_mailing_process_maili` FOREIGN KEY (`mailing_id`) REFERENCES `users_mailing` (`id`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for users_mailing_templates
-- ----------------------------
DROP TABLE IF EXISTS `users_mailing_templates`;
CREATE TABLE `users_mailing_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `text` text,
  `state` tinyint(4) DEFAULT '1',
  `site_id` char(3) DEFAULT '000',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for users_messages
-- ----------------------------
DROP TABLE IF EXISTS `users_messages`;
CREATE TABLE `users_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_seen` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_from_sender` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_from_receiver` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `attachments` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_messages_conversation_id` (`conversation_id`),
  KEY `IDX_messages_user_conversation_id` (`user_id`,`conversation_id`,`is_seen`),
  KEY `IDX_messages_user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=53 COMMENT='Личные сообщения';

-- ----------------------------
-- Table structure for users_profile
-- ----------------------------
DROP TABLE IF EXISTS `users_profile`;
CREATE TABLE `users_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `city_id` varchar(36) DEFAULT NULL,
  `surname` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `patronymic` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `delivery` varchar(50) DEFAULT NULL,
  `birth` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `data` longblob,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_users_profile_user_id` (`user_id`),
  KEY `IDX_users_profile_city_id` (`city_id`)
) ENGINE=InnoDB AUTO_INCREMENT=344 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1512 COMMENT='Профили пользователей';

-- ----------------------------
-- Table structure for users_profile_corporate
-- ----------------------------
DROP TABLE IF EXISTS `users_profile_corporate`;
CREATE TABLE `users_profile_corporate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Идентификатор пользователя',
  `legal_entity` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inn` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpp` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `okpo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` int(11) NOT NULL DEFAULT '0',
  `index` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `settlement` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `house` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `structure` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_country_id` int(11) DEFAULT NULL,
  `f_index` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_region` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_area` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_settlement` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_street` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_house` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_structure` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_office` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `general_manager` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chief_accountant` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_company` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_organization` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone_contact_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_contact_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_account` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bic` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correspondent_bank_account` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `same_addr` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` json DEFAULT NULL COMMENT 'Дополнительные параметры',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_users_profile_corporate_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Профили корпоративных клиентов';

-- ----------------------------
-- Table structure for users_profile_stat
-- ----------------------------
DROP TABLE IF EXISTS `users_profile_stat`;
CREATE TABLE `users_profile_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `guest_id` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_user_profile_stat_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=630 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=88 COMMENT='Статситсика просмотра профиля';

-- ----------------------------
-- Table structure for users_register_stat
-- ----------------------------
DROP TABLE IF EXISTS `users_register_stat`;
CREATE TABLE `users_register_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` int(11) unsigned DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=780 COMMENT='Статистика посещений пользователями сайта';

-- ----------------------------
-- Table structure for users_service
-- ----------------------------
DROP TABLE IF EXISTS `users_service`;
CREATE TABLE `users_service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_users_service_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Подключенные сервисы';

-- ----------------------------
-- Table structure for users_settings
-- ----------------------------
DROP TABLE IF EXISTS `users_settings`;
CREATE TABLE `users_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `send_personal_messages` tinyint(1) DEFAULT '1',
  `send_email_notify` tinyint(1) DEFAULT '1',
  `show_profile` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=326 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=99;

-- ----------------------------
-- Table structure for users_subscribe
-- ----------------------------
DROP TABLE IF EXISTS `users_subscribe`;
CREATE TABLE `users_subscribe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(80) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_id` char(3) DEFAULT '000',
  `state` tinyint(4) DEFAULT '1',
  `hash` char(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=574 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=145;

-- ----------------------------
-- Table structure for users_visit_stat
-- ----------------------------
DROP TABLE IF EXISTS `users_visit_stat`;
CREATE TABLE `users_visit_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` int(11) unsigned DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=655 COMMENT='Статистика посещений пользователями сайта';

-- ----------------------------
-- View structure for catalog_items_popular
-- ----------------------------
DROP VIEW IF EXISTS `catalog_items_popular`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `catalog_items_popular` AS select `ci`.`id` AS `id`,`ci`.`name` AS `name`,`ci`.`alias` AS `alias`,`ci`.`category_id` AS `category_id`,`ci`.`state` AS `state`,`ci`.`sort` AS `sort`,`ci`.`introtext` AS `introtext`,`ci`.`created_at` AS `created_at`,`ci`.`updated_at` AS `updated_at`,`ci`.`deleted_at` AS `deleted_at`,`ci`.`published_at` AS `published_at`,`ci`.`data` AS `data`,`ci`.`site_id` AS `site_id`,`civ`.`count` AS `count` from (`catalog_items` `ci` left join `catalog_items_view` `civ` on((`ci`.`id` = `civ`.`item_id`)));

-- ----------------------------
-- View structure for catalog_items_properties_index_view
-- ----------------------------
DROP VIEW IF EXISTS `catalog_items_properties_index_view`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `catalog_items_properties_index_view` AS select `catalog_items_properties_index`.`id` AS `id`,`catalog_items_properties_index`.`item_id` AS `item_id`,`catalog_items_properties_index`.`property_id` AS `property_id`,`catalog_items_properties_index`.`value` AS `value`,`catalog_items_properties_index`.`facet_id` AS `facet_id`,`catalog_items_properties_index`.`category_id` AS `category_id` from `catalog_items_properties_index`;

-- ----------------------------
-- View structure for catalog_items_search
-- ----------------------------
DROP VIEW IF EXISTS `catalog_items_search`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `catalog_items_search` AS select `ci`.`id` AS `id`,`ci`.`name` AS `name`,`ci`.`code` AS `code`,group_concat(`cipa`.`id` separator '||') AS `offers_ids`,group_concat(`cipa`.`name` separator '||') AS `offers_name`,group_concat(`cipa`.`code` separator '||') AS `offers_code`,`ci`.`alias` AS `alias`,`ci`.`category_id` AS `category_id`,`ci`.`created_at` AS `created_at`,`ci`.`deleted_at` AS `deleted_at`,`ci`.`state` AS `state`,`ci`.`introtext` AS `introtext`,`cc`.`name` AS `category_name`,`cc`.`data` AS `category_data`,`cip`.`price` AS `price`,`cip`.`id` AS `price_id`,`ci`.`data` AS `data`,`ci`.`sort` AS `sort` from (((`catalog_items` `ci` left join `catalog_categories` `cc` on((`cc`.`id` = `ci`.`category_id`))) left join `catalog_items_price` `cip` on((`cip`.`item_id` = `ci`.`id`))) left join `catalog_items` `cipa` on((`cipa`.`parent_id` = `ci`.`id`))) where isnull(`ci`.`parent_id`) group by `ci`.`id`;

-- ----------------------------
-- View structure for catalog_items_search_import
-- ----------------------------
DROP VIEW IF EXISTS `catalog_items_search_import`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `catalog_items_search_import` AS select `ci`.`id` AS `id`,`ci`.`category_id` AS `category_id`,`ci`.`deleted_at` AS `deleted_at`,`ci`.`sort` AS `sort`,`cip`.`price` AS `price`,`cip`.`id` AS `price_id`,`cia`.`value` AS `availability`,`cia`.`id` AS `availability_id` from (((`catalog_items` `ci` left join `catalog_categories` `cc` on((`cc`.`id` = `ci`.`category_id`))) left join `catalog_items_price` `cip` on((`cip`.`item_id` = `ci`.`id`))) left join `catalog_items_availability` `cia` on((`cia`.`item_id` = `ci`.`id`)));

-- ----------------------------
-- View structure for catalog_item_properties
-- ----------------------------
DROP VIEW IF EXISTS `catalog_item_properties`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `catalog_item_properties` AS select `cpv`.`value` AS `value`,`cp`.`name` AS `name`,`cpv`.`item_id` AS `item_id`,`cp`.`sort` AS `sort`,`cp`.`data` AS `data`,`cp`.`code` AS `code`,`cp`.`req` AS `req` from (`catalog_items_properties_values` `cpv` left join `catalog_categories_properties` `cp` on((`cp`.`id` = `cpv`.`property_id`)));

-- ----------------------------
-- View structure for catalog_item_related_items
-- ----------------------------
DROP VIEW IF EXISTS `catalog_item_related_items`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `catalog_item_related_items` AS select `cir`.`id` AS `id`,`cir`.`product_id` AS `product_id`,`cir`.`related_id` AS `related_id`,`ci`.`code` AS `code`,`ci`.`name` AS `title`,`ci`.`alias` AS `alias`,`ci`.`category_id` AS `category_id`,`ci`.`deleted_at` AS `deleted_at`,`ci`.`data` AS `data`,`cip`.`id` AS `price_id`,`cip`.`data` AS `price_data`,`cir`.`compare_manufacturer` AS `compare_manufacturer`,`cir`.`compare_type` AS `compare_type`,`cir`.`sort` AS `sort`,`cir`.`type` AS `type`,`cir`.`state` AS `state` from ((`catalog_items_related` `cir` left join `catalog_items` `ci` on((`ci`.`id` = `cir`.`related_id`))) left join `catalog_items_price` `cip` on((`cip`.`item_id` = `ci`.`id`)));

-- ----------------------------
-- View structure for new_messages
-- ----------------------------
DROP VIEW IF EXISTS `new_messages`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `new_messages` AS select `m`.`id` AS `messahe_id`,`m`.`message` AS `message`,`m`.`created_at` AS `created_at`,`c`.`id` AS `conversation_id`,`c`.`user_one` AS `user_one`,`c`.`user_two` AS `user_two`,`m`.`user_id` AS `sender_id` from (`conversations` `c` left join `messages` `m` on(((`m`.`conversation_id` = `c`.`id`) and (`m`.`is_seen` = 0)))) where (`m`.`id` is not null);

-- ----------------------------
-- View structure for search_content_index
-- ----------------------------
DROP VIEW IF EXISTS `search_content_index`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `search_content_index` AS select `c`.`id` AS `id`,`c`.`name` AS `name`,`c`.`alias` AS `alias`,`c`.`category_id` AS `category_id`,`c`.`favorites` AS `favorites`,`c`.`view_counter` AS `view_counter`,`c`.`sort` AS `sort`,`c`.`introtext` AS `introtext`,`c`.`fulltext` AS `fulltext`,`c`.`state` AS `state`,`c`.`created_at` AS `created_at`,`c`.`updated_at` AS `updated_at`,`c`.`published_at` AS `published_at`,`c`.`deleted_at` AS `deleted_at`,`c`.`data` AS `data`,`c`.`site_id` AS `site_id`,`si`.`text` AS `search_index` from (`content` `c` left join `search_index` `si` on(((`si`.`item_id` = `c`.`id`) and (`si`.`type` = 'content')))) where (`selectCountContentCanonical`(`c`.`id`) >= 1);

-- ----------------------------
-- View structure for search_content_tag
-- ----------------------------
DROP VIEW IF EXISTS `search_content_tag`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `search_content_tag` AS select `c`.`id` AS `id`,`c`.`name` AS `name`,`c`.`alias` AS `alias`,`c`.`category_id` AS `category_id`,`c`.`favorites` AS `favorites`,`c`.`view_counter` AS `view_counter`,`c`.`sort` AS `sort`,`c`.`introtext` AS `introtext`,`c`.`fulltext` AS `fulltext`,`c`.`state` AS `state`,`c`.`created_at` AS `created_at`,`c`.`updated_at` AS `updated_at`,`c`.`published_at` AS `published_at`,`c`.`deleted_at` AS `deleted_at`,`c`.`data` AS `data`,`c`.`site_id` AS `site_id`,`ct`.`text` AS `tag` from (`content` `c` left join `content_tag` `ct` on((`ct`.`item_id` = `c`.`id`)));

-- ----------------------------
-- View structure for search_users
-- ----------------------------
DROP VIEW IF EXISTS `search_users`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `search_users` AS select `u`.`id` AS `id`,`u`.`login` AS `login`,`u`.`email` AS `email`,`u`.`lang` AS `lang`,`u`.`created_at` AS `created_at`,`u`.`updated_at` AS `updated_at`,`u`.`deleted_at` AS `deleted_at`,`u`.`last_visit` AS `last_visit`,`u`.`type` AS `type`,`up`.`city_id` AS `city_id`,`up`.`name` AS `name`,`up`.`surname` AS `surname`,`u`.`data` AS `data`,`u`.`status` AS `status`,`us`.`show_profile` AS `show_profile`,`up`.`data` AS `profile_data`,concat_ws(' ',`lptn`.`full_name`,`lp`.`name`) AS `location_name` from ((((`users` `u` left join `users_profile` `up` on((`up`.`user_id` = `u`.`id`))) left join `users_settings` `us` on((`us`.`user_id` = `u`.`id`))) left join `location_place` `lp` on((`lp`.`fias_aoguid` = `up`.`city_id`))) left join `location_place_type_name` `lptn` on((`lptn`.`id` = `lp`.`place_type_name_id`))) where ((`u`.`type` = 'user') and (`u`.`status` = 'active') and (`us`.`show_profile` = 0) and isnull(`u`.`deleted_at`));

-- ----------------------------
-- View structure for user_orders
-- ----------------------------
DROP VIEW IF EXISTS `user_orders`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `user_orders` AS select `o`.`id` AS `id`,`o`.`user_id` AS `user_id`,`o`.`data` AS `data`,`o`.`status` AS `status`,`o`.`created_at` AS `created_at`,`o`.`updated_at` AS `updated_at`,`o`.`deleted_at` AS `deleted_at`,`o`.`site_id` AS `site_id`,`getOrderSum`(`o`.`id`) AS `total_sum` from `order` `o`;

-- ----------------------------
-- Procedure structure for checkSortUserIndex
-- ----------------------------
DROP PROCEDURE IF EXISTS `checkSortUserIndex`;
delimiter ;;
CREATE DEFINER=`root`@`%` PROCEDURE `checkSortUserIndex`(IN user_id int)
    COMMENT 'Обновление индекса сортировки пользователей'
BEGIN

  DECLARE count_items,
          count_like int;
  DECLARE avg_rating float;

  -- (100*0,3)+(500*0,02)+(5*12)=100

  SELECT
    ur.add_catalog_item,
    ur.add_like_catalog_item INTO count_items, count_like
  FROM user_rating ur
  WHERE ur.user_id = user_id;
  SELECT
    AVG(ur.rate) INTO avg_rating
  FROM users_reviews ur
  WHERE ur.user_id = user_id;

  UPDATE users u
  SET sort_index = (count_items * 0.3) + (count_like * 0.02) + (avg_rating * 12)
  WHERE u.id = user_id;


END;
;;
delimiter ;

-- ----------------------------
-- Procedure structure for createCatalogPropertiesIndex
-- ----------------------------
DROP PROCEDURE IF EXISTS `createCatalogPropertiesIndex`;
delimiter ;;
CREATE DEFINER=`root`@`%` PROCEDURE `createCatalogPropertiesIndex`(IN id int)
    COMMENT 'Заполнение индекса поиска по свойствам товаров'
BEGIN

  INSERT HIGH_PRIORITY INTO catalog_items_properties_index (property_id, item_id, value)
    SELECT
      ccp.id,
      cipv.item_id,
      cipv.value
    FROM catalog_items_properties_values cipv
      LEFT JOIN catalog_categories_properties ccp
        ON ccp.id = cipv.property_id
        AND ccp.req = 1
    WHERE cipv.item_id = id
    AND ccp.id IS NOT NULL;

END;
;;
delimiter ;

-- ----------------------------
-- Procedure structure for deleteCatalogPropertiesIndex
-- ----------------------------
DROP PROCEDURE IF EXISTS `deleteCatalogPropertiesIndex`;
delimiter ;;
CREATE DEFINER=`root`@`%` PROCEDURE `deleteCatalogPropertiesIndex`(IN id int)
    COMMENT 'Удаление поискового индекса'
BEGIN

  DELETE LOW_PRIORITY QUICK
    FROM catalog_items_properties_index
  WHERE item_id = id;

END;
;;
delimiter ;

-- ----------------------------
-- Function structure for getCatalogItemPrice
-- ----------------------------
DROP FUNCTION IF EXISTS `getCatalogItemPrice`;
delimiter ;;
CREATE DEFINER=`root`@`%` FUNCTION `getCatalogItemPrice`() RETURNS int(11)
BEGIN

  RETURN 1;
END;
;;
delimiter ;

-- ----------------------------
-- Function structure for getCountCatalogCategoryPropertyValues
-- ----------------------------
DROP FUNCTION IF EXISTS `getCountCatalogCategoryPropertyValues`;
delimiter ;;
CREATE DEFINER=`root`@`%` FUNCTION `getCountCatalogCategoryPropertyValues`(id int) RETURNS int(11)
BEGIN

  RETURN 1;
END;
;;
delimiter ;

-- ----------------------------
-- Function structure for getCountCategoryActiveItems
-- ----------------------------
DROP FUNCTION IF EXISTS `getCountCategoryActiveItems`;
delimiter ;;
CREATE DEFINER=`root`@`%` FUNCTION `getCountCategoryActiveItems`(category_id int) RETURNS int(11)
BEGIN

  DECLARE total_count int(11);

  SELECT
    COUNT(*) AS total_count INTO total_count
  FROM catalog_items ci
  WHERE ci.category_id = category_id
  AND ci.state = 1

  AND ISNULL(ci.deleted_at);

  RETURN total_count;
END;
;;
delimiter ;

-- ----------------------------
-- Function structure for getCountCategoryChildActiveItems
-- ----------------------------
DROP FUNCTION IF EXISTS `getCountCategoryChildActiveItems`;
delimiter ;;
CREATE DEFINER=`root`@`%` FUNCTION `getCountCategoryChildActiveItems`(category_id int) RETURNS int(11)
    COMMENT 'Возвращает кол-во активных позиций в дочерних категориях'
BEGIN

  DECLARE parent_left,
          parent_right,
          total_count int(11);

  SELECT
    `lft`,
    `rgt` INTO parent_left, parent_right
  FROM catalog_categories
  WHERE `id` = category_id;


  SELECT
    COUNT(*) AS total_count INTO total_count
  FROM catalog_items ci
  WHERE ci.category_id IN (SELECT
      cc.id
    FROM catalog_categories cc
    WHERE cc.state = 1
    AND cc.lft >= parent_left
    AND cc.rgt <= parent_right)

  AND ci.state = 1
  AND ISNULL(ci.deleted_at);


  RETURN total_count;
END;
;;
delimiter ;

-- ----------------------------
-- Function structure for getCountCategoryParentsActiveItems
-- ----------------------------
DROP FUNCTION IF EXISTS `getCountCategoryParentsActiveItems`;
delimiter ;;
CREATE DEFINER=`root`@`%` FUNCTION `getCountCategoryParentsActiveItems`(category_id int) RETURNS int(11)
    COMMENT 'Возвращает кол-во активных позиций в родительских категориях'
BEGIN

  DECLARE total_count int(11);

  SELECT
    COUNT(*) AS total_count INTO total_count
  FROM catalog_items ci
  WHERE ci.category_id IN (SELECT
      parent.ID
    FROM catalog_categories node,
         catalog_categories parent
    WHERE (
    node.lft BETWEEN parent.lft AND parent.rgt
    AND parent.ID
    AND parent.STATE = 1
    AND parent.ID > 1
    )
    AND node.ID = category_id)

  AND ci.STATE = 1

  AND ISNULL(ci.deleted_at);


  RETURN total_count;
END;
;;
delimiter ;

-- ----------------------------
-- Function structure for getLocationToRadius
-- ----------------------------
DROP FUNCTION IF EXISTS `getLocationToRadius`;
delimiter ;;
CREATE DEFINER=`root`@`%` FUNCTION `getLocationToRadius`() RETURNS int(11)
BEGIN

  RETURN 1;
END;
;;
delimiter ;

-- ----------------------------
-- Procedure structure for getLocationToRadius
-- ----------------------------
DROP PROCEDURE IF EXISTS `getLocationToRadius`;
delimiter ;;
CREATE DEFINER=`root`@`%` PROCEDURE `getLocationToRadius`()
BEGIN

  SET @latitude = 0, @longitude = 0;

  SELECT
    lpc.latitude,
    lpc.longitude INTO @latitude, @longitude
  FROM location_place_coordinates lpc
  WHERE lpc.ym_geocode LIKE '%Уфа';


  SELECT
    *,
    (6371 * ACOS(COS(RADIANS(@latitude)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(@longitude))
    + SIN(RADIANS(@latitude)) * SIN(RADIANS(latitude)))) AS distance
  FROM location_place_coordinates
  HAVING distance < 10
  ORDER BY distance;

END;
;;
delimiter ;

-- ----------------------------
-- Function structure for getOrderSum
-- ----------------------------
DROP FUNCTION IF EXISTS `getOrderSum`;
delimiter ;;
CREATE DEFINER=`root`@`%` FUNCTION `getOrderSum`() RETURNS int(11)
BEGIN

  RETURN 1;
END;
;;
delimiter ;

-- ----------------------------
-- Procedure structure for recalcalateAllCategoryActiveItems
-- ----------------------------
DROP PROCEDURE IF EXISTS `recalcalateAllCategoryActiveItems`;
delimiter ;;
CREATE DEFINER=`root`@`%` PROCEDURE `recalcalateAllCategoryActiveItems`()
BEGIN

  DECLARE id int DEFAULT 0;
  DECLARE done int DEFAULT 0;
  DECLARE cur CURSOR FOR
  SELECT
    cc.id
  FROM catalog_categories cc
  WHERE cc.state = 1;
  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

  OPEN cur;

  REPEAT
    FETCH cur INTO id;

    IF NOT done THEN
      CALL recalculateCategoryActiveItems(id);
    END IF;

  UNTIL done END REPEAT;

  CLOSE cur;

END;
;;
delimiter ;

-- ----------------------------
-- Procedure structure for recalculateCategoryActiveItems
-- ----------------------------
DROP PROCEDURE IF EXISTS `recalculateCategoryActiveItems`;
delimiter ;;
CREATE DEFINER=`root`@`%` PROCEDURE `recalculateCategoryActiveItems`(IN category_id int)
    COMMENT 'Пересчет кол-ва активных позиций каталога'
BEGIN

  DECLARE parent_depth smallint(5);
  DECLARE parentId int(11);

  -- Обновление кол-ва активных позиций в текущей категории с учетом активных позиций в дочерних
  UPDATE catalog_categories
  SET counter = getCountCategoryChildActiveItems(category_id)
  WHERE ID = category_id LIMIT 1;

  -- Обновление родительских категорий !!!Предусмотрено обновление родителей только с третьего уровня вложенности!!!
  -- Получаем родительскую категория для определения уровня вложенности и идентификатора родителя (если он есть)
  -- 
  -- 
  SELECT
    cc.parent_id,
    cc.depth INTO parentId, parent_depth
  FROM catalog_categories cc
  WHERE cc.ID = category_id LIMIT 1;


  -- Если обновляемая категория товара на втором уровне, то родитель на первом и нужно обновить кол-во по всем детям
  IF (parent_depth = 2) THEN
    UPDATE catalog_categories
    SET counter = getCountCategoryActiveItems(category_id)
    WHERE ID = category_id LIMIT 1;

    IF (parentId > 1) THEN
      UPDATE catalog_categories
      SET counter = getCountCategoryChildActiveItems(parentId)
      WHERE ID = parentId LIMIT 1;
    END IF;
  END IF;

  -- Если обновляемая категория товара находится на третьем уровне, то обновляем как второй так и первый уровень,
  -- для этого необходимо запросить данные категории первого уровня
  IF (parent_depth = 3) THEN
    IF (parentId > 1) THEN
      -- Обновляем родительскую категорию второго уровня
      UPDATE catalog_categories
      SET counter = getCountCategoryChildActiveItems(parentId)
      WHERE ID = parentId LIMIT 1;

      -- Обновляем переменные parentId, parent_depth для категории первого уровня
      SELECT
        cc.parent_id,
        cc.depth INTO parentId, parent_depth
      FROM catalog_categories cc
      WHERE cc.ID = parentId LIMIT 1;
      -- Обновляем данные категории первого уровня
      IF (parent_depth = 1) THEN
        UPDATE catalog_categories
        SET counter = getCountCategoryChildActiveItems(parentId)
        WHERE ID = parentId LIMIT 1;
      END IF;
    END IF;
  END IF;

END;
;;
delimiter ;

-- ----------------------------
-- Function structure for selectCountContentCanonical
-- ----------------------------
DROP FUNCTION IF EXISTS `selectCountContentCanonical`;
delimiter ;;
CREATE DEFINER=`root`@`%` FUNCTION `selectCountContentCanonical`() RETURNS int(11)
    DETERMINISTIC
BEGIN

  RETURN 1;
END;
;;
delimiter ;

-- ----------------------------
-- Procedure structure for updateCatalogItemPublished
-- ----------------------------
DROP PROCEDURE IF EXISTS `updateCatalogItemPublished`;
delimiter ;;
CREATE DEFINER=`root`@`%` PROCEDURE `updateCatalogItemPublished`(IN const bigint)
    COMMENT 'Обновление счетчика опубликованных позиций каталога (с учетом даты присвоения состояния)'
BEGIN

  DECLARE statisticId int;
  DECLARE check_id int;

  SET check_id = (SELECT
      id
    FROM catalog_items_history
    WHERE item_id = const
    AND event_type = 'published'
    AND DATE_FORMAT(created_at, '%Y-%m-%d') = CURDATE() LIMIT 1);

  IF (ISNULL(@check_id)) THEN

    SET statisticId = (SELECT
        id
      FROM catalog_items_statistic_published AS csv
      WHERE DATE_FORMAT(csv.created_at, '%Y-%m-%d') = CURDATE() LIMIT 1);

    IF (statisticId > 0) THEN
      UPDATE catalog_items_statistic_published
      SET VALUE = VALUE + 1
      WHERE ID = statisticId;
    ELSE
      INSERT LOW_PRIORITY INTO catalog_items_statistic_published (created_at, value)
        VALUES (NOW(), 1);
    END IF;

  END IF;

END;
;;
delimiter ;

-- ----------------------------
-- Procedure structure for updateCatalogItemUnpublished
-- ----------------------------
DROP PROCEDURE IF EXISTS `updateCatalogItemUnpublished`;
delimiter ;;
CREATE DEFINER=`root`@`%` PROCEDURE `updateCatalogItemUnpublished`(IN const bigint)
BEGIN

  DECLARE statisticId int;
  DECLARE check_id int;

  SET check_id = (SELECT
      id
    FROM catalog_items_history
    WHERE item_id = const
    AND event_type = 'unpublished'
    AND DATE_FORMAT(created_at, '%Y-%m-%d') = CURDATE() LIMIT 1);

  IF (ISNULL(@check_id)) THEN

    SET statisticId = (SELECT
        id
      FROM catalog_items_statistic_unpublished AS csv
      WHERE DATE_FORMAT(csv.created_at, '%Y-%m-%d') = CURDATE() LIMIT 1);

    IF (statisticId > 0) THEN
      UPDATE catalog_items_statistic_unpublished
      SET VALUE = VALUE + 1
      WHERE ID = statisticId;
    ELSE
      INSERT LOW_PRIORITY INTO catalog_items_statistic_unpublished (created_at, value)
        VALUES (NOW(), 1);
    END IF;

  END IF;

END;
;;
delimiter ;

-- ----------------------------
-- Procedure structure for userRatingAddLike
-- ----------------------------
DROP PROCEDURE IF EXISTS `userRatingAddLike`;
delimiter ;;
CREATE DEFINER=`root`@`%` PROCEDURE `userRatingAddLike`(IN const_id bigint)
    COMMENT 'Считает кол-во лайков у товаров пользователя'
BEGIN

  DECLARE user_id int;
  DECLARE count_like int;

  SET user_id = (SELECT
      ci.user_id
    FROM catalog_items ci
    WHERE ci.const_id = const_id LIMIT 1);

  SET count_like = (SELECT
      COUNT(DISTINCT cil.item_id)
    FROM catalog_items_like cil
      LEFT JOIN catalog_items ci
        ON ci.const_id = cil.item_id
    WHERE ci.user_id = user_id);

  UPDATE users_rating ur
  SET ur.add_like_catalog_item = count_like
  WHERE ur.user_id = user_id LIMIT 1;
END;
;;
delimiter ;

-- ----------------------------
-- Procedure structure for userRatingAddPosition
-- ----------------------------
DROP PROCEDURE IF EXISTS `userRatingAddPosition`;
delimiter ;;
CREATE DEFINER=`root`@`%` PROCEDURE `userRatingAddPosition`(IN user_id int)
    COMMENT 'Считает кол-во активных\\проданных товаров'
BEGIN
  DECLARE count int;
  -- не считаем 0, 2 - в корзине, 4 - автоматически создано при обновление
  -- считаем 1 - опубликовано, 5 - продано, одна коробка - один товар?

  SET count = (SELECT
      COUNT(*)
    FROM catalog_items ci
    WHERE ci.user_id = user_id
    AND ci.state IN (1, 5));

  UPDATE users_rating ur
  SET ur.add_catalog_item = count
  WHERE user_id = user_id LIMIT 1;

END;
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table catalog_items
-- ----------------------------
DROP TRIGGER IF EXISTS `catalog_items_after_insert`;
delimiter ;;
CREATE TRIGGER `catalog_items_after_insert` AFTER INSERT ON `catalog_items` FOR EACH ROW BEGIN

  SET @id = (SELECT
      id
    FROM catalog_items_view
    WHERE item_id = NEW.id LIMIT 1);

  IF (ISNULL(@id)) THEN
    INSERT INTO catalog_items_view (item_id)
      VALUE (NEW.id);
  END IF;

END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table catalog_items
-- ----------------------------
DROP TRIGGER IF EXISTS `catalog_items_before_update`;
delimiter ;;
CREATE TRIGGER `catalog_items_before_update` BEFORE UPDATE ON `catalog_items` FOR EACH ROW BEGIN

  IF (OLD.sort != NEW.sort) THEN
    INSERT INTO catalog_items_history (item_id, event_type, created_at, old_value, new_value)
      VALUES (NEW.id, 'change_sort', NOW(), OLD.sort, NEW.sort);
  END IF;

  IF (NEW.state = 1) THEN
    SET NEW.published_at = NOW();
  END IF;

END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table catalog_items
-- ----------------------------
DROP TRIGGER IF EXISTS `catalog_items_after_delete`;
delimiter ;;
CREATE TRIGGER `catalog_items_after_delete` AFTER DELETE ON `catalog_items` FOR EACH ROW BEGIN
  DELETE
    FROM catalog_items_properties_values
  WHERE item_id = OLD.id;
  DELETE
    FROM catalog_items_properties_index
  WHERE item_id = OLD.id;
  DELETE
    FROM catalog_items_price
  WHERE item_id = OLD.id;
  DELETE
    FROM media_gallery_item
  WHERE parent_id = OLD.id
    AND parent_type = 11;
  DELETE
    FROM catalog_items_like
  WHERE item_id = OLD.id;
  DELETE
    FROM catalog_items_comments
  WHERE item_id = OLD.id;

  #пересчет кол-ва позиций в категориях
  CALL recalculateCategoryActiveItems(OLD.category_id);

END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table catalog_items_availability
-- ----------------------------
DROP TRIGGER IF EXISTS `catalog_items_availability_before_update`;
delimiter ;;
CREATE TRIGGER `catalog_items_availability_before_update` BEFORE UPDATE ON `catalog_items_availability` FOR EACH ROW BEGIN


  IF (OLD.value != NEW.value) THEN
    INSERT INTO catalog_items_history (item_id, event_type, created_at, old_value, new_value)
      VALUES (NEW.item_id, 'change_availability', NOW(), OLD.value, NEW.value);
  END IF;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table catalog_items_like
-- ----------------------------
DROP TRIGGER IF EXISTS `catalog_items_like_after_insert`;
delimiter ;;
CREATE TRIGGER `catalog_items_like_after_insert` AFTER INSERT ON `catalog_items_like` FOR EACH ROW BEGIN


  #обновляем кол-во лайков у пользователя  
  CALL userRatingAddLike(NEW.item_id);

END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table catalog_items_like
-- ----------------------------
DROP TRIGGER IF EXISTS `catalog_items_like_after_delete`;
delimiter ;;
CREATE TRIGGER `catalog_items_like_after_delete` AFTER DELETE ON `catalog_items_like` FOR EACH ROW BEGIN

--   CALL userRatingAddLike(OLD.item_id);

END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table catalog_items_price
-- ----------------------------
DROP TRIGGER IF EXISTS `catalog_items_price_before_update`;
delimiter ;;
CREATE TRIGGER `catalog_items_price_before_update` AFTER UPDATE ON `catalog_items_price` FOR EACH ROW BEGIN

  IF (NEW.price != Old.price) THEN
    INSERT INTO catalog_items_history (item_id, event_type, created_at, old_value, new_value)
      VALUES (NEW.item_id, 'update_price', NOW(), OLD.price, NEW.price);
  END IF;


END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table catalog_items_view
-- ----------------------------
DROP TRIGGER IF EXISTS `catalog_items_view_after_insert`;
delimiter ;;
CREATE TRIGGER `catalog_items_view_after_insert` AFTER INSERT ON `catalog_items_view` FOR EACH ROW BEGIN
  SET @statisticId = (SELECT
      ID
    FROM catalog_items_statistic_views AS csv
    WHERE DATE_FORMAT(csv.created_at, '%Y-%m-%d') = CURDATE() LIMIT 1);

  IF (@statisticId > 0) THEN
    UPDATE catalog_items_statistic_views
    SET VALUE = VALUE + 1
    WHERE ID = @statisticId;
  ELSE
    INSERT LOW_PRIORITY INTO catalog_items_statistic_views (created_at, value)
      VALUES (NOW(), 1);
  END IF;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table catalog_items_view
-- ----------------------------
DROP TRIGGER IF EXISTS `catalog_items_view_after_update`;
delimiter ;;
CREATE TRIGGER `catalog_items_view_after_update` AFTER UPDATE ON `catalog_items_view` FOR EACH ROW BEGIN
  SET @statisticId = (SELECT
      id
    FROM catalog_items_statistic_views AS csv
    WHERE DATE_FORMAT(csv.created_at, '%Y-%m-%d') = CURDATE() LIMIT 1);

  IF (@statisticId > 0) THEN
    UPDATE catalog_items_statistic_views
    SET VALUE = VALUE + 1
    WHERE ID = @statisticId;
  ELSE
    INSERT LOW_PRIORITY INTO catalog_items_statistic_views (created_at, value)
      VALUES (NOW(), 1);
  END IF;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table content
-- ----------------------------
DROP TRIGGER IF EXISTS `content_before_insert`;
delimiter ;;
CREATE TRIGGER `content_before_insert` BEFORE INSERT ON `content` FOR EACH ROW BEGIN
  IF (NEW.state > 0) THEN
    SET NEW.published_at = NOW();
  END IF;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table content
-- ----------------------------
DROP TRIGGER IF EXISTS `content_before_update`;
delimiter ;;
CREATE TRIGGER `content_before_update` BEFORE UPDATE ON `content` FOR EACH ROW BEGIN

  IF (NEW.state > 0
    AND OLD.state != 1) THEN
    SET NEW.published_at = NOW();
  END IF;

  IF (NEW.state != 1) THEN
    SET NEW.published_at = NULL;
  END IF;

  IF (NEW.view_counter <> OLD.view_counter) THEN
    SET @statisticId = (SELECT
        ID
      FROM content_statistic_views AS csv
      WHERE DATE_FORMAT(csv.created_at, '%Y-%m-%d') = CURDATE() LIMIT 1);

    IF (@statisticId > 0) THEN
      UPDATE content_statistic_views
      SET VALUE = VALUE + 1
      WHERE ID = @statisticId;
    ELSE
      INSERT LOW_PRIORITY INTO content_statistic_views (created_at, value)
        VALUES (NOW(), 1);
    END IF;
  END IF;


END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table content
-- ----------------------------
DROP TRIGGER IF EXISTS `content_after_delete`;
delimiter ;;
CREATE TRIGGER `content_after_delete` AFTER DELETE ON `content` FOR EACH ROW BEGIN
  DELETE
    FROM content_canonical
  WHERE item_id = OLD.id;
  DELETE
    FROM search_index
  WHERE item_id = OLD.id
    AND type = 'content';
  DELETE
    FROM content_tag
  WHERE item_id = OLD.id;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table content_comments
-- ----------------------------
DROP TRIGGER IF EXISTS `content_comment_after_update`;
delimiter ;;
CREATE TRIGGER `content_comment_after_update` AFTER UPDATE ON `content_comments` FOR EACH ROW BEGIN

  IF (NEW.lft = 1) THEN

    SET @count = (NEW.rgt - NEW.lft - 1) / 2;

    UPDATE content
    SET count_comment = @count
    WHERE id = NEW.item_id LIMIT 1;

  END IF;

END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table forms_result
-- ----------------------------
DROP TRIGGER IF EXISTS `form_result_after_insert`;
delimiter ;;
CREATE TRIGGER `form_result_after_insert` AFTER INSERT ON `forms_result` FOR EACH ROW BEGIN

  SET @statisticId = (SELECT
      ID
    FROM forms_statistic_submit AS csv
    WHERE DATE_FORMAT(csv.created_at, '%Y-%m-%d') = CURDATE() LIMIT 1);

  IF (@statisticId > 0) THEN
    UPDATE forms_statistic_submit
    SET VALUE = VALUE + 1
    WHERE ID = @statisticId;
  ELSE
    INSERT LOW_PRIORITY INTO forms_statistic_submit (created_at, value)
      VALUES (NOW(), 1);
  END IF;

END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table media_gallery_item
-- ----------------------------
DROP TRIGGER IF EXISTS `media_gallery_item_before_insert`;
delimiter ;;
CREATE TRIGGER `media_gallery_item_before_insert` AFTER INSERT ON `media_gallery_item` FOR EACH ROW BEGIN

--   IF (NEW.parent_type = 10) THEN
--     INSERT INTO catalog_items_history (item_id, event_type, created_at)
--       VALUES (NEW.parent_id, 'add_certificate', NOW());
--   END IF;
-- 
--   IF (NEW.parent_type = 8) THEN
--     INSERT INTO catalog_items_history (item_id, event_type, created_at)
--       VALUES (NEW.parent_id, 'add_image', NOW());
--   END IF;

END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table media_gallery_item
-- ----------------------------
DROP TRIGGER IF EXISTS `media_gallery_item_before_delete`;
delimiter ;;
CREATE TRIGGER `media_gallery_item_before_delete` AFTER DELETE ON `media_gallery_item` FOR EACH ROW BEGIN

--   IF (OLD.parent_type = 10) THEN
--     INSERT INTO catalog_items_history (item_id, event_type, created_at)
--       VALUES (OLD.parent_id, 'delete_certificate', NOW());
--   END IF;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table menus
-- ----------------------------
DROP TRIGGER IF EXISTS `menus_befor_update`;
delimiter ;;
CREATE TRIGGER `menus_befor_update` BEFORE UPDATE ON `menus` FOR EACH ROW BEGIN
  IF (NEW.stat_error <> OLD.stat_error) THEN
    SET @statisticId = (SELECT
        ID
      FROM menus_statistic AS ms
      WHERE DATE_FORMAT(ms.created_at, '%Y-%m-%d') = CURDATE() LIMIT 1);

    IF (@statisticId > 0) THEN
      UPDATE menus_statistic
      SET errors = errors + 1
      WHERE ID = @statisticId;
    ELSE
      INSERT LOW_PRIORITY INTO menus_statistic (created_at, errors, site_id)
        VALUES (NOW(), 1, NEW.site_id);
    END IF;
  END IF;

  IF (NEW.stat_success <> OLD.stat_success) THEN
    SET @statisticId = (SELECT
        ID
      FROM menus_statistic AS ms
      WHERE DATE_FORMAT(ms.created_at, '%Y-%m-%d') = CURDATE() LIMIT 1);

    IF (@statisticId > 0) THEN
      UPDATE menus_statistic
      SET success = success + 1
      WHERE ID = @statisticId;
    ELSE
      INSERT LOW_PRIORITY INTO menus_statistic (created_at, success, site_id)
        VALUES (NOW(), 1, NEW.site_id);
    END IF;
  END IF;


  IF (NEW.stat_redirect <> OLD.stat_redirect) THEN
    SET @statisticId = (SELECT
        ID
      FROM menus_statistic AS ms
      WHERE DATE_FORMAT(ms.created_at, '%Y-%m-%d') = CURDATE() LIMIT 1);

    IF (@statisticId > 0) THEN
      UPDATE menus_statistic
      SET redirect = redirect + 1
      WHERE ID = @statisticId;
    ELSE
      INSERT LOW_PRIORITY INTO menus_statistic (created_at, redirect, site_id)
        VALUES (NOW(), 1, NEW.site_id);
    END IF;
  END IF;

END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table users_messages
-- ----------------------------
DROP TRIGGER IF EXISTS `messages_after_insert`;
delimiter ;;
CREATE TRIGGER `messages_after_insert` AFTER INSERT ON `users_messages` FOR EACH ROW BEGIN

END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
