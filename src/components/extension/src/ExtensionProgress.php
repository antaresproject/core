<?php

declare(strict_types=1);

namespace Antares\Extension;

use Antares\Installation\Progress;

class ExtensionProgress extends Progress {

    /**
     * File path name.
     *
     * @var string
     */
    protected $filePathName = 'extension-operation.txt';

}
