/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50743
Source Host           : localhost:3307
Source Database       : lightning

Target Server Type    : MYSQL
Target Server Version : 50743
File Encoding         : 65001

Date: 2023-10-22 19:02:15
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for webim_message_list
-- ----------------------------
DROP TABLE IF EXISTS `webim_message_list`;
CREATE TABLE `webim_message_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息ID',
  `room_id` int(11) NOT NULL DEFAULT '0' COMMENT '群聊 ID',
  `send_time` bigint(20) NOT NULL DEFAULT '0' COMMENT '服务端收到消息的时间',
  `seq` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义编码',
  `file_name` varchar(50) NOT NULL DEFAULT '' COMMENT '文件名称',
  `file_size` varchar(20) NOT NULL DEFAULT '' COMMENT '文件大小',
  `msg_content` text NOT NULL COMMENT '消息内容',
  `contact` varchar(100) NOT NULL DEFAULT '' COMMENT '@联系人',
  `msg_type` int(3) NOT NULL DEFAULT '0' COMMENT '0、其他 1 、单聊 2、 群聊',
  `content_type` int(1) NOT NULL DEFAULT '0' COMMENT '0 文本信息 1 音频  2 图片 3 文件',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_id_seq` (`id`,`seq`) USING BTREE,
  KEY `idx_nsame_size` (`file_name`,`file_size`) USING BTREE,
  FULLTEXT KEY `ft_content` (`msg_content`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='信息列表';

-- ----------------------------
-- Records of webim_message_list
-- ----------------------------
INSERT INTO `webim_message_list` VALUES ('1', '1', '1697970633175', '8519190524723456', '', '', '%26nbsp%3B%E7%AE%97%E6%9C%AF%3Cdiv%3E%3Cbr%3E%3C%2Fdiv%3E', '', '2', '0', '1697970635', '1697970635');
INSERT INTO `webim_message_list` VALUES ('2', '1', '1697971554289', '8520735991529728', '', '', '%26nbsp%3B%E7%9A%84%E4%B8%89%E5%88%86%E5%A4%A7%E8%B5%9B%3Cdiv%3E%3Cbr%3E%3C%2Fdiv%3E', '', '2', '0', '1697971556', '1697971556');
INSERT INTO `webim_message_list` VALUES ('3', '1', '1697971576257', '8520772616192256', '', '', '%26nbsp%3B+%3Cimg+id%3D%22image%22+onclick%3D%22myBtn%28%26quot%3Bfiles%2Ffiles_52_1697971570_png%26quot%3B%29%22+src%3D%22files%2Ffiles_52_1697971570_png%22%3E%E7%9A%84%3Cdiv%3E%3Cbr%3E%3Cdiv%3E%3Cbr%3E%3C%2Fdiv%3E%3C%2Fdiv%3E', '', '2', '0', '1697971577', '1697971577');
INSERT INTO `webim_message_list` VALUES ('4', '1', '1697971586034', '8520789024309504', '微信截图_20231014095340.png', '100331', 'files%2Ffiles_52_1697971585_%E5%BE%AE%E4%BF%A1%E6%88%AA%E5%9B%BE_20231014095340.png', '', '2', '2', '1697971587', '1697971587');
INSERT INTO `webim_message_list` VALUES ('5', '1', '1697971611084', '8520831051235584', 'files_52_1697603860_docker-compose.zip', '19631', 'files%2Ffiles_52_1697971610_files_52_1697603860_docker-compose.zip', '', '2', '3', '1697971615', '1697971615');
INSERT INTO `webim_message_list` VALUES ('6', '1', '1697971759618', '8521080427774208', '', '', '%26nbsp%3B+ss%26nbsp%3B%3Cdiv%3E%3Cbr%3E%3C%2Fdiv%3E', '', '2', '0', '1697971765', '1697971765');
INSERT INTO `webim_message_list` VALUES ('7', '1', '1697971761832', '8521083967766784', '', '', 'ssd%3Cdiv%3E%26nbsp%3B%3C%2Fdiv%3E', '', '2', '0', '1697971769', '1697971769');
INSERT INTO `webim_message_list` VALUES ('8', '1', '1697971777685', '8521110559654144', '', '', '%26nbsp%3B%E7%9A%84%E4%B8%89%E5%88%86%E5%A4%A7%E8%B5%9B%3Cdiv%3E%3Cbr%3E%3C%2Fdiv%3E', '', '2', '0', '1697971779', '1697971779');
INSERT INTO `webim_message_list` VALUES ('9', '1', '1697971779601', '8521113780879616', '', '', 'dfds%3Cdiv%3E%26nbsp%3B%3C%2Fdiv%3E', '', '2', '0', '1697971783', '1697971783');

-- ----------------------------
-- Table structure for webim_room
-- ----------------------------
DROP TABLE IF EXISTS `webim_room`;
CREATE TABLE `webim_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '聊天室名称',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updta_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='群组表';

-- ----------------------------
-- Records of webim_room
-- ----------------------------
INSERT INTO `webim_room` VALUES ('1', '谈天说地', '1689249977', '1689249977');

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
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='群聊用户信息表';

-- ----------------------------
-- Records of webim_room_user
-- ----------------------------
INSERT INTO `webim_room_user` VALUES ('5', '36', '1', '0', '0', '1689432442', '1689432442');
INSERT INTO `webim_room_user` VALUES ('6', '37', '1', '0', '0', '1689473449', '1689473449');
INSERT INTO `webim_room_user` VALUES ('7', '38', '1', '0', '0', '1689995856', '1689995856');
INSERT INTO `webim_room_user` VALUES ('8', '41', '1', '0', '0', '1691750379', '1691750379');
INSERT INTO `webim_room_user` VALUES ('9', '42', '1', '0', '0', '1691750411', '1691750411');
INSERT INTO `webim_room_user` VALUES ('10', '43', '1', '0', '0', '1691750423', '1691750423');
INSERT INTO `webim_room_user` VALUES ('11', '44', '1', '0', '0', '1691750431', '1691750431');
INSERT INTO `webim_room_user` VALUES ('12', '45', '1', '0', '0', '1691750439', '1691750439');
INSERT INTO `webim_room_user` VALUES ('13', '46', '1', '0', '0', '1691750447', '1691750447');
INSERT INTO `webim_room_user` VALUES ('14', '47', '1', '0', '0', '1691750454', '1691750454');
INSERT INTO `webim_room_user` VALUES ('15', '48', '1', '0', '0', '1691750462', '1691750462');
INSERT INTO `webim_room_user` VALUES ('16', '49', '1', '0', '0', '1691750471', '1691750471');
INSERT INTO `webim_room_user` VALUES ('17', '50', '1', '0', '0', '1691750517', '1691750517');
INSERT INTO `webim_room_user` VALUES ('18', '51', '1', '0', '0', '1691750528', '1691750528');
INSERT INTO `webim_room_user` VALUES ('19', '52', '1', '0', '0', '1691750536', '1691750536');
INSERT INTO `webim_room_user` VALUES ('20', '53', '1', '0', '0', '1691753698', '1691753698');
INSERT INTO `webim_room_user` VALUES ('21', '54', '1', '0', '0', '1691753709', '1691753709');
INSERT INTO `webim_room_user` VALUES ('22', '55', '1', '0', '0', '1691753718', '1691753718');
INSERT INTO `webim_room_user` VALUES ('23', '56', '1', '0', '0', '1691753743', '1691753743');
INSERT INTO `webim_room_user` VALUES ('24', '57', '1', '0', '0', '1691753772', '1691753772');
INSERT INTO `webim_room_user` VALUES ('25', '58', '1', '0', '0', '1691753778', '1691753778');
INSERT INTO `webim_room_user` VALUES ('26', '59', '1', '0', '0', '1691753785', '1691753785');
INSERT INTO `webim_room_user` VALUES ('27', '60', '1', '0', '0', '1691753794', '1691753794');
INSERT INTO `webim_room_user` VALUES ('29', '62', '1', '0', '0', '1696655447', '1696655447');

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
-- Records of webim_third_party_login_user
-- ----------------------------
INSERT INTO `webim_third_party_login_user` VALUES ('1', '62', '1035344', 'gongzhiyang', 'gzy', '', '088872ea01fe07c97226e9b0a1355637', 'f21137cd1a6136a773d56236cd20bfb87d5527d39646f364e936b54c6bdff531', '1697015294', '86400', '2016-10-08T19:51:10+08:00', '2023-10-11T17:07:16+08:00', 'gitee', '1696655447', '1697015294');

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
-- Records of webim_third_party_login_user_type
-- ----------------------------
INSERT INTO `webim_third_party_login_user_type` VALUES ('1', 'gitee', '1689432442', '1689432442');
INSERT INTO `webim_third_party_login_user_type` VALUES ('2', 'github', '1689432442', '1689432442');

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
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of webim_user
-- ----------------------------
INSERT INTO `webim_user` VALUES ('36', 'ChatGLM2', 'gong@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', 'online', '1', '0', '', '1', '1689432442', '1689432442');
INSERT INTO `webim_user` VALUES ('37', 'gzy', 'gong123@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', '', '0', '0', '', '1', '1689473449', '1689473449');
INSERT INTO `webim_user` VALUES ('38', 'gzy1991', 'gzy1991@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', 'online', '0', '0', '', '1', '1689995856', '1697967372');
INSERT INTO `webim_user` VALUES ('41', 'gzy2', 'gzy2@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', '', '0', '0', '', '1', '1691750379', '1691750379');
INSERT INTO `webim_user` VALUES ('42', 'gzy3', 'gzy3@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', '', '0', '0', '', '1', '1691750411', '1691750411');
INSERT INTO `webim_user` VALUES ('43', 'gzy4', 'gzy4@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', 'offline', '0', '0', '', '1', '1691750423', '1693050103');
INSERT INTO `webim_user` VALUES ('44', 'gzy6', 'gzy6@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', '', '0', '0', '', '1', '1691750431', '1691750431');
INSERT INTO `webim_user` VALUES ('45', 'gzy7', 'gzy7@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', '', '0', '0', '', '1', '1691750439', '1691750439');
INSERT INTO `webim_user` VALUES ('46', 'gzy8', 'gzy8@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', 'online', '0', '0', '', '1', '1691750447', '1695268372');
INSERT INTO `webim_user` VALUES ('47', 'gzy9', 'gzy9@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', '', '0', '0', '', '1', '1691750454', '1691750454');
INSERT INTO `webim_user` VALUES ('48', 'gzy10', 'gzy10@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', 'offline', '0', '0', '', '1', '1691750462', '1693044383');
INSERT INTO `webim_user` VALUES ('49', 'gzy11', 'gzy11@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', '', '0', '0', '', '1', '1691750471', '1691750471');
INSERT INTO `webim_user` VALUES ('50', 'gzy13', 'gzy13@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', 'offline', '0', '0', '', '1', '1691750517', '1693049978');
INSERT INTO `webim_user` VALUES ('51', 'gzy14', 'gzy14@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', '', '0', '0', '', '1', '1691750528', '1691750528');
INSERT INTO `webim_user` VALUES ('52', 'gzy15', 'gzy15@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', 'online', '0', '0', '', '1', '1691750536', '1697971539');
INSERT INTO `webim_user` VALUES ('53', 'gzy16', 'gzy17@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', 'online', '0', '0', '', '1', '1691753698', '1697971680');
INSERT INTO `webim_user` VALUES ('54', 'gzy18', 'gzy18@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', 'offline', '0', '0', '', '1', '1691753709', '1693113129');
INSERT INTO `webim_user` VALUES ('55', 'gzy19', 'gzy19@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', 'online', '0', '0', '', '1', '1691753718', '1697967364');
INSERT INTO `webim_user` VALUES ('56', 'gzy20', 'gzy21@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', 'online', '0', '0', '', '1', '1691753743', '1697603089');
INSERT INTO `webim_user` VALUES ('57', 'gzy22', 'gzy22@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', 'offline', '0', '0', '', '1', '1691753772', '1693113327');
INSERT INTO `webim_user` VALUES ('58', 'gzy23', 'gzy23@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', '', '0', '0', '', '1', '1691753778', '1691753778');
INSERT INTO `webim_user` VALUES ('59', 'gzy24', 'gzy24@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', 'offline', '0', '0', '', '1', '1691753785', '1693050534');
INSERT INTO `webim_user` VALUES ('60', 'gzy25', 'gzy25@qq.com', 'fcea920f7412b5da7be0cf42b8c93759', '', 'online', '0', '0', '', '1', '1691753794', '1697016264');
INSERT INTO `webim_user` VALUES ('62', 'gzy', 'gongzhiyang', '', '', 'online', '0', '0', '', '1', '1696655447', '1697015255');

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='登录日志';

-- ----------------------------
-- Records of webim_user_logs
-- ----------------------------
INSERT INTO `webim_user_logs` VALUES ('1', '36', '112.8.178.208', '0', '', '0', '', '', '1692190280', '1692190280');
INSERT INTO `webim_user_logs` VALUES ('2', '36', '112.8.178.208', '0', '', '0', '', '', '1692190308', '1692190308');
INSERT INTO `webim_user_logs` VALUES ('3', '36', '112.8.178.208', '0', '', '0', '', '', '1692192717', '1692192717');
INSERT INTO `webim_user_logs` VALUES ('4', '38', '112.8.178.208', '0', '', '0', '', '', '1692193169', '1692193169');
INSERT INTO `webim_user_logs` VALUES ('5', '36', '112.8.178.208', '0', '', '0', '', '', '1692193960', '1692193960');
INSERT INTO `webim_user_logs` VALUES ('6', '36', '112.8.178.208', '0', '', '0', '', '', '1692194243', '1692194243');
INSERT INTO `webim_user_logs` VALUES ('7', '36', '112.8.178.208', '0', '', '0', '', '', '1692194403', '1692194403');
INSERT INTO `webim_user_logs` VALUES ('8', '36', '112.8.178.208', '0', '', '0', '', '', '1692194603', '1692194603');
INSERT INTO `webim_user_logs` VALUES ('9', '36', '112.8.178.208', '0', '', '0', '', '', '1692194641', '1692194641');
INSERT INTO `webim_user_logs` VALUES ('10', '36', '112.8.178.208', '0', '', '0', '', '', '1692194733', '1692194733');
INSERT INTO `webim_user_logs` VALUES ('11', '36', '112.8.178.208', '0', '', '0', '', '', '1692233382', '1692233382');
INSERT INTO `webim_user_logs` VALUES ('12', '38', '112.8.178.208', '0', '', '0', '', '', '1692240373', '1692240373');
INSERT INTO `webim_user_logs` VALUES ('13', '38', '112.8.178.208', '0', '', '0', '', '', '1692241837', '1692241837');
INSERT INTO `webim_user_logs` VALUES ('14', '38', '112.8.178.208', '0', '', '0', '', '', '1692284776', '1692284776');

-- ----------------------------
-- Table structure for webim_user_receive
-- ----------------------------
DROP TABLE IF EXISTS `webim_user_receive`;
CREATE TABLE `webim_user_receive` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `room_id` int(11) NOT NULL DEFAULT '0' COMMENT '群聊id',
  `seq` bigint(20) NOT NULL DEFAULT '0' COMMENT '自定义编码',
  `msg_form` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '发送者',
  `msg_to` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '接收者',
  `delivered` int(3) NOT NULL DEFAULT '0' COMMENT '0 未送达 1 送达',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`msg_id`),
  UNIQUE KEY `uniq_id` (`msg_id`,`seq`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=217 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='消息推送表 用于离线消息';

-- ----------------------------
-- Records of webim_user_receive
-- ----------------------------
INSERT INTO `webim_user_receive` VALUES ('1', '1', '8519190524723456', '52', '52', '1', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('2', '1', '8519190524723456', '52', '56', '1', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('3', '1', '8519190524723456', '52', '60', '1', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('4', '1', '8519190524723456', '52', '36', '1', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('5', '1', '8519190524723456', '52', '38', '1', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('6', '1', '8519190524723456', '52', '46', '1', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('7', '1', '8519190524723456', '52', '53', '1', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('8', '1', '8519190524723456', '52', '55', '1', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('9', '1', '8519190524723456', '52', '62', '1', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('10', '1', '8519190524723456', '52', '48', '0', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('11', '1', '8519190524723456', '52', '50', '0', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('12', '1', '8519190524723456', '52', '54', '0', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('13', '1', '8519190524723456', '52', '57', '0', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('14', '1', '8519190524723456', '52', '59', '0', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('15', '1', '8519190524723456', '52', '43', '0', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('16', '1', '8519190524723456', '52', '58', '0', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('17', '1', '8519190524723456', '52', '42', '0', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('18', '1', '8519190524723456', '52', '44', '0', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('19', '1', '8519190524723456', '52', '49', '0', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('20', '1', '8519190524723456', '52', '51', '0', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('21', '1', '8519190524723456', '52', '37', '0', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('22', '1', '8519190524723456', '52', '41', '0', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('23', '1', '8519190524723456', '52', '45', '0', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('24', '1', '8519190524723456', '52', '47', '0', '1697970635', '1697970635');
INSERT INTO `webim_user_receive` VALUES ('25', '1', '8520735991529728', '52', '53', '1', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('26', '1', '8520735991529728', '52', '55', '1', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('27', '1', '8520735991529728', '52', '62', '1', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('28', '1', '8520735991529728', '52', '36', '1', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('29', '1', '8520735991529728', '52', '38', '1', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('30', '1', '8520735991529728', '52', '46', '1', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('31', '1', '8520735991529728', '52', '52', '1', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('32', '1', '8520735991529728', '52', '56', '1', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('33', '1', '8520735991529728', '52', '60', '1', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('34', '1', '8520735991529728', '52', '43', '0', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('35', '1', '8520735991529728', '52', '57', '0', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('36', '1', '8520735991529728', '52', '59', '0', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('37', '1', '8520735991529728', '52', '48', '0', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('38', '1', '8520735991529728', '52', '50', '0', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('39', '1', '8520735991529728', '52', '54', '0', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('40', '1', '8520735991529728', '52', '37', '0', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('41', '1', '8520735991529728', '52', '41', '0', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('42', '1', '8520735991529728', '52', '45', '0', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('43', '1', '8520735991529728', '52', '47', '0', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('44', '1', '8520735991529728', '52', '49', '0', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('45', '1', '8520735991529728', '52', '51', '0', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('46', '1', '8520735991529728', '52', '42', '0', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('47', '1', '8520735991529728', '52', '44', '0', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('48', '1', '8520735991529728', '52', '58', '0', '1697971556', '1697971556');
INSERT INTO `webim_user_receive` VALUES ('49', '1', '8520772616192256', '52', '53', '1', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('50', '1', '8520772616192256', '52', '55', '1', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('51', '1', '8520772616192256', '52', '62', '1', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('52', '1', '8520772616192256', '52', '46', '1', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('53', '1', '8520772616192256', '52', '52', '1', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('54', '1', '8520772616192256', '52', '56', '1', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('55', '1', '8520772616192256', '52', '60', '1', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('56', '1', '8520772616192256', '52', '36', '1', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('57', '1', '8520772616192256', '52', '38', '1', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('58', '1', '8520772616192256', '52', '43', '0', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('59', '1', '8520772616192256', '52', '57', '0', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('60', '1', '8520772616192256', '52', '59', '0', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('61', '1', '8520772616192256', '52', '48', '0', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('62', '1', '8520772616192256', '52', '50', '0', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('63', '1', '8520772616192256', '52', '54', '0', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('64', '1', '8520772616192256', '52', '41', '0', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('65', '1', '8520772616192256', '52', '45', '0', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('66', '1', '8520772616192256', '52', '47', '0', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('67', '1', '8520772616192256', '52', '49', '0', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('68', '1', '8520772616192256', '52', '51', '0', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('69', '1', '8520772616192256', '52', '37', '0', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('70', '1', '8520772616192256', '52', '42', '0', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('71', '1', '8520772616192256', '52', '44', '0', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('72', '1', '8520772616192256', '52', '58', '0', '1697971578', '1697971578');
INSERT INTO `webim_user_receive` VALUES ('73', '1', '8520789024309504', '52', '60', '1', '1697971587', '1697971587');
INSERT INTO `webim_user_receive` VALUES ('74', '1', '8520789024309504', '52', '36', '1', '1697971587', '1697971587');
INSERT INTO `webim_user_receive` VALUES ('75', '1', '8520789024309504', '52', '38', '1', '1697971587', '1697971587');
INSERT INTO `webim_user_receive` VALUES ('76', '1', '8520789024309504', '52', '46', '1', '1697971587', '1697971587');
INSERT INTO `webim_user_receive` VALUES ('77', '1', '8520789024309504', '52', '52', '1', '1697971587', '1697971587');
INSERT INTO `webim_user_receive` VALUES ('78', '1', '8520789024309504', '52', '56', '1', '1697971587', '1697971587');
INSERT INTO `webim_user_receive` VALUES ('79', '1', '8520789024309504', '52', '62', '1', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('80', '1', '8520789024309504', '52', '53', '1', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('81', '1', '8520789024309504', '52', '55', '1', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('82', '1', '8520789024309504', '52', '48', '0', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('83', '1', '8520789024309504', '52', '50', '0', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('84', '1', '8520789024309504', '52', '54', '0', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('85', '1', '8520789024309504', '52', '57', '0', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('86', '1', '8520789024309504', '52', '59', '0', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('87', '1', '8520789024309504', '52', '43', '0', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('88', '1', '8520789024309504', '52', '58', '0', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('89', '1', '8520789024309504', '52', '42', '0', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('90', '1', '8520789024309504', '52', '44', '0', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('91', '1', '8520789024309504', '52', '37', '0', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('92', '1', '8520789024309504', '52', '41', '0', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('93', '1', '8520789024309504', '52', '45', '0', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('94', '1', '8520789024309504', '52', '47', '0', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('95', '1', '8520789024309504', '52', '49', '0', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('96', '1', '8520789024309504', '52', '51', '0', '1697971588', '1697971588');
INSERT INTO `webim_user_receive` VALUES ('97', '1', '8520831051235584', '52', '36', '1', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('98', '1', '8520831051235584', '52', '38', '1', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('99', '1', '8520831051235584', '52', '46', '1', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('100', '1', '8520831051235584', '52', '52', '1', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('101', '1', '8520831051235584', '52', '56', '1', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('102', '1', '8520831051235584', '52', '60', '1', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('103', '1', '8520831051235584', '52', '53', '1', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('104', '1', '8520831051235584', '52', '55', '1', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('105', '1', '8520831051235584', '52', '62', '1', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('106', '1', '8520831051235584', '52', '48', '0', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('107', '1', '8520831051235584', '52', '50', '0', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('108', '1', '8520831051235584', '52', '54', '0', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('109', '1', '8520831051235584', '52', '43', '0', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('110', '1', '8520831051235584', '52', '57', '0', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('111', '1', '8520831051235584', '52', '59', '0', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('112', '1', '8520831051235584', '52', '42', '0', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('113', '1', '8520831051235584', '52', '44', '0', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('114', '1', '8520831051235584', '52', '58', '0', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('115', '1', '8520831051235584', '52', '37', '0', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('116', '1', '8520831051235584', '52', '41', '0', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('117', '1', '8520831051235584', '52', '45', '0', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('118', '1', '8520831051235584', '52', '47', '0', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('119', '1', '8520831051235584', '52', '49', '0', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('120', '1', '8520831051235584', '52', '51', '0', '1697971615', '1697971615');
INSERT INTO `webim_user_receive` VALUES ('121', '1', '8521080427774208', '53', '36', '1', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('122', '1', '8521080427774208', '53', '38', '1', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('123', '1', '8521080427774208', '53', '46', '1', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('124', '1', '8521080427774208', '53', '52', '1', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('125', '1', '8521080427774208', '53', '56', '1', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('126', '1', '8521080427774208', '53', '60', '1', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('127', '1', '8521080427774208', '53', '53', '1', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('128', '1', '8521080427774208', '53', '55', '1', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('129', '1', '8521080427774208', '53', '62', '1', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('130', '1', '8521080427774208', '53', '48', '0', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('131', '1', '8521080427774208', '53', '50', '0', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('132', '1', '8521080427774208', '53', '54', '0', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('133', '1', '8521080427774208', '53', '43', '0', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('134', '1', '8521080427774208', '53', '57', '0', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('135', '1', '8521080427774208', '53', '59', '0', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('136', '1', '8521080427774208', '53', '42', '0', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('137', '1', '8521080427774208', '53', '44', '0', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('138', '1', '8521080427774208', '53', '58', '0', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('139', '1', '8521080427774208', '53', '37', '0', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('140', '1', '8521080427774208', '53', '41', '0', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('141', '1', '8521080427774208', '53', '45', '0', '1697971765', '1697971765');
INSERT INTO `webim_user_receive` VALUES ('142', '1', '8521080427774208', '53', '47', '0', '1697971766', '1697971766');
INSERT INTO `webim_user_receive` VALUES ('143', '1', '8521080427774208', '53', '49', '0', '1697971766', '1697971766');
INSERT INTO `webim_user_receive` VALUES ('144', '1', '8521080427774208', '53', '51', '0', '1697971766', '1697971766');
INSERT INTO `webim_user_receive` VALUES ('145', '1', '8521083967766784', '53', '53', '1', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('146', '1', '8521083967766784', '53', '55', '1', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('147', '1', '8521083967766784', '53', '62', '1', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('148', '1', '8521083967766784', '53', '36', '1', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('149', '1', '8521083967766784', '53', '38', '1', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('150', '1', '8521083967766784', '53', '46', '1', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('151', '1', '8521083967766784', '53', '52', '1', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('152', '1', '8521083967766784', '53', '56', '1', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('153', '1', '8521083967766784', '53', '60', '1', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('154', '1', '8521083967766784', '53', '43', '0', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('155', '1', '8521083967766784', '53', '57', '0', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('156', '1', '8521083967766784', '53', '59', '0', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('157', '1', '8521083967766784', '53', '48', '0', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('158', '1', '8521083967766784', '53', '50', '0', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('159', '1', '8521083967766784', '53', '54', '0', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('160', '1', '8521083967766784', '53', '37', '0', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('161', '1', '8521083967766784', '53', '41', '0', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('162', '1', '8521083967766784', '53', '45', '0', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('163', '1', '8521083967766784', '53', '47', '0', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('164', '1', '8521083967766784', '53', '49', '0', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('165', '1', '8521083967766784', '53', '51', '0', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('166', '1', '8521083967766784', '53', '42', '0', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('167', '1', '8521083967766784', '53', '44', '0', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('168', '1', '8521083967766784', '53', '58', '0', '1697971770', '1697971770');
INSERT INTO `webim_user_receive` VALUES ('169', '1', '8521110559654144', '52', '36', '1', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('170', '1', '8521110559654144', '52', '38', '1', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('171', '1', '8521110559654144', '52', '46', '1', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('172', '1', '8521110559654144', '52', '52', '1', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('173', '1', '8521110559654144', '52', '56', '1', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('174', '1', '8521110559654144', '52', '60', '1', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('175', '1', '8521110559654144', '52', '53', '1', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('176', '1', '8521110559654144', '52', '55', '1', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('177', '1', '8521110559654144', '52', '62', '1', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('178', '1', '8521110559654144', '52', '48', '0', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('179', '1', '8521110559654144', '52', '50', '0', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('180', '1', '8521110559654144', '52', '54', '0', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('181', '1', '8521110559654144', '52', '43', '0', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('182', '1', '8521110559654144', '52', '57', '0', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('183', '1', '8521110559654144', '52', '59', '0', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('184', '1', '8521110559654144', '52', '42', '0', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('185', '1', '8521110559654144', '52', '44', '0', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('186', '1', '8521110559654144', '52', '58', '0', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('187', '1', '8521110559654144', '52', '37', '0', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('188', '1', '8521110559654144', '52', '41', '0', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('189', '1', '8521110559654144', '52', '45', '0', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('190', '1', '8521110559654144', '52', '47', '0', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('191', '1', '8521110559654144', '52', '49', '0', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('192', '1', '8521110559654144', '52', '51', '0', '1697971780', '1697971780');
INSERT INTO `webim_user_receive` VALUES ('193', '1', '8521113780879616', '52', '53', '1', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('194', '1', '8521113780879616', '52', '55', '1', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('195', '1', '8521113780879616', '52', '62', '1', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('196', '1', '8521113780879616', '52', '36', '1', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('197', '1', '8521113780879616', '52', '38', '1', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('198', '1', '8521113780879616', '52', '46', '1', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('199', '1', '8521113780879616', '52', '52', '1', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('200', '1', '8521113780879616', '52', '56', '1', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('201', '1', '8521113780879616', '52', '60', '1', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('202', '1', '8521113780879616', '52', '43', '0', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('203', '1', '8521113780879616', '52', '57', '0', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('204', '1', '8521113780879616', '52', '59', '0', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('205', '1', '8521113780879616', '52', '48', '0', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('206', '1', '8521113780879616', '52', '50', '0', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('207', '1', '8521113780879616', '52', '54', '0', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('208', '1', '8521113780879616', '52', '37', '0', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('209', '1', '8521113780879616', '52', '41', '0', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('210', '1', '8521113780879616', '52', '45', '0', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('211', '1', '8521113780879616', '52', '47', '0', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('212', '1', '8521113780879616', '52', '49', '0', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('213', '1', '8521113780879616', '52', '51', '0', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('214', '1', '8521113780879616', '52', '42', '0', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('215', '1', '8521113780879616', '52', '44', '0', '1697971784', '1697971784');
INSERT INTO `webim_user_receive` VALUES ('216', '1', '8521113780879616', '52', '58', '0', '1697971784', '1697971784');

-- ----------------------------
-- Table structure for webim_user_send
-- ----------------------------
DROP TABLE IF EXISTS `webim_user_send`;
CREATE TABLE `webim_user_send` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `room_id` int(11) NOT NULL DEFAULT '0' COMMENT '群聊id',
  `msg_form` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '发送者',
  `msg_to` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '接收者',
  `seq` bigint(20) NOT NULL DEFAULT '0' COMMENT '序列号',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`msg_id`),
  UNIQUE KEY `uniq_id_seq` (`msg_id`,`seq`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='消息发送表  用于历史消息';

-- ----------------------------
-- Records of webim_user_send
-- ----------------------------
INSERT INTO `webim_user_send` VALUES ('1', '1', '52', '', '8519190524723456', '1697970635', '1697970635');
INSERT INTO `webim_user_send` VALUES ('2', '1', '52', '', '8520735991529728', '1697971556', '1697971556');
INSERT INTO `webim_user_send` VALUES ('3', '1', '52', '', '8520772616192256', '1697971577', '1697971577');
INSERT INTO `webim_user_send` VALUES ('4', '1', '52', '', '8520789024309504', '1697971587', '1697971587');
INSERT INTO `webim_user_send` VALUES ('5', '1', '52', '', '8520831051235584', '1697971615', '1697971615');
INSERT INTO `webim_user_send` VALUES ('6', '1', '53', '', '8521080427774208', '1697971765', '1697971765');
INSERT INTO `webim_user_send` VALUES ('7', '1', '53', '', '8521083967766784', '1697971769', '1697971769');
INSERT INTO `webim_user_send` VALUES ('8', '1', '52', '', '8521110559654144', '1697971779', '1697971779');
INSERT INTO `webim_user_send` VALUES ('9', '1', '52', '', '8521113780879616', '1697971783', '1697971783');
