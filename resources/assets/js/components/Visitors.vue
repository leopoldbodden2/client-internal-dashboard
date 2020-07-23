<template>
    <div class="container-fluid form-inline">
        <div class="form-group date-range-group">
            <label for="dateRange">Date Range: </label>&nbsp;
            <select class="form-control" v-model="dateRange" @change="fetchData">
                <option value="7">Last 7 Days</option>
                <option value="30">Last 30 Days</option>
                <option value="60">Last 60 Days</option>
                <option value="90">Last 90 Days</option>
            </select>
        </div>
        <canvas ref="analyticsChart" class="w-100"></canvas>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                dateRange: '7',
                chartjs: {},
                analyticsChart: {
                    responsive: true,
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Visitors',
                            lineTension: 0,
                            backgroundColor: 'rgba(87, 160, 190, 0.2)',
                            borderColor: window.ThemeColors[0],
                            pointBackgroundColor: window.ThemeColors[0],
                            pointRadius: 5,
                            pointHoverRadius: 5,
                            pointHitRadius: 5,
                            data: []
                        }]
                    },
                    options: {
                        responsive: true,
                        chartArea: {
        					backgroundColor: '#fff'
        				},
                        layout: {
                            padding: {
                              top: 15
                            }
                        },
                        legend:{
                            display: false
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                gridLines: {
                                    display: false
                                }
                            }],
                            yAxes: [{
                                position: 'right',
                                ticks: {
                                    beginAtZero: true,
                                    userCallback: function(label, index, labels) {
                                        // when the floored value is the same as the value we have a whole number
                                        if (Math.floor(label) === label) {
                                            return label;
                                        }

                                    },
                                }
                            }]
                        },
                        tooltips: {
                            backgroundColor: '#fff',
                            titleFontFamily: "'Open Sans',sans-serif",
                            titleFontSize: 12,
                            titleFontColor: '#000',
                            titleFontStyle: '300',
                            bodyFontFamily: "'Open Sans',sans-serif",
                            bodyFontSize: 14,
                            bodyFontColor: window.ThemeColors[0],
                            bodyFontStyle: 'normal',
                            shadowOffsetX: 3,
                            shadowOffsetY: 3,
                            shadowBlur: 10,
                            shadowColor: 'rgba(0, 0, 0, 0.5)',
                            custom: function(tooltip) {
                                if (!tooltip) return;
                                // disable displaying the color box;
                                tooltip.displayColors = false;
                            },
                            callbacks: {
                                title: function(tooltipItem, data) {
                                    var datelabel = tooltipItem[0].xLabel || '';
                                    if (datelabel) {
                                        var momentlabel = moment(datelabel, 'MM/DD');
                                        if(momentlabel){
                                            return momentlabel.format('dddd MMMM D, YYYY');
                                        }
                                    }
                                    return datelabel;
                                },
                                label: function(tooltipItem, data) {
                                    var label = tooltipItem.yLabel || 0;
                                    return label+' Visitors';
                                }
                            }
                        }
                    }
                }
            }
        },
        methods: {
            fetchData(){
                localStorage.dateRange = this.dateRange;
                window.axios.post(APP_URL+'/api/visitors',{
                        date_range: this.dateRange
                    })
                    .then(response => response.data)
                    .then(data => {
                        this.analyticsChart.data = {
                            labels: data.labels,
                            datasets: [{
                                label: 'Visitors',
                                lineTension: 0,
                                backgroundColor: 'rgba(87, 160, 190, 0.2)',
                                borderColor: window.ThemeColors[0],
                                pointBackgroundColor: window.ThemeColors[0],
                                pointRadius: 5,
                                pointHoverRadius: 5,
                                pointHitRadius: 5,
                                data: data.datasets
                            }]
                        };

                        this.chartjs.update()
                    })
                    .catch(error => {
                        console.log(error);
                    });
            }
        },
        mounted(){
            if (localStorage.dateRange) {
                this.dateRange = localStorage.dateRange;
            }
            this.chartjs = new Chart(this.$refs.analyticsChart.getContext('2d'), this.analyticsChart);
            this.fetchData();
        }
    }
</script>
