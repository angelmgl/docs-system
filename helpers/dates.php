<?php 

function format_date($date) {
    $d = new DateTime($date);

    $formatter = new IntlDateFormatter(
        'es_ES',
        IntlDateFormatter::LONG,
        IntlDateFormatter::SHORT,
        date_default_timezone_get(),
        IntlDateFormatter::GREGORIAN,
        'd \'de\' MMMM \'de\' Y \'a las\' HH:mm \'hs\''
    );

    return $formatter->format($d);
}
