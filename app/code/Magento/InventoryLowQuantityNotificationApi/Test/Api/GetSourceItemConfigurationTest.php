<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventoryLowQuantityNotificationApi\Test\Api;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Webapi\Rest\Request;
use Magento\InventoryLowQuantityNotificationApi\Api\Data\SourceItemConfigurationInterface;
use Magento\TestFramework\TestCase\WebapiAbstract;

class GetSourceItemConfigurationTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/inventory/low-quantity-notification';
    const SERVICE_NAME = 'inventoryLowQuantityNotificationApiGetSourceItemConfigurationV1';

    /**
     * @magentoApiDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/sources.php
     * @magentoApiDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/source_items.php
     * @magentoApiDataFixture ../../../../app/code/Magento/InventoryLowQuantityNotificationApi/Test/_files/source_item_configuration.php
     */
    public function testGetSourceItemConfiguration()
    {
        $sourceCode = 'eu-1';
        $sku = 'SKU-1';

        $sourceItemConfiguration = $this->getSourceItemConfiguration($sourceCode, $sku);

        self::assertInternalType('array', $sourceItemConfiguration);
        self::assertNotEmpty($sourceItemConfiguration);

        self::assertEquals($sourceCode, $sourceItemConfiguration[SourceItemConfigurationInterface::SOURCE_CODE]);
        self::assertEquals($sku, $sourceItemConfiguration[SourceItemConfigurationInterface::SKU]);
        self::assertEquals(5.6, $sourceItemConfiguration[SourceItemConfigurationInterface::INVENTORY_NOTIFY_QTY]);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/sources.php
     * @magentoApiDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/source_items.php
     * @magentoApiDataFixture ../../../../app/code/Magento/InventoryLowQuantityNotificationApi/Test/_files/source_item_configuration.php
     */
    public function testGetSourceItemConfigurationNonExistingSku()
    {
        $sourceCode = 'eu-1';
        $sku = 'NO-Existing';
        self::throwException(new InputException(__('Sku %1 doesnt exits.', $sku)));
        $this->getSourceItemConfiguration($sourceCode, $sku);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/sources.php
     * @magentoApiDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/source_items.php
     * @magentoApiDataFixture ../../../../app/code/Magento/InventoryLowQuantityNotificationApi/Test/_files/source_item_configuration.php
     */
    public function testGetSourceItemConfigurationNonExistingSourceCode()
    {
        $sourceCode = 'NO-Existing';
        $sku = 'SKU-1';
        self::throwException(new InputException(__('Source code %1 doesnt exits.', $sourceCode)));
        $this->getSourceItemConfiguration($sourceCode, $sku);
    }

    /**
     * @param string $sourceCode
     * @param string $sku
     * @return array
     */
    private function getSourceItemConfiguration(string $sourceCode, string $sku)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $sourceCode . '/' . $sku,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME_GET,
                'operation' => self::SERVICE_NAME_GET . 'Execute',
            ],
        ];
        $sourceItemConfiguration = (TESTS_WEB_API_ADAPTER === self::ADAPTER_REST)
            ? $this->_webApiCall($serviceInfo)
            : $this->_webApiCall($serviceInfo, ['sourceCode' => $sourceCode, 'sku' => $sku]);

        self::assertInternalType('array', $sourceItemConfiguration);
        self::assertNotEmpty($sourceItemConfiguration);

        return $sourceItemConfiguration;
    }
}
