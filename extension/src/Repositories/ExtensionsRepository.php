<?php

declare(strict_types=1);

namespace Antares\Extension\Repositories;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Model\ExtensionModel;

class ExtensionsRepository {

	/**
	 * Returns the collection of stored packages.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|ExtensionModel[]
	 */
	public function all() {
		return ExtensionModel::all();
	}

	/**
	 * Create or update the package.
	 *
	 * @param ExtensionContract $extension
	 * @param array $attributes
	 * @return ExtensionModel
	 */
	public function save(ExtensionContract $extension, array $attributes = []) : ExtensionModel {
		$identity = [
			'vendor'    => $extension->getVendorName(),
			'name'      => $extension->getPackageName(),
		];

		return ExtensionModel::updateOrCreate($identity, $attributes);
	}

}
