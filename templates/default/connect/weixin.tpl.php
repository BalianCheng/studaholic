<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * weixin.tpl.php
 */
$config = &$data['config'];
$wxParams = json_encode(array(
    'id' => 'login_container',
    'appid' => $config['app_id'],
    'scope' => 'snsapi_login',
    'redirect_uri' => $config['call_back'],
));
?>
<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            <div id="login_container"></div>
        </div>
    </div>
</div>
<script src="//res.wx.qq.com/connect/zh_CN/htmledition/js/wxLogin.js"></script>
<script>
    $(function(){
        var obj = new WxLogin(<?php echo $wxParams ?>);
    });
</script>
