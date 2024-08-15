<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Intervention\Image\Facades\Image;

class ImageDimension implements Rule
{
    protected $minWidth;
    protected $maxWidth;
    protected $minHeight;
    protected $maxHeight;
    protected $originalFileName;
    protected $fileName;

    public function __construct($minWidth, $maxWidth, $minHeight, $maxHeight, $originalFileName)
    {
        $this->minWidth = $minWidth;
        $this->maxWidth = $maxWidth;
        $this->minHeight = $minHeight;
        $this->maxHeight = $maxHeight;
        $this->originalFileName = $originalFileName;
    }

    public function passes($attribute, $value)
    {
        $this->fileName = ($this->originalFileName)($attribute, $value);
        $image = Image::make($value);
        $width = $image->width();
        $height = $image->height();

        return ($width >= $this->minWidth && $width <= $this->maxWidth &&
                $height >= $this->minHeight && $height <= $this->maxHeight);
    }

    public function message()
    {
        return "Las dimensiones del Archivo '$this->fileName' deben ser entre $this->minWidth x $this->minHeight píxeles y $this->maxWidth x $this->maxHeight píxeles.";
    }
}
