<div class="container">
    <div class="row" style="padding:20px 0 10px; 0;border-bottom: 1px solid #f1f1f1">
        <div class="col-md-12">
            <h4><?php echo $data['page']['result_count'] ?> 条搜索结果</h4>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            if (!empty($data['result'])) {
                $this->contentListSection($data['result'], 'search/content', array('class' => 'search-result-list'));
            }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 tac">
            <?php $this->page($data['page']) ?>
        </div>
    </div>
</div>

<?php
/**
 * @Auth wonli <wonli@live.com>
 * index.tpl.php
 */
