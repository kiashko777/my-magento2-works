<?php
/**
 * Layout nodes integrity tests
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity;

use Magento\Framework\App\Utility\AggregateInvoker;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\File;
use Magento\Framework\View\File\Collector\Override\Base;
use Magento\Framework\View\File\Collector\Override\ThemeModular;
use Magento\Framework\View\File\CollectorInterface;
use Magento\Framework\View\Layout\Element;
use Magento\Framework\View\Layout\ProcessorInterface;
use Magento\Framework\View\Model\Layout\Merge;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Theme;
use Magento\Theme\Model\Theme\Collection;
use Magento\Theme\Model\Theme\Data;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LayoutTest extends TestCase
{
    /**
     * Cached lists of files
     *
     * @var array
     */
    protected static $_cachedFiles = [];

    public static function setUpBeforeClass(): void
    {
        Bootstrap::getObjectManager()->configure(
            ['preferences' => [Theme::class => Data::class]]
        );
    }

    public static function tearDownAfterClass(): void
    {
        self::$_cachedFiles = []; // Free memory
    }

    public function testHandleLabels()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
        /**
         * @param ThemeInterface $theme
         */
            function (ThemeInterface $theme) {
                $xml = $this->_composeXml($theme);

                $xpath = '/layouts/*[@design_abstraction]';
                $handles = $xml->xpath($xpath) ?: [];

                /** @var Element $node */
                $errors = [];
                foreach ($handles as $node) {
                    if (!$node->xpath('@label')) {
                        $nodeId = $node->getAttribute('id') ? ' id=' . $node->getAttribute('id') : '';
                        $errors[] = $node->getName() . $nodeId;
                    }
                }
                if ($errors) {
                    $this->fail(
                        'The following handles must have label, but they don\'t have it:' . PHP_EOL . var_export(
                            $errors,
                            true
                        )
                    );
                }
            },
            $this->areasAndThemesDataProvider()
        );
    }

    /**
     * Composes full layout xml for designated parameters
     *
     * @param ThemeInterface $theme
     * @return Element
     */
    protected function _composeXml(ThemeInterface $theme)
    {
        /** @var ProcessorInterface $layoutUpdate */
        $layoutUpdate = Bootstrap::getObjectManager()->create(
            ProcessorInterface::class,
            ['theme' => $theme]
        );
        return $layoutUpdate->getFileLayoutUpdatesXml();
    }

    /**
     * List all themes available in the system
     *
     * A test that uses such data provider is supposed to gather view resources in provided scope
     * and analyze their integrity. For example, merge and verify all layouts in this scope.
     *
     * Such tests allow to uncover complicated code integrity issues, that may emerge due to view fallback mechanism.
     * For example, a module layout file is overlapped by theme layout, which has mistakes.
     * Such mistakes can be uncovered only when to emulate this particular theme.
     * Also emulating "no theme" mode allows to detect inversed errors: when there is a view file with mistake
     * in a module, but it is overlapped by every single theme by files without mistake. Putting question of code
     * duplication aside, it is even more important to detect such errors, than an error in a single theme.
     *
     * @return array
     */
    public function areasAndThemesDataProvider()
    {
        $result = [];
        $themeCollection = Bootstrap::getObjectManager()->create(
            ThemeInterface::class
        )->getCollection();
        /** @var $theme ThemeInterface */
        foreach ($themeCollection as $theme) {
            $result[$theme->getFullPath() . ' [' . $theme->getId() . ']'] = [$theme];
        }
        return $result;
    }

    public function testPageTypesDeclaration()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
        /**
         * Check whether page types are declared only in layout update files allowed for it - base ones
         */
            function (File $layout) {
                $content = simplexml_load_file($layout->getFilename());
                $this->assertEmpty(
                    $content->xpath(Merge::XPATH_HANDLE_DECLARATION),
                    "Theme layout update '" . $layout->getFilename() . "' contains page type declaration(s)"
                );
            },
            $this->pageTypesDeclarationDataProvider()
        );
    }

    /**
     * Get theme layout updates
     *
     * @return File[]
     */
    public function pageTypesDeclarationDataProvider()
    {
        /** @var $themeUpdates \Magento\Framework\View\File\Collector\ThemeModular */
        $themeUpdates = Bootstrap::getObjectManager()
            ->create(\Magento\Framework\View\File\Collector\ThemeModular::class, ['subDir' => 'layout']);
        /** @var $themeUpdatesOverride ThemeModular */
        $themeUpdatesOverride = Bootstrap::getObjectManager()
            ->create(
                ThemeModular::class,
                ['subDir' => 'layout/override/theme']
            );
        /** @var $themeCollection Collection */
        $themeCollection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        /** @var $themeLayouts File[] */
        $themeLayouts = [];
        /** @var $theme ThemeInterface */
        foreach ($themeCollection as $theme) {
            $themeLayouts = array_merge($themeLayouts, $themeUpdates->getFiles($theme, '*.xml'));
            $themeLayouts = array_merge($themeLayouts, $themeUpdatesOverride->getFiles($theme, '*.xml'));
        }
        $result = [];
        foreach ($themeLayouts as $layout) {
            $result[$layout->getFileIdentifier()] = [$layout];
        }
        return $result;
    }

    public function testOverrideBaseFiles()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
        /**
         * Check, that for an overriding file ($themeFile) in a theme ($theme), there is a corresponding base file
         *
         * @param File $themeFile
         * @param ThemeInterface $theme
         */
            function ($themeFile, $theme) {
                $baseFiles = self::_getCachedFiles(
                    $theme->getArea(),
                    \Magento\Framework\View\File\Collector\Base::class,
                    $theme
                );
                $fileKey = $themeFile->getModule() . '/' . $themeFile->getName();
                $this->assertArrayHasKey(
                    $fileKey,
                    $baseFiles,
                    sprintf("Could not find base file, overridden by theme file '%s'.", $themeFile->getFilename())
                );
            },
            $this->overrideBaseFilesDataProvider()
        );
    }

    /**
     * Retrieve list of cached source files
     *
     * @param string $cacheKey
     * @param string $sourceClass
     * @param ThemeInterface $theme
     * @return File[]
     */
    protected static function _getCachedFiles(
        $cacheKey,
        $sourceClass,
        ThemeInterface $theme
    )
    {
        if (!isset(self::$_cachedFiles[$cacheKey])) {
            /* @var $fileList File[] */
            $fileList = Bootstrap::getObjectManager()
                ->create($sourceClass, ['subDir' => 'layout'])->getFiles($theme, '*.xml');
            $files = [];
            foreach ($fileList as $file) {
                $files[$file->getModule() . '/' . $file->getName()] = true;
            }
            self::$_cachedFiles[$cacheKey] = $files;
        }
        return self::$_cachedFiles[$cacheKey];
    }

    /**
     * @return array
     */
    public function overrideBaseFilesDataProvider()
    {
        return $this->_retrieveFilesForEveryTheme(
            Bootstrap::getObjectManager()
                ->create(
                    Base::class,
                    ['subDir' => 'layout/override/base']
                )
        );
    }

    /**
     * Scan all the themes in the system, for each theme retrieve list of files via $filesRetriever,
     * and return them as array of pairs [file, theme].
     *
     * @param CollectorInterface $filesRetriever
     * @return array
     */
    protected function _retrieveFilesForEveryTheme(CollectorInterface $filesRetriever)
    {
        $result = [];
        /** @var $themeCollection Collection */
        $themeCollection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        /** @var $theme ThemeInterface */
        foreach ($themeCollection as $theme) {
            foreach ($filesRetriever->getFiles($theme, '*.xml') as $file) {
                $result['theme: ' . $theme->getFullPath() . ', ' . $file->getFilename()] = [$file, $theme];
            }
        }
        return $result;
    }

    public function testOverrideThemeFiles()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
        /**
         * Check, that for an ancestor-overriding file ($themeFile) in a theme ($theme),
         * there is a corresponding file in that ancestor theme
         *
         * @param File $themeFile
         * @param ThemeInterface $theme
         */
            function ($themeFile, $theme) {
                // Find an ancestor theme, where a file is to be overridden
                $ancestorTheme = $theme;
                while ($ancestorTheme = $ancestorTheme->getParentTheme()) {
                    if ($ancestorTheme == $themeFile->getTheme()) {
                        break;
                    }
                }
                $this->assertNotNull(
                    $ancestorTheme,
                    sprintf(
                        'Could not find ancestor theme "%s", ' .
                        'its layout file is supposed to be overridden by file "%s".',
                        $themeFile->getTheme()->getCode(),
                        $themeFile->getFilename()
                    )
                );

                // Search for the overridden file in the ancestor theme
                $ancestorFiles = self::_getCachedFiles(
                    $ancestorTheme->getFullPath(),
                    \Magento\Framework\View\File\Collector\ThemeModular::class,
                    $ancestorTheme
                );
                $fileKey = $themeFile->getModule() . '/' . $themeFile->getName();
                $this->assertArrayHasKey(
                    $fileKey,
                    $ancestorFiles,
                    sprintf(
                        "Could not find original file in '%s' theme, overridden by file '%s'.",
                        $themeFile->getTheme()->getCode(),
                        $themeFile->getFilename()
                    )
                );
            },
            $this->overrideThemeFilesDataProvider()
        );
    }

    /**
     * @return array
     */
    public function overrideThemeFilesDataProvider()
    {
        return $this->_retrieveFilesForEveryTheme(
            Bootstrap::getObjectManager()
                ->create(
                    ThemeModular::class,
                    ['subDir' => 'layout/override/theme']
                )
        );
    }

    /**
     * Validate node's declared position in hierarchy and add errors to the specified array if found
     *
     * @param SimpleXMLElement $node
     * @param Element $xml
     * @param array &$errors
     */
    protected function _collectHierarchyErrors($node, $xml, &$errors)
    {
        $name = $node->getName();
        $refName = $node->getAttribute('type') == $node->getAttribute('parent');
        if ($refName) {
            $refNode = $xml->xpath("/layouts/{$refName}");
            if (!$refNode) {
                $errors[$name][] = "Node '{$refName}', referenced in hierarchy, does not exist";
            }
        }
    }
}
