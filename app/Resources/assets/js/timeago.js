require('./vendor/jquery.timeago')
/* global jQuery, Translator */

function translateTimeago () {
  jQuery.timeago.settings.strings = {
    prefixAgo: Translator.trans('prefixAgo'),
    prefixFromNow: Translator.trans('prefixFromNow'),
    suffixAgo: Translator.trans('suffixAgo'),
    suffixFromNow: Translator.trans('suffixFromNow'),
    lessMinute: Translator.trans('lessMinute'),
    seconds: Translator.trans('seconds'),
    second: Translator.trans('second'),
    minute: Translator.trans('minute'),
    minutes: Translator.trans('minutes'),
    hour: Translator.trans('hour'),
    hours: Translator.trans('hours'),
    day: Translator.trans('day'),
    days: Translator.trans('days'),
    month: Translator.trans('month'),
    months: Translator.trans('months'),
    year: Translator.trans('year'),
    years: Translator.trans('years'),
    wordSeparator: Translator.trans('wordSeparator'),
  }
}

jQuery(() => {
  translateTimeago()
})
