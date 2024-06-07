<?php

/**
 * Get data array for index display.
 *
 * @param string $title The title of the page
 * @param int $active The id of active menu in sidebar
 * @param int $activeChild The id of active sub-menu in sidebar
 * @param array|null $data Any additional information needed (default is null).
 *
 * @return array Associative array containing data for index display.
 */
function getIndexData($title = null, $data = null)
{
    return array(
        "title" => $title . ' | ' . config('app.name'),
        "data" => $data
    );
}

/**
 * Get data array for request response result.
 *
 * @param bool $status The status of the response
 * @param string $message (Optional) The response message (if not provided, a default error message is used)
 *
 * @return string JSON-encoded string representing the response result.
 */
function getResponseData($status, $message = '')
{
    // Set a default error message if status is false and message is empty.
    if (!$status && $message === '') {
        $message = "An error occurred while processing your request!";
    }

    return json_encode(array(
        "status" => $status,
        "message" => $message
    ));
}

/**
 * Convert a numeric price value to Indonesian numeric format.
 * 
 * @param int $price The numeric price value to be converted.
 * 
 * @return string The price in Indonesian numeric format.
 */
function formatPrice($price)
{
    return number_format($price, 0, ',', '.');
}

/**
 * Convert a string date value to readable date format.
 * 
 * @param string $price The string date value to be converted.
 * @param string $format (Optional) The desired date format (the default date format is 1 Jan 2000).
 * 
 * @return string The formatted date.
 */
function formatDate($date, $format = "j M Y")
{
    return date_format(date_create($date), $format);
}

/**
 * Convert a numeric price into its Indonesian terbilang representation.
 * Shout out to @cahsowan (https://gist.github.com/cahsowan/d315d54a59e4f14a6bab)!
 * 
 * @param int $price The numeric price to be converted into terbilang.
 * 
 * @return string The terbilang representation of the price in Bahasa Indonesia.
 */
function convertToTerbilang($price)
{
    $number = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];

    if ($price < 12)
        return " " . $number[$price];
    else if ($price < 20)
        return convertToTerbilang($price - 10) . " belas";
    else if ($price < 100)
        return convertToTerbilang($price / 10) . " puluh" . convertToTerbilang($price % 10);
    else if ($price < 200)
        return "seratus" . convertToTerbilang($price - 100);
    else if ($price < 1000)
        return convertToTerbilang($price / 100) . " ratus" . convertToTerbilang($price % 100);
    else if ($price < 2000)
        return "seribu" . convertToTerbilang($price - 1000);
    else if ($price < 1000000)
        return convertToTerbilang($price / 1000) . " ribu" . convertToTerbilang($price % 1000);
    else if ($price < 1000000000)
        return convertToTerbilang($price / 1000000) . " juta" . convertToTerbilang($price % 1000000);
}
