-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 21, 2012 at 09:51 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `dataspot`
--

-- --------------------------------------------------------

--
-- Table structure for table `ds_fields`
--

CREATE TABLE `ds_fields` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `record_id` bigint(20) NOT NULL,
  `property_id` bigint(20) NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `record_id` (`record_id`),
  KEY `property_id` (`property_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ds_libraries`
--

CREATE TABLE `ds_libraries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'new-library',
  `display_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'New Library',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ds_properties`
--

CREATE TABLE `ds_properties` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `library_id` bigint(20) NOT NULL DEFAULT '0',
  `section_id` bigint(20) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  `display_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Text',
  `auto_name` tinyint(1) NOT NULL DEFAULT '1',
  `index` tinyint(1) NOT NULL DEFAULT '0',
  `default` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `type` varchar(127) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  `order` int(11) NOT NULL DEFAULT '99',
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'hidden',
  `insert` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'newline',
  `truncate` int(11) NOT NULL DEFAULT '60',
  `prepend` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `append` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `label_order` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `library_id` (`library_id`),
  KEY `parent_id` (`section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ds_records`
--

CREATE TABLE `ds_records` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `library_id` bigint(20) NOT NULL,
  `time_created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `library_id` (`library_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ds_sections`
--

CREATE TABLE `ds_sections` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `library_id` bigint(20) NOT NULL,
  `name` varchar(255) COLLATE utf16_unicode_ci NOT NULL,
  `order` int(11) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf16 COLLATE=utf16_unicode_ci;
