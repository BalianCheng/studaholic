/**
 * Created by ideaa on 2016/5/9.
 */
wangEditor.config.printLog = false;
var config = {
    menus: {
        simple: [
            'ahead', 'quote', '|',
            'bold', 'italic', '|',
            'link', 'img', '|',
            'fullscreen'
        ]
    },
    uploadAction: function (editor) {
        return {
            onload: function (result, xhr) {
                // result 服务器端返回的text
                // xhr 是 xmlHttpRequest 对象，IE8、9中不支持
                if (typeof result != "object") {
                    result = $.parseJSON(result);
                }

                if (result.status != 1) {
                    var notice = result.message;
                    if (result.data) {
                        notice = notice + "<br>" + result.data;
                    }
                    layer.msg(notice);
                } else {
                    var storage = result.data.storage, origin = result.data.origin, src = storage + origin;
                    editor.command(null, 'insertHtml', '<img src="' + src + '" storage="' + storage + '" origin="' +
                        origin + '" style="max-width:780px;" local="' + result.data.local + '" alt="upload image"/>');
                }
            },
            onerror: function (xhr) {
                layer.msg('上传超时');
            },
            ontimeout: function (xhr) {
                layer.msg('上传错误');
            }
        }
    }
};

//标题插件
(function (E, $) {
    E.createMenu(function (check) {
        var menuId = 'ahead';
        if (!check(menuId)) {
            return;
        }
        var editor = this, lang = editor.config.lang, data = {'<h3>': '标题', '<h4>': '子标题'}, ids = [],
            isOrderedList, tpl = '<div style="margin:5px 10px 10px 0">{#commandValue}{#title}</div>', menu = new E.Menu({
                editor: editor,
                id: menuId,
                title: lang.head,
                commandName: 'formatBlock'
            });

        E.UI.menus.ahead = {
            normal: '<a href="#" tabindex="-1"><i class="wangeditor-menu-img-header"></i></a>',
            selected: '.selected'
        };

        menu.dropList = new E.DropList(editor, menu, {
            data: data,
            tpl: tpl,
            beforeEvent: function beforeEvent(e) {
                if (editor.queryCommandState('InsertOrderedList')) {
                    isOrderedList = true;
                    editor.command(e, 'InsertOrderedList');
                } else {
                    isOrderedList = false;
                }
            },
            afterEvent: function afterEvent(e) {
                if (isOrderedList) {
                    editor.command(e, 'InsertOrderedList');
                }
            }
        });

        menu.updateSelectedEvent = function () {
            var rangeElem = editor.getRangeElem();
            rangeElem = editor.getSelfOrParentByName(rangeElem, 'h3,h4');
            return !!rangeElem;
        };
        editor.menus[menuId] = menu;
    });

})(window.wangEditor, window.jQuery);

/**
 * 初始化编辑器
 *
 * @param documentID
 * @param uploadUrl
 * @param enable
 * @returns {wangEditor}
 */
function editor(documentID, uploadUrl, enable) {
    var editor = new wangEditor(documentID), is_enable = enable||1;
    editor.config.uploadImgUrl = uploadUrl;
    editor.config.uploadImgFns = config.uploadAction(editor);
    editor.config.menus = config.menus.simple;
    editor.create();
    if(is_enable == 1) {
        return editor;
    } else {
        editor.disable();
    }
}
