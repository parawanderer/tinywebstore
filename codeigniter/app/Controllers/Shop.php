<?php

namespace App\Controllers;

use App\Helpers\HtmlSanitizer;
use App\Helpers\ContrastRatioChecker;
use App\Helpers\FFMPregHelper;
use App\Models\ShopMediaModel;
use App\Models\ShopModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Files\Exceptions\FileNotFoundException;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;
use Exception;

class Shop extends AppBaseController
{
    public function index(int $shopId = -1) {
        $templateParams = $this->getUserTemplateParams();

        /** @var \App\Models\ShopModel */
        $model = model(ShopModel::class);
        $shop = $model->getShop($shopId);

        if (!$shop) {
            throw new PageNotFoundException("The shop does not exist");
        }

        /** @var \App\Models\ShopMediaModel */
        $mediaModel = model(ShopMediaModel::class);

        $media = $mediaModel->getForShop($shopId);

        $templateParams['owns_shop'] = $this->ownsShop($shop['id']);
        $templateParams['shop'] = $shop;
        $templateParams['description_safe'] = HtmlSanitizer::sanitize($shop['description']);
        $templateParams['media'] = $media;

        return view('templates/header')
            . view('templates/top_bar', $templateParams)
            . view('shop/index', $templateParams)
            . view('templates/footer');
    }

    public function edit() {
        $ownedShopId = $this->getOwnedShopId();
        if (!$ownedShopId) return redirect()->to("/");

        /** @var \App\Models\ShopModel */
        $model = model(ShopModel::class);
        $shop = $model->getShop($ownedShopId);

        $templateParams = $this->getUserTemplateParams();
        $templateParams['shop'] = $shop;
        $templateParams['error'] = false;


        $shopEditRules = [
            'shopName' => 'required',
            'supportEmail' => 'valid_email',
            'backgroundColor' => 'hexcolor',
            'textColor' => 'hexcolor',
            'shopBanner' => 'is_image[shopBanner]|mime_in[shopBanner,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
            'shopLogo' => 'is_image[shopLogo]|mime_in[shopLogo,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
        ];


        if ($this->request->getMethod() === 'post') {        
            
            if ($this->validate($shopEditRules) && $this->validateColorContrast()) {
                $shopLogo = $this->request->getFile('shopLogo');
                $shopBanner = $this->request->getFile('shopBanner');

                [ $shopLogoName, $shopBannerName ] = $this->replaceShopImages(
                    $shop, 
                    boolval($this->request->getPost('removeCurrentBanner')),
                    boolval($this->request->getPost('removeCurrentLogo')),
                    $shopLogo, 
                    $shopBanner
                );
    
                $model->update($ownedShopId, [
                    'name' => $this->request->getPost('shopName'),
                    'support_email' => $this->request->getPost('supportEmail'),
                    'address' => $this->request->getPost('address'),
                    'phone_number' => $this->request->getPost('phoneNumber'),
                    'description' => $this->request->getPost('description'),
                    'theme_color' => $this->request->getPost('backgroundColor'),
                    'font_color' => $this->request->getPost('textColor'),
                    'shop_logo_img' => $shopLogoName,
                    'shop_banner_img' => $shopBannerName,
                ]);
    
                return redirect()->to("/shop/{$ownedShopId}");
            } else {
                $templateParams['error'] = true;
            }   
        }

        return view('templates/header')
            . view('templates/top_bar', $templateParams)
            . view('shop/edit', $templateParams)
            . view('templates/footer');
    }

    public function deleteMedia() {
        if (!$this->loggedIn()) throw new Exception("No access");

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

            unlink(ROOTPATH . "public/uploads/" . ShopMediaModel::SHOP_MEDIA_PATH . $media['id']);
            if (Shop::isVideoType($media['mimetype'])) {
                $thumbnailName = Shop::getVideoThumnailPath($mediaId);
                unlink($thumbnailName);
            }

            $model->deleteById($mediaId);

            return redirect()->to("/shop/{$media['shop_id']}");
        }

        throw new Exception("Bad request");
    }

    public function addMedia() {
        if (!$this->loggedIn()) throw new Exception("No access");
        if (!$this->isShopOwner()) throw new Exception("No access");
        // add media to own shop

        $mediaUploadRules = [
            'mediaFile' => 'uploaded[mediaFile]|mime_in[mediaFile,image/jpg,image/jpeg,image/gif,image/png,image/webp,video/mp4]',
        ];

        if ($this->validate($mediaUploadRules)) {
            $shopId = $this->getOwnedShopId();
            $mediaFile = $this->request->getFile('mediaFile');
            $mediaFileId = null;
            $mimeType = $mediaFile->getMimeType();
            // store media file
            if (!$mediaFile->hasMoved()) {
                $mediaFileId = $mediaFile->getRandomName();

                if (Shop::isVideoType($mimeType)) {
                    $this->handleSaveThumbnail($mediaFile, $mediaFileId);
                }

                $mediaFile->move(ROOTPATH . "public/uploads/" . ShopMediaModel::SHOP_MEDIA_PATH, $mediaFileId);
            } else {
                throw new Exception("Error processing request");
            }

            // link media file to store
            /** @var \App\Models\ShopMediaModel */
            $model = model(ShopMediaModel::class);
            $model->addForShop($mediaFileId, $mimeType, $shopId);

            // redirect
            return redirect()->to("/shop/{$shopId}");
        } 

        throw new Exception("Bad request");
    }

    private function handleSaveThumbnail(UploadedFile $file, string $mediaFileId) {
        $thumbnailName = Shop::getVideoThumnailPath($mediaFileId);
        FFMPregHelper::saveThumbnail( $file->getPathname(), $thumbnailName);
    }

    private function validateColorContrast() {
        $bg = $this->request->getPost('backgroundColor');
        $text = $this->request->getPost('textColor');

        if (!ContrastRatioChecker::isOkContrast($bg, $text)) {
            $this->validator->setError('backgroundColor', 'Insufficient contrast ratio with text color!');
            return false;
        }

        return true;
    }

    private function replaceShopImages(array $shop, bool $removeCurrentBanner, bool $removeCurrentLogo, UploadedFile $shopLogo = null, UploadedFile $shopBanner = null) {
        $shopLogoName = $shop['shop_logo_img'];
        $shopBannerName = $shop['shop_banner_img'];
        
        // delete old
        if (($removeCurrentLogo || $shopLogo->isValid()) && $shop['shop_logo_img']) {
            try {
                $oldShopImg = new File(ROOTPATH . "public/uploads/" . ShopModel::SHOP_LOGO_PATH . $shop['shop_logo_img'], true);
                unlink($oldShopImg->getPathname());
            } catch (FileNotFoundException $e) {}

            $shopLogoName = null;
        }

        if (($removeCurrentBanner || $shopBanner->isValid()) && $shop['shop_banner_img']) {
            try {
                $oldShopImg = new File(ROOTPATH . "public/uploads/" . ShopModel::SHOP_BANNER_PATH . $shop['shop_banner_img'], true);
                unlink($oldShopImg->getPathname());
            } catch (FileNotFoundException $e) {}

            $shopBannerName = null;
        }

        //add new
        if ($shopLogo->isValid() && !$shopLogo->hasMoved()) {
            $shopLogoName = $shopLogo->getRandomName();
            $shopLogo->move(ROOTPATH . "public/uploads/" . ShopModel::SHOP_LOGO_PATH, $shopLogoName);
        }

        if ($shopBanner->isValid() && !$shopBanner->hasMoved()) {
            $shopBannerName = $shopBanner->getRandomName();
            $shopBanner->move(ROOTPATH . "public/uploads/" . ShopModel::SHOP_BANNER_PATH, $shopBannerName);
        }

        return [$shopLogoName, $shopBannerName];
    }

    private static function isVideoType(string $mimeType) {
        return $mimeType === "video/mp4"; // only mp4 supported for this implementation
    }

    private static function getVideoThumnailPath(string $fileName) {
        return ROOTPATH . "public/uploads/" . ShopMediaModel::SHOP_MEDIA_PATH . substr($fileName, 0, strrpos($fileName, ".")) . ".jpg";
    }
}