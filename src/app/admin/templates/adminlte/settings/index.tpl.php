<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * index.tpl.php
 */
$config = &$data['config'];

$smtp = &$config['smtp'];
$invite = &$config['invite'];
$rewrite = &$config['rewrite'];
$encrypt = &$config['encrypt'];
if (!empty($config['site_logo'])) {
}

$mode_options = array(0 => '综合', 1 => '问答', 2 => '论坛', 3 => '文章');
?>
<form action="" method="post">
    <div class="box box-primary">
        <div class="box-body table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th width="260">配置项</th>
                    <th>
                        <div class="row">
                            <div class="col-md-12">值</div>
                        </div>
                    </th>
                </tr>

                <tr>
                    <td colspan="2" class="title">基础配置</td>
                </tr>

                <tr>
                    <td class="control-label">网站名称</td>
                    <td>
                        <div class="row">
                            <div class="col-md-5">
                                <input type="text" class="form-control col-md-3" name="site_name"
                                       value="<?php echo $this->e($config, 'site_name') ?>">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="control-label">一句话介绍</td>
                    <td>
                        <div class="row">
                            <div class="col-md-5">
                                <input type="text" class="form-control col-md-3" name="introduce"
                                       value="<?php echo $this->e($config, 'introduce') ?>">
                            </div>
                            <div class="col-md-5 tips">
                                出现在登录和注册页面
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="control-label">前台访问路径</td>
                    <td>
                        <div class="row">
                            <div class="col-md-5">
                                <input type="text" class="form-control col-md-3" name="site_homepage"
                                       value="<?php echo $this->e($config, 'site_homepage') ?>">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="control-label">默认标题(title)</td>
                    <td>
                        <div class="row">
                            <div class="col-md-5">
                                <input type="text" class="form-control col-md-3" name="title"
                                       value="<?php echo $this->e($config, 'title') ?>">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="control-label">默认关键词(keywords)</td>
                    <td>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" class="form-control col-md-3" name="keywords"
                                       value="<?php echo $this->e($config, 'keywords') ?>">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="control-label">默认描述(description)</td>
                    <td>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" class="form-control col-md-3" name="description"
                                       value="<?php echo $this->e($config, 'description') ?>">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="title">REWRITE</td>
                </tr>

                <tr>
                    <td>服务器是否支持rewrite</td>
                    <td>
                        <div class="row">
                            <div class="col-md-1">
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="rewrite" data-toggle="toggle" data-on="是" data-off="否"
                                           data-size="small" <?php if ($rewrite) echo 'checked' ?>>
                                </label>
                            </div>
                            <div class="col-md-10 tips">
                                请确认服务器支持rewrite后再开启此配置
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="title">发现模式</td>
                </tr>

                <tr>
                    <td>发现模式选择</td>
                    <td>
                        <div class="row">
                            <div class="col-md-3">
                                <?php echo $this->select($mode_options, $config['mode'], array(
                                    'class' => 'form-control',
                                    'name' => 'mode'
                                ))
                                ?>
                            </div>
                            <div class="col-md-9 tips">
                                发现频道调用指定模块内容数据
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="title">默认模板</td>
                </tr>

                <tr>
                    <td>默认模板</td>
                    <td>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" class="form-control col-md-3" name="tpl_dir"
                                   value="<?php echo $this->e($config, 'tpl_dir', 'default') ?>">
                            </div>
                            <div class="col-md-10 tips">
                                默认模板路径
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="title">邀请注册</td>
                </tr>

                <tr>
                    <td>是否开启邀请注册</td>
                    <td>
                        <div class="row">
                            <div class="col-md-1">
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="invite" data-toggle="toggle" data-on="是" data-off="否"
                                           data-size="small" <?php if ($invite) echo 'checked' ?>>
                                </label>
                            </div>
                            <div class="col-md-10 tips">
                                开启以后需要输入邀请码才能注册
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="title">加密参数(留空时随机生成)</td>
                </tr>

                <tr>
                    <td class="control-label">uri(加密key)</td>
                    <td>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" class="form-control col-md-3" name="encrypt[uri]"
                                       value="<?php echo $this->e($encrypt, 'uri') ?>">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="control-label">COOKIE加密key</td>
                    <td>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" class="form-control col-md-3" name="encrypt[auth]"
                                       value="<?php echo $this->e($encrypt, 'auth') ?>">
                            </div>
                            <div class="col-md-10 tips">
                                更改后已登录用户需重新登录
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="title">邮件服务器</td>
                </tr>

                <tr>
                    <td class="control-label">SMTP服务器地址</td>
                    <td>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" class="form-control col-md-3" name="smtp[smtp_host]"
                                       value="<?php echo $this->e($smtp, 'smtp_host') ?>">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="control-label">SMTP服务器端口</td>
                    <td>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" class="form-control col-md-3" name="smtp[smtp_port]"
                                       value="<?php echo $this->e($smtp, 'smtp_port') ?>">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="control-label">SMTP用户名</td>
                    <td>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" class="form-control col-md-3" name="smtp[username]"
                                       value="<?php echo $this->e($smtp, 'username') ?>">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="control-label">SMTP密码</td>
                    <td>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" class="form-control col-md-3" name="smtp[password]"
                                       value="<?php echo $this->e($smtp, 'password') ?>">
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="box-footer">
            <button class="btn btn-primary">提交</button>
        </div>
    </div>
</form>


