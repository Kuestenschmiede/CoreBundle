<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\CoreBundle\Resources\contao\classes;

/**
 * Class C4GUtils
 * @package con4gis\CoreBundle\Resources\contao\classes
 */
class C4GUtils
{
    /**
     * Secure user generated content
     * @param $str
     */
    public static function secure_ugc ($str)
    {
        // kritische Kontrollzeichen rausfiltern
        $search = array( chr(0), chr(1), chr(2), chr(3), chr(4), chr(5), chr(6), chr(7), chr(8),
            chr(11), chr(12), chr(14), chr(15), chr(16), chr(17), chr(18), chr(19) );
        $result = str_replace( $search, ' ', $str );

        // Unerwünschte Unicode Sonderzeichen z.B. zur Umkehrung des Textflusses entfernen
        $regex  = '/(?:%E(?:2|3)%8(?:0|1)%(?:A|8|9)\w)/i';
        $result = urldecode(preg_replace($regex,' ',urlencode($result)));

        // Eingangs-Html formatieren und überflüssige Leerzeichen entfernen
        return trim(htmlspecialchars($result));
    }

    public static function cleanHtml($html, $img=false) {
        $javascript = '/<script[^>]*?javascript{1}[^>]*?>.*?<\/script>/si';
        $noscript = '';
        $html = preg_replace($javascript, $noscript, $html);

        $unsafe=array(
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
            '/<\/html>/is');

        // Remove graphic too if the user wants
        if ($img==true)
        {
            $unsafe[]='/<img(.*?)>/is';
        }

        // Remove these tags and all parameters within them
        $html=preg_replace($unsafe, "", $html);

        // Remove html events
        $html = preg_replace("#<([^><]+?)([^a-z_\-]on\w*|xmlns)(\s*=\s*[^><]*)([><]*)#i", "<\\1\\4", $html);

        return $html;
    }

    /**
     * Flatten a multi dimensional array
     * @param array $a
     */
    public static function array_flatten ($a)
    {
        $ab = array();
        if(!is_array($a))
            return $ab;
        foreach($a as $value){
            if(is_array($value)){
                $ab = array_merge($ab,self::array_flatten($value));
            }else{
                array_push($ab,$value);
            }
        }
        return $ab;
    }

    /**
     * @param array $params
     */
    public static function addParametersToURL ( $url, $params )
    {
        list( $urlpart, $qspart ) = array_pad( explode( '?', $url, 2 ), 2, '' );
        if (!$urlpart) {
            $urlpart = $url;
        }
        parse_str( $qspart, $qsvars );
        foreach ($params AS $key=>$value)
        {
            $qsvars[$key] = $value;
        }
        $newqs = http_build_query( $qsvars );
        return $urlpart . '?' . $newqs;
    }

    /**
     * adds default options for dialogs to an array
     * @param array $options
     */
    public static function addDefaultDialogOptions ( $options )
    {
        $options['show'] = 'fold';
        $options['hide'] = 'fold';
        if (!isset( $options['width'] )) {
            $options['width'] = 'auto';
        }
        if (!isset( $options['height'] )) {
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
    public static function postalIsValid ($postal)
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
    public static function emailIsValid ( $mail )
    {
        $mail = trim( $mail );
        return preg_match( '/([^@]+@{1}[^@\.]+\.{1}[A-Za-z0-9]+)/', $mail );
    }

    /**
     * function to send mails
     * @param array $mailData
     * @return multitype:string
     */
    public static function sendMail ($mailData)
    {
        try {
            // preparemail
            $eMail = new \Email();
            $eMail->charset = $mailData['charset'] ?: 'UTF-8';

            $eMail->from = $mailData['from'];
            if ($mailData['from']) {
                $eMail->from = $mailData['from'];
            } elseif ($GLOBALS['TL_CONFIG']['useSMTP'] and filter_var( $GLOBALS['TL_CONFIG']['smtpUser'] )) {
                $eMail->from = $GLOBALS['TL_CONFIG']['smtpUser'];
            } else {
                $eMail->from = $GLOBALS['TL_CONFIG']['adminEmail'];
            }

            $eMail->subject = $mailData['subject'];
            if(isset($mailData['text'])){
                $eMail->text = $mailData['text'];
            }
            if(isset($mailData['html'])){
                $eMail->html = $mailData['html'];
            }
            $eMail->sendTo($mailData['to']);
            unset($eMail);
        } catch ( Swift_RfcComplianceException $e ) {
            return false;
        }
        return true;
    }

    public static function getMailErrors($mailData)
    {
        // check if fields are filled
        //
        // reciever
        if (empty( $mailData['to'] )) {
            return array
            (
                'usermessage' => $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['NO_EMAIL_ADDRESS']
            );
        }
        // subject
        if (empty( $mailData['subject'] )) {
            return array
            (
                'usermessage' => $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['NO_EMAIL_SUBJECT']
            );
        }
        // message-text
        if (empty( $mailData['text'] )) {
            return array
            (
                'usermessage' => $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['NO_EMAIL_MESSAGE']
            );
        }

        return array();
    } // end of function "sendMail"

    public static function startsWith ( $haystack, $needle )
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static function endsWith ( $haystack, $needle )
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr( $haystack, -$length ) === $needle);
    }

    /**
     * compresses the raw data set for searching/indexing
     * and removes stopwords
     * @param array (of strings)  $rawDataSet
     * @return string         [compressed data]
     */
    public static function compressDataSetForSearch ( $rawDataSet, $stripStopwords=true )
    {
        $dSearch = array(
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
            '# +#'
        );
        $dReplace = array(
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
            ' '
        );

        $dataSet = trim( stripslashes( strip_tags( $rawDataSet ) ) );
        $dataSet = preg_replace( $dSearch, $dReplace, $dataSet );
        $dataSet = trim( strtolower( $dataSet ) );

        unset( $dSearch );
        unset( $dReplace );

        if ($stripStopwords) {
            $dSearch = array(
                '#(\s[A-Za-z]{1,2})\s#',
                '# ' . implode( ' | ', $GLOBALS['TL_LANG']['C4G_FORUM']['STOPWORDS'] ) . ' #',
                '# +#'
            );
            $dReplace = array(
                ' ',
                ' ',
                ' '
            );

            $dataSet = ' ' . str_replace( ' ', '  ', $dataSet ) . ' ';
            $dataSet = trim( preg_replace( $dSearch, $dReplace, $dataSet ) );
        }
        return $dataSet;
    }

    /**
     * @param $parsed_url
     * @return string
     */
    public static function unparse_url($parsed_url) {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }


    /**
     * @param $string
     * @return bool|string
     */
    public static function removeLastSlashes($string) {
        if ($string) {
            if ((substr($string, -1, 1) == '/') || (substr($string, -1, 1) == '\\')) {
                return substr($string, 0, -1);
            }
        }

        return $string;
    }

    /**
     * Checks if a frontend user is logged in. Works for multiple contao authentication models
     * (speak for contao 4 in general)
     */
    public static function checkFrontendUserLogin()
    {
        $name = "Contao\CoreBundle\Security\Authentication\Token\TokenChecker";
        // check if the symfony authentication model is used
        if (class_exists($name)) {
            return \System::getContainer()->get('contao.security.token_checker')->hasFrontendUser();
        } else {
            return (\Contao\FrontendUser::getInstance() !== null);
        }
    }

    /**
     * Checks if a backend user is logged in. Works for multiple contao authentication models
     * (speak for contao 4 in general)
     */
    public static function checkBackendUserLogin()
    {
        $name = "Contao\CoreBundle\Security\Authentication\Token\TokenChecker";
        // check if the symfony authentication model is used
        if (class_exists($name)) {
            return \System::getContainer()->get('contao.security.token_checker')->hasBackendUser();
        } else {
            return (\Contao\BackendUser::getInstance() !== null);
        }
    }
}
