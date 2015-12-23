var ReportMatch = { 
    report : function(){
        Highcharts.setOptions({
            colors: ['#1AA53B', '#9F224E', '#ACACAC']
        });
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'hinhtron_tk',
                backgroundColor: null,
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false, 
                width: 180,
                height: 180,
                marginRight: 30
            },
            credits: {
                enabled: false
            },
            title: {
                text: null
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        formatter: function() {   }
                    }
                }
            },
            exporting: {
                enabled: false
            },
            series: [{
                type: 'pie',
                name: 'Browser share',
                data: [
                    [team1Name, percentTeam1],
                    [team2Name, percentTeam2],
                    ['Hòa',     percentDrawn],
                ]
            }]
        }); 
    }
};

$(function(){        
    ReportMatch.report();
});



