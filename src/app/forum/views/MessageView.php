<?php
/**
 * @Auth wonli <wonli@live.com>
 * MessageView.php
 */

namespace app\forum\views;

/**
 * @Auth wonli <wonli@live.com>
 * Class MessageView
 * @package app\forum\views
 */
class MessageView extends ForumView
{
    function with($data = array())
    {
        $this->set(array(
            'layer' => 'empty'
        ));

        $this->renderTpl('message/detail', $data);
    }

    /**
     * 消息列表
     *
     * @param array $data
     */
    function dialogMessageList($data)
    {
        foreach ($data as $d) {
            if ($d['sender'] == $this->data['loginUser']['uid']) {
                $this->renderTpl('fragment/message/left', $d);
            } else {
                $this->renderTpl('fragment/message/right', $d);
            }
        }
    }
}
