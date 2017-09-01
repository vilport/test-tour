<?php
function isAnagram($string1, $string2)
{
    if (!isValid($string1, $string2)) {
        return false;
    }

    $stringOne = formatString($string1);
    $stringTwo = formatString($string2);

    return compareLetters($stringOne, $stringTwo);
}

// check for invalid inputs (letters and spaces allowed only)
function isValid($string1, $string2)
{
    $pattern = '/^[a-zA-Z ]+$/';
    if (!preg_match($pattern, $string1)) {
        return false;
    }
    if (!preg_match($pattern, $string2)) {
        return false;
    }
    return true;
}

// remove spaces and ignore capital letters - lowercase
function formatString($string)
{
    return preg_replace('/\s+/', '', strtolower($string));
}

// compare letter of two strings
function compareLetters($string1, $string2)
{
    // check for the same length
    if (strlen($string1) == 0 || strlen($string1) !== strlen($string2)) {
        return false;
    }

    // convert strings into arrays
    $arr_string1 = str_split($string1);
    $arr_string2 = str_split($string2);

    // loop over letters and unset found key in the second array
    foreach ($arr_string1 as $letter) {
        $key = array_search($letter, $arr_string2);
        if ($key >= 0) {
            unset($arr_string2[$key]);
        } else {
            return false;
        }
    }

    // matches when the second array is empty
    if (count($arr_string2) == 0) {
        return true;
    } else {
        return false;
    }
}
?>
