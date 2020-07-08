$(function () {
    var $katColors = ['#f0910f', '#9c6372', '#656049', '#e91b53', '#ca001f', '#02c89b', '#ca00a7', '#fca570', '#61c505', '#035bc7', '#7901c9', '#d6e41f', '#60c1f0', '#f4d25b', '#c2eb65', '#94bc9a', '#cbba85', '#9ca2b4'];

    new Morris.Bar({
        element: 'count_reg',
        data: $data_reg,
        xLabelAngle: 30,
        resize: true,
        // barRatio: 0.9,
        parseTime: false,
        gridTextSize : 10,
        xkey: ['reg'],
        ykeys: ['count_reg'],
        labels: ['reg'],
        barColors: function (row, series, type) {
            return $katColors[row.x];
        },
    });
});