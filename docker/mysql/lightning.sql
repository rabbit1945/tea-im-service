/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50744
Source Host           : localhost:3307
Source Database       : lightning

Target Server Type    : MYSQL
Target Server Version : 50744
File Encoding         : 65001

Date: 2023-12-13 22:41:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for webim_admin_extensions
-- ----------------------------
DROP TABLE IF EXISTS `webim_admin_extensions`;
CREATE TABLE `webim_admin_extensions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_enabled` tinyint(4) NOT NULL DEFAULT '0',
  `options` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `webim_admin_extensions_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_admin_extension_histories
-- ----------------------------
DROP TABLE IF EXISTS `webim_admin_extension_histories`;
CREATE TABLE `webim_admin_extension_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `version` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `detail` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `webim_admin_extension_histories_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `webim_admin_menu`;
CREATE TABLE `webim_admin_menu` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uri` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `show` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_admin_permissions
-- ----------------------------
DROP TABLE IF EXISTS `webim_admin_permissions`;
CREATE TABLE `webim_admin_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `http_path` text COLLATE utf8mb4_unicode_ci,
  `order` int(11) NOT NULL DEFAULT '0',
  `parent_id` bigint(20) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `webim_admin_permissions_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_admin_permission_menu
-- ----------------------------
DROP TABLE IF EXISTS `webim_admin_permission_menu`;
CREATE TABLE `webim_admin_permission_menu` (
  `permission_id` bigint(20) NOT NULL,
  `menu_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `webim_admin_permission_menu_permission_id_menu_id_unique` (`permission_id`,`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_admin_roles
-- ----------------------------
DROP TABLE IF EXISTS `webim_admin_roles`;
CREATE TABLE `webim_admin_roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `webim_admin_roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_admin_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `webim_admin_role_menu`;
CREATE TABLE `webim_admin_role_menu` (
  `role_id` bigint(20) NOT NULL,
  `menu_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `webim_admin_role_menu_role_id_menu_id_unique` (`role_id`,`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_admin_role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `webim_admin_role_permissions`;
CREATE TABLE `webim_admin_role_permissions` (
  `role_id` bigint(20) NOT NULL,
  `permission_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `webim_admin_role_permissions_role_id_permission_id_unique` (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_admin_role_users
-- ----------------------------
DROP TABLE IF EXISTS `webim_admin_role_users`;
CREATE TABLE `webim_admin_role_users` (
  `role_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `webim_admin_role_users_role_id_user_id_unique` (`role_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_admin_settings
-- ----------------------------
DROP TABLE IF EXISTS `webim_admin_settings`;
CREATE TABLE `webim_admin_settings` (
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_admin_users
-- ----------------------------
DROP TABLE IF EXISTS `webim_admin_users`;
CREATE TABLE `webim_admin_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `webim_admin_users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `webim_failed_jobs`;
CREATE TABLE `webim_failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `webim_failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_message_list
-- ----------------------------
DROP TABLE IF EXISTS `webim_message_list`;
CREATE TABLE `webim_message_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息ID',
  `md5` varchar(50) NOT NULL DEFAULT '' COMMENT 'MD5',
  `room_id` int(11) NOT NULL DEFAULT '0' COMMENT '群聊 ID',
  `send_time` bigint(20) NOT NULL DEFAULT '0' COMMENT '服务端收到消息的时间',
  `seq` varchar(50) NOT NULL DEFAULT '' COMMENT '自定义编码',
  `original_file_name` varchar(50) NOT NULL DEFAULT '' COMMENT '原先的文件名称',
  `file_name` varchar(50) NOT NULL DEFAULT '' COMMENT '文件名称',
  `file_size` varchar(20) NOT NULL DEFAULT '' COMMENT '文件大小',
  `thumb_path` varchar(100) NOT NULL DEFAULT '' COMMENT '缩略图地址',
  `file_path` varchar(100) NOT NULL DEFAULT '' COMMENT '文件路径',
  `total_chunks` smallint(3) NOT NULL DEFAULT '0' COMMENT '总的分片',
  `chunk_number` smallint(3) NOT NULL DEFAULT '0' COMMENT '上传的分片',
  `merge_number` smallint(3) NOT NULL DEFAULT '0' COMMENT '合并分片',
  `msg_content` text NOT NULL COMMENT '消息内容',
  `contact` varchar(50) NOT NULL DEFAULT '' COMMENT '@联系人',
  `msg_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0、其他 1 、单聊 2、 群聊',
  `content_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 文本信息 1 音频  2 图片 3 文件 4 视频',
  `upload_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 上传成功 0 上传中 2 合并中 3 合并成功 4 上传发送失败',
  `is_revoke` tinyint(1) NOT NULL DEFAULT '0' COMMENT ' 1 撤回',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_seq` (`seq`) USING BTREE,
  KEY `idx_nsame_size` (`file_name`,`file_size`,`original_file_name`) USING BTREE,
  KEY `room_id` (`room_id`),
  FULLTEXT KEY `ft_content` (`msg_content`),
  CONSTRAINT `webim_message_list_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `webim_room` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2204 DEFAULT CHARSET=utf8mb4 COMMENT='信息列表';

-- ----------------------------
-- Table structure for webim_migrations
-- ----------------------------
DROP TABLE IF EXISTS `webim_migrations`;
CREATE TABLE `webim_migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_password_resets
-- ----------------------------
DROP TABLE IF EXISTS `webim_password_resets`;
CREATE TABLE `webim_password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `webim_password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_personal_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `webim_personal_access_tokens`;
CREATE TABLE `webim_personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `webim_personal_access_tokens_token_unique` (`token`),
  KEY `webim_personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_robot
-- ----------------------------
DROP TABLE IF EXISTS `webim_robot`;
CREATE TABLE `webim_robot` (
  `id` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '名称',
  `keyword` varchar(50) NOT NULL DEFAULT '' COMMENT '关键词',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='机器人列表';

-- ----------------------------
-- Table structure for webim_room
-- ----------------------------
DROP TABLE IF EXISTS `webim_room`;
CREATE TABLE `webim_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '聊天室名称',
  `intro` varchar(100) NOT NULL DEFAULT '' COMMENT '介绍',
  `deleted_at` int(1) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='群组表';

-- ----------------------------
-- Table structure for webim_room_user
-- ----------------------------
DROP TABLE IF EXISTS `webim_room_user`;
CREATE TABLE `webim_room_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `room_id` int(11) NOT NULL DEFAULT '0' COMMENT '群组ID',
  `is_master` int(1) NOT NULL DEFAULT '0' COMMENT '0 否 1 是群主',
  `types` int(1) NOT NULL DEFAULT '0' COMMENT '0 群聊  1 私聊',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_room_user` (`room_id`,`user_id`) USING BTREE,
  CONSTRAINT `RESTRICT` FOREIGN KEY (`room_id`) REFERENCES `webim_room` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COMMENT='用户房间中间表';

-- ----------------------------
-- Table structure for webim_sensitive_word
-- ----------------------------
DROP TABLE IF EXISTS `webim_sensitive_word`;
CREATE TABLE `webim_sensitive_word` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '敏感词',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='敏感词';

-- ----------------------------
-- Table structure for webim_telescope_entries
-- ----------------------------
DROP TABLE IF EXISTS `webim_telescope_entries`;
CREATE TABLE `webim_telescope_entries` (
  `sequence` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `family_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `should_display_on_index` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`sequence`),
  UNIQUE KEY `webim_telescope_entries_uuid_unique` (`uuid`),
  KEY `webim_telescope_entries_batch_id_index` (`batch_id`),
  KEY `webim_telescope_entries_family_hash_index` (`family_hash`),
  KEY `webim_telescope_entries_created_at_index` (`created_at`),
  KEY `webim_telescope_entries_type_should_display_on_index_index` (`type`,`should_display_on_index`)
) ENGINE=InnoDB AUTO_INCREMENT=13707 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_telescope_entries_tags
-- ----------------------------
DROP TABLE IF EXISTS `webim_telescope_entries_tags`;
CREATE TABLE `webim_telescope_entries_tags` (
  `entry_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  KEY `webim_telescope_entries_tags_entry_uuid_tag_index` (`entry_uuid`,`tag`),
  KEY `webim_telescope_entries_tags_tag_index` (`tag`),
  CONSTRAINT `webim_telescope_entries_tags_entry_uuid_foreign` FOREIGN KEY (`entry_uuid`) REFERENCES `webim_telescope_entries` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_telescope_monitoring
-- ----------------------------
DROP TABLE IF EXISTS `webim_telescope_monitoring`;
CREATE TABLE `webim_telescope_monitoring` (
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for webim_third_party_login_user
-- ----------------------------
DROP TABLE IF EXISTS `webim_third_party_login_user`;
CREATE TABLE `webim_third_party_login_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `third_party_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '第三方登录id',
  `login_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '登录用户名',
  `nick_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `access_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'token',
  `refresh_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '刷新token',
  `create_token_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建token的日期',
  `expires_in` int(11) NOT NULL DEFAULT '0' COMMENT 'token有效期',
  `createdAt` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '账号创立日期',
  `updatedAt` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '最近项目活跃时间',
  `origin` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '第三方 登录code',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='第三方登录';

-- ----------------------------
-- Table structure for webim_third_party_login_user_type
-- ----------------------------
DROP TABLE IF EXISTS `webim_third_party_login_user_type`;
CREATE TABLE `webim_third_party_login_user_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='第三方登录类型';

-- ----------------------------
-- Table structure for webim_upload_chunck_info
-- ----------------------------
DROP TABLE IF EXISTS `webim_upload_chunck_info`;
CREATE TABLE `webim_upload_chunck_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `md5` varchar(50) NOT NULL DEFAULT '' COMMENT 'md5',
  `file_name` varchar(50) NOT NULL DEFAULT '' COMMENT '文件名称',
  `file_size` varchar(20) NOT NULL DEFAULT '' COMMENT '大小',
  `file_path` varchar(100) NOT NULL DEFAULT '' COMMENT '路径',
  `total_chunks` smallint(3) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` tinyint(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for webim_user
-- ----------------------------
DROP TABLE IF EXISTS `webim_user`;
CREATE TABLE `webim_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `nick_name` varchar(20) NOT NULL DEFAULT '' COMMENT '昵称',
  `login_name` varchar(20) NOT NULL DEFAULT '' COMMENT '账号',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `photo` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `is_online` char(10) NOT NULL DEFAULT '' COMMENT '上线 online 离线 offline',
  `is_robot` int(1) NOT NULL DEFAULT '0' COMMENT '1 机器人',
  `sex` int(1) NOT NULL DEFAULT '0' COMMENT ' 0 无 1 男 2 女',
  `email` varchar(20) NOT NULL DEFAULT '' COMMENT '邮件',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '1 正常 2 异常',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '编辑时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_login_name` (`login_name`,`id`) USING BTREE,
  KEY `idx_pass_nick` (`password`,`nick_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Table structure for webim_user_logs
-- ----------------------------
DROP TABLE IF EXISTS `webim_user_logs`;
CREATE TABLE `webim_user_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `ip` char(15) NOT NULL DEFAULT '' COMMENT 'ip',
  `pro_code` int(11) NOT NULL DEFAULT '0' COMMENT '省份编码',
  `pro` varchar(30) NOT NULL DEFAULT '' COMMENT '省份',
  `city_code` int(11) NOT NULL DEFAULT '0' COMMENT '城市编码',
  `city` varchar(30) NOT NULL DEFAULT '' COMMENT '城市',
  `addr` varchar(50) NOT NULL DEFAULT '' COMMENT '详细地址',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `idx_id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=662 DEFAULT CHARSET=utf8 COMMENT='登录日志';

-- ----------------------------
-- Table structure for webim_user_receive
-- ----------------------------
DROP TABLE IF EXISTS `webim_user_receive`;
CREATE TABLE `webim_user_receive` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `room_id` int(11) NOT NULL DEFAULT '0' COMMENT '群聊id',
  `seq` bigint(20) NOT NULL DEFAULT '0' COMMENT '自定义编码',
  `msg_form` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '发送者',
  `msg_to` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '接收者',
  `delivered` int(3) NOT NULL DEFAULT '0' COMMENT '0 未送达 1 送达',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_id` (`id`,`seq`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=66871 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='消息推送表 用于离线消息';

-- ----------------------------
-- Table structure for webim_user_send
-- ----------------------------
DROP TABLE IF EXISTS `webim_user_send`;
CREATE TABLE `webim_user_send` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `room_id` int(11) NOT NULL DEFAULT '0' COMMENT '群聊id',
  `msg_form` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '发送者',
  `msg_to` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '接收者',
  `seq` bigint(20) NOT NULL DEFAULT '0' COMMENT '序列号',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_id_seq` (`id`,`seq`)
) ENGINE=InnoDB AUTO_INCREMENT=3164 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='消息发送表  用于历史消息';
