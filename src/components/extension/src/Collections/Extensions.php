<?php

declare(strict_types = 1);

namespace Antares\Extension\Collections;

use Antares\Extension\Contracts\ExtensionContract;
use Illuminate\Support\Collection;

class Extensions extends Collection
{

    /**
     * Returns the first extension by the given name.
     *
     * @param string $name
     * @return ExtensionContract|null
     */
    public function quessByName(string $name)
    {
        return $this->first(function(ExtensionContract $extension) use($name) {
                    $package = $extension->getPackageName();
                    return (str_contains($package, 'module-')) ? $package === 'module-' . $name : $package === 'component-' . $name;
                });
    }

    /**
     * Returns the first extension by the given name.
     *
     * @param string $name
     * @return ExtensionContract|null
     */
    public function findByName(string $name)
    {
        return $this->first(function(ExtensionContract $extension) use($name) {
                    return $extension->getPackage()->getName() === $name;
                });
    }

    /**
     * Returns the first extension by the given vendor and name.
     *
     * @param string $vendor
     * @param string $name
     * @return ExtensionContract|null
     */
    public function findByVendorAndName(string $vendor, string $name)
    {
        $fullName = $vendor . '/' . $name;

        return $this->first(function(ExtensionContract $extension) use($fullName) {
                    return $extension->getPackage()->getName() === $fullName;
                });
    }

    /**
     * Returns the first extension by the file path.
     *
     * @param string $path
     * @return ExtensionContract|null
     */
    public function findByPath(string $path)
    {
        return $this->first(function(ExtensionContract $extension) use($path) {
                    return $extension->getPath() === $path;
                });
    }

    /**
     * Filter the collection by activated extensions.
     *
     * @return Extensions
     */
    public function filterByActivated(): Extensions
    {
        return $this->filter(function(ExtensionContract $extension) {
                    return $extension->isActivated();
                });
    }

}
