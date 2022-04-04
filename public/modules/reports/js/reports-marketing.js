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

$('.box-export').on('click', function($q) {

  $.ajax({
      method: "GET",
      url: "http://dev.sirius.com/api/reports/marketing/resume?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
      dataType: "json",
      headers: {
          Authorization: $('meta[name="access-token"]').attr("content"),
          Accept: "application/json",
      },
      error: function error(response) {

      },
      success: function success(response) {

      }
  }); 

  $.ajax({
      method: "GET",
      url: "http://dev.sirius.com/api/reports/marketing/sales-by-state?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
      dataType: "json",
      headers: {
          Authorization: $('meta[name="access-token"]').attr("content"),
          Accept: "application/json",
      },
      error: function error(response) {

      },
      success: function success(response) {

      }
  }); 

});

