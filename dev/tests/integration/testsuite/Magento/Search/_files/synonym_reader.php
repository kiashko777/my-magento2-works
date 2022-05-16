<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Search\Model\SynonymReader;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

// Synonym groups for "Default Store View"
/** @var $synonymsModel SynonymReader */
$synonymsModel = $objectManager->create(SynonymReader::class);
$synonymsModel->setSynonyms('queen,monarch')->setStoreId(1)->save();

$synonymsModel = $objectManager->create(SynonymReader::class);
$synonymsModel->setSynonyms('british,english')->setStoreId(1)->save();

$synonymsModel = $objectManager->create(SynonymReader::class);
$synonymsModel->setSynonyms('schlicht,natürlich')->setStoreId(1)->save();

// Synonym groups for "Main Website"
$synonymsModel = $objectManager->create(SynonymReader::class);
$synonymsModel->setSynonyms('orange,magento')->setWebsiteId(1)->save();

$synonymsModel = $objectManager->create(SynonymReader::class);
$synonymsModel->setSynonyms('hill,mountain,peak')->setWebsiteId(1)->save();

$synonymsModel = $objectManager->create(SynonymReader::class);
$synonymsModel->setSynonyms('Community Engineering,Contributors,Magento Community Engineering')->setWebsiteId(1)
    ->save();

$synonymsModel = $objectManager->create(SynonymReader::class);
$synonymsModel->setSynonyms('Engineering,Technical Staff')->setWebsiteId(1)->save();

// Synonym groups for "All Store Views"
$synonymsModel = $objectManager->create(SynonymReader::class);
$synonymsModel->setSynonyms('universe,cosmos')->setWebsiteId(0)->save();

$synonymsModel = $objectManager->create(SynonymReader::class);
$synonymsModel->setSynonyms('unix,linux')->setWebsiteId(0)->save();

$synonymsModel = $objectManager->create(SynonymReader::class);
$synonymsModel->setSynonyms('Great Britain,United Kingdom')->setWebsiteId(0)->save();

$synonymsModel = $objectManager->create(SynonymReader::class);
$synonymsModel->setSynonyms('big,huge,large,enormous')->setWebsiteId(0)->save();
