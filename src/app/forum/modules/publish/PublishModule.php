<?php
namespace app\forum\modules\publish;

use app\forum\modules\title\TitleImagesModule;
use app\forum\modules\activity\ActivityModule;
use app\forum\modules\content\ArticleModule;
use app\forum\modules\content\PostsModule;
use app\forum\modules\common\BaseModule;
use Cross\Core\Helper;

/**
 * @Auth: cmz <393418737@qq.com>
 * PublishModule.php
 */
class PublishModule extends BaseModule
{

    /**
     * 提问
     *
     * @param int $uid
     * @param array $data
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function saveQuestion($uid, array $data)
    {
        //添加标题
        $title = parent::getArrayData($data, 'title');
        $topic_ids = array();
        if (isset($data['topic_ids'])) {
            $topic_ids = $data['topic_ids'];
        }

        $images_list = array();
        $question_id = parent::getArrayData($data, 'question_id');
        $content = $this->getContent($data['content'], $images_list);
        if ($question_id) {
            $title_id = parent::getArrayData($data, 'title_id');

            //处理图片
            $this->saveImages($title_id, $images_list);

            //更新标题和话题
            $this->updateTitleAndTopic($title_id, BaseModule::TYPE_QUESTION, $title, $topic_ids);

            //更新内容
            $this->link->update($this->questions, array('question_content' => $content), array(
                'question_id' => $question_id
            ));

            return $this->result(1, array('question_id' => $question_id));
        } else {
            $addTitle = $this->addTitle($uid, $title, BaseModule::TYPE_QUESTION, $topic_ids);
            if ($addTitle['status'] == 1) {
                $title_id = $addTitle['message'];

                //处理图片
                $this->saveImages($title_id, $images_list);

                //添加私有内容
                $question_id = $this->link->add($this->questions, array(
                    'title_id' => $title_id,
                    'question_content' => $content,
                    'best_answer_id' => 0
                ));

                //添加动态
                $ACTIVITY = new ActivityModule();
                $ACTIVITY->add($uid, $title_id, ActivityModule::QUESTION, $question_id);

                return $this->result(1, array('question_id' => $question_id));
            } else {
                return $this->result($addTitle['status']);
            }
        }
    }

    /**
     * 发帖
     *
     * @param int $uid
     * @param array $data
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function savePosts($uid, array $data)
    {
        //添加标题
        $title = parent::getArrayData($data, 'title');
        $topic_ids = array();
        if (isset($data['topic_ids'])) {
            $topic_ids = $data['topic_ids'];
        }

        $postsType = parent::getArrayData($data, 'posts_type');
        if ($postsType == 'on') {
            //连载帖
            $posts_type = 2;
            //连载状态 1，已完结  2，连载中
            $posts_status = parent::getArrayData($data, 'posts_status');
            if ($posts_status == 'on') {
                $posts_status = 1;
            } else {
                $posts_status = 2;
            }
        } else {
            //普通贴
            $posts_type = 1;
            $posts_status = 1;
        }

        $images_list = array();
        $p = !empty($data['p']) ? (int)$data['p'] : 1;
        $content = $this->getContent($data['content'], $images_list);
        $posts_id = parent::getArrayData($data, 'posts_id');
        if (empty($content)) {
            return $this->result(200223);
        }

        if ($posts_id) {
            $title_id = (int)parent::getArrayData($data, 'title_id');

            $PM = new PostsModule();
            $contentInfo = $PM->getPostsContent($posts_id, $p);
            $content_id = $contentInfo['id'];

            //更新标题和话题
            $this->updateTitleAndTopic($title_id, BaseModule::TYPE_POSTS, $title, $topic_ids);

            //更新状态
            $this->link->update($this->posts, array('posts_status' => $posts_status), array('posts_id' => $posts_id));

            //处理图片
            $this->saveImages($title_id, $images_list);

            //更新或添加内容
            if ($content_id) {
                $this->link->update($this->posts_content, array('content' => $content), array('id' => $content_id));
            } else {
                $this->link->add($this->posts_content, array(
                    'posts_id' => $posts_id,
                    'content' => $content,
                    'p' => $p,
                    'create_time' => TIME
                ));
            }

            return $this->result(1, array('posts_id' => $posts_id));
        } else {
            $addTitle = $this->addTitle($uid, $title, BaseModule::TYPE_POSTS, $topic_ids);
            if ($addTitle['status'] == 1) {
                $title_id = $addTitle['message'];

                //添加私有内容
                $posts_id = $this->link->add($this->posts, array(
                    'title_id' => $title_id,
                    'posts_type' => $posts_type,
                    'posts_status' => $posts_status,
                ));

                //处理图片
                $this->saveImages($title_id, $images_list);

                if ($posts_id && $content) {
                    $this->link->add($this->posts_content, array(
                        'posts_id' => $posts_id,
                        'content' => $content,
                        'p' => $p,
                        'create_time' => TIME
                    ));
                }

                //添加动态
                $ACTIVITY = new ActivityModule();
                $ACTIVITY->add($uid, $title_id, ActivityModule::POSTS, $posts_id);

                return $this->result(1, array('posts_id' => $posts_id));
            } else {
                return $this->result($addTitle['status']);
            }
        }
    }

    /**
     * 追加帖子内容
     *
     * @param int $title_id
     * @param int $posts_id
     * @param string $content
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function appendPostsContent($title_id, $posts_id, $content)
    {
        $images_list = array();
        $posts_id = (int)$posts_id;
        $content = parent::getContent($content, $images_list);

        $contentInfo = $this->link->get($this->posts_content, 'MAX(p) p', array('posts_id' => $posts_id));
        $p = (int)$contentInfo['p'] + 1;

        //保存图片
        $this->saveImages($title_id, $images_list, $p);

        //添加内容
        $this->link->add($this->posts_content, array(
            'posts_id' => $posts_id,
            'content' => $content,
            'p' => $p,
            'create_time' => TIME
        ));

        return $this->result(1, array('posts_id' => $posts_id));
    }

    /**
     * 发布文章
     *
     * @param int $uid
     * @param array $data
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function saveArticle($uid, array $data)
    {
        //添加标题
        $title = parent::getArrayData($data, 'title');
        $topic_ids = array();
        if (isset($data['topic_ids'])) {
            $topic_ids = $data['topic_ids'];
        }

        $images_list = array();
        $p = !empty($data['p']) ? (int)$data['p'] : 1;
        $article_id = (int)parent::getArrayData($data, 'article_id');
        $category_id = (int)parent::getArrayData($data, 'category_id');
        $content = $this->getContent($data['content'], $images_list);
        if (empty($content)) {
            return $this->result(200222);
        }

        //处理摘要
        if (!empty($data['summary'])) {
            $summary = parent::getSummary($data['summary']);
        } else {
            $summary = parent::getSummary($content);
        }

        $summary = Helper::formatHTMLString($summary);
        if ($article_id) {
            $title_id = (int)parent::getArrayData($data, 'title_id');

            $AM = new ArticleModule();
            $contentInfo = $AM->getArticleContent($article_id, $p);
            $content_id = &$contentInfo['id'];

            //更新标题和话题
            $this->updateTitleAndTopic($title_id, BaseModule::TYPE_ARTICLE, $title, $topic_ids);

            //处理图片
            $this->saveImages($title_id, $images_list);

            //更新基本内容
            $this->link->update($this->articles, array('category_id' => $category_id, 'summary' => $summary), array(
                'article_id' => $article_id
            ));

            //更新或添加内容
            if ($content_id) {
                $this->link->update($this->articles_content, array('content' => $content), array(
                    'id' => $content_id
                ));
            } else {
                $this->link->add($this->articles_content, array(
                    'article_id' => $article_id,
                    'content' => $content,
                    'p' => $p,
                    'ct' => TIME
                ));
            }

            return $this->result(1, array('article_id' => $article_id));
        } else {
            $addTitle = $this->addTitle($uid, $title, BaseModule::TYPE_ARTICLE, $topic_ids);
            if ($addTitle) {
                $title_id = $addTitle['message'];

                //处理图片
                $this->saveImages($title_id, $images_list);

                //添加私有内容
                $article_id = $this->link->add($this->articles, array(
                    'title_id' => $title_id,
                    'category_id' => $category_id,
                    'summary' => $summary,
                ));

                if ($article_id && $content) {
                    $this->link->add($this->articles_content, array(
                        'article_id' => $article_id,
                        'content' => $content,
                        'p' => $p,
                        'ct' => TIME
                    ));
                }

                //添加动态
                $ACTIVITY = new ActivityModule();
                $ACTIVITY->add($uid, $title_id, ActivityModule::ARTICLE, $article_id);

                return $this->result(1, array('article_id' => $article_id));
            } else {
                return $this->result($addTitle['status']);
            }
        }
    }

    /**
     * 追加文章
     *
     * @param int $title_id
     * @param int $article_id
     * @param string $content
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function appendArticleContent($title_id, $article_id, $content)
    {
        $images_list = array();
        $title_id = (int)$title_id;
        $article_id = (int)$article_id;
        $content = parent::getContent($content, $images_list);
        if (empty($content)) {
            return $this->result(200610);
        }

        $contentInfo = $this->link->get($this->articles_content, 'MAX(p) p', array('article_id' => $article_id));
        $p = $contentInfo['p'] + 1;

        //处理图片
        $this->saveImages($title_id, $images_list, $p);

        $this->link->add($this->articles_content, array(
            'article_id' => $article_id,
            'content' => $content,
            'p' => $p,
            'ct' => TIME
        ));

        return $this->result(1, array('article_id' => $article_id));
    }

    /**
     * 保存内容中的图片
     *
     * @param int $title_id
     * @param array $images
     * @param int $page
     */
    private function saveImages($title_id, $images, $page = 1)
    {
        $TIM = new TitleImagesModule();
        $TIM->saveImages($title_id, $images, TitleImagesModule::LOCATION_CONTENT, $page);
    }

    /**
     * 更新标题
     *
     * @param int $title_id
     * @param $type
     * @param string $title_content
     * @param $topic_ids
     * @return bool
     */
    private function updateTitleAndTopic($title_id, $type, $title_content, $topic_ids)
    {
        //保存标题表
        $update_data = array(
            'title' => $title_content,
            'topic_ids' => !empty($topic_ids) ? implode(',', $topic_ids) : '',
        );
        $this->link->update($this->title, $update_data, array('title_id' => $title_id));

        //更新话题
        $this->updateTitleTopic($title_id, $type, $topic_ids);
    }

    /**
     * 更新话题
     *
     * @param int $title_id
     * @param int $type
     * @param array $topic_ids
     * @throws \Cross\Exception\CoreException
     */
    private function updateTitleTopic($title_id, $type, array $topic_ids)
    {
        //更新话题
        $saved_topic = array();
        $this->link->select('id,topic_id')->from($this->topic_title_id)
            ->where(array('title_id' => $title_id))
            ->stmt()->fetchAll(\PDO::FETCH_FUNC, function ($id, $topic_id) use (&$saved_topic) {
                $saved_topic[$topic_id] = $id;
            });

        //要添加的话题id和待删除的话题记录id
        $add_topic_ids = $del_topic_ids = array();
        if (empty($saved_topic)) {
            $add_topic_ids = $topic_ids;
        } else {
            foreach ($topic_ids as $topic_id) {
                if (isset($saved_topic[$topic_id])) {
                    unset($saved_topic[$topic_id]);
                } else {
                    $add_topic_ids[] = $topic_id;
                }
            }

            if (!empty($saved_topic)) {
                foreach ($saved_topic as $topic_id => $id) {
                    $del_topic_ids[] = $id;
                }
            }
        }

        //保存添加的话题
        if (!empty($add_topic_ids)) {
            $topicTitleData['fields'] = array('type', 'topic_id', 'title_id');
            foreach ($add_topic_ids as $topic_id) {
                $topicTitleData['values'][] = array(
                    $type, $topic_id, $title_id
                );
            }

            $this->link->add($this->topic_title_id, $topicTitleData, true);
        }

        //删除记录
        if (!empty($del_topic_ids)) {
            $this->link->del($this->topic_title_id, array('id' => array('IN', $del_topic_ids)));
        }
    }

    /**
     * 添加标题
     *
     * @param int $uid
     * @param string $title
     * @param int $type
     * @param array $topic_ids
     * @return array|bool|mixed|string
     * @throws \Cross\Exception\CoreException
     */
    private function addTitle($uid, $title, $type, array $topic_ids = array())
    {
        if (empty($uid)) {
            return $this->result(200010);
        }

        if (empty($title)) {
            return $this->result(200210);
        }

        $user_ip = $this->request->getUserIPAddress();
        $ipIsBlock = $this->ipIsBlock($user_ip);
        if ($ipIsBlock) {
            return $this->result(200015);
        }

        $save_topic = false;
        $topic_id_string = '';
        if (!empty($topic_ids)) {
            $save_topic = true;
            $topic_id_string = implode(',', $topic_ids);
        }

        $data = array(
            'title' => $title,
            'uid' => (int)$uid,
            'type' => $type,
            'topic_ids' => $topic_id_string,
            'post_ip' => Helper::getLongIp(),
            'post_time' => TIME,
            'last_interact_time' => TIME
        );

        $title_id = $this->link->add($this->title, $data);
        if ($title_id) {
            //保存标题和话题的对应关系
            if($save_topic) {
                $topicTitleData['fields'] = array('type', 'topic_id', 'title_id');
                foreach ($topic_ids as $topic_id) {
                    $topicTitleData['values'][] = array(
                        $type, $topic_id, $title_id
                    );
                }

                $this->link->add($this->topic_title_id, $topicTitleData, true);
            }

            return $this->result(1, $title_id);
        } else {
            $fail_status_config = array(
                self::TYPE_POSTS => 200212,
                self::TYPE_ARTICLE => 200213,
                self::TYPE_QUESTION => 200211,
            );

            return $this->result($fail_status_config[$type]);
        }
    }
}
