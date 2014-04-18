-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 18, 2014 at 03:11 AM
-- Server version: 5.6.12
-- PHP Version: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `concrete_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `CollectionSearchIndexAttributes`
--

CREATE TABLE IF NOT EXISTS `CollectionSearchIndexAttributes` (
  `cID` int(11) unsigned NOT NULL DEFAULT '0',
  `ak_meta_title` text,
  `ak_meta_description` text,
  `ak_meta_keywords` text,
  `ak_icon_dashboard` text,
  `ak_exclude_nav` tinyint(4) DEFAULT '0',
  `ak_exclude_page_list` tinyint(4) DEFAULT '0',
  `ak_header_extra_content` text,
  `ak_exclude_search_index` tinyint(4) DEFAULT '0',
  `ak_exclude_sitemapxml` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`cID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `CollectionSearchIndexAttributes`
--

INSERT INTO `CollectionSearchIndexAttributes` (`cID`, `ak_meta_title`, `ak_meta_description`, `ak_meta_keywords`, `ak_icon_dashboard`, `ak_exclude_nav`, `ak_exclude_page_list`, `ak_header_extra_content`, `ak_exclude_search_index`, `ak_exclude_sitemapxml`) VALUES
(1, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0),
(3, NULL, NULL, 'blog, blogging', 'icon-book', 0, 0, NULL, 0, 0),
(4, NULL, NULL, 'new blog, write blog, blogging', 'icon-pencil', 0, 0, NULL, 0, 0),
(5, NULL, NULL, 'blog drafts, composer', 'icon-book', 0, 0, NULL, 0, 0),
(6, NULL, NULL, 'pages, add page, delete page, copy, move, alias', NULL, 0, 0, NULL, 0, 0),
(7, NULL, NULL, 'pages, add page, delete page, copy, move, alias', 'icon-home', 0, 0, NULL, 0, 0),
(8, NULL, NULL, 'pages, add page, delete page, copy, move, alias, bulk', 'icon-road', 0, 0, NULL, 0, 0),
(9, NULL, NULL, 'find page, search page, search, find, pages, sitemap', 'icon-search', 0, 0, NULL, 0, 0),
(11, NULL, NULL, 'add file, delete file, copy, move, alias, resize, crop, rename, images, title, attribute', 'icon-picture', 0, 0, NULL, 0, 0),
(12, NULL, NULL, 'file, file attributes, title, attribute, description, rename', 'icon-cog', 0, 0, NULL, 0, 0),
(13, NULL, NULL, 'files, category, categories', 'icon-list-alt', 0, 0, NULL, 0, 0),
(14, NULL, NULL, 'new file set', 'icon-plus-sign', 1, 0, NULL, 0, 0),
(15, NULL, NULL, 'users, groups, people, find, delete user, remove user, change password, password', NULL, 0, 0, NULL, 0, 0),
(16, NULL, NULL, 'find, search, people, delete user, remove user, change password, password', 'icon-user', 0, 0, NULL, 0, 0),
(17, NULL, NULL, 'user, group, people, permissions, access, expire', 'icon-globe', 0, 0, NULL, 0, 0),
(18, NULL, NULL, 'user attributes, user data, gather data, registration data', 'icon-cog', 0, 0, NULL, 0, 0),
(19, NULL, NULL, 'new user, create', 'icon-plus-sign', 1, 0, NULL, 0, 0),
(20, NULL, NULL, 'new user group, new group, group, create', 'icon-plus', 1, 0, NULL, 0, 0),
(21, NULL, NULL, 'group set', 'icon-list', 0, 0, NULL, 0, 0),
(22, NULL, NULL, 'forms, log, error, email, mysql, exception, survey', NULL, 0, 0, NULL, 0, 0),
(23, NULL, NULL, 'hits, pageviews, visitors, activity', 'icon-signal', 0, 0, NULL, 0, 0),
(24, NULL, NULL, 'forms, questions, response, data', 'icon-briefcase', 0, 0, NULL, 0, 0),
(25, NULL, NULL, 'questions, quiz, response', 'icon-tasks', 0, 0, NULL, 0, 0),
(26, NULL, NULL, 'forms, log, error, email, mysql, exception, survey, history', 'icon-time', 0, 0, NULL, 0, 0),
(28, NULL, NULL, 'new theme, theme, active theme, change theme, template, css', 'icon-font', 0, 0, NULL, 0, 0),
(29, NULL, NULL, 'theme', NULL, 0, 0, NULL, 0, 0),
(30, NULL, NULL, 'page types', NULL, 0, 0, NULL, 0, 0),
(31, NULL, NULL, 'custom theme, change theme, custom css, css', NULL, 0, 0, NULL, 0, 0),
(32, NULL, NULL, 'page type defaults, global block, global area, starter, template', 'icon-file', 0, 0, NULL, 0, 0),
(34, NULL, NULL, 'page attributes, custom', 'icon-cog', 0, 0, NULL, 0, 0),
(35, NULL, NULL, 'single, page, custom, application', 'icon-wrench', 0, 0, NULL, 0, 0),
(36, NULL, NULL, 'add workflow, remove workflow', NULL, 0, 0, NULL, 0, 0),
(37, NULL, NULL, NULL, 'icon-list', 0, 0, NULL, 0, 0),
(38, NULL, NULL, NULL, 'icon-user', 0, 0, NULL, 0, 0),
(40, NULL, NULL, 'stacks, reusable content, scrapbook, copy, paste, paste block, copy block, site name, logo', 'icon-th', 0, 0, NULL, 0, 0),
(41, NULL, NULL, NULL, 'icon-lock', 0, 0, NULL, 0, 0),
(42, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0),
(43, NULL, NULL, 'block, refresh, custom', 'icon-wrench', 0, 0, NULL, 0, 0),
(44, NULL, NULL, NULL, NULL, 1, 0, NULL, 0, 0),
(45, NULL, NULL, NULL, NULL, 1, 0, NULL, 0, 0),
(46, NULL, NULL, 'add-on, addon, ecommerce, install, discussions, forums, themes, templates, blocks', NULL, 0, 0, NULL, 0, 0),
(47, NULL, NULL, 'update, upgrade', NULL, 0, 0, NULL, 0, 0),
(48, NULL, NULL, 'concrete5.org, my account, marketplace', NULL, 0, 0, NULL, 0, 0),
(49, NULL, NULL, 'buy theme, new theme, marketplace, template', NULL, 0, 0, NULL, 0, 0),
(50, NULL, NULL, 'buy addon, buy add on, buy add-on, purchase addon, purchase add on, purchase add-on, find addon, new addon, marketplace', NULL, 0, 0, NULL, 0, 0),
(51, NULL, NULL, NULL, NULL, 1, 0, NULL, 0, 0),
(53, NULL, NULL, 'website name, title', NULL, 0, 0, NULL, 0, 0),
(54, NULL, NULL, 'logo, favicon, iphone, icon, bookmark', NULL, 0, 0, NULL, 0, 0),
(55, NULL, NULL, 'tinymce, content block, fonts, editor, content, overlay', NULL, 0, 0, NULL, 0, 0),
(56, NULL, NULL, 'translate, translation, internationalization, multilingual', NULL, 0, 0, NULL, 0, 0),
(57, NULL, NULL, 'timezone, profile, locale', NULL, 0, 0, NULL, 0, 0),
(58, NULL, NULL, 'interface, quick nav, dashboard background, background image', NULL, 0, 0, NULL, 0, 0),
(60, NULL, NULL, 'vanity, pretty url, seo, pageview, view', NULL, 0, 0, NULL, 0, 0),
(61, NULL, NULL, 'bulk, seo, change keywords, engine, optimization, search', NULL, 0, 0, NULL, 0, 0),
(62, NULL, NULL, 'traffic, statistics, google analytics, quant, pageviews, hits', NULL, 0, 0, NULL, 0, 0),
(63, NULL, NULL, 'pretty, slug', NULL, 0, 0, NULL, 0, 0),
(64, NULL, NULL, 'turn off statistics, tracking, statistics, pageviews, hits', NULL, 0, 0, NULL, 0, 0),
(65, NULL, NULL, 'configure search, site search, search option', NULL, 0, 0, NULL, 0, 0),
(67, NULL, NULL, 'cache option, change cache, override, turn on cache, turn off cache, no cache, page cache, caching', NULL, 0, 0, NULL, 0, 0),
(68, NULL, NULL, 'cache option, turn off cache, no cache, page cache, caching', NULL, 0, 0, NULL, 0, 0),
(69, NULL, NULL, 'index search, reindex search, build sitemap, sitemap.xml, clear old versions, page versions, remove old', NULL, 0, 0, NULL, 0, 0),
(71, NULL, NULL, 'editors, hide site, offline, private, public, access', NULL, 0, 0, NULL, 0, 0),
(72, NULL, NULL, 'file options, file manager, upload, modify', NULL, 0, 0, NULL, 0, 0),
(73, NULL, NULL, 'security, files, media, extension, manager, upload', NULL, 0, 0, NULL, 0, 0),
(74, NULL, NULL, 'security, actions, administrator, admin, package, marketplace, search', NULL, 0, 0, NULL, 0, 0),
(77, NULL, NULL, 'security, lock ip, lock out, block ip, address, restrict, access', NULL, 0, 0, NULL, 0, 0),
(78, NULL, NULL, 'security, registration', NULL, 0, 0, NULL, 0, 0),
(79, NULL, NULL, 'antispam, block spam, security', NULL, 0, 0, NULL, 0, 0),
(80, NULL, NULL, 'lock site, under construction, hide, hidden', NULL, 0, 0, NULL, 0, 0),
(82, NULL, NULL, 'profile, login, redirect, specific, dashboard, administrators', NULL, 0, 0, NULL, 0, 0),
(83, NULL, NULL, 'member profile, member page, community, forums, social, avatar', NULL, 0, 0, NULL, 0, 0),
(84, NULL, NULL, 'signup, new user, community', NULL, 0, 0, NULL, 0, 0),
(85, NULL, NULL, 'smtp, mail settings', NULL, 0, 0, NULL, 0, 0),
(86, NULL, NULL, 'email server, mail settings, mail configuration, external, internal', NULL, 0, 0, NULL, 0, 0),
(87, NULL, NULL, 'email server, mail settings, mail configuration, private message, message system, import, email, message', NULL, 0, 0, NULL, 0, 0),
(88, NULL, NULL, 'attribute configuration', NULL, 0, 0, NULL, 0, 0),
(89, NULL, NULL, 'attributes, sets', NULL, 0, 0, NULL, 0, 0),
(90, NULL, NULL, 'attributes, types', NULL, 0, 0, NULL, 0, 0),
(91, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0),
(92, NULL, NULL, 'overrides, system info, debug, support, help', NULL, 0, 0, NULL, 0, 0),
(93, NULL, NULL, 'errors, exceptions, develop, support, help', NULL, 0, 0, NULL, 0, 0),
(94, NULL, NULL, 'email, logging, logs, smtp, pop, errors, mysql, log', NULL, 0, 0, NULL, 0, 0),
(95, NULL, NULL, 'security, alternate storage, hide files', NULL, 0, 0, NULL, 0, 0),
(96, NULL, NULL, 'network, proxy server', NULL, 0, 0, NULL, 0, 0),
(97, NULL, NULL, 'export, backup, database, sql, mysql, encryption, restore', NULL, 0, 0, NULL, 0, 0),
(99, NULL, NULL, 'upgrade, new version, update', NULL, 0, 0, NULL, 0, 0),
(100, NULL, NULL, 'export, database, xml, starting, points, schema, refresh, custom, tables', NULL, 0, 0, NULL, 0, 0),
(105, NULL, NULL, NULL, NULL, 1, 0, NULL, 0, 0),
(106, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0),
(122, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0),
(126, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0),
(139, '', '', '', '', 0, 0, '', 0, 0),
(151, NULL, NULL, '', NULL, 0, 0, NULL, 0, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
