CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_option` varchar(255) DEFAULT NULL,
  `config_value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ;#1861c#

INSERT INTO config VALUES("1","www_themex","bdaysuite");#1861c#
INSERT INTO config VALUES("2","www_themex_admin","@panel");#1861c#
INSERT INTO config VALUES("3","www_costume","bdaysuite");#1861c#
INSERT INTO config VALUES("4","www_costume_admin","adminzone");#1861c#
INSERT INTO config VALUES("5","site_name","SuperDomX");#1861c#
INSERT INTO config VALUES("6","site_moto","Websites Gone Easy!");#1861c#
INSERT INTO config VALUES("7","site_logo","");#1861c#

DROP TABLE costume_adminzone;#1861c#

CREATE TABLE `costume_adminzone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element` varchar(255) DEFAULT NULL,
  `state` varchar(40) DEFAULT NULL,
  `border` varchar(50) DEFAULT NULL,
  `color` varchar(15) DEFAULT NULL,
  `font-size` varchar(10) DEFAULT NULL,
  `font-family` varchar(100) DEFAULT NULL,
  `font-style` varchar(10) DEFAULT NULL,
  `font-weight` varchar(10) DEFAULT NULL,
  `background` blob,
  `background-color` varchar(255) DEFAULT NULL,
  `background-attachment` varchar(10) DEFAULT NULL,
  `background-image` blob,
  `background-repeat` varchar(25) DEFAULT NULL,
  `background-position` varchar(25) DEFAULT NULL,
  `margin` varchar(30) DEFAULT NULL,
  `padding` varchar(30) DEFAULT NULL,
  `z-index` varchar(10) DEFAULT NULL,
  `width` varchar(10) DEFAULT NULL,
  `height` varchar(10) DEFAULT NULL,
  `float` varchar(5) DEFAULT NULL,
  `display` varchar(20) DEFAULT NULL,
  `border-color` varchar(255) DEFAULT NULL,
  `border-style` varchar(20) DEFAULT NULL,
  `border-width` varchar(20) DEFAULT NULL,
  `cursor` varchar(15) DEFAULT NULL,
  `position` varchar(20) DEFAULT NULL,
  `left` varchar(7) DEFAULT NULL,
  `right` varchar(7) DEFAULT NULL,
  `bottom` varchar(7) DEFAULT NULL,
  `top` varchar(7) DEFAULT NULL,
  `-moz-border-radius` varchar(50) DEFAULT NULL,
  `-webkit-border-radius` varchar(50) DEFAULT NULL,
  `border-bottom` varchar(25) DEFAULT NULL,
  `border-bottom-color` varchar(10) DEFAULT NULL,
  `border-bottom-style` varchar(10) DEFAULT NULL,
  `border-bottom-width` varchar(10) DEFAULT NULL,
  `border-left` varchar(25) DEFAULT NULL,
  `border-left-color` varchar(10) DEFAULT NULL,
  `border-left-style` varchar(10) DEFAULT NULL,
  `border-left-width` varchar(10) DEFAULT NULL,
  `border-right` varchar(25) DEFAULT NULL,
  `border-right-color` varchar(10) DEFAULT NULL,
  `border-right-style` varchar(10) DEFAULT NULL,
  `border-right-width` varchar(10) DEFAULT NULL,
  `border-top` varchar(25) DEFAULT NULL,
  `border-top-color` varchar(10) DEFAULT NULL,
  `border-top-style` varchar(25) DEFAULT NULL,
  `border-top-width` varchar(25) DEFAULT NULL,
  `outline` varchar(20) DEFAULT NULL,
  `outline-color` varchar(10) DEFAULT NULL,
  `outline-style` varchar(10) DEFAULT NULL,
  `outline-width` varchar(10) DEFAULT NULL,
  `max-height` varchar(10) DEFAULT NULL,
  `min-height` varchar(10) DEFAULT NULL,
  `max-width` varchar(10) DEFAULT NULL,
  `min-width` varchar(10) DEFAULT NULL,
  `font-variant` varchar(20) DEFAULT NULL,
  `content` blob,
  `counter-increment` varchar(5) DEFAULT NULL,
  `counter-reset` varchar(5) DEFAULT NULL,
  `quotes` varchar(10) DEFAULT NULL,
  `list-style` varchar(10) DEFAULT NULL,
  `list-style-image` varchar(255) DEFAULT NULL,
  `list-style-position` varchar(15) DEFAULT NULL,
  `list-style-type` varchar(25) DEFAULT NULL,
  `margin-bottom` varchar(25) DEFAULT NULL,
  `margin-left` varchar(25) DEFAULT NULL,
  `margin-right` varchar(25) DEFAULT NULL,
  `margin-top` varchar(25) DEFAULT NULL,
  `padding-bottom` varchar(25) DEFAULT NULL,
  `padding-left` varchar(25) DEFAULT NULL,
  `padding-right` varchar(25) DEFAULT NULL,
  `padding-top` varchar(25) DEFAULT NULL,
  `clear` varchar(10) DEFAULT NULL,
  `overflow` varchar(10) DEFAULT NULL,
  `visibility` varchar(10) DEFAULT NULL,
  `page-break-after` varchar(10) DEFAULT NULL,
  `page-break-before` varchar(10) DEFAULT NULL,
  `page-break-inside` varchar(10) DEFAULT NULL,
  `border-collapse` varchar(10) DEFAULT NULL,
  `border-spacing` varchar(10) DEFAULT NULL,
  `caption-side` varchar(7) DEFAULT NULL,
  `empty-cells` varchar(7) DEFAULT NULL,
  `table-layout` varchar(7) DEFAULT NULL,
  `direction` varchar(7) DEFAULT NULL,
  `letter-spacing` varchar(10) DEFAULT NULL,
  `line-height` varchar(10) DEFAULT NULL,
  `text-align` varchar(10) DEFAULT NULL,
  `text-decoration` varchar(50) DEFAULT NULL,
  `text-indent` varchar(10) DEFAULT NULL,
  `text-shadow` varchar(25) DEFAULT NULL,
  `text-transform` varchar(25) DEFAULT NULL,
  `vertical-align` varchar(10) DEFAULT NULL,
  `white-space` varchar(10) DEFAULT NULL,
  `word-spacing` varchar(10) DEFAULT NULL,
  `opacity` varchar(7) DEFAULT NULL,
  `overflow-x` varchar(10) DEFAULT NULL,
  `overflow-y` varchar(10) DEFAULT NULL,
  `font` varchar(100) DEFAULT NULL,
  `-moz-box-shadow` varchar(50) DEFAULT NULL,
  `-webkit-box-shadow` varchar(50) DEFAULT NULL,
  `box-shadow` varchar(50) DEFAULT NULL,
  `border-radius` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `element` (`element`,`state`)
) ENGINE=MyISAM AUTO_INCREMENT=651 DEFAULT CHARSET=utf8;#1861c#

INSERT INTO costume_adminzone VALUES("1",".halfmoon ul","","","","","","","","","","","","","","","","","","","","block","","","","","absolute","","0","0","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","none","","","","","","","","","","","","","","","","","","","","","","","center","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_adminzone VALUES("2",".halfmoon a"," ","","#111","12px","verdana","","bold","","white","","url(/bin/images/bgs/strip/lightblue&white-bottom2top.png)","","center center","0","0","","","","left","inline-block","","","","pointer","inherit","","","","","100px 100px 0px 0px","100px 100px 0px 0px","2px solid transparent","","","","1px solid transparent","","","","1px solid transparent","","","","1px solid transparent","","","","","","","","","","","","","","","","","","","","","-2px","","","","","","","","","","","","","","","","","","","","","","center","none","","#0072f2 0px 1px 3px","","","","",".9","","","","","","","");#1861c#
INSERT INTO costume_adminzone VALUES("3",".halfmoon a","hover","","white","","","","","","white","","url(/bin/images/bgs/strip/skybluegradround-cthru.png) ","","","","","",""," ","","","","","","","","","","","","","","2px solid transparent","","","","1px solid transparent","","","","1px solid transparent","","","","1px solid transparent","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","#0072f2 0px 1px 3px","","","","","","","","","","","","");#1861c#
INSERT INTO costume_adminzone VALUES("4",".halfmoon li.selected a","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_adminzone VALUES("5",".halfmoon a","active","","","","","","","","","","","","","","","","","","","","","inset","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_adminzone VALUES("6",".halfmoon li a","","","","","","","","","","","","","","0px 2px 0px","5px 10px 0px","","100px","","","","","","","","","","","","","","","2px solid transparent","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_adminzone VALUES("7",".halfmoon li a","hover","","","","","","","","","","","","","0px 2px 0px","5px 10px 0px","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_adminzone VALUES("8",".halfmoon li a","active","","","","","","","","","","","","","0px 2px 0px","5px 10px 0px","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_adminzone VALUES("9","#moonmenu","","","","","","","","","","","","","","0px","0px","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_adminzone VALUES("10","#halfmoon","","","","","","","","","","","","","","","","","","","","","","","","","absolute","0","","0","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#


DROP TABLE costume_bdaysuite;#1861c#

CREATE TABLE `costume_bdaysuite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element` varchar(255) NOT NULL,
  `state` varchar(10) DEFAULT NULL,
  `border` varchar(50) DEFAULT NULL,
  `color` varchar(15) DEFAULT NULL,
  `font-size` varchar(10) DEFAULT NULL,
  `font-family` varchar(50) DEFAULT NULL,
  `font-style` varchar(10) DEFAULT NULL,
  `font-weight` varchar(10) DEFAULT NULL,
  `background` varchar(255) DEFAULT NULL,
  `background-color` varchar(15) DEFAULT NULL,
  `background-attachment` varchar(10) DEFAULT NULL,
  `background-image` varchar(255) DEFAULT NULL,
  `background-repeat` varchar(25) DEFAULT NULL,
  `background-position` varchar(25) DEFAULT NULL,
  `margin` varchar(30) DEFAULT NULL,
  `padding` varchar(30) DEFAULT NULL,
  `z-index` varchar(10) DEFAULT NULL,
  `width` varchar(10) DEFAULT NULL,
  `height` varchar(10) DEFAULT NULL,
  `float` varchar(5) DEFAULT NULL,
  `display` varchar(10) DEFAULT NULL,
  `border-color` varchar(10) DEFAULT NULL,
  `border-style` varchar(10) DEFAULT NULL,
  `border-width` varchar(10) DEFAULT NULL,
  `cursor` varchar(15) DEFAULT NULL,
  `position` varchar(20) DEFAULT NULL,
  `left` varchar(7) DEFAULT NULL,
  `right` varchar(7) DEFAULT NULL,
  `bottom` varchar(7) DEFAULT NULL,
  `top` varchar(7) DEFAULT NULL,
  `-moz-border-radius` varchar(30) DEFAULT NULL,
  `-webkit-border-radius` varchar(30) DEFAULT NULL,
  `border-bottom` varchar(25) DEFAULT NULL,
  `border-bottom-color` varchar(10) DEFAULT NULL,
  `border-bottom-style` varchar(10) DEFAULT NULL,
  `border-bottom-width` varchar(10) DEFAULT NULL,
  `border-left` varchar(25) DEFAULT NULL,
  `border-left-color` varchar(10) DEFAULT NULL,
  `border-left-style` varchar(10) DEFAULT NULL,
  `border-left-width` varchar(10) DEFAULT NULL,
  `border-right` varchar(25) DEFAULT NULL,
  `border-right-color` varchar(10) DEFAULT NULL,
  `border-right-style` varchar(10) DEFAULT NULL,
  `border-right-width` varchar(10) DEFAULT NULL,
  `border-top` varchar(25) DEFAULT NULL,
  `border-top-color` varchar(10) DEFAULT NULL,
  `border-top-style` varchar(25) DEFAULT NULL,
  `border-top-width` varchar(25) DEFAULT NULL,
  `outline` varchar(20) DEFAULT NULL,
  `outline-color` varchar(10) DEFAULT NULL,
  `outline-style` varchar(10) DEFAULT NULL,
  `outline-width` varchar(10) DEFAULT NULL,
  `max-height` varchar(10) DEFAULT NULL,
  `min-height` varchar(10) DEFAULT NULL,
  `max-width` varchar(10) DEFAULT NULL,
  `min-width` varchar(10) DEFAULT NULL,
  `font-variant` varchar(20) DEFAULT NULL,
  `content` blob,
  `counter-increment` varchar(5) DEFAULT NULL,
  `counter-reset` varchar(5) DEFAULT NULL,
  `quotes` varchar(10) DEFAULT NULL,
  `list-style` varchar(10) DEFAULT NULL,
  `list-style-image` varchar(255) DEFAULT NULL,
  `list-style-position` varchar(15) DEFAULT NULL,
  `list-style-type` varchar(25) DEFAULT NULL,
  `margin-bottom` varchar(25) DEFAULT NULL,
  `margin-left` varchar(25) DEFAULT NULL,
  `margin-right` varchar(25) DEFAULT NULL,
  `margin-top` varchar(25) DEFAULT NULL,
  `padding-bottom` varchar(25) DEFAULT NULL,
  `padding-left` varchar(25) DEFAULT NULL,
  `padding-right` varchar(25) DEFAULT NULL,
  `padding-top` varchar(25) DEFAULT NULL,
  `clear` varchar(10) DEFAULT NULL,
  `overflow` varchar(10) DEFAULT NULL,
  `visibility` varchar(10) DEFAULT NULL,
  `page-break-after` varchar(10) DEFAULT NULL,
  `page-break-before` varchar(10) DEFAULT NULL,
  `page-break-inside` varchar(10) DEFAULT NULL,
  `border-collapse` varchar(10) DEFAULT NULL,
  `border-spacing` varchar(10) DEFAULT NULL,
  `caption-side` varchar(7) DEFAULT NULL,
  `empty-cells` varchar(7) DEFAULT NULL,
  `table-layout` varchar(7) DEFAULT NULL,
  `direction` varchar(7) DEFAULT NULL,
  `letter-spacing` varchar(10) DEFAULT NULL,
  `line-height` varchar(10) DEFAULT NULL,
  `text-align` varchar(10) DEFAULT NULL,
  `text-decoration` varchar(50) DEFAULT NULL,
  `text-indent` varchar(10) DEFAULT NULL,
  `text-shadow` varchar(25) DEFAULT NULL,
  `text-transform` varchar(25) DEFAULT NULL,
  `vertical-align` varchar(10) DEFAULT NULL,
  `white-space` varchar(10) DEFAULT NULL,
  `word-spacing` varchar(10) DEFAULT NULL,
  `opacity` varchar(7) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;#1861c#

INSERT INTO costume_bdaysuite VALUES("1","#html_arm_left","","","","","","","","","","","","","","","","0","","","left","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_bdaysuite VALUES("2","HTML","","","","13px","tahoma","","","","","","","","","","","0","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_bdaysuite VALUES("3","#html_arm_right","","","","","","","","","","","","","","","","0","","","right","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_bdaysuite VALUES("4","#shortcuts li","","","","","","","","","","","","","","","","0","","","","inline","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_bdaysuite VALUES("5","#main_menu","","","","","","","","","","","","","","","","0","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","none","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_bdaysuite VALUES("6","","","","","45px","verdana","","bold","","","","","","","","","0","","","left","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_bdaysuite VALUES("7","#main_menu UL","","","","","","","","","","","","","","","","0","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_bdaysuite VALUES("8","","","","","","","","","","","","","","","","","0","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","none","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_bdaysuite VALUES("9","DIV","","1px inset #ffffff","","","","","","","","","","","","5px","5px","0","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_bdaysuite VALUES("10","","","","","75px","","","","","","","","","","","","0","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_bdaysuite VALUES("11","","","","","20px","","","bold","","","","","","","","","0","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_bdaysuite VALUES("12","","","","","75px","","","","","","","","","","","","0","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_bdaysuite VALUES("13","","","","","20px","","","bolx","","","","","","","","","0","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_bdaysuite VALUES("14","","","","","","","","","","","","","","","","","0","","","left","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_bdaysuite VALUES("15","#shortcuts","","","","","","","","","","","","","","","","0","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");#1861c#
INSERT INTO costume_bdaysuite VALUES("16","","","","","","","","","","","","","","","","","0","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","both","","","","","","","","","","","","","","","","","","","","","","");#1861c#


CREATE TABLE `costumez` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `table_name` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `credits_url` varchar(255) DEFAULT NULL,
  `describe` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;#1861c#
INSERT INTO costumez VALUES("1","@dmin Zone","v1.563","adminzone","SuperDomX","www.xtiv.net","");#1861c#
INSERT INTO costumez VALUES("2","Birthday Suite","v1.0","bdaysuite","SuperDomX","www.xtiv.net","");#1861c#