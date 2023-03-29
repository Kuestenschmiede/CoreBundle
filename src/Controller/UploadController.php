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

namespace con4gis\CoreBundle\Controller;

use con4gis\CoreBundle\Classes\Exception\C4GFileSizeException;
use con4gis\CoreBundle\Classes\Exception\C4GGenericException;
use con4gis\CoreBundle\Classes\Exception\C4GImageDimensionsException;
use con4gis\CoreBundle\Classes\Exception\C4GInvalidFileFormatException;
use con4gis\CoreBundle\Classes\Utility\C4GByteConverter;
use con4gis\CoreBundle\Resources\contao\models\C4gSettingsModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Contao\System;

class UploadController
{
    public function __construct(ContaoFramework $framework)
    {
        $framework->initialize();
    }

    public function imageUploadAction(Request $request) {

        if ($request->query->get('CKEditor')) {
            $type = 'ckeditor';
        } else {
            $type = 'json';
        }

        System::loadLanguageFile('con4giscoreupload');

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
                    $uploadDirectoryString = $uploadDirectoryString . "/" .$subDirectory;
                    if (!is_dir(TL_ROOT . "/" . $uploadDirectoryString)) {
                        $success = mkdir(TL_ROOT . "/" . $uploadDirectoryString, 0777, true);
                        if (!$success) {
                            throw new C4GGenericException();
                        }
                    }
                    $fileExtension = explode('/', $uploadedFile->getMimeType())[1];
                    $img_name   = md5(uniqid('', true)) . "." .$fileExtension;
                    $uploadPath = TL_ROOT . '/' . $uploadDirectoryString;
                    $newFile = $uploadedFile->move($uploadPath, $img_name);
                    $protocol = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
                    $site     = $protocol . $_SERVER['SERVER_NAME'] . '/';
                    $url = System::getContainer()->get('contao.insert_tag.parser')->replace('{{env::url}}') . "/" . $uploadDirectoryString . "/" . $img_name;
                    $message = sprintf($GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_successful'], $uploadedFile->getClientOriginalName(), number_format( $newFile->getSize() / 1024, 3, '.', ''), $imageWidth, $imageHeight);

                    if ($type === 'json') {
                        $response = array(
                            'title' => $GLOBALS['TL_LANG']['con4gis']['core']['frontend']['successTitle'],
                            'message' => $GLOBALS['TL_LANG']['con4gis']['core']['frontend']['successMessage'],
                            'url' => $url,
                        );
                    } else {
                        $response = "<script>window.parent.CKEDITOR.tools.callFunction(".$request->query->get('CKEditorFuncNum').", '$url', '$message');</script>";
                    }



                } else {
                    throw new C4GGenericException();
                }
            } else {
                throw new C4GGenericException();
            }
        } catch (C4GGenericException $e) {
            $response = array(
                'title' => $GLOBALS['TL_LANG']['con4gis']['core']['frontend']['genericUploadErrorTitle'],
                'message' => $GLOBALS['TL_LANG']['con4gis']['core']['frontend']['genericUploadErrorMessage'],
            );
            if ($type === 'ckeditor') {
                $response = "<script>window.parent.CKEDITOR.tools.callFunction(".$request->query->get('CKEditorFuncNum').", '', '".$response['message']."');</script>";
            }

        } catch (C4GInvalidFileFormatException $e) {
            $response = array(
                'title' => $GLOBALS['TL_LANG']['con4gis']['core']['frontend']['genericUploadErrorTitle'],
                'message' => $GLOBALS['TL_LANG']['con4gis']['core']['frontend']['invalidFormatErrorMessage'],
            );
            if ($type === 'ckeditor') {
                $response = "<script>window.parent.CKEDITOR.tools.callFunction(".$request->query->get('CKEditorFuncNum').", '', '".$response['message']."');</script>";
            }
        } catch (C4GFileSizeException $e) {
            $converter = new C4GByteConverter();
            $converter->setBytes($e->getMaxFileSize());
            $response = array(
                'title' => $GLOBALS['TL_LANG']['con4gis']['core']['frontend']['genericUploadErrorTitle'],
                'message' => $GLOBALS['TL_LANG']['con4gis']['core']['frontend']['fileSizeErrorMessage'].round($converter->getMegaBytes(),2).$GLOBALS['TL_LANG']['con4gis']['core']['frontend']['fileSizeErrorMessageMegaBytes'],
            );
            if ($type === 'ckeditor') {
                $response = "<script>window.parent.CKEDITOR.tools.callFunction(".$request->query->get('CKEditorFuncNum').", '', '".$response['message']."');</script>";
            }
        } catch (C4GImageDimensionsException $e) {
            $response = array(
                'title' => $GLOBALS['TL_LANG']['con4gis']['core']['frontend']['genericUploadErrorTitle'],
                'message' => $GLOBALS['TL_LANG']['con4gis']['core']['frontend']['imageDimensionsErrorMessage'],
            );
            if ($e->getMaxHeight() < $e->getFileHeight()) {
                $response['message'] .= $GLOBALS['TL_LANG']['con4gis']['core']['frontend']['imageDimensionsErrorMessageWidth'].$e->getMaxHeight().$GLOBALS['TL_LANG']['con4gis']['core']['frontend']['imageDimensionsErrorMessageMaxWidth'].$e->getFileHeight();
            }
            if ($e->getMaxWidth() < $e->getFileWidth()) {
                $response['message'] .= $GLOBALS['TL_LANG']['con4gis']['core']['frontend']['imageDimensionsErrorMessageHeight'].$e->getMaxWidth().$GLOBALS['TL_LANG']['con4gis']['core']['frontend']['imageDimensionsErrorMessageMaxHeight'].$e->getFileWidth();
            }

            if ($type === 'ckeditor') {
                $response = "<script>window.parent.CKEDITOR.tools.callFunction(".$request->query->get('CKEditorFuncNum').", '', '".$response['message']."');</script>";
            }
        } catch (\Throwable $e) {
            $response = array(
                'title' => $GLOBALS['TL_LANG']['con4gis']['core']['frontend']['genericUploadErrorTitle'],
                'message' => $GLOBALS['TL_LANG']['con4gis']['core']['frontend']['genericUploadErrorMessage'],
            );
            if ($type === 'ckeditor') {
                $response = "<script>window.parent.CKEDITOR.tools.callFunction(".$request->query->get('CKEditorFuncNum').", '', '".$response['message']."');</script>";
            }
        }
        if ($type === 'json') {
            return new JsonResponse($response);
        } else {
            return new Response($response);
        }
    }

    public function fileUploadAction(Request $request) {
        System::loadLanguageFile('con4giscoreupload');

        if ($request->files instanceof FileBag) {
            $files = $request->files;
            $uploadedFile = $files->get('upload');
            if ($uploadedFile instanceof UploadedFile) {
                $settings = C4gSettingsModel::findSettings();
                $allowedTypes = explode(',', $settings->uploadAllowedDocumentTypes);
                $maxSize = $settings->uploadMaxFileSize;
                if (empty($allowedTypes) || !is_array($allowedTypes) ||
                    !in_array($uploadedFile->getMimeType(), $allowedTypes, true)) {
                    $allowedTypes = explode(',', $settings->uploadAllowedGenericTypes);
                    if (empty($allowedTypes) || !is_array($allowedTypes) ||
                        !in_array($uploadedFile->getMimeType(), $allowedTypes, true)) {
                        return new JsonResponse([], Response::HTTP_BAD_REQUEST);
                    } else {
                        $uploadDirectoryBinary = $settings->uploadPathGeneric;
                    }
                } else {
                    $uploadDirectoryBinary = $settings->uploadPathDocuments;
                }



                if ($maxSize < $uploadedFile->getSize()) {
                    return new JsonResponse([], Response::HTTP_BAD_REQUEST);
                }

                $uploadDirectoryString = \FilesModel::findByUuid(\Contao\StringUtil::binToUuid($uploadDirectoryBinary))->path;

                $subDirectory = date("Y-m-d");
                $uploadDirectoryString = $uploadDirectoryString . "/" .$subDirectory;
                if (!is_dir(TL_ROOT . "/" . $uploadDirectoryString)) {
                    mkdir(TL_ROOT . "/" . $uploadDirectoryString, 0777, true);
                }
                $fileExtension = explode('/', $uploadedFile->getMimeType())[1];
                $fileName   = md5(uniqid('', true)) . "." .$fileExtension;
                $uploadPath = TL_ROOT . '/' . $uploadDirectoryString;
                $uploadedFile->move($uploadPath, $fileName);
                $url = System::getContainer()->get('contao.insert_tag.parser')->replace('{{env::url}}') . "/" . $uploadDirectoryString . "/" . $fileName;

                return new JsonResponse(['url' => $url]);

            }
        }
        return new JsonResponse([], Response::HTTP_BAD_REQUEST);
    }
}