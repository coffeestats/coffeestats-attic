<?php
/*
 * Utility functions for chart generation.
 */

/**
 * Extract the labels (keys) from an associative array and format them for use
 * as chart label list.
 */
function extractlabels(&$assocarray) {
    $labels = array();
    foreach (array_keys($assocarray) as $key) {
        array_push($labels, sprintf("'%s'", $key));
    }
    print(implode(',', $labels));
}

/**
 * Extract data from the n'th field of all values in an associative array and
 * format them as chart data list.
 */
function extractdata(&$assocarray, $field) {
    $data = array();
    foreach ($assocarray as $key => $value) {
        array_push($data, $assocarray[$key][$field]);
    }
    print(implode(',', $data));
}
?>
