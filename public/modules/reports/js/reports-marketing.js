$(function() {
    newGraphPieMkt();
});


function newGraphPieMkt() {
    new Chartist.Pie('.new-graph-pie-mkt', {
        series: [18, 16, 10, 6, 32]
      }, {
        donut: true,
        donutWidth: 28,
        donutSolid: true,
        startAngle: 270,
        showLabel: false,
        chartPadding: 0,
        labelOffset: 0,
      });
}