<?php

namespace Scottboms\Check;

use Kirby\Form\Field\DisplayField;
use Kirby\Reflection\Attributes\Prop;

class ThumbnailField extends DisplayField
{
	#[Prop]
	protected ?string $src = null;

	#[Prop]
	protected ?string $alt = null;

	#[Prop]
	protected ?string $filename = null;

	#[Prop]
	protected ?string $size = null;
}