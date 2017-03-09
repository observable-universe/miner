<?php

class Elementifier {
	public $constants, $vowels;

	public function __construct() {

		// letter => frequency (less 1-10 more)
		$this->consonants = [
			'b' => 2,
			'c'	=> 3,
			'd'	=> 4,
			'f' => 2,
			'g' => 2,
			'h'	=> 6,
			'j'	=> 1,
			'k' => 1,
			'l' => 4,
			'm' => 3,
			'n' => 7,
			'p' => 2,
			'q' => [
				'weight' => 1,
				'rules' => [
					0 => function ($prev, $curr) {
						return $curr.'u';
					}
				]
			],
			'r' => 6,
			's' => 6,
			't' => 9,
			'v' => 1,
			'w' => 2,
			'x' => 1,
			'z' => 1
		];

		$this->vowels = [
			'a' => 8,
			'e' => 10,
			'i'	=> 7,
			'o'	=> 8,
			'u' => 3,
			'y' => 2
		];

		$this->realElements = [
			'Aluminum',
			'Copper',
			'Gold',
			'Iron',
			'Nickel',
			'Platinum',
			'Silver',
			'Tin',
			'Zinc'
		];

		$this->suffixes = [
			'ium',
			'um',
			'er',
			'on',
			'el',
			'in',
			'ine',
			'ese'
		];
	}

	private function getSuffix() {
		$i = rand(0, count($this->suffixes)-1);
		return $this->suffixes[$i];
	}

	public function getRoot($prefix, $suffix) {
		$lengthRange = [2,3,4];

		$likelyhoodToBeginInVowel = 0.125;
		$likelyhoodToEndInVowel = 0.75;

		$chosenLengthIdx = rand(0, count($lengthRange) - 1);
		$chosenLength = $lengthRange[$chosenLengthIdx];

		// chose whether or not to use vowels in the beginning or the end
		$beginInVowel = $this->randDecimal() >= $likelyhoodToBeginInVowel;
		$endInVowel = $this->randDecimal() >= $likelyhoodToEndInVowel;

		$prevChar = false;	

		$chosenCharacters = [];
			
		// get the last letter of the prefix
		$prefixLastChar = str_split($prefix)[strlen($prefix)-1];
		// get the first letter of the suffix
		$suffixFirstChar = str_split($suffix)[0];

		for ($i = 0; $i < $chosenLength-1; $i++) {
			$prevChar = $i == 0
				? $prefixLastChar
				: $chosenCharacters[$i-1];

			// make sure two vowels don't exist in a row
			$chosenChar = $this->isVowel($prevChar)
				? $this->randConsonant($prevChar)
				: $this->randVowel();

			// if a duplicate is found as the prev. char., go again
			if ($prevChar == $chosenChar) {
				$i--;
			} else {
				$chosenCharacters[$i] = $chosenChar;
			}
		}
		// ensure we end vowel - constant combination
		$potentialEndChar = $this->isVowel($suffixFirstChar)
			? $this->randConsonant($prevChar)
			: $this->randVowel();

		// inject a vowel before the last two consonants
		$chosenCharacters[] = $this->isVowel($prevChar)
			? $this->randVowel().$potentialEndChar
			: $potentialEndChar;

		return $chosenCharacters;
	}

	private function isVowel($char) {
		return isset($this->vowels[$char]);
	}

	private function randVowel() {
		if (!isset($this->vowelsAvail)) {
			$this->vowelsAvail = []; // generate a temp array of vowels with the required frequency for each
			foreach ($this->vowels as $vowel => $vowelWeight) {
				for ($i = 0; $i < $vowelWeight; $i++) {
					$this->vowelsAvail[] = $vowel;
				}
			}
		}

		$i = rand(0, count($this->vowelsAvail) - 1);
		return $this->vowelsAvail[$i];
	}

	private function randConsonant($prev) {
		if (!isset($this->consonantsAvail)) {
			$this->consonantsAvail = []; // generate a temp array of consonants with the required frequency for each
			foreach ($this->consonants as $consonant => $consonantSpec) {
				$consonantWeight = !is_array($consonantSpec)
					? $consonantSpec
					: $consonantSpec['weight'];

				for ($i = 0; $i < $consonantWeight; $i++) {
					$this->consonantsAvail[] = $consonant;
				}

			}
		}

		$i = rand(0, count($this->consonantsAvail) - 1);
		$chosenChar = $this->consonantsAvail[$i];

		// execute rules (callbacks) if available
		if (!empty($this->consonants[$chosenChar]['rules'])) {
			foreach ($this->consonants[$chosenChar]['rules'] as $ruleId => $callback) {
				$chosenChar = call_user_func_array($callback, [$prev, $this->consonants[$i]]);
			}
		}
		
		return $chosenChar;
	}

	private function randDecimal($min = 0, $max = 1) {
		return rand ($min*10, $max*10) / 10;
	}

	private function randomKey(array $array)
	{
	    return $this->randomElement(array_keys($array));
	}

	private function randomElement(array $array)
	{
	    if (count($array) === 0)
	    {
	        trigger_error('Array is empty.',  E_USER_WARNING);
	        return null;
	    }

	    $rand = mt_rand(0, count($array) - 1);
	    $array_keys = array_keys($array);
	    
	    return $array[$array_keys[$rand]];
	}

	public function generateWordsForElements() {
		$generated = [];
		foreach ($this->realElements as $element) {
			$prefixArr = str_split($element, 4);
			$prefix = ucfirst($prefixArr[0]);
			for ($i=0;$i<10;$i++){
				$suffix = $this->getSuffix();
				$generated[$element][$i] = $prefix.implode('',$this->getRoot($prefix, $suffix)).$suffix;
			}
		}
		return $generated;
	}
}

$elementifier = new Elementifier();
var_dump($elementifier->generateWordsForElements());
