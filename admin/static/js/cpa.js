var hashData = {
    read: function () {
        var hashData = document.location.hash;
        hashData = hashData.slice(1);
        var tempArr = new Array();
        tempArr = hashData.split(",");
        var value = 0;
        var object = {};
        for (var i = 0; i < tempArr.length; i++) {
            if (!tempArr[i])
                continue;
            var cutNum = (tempArr[i]).indexOf("=");//参数分割符号
            var menuName = (tempArr[i]).substr(0, cutNum);
            object[menuName] = (tempArr[i]).substr(cutNum + 1);
        }
        return object;
    },
    write: function (a, v) {
        var hd = hashData.read();
        var hashStr = "#";
        var flag = 0;
        for (var attr in hd) {
            if (a == attr) {
                if (v != null) {
                    hashStr = hashStr + attr + "=" + v + ",";
                }
                flag = 1;
            }
            else {
                hashStr = hashStr + attr + "=" + hd[attr] + ",";
            }
        }
        if (flag == 0 && v != null) {
            hashStr = hashStr + a + "=" + v + ",";
        }
        document.location.hash = hashStr;
    }
};

$.jheartbeat = {
    options: {delay: 10000},
    beatfunction: function () {
    },
    timeoutobj: {id: -1},

    set: function (options, onbeatfunction) {
        if (this.timeoutobj.id > -1) {
            clearTimeout(this.timeoutobj);
        }
        if (options) {
            $.extend(this.options, options);
        }
        if (onbeatfunction) {
            this.beatfunction = onbeatfunction;
        }

        this.timeoutobj.id = setTimeout("$.jheartbeat.beat();", this.options.delay);
    },

    beat: function () {
        this.timeoutobj.id = setTimeout("$.jheartbeat.beat();", this.options.delay);
        this.beatfunction();
    }
};

function timer(func, interval) {
    $.jheartbeat.set({delay: interval}, func);
}

$(function () {
    $('.confirm-href-flag').on('click', function () {
        var t = $(this).attr('title') || '确定执行该操作吗?',
            a = $(this).attr('action');
        layer.confirm(t, {
            title: false,
            btn: ['确定', '取消']
        }, function () {
            if(a) {
                location.href = a;
            } else {
                layer.msg('未设定跳转地址')
            }
        });
    });

    $('.pop-alert-flag').on('click', function () {
        layer.msg($(this).attr('title'));
    });
});
