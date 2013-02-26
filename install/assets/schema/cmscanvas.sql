--
-- Database: `cmscanvas_trunk`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_group_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `url_title` varchar(255) NOT NULL,
  `description` text,
  `tag_id` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `target` varchar(50) DEFAULT NULL,
  `subcategories_visibility` enum('show','current_trail','hide') NOT NULL,
  `hide` tinyint(1) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_group_id` (`category_group_id`,`parent_id`),
  KEY `url_title` (`url_title`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `categories_entries`
--

CREATE TABLE IF NOT EXISTS `categories_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `entry_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`,`entry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `category_groups`
--

CREATE TABLE IF NOT EXISTS `category_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `content_fields`
--

CREATE TABLE IF NOT EXISTS `content_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_type_id` int(11) NOT NULL,
  `content_field_type_id` int(11) NOT NULL,
  `label` varchar(50) NOT NULL,
  `short_tag` varchar(50) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `options` text,
  `settings` text,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `content_field_type_id` (`content_field_type_id`),
  KEY `content_type_id` (`content_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `content_fields`
--

INSERT INTO `content_fields` (`id`, `content_type_id`, `content_field_type_id`, `label`, `short_tag`, `required`, `options`, `settings`, `sort`) VALUES
(1, 1, 1, 'Left Column', 'left_column', 0, NULL, NULL, 1),
(2, 1, 1, 'Right Column', 'right_column', 0, NULL, NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `content_field_types`
--

CREATE TABLE IF NOT EXISTS `content_field_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `model_name` varchar(50) NOT NULL,
  `datatype` varchar(50) NOT NULL DEFAULT 'text',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `content_field_types`
--

INSERT INTO `content_field_types` (`id`, `title`, `model_name`, `datatype`) VALUES
(1, 'CKEditor', 'ckeditor', 'text'),
(2, 'TinyMCE', 'tinymce', 'text'),
(3, 'Text', 'text', 'text'),
(4, 'Dropdown', 'dropdown', 'text'),
(5, 'Radio', 'radio', 'text'),
(6, 'Textarea', 'textarea', 'text'),
(7, 'HTML', 'html', 'text'),
(8, 'Image', 'image', 'text'),
(9, 'File', 'file', 'text'),
(10, 'Date', 'date', 'date'),
(11, 'Date Time', 'datetime', 'datetime'),
(12, 'Page URL', 'page_url', 'text'),
(13, 'Gallery', 'gallery_id', 'int'),
(14, 'Checkbox', 'checkbox', 'text'),
(15, 'Integer', 'text', 'int');

-- --------------------------------------------------------

--
-- Table structure for table `content_types`
--

CREATE TABLE IF NOT EXISTS `content_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `short_name` varchar(50) NOT NULL,
  `layout` text,
  `page_head` text,
  `theme_layout` varchar(50) DEFAULT NULL,
  `dynamic_route` varchar(255) DEFAULT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `access` tinyint(1) NOT NULL,
  `restrict_to` text,
  `restrict_admin_access` tinyint(1) NOT NULL DEFAULT '0',
  `enable_versioning` tinyint(1) NOT NULL,
  `max_revisions` int(11) NOT NULL,
  `entries_allowed` int(11) DEFAULT NULL,
  `category_group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `short_name` (`short_name`),
  KEY `dynamic_route` (`dynamic_route`),
  KEY `category_group_id` (`category_group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `content_types`
--

INSERT INTO `content_types` (`id`, `title`, `short_name`, `layout`, `page_head`, `theme_layout`, `dynamic_route`, `required`, `access`, `restrict_to`, `restrict_admin_access`, `enable_versioning`, `max_revisions`, `entries_allowed`, `category_group_id`) VALUES
(1, 'Page', 'page', '<div id="left_column">\n    <article>\n	<h1>{{ title }}</h1>\n	{{ left_column }}\n    </article>\n</div>\n\n<div id="right_column">\n    <aside>\n	{{ right_column }}\n    </aside>\n</div>', NULL, 'default', NULL, 0, 0, NULL, 0, 1, 5, NULL, NULL),
(2, 'Contact Page', 'contact_page', '<h1>{{ title }}</h1>\r\n{{ contact:form }}', NULL, 'default', NULL, 0, 0, NULL, 0, 1, 5, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `content_types_admin_groups`
--

CREATE TABLE IF NOT EXISTS `content_types_admin_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_type_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `content_type_id` (`content_type_id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `entries`
--

CREATE TABLE IF NOT EXISTS `entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `url_title` varchar(100) DEFAULT NULL,
  `required` tinyint(4) NOT NULL DEFAULT '0',
  `content_type_id` int(11) NOT NULL,
  `status` enum('published','draft') NOT NULL DEFAULT 'published',
  `meta_title` varchar(65) DEFAULT NULL,
  `meta_description` text,
  `meta_keywords` text,
  `created_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `content_type_id` (`content_type_id`),
  KEY `slug` (`slug`),
  KEY `url_title` (`url_title`),
  KEY `author_id` (`author_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `entries`
--

INSERT INTO `entries` (`id`, `slug`, `title`, `url_title`, `required`, `content_type_id`, `status`, `meta_title`, `meta_description`, `meta_keywords`, `created_date`, `modified_date`, `author_id`) VALUES
(1, 'home', 'Home', NULL, 0, 1, 'published', NULL, NULL, NULL, '2012-03-06 21:07:07', '2012-03-11 16:13:42', NULL),
(2, NULL, 'Page Not Found', NULL, 0, 1, 'published', NULL, NULL, NULL, '2012-03-06 22:55:06', '2012-03-06 22:55:20', NULL),
(3, 'contact', 'Contact', NULL, 0, 2, 'published', NULL, NULL, NULL, '2012-03-07 21:45:48', '2012-03-07 21:46:56', NULL),
(4, 'about', 'About', NULL, 0, 1, 'published', NULL, NULL, NULL, '2012-03-11 16:06:40', '2012-03-11 16:12:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `entries_data`
--

CREATE TABLE IF NOT EXISTS `entries_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) NOT NULL,
  `field_id_1` text,
  `field_id_2` text,
  PRIMARY KEY (`id`),
  KEY `entry_id` (`entry_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `entries_data`
--

INSERT INTO `entries_data` (`id`, `entry_id`, `field_id_1`, `field_id_2`) VALUES
(1, 1, '<p>\n	Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna <a href="#">aliquam</a> erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>\n<p>\n	Sed ut perspiciatis unde <a href="#">omnis</a> iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>\n<h1>\n	H1 Tag</h1>\n<h2>\n	H2 Tag</h2>\n<h3>\n	H3 Tag</h3>\n<h4>\n	H4 Tag</h4>\n<p>\n	<strong>Strong</strong></p>\n<ul>\n	<li>\n		List Item 1\n		<ul>\n			<li>\n				Indented Item 1</li>\n			<li>\n				Indented Item 2</li>\n		</ul>\n	</li>\n	<li>\n		List Item 2</li>\n	<li>\n		List Item 3</li>\n	<li>\n		List Item 4</li>\n</ul>', '<h2>\n	Lorem Ipsum</h2>\n<p>\n	Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>\n<p>\n	Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>'),
(2, 2, NULL, NULL),
(3, 3, NULL, NULL),
(4, 4, '<p>\n	Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>\n<p>\n	Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>\n<p>\n	Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>\n<p>\n	Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>\n<p>\n	Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>\n<p>\n	Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>\n<p>\n	Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>\n<p>\n	Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>', '<h2>\n	Lorem Ipsum</h2>\n<p>\n	Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>\n<p>\n	Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>');

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE IF NOT EXISTS `galleries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `resize` tinyint(4) NOT NULL,
  `image_width` int(11) DEFAULT NULL,
  `image_height` int(11) DEFAULT NULL,
  `image_crop` tinyint(4) DEFAULT NULL,
  `thumbs` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

CREATE TABLE IF NOT EXISTS `gallery_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(100) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(250) NOT NULL,
  `hide` tinyint(4) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gallery_id` (`gallery_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` varchar(15) NOT NULL,
  `permissions` text,
  `required` tinyint(4) NOT NULL DEFAULT '0',
  `modifiable_permissions` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `type`, `permissions`, `required`, `modifiable_permissions`) VALUES
(1, 'Super Admin', 'super_admin', NULL, 1, 0),
(2, 'Administrator', 'administrator', 'a:1:{s:6:"access";a:12:{i:0;s:23:"sitemin/content/entries";i:1;s:19:"sitemin/navigations";i:2;s:17:"sitemin/galleries";i:3;s:13:"sitemin/users";i:4;s:20:"sitemin/users/groups";i:5;s:21:"sitemin/content/types";i:6;s:24:"sitemin/content/snippets";i:7;s:18:"sitemin/categories";i:8;s:29:"sitemin/settings/theme-editor";i:9;s:33:"sitemin/settings/general-settings";i:10;s:28:"sitemin/settings/clear-cache";i:11;s:28:"sitemin/settings/server-info";}}', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `navigations`
--

CREATE TABLE IF NOT EXISTS `navigations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `navigations`
--

INSERT INTO `navigations` (`id`, `title`, `required`) VALUES
(1, 'Main Navigation', 0);

-- --------------------------------------------------------

--
-- Table structure for table `navigation_items`
--

CREATE TABLE IF NOT EXISTS `navigation_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(25) NOT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `tag_id` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `target` varchar(50) DEFAULT NULL,
  `parent_id` int(11) NOT NULL,
  `navigation_id` int(11) NOT NULL,
  `subnav_visibility` enum('show','current_trail','hide') NOT NULL,
  `hide` tinyint(4) NOT NULL DEFAULT '0',
  `disable_current` tinyint(1) NOT NULL DEFAULT '0',
  `disable_current_trail` tinyint(1) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `navigation_items`
--

INSERT INTO `navigation_items` (`id`, `type`, `entry_id`, `title`, `url`, `tag_id`, `class`, `target`, `parent_id`, `navigation_id`, `subnav_visibility`, `hide`, `disable_current`, `disable_current_trail`, `sort`) VALUES
(1, 'page', 1, NULL, NULL, NULL, NULL, NULL, 0, 1, 'show', 0, 0, 0, 0),
(2, 'page', 4, NULL, NULL, NULL, NULL, NULL, 1, 1, 'show', 0, 0, 0, 1),
(3, 'page', 3, NULL, NULL, NULL, NULL, NULL, 0, 1, 'show', 0, 0, 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `revisions`
--

CREATE TABLE IF NOT EXISTS `revisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `revision_resource_type_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `content_type_id` int(11) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `author_name` varchar(150) NOT NULL,
  `revision_date` datetime NOT NULL,
  `revision_data` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `revision_resource_type_id` (`revision_resource_type_id`),
  KEY `content_type_id` (`content_type_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `revisions`
--

INSERT INTO `revisions` (`id`, `revision_resource_type_id`, `resource_id`, `content_type_id`, `author_id`, `author_name`, `revision_date`, `revision_data`) VALUES
(1, 1, 1, 1, 1, 'Albert Einstein', '2012-03-11 16:26:38', 'a:10:{s:5:"title";s:4:"Home";s:10:"field_id_2";s:992:"<p>\n  Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna <a href="#">aliquam</a> erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>\n<p>\n Sed ut perspiciatis unde <a href="#">omnis</a> iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>\n<h1>\n  H1 Tag</h1>\n<h2>\n H2 Tag</h2>\n<h3>\n H3 Tag</h3>\n<h4>\n H4 Tag</h4>\n<p>\n  <strong>Strong</strong></p>\n<ul>\n <li>\n    List Item 1\n   <ul>\n      <li>\n        Indented Item 1</li>\n      <li>\n        Indented Item 2</li>\n    </ul>\n </li>\n <li>\n    List Item 2</li>\n  <li>\n    List Item 3</li>\n  <li>\n    List Item 4</li>\n</ul>";s:10:"field_id_1";s:684:"<h2>\n  Lorem Ipsum</h2>\n<p>\n Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>\n<p>\n Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>";s:4:"slug";s:4:"home";s:10:"meta_title";s:0:"";s:13:"meta_keywords";s:0:"";s:16:"meta_description";s:0:"";s:6:"status";s:9:"published";s:15:"content_type_id";s:1:"1";s:12:"created_date";s:22:"03/06/2012 09:07:07 pm";}'),
(2, 1, 4, 1, 1, 'Albert Einstein', '2012-03-11 16:27:01', 'a:10:{s:5:"title";s:5:"About";s:10:"field_id_2";s:2647:"<p>\n  Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>\n<p>\n Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>\n<p>\n Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>\n<p>\n Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>\n<p>\n Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>\n<p>\n Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>\n<p>\n Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>\n<p>\n Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>";s:10:"field_id_1";s:684:"<h2>\n Lorem Ipsum</h2>\n<p>\n Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>\n<p>\n Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>";s:4:"slug";s:5:"about";s:10:"meta_title";s:0:"";s:13:"meta_keywords";s:0:"";s:16:"meta_description";s:0:"";s:6:"status";s:9:"published";s:15:"content_type_id";s:1:"1";s:12:"created_date";s:22:"03/11/2012 04:06:40 pm";}'),
(3, 1, 3, 2, 1, 'Albert Einstein', '2012-03-11 16:27:31', 'a:8:{s:5:"title";s:7:"Contact";s:4:"slug";s:7:"contact";s:10:"meta_title";s:0:"";s:13:"meta_keywords";s:0:"";s:16:"meta_description";s:0:"";s:6:"status";s:9:"published";s:15:"content_type_id";s:1:"2";s:12:"created_date";s:22:"03/07/2012 09:45:48 pm";}'),
(4, 1, 2, 1, 1, 'Albert Einstein', '2012-03-11 16:27:52', 'a:10:{s:5:"title";s:14:"Page Not Found";s:10:"field_id_2";s:0:"";s:10:"field_id_1";s:0:"";s:4:"slug";s:0:"";s:10:"meta_title";s:0:"";s:13:"meta_keywords";s:0:"";s:16:"meta_description";s:0:"";s:6:"status";s:9:"published";s:15:"content_type_id";s:1:"1";s:12:"created_date";s:22:"03/06/2012 10:55:06 pm";}'),
(5, 2, 2, NULL, 1, 'Administrator', '2013-01-01 00:00:00', 'a:12:{s:6:"layout";s:39:"<h1>{{ title }}</h1>\n{{ contact:form }}";s:9:"page_head";s:0:"";s:5:"title";s:12:"Contact Page";s:10:"short_name";s:12:"contact_page";s:12:"theme_layout";s:7:"default";s:13:"dynamic_route";s:0:"";s:17:"enable_versioning";s:1:"1";s:13:"max_revisions";s:1:"5";s:15:"entries_allowed";s:0:"";s:17:"category_group_id";s:0:"";s:21:"restrict_admin_access";s:1:"0";s:6:"access";s:1:"0";}'),
(6, 2, 1, NULL, 1, 'Administrator', '2013-01-01 00:00:00', 'a:12:{s:6:"layout";s:176:"<div id="left_column">\n    <article>\n <h1>{{ title }}</h1>\n  {{ left_column }}\n    </article>\n</div>\n\n<div id="right_column">\n    <aside>\n {{ right_column }}\n    </aside>\n</div>";s:9:"page_head";s:0:"";s:5:"title";s:4:"Page";s:10:"short_name";s:4:"page";s:12:"theme_layout";s:7:"default";s:13:"dynamic_route";s:0:"";s:17:"enable_versioning";s:1:"1";s:13:"max_revisions";s:1:"5";s:15:"entries_allowed";s:0:"";s:17:"category_group_id";s:0:"";s:21:"restrict_admin_access";s:1:"0";s:6:"access";s:1:"0";}');

-- --------------------------------------------------------

--
-- Table structure for table `revision_resource_types`
--

CREATE TABLE IF NOT EXISTS `revision_resource_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `key_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key_name` (`key_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `revision_resource_types`
--

INSERT INTO `revision_resource_types` (`id`, `name`, `key_name`) VALUES
(1, 'Entry', 'ENTRY'),
(2, 'Content Type', 'CONTENT_TYPE'),
(3, 'Snippet', 'SNIPPET');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) NOT NULL,
  `value` varchar(255) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `slug`, `value`, `module`) VALUES
(1, 'site_name', 'CMS Canvas', NULL),
(2, 'ga_account_id', '', NULL),
(3, 'suspend', '0', NULL),
(4, 'enable_admin_toolbar', '1', NULL),
(10, 'custom_404', '2', 'content'),
(6, 'site_homepage', '1', 'content'),
(7, 'enable_registration', '0', 'users'),
(8, 'default_group', '2', 'users'),
(9, 'email_activation', '0', 'users'),
(11, 'ga_email', '', NULL),
(12, 'ga_password', '', NULL),
(13, 'ga_profile_id', '', NULL),
(14, 'theme', 'default', NULL),
(15, 'layout', 'default', NULL),
(16, 'enable_profiler', '0', NULL),
(17, 'notification_email', '', NULL),
(18, 'editor_stylesheet', 'assets/css/content.css', NULL),
(19, 'enable_inline_editing', '0', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `snippets`
--

CREATE TABLE IF NOT EXISTS `snippets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `short_name` varchar(50) NOT NULL,
  `snippet` text NOT NULL,
  PRIMARY KEY (`id`)
  KEY `short_name` (`short_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `address` varchar(100) NOT NULL,
  `address2` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `zip` varchar(15) NOT NULL,
  `group_id` int(11) NOT NULL,
  `enabled` tinyint(4) NOT NULL DEFAULT '1',
  `activated` tinyint(4) NOT NULL DEFAULT '1',
  `activation_code` varchar(32) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;