DELETE FROM `PREFIX@acl_menu`;
INSERT INTO `PREFIX@acl_menu` (`id`, `pid`, `type`, `name`, `link`, `status`, `display`, `order`) VALUES
	(1, 0, 1, '权限', 'acl', 1, 1, 90),
	(2, 0, 1, 'admin', 'admin', 1, 0, 0),
	(3, 0, 1, 'main', 'main', 1, 0, 0),
	(4, 0, 1, '面板', 'panel', 1, 1, 0),
	(5, 0, 1, '安全', 'security', 1, 1, 80),
	(6, 4, 1, '默认主页', 'index', 1, 0, 0),
	(7, 1, 1, '', 'index', 1, 0, 0),
	(8, 1, 1, '', 'editMenu', 1, 0, 0),
	(9, 1, 1, '导航管理', 'navManager', 1, 1, 10),
	(10, 1, 1, '', 'del', 1, 0, 0),
	(11, 1, 1, '添加角色', 'addRole', 1, 1, 20),
	(12, 1, 1, '角色列表', 'roleList', 1, 1, 30),
	(13, 1, 1, '', 'editRole', 1, 0, 0),
	(14, 1, 1, '用户列表', 'user', 1, 1, 50),
	(15, 5, 1, '', 'index', 1, 0, 0),
	(16, 5, 1, '密保卡预览', 'printSecurityCard', 1, 1, 10),
	(17, 5, 1, '下载密保卡', 'makeSecurityImage', 1, 1, 30),
	(18, 5, 1, '绑定密保卡', 'bind', 1, 1, 20),
	(19, 5, 1, '重置密保卡', 'refresh', 1, 1, 40),
	(20, 5, 1, '密保卡解绑', 'kill', 1, 1, 50),
	(21, 5, 1, '', 'create', 1, 0, 0),
	(22, 5, 1, '更改登录密码', 'changePassword', 1, 1, 60),
	(23, 0, 1, '用户', 'user', 1, 1, 15),
	(24, 23, 1, '用户列表', 'index', 1, 1, 0),
	(25, 0, 1, 'forum', 'forum', 1, 0, 0),
	(26, 0, 1, '配置', 'settings', 1, 1, 70),
	(27, 0, 1, '话题', 'topics', 1, 1, 60),
	(28, 27, 1, '话题列表', 'index', 1, 1, 0),
	(29, 26, 1, '网站设置', 'index', 1, 1, 0),
	(30, 23, 1, '', 'ban', 1, 0, 0),
	(31, 23, 1, '', 'resetPassword', 1, 0, 0),
	(32, 1, 1, '', 'delRole', 1, 0, 0),
	(33, 1, 1, '', 'delUser', 1, 0, 0),
	(34, 25, 1, '', 'jumpToContent', 1, 0, 0),
	(35, 0, 1, '内容', 'content', 1, 1, 65),
	(36, 35, 1, '内容列表', 'index', 1, 1, 0),
	(37, 35, 1, '', 'preview', 1, 0, 0),
	(38, 4, 1, '', 'contentStat', 1, 0, 0),
	(39, 4, 1, '', 'userStat', 1, 0, 0),
	(40, 27, 1, '', 'saveTopicUI', 1, 0, 0),
	(41, 27, 1, '', 'saveTopic', 1, 0, 0),
	(42, 27, 1, '', 'saveRootTopicUI', 1, 0, 0),
	(43, 27, 1, '', 'saveRootTopic', 1, 0, 0),
	(44, 35, 1, '推荐话题', 'recommendList', 1, 1, 0),
	(45, 35, 1, '', 'blockContentUI', 1, 0, 0),
	(46, 35, 1, '', 'blockContent', 1, 0, 0),
	(47, 35, 1, '', 'recommend', 1, 0, 0),
	(48, 26, 1, 'SEO配置', 'seo', 1, 1, 0),
	(49, 26, 1, '', 'updateSeoConfig', 1, 0, 0),
	(50, 27, 1, '', 'managerUI', 1, 0, 0),
	(51, 27, 1, '', 'saveManager', 1, 0, 0),
	(52, 27, 1, '', 'delTopic', 1, 0, 0),
	(53, 0, 1, '交互', 'interact', 1, 1, 66),
	(54, 53, 1, '问题答案', 'answer', 1, 1, 0),
	(55, 53, 1, '帖子回复', 'reply', 1, 1, 0),
	(56, 53, 1, '文章评论', 'comment', 1, 1, 0),
	(57, 53, 1, '', 'changeStatus', 1, 0, 0),
	(58, 27, 1, '设置主编', 'chiefEditor', 1, 1, 0),
	(59, 27, 1, '', 'checkTopicUrl', 1, 0, 0),
	(60, 0, 1, '反馈', 'report', 1, 1, 67),
	(61, 60, 1, '举报列表', 'violation', 1, 1, 0),
	(62, 60, 1, '', 'action', 1, 0, 0),
	(63, 60, 1, '', 'ignore', 1, 0, 0),
	(64, 23, 1, '推荐用户', 'recommend', 1, 1, 0),
	(65, 23, 1, '', 'delRecommendUser', 1, 0, 0),
	(66, 23, 1, '', 'addRecommendUser', 1, 0, 0),
	(67, 26, 1, '邀请码管理', 'inviteCode', 1, 1, 0),
	(68, 26, 1, '第三方登录', 'OAuth', 1, 1, 0),
	(69, 26, 1, '', 'addInviteCode', 1, 0, 0),
	(70, 26, 1, '', 'delInviteCode', 1, 0, 0),
	(71, 26, 1, '', 'changeInviteCodeStatus', 1, 0, 0);

DELETE FROM `PREFIX@seo`;
INSERT INTO `PREFIX@seo` (`id`, `controller`, `name`, `title`, `keywords`, `description`) VALUES
	(1, 'user.login', '登录', '登录', '', ''),
	(2, 'user.register', '注册', '注册', '', ''),
	(3, 'user.invite', '注册邀请页面', '你的朋友 {{inviteUserInfo.nickname}} 邀请你加入我们!', '', ''),
	(4, 'main.index', '动态列表', '动态列表', '', ''),
	(5, 'main.invite', '邀请我参与的', '邀请我参与的', '', ''),
	(6, 'main.message', '我的私信', '我的私信', '', ''),
	(7, 'main.following', '我关注的的主题', '我关注的的主题', '', ''),
	(8, 'main.inviteRegister', '邀请好友', '邀请好友', '', ''),
	(9, 'main.collections', '我的收藏', '我的收藏', '', ''),
	(10, 'publish.article', '写文章', '写文章', '', ''),
	(11, 'publish.question', '提问题', '提问题', '', ''),
	(12, 'publish.posts', '发帖子', '发帖子', '', ''),
	(13, 'user.detail', '个人主页', '{{account_info.nickname}}的主页', '{{account_info.nickname}}发布的{{current_tab_name}}列表', '{{account_info.nickname}}的个人主页，{{account_info.nickname}}发布的{{current_tab_name}}列表'),
	(14, 'search.index', '搜索', '{{seo_title}}', '', ''),
	(15, 'explore.index', '发现', '发现', '全站最新主题，帖子和文章', '为您提供全站最新的主题，帖子和文章'),
	(16, 'topics.index', '话题列表', '话题列表', '话题列表', '推荐你关注以下话题:{{recommend_topic.0.topic_name}}，{{recommend_topic.1.topic_name}}和{{recommend_topic.2.topic_name}}'),
	(17, 'topics.detail', '话题详细页', '话题 {{topic_info.topic_name}}', '话题 {{topic_info.topic_name}}', '关于话题 {{topic_info.topic_name}} 的帖子，讨论，和文章'),
	(18, 'content.question', '问题详细页', '{{question_info.title}}', '问题 {{question_info.title}} 的回答', '{{question_info.topics.0.topic_name}}，关于 {{question_info.title}} 的回答列表'),
	(19, 'content.article', '文章详细页', '{{article_info.title}}', '{{article_info.nickname}}的文章 {{article_info.title}}', '{{article_info.topics.0.topic_name}}，文章{{article_info.title}}'),
	(20, 'content.posts', '帖子详细页', '{{posts_info.title}}', '帖子 {{posts_info.title}}', '{{posts_info.topics.0.topic_name}}，{{posts_info.title}} 帖子的回复列表'),
	(21, 'interact.question', '问题阅读页', '{{question_info.title}} - {{answer_info.nickname}}的回答', '问题 {{question_info.title}} - {{answer_info.nickname}}的回答 的评论列表', '问题 {{question_info.title}} - {{answer_info.nickname}}的回答 的评论列表'),
	(22, 'interact.reply', '回复阅读页', '{{posts_info.title}} - {{reply_info.nickname}}的回复', '帖子 {{posts_info.title}} - {{reply_info.nickname}}的回复 的评论列表', '帖子 {{posts_info.title}} - {{reply_info.nickname}}的回复 的评论列表');