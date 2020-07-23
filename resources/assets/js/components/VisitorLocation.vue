<template>
    <div class="container-fluid">
        <div class="form-group row date-range-group">
            <label for="dateRange" class="col-auto col-form-label">Date Range</label>
            <div class="col-auto">
                <select class="form-control" v-model="dateRange" @change="fetchData">
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="60">Last 60 Days</option>
                    <option value="90">Last 90 Days</option>
                </select>
            </div>
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
                    type: 'pie',
                    data: {
                        labels: [],
                        datasets: [{
                            fill: true,
                            backgroundColor: window.ThemeColors,
                            borderColor: '#ccc',
                            borderWidth: 0,
                            data: []
                        }]
                    },
                    options: {
                        legend:{
                            display: true,
                            position: 'left',
                            labels: {
                                usePointStyle:true,
                                pointStyle: 'circle',
                                fontSize: 12
                            }
                        },
                        responsive: true,
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
                            }
                        }
                    }
                }
            }
        },
        methods: {
            fetchData(){
                localStorage.dateRange = this.dateRange;
                window.axios.post(APP_URL+'/api/visitor-location',{
                        date_range: this.dateRange
                    })
                    .then(response => response.data)
                    .then(data => {
                        this.analyticsChart.data = {
                            labels: data.labels,
                            datasets: [{
                                fill: true,
                                backgroundColor: window.ThemeColors,
                                borderColor: '#ccc',
                                borderWidth: 0,
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
