require('@app/js/tooltips')
let Highcharts = require('@npm/highcharts/highcharts')
/* global $, Translator, penaltyData, weaponData, bonusData */

$(function () {
  if (penaltyData) {
    document.querySelector('table.penalty').style.display = 'table'
    // Penalty chart
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
      credits: {
        enabled: false,
      },
      title: {
        text: Translator.trans('penalty_name'),
      },
      series: [{
        name: Translator.trans('penalty_name'),
        data: JSON.parse(penaltyData.replace(/&quot;/g, '"')).nb,
      }],
    })

    // Penalty victim chart
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
      credits: {
        enabled: false,
      },
      series: [{
        name: Translator.trans('penalty_name'),
        data: JSON.parse(penaltyData.replace(/&quot;/g, '"')).victim,
      }],
    })
  }

  // Weapons
  Highcharts.chart('weapons', {
    title: {
      text: '',
    },
    plotOptions: {
      column: {
        stacking: 'normal',
        dataLabels: {
          enabled: false,
        },
      },
    },
    xAxis: {
      type: 'category',
      categories: JSON.parse(weaponData.replace(/&quot;/g, '"')).categories,
      labels: {
        formatter: function () {
          return Translator.trans(this.value)
        },
      },
    },
    yAxis: {
      allowDecimals: false,
      title: {
        text: '',
      },
    },
    tooltip: {
      formatter: function () {
        let code = '<p style="font-size: 12px;"><b>' + Translator.trans(this.x) + ' :</b></p><br>'
        this.points.forEach(function (point) {
          code += '<span style="font-size: 10px"><span style="color:' + point.color + '">\u25CF</span> ' + point.series.name + ' : <b>' + point.y + '</b></span><br>'
        })
        return code
      },
      shared: true,
    },
    credits: {
      enabled: false,
    },
    series: JSON.parse(weaponData.replace(/&quot;/g, '"')).series,
  })

  // Bonus
  Highcharts.chart('bonus', {
    title: {
      text: '',
    },
    plotOptions: {
      column: {
        stacking: 'normal',
        dataLabels: {
          enabled: false,
        },
      },
    },
    xAxis: {
      categories: JSON.parse(bonusData.replace(/&quot;/g, '"')).categories,
      labels: {
        formatter: function () {
          return Translator.trans('bonus.' + this.value)
        },
      },
    },
    yAxis: {
      allowDecimals: false,
      title: {
        text: '',
      },
    },
    tooltip: {
      formatter: function () {
        let code = '<p style="font-size: 12px;"><b>' + Translator.trans('bonus_name') + ' "' + Translator.trans('bonus.' + this.x) + '" :</b></p><br>'
        this.points.forEach(function (point) {
          code += '<span style="font-size: 10px"><span style="color:' + point.color + '">\u25CF</span> ' + point.series.name + ' : <b>' + point.y + '</b></span><br>'
        })
        return code
      },
      shared: true,
    },
    credits: {
      enabled: false,
    },
    series: JSON.parse(bonusData.replace(/&quot;/g, '"')).series,
  })
})
