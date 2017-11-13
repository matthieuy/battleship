let Highcharts = require('@npm/highcharts/highcharts')
/* global $, Translator, penaltyData, weaponData, bonusData */

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
      text: Translator.trans('weapon_name'),
    },
    xAxis: {
      categories: JSON.parse(weaponData.replace(/&quot;/g, '"')).categories,
      labels: {
        formatter: function () {
          return Translator.trans(this.value)
        },
      },
    },
    tooltip: {
      formatter: function () {
        return '<span style="font-size: 10px">' + Translator.trans(this.key) + '</span><br/><span style="color:' + this.color + '">\u25CF</span> ' + this.series.name + ': <b>' + this.y + '</b><br/>'
      },
    },
    yAxis: {
      allowDecimals: false,
    },
    credits: {
      enabled: false,
    },
    series: JSON.parse(weaponData.replace(/&quot;/g, '"')).series,
  })

  // Bonus
  Highcharts.chart('bonus', {
    title: {
      text: Translator.trans('bonus_name'),
    },
    xAxis: {
      categories: JSON.parse(bonusData.replace(/&quot;/g, '"')).categories,
      labels: {
        formatter: function () {
          return Translator.trans('bonus.' + this.value)
        },
      },
    },
    tooltip: {
      formatter: function () {
        return '<span style="font-size: 10px">' + Translator.trans(this.key) + '</span><br/><span style="color:' + this.color + '">\u25CF</span> ' + this.series.name + ': <b>' + this.y + '</b><br/>'
      },
    },
    yAxis: {
      allowDecimals: false,
    },
    credits: {
      enabled: false,
    },
    series: JSON.parse(bonusData.replace(/&quot;/g, '"')).series,
  })
})
