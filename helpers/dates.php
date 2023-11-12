<?php 

function format_date($date, $show_time = true) {
    $d = new DateTime($date);

    $pattern = $show_time
        ? 'd \'de\' MMMM \'de\' Y \'a las\' HH:mm \'hs\''
        : 'd \'de\' MMMM \'de\' Y';

    $formatter = new IntlDateFormatter(
        'es_ES',
        IntlDateFormatter::LONG,
        $show_time ? IntlDateFormatter::SHORT : IntlDateFormatter::NONE,
        date_default_timezone_get(),
        IntlDateFormatter::GREGORIAN,
        $pattern
    );

    return $formatter->format($d);
}
