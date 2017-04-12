<?php
class Elementifier {
	public $charsAvail;
	public static $chars, $realElements, $suffixes, $charFreqArr;

	public function __construct() {

		// letter => frequency (less 1-10 more)
		static::$chars['consonant'] = [
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
				'freq' => 1,
				'rules' => [
					0 => function ($iChar, $currChar, $wordChars) {
						// ensure a Q is trailed by a U
						if ($wordChars[$iChar+1] == 'u') {
							throw new CharValidationException("Consonant rule failed: 'Q is trailed by a U'");
						}
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
		static::$charFreqArr['consonant'] = static::generateCharFreqArr(static::$chars, 'consonant');

		static::$chars['vowel'] = [
			'a' => 8,
			'e' => [
				'freq' => 10,
				'rules' => [
					0 => function ($iChar, $currChar, $wordChars) {
						// ensure I before E except after C
						if ($wordChars[$iChar-1] != 'c' && $wordChars[$iChar+1] == 'i') {
							throw new CharValidationException("Vowel rule failed: 'I before E'");
						}
					}
				]
			],
			'i'	=> 7,
			'o'	=> 8,
			'u' => 3,
			'y' => 2
		];
		static::$charFreqArr['vowel'] = static::generateCharFreqArr(static::$chars, 'vowel');

		static::$realElements = [
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

		static::$suffixes = [
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
		$i = rand(0, count(static::$suffixes)-1);
		return static::$suffixes[$i];
	}

	public function getRoot($prefix, $suffix) {
		// $likelyhoodToBeginInVowel = 0.125;
		// $likelyhoodToEndInVowel = 0.75;

		// chose a random character length for the root among the available lengths
		$lengthRange = [2,3,4];
		$chosenLengthIdx = rand(0, count($lengthRange) - 1);
		$chosenLength = $lengthRange[$chosenLengthIdx];

		// chose whether or not to use vowels in the beginning or the end
		// $beginInVowel = static::randDecimal() >= $likelyhoodToBeginInVowel;
		// $endInVowel = static::randDecimal() >= $likelyhoodToEndInVowel;

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
			$chosenChar = static::isVowel($prevChar)
				? static::randConsonant($prevChar)
				: static::randVowel($prevChar);

			// if a duplicate is found as the prev. char., go again
			if ($prevChar == $chosenChar) {
				$i--;
			} else {
				$chosenCharacters[$i] = $chosenChar;
			}
		}

		// ensure we end in a vowel - constant combination
		$potentialEndChar = static::isVowel($suffixFirstChar)
			? static::randConsonant($prevChar)
			: static::randVowel($prevChar);

		// inject a vowel before the last two consonants
		$chosenCharacters[] = static::isVowel($prevChar)
			? static::randVowel($prevChar).$potentialEndChar
			: $potentialEndChar;

		return $chosenCharacters;
	}

	// validate the generated word against all rules
	// if validation passes... well yippee!
	// else fails... try again
	private function validateWord($word) {
		$wordChars = explode('', $word);

		foreach ($wordChars as $iChar => $currChar) {
			$charType = static::isVowel($currChar)
				? 'vowel'
				: 'constant';

			// execute rules (callbacks) if available
			if (!empty(static::$chars[$charType][$currChar]['rules'])) {
				try {
					foreach (static::$chars[$charType][$currChar]['rules'] as $ruleId => $callback) {
						call_user_func_array($callback, [$iChar, $currChar, $wordChars]);
					}
				} catch (CharValidationException $e) {
					echo $e->getMessage();
				}
			}
		}
		
	}

	private static function isVowel($char) {
		return isset(static::$chars['vowel'][$char]);
	}

	private static function randConsonant($prevChar) {
		return static::randChar('consonant', $prevChar);
	}

	private static function randVowel($prevChar) {
		return static::randChar('vowel', $prevChar);
	}

	private static function generateCharFreqArr($charArr, $type) {
		$charFreqArr = [];
		foreach ($charArr[$type] as $char => $charSpec) {
			$charFreq = !is_array($charSpec)
				? $charSpec
				: $charSpec['freq'];

			for ($i = 0; $i < $charFreq; $i++) {
				$charFreqArr[] = $char;
			}

		}

		return $charFreqArr;
	}

	private static function randChar($charType, $prev) {
		
		if (!isset(static::$charFreqArr[$charType])) {
			throw new DataMissingException("Missing frequency array for $charType");
		}

		// grab a random char from the frequency array
		$i = rand(0, count(static::$charFreqArr[$charType]) - 1);
		$chosenChar = static::$charFreqArr[$charType][$i];
		
		return $chosenChar;
	}

	private static function randDecimal($min = 0, $max = 1) {
		return rand ($min*10, $max*10) / 10;
	}

	private function randomKey(array $array)
	{
	    return static::randomElement(array_keys($array));
	}

	private static function randomElement(array $array)
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

	public static function grabElementPrefix($element, $chars = 4) {
		$prefixArr = str_split($element, $chars);
		return ucfirst($prefixArr[0]);
	}

	public function generateWordsForElements($count) {
		$generated = [];
		foreach (static::$realElements as $element) {
			
			$prefix = static::grabElementPrefix($element);
			for ($i=0; $i<$count; $i++){
				$suffix = $this->getSuffix();
			
				$generated[$element][$i] = $prefix.implode('',$this->getRoot($prefix, $suffix)).$suffix;
			}
		}
		return $generated;
	}
}

class CharValidationException extends Exception {

}
class DataMissingException extends Exception {

}

$elementifier = new Elementifier();
var_dump($elementifier->generateWordsForElements(10));
