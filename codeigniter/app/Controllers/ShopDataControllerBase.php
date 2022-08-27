<?php

namespace App\Controllers;

use App\Helpers\MediaFile;
use CodeIgniter\Exceptions\PageNotFoundException;
use Exception;

class ShopDataControllerBase extends AppBaseController
{

    protected function handleAddMedia(int $productId = null) {
        $mediaUploadRules = [
            'mediaFile' => 'uploaded[mediaFile]|mime_in[mediaFile,image/jpg,image/jpeg,image/gif,image/png,image/webp,video/mp4]',
        ];

        if ($this->validate($mediaUploadRules)) {
            $shopId = $this->getOwnedShopId();
            $mediaFile = $this->request->getFile('mediaFile');
            // store media file

            $media = MediaFile::saveFromFile($mediaFile, MediaFile::TYPE_MEDIA);
            $mediaFileId = $media->getId();

            // link media file to store
            /** @var \App\Models\ShopMediaModel */
            $model = model(ShopMediaModel::class);
            $model->addForShop($mediaFileId, $media->getMimeType(), $shopId, $productId);

            return $mediaFileId;
        } 

        return null;
    }

    protected function handleDeleteMedia() {
        $deleteRules = [
            'deleteMediaId' => 'required'
        ];

        if ($this->validate($deleteRules)) {
            $mediaId = $this->request->getPost('deleteMediaId');

            /** @var \App\Models\ShopMediaModel */
            $model = model(ShopMediaModel::class);
            $media = $model->getById($mediaId);

            if (!$media) {
                throw new PageNotFoundException("Media does not exist");
            }

            if ($media['shop_id'] != $this->getOwnedShopId()) {
                throw new Exception("No access");
            }

            $model->deleteById($mediaId);

            $mediaFile = new MediaFile($media['id'], MediaFile::TYPE_MEDIA, $media['mimetype']);
            $mediaFile->deleteAllFiles();

            return $media;
        }

        return null;
    }

}