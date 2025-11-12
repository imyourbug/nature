<?php

namespace App\Constant;

class GlobalConstant
{
    /**
     * Vendor status constants
     */
    public const VENDOR_STATUS_ACTIVE = 'active';
    public const VENDOR_STATUS_INACTIVE = 'inactive';
    public const VENDOR_STATUS_SUSPENDED = 'suspended';

    /**
     * Product status constants
     */
    public const PRODUCT_STATUS_ACTIVE = 'active';
    public const PRODUCT_STATUS_INACTIVE = 'inactive';
    public const PRODUCT_STATUS_OUT_OF_STOCK = 'out_of_stock';

    /**
     * RFQ status constants
     */
    public const RFQ_STATUS_PENDING = 'pending';
    public const RFQ_STATUS_ACCEPTED = 'accepted';
    public const RFQ_STATUS_REJECTED = 'rejected';

    /**
     * Get all vendor statuses
     *
     * @return array
     */
    public static function getVendorStatuses(): array
    {
        return [
            self::VENDOR_STATUS_ACTIVE,
            self::VENDOR_STATUS_INACTIVE,
            self::VENDOR_STATUS_SUSPENDED,
        ];
    }

    /**
     * Get all product statuses
     *
     * @return array
     */
    public static function getProductStatuses(): array
    {
        return [
            self::PRODUCT_STATUS_ACTIVE,
            self::PRODUCT_STATUS_INACTIVE,
            self::PRODUCT_STATUS_OUT_OF_STOCK,
        ];
    }

    /**
     * Get all RFQ statuses
     *
     * @return array
     */
    public static function getRFQStatuses(): array
    {
        return [
            self::RFQ_STATUS_PENDING,
            self::RFQ_STATUS_ACCEPTED,
            self::RFQ_STATUS_REJECTED,
        ];
    }
}
