let Highcharts = require('@npm/highcharts/highcharts')
/* global $, Translator, penaltyData */

$(function () {
  // Penalty chart
  if (document.getElementById('penalty') !== null) {
    Highcharts.chart('penalty', {
      chart: {
        type: 'pie',
      },
      plotOptions: {
        pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
            enabled: true,
          },
        },
      },
      title: {
        text: Translator.trans('penalty_name'),
      },
      series: [{
        name: Translator.trans('penalty_name'),
        data: penaltyData.nb,
      }],
    })
  }

  // Penalty victim chart
  if (document.getElementById('penaltyvic') !== null) {
    Highcharts.chart('penaltyvic', {
      chart: {
        type: 'pie',
      },
      plotOptions: {
        pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
            enabled: true,
          },
        },
      },
      title: {
        text: Translator.trans('Victim'),
      },
      series: [{
        name: Translator.trans('penalty_name'),
        data: penaltyData.victim,
      }],
    })
  }
})
