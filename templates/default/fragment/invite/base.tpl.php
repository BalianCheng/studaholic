<div class="row">
    <div class="col-md-12">
        <div class="panel panel-cpf-invite">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-4 col-xs-7 ft18" style="line-height: 34px;">
                        <?php echo $data['title'] ?>
                        <a href="javascript:void(0)" id="ref-invite" class="invite-change" title="换一换">
                            <i class="iconfont-small icon-ref"></i> 换一换
                        </a>
                    </div>
                    <div class="col-md-offset-5 col-md-3 col-xs-5 tar">
                        <div class="input-group">
                            <input type="text" class="form-control invite-input" id="search-user-input"
                                   placeholder="您想邀请的人">
                            <span class="input-group-btn">
                                <button class="btn btn-default" id="search-user-btn" type="button">
                                    搜索
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="inviteUserList"></div>
        </div>
    </div>
</div>
<script src="<?php echo $this->res("libs/artTemplate/3.0.1/template-debug.js") ?>"></script>
<script id="recommend_user" type="text/html">
    <div class="panel-body">
        <div class="row">
            {{each user_list as d}}
            <div class="col-md-6">
                <div class="media invite-list">
                    <div class="media-left">
                        <a href="{{d.homepage}}">
                            <img class="img-circle" src="{{d.avatar}}" alt="user avatar"
                                 style="width:36px;height:36px;"/>
                        </a>
                    </div>
                    <div class="media-body">
                        <div class="row">
                            {{if t == "answer"}}
                            <div class="col-xs-10">
                                <div class="user-info" style="margin:1px 0">
                                    <a href="{{d.homepage}}">{{d.nickname}}</a>
                                    {{if d.introduce}}, {{d.introduce}}{{/if}}
                                </div>
                                <div class="ft14">
                                    在话题 {{d.topic_name}} 下有{{d.answer_count}}个回答
                                </div>
                            </div>
                            {{else}}
                            <div class="col-xs-10">
                                <div class="user-info" style="margin:1px 0">
                                    <a href="{{d.homepage}}">{{d.nickname}}</a>
                                </div>
                                <div class="ft14">
                                    {{if d.introduce}} {{d.introduce}}{{/if}}
                                </div>
                            </div>
                            {{/if}}
                            <div class="col-xs-2 tac" style="margin-top:5px;">
                                <button class="btn btn-default invite-btn"
                                        onclick="inviteUser({{d.uid}}, <?php echo $data['title_id'] ?>)">
                                    邀请
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{/each}}
        </div>
    </div>
</script>
<script>
    //邀请
    function inviteUser(uid, title_id) {
        $.post('<?php echo $this->url('action:invite') ?>', {'uid': uid, 'title_id': title_id}, function (d) {
            layer.msg(d.message);
        });
    }

    var listDom = $('#inviteUserList');
    function getInviteUserList() {
        var aboutTopics = '<?php echo $data['topics'] ?>';
        $.post('<?php echo $this->url('action:followTopicUsers') ?>', {'topic_ids': aboutTopics}, function (d) {
            if (d.data.length == 0) {
                listDom.html('暂无推荐用户');
            } else {
                listDom.html(template('recommend_user', d.data));
            }
        });
    }

    $(function () {
        getInviteUserList();
        $('#ref-invite').on('click', function () {
            getInviteUserList();
        });

        $('#search-user-btn').on('click', function () {
            var input = $('#search-user-input'), username = input.val();
            if (username) {
                $.post('<?php echo $this->url('action:searchUsers') ?>', {'username': username}, function (d) {
                    if (d.status != 1) {
                        layer.msg(d.message);
                    } else if (d.data.user_list.length == 0) {
                        layer.msg('没找到该用户');
                    } else {
                        input.val('');
                        listDom.html(template('recommend_user', d.data));
                    }
                })
            } else {
                layer.msg('请输入你想邀请的人的名字');
            }
        });
    })
</script>
<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * reply.tpl.php
 */
