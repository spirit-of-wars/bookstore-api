<?php

namespace App\Service\File;

use App\Entity\Resource;
use App\Enum\FileTypeEnum;
use App\Enum\ResourceTypeEnum;
use App\Helper\FilePathHelper;
use App\Helper\TranslitHelper;
use App\Mif;
use App\Request;
use App\Service\Entity\EntityService;
use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileService
 * @package App\Service\FIle
 */
class FileService extends EntityService
{
    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return Resource::class;
    }

    /**
     * @param Request $request
     * @return array|null
     * @throws Exception
     */
    public function uploadFileForEntityFromRequest(Request $request)
    {
        $files = $request->files->all();
        if (empty($files)) {
            return null;
        }

        $entityResourceId = $request->request->get('id');
        $entityResource = $this->getResource($entityResourceId);

        return $this->attachFiles($files, $entityResource);
    }

    /**
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function deleteResource($id)
    {
        /** @var Resource $resource */
        $resource = $this->getEntity($id);
        if (!$resource) {
            throw new Exception('Resource with id = '. $id .' not found');
        }

        $currentFile = FilePathHelper::getPathPublicDirectory() .'/'. $resource->getPath();
        if (file_exists($currentFile)) {
            unlink($currentFile);
        }

        $resource->remove();

        return true;
    }

    /**
     * @param array $files
     * @param Resource $entityResource
     * @return array
     * @throws Exception
     */
    public function attachFiles(array $files, Resource $entityResource)
    {
        $filePath = FilePathHelper::buildFilePath();
        $fileUrl = FilePathHelper::buildFileUrl();
        $pathPublicDirectory = FilePathHelper::getPathPublicDirectory();
        $fileUrls = [];

        $isNew = $entityResource->isNew();
        foreach ($files as $file) {
            $mimeType = $file->getMimeType();
            if (!FileTypeEnum::validateFileType($mimeType)) {
                throw new Exception('Unsupported file type ' . $file->getMimeType());
            }

            if (!$isNew) {
                $currentFile = $pathPublicDirectory .'/'. $entityResource->getPath();
                if (file_exists($currentFile)) {
                    unlink($currentFile);
                    if (file_exists($currentFile)) {
                        throw new Exception('Can not unlink file ' . $currentFile);
                    }
                }
            }

            $fileName = $this->getFileName($file);
            $this->upload($file, $filePath, $fileName);
            $fullFileUrl = $fileUrl . $fileName;

            $attributes = [
                'path' => $fullFileUrl,
                'name' => $fileName,
                'format' => $mimeType,
                'type' => ResourceTypeEnum::getSourceTypeFromFile($mimeType),
            ];

            $entityResource->setAttributes($attributes);
            $entityResource->save();

            $fileUrls[] = ['id' => $entityResource->getId(), 'src' => $fullFileUrl];
        }

        return $fileUrls;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    private function getFileName($file) : string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = TranslitHelper::cyrillicTransliter($originalFilename);

        return $safeFilename . '.' . $file->guessExtension();
    }

    /**
     * @param UploadedFile $file
     * @param string $filePath
     * @param $fileName
     * @return string
     * @throws Exception
     */
    private function upload(UploadedFile $file, $filePath, $fileName)
    {
        if ($this->checkFileSize($file)) {
            throw new Exception('File size is larger than allowed, file size: ' . $file->getSize());
        }

        if ($this->checkSvgJsInsert($file)) {
            throw new Exception('Svg file contains tag </script>');
        }

        try {
            $file->move($filePath, $fileName);
        } catch (FileException $e) {
            throw new Exception($e->getMessage());
        }

        return $fileName;
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    private function checkFileSize(UploadedFile $file) : bool
    {
        return $file->getSize() > Mif::getEnvConfig('FILE_SIZE');
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    private function checkSvgJsInsert(UploadedFile $file) : bool
    {
        $content = file_get_contents($file->getPathname());

        return strripos($content, '</script>');
    }

    /**
     * @param $id
     * @throws Exception
     * @return Resource
     */
    private function getResource($id)
    {
        if ($id) {
            /** @var Resource $entityResource */
            $entityResource = $this->getEntity($id);
            if (!$entityResource) {
                throw new Exception('Resource with id = '. $id .' not found');
            }
        } else {
            $entityResource = new Resource();
        }

        return $entityResource;
    }
}
