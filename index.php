<?php

require_once __DIR__ . '/classes/CheckFiles.php';

use Scottboms\Check\CheckFiles;
use Scottboms\Check\ThumbnailField;
use Kirby\Cms\App as Kirby;

require_once __DIR__ . '/fields/ThumbnailField.php';

$version = Kirby::version() ?? '0.0.0';
if (
	version_compare($version, '6.0.0-beta.1', '<') ||
	version_compare($version, '7.0.0', '>=')
) {
	throw new Exception('Template Checker requires Kirby 6');
}

Kirby::plugin('scottboms/template-checker',
	info: [
		'homepage' => 'https://github.com/scottboms/template-checker',
		'license'  => 'MIT',
		'version'  => '6.0.0',
		'authors'  => [[ 'name' => 'Scott Boms']]
	],

	extends: [
	'options' => [],
	'fields' => [
		'thumbnail' => ThumbnailField::class
	],
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
							$checker = new CheckFiles();

							//$images = $checker->findImagesWithoutContentOrTemplate();
							//$templates = $checker->availableFileTemplates();
							// dump($templates); // DEBUG

							return [
								'component' => 'k-image-template-checker',
								'title' => 'Images Without Template',
								'props' => [
									'images' => $checker->findImagesWithoutContentOrTemplate(),
									'availableTemplates' => $checker->availableFileTemplates()
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
