<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
declare(strict_types=1);

namespace Magento\MediaGallery\Model\Directory\Command;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\MediaGalleryApi\Api\DeleteDirectoriesByPathsInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for DeleteDirectoriesByPathsInterface
 */
class DeleteByPathsTest extends TestCase
{
    /**
     * @var DeleteDirectoriesByPathsInterface
     */
    private $deleteByPaths;

    /**
     * @var string
     */
    private $testDirectoryName = 'testDeleteDirectory';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @throws CouldNotDeleteException
     * @throws FileSystemException
     */
    public function testDeleteDirectory(): void
    {
        /** @var WriteInterface $mediaDirectory */
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $mediaDirectory->create($this->testDirectoryName);
        $fullPath = $mediaDirectory->getAbsolutePath($this->testDirectoryName);
        $this->assertFileExists($fullPath);
        $this->deleteByPaths->execute([$this->testDirectoryName]);
        $this->assertFileDoesNotExist($fullPath);
    }

    /**
     * @param array $paths
     * @throws CouldNotDeleteException
     * @dataProvider notAllowedPathsProvider
     */
    public function testDeleteDirectoryThatIsNotAllowed(array $paths): void
    {
        $this->expectException(CouldNotDeleteException::class);

        $this->deleteByPaths->execute($paths);
    }

    /**
     * Provider of paths that are not allowed for deletion
     *
     * @return array
     */
    public function notAllowedPathsProvider(): array
    {
        return [
            [
                ['../../pub/media']
            ],
            [
                ['theme']
            ],
            [
                ['../../pub/media', 'theme']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->deleteByPaths = Bootstrap::getObjectManager()->get(DeleteDirectoriesByPathsInterface::class);
        $this->filesystem = Bootstrap::getObjectManager()->get(Filesystem::class);
    }

    /**
     * @throws FileSystemException
     */
    protected function tearDown(): void
    {
        $directory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        if ($directory->isExist($this->testDirectoryName)) {
            $directory->delete($this->testDirectoryName);
        }
    }
}
