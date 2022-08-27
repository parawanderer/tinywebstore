<?php
namespace App\Helpers;

use App\Models\ShopMediaModel;
use App\Models\ShopModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use Exception;

// utility for managing size info
class MediaFile {
    
    public const TYPE_MEDIA = 0;
    public const TYPE_LOGO = 1;
    public const TYPE_BANNER = 2;

    public const SIZE_XL = 0;
    public const SIZE_L = 1;
    public const SIZE_M = 2;
    public const SIZE_S = 3;
    public const SIZE_XS = 4;

    public const SIZE_POSTFIX_L = "-l";
    public const SIZE_POSTFIX_M = "-m";
    public const SIZE_POSTFIX_S = "-s";
    public const SIZE_POSTFIX_XS = "-xs";

    private string $fileId;
    private int $type;
    private ?string $mimeType;

    private ?bool $hasThumbSizesCache = null;


    public static function saveFromFile(UploadedFile $file, int $type = MediaFile::TYPE_MEDIA) {
        $newId = $file->getRandomName();

        $media = new MediaFile($newId, $type, $file->getMimeType());
        $media->saveMedia($file);

        return $media;
    }

    public static function getThumbnailsOrNulls(?string $productMainMedia, ?string $mimeType) {
        if (!$productMainMedia || !$mimeType) {
            return [
                "xl" => null,
                "l" => null,
                "m" => null,
                "s" => null,
                "xs" => null,
            ];
        }

        $media = new MediaFile($productMainMedia, MediaFile::TYPE_MEDIA, $mimeType);
        return $media->getThumbnails();
    }

    public function __construct(string $fileId, int $type = MediaFile::TYPE_MEDIA, ?string $mimeType = null)
    {
        $this->fileId = $fileId;
        $this->mimeType = $mimeType;
        $this->type = $type;
    }

    public function getId() {
        return $this->fileId;
    }

    public function getMimeType() {
        return $this->mimeType;
    }

    public function getType() {
        return $this->type;
    }

    public function isVideoType() {
        if ($this->mimeType == null) {
            if ($this->getFileExt() === ".mp4") return true;
            return false;
        }

        return $this->mimeType === "video/mp4"; // only mp4 supported for this implementation
    }

    public function getVideoThumbnailId() {
        return $this->getIdNoExt() . ".jpg";
    }

    public function getThumbnails() {
        return [
            "xl" => $this->getThumbnailId(MediaFile::SIZE_XL),
            "l" => $this->getThumbnailId(MediaFile::SIZE_L),
            "m" => $this->getThumbnailId(MediaFile::SIZE_M),
            "s" => $this->getThumbnailId(MediaFile::SIZE_S),
            "xs" => $this->getThumbnailId(MediaFile::SIZE_XS),
        ];
    }

    public function getThumbnailId(int $fileSize = MediaFile::SIZE_XL) { // existing mediaFiles
        // size l is default upload size
        if ($fileSize === MediaFile::SIZE_XL || !$this->hasThumbnailSubSizes()) {
            return $this->getIdNoExt() . $this->getThumbnailFileExt();
        }

        // subsizes
        switch($fileSize) {
            case MediaFile::SIZE_XS:
                return $this->getIdNoExt() . MediaFile::SIZE_POSTFIX_XS . $this->getThumbnailFileExt();
            case MediaFile::SIZE_S:
                return $this->getIdNoExt() . MediaFile::SIZE_POSTFIX_S . $this->getThumbnailFileExt();
            case MediaFile::SIZE_M:
                return $this->getIdNoExt() . MediaFile::SIZE_POSTFIX_M . $this->getThumbnailFileExt();
            case MediaFile::SIZE_L: default:
                return $this->getIdNoExt() . MediaFile::SIZE_POSTFIX_L . $this->getThumbnailFileExt();
        }
    }

    
    public function getVideoThumbnailPath() {
        return ROOTPATH . "public/uploads/" . ShopMediaModel::SHOP_MEDIA_PATH . $this->getIdNoExt() . ".jpg";
    }

    public function getIdNoExt() {
        return substr($this->fileId, 0, strrpos($this->fileId, "."));
    }

    public function getFileExt() {
        return substr($this->fileId, strrpos($this->fileId, "."));
    }

    public function getThumbnailFileExt() {
        if ($this->isVideoType()) {
            return ".jpg";
        }

        return $this->getFileExt();
    }

    public function deleteAllFiles() {
        // main file
        $mainFile = $this->getMainFilePath();
        MediaFile::unlinkIfExists($mainFile);

        // subfiles
        if ($this->hasThumbnailSubSizes()) { // needs to happen first
            $thumbL = $this->getThumbnailFilePath(MediaFile::SIZE_L);
            MediaFile::unlinkIfExists($thumbL);

            $thumbM = $this->getThumbnailFilePath(MediaFile::SIZE_M);
            MediaFile::unlinkIfExists($thumbM);

            $thumbS = $this->getThumbnailFilePath(MediaFile::SIZE_S);
            MediaFile::unlinkIfExists($thumbS);

            $thumbXS = $this->getThumbnailFilePath(MediaFile::SIZE_XS);
            MediaFile::unlinkIfExists($thumbXS);
        }

        $thumbnailMain = $this->getThumbnailFilePath();
        MediaFile::unlinkIfExists($thumbnailMain);
    }

    public function hasThumbnailSubSizes() {
        if ($this->hasThumbSizesCache === null) {
            $sub = $this->getSubPathPiece();
            $fullPath = ROOTPATH . "public/uploads/" . $sub . $this->getIdNoExt() . MediaFile::SIZE_POSTFIX_M . $this->getThumbnailFileExt();
            $this->hasThumbSizesCache = file_exists($fullPath);
        }
        return $this->hasThumbSizesCache;
    }

    public function getMainFilePath() {
        $sub = $this->getSubPathPiece();
        return ROOTPATH . "public/uploads/" . $sub . $this->fileId;
    }

    public function getThumbnailFilePath(int $fileSize = MediaFile::SIZE_XL) {
        $sub = $this->getSubPathPiece();
        return ROOTPATH . "public/uploads/" . $sub . $this->getThumbnailId($fileSize);
    }

    private function getThumbnailIdNoCheck(int $fileSize = MediaFile::SIZE_XL) { // just to get the path
        // subsizes
        switch($fileSize) {
            case MediaFile::SIZE_XL:
                return $this->getIdNoExt() . $this->getThumbnailFileExt(); // default one
            case MediaFile::SIZE_XS:
                return $this->getIdNoExt() . MediaFile::SIZE_POSTFIX_XS . $this->getThumbnailFileExt();
            case MediaFile::SIZE_S:
                return $this->getIdNoExt() . MediaFile::SIZE_POSTFIX_S . $this->getThumbnailFileExt();
            case MediaFile::SIZE_M:
                return $this->getIdNoExt() . MediaFile::SIZE_POSTFIX_M . $this->getThumbnailFileExt();
            case MediaFile::SIZE_L: default:
                return $this->getIdNoExt() . MediaFile::SIZE_POSTFIX_L . $this->getThumbnailFileExt();
        }
    }

    private function saveMedia(UploadedFile $file) {
        if ($file->isValid() && !$file->hasMoved()) {
            if ($this->isVideoType()) { // prerequisite for derived
                $thumbnailName = $this->getVideoThumbnailPath();
                FFMPregHelper::saveThumbnail($file->getPathname(), $thumbnailName);
            }
            $basePath = $this->getBasePath();
            $file->move($basePath, $this->fileId);
            
            $this->createAndSaveDerivedSizes();
        }
    }

    private function createAndSaveDerivedSizes() {
        $image = \Config\Services::image('gd');

        $mainFile = $this->getThumbnailFilePathNoCheck(MediaFile::SIZE_XL);
        
        $sizeLPath = $this->getThumbnailFilePathNoCheck(MediaFile::SIZE_L);
        $sizeMPath = $this->getThumbnailFilePathNoCheck(MediaFile::SIZE_M);
        $sizeSPath = $this->getThumbnailFilePathNoCheck(MediaFile::SIZE_S);
        $sizeXSPath = $this->getThumbnailFilePathNoCheck(MediaFile::SIZE_XS);

        $image->withFile($mainFile)
            ->resize(360, 360, true, 'auto')
            ->save($sizeLPath);

        $image->withFile($mainFile)
            ->resize(160, 160, true, 'auto')
            ->save($sizeMPath);

        $image->withFile($mainFile)
            ->fit(80, 80, 'center')
            ->save($sizeSPath);
        
        $image->withFile($mainFile)
            ->fit(48, 48, 'center')
            ->save($sizeXSPath);
    }

    private function getThumbnailFilePathNoCheck(int $fileSize = MediaFile::SIZE_XL) {
        $sub = $this->getSubPathPiece();
        return ROOTPATH . "public/uploads/" . $sub . $this->getThumbnailIdNoCheck($fileSize);
    }

    private function getBasePath() {
        $sub = $this->getSubPathPiece();
        return ROOTPATH . "public/uploads/" . $sub;
    }

    private function getSubPathPiece() {
        switch ($this->type) {
            case MediaFile::TYPE_BANNER:
                return ShopModel::SHOP_BANNER_PATH;
            case MediaFile::TYPE_LOGO:
                return ShopModel::SHOP_LOGO_PATH;
            case MediaFile::TYPE_MEDIA: default:
                return ShopMediaModel::SHOP_MEDIA_PATH;
        }
    }

    private static function unlinkIfExists(string $path) {
        if (file_exists($path)) unlink($path);
    }
}