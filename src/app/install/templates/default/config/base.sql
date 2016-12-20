DROP TABLE IF EXISTS `PREFIX@acl_menu`;
CREATE TABLE IF NOT EXISTS `PREFIX@acl_menu` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT '0',
  `type` tinyint(4) DEFAULT '1' COMMENT '0系统,1用户',
  `name` varchar(128) DEFAULT '',
  `link` varchar(64) DEFAULT '',
  `status` tinyint(4) DEFAULT '-1' COMMENT '状态-1:未激活',
  `display` tinyint(4) DEFAULT '1' COMMENT '1:显示,0:不显示',
  `order` int(11) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@acl_role`;
CREATE TABLE IF NOT EXISTS `PREFIX@acl_role` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '角色名称',
  `behavior` text COMMENT '允许的行为',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色管理';

DROP TABLE IF EXISTS `PREFIX@admin`;
CREATE TABLE IF NOT EXISTS `PREFIX@admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `t` int(11) NOT NULL DEFAULT '1' COMMENT '状态 1:正常',
  `rid` int(11) NOT NULL DEFAULT '1' COMMENT '角色',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@answers`;
CREATE TABLE IF NOT EXISTS `PREFIX@answers` (
  `answer_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `title_id` int(11) unsigned NOT NULL,
  `question_id` int(11) unsigned NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '1,正常 -1,折叠, -2,屏蔽',
  `answer_content` text NOT NULL,
  `answer_ip` int(11) unsigned NOT NULL,
  `answer_time` int(11) NOT NULL,
  PRIMARY KEY (`answer_id`),
  KEY `uid` (`uid`),
  KEY `title_id` (`title_id`),
  KEY `question_id` (`question_id`),
  KEY `uid_title_id` (`uid`,`title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帖子评论';

DROP TABLE IF EXISTS `PREFIX@answers_comment`;
CREATE TABLE IF NOT EXISTS `PREFIX@answers_comment` (
  `comment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `answer_id` int(11) unsigned NOT NULL,
  `at_comment_id` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1,正常 -1,被折叠',
  `uid` int(11) unsigned NOT NULL,
  `comment_content` varchar(255) NOT NULL,
  `comment_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `comment_id` (`answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='答案评论';

DROP TABLE IF EXISTS `PREFIX@answers_stand`;
CREATE TABLE IF NOT EXISTS `PREFIX@answers_stand` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `answer_id` int(10) unsigned NOT NULL,
  `stand` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0,中立 1,赞同 2,反对',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_answer_id` (`uid`,`answer_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@answers_stat`;
CREATE TABLE IF NOT EXISTS `PREFIX@answers_stat` (
  `answer_id` int(11) unsigned NOT NULL,
  `question_id` int(11) unsigned NOT NULL,
  `up_count` int(11) NOT NULL DEFAULT '0' COMMENT '点赞次数',
  PRIMARY KEY (`answer_id`),
  KEY `question_id_up_count` (`question_id`,`up_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='评论统计';

DROP TABLE IF EXISTS `PREFIX@articles`;
CREATE TABLE IF NOT EXISTS `PREFIX@articles` (
  `article_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_id` int(10) unsigned NOT NULL,
  `summary` text,
  `category_id` int(10) unsigned NOT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `hits_update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`article_id`),
  KEY `title_id` (`title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@articles_category`;
CREATE TABLE IF NOT EXISTS `PREFIX@articles_category` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `sort` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `PREFIX@articles_comment`;
CREATE TABLE IF NOT EXISTS `PREFIX@articles_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1,正常, -1,折叠, -2,屏蔽',
  `at_comment_id` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_content` text NOT NULL,
  `comment_ip` int(11) unsigned NOT NULL,
  `comment_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `article_id` (`article_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@articles_content`;
CREATE TABLE IF NOT EXISTS `PREFIX@articles_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(11) unsigned NOT NULL,
  `content` longtext,
  `p` mediumint(8) unsigned NOT NULL DEFAULT '1',
  `ct` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@articles_income`;
CREATE TABLE IF NOT EXISTS `PREFIX@articles_income` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '打赏',
  `article_id` int(10) unsigned NOT NULL,
  `num` int(10) unsigned NOT NULL,
  `income_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章收益\r\n';

DROP TABLE IF EXISTS `PREFIX@articles_stand`;
CREATE TABLE IF NOT EXISTS `PREFIX@articles_stand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `act_id` int(11) NOT NULL COMMENT '对应的动态id',
  `article_id` int(11) NOT NULL,
  `vote_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

DROP TABLE IF EXISTS `PREFIX@coin_log`;
CREATE TABLE IF NOT EXISTS `PREFIX@coin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `router` varchar(128) NOT NULL,
  `change_type` char(1) NOT NULL DEFAULT '+',
  `change_value` int(10) unsigned NOT NULL,
  `change_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@collections`;
CREATE TABLE IF NOT EXISTS `PREFIX@collections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `title_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `collections_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_title_id` (`uid`,`title_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='主题收藏';

DROP TABLE IF EXISTS `PREFIX@collections_category`;
CREATE TABLE IF NOT EXISTS `PREFIX@collections_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '1',
  `public` int(11) NOT NULL DEFAULT '1' COMMENT '0,私有, 1,公开',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `PREFIX@following_act`;
CREATE TABLE IF NOT EXISTS `PREFIX@following_act` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `follow_id` int(11) NOT NULL,
  `act_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `following_id` (`follow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@following_content`;
CREATE TABLE IF NOT EXISTS `PREFIX@following_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `title_id` int(11) unsigned NOT NULL,
  `following_time` int(11) unsigned NOT NULL,
  `last_view_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_title_id` (`uid`,`title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@following_topic`;
CREATE TABLE IF NOT EXISTS `PREFIX@following_topic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `topic_id` int(11) unsigned NOT NULL,
  `following_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_topic_id` (`uid`,`topic_id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户关注的话题';

DROP TABLE IF EXISTS `PREFIX@following_user`;
CREATE TABLE IF NOT EXISTS `PREFIX@following_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `following_uid` int(10) unsigned NOT NULL,
  `following_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1,主动关注, 2,系统推荐关注, 3, 点赞自动关注',
  `following_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_following_uid` (`uid`,`following_uid`) USING BTREE,
  KEY `following_uid` (`following_uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户关注的人\r\n';

DROP TABLE IF EXISTS `PREFIX@hits_articles`;
CREATE TABLE IF NOT EXISTS `PREFIX@hits_articles` (
  `ip` int(11) unsigned NOT NULL,
  `article_id` int(11) unsigned NOT NULL,
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ip`,`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@hits_posts`;
CREATE TABLE IF NOT EXISTS `PREFIX@hits_posts` (
  `ip` int(11) unsigned NOT NULL,
  `posts_id` int(11) unsigned NOT NULL,
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ip`,`posts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@hits_questions`;
CREATE TABLE IF NOT EXISTS `PREFIX@hits_questions` (
  `ip` int(11) unsigned NOT NULL,
  `question_id` int(11) unsigned NOT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ip`,`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@invite`;
CREATE TABLE IF NOT EXISTS `PREFIX@invite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `title_id` int(10) unsigned NOT NULL,
  `count` int(10) unsigned NOT NULL DEFAULT '1',
  `status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0:未处理 1:忽略',
  `invite_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_title_id` (`uid`,`title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@invite_code`;
CREATE TABLE IF NOT EXISTS `PREFIX@invite_code` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `invite_code` varchar(50) NOT NULL,
  `use_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comments` varchar(255) NOT NULL DEFAULT '' COMMENT '注释',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '0,已失效 1,正常',
  `create_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@message`;
CREATE TABLE IF NOT EXISTS `PREFIX@message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '发送者id',
  `receiver_uid` int(11) NOT NULL DEFAULT '0' COMMENT '接受者id',
  `sender` int(11) NOT NULL DEFAULT '0' COMMENT '发送者',
  `receiver` int(11) NOT NULL DEFAULT '0' COMMENT '接受者',
  `message_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '1 普通消息 2 系统消息',
  `content` text,
  `send_time` int(11) NOT NULL DEFAULT '0',
  `read_time` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `read_time` (`read_time`) USING BTREE,
  KEY `uid_receiver_uid` (`uid`,`receiver_uid`) USING BTREE,
  KEY `receiver` (`receiver`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `PREFIX@posts`;
CREATE TABLE IF NOT EXISTS `PREFIX@posts` (
  `posts_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title_id` int(11) unsigned NOT NULL,
  `posts_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1,普通帖 2,连载帖',
  `posts_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1,已完结 2,连载中',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `hits_update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`posts_id`),
  KEY `title_id` (`title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帖子';

DROP TABLE IF EXISTS `PREFIX@posts_content`;
CREATE TABLE IF NOT EXISTS `PREFIX@posts_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `posts_id` int(10) unsigned NOT NULL,
  `content` longtext,
  `p` mediumint(8) unsigned NOT NULL DEFAULT '1',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_id` (`posts_id`),
  KEY `p` (`p`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帖子内容';

DROP TABLE IF EXISTS `PREFIX@questions`;
CREATE TABLE IF NOT EXISTS `PREFIX@questions` (
  `question_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title_id` int(11) unsigned NOT NULL,
  `question_content` text,
  `best_answer_id` int(11) unsigned NOT NULL,
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `hits_update_time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`question_id`),
  KEY `title_id` (`title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帖子';

DROP TABLE IF EXISTS `PREFIX@recommend_title`;
CREATE TABLE IF NOT EXISTS `PREFIX@recommend_title` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title_id` int(11) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '1',
  `ct` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sort` (`sort`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `PREFIX@recommend_user`;
CREATE TABLE IF NOT EXISTS `PREFIX@recommend_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

DROP TABLE IF EXISTS `PREFIX@reply`;
CREATE TABLE IF NOT EXISTS `PREFIX@reply` (
  `reply_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `posts_id` int(11) unsigned NOT NULL,
  `up_count` int(11) unsigned NOT NULL DEFAULT '0',
  `at_reply_id` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '1,正常 -1,折叠, -2,屏蔽',
  `reply_content` text NOT NULL,
  `reply_ip` int(11) unsigned NOT NULL,
  `reply_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`reply_id`),
  KEY `uid` (`uid`),
  KEY `topic_id` (`posts_id`),
  KEY `posts_id_up_count` (`posts_id`,`up_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帖子评论';

DROP TABLE IF EXISTS `PREFIX@reply_comment`;
CREATE TABLE IF NOT EXISTS `PREFIX@reply_comment` (
  `comment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `reply_id` int(11) unsigned NOT NULL,
  `at_reply_id` int(11) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1,正常 -1,被折叠',
  `uid` int(11) unsigned NOT NULL,
  `comment_content` varchar(255) NOT NULL,
  `comment_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `comment_id` (`reply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@reply_up`;
CREATE TABLE IF NOT EXISTS `PREFIX@reply_up` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reply_id` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `ct` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reply_id_uid` (`reply_id`,`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

DROP TABLE IF EXISTS `PREFIX@report`;
CREATE TABLE IF NOT EXISTS `PREFIX@report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` int(10) unsigned NOT NULL,
  `report_id` int(10) unsigned NOT NULL,
  `rt` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_report_id` (`type`,`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@security_card`;
CREATE TABLE IF NOT EXISTS `PREFIX@security_card` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `card_data` text NOT NULL,
  `bind_user` varchar(255) NOT NULL,
  `ext_time` int(11) NOT NULL DEFAULT '0' COMMENT '已过期,-1',
  PRIMARY KEY (`id`),
  KEY `bind_user` (`bind_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@seo`;
CREATE TABLE IF NOT EXISTS `PREFIX@seo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `controller` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `controller` (`controller`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@title`;
CREATE TABLE IF NOT EXISTS `PREFIX@title` (
  `title_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `topic_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '所属话题',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '1,问答 2,帖子 3,文章',
  `uid` int(11) unsigned NOT NULL,
  `up_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '被赞次数',
  `interact_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '回复,答案或评论次数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `post_ip` int(11) unsigned NOT NULL,
  `post_time` int(11) unsigned NOT NULL,
  `last_interact_time` int(11) unsigned NOT NULL COMMENT '最后发生交互时间',
  PRIMARY KEY (`title_id`),
  KEY `uid` (`uid`),
  KEY `up_count` (`up_count`),
  KEY `type` (`type`),
  KEY `topic_ids_interact_count` (`topic_ids`,`interact_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帖子';

DROP TABLE IF EXISTS `PREFIX@title_images`;
CREATE TABLE IF NOT EXISTS `PREFIX@title_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_id` int(10) unsigned NOT NULL,
  `origin_url` varchar(255) NOT NULL DEFAULT '',
  `image_url` varchar(255) NOT NULL,
  `image_url_md5` char(50) NOT NULL,
  `location` tinyint(4) NOT NULL DEFAULT '1',
  `sync_status` tinyint(4) NOT NULL DEFAULT '0',
  `p` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `title_id_p` (`title_id`,`p`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@topic`;
CREATE TABLE IF NOT EXISTS `PREFIX@topic` (
  `topic_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `as_recommend` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_name` varchar(255) NOT NULL,
  `topic_url` varchar(255) NOT NULL,
  `topic_image` varchar(255) NOT NULL DEFAULT 'images/topics/default.png',
  `topic_description` varchar(255) NOT NULL DEFAULT '',
  `enable_question` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '开启问答',
  `enable_posts` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '开启帖子',
  `enable_article` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '开启文章分类',
  `create_time` int(10) unsigned NOT NULL,
  `sort` mediumint(8) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`topic_id`),
  UNIQUE KEY `topic_url` (`topic_url`),
  KEY `as_recommend` (`as_recommend`),
  KEY `sort` (`sort`),
  KEY `topic_name` (`topic_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='话题';

DROP TABLE IF EXISTS `PREFIX@topic_editor`;
CREATE TABLE IF NOT EXISTS `PREFIX@topic_editor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` int(10) unsigned NOT NULL,
  `editor_uid` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@topic_score`;
CREATE TABLE IF NOT EXISTS `PREFIX@topic_score` (
  `id` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `topic_id` int(10) unsigned NOT NULL,
  `title_id` int(10) unsigned NOT NULL,
  `score` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid_topic_id` (`uid`,`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@topic_title_id`;
CREATE TABLE IF NOT EXISTS `PREFIX@topic_title_id` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL COMMENT '模型类型',
  `topic_id` int(10) unsigned NOT NULL,
  `title_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `topic_id_title_id` (`topic_id`,`title_id`),
  KEY `type_topic_id` (`topic_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@user`;
CREATE TABLE IF NOT EXISTS `PREFIX@user` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(50) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `from_platform` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '1,本地 2,QQ, 3,微博, 4,微信',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '电子邮件',
  `cellphone_number` varchar(50) NOT NULL DEFAULT '' COMMENT '手机号码',
  `introduce` varchar(255) NOT NULL DEFAULT '',
  `qr` varchar(255) NOT NULL DEFAULT '' COMMENT '收款二维码',
  `validate` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '认证状态',
  `invite_code_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '邀请码ID',
  `password` varchar(50) NOT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT 'avatar/default.png',
  `gender` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0,未知 1,男 2,女',
  `salt` char(16) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '-1,被封号 0,未完成 1,正常',
  `last_login_ip` int(11) unsigned NOT NULL DEFAULT '0',
  `last_login_time` int(11) unsigned NOT NULL DEFAULT '0',
  `register_ip` int(11) unsigned NOT NULL,
  `register_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `nickname` (`nickname`) USING BTREE,
  KEY `account` (`account`),
  KEY `register_time` (`register_time`),
  KEY `invite_code_id` (`invite_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@user_act_log`;
CREATE TABLE IF NOT EXISTS `PREFIX@user_act_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `action_type` tinyint(3) unsigned NOT NULL,
  `title_id` int(11) unsigned NOT NULL,
  `relation_id` int(11) unsigned NOT NULL,
  `act_time` int(11) unsigned NOT NULL,
  `act_ip` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX@user_openid`;
CREATE TABLE IF NOT EXISTS `PREFIX@user_openid` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `platform` tinyint(3) unsigned NOT NULL,
  `openid` varchar(64) NOT NULL,
  `unionid` varchar(64) NOT NULL DEFAULT '',
  `nickname` varchar(64) NOT NULL DEFAULT '',
  `gender` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0,未知 1,男 2,女',
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `access_token` varchar(255) NOT NULL,
  `refresh_token` varchar(255) NOT NULL DEFAULT '',
  `bind_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `platform` (`platform`),
  KEY `openid` (`openid`),
  KEY `unionid` (`unionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
