<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * correlation_content.tpl.php
 */
?>
<div class="panel-cpf-slide">
    <div class="panel-heading ft18">
        <div style="margin:10px 0">
            相关内容
        </div>
    </div>
    <div class="panel-body">
        <?php
        $li = '';
        foreach ($data as $d) {
            switch ($d['type']) {
                case 1:
                    $url = $this->url('content:question', array('question_id' => $d['question_id']));
                    break;

                case 2:
                    $url = $this->url('content:posts', array('posts_id' => $d['posts_id']));
                    break;

                case 3:
                    $url = $this->url('content:article', array('article_id' => $d['article_id']));
                    break;

                default:
                    $url = false;
            }

            if ($url) {
                $li .= $this->wrap('li')->a($d['title'], $url, array('class' => 'ia', 'title' => $d['title']));
            }
        }

        echo $this->wrap('ul', array('class' => 'correlation-list'))->html($li);
        ?>
    </div>
</div>
