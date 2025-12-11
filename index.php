<?php

require_once __DIR__ . '/classes/CheckFiles.php';

use Scottboms\Check\CheckFiles;
use Composer\Semver\Semver;
use Kirby\Cms\App;
use Kirby\Toolkit\F;

// shamelessly borrowed from distantnative/retour-for-kirby
if (
	version_compare(App::version() ?? '0.0.0', '5.0.0', '<') === true ||
	version_compare(App::version() ?? '6.0.0', '7.0.0', '>=') === true
) {
	throw new Exception('Template Checker requires Kirby v6 or v7');
}

Kirby::plugin(
	name: 'scottboms/template-checker',
	info: [
		'homepage' => 'https://github.com/scottboms/template-checker',
		'license'  => 'MIT'
	],
	version: '1.2.2',
	extends: [
	'options' => [],
	'areas' => [
		'checker' => function ($kirby) {
			return [
				'label' => 'Check Images',
				'icon'  => 'images',
				'breadcrumbLabel' => function() {
					return 'Check Images';
				},
				'menu'  => true,
				'link' => 'checker/images',
				'views' => [
					[
						'pattern' => 'checker/images',
						'action'  => function () {
							$user = kirby()->user();
							//$checker = new CheckFiles();
							//$images = $checker->findImagesWithoutContentOrTemplate();
							//$templates = $checker->availableFileTemplates();

							// dump($templates); // DEBUG

							return [
								'component' => 'k-image-template-checker',
								'title' => 'Images Without Template',
								'props' => [
									'images' => (new CheckFiles())->findImagesWithoutContentOrTemplate(),
									'availableTemplates' => (new CheckFiles())->availableFileTemplates()
								]
							];
						}
					]
				]
			];
		}
	],
	'api' => [
		'routes' => [
			[
				'pattern'   => 'checker/assign',
					'method'  => 'POST',
					'auth'    => true,
					'action'  => function () {

						//$logger = new CheckFiles();

						// get values submitted from the drawer component
						$key = get('key');
						$template = get('template');
						$alt = get('alt');

						//$logger->log('debug', 'values: Key ' . $key . ' Template: ' . $template . 'Alt: ' . $alt);

					  if (!$file = $key ? $file = site()->file($key) ?? page()->file($key) : null) {
							throw new Exception('File not found', 404);
						}
						$updates = []; // create array of values to update

						if ($template) {
							$updates['template'] = $template;
						}

						if ($alt !== null) {
							$updates['alt'] = $alt;
						}

						// try performing updates...
						try {
							$file->update($updates);
							return [
								'status' => 'ok',
								'message' => 'File updated',
							];
						} catch (Exception $e) {
							throw new Exception('Update failed: ' . $e->getMessage(), 500);
						}
					}
			]
		]
	]
]);
