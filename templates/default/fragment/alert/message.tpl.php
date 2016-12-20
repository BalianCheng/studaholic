<?php
/**
 * @Auth wonli <wonli@live.com>
 * message.tpl.php
 */
$class = sprintf('alert alert-%s alert-dismissible', $data['alert_type']);
$style = 'margin-top:60px;';

?>
<div class="row">
    <div class="<?php echo $data['wrap_class'] ?>">
        <div class="<?php echo $class ?>" role="alert">

            <?php if ($data['can_close']) : ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            <?php endif ?>

            <strong><?php echo $data['alert_title'] ?></strong>
            <?php echo $data['message'] ?>
        </div>
    </div>
</div>

