<div class="col-md-12">
    <form class="form-horizontal" method="post">
        <div class="form-group">
            <label for="nickname" class="col-sm-2 control-label tal">昵称</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="nickname" name="nickname"
                       value="<?php echo $this->data['loginUser']['nickname'] ?>" placeholder="昵称">
            </div>

        </div>
        <div class="form-group">
            <label for="introduce" class="col-sm-2 control-label tal">一句话介绍自己</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="introduce" name="introduce"
                       value="<?php echo $this->data['loginUser']['introduce'] ?>" placeholder="一句话介绍自己">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">保存</button>
            </div>
        </div>
    </form>
</div>
<?php
/**
 * @Auth wonli <wonli@live.com>
 * base.tpl.php
 */
