<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Abstract database handler for integration tests
 */

namespace Magento\TestFramework\Db;

use LogicException;
use Magento\Framework\Shell;

abstract class AbstractDb
{
    /**
     * DB host name
     *
     * @var string
     */
    protected $_host = '';

    /**
     * DB credentials -- user name
     *
     * @var string
     */
    protected $_user = '';

    /**
     * DB credentials -- password
     *
     * @var string
     */
    protected $_password = '';

    /**
     * DB name
     *
     * @var string
     */
    protected $_schema = '';

    /**
     * Path to a temporary directory in the file system
     *
     * @var string
     */
    protected $_varPath = '';

    /**
     * @var Shell
     */
    protected $_shell;

    /**
     * Set initial essential parameters
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $schema
     * @param string $varPath
     * @param Shell $shell
     */
    public function __construct($host, $user, $password, $schema, $varPath, Shell $shell)
    {
        $this->_host = $host;
        $this->_user = $user;
        $this->_password = $password;
        $this->_schema = $schema;
        $this->_varPath = $varPath;
        $this->_shell = $shell;
    }

    /**
     * Remove all DB objects
     */
    abstract public function cleanup();

    /**
     * Is dump exists
     *
     * @return bool
     */
    abstract public function isDbDumpExists();

    /**
     * Store setup db dump
     */
    abstract public function storeDbDump();

    /**
     * Restore db from setup db dump
     */
    abstract public function restoreFromDbDump();

    /**
     * @return string
     */
    abstract public function getVendorName();

    /**
     * @return string
     */
    public function getSchema()
    {
        return $this->_schema;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->_host;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Get filename for setup db dump
     *
     * @return string
     */
    abstract protected function getSetupDbDumpFilename();

    /**
     * Create file with sql script content.
     * Utility method that is used in children classes
     *
     * @param string $file
     * @param string $content
     * @return int
     */
    protected function _createScript($file, $content)
    {
        return file_put_contents($file, $content);
    }

    /**
     * @throws LogicException
     */
    protected function assertVarPathWritable()
    {
        if (!is_dir($this->_varPath) || !is_writable($this->_varPath)) {
            throw new LogicException("The specified '{$this->_varPath}' is not a directory or not writable.");
        }
    }
}
