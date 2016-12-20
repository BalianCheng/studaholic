<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * index.tpl.php
 */
use app\forum\modules\common\BaseModule;

$content = &$data['content'];
$contentStat = &$data['contentStat'];
$registerStat = &$data['registerStat'];
?>
<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-user"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">用户</span>
                <span class="info-box-number"><?php echo $this->e($data, 'totalUser') ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-question-circle-o"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">问题</span>
                <span class="info-box-number"><?php echo $this->e($content, BaseModule::TYPE_QUESTION, 0) ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-quote-left"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">帖子</span>
                <span class="info-box-number"><?php echo $this->e($content, BaseModule::TYPE_POSTS, 0) ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-files-o"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">文章</span>
                <span class="info-box-number"><?php echo $this->e($content, BaseModule::TYPE_ARTICLE, 0) ?></span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">内容</h3>
                <div class="box-tools pull-right">
                    <div class="btn-group" data-toggle="btn-toggle">
                        <button type="button" class="btn btn-default btn-xs"><i class="fa fa-calendar"></i></button>
                        <button type="button" class="btn btn-default btn-xs" id="cdp">
                            <span>今天</span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="box-body chart-responsive">
                <canvas id="contentChart" style="height:250px"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">用户</h3>
                <div class="box-tools pull-right">
                    <div class="btn-group" data-toggle="btn-toggle">
                        <button type="button" class="btn btn-default btn-xs"><i class="fa fa-calendar"></i></button>
                        <button type="button" class="btn btn-default btn-xs" id="rdp">
                            <span>今天</span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="box-body chart-responsive">
                <canvas id="regChart" style="height:250px"></canvas>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo $this->res('libs/daterangepicker/2.1.24/moment.min.js') ?>"></script>
<script src="<?php echo $this->res('libs/daterangepicker/2.1.24/daterangepicker.js') ?>"></script>
<script src="<?php echo $this->res('libs/chartjs/2.2.1/chart.min.js') ?>"></script>
<link rel="stylesheet" href="<?php echo $this->res('libs/daterangepicker/2.1.24/daterangepicker.css') ?>">
<script>
    $(function () {
        var p = {
                config: {
                    locale: {
                        format: 'YYYY-MM-DD', applyLabel: '确定', cancelLabel: '取消', customRangeLabel: '自定义',
                        daysOfWeek: ['日', '一', '二', '三', '四', '五', '六'],
                        monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月']
                    },
                    ranges: {
                        '今天': [moment(), moment()],
                        '昨天': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        '最近7天': [moment().subtract(6, 'days'), moment()],
                        '最近30天': [moment().subtract(29, 'days'), moment()],
                        '本月': [moment().startOf('month'), moment().endOf('month')],
                        '上月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment(),
                    endDate: moment()
                },
                action: function (start, end) {
                    $('#' + this.element[0].id + ' span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                }
            },
            ccd = {
                labels: ['问题', '帖子', '文章'],
                datasets: [{
                    data: <?php echo json_encode($contentStat) ?>,
                    backgroundColor: ["#FF6384", "#FFCE56", "#36A2EB"],
                    hoverBackgroundColor: ["#FF6384", "#FFCE56", "#36A2EB"]
                }]
            },
            crd = {
                labels: <?php echo json_encode($registerStat['labels']) ?>,
                datasets: [
                    {label: "新增用户数", backgroundColor: '#00c0ef', data: <?php echo json_encode($registerStat['data']) ?>}
                ]
            };

        //内容统计
        var contentChart = new Chart(document.getElementById('contentChart'), {
            type: 'pie', data: ccd
        });

        //注册统计
        var regChart = new Chart(document.getElementById('regChart'), {
            type: 'bar',
            data: crd,
            options: {
                scales: {
                    xAxes: [{stacked: true}],
                    yAxes: [{stacked: true, ticks: {min: 0, stepSize:1}}]
                }
            }
        });

        //日期选择
        $('#cdp, #rdp').daterangepicker(p.config, p.action).on('apply.daterangepicker', function (ev, picker) {
            var target = ev.delegateTarget.id,
                data = {s: picker.startDate.format('YYYY-MM-DD'), e: picker.endDate.format('YYYY-MM-DD')};

            if (target == 'cdp') {
                $.post('<?php echo $this->url('panel:contentStat') ?>', data, function (d) {
                    contentChart.data.datasets[0]['data'] = d.data;
                    contentChart.update();
                });
            } else {
                $.post('<?php echo $this->url('panel:userStat') ?>', data, function (d) {
                    regChart.data.labels = d.labels;
                    regChart.data.datasets[0]['data'] = d.data;
                    regChart.update();
                });
            }
        });
    });
</script>


