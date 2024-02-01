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
function getIndexData($title, $active, $activeChild, $data = null)
{
    return array(
        "title" => $title . ' | ' . config('app.name'),
        "active" => $active,
        "active_child" => $activeChild,
        "data" => $data
    );
}
