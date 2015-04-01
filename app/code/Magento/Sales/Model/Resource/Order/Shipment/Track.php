<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Model\Resource\Order\Shipment;

use Magento\Sales\Model\Resource\EntityAbstract as SalesResource;
use Magento\Sales\Model\Resource\EntitySnapshot;
use Magento\Sales\Model\Spi\ShipmentTrackResourceInterface;

/**
 * Flat sales order shipment comment resource
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Track extends SalesResource implements ShipmentTrackResourceInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_order_shipment_track_resource';

    /**
     * Validator
     *
     * @var \Magento\Sales\Model\Order\Shipment\Track\Validator
     */
    protected $validator;

    /**
     * @param \Magento\Framework\Model\Resource\Db\Context $context
     * @param \Magento\Sales\Model\Resource\Attribute $attribute
     * @param \Magento\SalesSequence\Model\Manager $sequenceManager
     * @param EntitySnapshot $entitySnapshot
     * @param \Magento\Sales\Model\Order\Shipment\Track\Validator $validator
     * @param null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\Resource\Db\Context $context,
        \Magento\Sales\Model\Resource\Attribute $attribute,
        \Magento\SalesSequence\Model\Manager $sequenceManager,
        EntitySnapshot $entitySnapshot,
        \Magento\Sales\Model\Order\Shipment\Track\Validator $validator,
        $resourcePrefix = null
    ) {
        $this->validator = $validator;
        parent::__construct($context, $attribute, $sequenceManager, $entitySnapshot, $resourcePrefix);
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_shipment_track', 'entity_id');
    }

    /**
     * Performs validation before save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var \Magento\Sales\Model\Order\Shipment\Track $object */
        if (!$object->getParentId() && $object->getShipment()) {
            $object->setParentId($object->getShipment()->getId());
        }

        parent::_beforeSave($object);
        $errors = $this->validator->validate($object);
        if (!empty($errors)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("Cannot save track:\n%1", implode("\n", $errors))
            );
        }

        return $this;
    }
}
