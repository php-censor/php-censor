<?php

namespace PHPCensor\Helper;

class XmlUtf8CleanFilter extends \php_user_filter
{
    const PATTERN = '/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u';

    /**
     * @param resource $in
     * @param resource $out
     * @param int      $consumed
     * @param bool     $closing
     *
     * @return int
     */
    function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = preg_replace(self::PATTERN, '', $bucket->data);
            $consumed     += $bucket->datalen;

            stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }
}

class Xml
{
    /**
     * @param $filePath
     *
     * @return null|\SimpleXMLElement
     */
    public static function loadFromFile($filePath)
    {
        stream_filter_register('xml_utf8_clean', 'PHPCensor\Helper\XmlUtf8CleanFilter');

        try {
            $xml = simplexml_load_file('php://filter/read=xml_utf8_clean/resource=' . $filePath);
        } catch (\Exception $ex) {
            $xml = null;
        } catch (\Throwable $ex) { // since php7
            $xml = null;
        }

        if (!$xml) {
            // from https://stackoverflow.com/questions/7766455/how-to-handle-invalid-unicode-with-simplexml/8092672#8092672
            $oldUse = libxml_use_internal_errors(true);

            libxml_clear_errors();

            $dom = new \DOMDocument("1.0", "UTF-8");

            $dom->strictErrorChecking = false;
            $dom->validateOnParse     = false;
            $dom->recover             = true;

            $dom->loadXML(strtr(
                file_get_contents($filePath),
                ['&quot;' => "'"] // &quot; in attribute names may mislead the parser
            ));

            /** @var \LibXMLError $xmlError */
            $xmlError = libxml_get_last_error();
            if ($xmlError) {
                $warning = sprintf('L%s C%s: %s', $xmlError->line, $xmlError->column, $xmlError->message);
                print 'WARNING: ignored errors while reading phpunit result, '.$warning."\n";
            }

            if (!$dom->hasChildNodes()) {
                new \SimpleXMLElement('<empty/>');
            }

            $xml = simplexml_import_dom($dom);

            libxml_clear_errors();
            libxml_use_internal_errors($oldUse);
        }

        return $xml;
    }
}
