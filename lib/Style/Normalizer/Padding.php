<?php
declare(strict_types=1);
/**
 * Padding class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class Padding
 */
class Padding extends Normalizer
{
	/**
	 * @param array $matches
	 * @return array
	 */
	protected function oneValue(string $ruleValue)
	{
		$normalized = [];
		$matches = [];
		preg_match_all('/(([0-9]+)([a-z]+))/', $ruleValue, $matches);
		$values = $matches[2];
		$units = $matches[3];

		$originalSize = (float)$values[0];
		$originalUnit = $units[0];
		$computedValue = $this->document->convertUnits($originalUnit, $originalSize);
		$normalized['padding-top'] = $computedValue;
		$normalized['padding-bottom'] = $$computedValue;
		$normalized['padding-left'] = $$computedValue;
		$normalized['padding-right'] = $$computedValue;
		return $normalized;
	}

	/**
	 * @param array $matches
	 * @return array
	 */
	protected function twoValues(string $ruleValue)
	{
		$normalized = [];
		$matches = [];
		preg_match_all('/(([0-9]+)([a-z]+))/', $ruleValue, $matches);
		$values = $matches[2];
		$units = $matches[3];

		$originalSize = (float)$values[0];
		$originalUnit = $units[0];
		$computedValue = $this->document->convertUnits($originalUnit, $originalSize);
		$normalized['padding-top'] = $computedValue;
		$normalized['padding-bottom'] = $computedValue;

		$originalSize = (float)$values[1];
		$originalUnit = $units[1];
		$computedValue = $this->document->convertUnits($originalUnit, $originalSize);
		$normalized['padding-left'] = $computedValue;
		$normalized['padding-right'] = $computedValue;
		return $normalized;
	}

	/**
	 * @param array $matches
	 * @return array
	 */
	protected function threeValues(string $ruleValue)
	{
		$normalized = [];
		$matches = [];
		preg_match_all('/(([0-9]+)([a-z]+))/', $ruleValue, $matches);
		$values = $matches[2];
		$units = $matches[3];

		$originalSize = (float)$values[0];
		$originalUnit = $units[0];
		$normalized['padding-top'] = $this->document->convertUnits($originalUnit, $originalSize);

		$originalSize = (float)$values[1];
		$originalUnit = $units[1];
		$computedValue = $this->document->convertUnits($originalUnit, $originalSize);
		$normalized['padding-left'] = $computedValue;
		$normalized['padding-right'] = $computedValue;

		$originalSize = (float)$values[2];
		$originalUnit = $units[2];
		$normalized['padding-bottom'] = $this->document->convertUnits($originalUnit, $originalSize);
		return $normalized;
	}

	/**
	 * @param array $matches
	 * @return array
	 */
	protected function fourValues(string $ruleValue)
	{
		$normalized = [];
		$matches = [];
		preg_match_all('/(([0-9]+)([a-z]+))/', $ruleValue, $matches);
		$values = $matches[2];
		$units = $matches[3];

		$originalSize = (float)$values[0];
		$originalUnit = $units[0];
		$normalized['padding-top'] = $this->document->convertUnits($originalUnit, $originalSize);

		$originalSize = (float)$values[1];
		$originalUnit = $units[1];
		$normalized['padding-right'] = $this->document->convertUnits($originalUnit, $originalSize);

		$originalSize = (float)$values[2];
		$originalUnit = $units[2];
		$normalized['padding-bottom'] = $this->document->convertUnits($originalUnit, $originalSize);

		$originalSize = (float)$values[3];
		$originalUnit = $units[3];
		$normalized['padding-left'] = $this->document->convertUnits($originalUnit, $originalSize);
		return $normalized;
	}

	public function normalize(string $ruleValue): array
	{
		$matches = [];
		preg_match_all('/([0-9]+[a-z]+)/', $ruleValue, $matches);
		switch (count($matches[1])) {
			case 1:
				return $this->oneValue($ruleValue);
			case 2:
				return $this->twoValues($ruleValue);
			case 3:
				return $this->threeValues($ruleValue);
			case 4:
				return $this->fourValues($ruleValue);
		}
	}
}
