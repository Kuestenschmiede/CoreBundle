<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\CoreBundle\Controller;

use con4gis\CoreBundle\Resources\contao\classes\C4GUtils;
use con4gis\CoreBundle\Resources\contao\classes\exception\C4GFileSizeException;
use con4gis\CoreBundle\Resources\contao\classes\exception\C4GGenericException;
use con4gis\CoreBundle\Resources\contao\classes\exception\C4GImageDimensionsException;
use con4gis\CoreBundle\Resources\contao\classes\exception\C4GInvalidFileFormatException;
use con4gis\CoreBundle\Resources\contao\models\C4gSettingsModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UploadController extends Controller
{
    public function imageUploadAction(Request $request) {

        $beginHere = '';
        try {
            if ($request->files instanceof FileBag) {
                $files = $request->files;
                $uploadedFile = $files->get('upload');
                if ($uploadedFile instanceof UploadedFile) {
                    $settings = C4gSettingsModel::findSettings();
                    $allowedTypes = explode(',', $settings->uploadAllowedImageTypes);
                    $maxWidth = $settings->uploadAllowedImageWidth;
                    $maxHeight = $settings->uploadAllowedImageHeight;
                    $maxSize = $settings->uploadMaxFileSize;
                    if (empty($allowedTypes) || !is_array($allowedTypes)) {
                        throw new C4GGenericException();
                    }

                    if (!in_array($uploadedFile->getMimeType(), $allowedTypes, true)) {
                        throw new C4GInvalidFileFormatException();
                    }

                    if ($maxSize < $uploadedFile->getSize()) {
                        throw new C4GFileSizeException($maxSize, $uploadedFile->getSize());
                    }

                    $imageSize = getimagesize($uploadedFile->getPath() . "/" . $uploadedFile->getFilename());
                    $imageWidth = $imageSize[0];
                    $imageHeight = $imageSize[1];
                    if ($maxHeight < $imageHeight || $maxWidth < $imageWidth) {
                        throw new C4GImageDimensionsException($maxHeight, $imageHeight, $maxWidth, $imageWidth);
                    }

                    $uploadDirectoryBinary = $settings->uploadPathImages;
                    if ($uploadDirectoryBinary === null) {
                        throw new C4GGenericException();
                    }
                    $uploadDirectoryString = \FilesModel::findByUuid(\Contao\StringUtil::binToUuid($uploadDirectoryBinary))->path;

                    $subDirectory = date("Y-m-d");
                    $uploadDirectoryString = $uploadDirectoryString . $subDirectory;
                    if (!is_dir(TL_ROOT . "/" . $uploadDirectoryString)) {
                        $success = mkdir(TL_ROOT . "/" . $uploadDirectoryString, 0777, true);
                        if (!$success) {
                            throw new C4GGenericException();
                        }
                    }

                    $img_name   = md5(uniqid('', true)) . "." .$uploadedFile->getExtension();
                    $uploadPath = TL_ROOT . '/' . $uploadDirectoryString;
                    $newFile = $uploadedFile->move($uploadPath, $img_name);
                    $protocol = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
                    $site     = $protocol . $_SERVER['SERVER_NAME'] . '/';
                    $url = $site.$uploadPath."/".$img_name;
                    $message = sprintf($GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_successful'], $uploadedFile->getClientOriginalName(), number_format( $uploadedFile->getSize() / 1024, 3, '.', ''), $imageWidth, $imageHeight);
                    $response = array(
                        'title' => 'Erfolg',
                        'message' => 'Der Upload war erfolgreich.',
                        'ckScript' => "<script>window.parent.CKEDITOR.tools.callFunction(".$request->request->get('CKEditorFuncNum').", '$url', '$message');</script>",
                    );

                } else {
                    throw new C4GGenericException();
                }
            } else {
                throw new C4GGenericException();
            }
        } catch (C4GGenericException $e) {
            $response = array(
                'title' => 'Fehler',
                'message' => 'Ein Fehler ist aufgetreten.',
            );
        } catch (C4GInvalidFileFormatException $e) {
            $response = array(
                'title' => 'Fehler',
                'message' => 'Das Dateiformat is ungültig.',
            );
        } catch (C4GFileSizeException $e) {
            $response = array(
                'title' => 'Fehler',
                'message' => "Die Datei unterschreitet die maximale Größe von ".$e->getMaxFileSize()." Bytes.",
            );
        } catch (C4GImageDimensionsException $e) {
            $response = array(
                'title' => 'Fehler',
                'message' => "Das Bild überschreitet die maximale Größe. ",
            );
            if ($e->getMaxHeight() < $e->getFileHeight()) {
                $response['message'] .= "\nErlaubte Höhe: ".$e->getMaxHeight()." Höhe der Datei: ".$e->getFileHeight();
            }
            if ($e->getMaxWidth() < $e->getFileWidth()) {
                $response['message'] .= "\nErlaubte Breite: ".$e->getMaxWidth()." Breite der Datei: ".$e->getFileWidth();
            }
        } catch (\Throwable $e) {
            $response = array(
                'title' => 'Fehler',
                'message' => 'Ein Fehler ist aufgetreten.',
            );
        }

        return new JsonResponse($response);
    }

    public function documentUploadAction(Request $request) {

    }
}