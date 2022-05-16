<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model\FixtureGenerator;

use Magento\Framework\Model\AbstractModel;

/**
 * Generate entity template which is used for entity generation
 */
interface TemplateEntityGeneratorInterface
{
    /**
     * @return AbstractModel
     */
    public function generateEntity();
}
