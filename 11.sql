CREATE DATABASE tp1229 CHARSET utf8 ;
USE tp1229 ;
 
# 供货商表
CREATE TABLE supplier (
  id SMALLINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR (50) NOT NULL DEFAULT '' COMMENT '名称',
  intro TEXT COMMENT '简介',
  sort TINYINT NOT NULL DEFAULT 20 COMMENT '排序 数字越小越靠前',
  `status` TINYINT NOT NULL DEFAULT 1 COMMENT '状态-1删除   0隐藏   1正常'
) ENGINE = MYISAM COMMENT '供货商' ;
 
INSERT INTO supplier VALUES(NULL,'北京供货商','北京供货商的简介',20,1);
INSERT INTO supplier VALUES(NULL,'上海供货商','上海供货商的简介',20,1);
INSERT INTO supplier VALUES(NULL,'成都供货商','成都供货商的简介',20,1);
INSERT INTO supplier VALUES(NULL,'武汉供货商','武汉供货商的简介',20,1);
INSERT INTO supplier VALUES(NULL,'重庆供货商','重庆供货商的简介',20,1);

#article_category(文章分类)
CREATE TABLE article_category(
        `id` TINYINT UNSIGNED  PRIMARY KEY AUTO_INCREMENT,
        `name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '名称',
        `intro` TEXT COMMENT '简介@textarea',
        `status` TINYINT NOT NULL DEFAULT 1 COMMENT '状态@radio|1=是&0=否',
        `sort` TINYINT  NOT NULL DEFAULT 20 COMMENT '排序',
        `is_help` TINYINT NOT NULL DEFAULT 1 COMMENT '是否是帮助相关的分类'
)ENGINE=MYISAM COMMENT '文章分类';

#article(文章)
CREATE TABLE article(
        `id` INT UNSIGNED  PRIMARY KEY AUTO_INCREMENT,
        `name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '名称',
        `article_category_id` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '文章分类',
        `intro` TEXT COMMENT '简介@textarea',
        `status` TINYINT NOT NULL DEFAULT 1 COMMENT '状态@radio|1=是&0=否',
        `sort` TINYINT  NOT NULL DEFAULT 20 COMMENT '排序',
        `inputtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '录入时间',
        KEY(article_category_id)
)ENGINE=MYISAM COMMENT '文章'
 
 
#article_content(文章内容)
CREATE TABLE article_content(
        `article_id` INT UNSIGNED  PRIMARY KEY,
      `content` TEXT COMMENT '文章内容'
)ENGINE=MYISAM COMMENT '文章内容';

#goods_category(商品分类)
CREATE TABLE goods_category (
  id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR (50) NOT NULL DEFAULT '' COMMENT '名称',
  parent_id TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '父分类',
  lft SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '左边界',
  rght SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '右边界',
  `level` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '级别',
  intro TEXT COMMENT '简介@textarea',
  `status` TINYINT NOT NULL DEFAULT 1 COMMENT '状态@radio|1=是&0=否',
  
  INDEX (parent_id),
  INDEX (lft, rght)
) ENGINE = MYISAM COMMENT '商品分类'

INSERT INTO goods_category VALUES(1,'平板电视',9,3,4,3,'',1);
INSERT INTO goods_category VALUES(2,'空调',9,5,6,3,'',1);
INSERT INTO goods_category VALUES(3,'冰箱',9,7,8,3,'',1);
INSERT INTO goods_category VALUES(4,'取暖器',8,11,14,3,'',1);
INSERT INTO goods_category VALUES(5,'净化器',8,15,16,3,'',1);
INSERT INTO goods_category VALUES(6,'加湿器',8,17,18,3,'',1);
INSERT INTO goods_category VALUES(7,'小太阳',4,12,13,4,'',1);
INSERT INTO goods_category VALUES(8,'生活电器',10,10,19,2,'',1);
INSERT INTO goods_category VALUES(9,'大家电',10,2,9,2,'',1);
INSERT INTO goods_category VALUES(10,'家用电器',0,1,20,1,'',1);

#商品基本信息表
CREATE TABLE goods (
  `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR (50) NOT NULL DEFAULT '' COMMENT '名称',
  `sn` VARCHAR (20) NOT NULL DEFAULT '' COMMENT '货号',  # SN20150825000000000id
  `logo` VARCHAR (50) NOT NULL DEFAULT '' COMMENT '商品LOGO',
  `goods_category_id` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品分类',
  `is_on_sale` TINYINT NOT NULL DEFAULT 1 COMMENT '是否上架',  #1表示上架  0:不上架
  `status` TINYINT NOT NULL DEFAULT 1 COMMENT '状态@radio|1=是&0=否',
  `sort` TINYINT NOT NULL DEFAULT 20 COMMENT '排序',
  `inputtime` INT NOT NULL DEFAULT 0 COMMENT '录入时间',
  INDEX (`goods_category_id`),
  INDEX (`brand_id`),
  INDEX (`supplier_id`)
) ENGINE = INNODB COMMENT '商品'
 
#商品描述表
CREATE TABLE goods_intro (
  `goods_id` BIGINT PRIMARY KEY COMMENT '商品ID',
  `content` TEXT COMMENT '商品描述'
) ENGINE = INNODB COMMENT '商品描述' 

# 每天创建的商品个数
;CREATE TABLE goods_num (
 `date` DATE PRIMARY KEY,
 num SMALLINT UNSIGNED 
)CHARSET utf8 ENGINE INNODB 

;TRUNCATE goods_num

##商品相册
;CREATE TABLE `goods_gallery` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `goods_id` BIGINT(20) DEFAULT NULL COMMENT '商品ID',
  `path` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '商品图片地址',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8 COMMENT='商品相册'

#permission(权限表)
;CREATE TABLE permission (
  `id` SMALLINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR (50) NOT NULL DEFAULT '' COMMENT '名称',
  `path` VARCHAR (50) NOT NULL DEFAULT '' COMMENT 'URL',
  `parent_id` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '父分类',
  `lft` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '左边界',
  `rght` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '右边界',
  `level` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '级别',
  `intro` TEXT COMMENT '简介@textarea',
  `status` TINYINT NOT NULL DEFAULT 1 COMMENT '状态@radio|1=是&0=否',
  `sort` TINYINT NOT NULL DEFAULT 20 COMMENT '排序',
  INDEX (parent_id),
  INDEX (lft, rght)
) ENGINE = INNODB COMMENT '权限'
 
 
#role(角色表)
;CREATE TABLE role (
  `id` TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR (50) NOT NULL DEFAULT '' COMMENT '名称',
  `intro` TEXT COMMENT '简介@textarea',
  `status` TINYINT NOT NULL DEFAULT 1 COMMENT '状态@radio|1=是&0=否',
  `sort` TINYINT NOT NULL DEFAULT 20 COMMENT '排序'
) ENGINE = INNODB COMMENT '角色'
 
 
#role_permission(角色权限表)
;CREATE TABLE role_permission (
  `role_id` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  `permission_id` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '权限ID',
  INDEX (`role_id`)
) ENGINE = INNODB COMMENT '角色权限关系'
 
 
#admin(管理员表)
;CREATE TABLE admin (
  `id` TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR (50) NOT NULL DEFAULT '' COMMENT '用户名' UNIQUE,
  `password` CHAR(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` CHAR(6) NOT NULL DEFAULT '' COMMENT '盐',
  `email` VARCHAR (30) NOT NULL DEFAULT '' COMMENT '邮箱' UNIQUE,
  `add_time` INT NOT NULL DEFAULT 0 COMMENT '注册时间',
  `last_login_time` INT NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `last_login_ip` BIGINT NOT NULL DEFAULT 0 COMMENT '最后登录IP'
) ENGINE = INNODB COMMENT '管理员'
 
 
#admin_role(管理员角色)
;CREATE TABLE admin_role (
  `admin_id` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '管理员ID',
  `role_id` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  INDEX (`admin_id`)
) ENGINE = INNODB COMMENT '管理员角色关系'
 
#admin_permission(额外权限)
;CREATE TABLE admin_permission (
  `admin_id` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '管理员ID',
  `permission_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '权限ID',
  INDEX (`admin_id`)
) ENGINE = INNODB COMMENT '额外权限' 

#menu(菜单表)
;CREATE TABLE menu (
  `id` TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR (50) NOT NULL DEFAULT '' COMMENT '名称',
  `path` VARCHAR (50) NOT NULL DEFAULT '' COMMENT 'path:module/controller/action',
  `parent_id` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '父分类',
  `lft` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '左边界',
  `rght` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '右边界',
  `level` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '级别',
  `intro` TEXT COMMENT '简介@textarea',
  `status` TINYINT NOT NULL DEFAULT 1 COMMENT '状态@radio|1=是&0=否',
  `sort` TINYINT NOT NULL DEFAULT 20 COMMENT '排序',
  INDEX (`parent_id`),
  INDEX (`lft`, `rght`)
) ENGINE = INNODB COMMENT '菜单表'
 
 
#menu(菜单和权限的关系)
;CREATE TABLE menu_permission (
  `menu_id` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '菜单',
  `permission_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '权限ID',
  KEY (`permission_id`)
) ENGINE = INNODB COMMENT '菜单权限' 

#CREATE TABLE admin_token (
  admin_id INT UNSIGNED PRIMARY KEY,
  token CHAR(40)
) CHARSET utf8 ;

#创建一个令牌表,用于保存用户的令牌信息
CREATE TABLE admin_token (
  admin_id INT UNSIGNED PRIMARY KEY,
  token CHAR(40)
) CHARSET utf8 ;
