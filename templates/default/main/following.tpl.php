<div class="container">
    <div class="row" id="recommend"></div>
</div>
<script src="<?php echo $this->res("libs/artTemplate/3.0.1/template.js") ?>"></script>
<script id="recommendFollow" type="text/html">
    {{each recommend_data as d}}
    <div class="col-md-12">
        <div class="panel panel-cpf-default content-box">
            <div class="panel-heading">
                <h4>{{d.type_title}}</h4>
            </div>
            <div class="panel-body">
                {{each d.user_list as u}}
                <div class="media">
                    <div class="media-left">
                        <a href="{{u.homepage}}">
                            <img class="img-circle" src="{{u.avatar}}" alt="user avatar"
                                 style="width:48px;height:48px;"/>
                        </a>
                    </div>
                    <div class="media-body">
                        <div class="row">
                            <div class="col-xs-9">
                                <h5>
                                    <a href="{{u.homepage}}">
                                        {{u.nickname}}
                                    </a>
                                </h5>
                                {{u.introduce}}
                            </div>
                            <div class="col-xs-3">
                                {{if u.following_status == 0}}
                                <button class="btn btn-default" onclick="followingUser(this, {{u.uid}})">
                                    关注TA
                                </button>
                                {{else}}
                                <button class="btn btn-default btn-current" onclick="followingUser(this, {{u.uid}})">
                                    已关注
                                </button>
                                {{/if}}
                            </div>
                        </div>
                    </div>
                </div>
                {{/each}}
            </div>
        </div>
    </div>
    {{/each}}
</script>
<script>
    function followingUser(self, uid) {
        var apiUrl = '<?php echo $this->url('action:following', array('type' => 'user')) ?>';
        $.post(apiUrl, {'uid': uid}, function (d) {
            if (d.status == 1) {
                if (d.data.act == 'follow') {
                    $(self).addClass('btn-current').html('已关注');
                } else {
                    $(self).removeClass('btn-current').html('关注TA');
                }

                location.href = '<?php echo $this->url() ?>';
            } else {
                layer.msg(d.message);
            }
        });
    }

    $(function () {
        $.get('<?php echo $this->url('action:recommendUser') ?>', function (d) {
            var data = {recommend_data: d.data.recommend_list};
            document.getElementById('recommend').innerHTML = template('recommendFollow', data);
        });
    })
</script>
<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * following.tpl.php
 */


