<?php
function validate_positive_int($value) {
    $val = filter_var($value, FILTER_VALIDATE_INT);
    return ($val !== false && $val > 0) ? $val : false;
}

function validate_non_negative_number($value) {
    return (is_numeric($value) && $value >= 0) ? (float)$value : false;
}