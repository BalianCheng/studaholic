<?php
/**
 * @Auth wonli <wonli@live.com>
 * seo.tpl.php
 */
$current = &$data['current'];
$category = &$data['category'];
$controllerNameList = &$data['controller_list'];
$seoKeyToName = array('title' => '标题', 'keywords' => '关键词', 'description' => '描述');
?>
<div class="row">
    <div class="col-md-2">
        <div class="box box-primary">
            <div class="box-body">
                <ul class="list-unstyled seo-category-list">
                    <?php
                    if (!empty($controllerNameList)) {
                        foreach ($controllerNameList as $categoryIndex => $name) {
                            $attr = array();
                            if ($category == $categoryIndex) {
                                $attr['class'] = 'active';
                            }

                            echo $this->wrap('li', $attr)
                                ->a($name, $this->url('settings:seo', array('category' => $categoryIndex)));
                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="box-footer">
                <a href="<?php echo $this->url('settings:updateSeoConfig') ?>"
                   style="display:block;text-align:center;min-width:100%"
                   class="btn btn-success">更新缓存</a>
            </div>
        </div>
    </div>

    <div class="col-md-10">
        <form action="" class="form" method="post">
            <div class="box">
                <div class="box-body">
                    <input name="id" value="<?php echo $current['id'] ?>" type="hidden"/>
                    <?php
                    foreach ($seoKeyToName as $key => $name) {
                        $value = '';
                        if (!empty($current[$key])) {
                            $value = $current[$key];
                        }
                        ?>
                        <div class="form-group">
                            <label for=""><?php echo $name ?></label>
                            <input type="text" class="form-control" name="<?php echo $key ?>" value="<?php echo $value ?>"
                                   placeholder="留空使用全站通用配置">
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="box-footer">
                    <input type="submit" class="btn btn-primary" value="保存">
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="topicSaveModal"></div>
<div class="modal fade" id="newRootTopicModal"></div>
<script>
    $(function(){
        $('#viewData').on('click', function(){
            $.post('<?php echo $this->url('settings:dataPreview') ?>', {'category':$(this).attr('category')}, function(d) {
                console.log(d);
            })
        })
    })
</script>



