<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * PHP Code Mess v1.3.3 tool wrapper
 */

namespace Magento\TestFramework\CodingStandard\Tool;

use Magento\TestFramework\CodingStandard\ToolInterface;
use PHPMD\RuleSetFactory;
use PHPMD\TextUI\Command;
use PHPMD\TextUI\CommandLineOptions;

class CodeMessDetector implements ToolInterface
{
    /**
     * Ruleset directory
     *
     * @var string
     */
    private $rulesetFile;

    /**
     * Report file
     *
     * @var string
     */
    private $reportFile;

    /**
     * Constructor
     *
     * @param string $rulesetDir \Directory that locates the inspection rules
     * @param string $reportFile Destination file to write inspection report to
     */
    public function __construct($rulesetFile, $reportFile)
    {
        $this->reportFile = $reportFile;
        $this->rulesetFile = $rulesetFile;
    }

    /**
     * Whether the tool can be ran on the current environment
     *
     * @return bool
     */
    public function canRun()
    {
        return class_exists(Command::class);
    }

    /**
     * {@inheritdoc}
     */
    public function run(array $whiteList)
    {
        if (empty($whiteList)) {
            return Command::EXIT_SUCCESS;
        }

        $commandLineArguments = [
            'run_file_mock', //emulate script name in console arguments
            implode(',', $whiteList),
            'text', //report format
            $this->rulesetFile,
            '--reportfile',
            $this->reportFile,
        ];

        $options = new CommandLineOptions($commandLineArguments);

        $command = new Command();

        return $command->run($options, new RuleSetFactory());
    }
}
