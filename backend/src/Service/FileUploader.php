<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileUploader
{
    private $targetDirectory;

    public function __construct(ParameterBagInterface $params)
    {
        $this->targetDirectory = $params->get('uploads_directory');
    }

    public function upload(UploadedFile $file): string
    {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
