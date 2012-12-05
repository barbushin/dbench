<?php

define('PCHART_BASE_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PChart');
define('PCHART_FONTS_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PChart' . DIRECTORY_SEPARATOR . 'Fonts');

class DBench_Log_Chart {
	
	public $maxWidth = 1280;
	public $maxHeight = 800;
	public $graphMargins = array(60, 20, 200, 50);
	
	public $absciseMin;
	public $absciseMax;
	public $absciseStep;
	public $absciseValues;
	public $absciseStepsLimit;
	public $absciseStepTitlePixels = 20;
	public $ordinateDevisions = 10;
	public $chartTitle;
	public $absciseTitle;
	public $absciseStepTitle;
	public $ordinateStepTitle = 'ms';
	
	protected $outputFilepath;
	protected $lines;

	public function __construct($absciseMin, $absciseMax, $absciseStep, $outputFilepath) {
		$this->absciseMin = $absciseMin;
		$this->absciseMax = $absciseMax;
		$this->absciseStep = $absciseStep;
		$this->setOutputFilepath($outputFilepath);
	}

	public function setOutputFilepath($outputFilepath) {
		$outDir = dirname($outputFilepath);
		if(!is_writable($outDir)) {
			chmod($outDir, 0777);
			if(!is_writable($outDir)) {
				throw new Exception('Have no permissions to create file ' . $outputFilepath);
			}
		}
		if(is_file($outputFilepath)) {
			unlink($outputFilepath);
		}
		$this->outputFilepath = $outputFilepath;
	}

	public function setAbsciseValues(array $absciseValues) {
		$this->absciseValues = $absciseValues;
	}

	public function addLine($name, array $ordinateValues) {
		$this->lines[$name] = $ordinateValues;
	}

	protected function getMinOrdinateValue() {
		$min = 99999999;
		foreach($this->lines as $lineOrdinates) {
			$min = min($min, min($lineOrdinates));
		}
		return $min;
	}

	protected function getMaxOrdinateValue() {
		$max = 0;
		foreach($this->lines as $lineOrdinates) {
			$max = max($max, max($lineOrdinates));
		}
		return $max;
	}

	protected function getGraphWidth() {
		return $this->maxWidth - $this->graphMargins[0] - $this->graphMargins[2];
	}

	protected function getAbsciseStepsLimit() {
		if($this->absciseStepsLimit) {
			return $this->absciseStepsLimit;
		}
		else {
			return floor($this->getGraphWidth() / $this->absciseStepTitlePixels);
		}
	}

	public function build() {
		
		require_once (PCHART_BASE_DIR . DIRECTORY_SEPARATOR . 'pData.php');
		require_once (PCHART_BASE_DIR . DIRECTORY_SEPARATOR . 'pChart.php');
		
		$dataSet = new pData();
		
		foreach($this->lines as $name => $ordinateValues) {
			if(count($ordinateValues) != count($this->absciseValues)) {
				throw new Exception('Count of line "' . $name . '" ordinate points "' . count($ordinateValues) . '" mismatch to abscise points "' . count($this->absciseValues) . '"');
			}
			$dataSet->AddPoint($ordinateValues, $name);
		}
		$dataSet->AddPoint($this->absciseValues, 'Abscise');
		$dataSet->AddAllSeries();
		$dataSet->RemoveSerie('Abscise');
		$dataSet->SetAbsciseLabelSerie('Abscise');
		
		foreach($this->lines as $name => $ordinateValues) {
			$dataSet->SetSerieName($name, $name);
		}
		
		$dataSet->SetYAxisUnit($this->ordinateStepTitle);
		$dataSet->SetXAxisUnit($this->absciseStepTitle);
		
		$chart = new pChart($this->maxWidth, $this->maxHeight);
		$chart->drawGraphAreaGradient(132, 153, 172, 50, TARGET_BACKGROUND);
		
		// Graph area setup
		$chart->setFontProperties(PCHART_FONTS_DIR . DIRECTORY_SEPARATOR . 'tahoma.ttf', 10);
		$chart->setGraphArea($this->graphMargins[0], $this->graphMargins[1], $this->maxWidth - $this->graphMargins[2], $this->maxHeight - $this->graphMargins[3]);
		$chart->drawGraphArea(213, 217, 221, FALSE);
		
		$ordinateScaleMargin = ($this->getMaxOrdinateValue() - $this->getMinOrdinateValue()) / $this->ordinateDevisions;
		$chart->setFixedScale($this->getMinOrdinateValue() - $ordinateScaleMargin, $this->getMaxOrdinateValue() + $ordinateScaleMargin, $this->ordinateDevisions);
		$chart->drawScale($dataSet->GetData(), $dataSet->GetDataDescription(), SCALE_NORMAL, 213, 217, 221, TRUE, 0, 2);
		$chart->drawGraphAreaGradient(162, 183, 202, 50);
		$chart->drawGrid(4, TRUE, 230, 230, 230, 20);
		
		// Draw the line chart
		//		$chart->setShadowProperties(1, 1, 0, 0, 0, 30, 4);
		$chart->drawLineGraph($dataSet->GetData(), $dataSet->GetDataDescription());
		$chart->clearShadow();
		$chart->drawPlotGraph($dataSet->GetData(), $dataSet->GetDataDescription(), 5, 3, -1, -1, -1, TRUE);
		
		// Draw the legend
		$chart->drawLegend($this->maxWidth - $this->graphMargins[2] + 10, $this->graphMargins[1], $dataSet->GetDataDescription(), 236, 238, 240, 52, 58, 82);
		
		// Draw chart title
		if($this->chartTitle) {
			$chart->drawTextBox(0, $this->maxHeight - 20, $this->maxWidth, $this->maxHeight, $this->chartTitle, 0, 255, 255, 255, ALIGN_RIGHT, TRUE, 0, 0, 0, 30);
		}
		
		// Render the picture
		$chart->addBorder(2);
		$chart->Render($this->outputFilepath);
	}
}