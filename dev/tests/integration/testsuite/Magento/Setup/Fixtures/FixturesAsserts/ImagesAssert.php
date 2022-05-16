<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Fixtures\FixturesAsserts;

use AssertionError;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\ReadHandler;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;

/**
 * Class ImagesAssert
 *
 * Class performs assertions to check that generated images are valid
 * after running setup:performance:generate-fixtures command
 */
class ImagesAssert
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ReadHandler
     */
    private $readHandler;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Config
     */
    private $mediaConfig;

    /**
     * @var ReadInterface
     */
    private $mediaDirectory;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductRepositoryInterface $productRepository
     * @param ReadHandler $readHandler
     * @param Filesystem $filesystem
     * @param Config $mediaConfig
     */
    public function __construct(
        SearchCriteriaBuilder       $searchCriteriaBuilder,
        ProductRepositoryInterface    $productRepository,
        ReadHandler $readHandler,
        Filesystem                      $filesystem,
        Config        $mediaConfig
    )
    {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->readHandler = $readHandler;
        $this->filesystem = $filesystem;
        $this->mediaConfig = $mediaConfig;
    }

    /**
     * Performs assertions over images
     *
     * @return bool
     * @throws AssertionError
     */
    public function assert()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $products = $this->productRepository->getList($searchCriteria)->getItems();

        foreach ($products as $product) {
            $this->assertProductMediaGallery($product);
            $this->assertProductMediaAttributes($product);
            $this->assertProductImageExistsInFS($product);
        }

        return true;
    }

    /**
     * Performs assertions over media_gallery product attribute
     *
     * @param Product $product
     * @throws AssertionError
     */
    private function assertProductMediaGallery(Product $product)
    {
        $extendedProduct = $this->readHandler->execute($product);
        $mediaGalleryImages = $extendedProduct->getMediaGalleryEntries();

        if (count($mediaGalleryImages) !== 1) {
            throw new AssertionError('Products supposed to contain one image');
        }

        $image = reset($mediaGalleryImages);

        if ($image->getFile() === null) {
            throw new AssertionError('Image path should not be null');
        }
    }

    /**
     * Performs assertions over product media attributes
     * e.g. image|small_image|swatch_image|thumbnail
     *
     * @param Product $product
     * @throws AssertionError
     */
    private function assertProductMediaAttributes(Product $product)
    {
        foreach ($product->getMediaAttributeValues() as $attributeCode => $attributeValue) {
            if (empty($attributeValue)) {
                throw new AssertionError(
                    sprintf('Attribute: %s should not be empty', $attributeCode)
                );
            }
        }
    }

    /**
     * Performs assertions over image files in FS
     *
     * @param Product $product
     * @throws AssertionError
     */
    private function assertProductImageExistsInFS(Product $product)
    {
        $mediaDirectory = $this->getMediaDirectory();
        $mediaAttributes = $product->getMediaAttributeValues();

        if (!$mediaDirectory->isExist($this->mediaConfig->getBaseMediaPath() . $mediaAttributes['image'])) {
            throw new AssertionError('Image file for product supposed to exist');
        }
    }

    /**
     * Local cache for $mediaDirectory
     *
     * @return ReadInterface
     */
    private function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }
}
