<?php 
class SimpleXLSX {
    public static function parse($file_path) {
        $zip = new ZipArchive();

        if ($zip->open($file_path) === true) {
            $xmlData = $zip->getFromName('xl/sharedStrings.xml');
            $sheetData = $zip->getFromName('xl/worksheets/sheet1.xml');
            $zip->close();

            if (!$xmlData || !$sheetData) {
                error_log('Missing sharedStrings.xml or sheet1.xml in the Excel file.');
                return false;
            }

            $sharedStrings = [];
            $dom = new DOMDocument();
            $dom->loadXML($xmlData);
            foreach ($dom->getElementsByTagName('t') as $item) {
                $sharedStrings[] = $item->nodeValue;
            }

            $rows = [];
            $dom->loadXML($sheetData);
            foreach ($dom->getElementsByTagName('row') as $row) {
                $cells = [];
                foreach ($row->getElementsByTagName('c') as $cell) {
                    $value = $cell->getElementsByTagName('v')->item(0)->nodeValue ?? '';
                    if ($cell->getAttribute('t') === 's') {
                        $value = $sharedStrings[(int)$value];
                    }
                    $cells[] = $value;
                }
                $rows[] = $cells;
            }

            // error_log("Parsed Rows: " . print_r($rows, true));
            return $rows;
        }

        // error_log('Unable to open Excel file.');
        return false;
    }
}
