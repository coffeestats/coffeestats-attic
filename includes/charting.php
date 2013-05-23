<?php
if (strcmp($_SERVER['SCRIPT_FILENAME'], __FILE__) == 0) {
    header('Status: 301 Moved Permanently');
    header('Location: ../index');
    exit();
}

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

/**
 * Return the maximum value from the values of the given associative array.
 */
function scalesteps(&$dataarray) {
    $max = 1;
    foreach ($dataarray as $key => $valuearray) {
        foreach ($valuearray as $item) {
            if ($item > $max) {
                $max = $item;
            }
        }
    }
    print($max);
}

?>
<script type="text/javascript" src="lib/Chart.min.js"></script>
<script type="text/javascript">
function drawBarChart(canvasid, data, scaleSteps) {
    new Chart(document.getElementById(canvasid).getContext("2d")).Bar(data, {
        scaleOverride: true,
        scaleSteps: Math.ceil(scaleSteps / Math.ceil(scaleSteps / 10)),
        scaleStepWidth: Math.ceil(scaleSteps / 10),
        scaleStartValue: 0,
    });
}

function drawLineChart(canvasid, data, scaleSteps) {
    new Chart(document.getElementById(canvasid).getContext("2d")).Line(data, {
        scaleOverride: true,
        scaleSteps: Math.ceil(scaleSteps / Math.ceil(scaleSteps / 10)),
        scaleStepWidth: Math.ceil(scaleSteps / 10),
        scaleStartValue: 0,
    });
}
</script>
