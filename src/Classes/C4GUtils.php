<?php

/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes;

use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use con4gis\CoreBundle\Resources\contao\models\C4gSettingsModel;
use con4gis\MapsBundle\Resources\contao\models\C4gMapSettingsModel;
use Symfony\Component\HttpClient\HttpClient;
use Contao\System;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class C4GUtils
 * @package con4gis\CoreBundle\Classes
 */
class C4GUtils
{
    /**
     * Secure user generated content
     * @param $str
     */
    public static function secure_ugc($str)
    {
        // kritische Kontrollzeichen rausfiltern
        $search = [chr(0), chr(1), chr(2), chr(3), chr(4), chr(5), chr(6), chr(7), chr(8),
            chr(11), chr(12), chr(14), chr(15), chr(16), chr(17), chr(18), chr(19), ];
        $result = str_replace($search, ' ', $str);

        // Unerwünschte Unicode Sonderzeichen z.B. zur Umkehrung des Textflusses entfernen
        $regex = '/(?:%E(?:2|3)%8(?:0|1)%(?:A|8|9)\w)/i';
        $result = urldecode(preg_replace($regex, ' ', urlencode($result)));

        // Eingangs-Html formatieren und überflüssige Leerzeichen entfernen
        return trim(htmlspecialchars($result));
    }

    public static function cleanHtml($html, $img = false, $allowedTags = [])
    {
        $javascript = '/<script[^>]*?javascript{1}[^>]*?>.*?<\/script>/si';
        $noscript = '';
        $html = preg_replace($javascript, $noscript, $html);

        $unsafe = [
            '/<iframe(.*?)<\/iframe>/is',
            '/<title(.*?)<\/title>/is',
            '/<pre(.*?)<\/pre>/is',
            '/<frame(.*?)<\/frame>/is',
            '/<frameset(.*?)<\/frameset>/is',
            '/<object(.*?)<\/object>/is',
            '/<script(.*?)<\/script>/is',
            '/<embed(.*?)<\/embed>/is',
            '/<applet(.*?)<\/applet>/is',
            '/<meta(.*?)>/is',
            '/<!doctype(.*?)>/is',
            '/<link(.*?)>/is',
            '/<body(.*?)>/is',
            '/<\/body>/is',
            '/<head(.*?)>/is',
            '/<\/head>/is',
            '/<html(.*?)>/is',
            '/<\/html>/is', ];

        $unsafe = array_diff($unsafe, $allowedTags);
        // Remove graphic too if the user wants
        if ($img == true) {
            $unsafe[] = '/<img(.*?)>/is';
        }

        // Remove these tags and all parameters within them
        $html = preg_replace($unsafe, '', $html);

        // Remove html events
        $html = preg_replace("#<([^><]+?)([^a-z_\-]on\w*|xmlns)(\s*=\s*[^><]*)([><]*)#i", '<\\1\\4', $html);

        return $html;
    }

    /**
     * Flatten a multi dimensional array
     * @param array $a
     */
    public static function array_flatten($a)
    {
        $ab = [];
        if (!is_array($a)) {
            return $ab;
        }
        foreach ($a as $value) {
            if (is_array($value)) {
                $ab = array_merge($ab, self::array_flatten($value));
            } else {
                array_push($ab, $value);
            }
        }

        return $ab;
    }

    /**
     * @param array $params
     */
    public static function addParametersToURL($url, $params)
    {
        list($urlpart, $qspart) = array_pad(explode('?', $url, 2), 2, '');
        if (!$urlpart) {
            $urlpart = $url;
        }
        parse_str($qspart, $qsvars);
        foreach ($params as $key => $value) {
            $qsvars[$key] = $value;
        }
        $newqs = http_build_query($qsvars);

        return $urlpart . '?' . $newqs;
    }

    /**
     * adds default options for dialogs to an array
     * @param array $options
     */
    public static function addDefaultDialogOptions($options)
    {
        $options['show'] = 'fold';
        $options['hide'] = 'fold';
        if (!isset($options['width'])) {
            $options['width'] = 'auto';
        }
        if (!isset($options['height'])) {
            $options['height'] = 'auto';
        }

        return $options;
    }

    /**
     * validates a postal-number
     * returns "1" if valid
     * @param string $postal
     * @return number
     */
    public static function postalIsValid($postal)
    {
        $postal = trim($postal);

        return is_numeric($postal);
    }
    /**
     * validates an email-address
     * returns "1" if valid
     * @param string $mail
     * @return number
     */
    public static function emailIsValid($mail)
    {
        $mail = trim($mail);

        return preg_match('/([^@]+@{1}[^@\.]+\.{1}[A-Za-z0-9]+)/', $mail);
    }

    /**
     * function to send mails
     * @param array $mailData
     * @return bool
     */
    public static function sendMail($mailData)
    {
        try {
            // preparemail
            $eMail = new \Contao\Email();
            $eMail->charset = $mailData['charset'] ?: 'UTF-8';

            $eMail->from = $mailData['from'];
            if ($mailData['from']) {
                $eMail->from = $mailData['from'];
            } elseif ($GLOBALS['TL_CONFIG']['useSMTP'] and filter_var($GLOBALS['TL_CONFIG']['smtpUser'])) {
                $eMail->from = $GLOBALS['TL_CONFIG']['smtpUser'];
            } else {
                $eMail->from = $GLOBALS['TL_CONFIG']['adminEmail'];
            }

            $eMail->subject = $mailData['subject'];
            if (isset($mailData['text'])) {
                $eMail->text = $mailData['text'];
            }
            if (isset($mailData['html'])) {
                $eMail->html = $mailData['html'];
            }
            $eMail->sendTo($mailData['to']);
            unset($eMail);
        } catch (Swift_RfcComplianceException $e) {
            C4gLogModel::addLogEntry('core', $e->getMessage());

            return false;
        }

        return true;
    }

    public static function getMailErrors($mailData)
    {
        // check if fields are filled
        //
        // reciever
        if (empty($mailData['to'])) {
            return [
                'usermessage' => $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['NO_EMAIL_ADDRESS'],
            ];
        }
        // subject
        if (empty($mailData['subject'])) {
            return [
                'usermessage' => $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['NO_EMAIL_SUBJECT'],
            ];
        }
        // message-text
        if (empty($mailData['text'])) {
            return [
                'usermessage' => $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['NO_EMAIL_MESSAGE'],
            ];
        }

        return [];
    } // end of function "sendMail"

    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return (substr($haystack, 0, $length) === $needle);
    }

    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    /**
     * compresses the raw data set for searching/indexing
     * and removes stopwords
     * @param array (of strings)  $rawDataSet
     * @return string         [compressed data]
     */
    public static function compressDataSetForSearch($rawDataSet, $stripStopwords = true)
    {
        $dSearch = [
            '#ß#',
            '#Ä|ä#',
            '#Ö|ö#',
            '#Ü|ü#',
            '#Á|á|À|à|Â|â#',
            '#Ó|ó|Ò|ò|Ô|ô#',
            '#Ú|ú|Ù|ù|Û|û#',
            '#É|é|È|è|Ê|ê#',
            '#Í|í|Ì|ì|Î|î#',
            '#([/.,+-]*\s)#',
            '#([^A-Za-z])#',
            '# +#',
        ];
        $dReplace = [
            'ss',
            'ae',
            'oe',
            'ue',
            'a',
            'o',
            'u',
            'e',
            'i',
            ' ',
            ' ',
            ' ',
        ];

        $dataSet = trim(stripslashes(strip_tags($rawDataSet)));
        $dataSet = preg_replace($dSearch, $dReplace, $dataSet);
        $dataSet = trim(strtolower($dataSet));

        unset($dSearch , $dReplace);

        if ($stripStopwords) {
            $dSearch = [
                '#(\s[A-Za-z]{1,2})\s#',
                '# ' . implode(' | ', $GLOBALS['TL_LANG']['C4G_FORUM']['STOPWORDS']) . ' #',
                '# +#',
            ];
            $dReplace = [
                ' ',
                ' ',
                ' ',
            ];

            $dataSet = ' ' . str_replace(' ', '  ', $dataSet) . ' ';
            $dataSet = trim(preg_replace($dSearch, $dReplace, $dataSet));
        }

        return $dataSet;
    }

    /**
     * @param $parsed_url
     * @return string
     */
    public static function unparse_url($parsed_url)
    {
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = $parsed_url['host'] ?? '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = $parsed_url['user'] ?? '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = $parsed_url['path'] ?? '';
        $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    /**
     * @param $string
     * @return bool|string
     */
    public static function removeLastSlashes($string)
    {
        if ($string) {
            if ((substr($string, -1, 1) == '/') || (substr($string, -1, 1) == '\\')) {
                return substr($string, 0, -1);
            }
        }

        return $string;
    }

    /**
     * Checks if a frontend user is logged in. Works for multiple Contao 4.x authentication models
     */
    public static function isFrontendUserLoggedIn()
    {
        $name = "Contao\CoreBundle\Security\Authentication\Token\TokenChecker";
        // check if the symfony authentication model is used
        if (class_exists($name)) {
            return \Contao\System::getContainer()->get('contao.security.token_checker')->hasFrontendUser();
        }
        $hasFrontendUser = System::getContainer()->get('contao.security.token_checker')->hasFrontendUser();
        return $hasFrontendUser;
    }

    /**
     * @return bool
     * @deprecated
     */
    public static function checkFrontendUserLogin()
    {
        return self::isFrontendUserLoggedIn();
    }

    /**
     * Checks if a backend user is logged in. Works for multiple Contao 4.x authentication models
     */
    public static function isBackendUserLoggedIn()
    {
        $name = "Contao\CoreBundle\Security\Authentication\Token\TokenChecker";
        // check if the symfony authentication model is used
        if (class_exists($name)) {
            return \Contao\System::getContainer()->get('contao.security.token_checker')->hasBackendUser();
        }

        return (\Contao\BackendUser::getInstance() !== null);
    }

    /**
     * @return bool
     * @deprecated
     */
    public static function checkBackendUserLogin()
    {
        return self::isBackendUserLoggedIn();
    }

    public static function sortBackendModules($arrModules)
    {
        System::loadLanguageFile('modules');
        $arrKeys = array_keys($arrModules);
        // extract help and settings module
        $arrInfoModule = array_splice($arrModules, array_search('c4g_core', $arrKeys), 1)['c4g_core'];
        array_splice($arrKeys, array_search('c4g_core', $arrKeys), 1);
        $arrSettingsModule = array_splice($arrModules, array_search('c4g_settings', $arrKeys), 1)['c4g_settings'];
        array_splice($arrKeys, array_search('c4g_settings', $arrKeys), 1);
        $langArray = $GLOBALS['TL_LANG']['MOD'];
        usort($arrKeys, function ($a, $b) use ($langArray) {
            // access offset 0 because the lang ref is a two element array
            return strnatcmp($langArray[$a][0], $langArray[$b][0]);
        });
        $arrResult = [];
        $arrResult['c4g_core'] = $arrInfoModule;
        $arrResult['c4g_settings'] = $arrSettingsModule;
        foreach ($arrKeys as $value) {
            // check because of previous splice
            $arrResult[$value] = $arrModules[$value];
        }

        return $arrResult;
    }

    /**
     * returns communication key
     * @param $settings
     * @param $serviceId
     * @return bool
     */
    public static function getKey($settings, $service, $params = '', $getKeyOnly = true)
    {
        if ($settings && $settings->con4gisIoUrl && $settings->con4gisIoKey) {
            $hour = date('YmdH', time());

            $keySearchUrl = rtrim($settings->con4gisIoUrl, '/') . '/';
            $keySearchUrl = $keySearchUrl . 'getKey.php';
            if ($params && ($params[0] !== '&')) {
                $params = '&' . $params;
            }
            $keySearchUrl .= '?key=' . $settings->con4gisIoKey . '&service=' . $service . $params;
            $headers = [];
            if (isset($_SERVER['HTTP_REFERER'])) {
                $headers['Referer'] = $_SERVER['HTTP_REFERER'];
            }
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $headers['User-Agent'] = $_SERVER['HTTP_USER_AGENT'];
            }
            $client = HttpClient::create([
                'headers' => $headers,
            ]);

            try {
                $response = $client->request('GET', $keySearchUrl, ['timeout' => 2]);
                $statusCode = $response->getStatusCode();
                if ($response && $response->getStatusCode() === 200) {
                    $response = $response->getContent();
                    $response = \GuzzleHttp\json_decode($response);
                    if ($response && $response->key && (strlen($response->key) == 64)) {
                        if (System::getContainer()->has('session')) {
                            $objSession = System::getContainer()->get('session');
                            $objSession->set('ciokey_' . $service . '_' . $params, $hour . '_' . $response->key);
                        }
                        if ($getKeyOnly) {
                            return $response->key;
                        }

                        return $response;
                    }
                }
            } catch (\Exception $exception) {
                return false;
            }
        }

        return false;
    }
    public static function getKeys($settings, $arrKeyParams)
    {
        if ($settings && $settings->con4gisIoUrl && $settings->con4gisIoKey) {
            $hour = date('YmdH', time());

            $keySearchUrl = rtrim($settings->con4gisIoUrl, '/') . '/';
            $keySearchUrl = $keySearchUrl . 'getMultipleKeys.php';
            $services = '';
            $ids = '';
            foreach ($arrKeyParams as $keyParam) {
                $services .= $keyParam[0] . ',';
                $ids .= $keyParam[1] . ',';
            }
            $services = rtrim($services, ',');
            $ids = rtrim($ids, ',');
            $keySearchUrl .= '?key=' . $settings->con4gisIoKey . '&services=' . $services . '&ids=' . $ids;
            $headers = [];
            if (isset($_SERVER['HTTP_REFERER'])) {
                $headers['Referer'] = $_SERVER['HTTP_REFERER'];
            }
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $headers['User-Agent'] = $_SERVER['HTTP_USER_AGENT'];
            }
            $client = HttpClient::create([
                'headers' => $headers,
            ]);

            try {
                $response = $client->request('GET', $keySearchUrl, ['timeout' => 2]);
                $statusCode = $response->getStatusCode();
                if ($response && $response->getStatusCode() === 200) {
                    $response = $response->getContent();
                    $response = \GuzzleHttp\json_decode($response, true);
                    foreach ($response as $key => $valueKey) {
                        if (System::getContainer()->has('session')) {
                            $objSession = System::getContainer()->get('session');
                            $objSession->set('ciokey_' . $arrKeyParams[$key][0] . '_' . $arrKeyParams[$key][1] ? 'id=' . $arrKeyParams[$key][1] : '', $hour . '_' . $valueKey['key']);
                        }
                    }

                    return $response;
                }
            } catch (\Exception $exception) {
                return false;
            }
        }
    }
    /**
     * returns the lon/lat for an address string
     * @param string $strAddress
     * @return array
     */
    public static function geocodeAddress($strAddress) {
        $settings = C4gMapSettingsModel::findOnly();
        if ($settings->con4gisIoUrl && $settings->con4gisIoKey){
            $searchUrl = rtrim($settings->con4gisIoUrl, '/') . '/';
            $searchUrl .= 'search.php?key=' . $settings->con4gisIoKey;
            $searchUrl .= '&q=' . urlencode($strAddress) . '&format=json';

            $headers = [];
            if ($_SERVER['HTTP_REFERER']) {
                $headers['Referer'] = $_SERVER['HTTP_REFERER'];
            }
            if ($_SERVER['HTTP_USER_AGENT']) {
                $headers['User-Agent'] = $_SERVER['HTTP_USER_AGENT'];
            }
            $client = HttpClient::create([
                'headers' => $headers,
            ]);
            try {
                $response = $client->request('GET', $searchUrl, ['timeout' => 2]);
                $statusCode = $response->getStatusCode();
                if ($response && $response->getStatusCode() === 200) {
                    $response = $response->getContent();
                    $response = \GuzzleHttp\json_decode($response, true);
                    $elemResponse = $response[0];
                    $coordinates = [$elemResponse['lon'], $elemResponse['lat']];
                    return $coordinates;
                }
            } catch (\Exception $exception) {
                return false;
            }

        }

    }
    /**
     * returns the address as a string or array for given coordinates
     * @param array $coordinates
     * @param bool $getArray
     * @return mixed
     */
    public static function reverseGeocode($coordinates, $getArray = false) {
        $settings = C4gMapSettingsModel::findOnly();
        if ($settings->con4gisIoUrl && $settings->con4gisIoKey){
            $searchUrl = rtrim($settings->con4gisIoUrl, '/') . '/';
            $searchUrl .= 'reverse.php?key=' . $settings->con4gisIoKey;
            $searchUrl .= '&lon=' . $coordinates[0] . '&lat=' . $coordinates[1] . '&format=json';

            $headers = [];
            if ($_SERVER['HTTP_REFERER']) {
                $headers['Referer'] = $_SERVER['HTTP_REFERER'];
            }
            if ($_SERVER['HTTP_USER_AGENT']) {
                $headers['User-Agent'] = $_SERVER['HTTP_USER_AGENT'];
            }
            $client = HttpClient::create([
                'headers' => $headers,
            ]);
            try {
                $response = $client->request('GET', $searchUrl, ['timeout' => 2]);
                $statusCode = $response->getStatusCode();
                if ($response && $response->getStatusCode() === 200) {
                    $response = $response->getContent();
                    $response = \GuzzleHttp\json_decode($response, true);
                    if ($getArray) {
                        return $response['address'];
                    }
                    else {
                        return $response['display_name'];
                    }
                }
            } catch (\Exception $exception) {
                return false;
            }

        }

    }
    /**
     * @return string
     */
    public static function getGUID()
    {
        mt_srand((double) microtime() * 10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
            . substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12)
            . chr(125);// "}"
        return $uuid;
    }

    /**
     * @param $uuid
     * @return bool
     */
    public static function isValidGUID($uuid)
    {
        $chars = ['{','}'];
        $uuid = $uuid ? str_replace($chars, ' ', $uuid) : false;
        $elements = $uuid ? explode('-', $uuid) : false;
        if ($uuid && (strlen($elements[0]) === 8) && (strlen($elements[1]) === 4) && (strlen($elements[2]) === 4) && (strlen($elements[3]) === 4) && (strlen($elements[4]) === 12)) {
            return true;
        }

        return false;
    }

    /**
     * @param array $array
     * @return string
     */
    public static function buildInString(array $array)
    {
        return 'IN(' . implode(',', array_fill(0, count($array), '?')) . ')';
    }

    public static function buildInStringValues(array $array)
    {
        $result = '';
        foreach ($array as $key => $value) {
           if ($result) {
               $result .= ','.$value;
           } else {
               $result = $value;
           }
        }
       return $result;
    }

    /**
     * @param $link
     * @return string
     */
    public static function addProtocolToLink($link)
    {
        $testLink = trim(strtoupper($link));
        if ($testLink && (substr($testLink, 0, 4) != 'HTTP')) {
            $link = 'https://' . $link;
        }

        return $link;
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @param bool $caseSensitive
     * @return bool
     */
    public static function stringContains(string $haystack, string $needle, bool $caseSensitive = false): bool
    {
        if ($caseSensitive) {
            return strpos($haystack, $needle) !== false;
        }

        return stripos($haystack, $needle) !== false;
    }

    /**
     * @param string $haystack
     * @param array $needles
     * @param bool $caseSensitive
     * @return bool
     */
    public static function stringContainsAny(string $haystack, array $needles, bool $caseSensitive = false): bool
    {
        foreach ($needles as $needle) {
            if (static::stringContains($haystack, $needle, $caseSensitive)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $str
     * @return bool
     */
    public static function isBinary($str)
    {
        if (!C4gUtils::containsUmlaut($str)) {
            return false;
        }

        return preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0;
    }

    /**
     * @param $uuid
     * @return bool
     */
    public static function isValidUuid($uuid)
    {
        if (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $uuid) !== 1)) {
            return false;
        }

        return true;
    }

    /**
     * @param $str
     * @return bool
     */
    public static function containsUmlaut($str)
    {
        $umlauts = explode(',', 'Ŕ,Á,Â,Ă,Ä,Ĺ,Ç,Č,É,Ę,Ë,Ě,Í,Î,Ď,Ň,Ó,Ô,Ő,Ö,Ř,Ů,Ú,Ű,Ü,Ý,ŕ,á,â,ă,ä,ĺ,ç,č,é,ę,ë,ě,í,î,ď,đ,ň,ó,ô,ő,ö,ř,ů,ú,ű,ü,ý,˙,Ń,ń,ß');
        foreach ($umlauts as $umlaut) {
            if (false !== (strpos($str, $umlaut))) {
                return false;
            }
        }

        return true;
    }


    /**
     * @param $text
     * @param $length
     * @return array|false|string|string[]|null
     */
    public static function truncate($text, $length)
    {
        $text = str_replace('><', '> <', $text);
        $text = strip_tags($text);
        $text = htmlspecialchars($text, ENT_QUOTES, 'utf-8');
        $length = abs((int) $length);
        $dot = strpos($text, '.');
        $firstFullstop = 0;
        if ($dot && $dot <= ($length - 1)) {
            for ($i = 0, $j = strlen($text); $i < $j; $i++) {
                if ((strstr('.', $text[$i])) && ($i <= ($length - 1))) {
                    if ($i > ($length*0.7)) {
                        $firstFullstop = $i;
                    }
                }
            }

            $text = html_entity_decode(htmlspecialchars($text));
            $text = str_replace('&amp;', '&', $text);
        }

        if ($firstFullstop) {
            return substr($text, 0, $firstFullstop + 1);
        }

        if (strlen($text) > $length) {
            $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
        }

        $text = html_entity_decode(htmlspecialchars($text));
        $text = str_replace('&amp;', '&', $text);

        return($text);
    }

    /**
     * @param string $insertTag
     * @return string
     */
    public static function replaceInsertTags($insertTag) {
        $result = '';
        $parser = System::getContainer()->get('contao.insert_tag.parser');
        if ($parser && $insertTag) {
            $result = html_entity_decode($parser->replace($insertTag));
        }
        return $result;
    }
}
