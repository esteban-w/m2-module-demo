<?php declare(strict_types=1);

namespace EW\Core\Setup\Patch;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

abstract class AbstractDataPatch implements DataPatchInterface
{

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        protected ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    abstract public function apply(): void;
}
