<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Dependency;

use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Setup\Module\Dependency\Circular as CircularTool;
use Magento\Setup\Module\Dependency\Report\BuilderInterface;
use Magento\Setup\Module\Dependency\Report\Circular as CircularReport;
use Magento\Setup\Module\Dependency\Report\Dependency;
use Magento\Setup\Module\Dependency\Report\Framework;

/**
 * Service Locator (instead DI container)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ServiceLocator
{
    /**
     * Xml config dependencies parser
     *
     * @var ParserInterface
     */
    private static $xmlConfigParser;

    /**
     * Composer Json parser
     *
     * @var ParserInterface
     */
    private static $composerJsonParser;

    /**
     * Framework dependencies parser
     *
     * @var ParserInterface
     */
    private static $frameworkDependenciesParser;

    /**
     * Modules dependencies report builder
     *
     * @var BuilderInterface
     */
    private static $dependenciesReportBuilder;

    /**
     * Modules circular dependencies report builder
     *
     * @var BuilderInterface
     */
    private static $circularDependenciesReportBuilder;

    /**
     * Framework dependencies report builder
     *
     * @var BuilderInterface
     */
    private static $frameworkDependenciesReportBuilder;

    /**
     * Csv file writer
     *
     * @var Csv
     */
    private static $csvWriter;

    /**
     * Get modules dependencies report builder
     *
     * @return BuilderInterface
     */
    public static function getDependenciesReportBuilder()
    {
        if (null === self::$dependenciesReportBuilder) {
            self::$dependenciesReportBuilder = new Dependency\Builder(
                self::getComposerJsonParser(),
                new Dependency\Writer(self::getCsvWriter())
            );
        }
        return self::$dependenciesReportBuilder;
    }

    /**
     * Get modules dependencies from composer.json parser
     *
     * @return ParserInterface
     */
    private static function getComposerJsonParser()
    {
        if (null === self::$composerJsonParser) {
            self::$composerJsonParser = new Parser\Composer\Json();
        }
        return self::$composerJsonParser;
    }

    /**
     * Get csv file writer
     *
     * @return Csv
     */
    private static function getCsvWriter()
    {
        if (null === self::$csvWriter) {
            self::$csvWriter = new Csv(new File());
        }
        return self::$csvWriter;
    }

    /**
     * Get modules circular dependencies report builder
     *
     * @return BuilderInterface
     */
    public static function getCircularDependenciesReportBuilder()
    {
        if (null === self::$circularDependenciesReportBuilder) {
            self::$circularDependenciesReportBuilder = new CircularReport\Builder(
                self::getComposerJsonParser(),
                new CircularReport\Writer(self::getCsvWriter()),
                new CircularTool()
            );
        }
        return self::$circularDependenciesReportBuilder;
    }

    /**
     * Get framework dependencies report builder
     *
     * @return BuilderInterface
     */
    public static function getFrameworkDependenciesReportBuilder()
    {
        if (null === self::$frameworkDependenciesReportBuilder) {
            self::$frameworkDependenciesReportBuilder = new Framework\Builder(
                self::getFrameworkDependenciesParser(),
                new Framework\Writer(self::getCsvWriter()),
                self::getXmlConfigParser()
            );
        }
        return self::$frameworkDependenciesReportBuilder;
    }

    /**
     * Get framework dependencies parser
     *
     * @return ParserInterface
     */
    private static function getFrameworkDependenciesParser()
    {
        if (null === self::$frameworkDependenciesParser) {
            self::$frameworkDependenciesParser = new Parser\Code();
        }
        return self::$frameworkDependenciesParser;
    }

    /**
     * Get modules dependencies parser
     *
     * @return ParserInterface
     */
    private static function getXmlConfigParser()
    {
        if (null === self::$xmlConfigParser) {
            self::$xmlConfigParser = new Parser\Config\Xml();
        }
        return self::$xmlConfigParser;
    }
}
