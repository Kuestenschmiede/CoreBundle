<?php

/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes;

use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use Symfony\Component\HttpClient\HttpClient;
use Contao\System;

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
//            return (\Contao\FrontendUser::getInstance() !== null);
        return FE_USER_LOGGED_IN;
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
                $response = $client->request('GET', $keySearchUrl, ['timeout' => 2]);
                $statusCode = $response->getStatusCode();
                if ($response && $response->getStatusCode() === 200) {
                    $response = $response->getContent();
                    $response = \GuzzleHttp\json_decode($response);
                    if ($response && $response->key && (strlen($response->key) == 64)) {
                        \Contao\Session::getInstance()->set('ciokey_' . $service . '_' . $params, $hour . '_' . $response->key);
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
                $response = $client->request('GET', $keySearchUrl, ['timeout' => 2]);
                $statusCode = $response->getStatusCode();
                if ($response && $response->getStatusCode() === 200) {
                    $response = $response->getContent();
                    $response = \GuzzleHttp\json_decode($response, true);
                    foreach ($response as $key => $valueKey) {
                        \Contao\Session::getInstance()->set('ciokey_' . $arrKeyParams[$key][0] . '_' . $arrKeyParams[$key][1] ? 'id=' . $arrKeyParams[$key][1] : '', $hour . '_' . $valueKey['key']);
                    }

                    return $response;
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
}
