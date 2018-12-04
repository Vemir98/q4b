<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 15.03.2018
 * Time: 14:05
 */
class Pdf
{
    protected $pdfFile;

    protected $resolution = 144;

    protected $outputFormat = 'jpg';

    protected $page = 1;

    public $imagick;

    protected $numberOfPages;

    protected $validOutputFormats = ['jpg', 'jpeg', 'png'];

    protected $layerMethod = Imagick::LAYERMETHOD_FLATTEN;

    protected $colorspace;

    protected $compressionQuality;


    public function __construct($pdfFile)
    {
        if (!filter_var($pdfFile, FILTER_VALIDATE_URL) && !file_exists($pdfFile)) {
            throw new Exception('Pdf does not exists');
        }
        $this->imagick = new Imagick($pdfFile);
        $this->numberOfPages = $this->imagick->getNumberImages();

        $this->pdfFile = $pdfFile;
    }


    public function setResolution($resolution)
    {
        $this->resolution = $resolution;

        return $this;
    }


    public function setOutputFormat($outputFormat)
    {
        if (!$this->isValidOutputFormat($outputFormat)) {
            throw new Exception("Format {$outputFormat} is not supported");
        }

        $this->outputFormat = $outputFormat;

        return $this;
    }


    public function setLayerMethod($layerMethod)
    {
        if (
            is_int($layerMethod) === false &&
            is_null($layerMethod) === false
        ) {
            throw new Exception('LayerMethod must be an integer or null');
        }

        $this->layerMethod = $layerMethod;

        return $this;
    }


    public function isValidOutputFormat($outputFormat)
    {
        return in_array($outputFormat, $this->validOutputFormats);
    }


    public function setPage($page)
    {
        if ($page > $this->getNumberOfPages()) {
            throw new Exception("Page {$page} does not exist");
        }

        $this->page = $page;

        return $this;
    }


    public function getNumberOfPages()
    {
        return $this->numberOfPages;
    }


    public function saveImage($pathToImage)
    {
        $imageData = $this->getImageData($pathToImage);

        return file_put_contents($pathToImage, $imageData) !== false;
    }


    public function saveAllPagesAsImages($directory, $prefix = '')
    {
        $numberOfPages = $this->getNumberOfPages();

        if ($numberOfPages === 0) {
            return [];
        }

        return array_map(function ($pageNumber) use ($directory, $prefix) {
            $this->setPage($pageNumber);
            if(!empty($prefix) AND $pageNumber == 1){
                $destination = "{$directory}/{$prefix}.{$this->outputFormat}";
            }else{
                $destination = "{$directory}/{$prefix}{$pageNumber}.{$this->outputFormat}";
            }


            $this->saveImage($destination);

            return $destination;
        }, range(1, $numberOfPages));
    }


    public function getImageData($pathToImage)
    {
        $this->imagick = new Imagick();

        $this->imagick->setResolution($this->resolution, $this->resolution);

        if ($this->colorspace !== null) {
            $this->imagick->setColorspace($this->colorspace);
        }

        if ($this->compressionQuality !== null) {
            $this->imagick->setCompressionQuality($this->compressionQuality);
        }

        if (filter_var($this->pdfFile, FILTER_VALIDATE_URL)) {
            return $this->getRemoteImageData($pathToImage);
        }

        $this->imagick->readImage(sprintf('%s[%s]', $this->pdfFile, $this->page - 1));

        if (is_int($this->layerMethod)) {
            $this->imagick = $this->imagick->mergeImageLayers($this->layerMethod);
        }

        $this->imagick->setFormat($this->determineOutputFormat($pathToImage));

        return $this->imagick;
    }


    public function setColorspace($colorspace)
    {
        $this->colorspace = $colorspace;

        return $this;
    }


    public function setCompressionQuality($compressionQuality)
    {
        $this->compressionQuality = $compressionQuality;

        return $this;
    }


    protected function getRemoteImageData($pathToImage)
    {
        $this->imagick->readImage($this->pdfFile);

        $this->imagick->setIteratorIndex($this->page - 1);

        if (is_int($this->layerMethod)) {
            $this->imagick = $this->imagick->mergeImageLayers($this->layerMethod);
        }

        $this->imagick->setFormat($this->determineOutputFormat($pathToImage));

        return $this->imagick;
    }


    protected function determineOutputFormat($pathToImage)
    {
        $outputFormat = pathinfo($pathToImage, PATHINFO_EXTENSION);

        if ($this->outputFormat != '') {
            $outputFormat = $this->outputFormat;
        }

        $outputFormat = strtolower($outputFormat);

        if (!$this->isValidOutputFormat($outputFormat)) {
            $outputFormat = 'jpg';
        }

        return $outputFormat;
    }
}

$pdf = new Pdf(__DIR__.'/1.pdf');
$pdf->setCompressionQuality(30);
var_dump($pdf->saveAllPagesAsImages(__DIR__.'/result','sdf-'));