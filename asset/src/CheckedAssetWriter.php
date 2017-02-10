<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Asset;

use Assetic\AssetWriter;
use Assetic\Asset\AssetInterface;
use Assetic\Util\VarUtils;

/**
 * Writes assets to the filesystem.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class CheckedAssetWriter extends AssetWriter
{

    private $dir;
    private $values;

    /**
     * Constructor.
     *
     * @param string $dir    The base web directory
     * @param array  $values Variable values
     *
     * @throws \InvalidArgumentException if a variable value is not a string
     */
    public function __construct($dir, array $values = array())
    {
        foreach ($values as $var => $vals) {
            foreach ($vals as $value) {
                if (!is_string($value)) {
                    throw new \InvalidArgumentException(sprintf('All variable values must be strings, but got %s for variable "%s".', json_encode($value), $var));
                }
            }
        }
        $this->dir    = $dir;
        $this->values = $values;
    }

    public function writeAsset(AssetInterface $asset)
    {
        foreach (VarUtils::getCombinations($asset->getVars(), $this->values) as $combination) {
            $asset->setValues($combination);
            $path = $this->dir . '/' . VarUtils::resolve(
                            $asset->getTargetPath(), $asset->getVars(), $asset->getValues()
            );
            if (!is_dir($path) && (!file_exists($path) || filemtime($path) <= $asset->getLastModified())) {
                static::write(
                        $path, $asset->dump()
                );
            }
        }
    }

}
