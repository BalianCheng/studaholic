/*! Select2 4.0.2 | https://github.com/select2/select2/blob/master/LICENSE.md */

(function () {
    if (jQuery && jQuery.fn && jQuery.fn.select2 && jQuery.fn.select2.amd)var e = jQuery.fn.select2.amd;
    return e.define("select2/i18n/zh-CN", [], function () {
        return {
            errorLoading: function () {
                return "无法载入结果。"
            }, inputTooLong: function (e) {
                var t = e.input.length - e.maximum, n = "请删除" + t + "个字符";
                return n
            }, inputTooShort: function (e) {
                return "请输入话题名称";
            }, loadingMore: function () {
                return "载入更多结果…"
            }, maximumSelected: function (e) {
                return "最多只能添加" + e.maximum + "个话题";
            }, noResults: function () {
                return "请选择已创建的话题"
            }, searching: function () {
                return "搜索中…"
            }
        }
    }), {define: e.define, require: e.require}
})();
