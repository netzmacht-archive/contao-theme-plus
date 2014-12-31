<?php

/**
 * Class upgrade_version5_0_0
 */
class upgrade_version5_0_0
{
    public function run()
    {
        $assetsCachePath = implode(DIRECTORY_SEPARATOR, [TL_ROOT, 'system', 'cache', 'assets']);

        if (!is_dir($assetsCachePath)) {
            mkdir($assetsCachePath, 0777, true);
        }
    }
}

$upgrade_version5_0_0 = new upgrade_version5_0_0();
$upgrade_version5_0_0->run();
