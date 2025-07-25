function createCircleChart(percent, color, secondaryColor, size, stroke) {
    let svg = `<svg class="mkc_circle-chart" viewbox="0 0 36 36" width="${size}" height="${size}" xmlns="http://www.w3.org/2000/svg">
        <path class="mkc_circle-bg"  stroke="${secondaryColor || '#eeeee'}" stroke-width="${stroke * 0.9}" fill="none" d="M18 2.0845
              a 15.9155 15.9155 0 0 1 0 31.831
              a 15.9155 15.9155 0 0 1 0 -31.831"/>
        <path class="mkc_circle" stroke="${color}" stroke-width="${stroke}" stroke-dasharray="${percent},100" stroke-linecap="round" fill="none" style=" ${ percent < 1 ? 'display: none' : '' }" 
            d="M18 2.0845
              a 15.9155 15.9155 0 0 1 0 31.831
              a 15.9155 15.9155 0 0 1 0 -31.831" />
    </svg>`;
    return svg;
}


{/* <text class="mkc_info" x="50%" y="50%" alignment-baseline="central" text-anchor="middle" font-size="8">${percent}%</text> */}

const mkChartRender = () => {
    let charts = document.getElementsByClassName('mkCharts');

    for(let i=0;i<charts.length;i++) {
        let chart = charts[i];
        let percent = chart.dataset.percent;
        let color = ('color' in chart.dataset) ? chart.dataset.color : "#2F4F4F";
        let secondaryColor = ('border' in chart.dataset) ? chart.dataset.border : "#EEEEEE";
        let size = ('size' in chart.dataset) ? chart.dataset.size : "100";
        let stroke = ('stroke' in chart.dataset) ? chart.dataset.stroke : "1";
        charts[i].innerHTML = createCircleChart(percent, color, secondaryColor, size, stroke);
    }
};

mkChartRender();

