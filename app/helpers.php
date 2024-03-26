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
function getIndexData($title = null, $active = null, $activeChild = null, $data = null)
{
    return array(
        "title" => $title . ' | ' . config('app.name'),
        "active" => $active,
        "active_child" => $activeChild,
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
