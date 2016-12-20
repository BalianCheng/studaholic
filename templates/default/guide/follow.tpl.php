<div class="container" id="followArea" style="display:none">
    <div class="row">
        <div class="col-md-9 col-sm-9 col-centered tac">
            <form action="" method="post">
                <input name="follow_data" id="follow_data" type="hidden"/>
                <input type="submit" class="btn btn-default btn-current btn-selected" style="margin:8px 18px;"
                       value="进入"/>
            </form>
        </div>
    </div>
</div>

<div class="container">
    <div class="row" id="recommend">
        <div class="text-center">
            <img src="<?php echo $this->res('images/load_content.gif') ?>" alt="正在获取推荐用户列表">
        </div>
    </div>
</div>

<script src="<?php echo $this->res("libs/artTemplate/3.0.1/template.js") ?>"></script>
<script id="recommendFollow" type="text/html">
    {{each recommend_data as d}}
    <div class="col-md-9 col-sm-9 col-centered">
        <div class="panel panel-cpf-default">
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
                                <button class="btn btn-default btn-current" f="1"
                                        onclick="followingUser(this, {{u.uid}})">
                                    已关注
                                </button>
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
    var follow_user = [], follow_data = $('#follow_data');
    function followingUser(ele, uid) {
        var e = $(ele), following = e.attr('f');
        if (following == 1) {
            follow_user.splice($.inArray(uid, follow_user), 1);
            e.removeClass('btn-current').attr('f', 0).html('关注');
        } else {
            follow_user.push(uid);
            e.addClass('btn-current').attr('f', 1).html('已关注');
        }

        follow_user = $.unique(follow_user);
        follow_data.val(follow_user.join(','));
    }

    $(function () {
        $.get('<?php echo $this->url('action:recommendUser') ?>', function (d) {
            if (d.data.recommend_list.length > 0) {
                var data = {recommend_data: d.data.recommend_list};
                follow_user = d.data.recommend_uid;
                follow_data.val(follow_user.join(','));
                document.getElementById('recommend').innerHTML = template('recommendFollow', data);

                $('#followArea').show();
            } else {
                window.location.href = '<?php echo $this->url() ?>';
            }
        });
    })
</script>


