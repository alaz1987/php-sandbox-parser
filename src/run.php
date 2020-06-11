<?php

/* Params */
$url         = "http://cbonds.info/sandbox/some_source.php";
$data_file   = __DIR__."/data/server_data.html";
$xml_file    = __DIR__."/data/emissions.xml";
$cookie_file = "/tmp/cookie.file";

/* Task #1 - Download data via curl and save to file */

if (!function_exists("curl_init")) {
    die("Curl extension isn't installed\n");
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); 
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);

$data  = curl_exec($ch);
$error = false;

if($data === true) { 
    $error = "Content server data is empty"; 
}
if (strlen($data) == 0) {
    $error = curl_error($ch);
}

curl_close($ch);
@unlink($cookie_file);

if ($error) {
    die("Error download data from $url -> $error\n");
}

$result = file_put_contents($data_file, $data);

if ($result === false) {
    die("Unable save data to file $data_file. Check permissions for writing\n");
}

print("Server data has saved to file $data_file\n");

/* Task #2 - Parse data and save to XML */
if (!class_exists("DOMDocument")) {
    die("DOM extension isn't installed\n");
}

if (preg_match('/<table id="stable".*<\/table>/s', $data, $matches)) {
    $table = $matches[0];

    if (preg_match_all('/<th.*?<\/th>/s', $table, $matches)) {
        $th = array_map(function($tag) {
            $val = trim(strip_tags($tag));
            return mb_convert_encoding($val, "utf-8", "windows-1251");
        }, $matches[0]);
    }

    if (preg_match('/<tbody.*?<\/tbody>/s', $table, $matches)) {
        $body = $matches[0];
    }

    if ($body && preg_match_all('/<tr.*?<\/tr>/s', $body, $matches)) {
        $tr = $matches[0];
    }

    $doc = new DOMDocument('1.0', 'utf-8');
    $doc->formatOutput = true;

    $items = $doc->createElement('items');
    $items = $doc->appendChild($items);

    if ($tr) {
        foreach ($tr as $r) {
            if (preg_match_all('/<td.*?<\/td>/s', $r, $matches)) {
                $td = array_map(function($tag) { return trim(strip_tags($tag)); }, $matches[0]);

                $item = $doc->createElement('item');
                $item = $items->appendChild($item);

                foreach ($td as $k => $value) {
                    $property = $doc->createElement('property');
                    
                    $title        = $doc->createAttribute('title');
                    $title->value = $th[$k];
                    
                    $cdata        = $doc->createCDATASection($value);

                    $property->appendChild($title);
                    $property->appendChild($cdata);

                    $property = $item->appendChild($property);
                }
            }
        }
    }

    $result = $doc->save($xml_file);
    
    if ($result){
        print("Table's data has saved to file $xml_file\n");
    } else {
        die("Unable save xml to file $data_file. Check permissions for writing\n");
    }
} else{
    die("Data table could't be parse\n");
}