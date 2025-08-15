<?php
namespace Pelmered\FilamentMoneyField;

use Composer\InstalledVersions;

class Helper
{
    public static function isFilament3(): bool
    {
        $filamentVersion = InstalledVersions::getVersion('filament/filament');

        return version_compare($filamentVersion, '4.0.0.0', '<=');
    }
}
