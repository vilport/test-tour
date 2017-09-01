<?php
// display_errors
error_reporting(1);
ini_set('display_errors', E_ALL);
// extend to 10 minutes
set_time_limit(600);

function xmlToCSV($text, $delimiter = '|', $currency = 'EUR', $returnAsFile = true)
{
    $xml = simplexml_load_string($text, 'SimpleXMLElement', LIBXML_NOCDATA);
    $tours = $xml->xpath('TOUR');
    $csv = getCsv($tours, $delimiter, $currency);
    // return csv output as file
    if ($returnAsFile) {
        header('Content-Type: application/csv');
    	header('Content-Disposition: attachement; filename="tours.csv"');
        echo $csv;
        exit();
    } else {
        return $csv;
    }
}

function xmlFileToCSV($filename = null)
{
    $file = $filename ?? __DIR__.'/tours.xml';
    if (!isValid($file)) {
        throw new Exception('Could not found xml file for parsing');
    }
    $text = file_get_contents($file);
    return xmlToCSV($text);
}

function isValid($file)
{
    // check if file exists
    if (!file_exists($file) || !is_file($file)) {
        return false;
    }
    // check for xml extension
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    if ($extension != 'xml') {
        return false;
    }
    return true;
}

function getCsv($tours, $delimiter, $currency)
{
    $header = getCsvHeader($delimiter);
    $body = getCsvBody($tours, $delimiter, $currency);
    return $header.$body;
}

function getCsvHeader($delimiter)
{
    // Title|Code|Duration|Inclusions|MinPrice
    $fields = [
        'Title','Code','Duration','Inclusions','MinPrice'
    ];
    return implode($delimiter, $fields) . PHP_EOL;
}

function getCsvBody($tours, $delimiter, $currency)
{
    $body = '';
    foreach ($tours as $tour) {
        $body.= getCsvRow($tour, $delimiter, $currency) . PHP_EOL;
    }
    return $body;
}

/*
Output comments:
"Title" is a string, with html entities (like '&amp;') converted back to symbols
"Code" is a string
"Duration" is an integer
"Inclusions" is a string (just simple text: without html tags, double or triple spaces; with html entities converted back to symbols)
"MinPrice" is a float with 2 digits after the decimal point; it's the minimal EUR value among all tour departures, taking into account the discount, if presented (for example, if the price is 1724 and the discount is "15%", then the departure price evaluates to 1465.40).
*/
function getCsvRow($tour, $delimiter, $currency)
{
    $row = [];
    $row[] = htmlspecialchars_decode($tour->Title->__toString());
    $row[] = $tour->Code->__toString();
    $row[] = (int)$tour->Duration->__toString();
    $row[] = cleanString($tour->Inclusions->__toString());
    $row[] = getMinPrice($tour, $currency);
    return implode($delimiter, $row);
}

function cleanString($string)
{
	$str = utf8_decode($string);
    $str = strip_tags($str);
	$str = str_replace('&nbsp;', ' ', $str);
	$str = preg_replace('/\s+/', ' ', trim($str));
	return $str;
}

function getMinPrice($tour, $currency)
{
    $minPrice = null;
    foreach($tour->DEP as $dep) {
        $price = $dep->attributes(){$currency}->__toString();
        // count discount
        if (isset($dep->attributes()['DISCOUNT'])) {
			$discount = str_replace('%','', $dep->attributes()['DISCOUNT']->__toString());
			$price = $price - ($price * $discount / 100);
        }
        // find min price
        if (is_null($minPrice)) {
            $minPrice = $price;
        } else if ($price < $minPrice) {
            $minPrice = $price;
        }
    }
    return sprintf("%.2f", $minPrice);
}
?>
