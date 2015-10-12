<?php
/**
 * @author hackerone
 * @url https://github.com/hackerone/color
 */
class Color{

	private $_color, $_mode, $_hsl;

	/**
	*	darkens color by the given value p
	*	@return Color
	*/
	public function lighten($p){
		$color = $this->_hsl;
		$color[2] = $this->_add($color[2], $p);
		$this->_hsl = $color;
		return $this;
	}

	/**
	*	darkens color by the given value p
	*	@return Color
	*/
	public function darken($p){
		$color = $this->_hsl;
		$color[2] = $this->_subtract($color[2], $p);
		$this->_hsl = $color;
		return $this;
	}
	/**
	*	saturates color by the given value p
	*	@return Color
	*/
	public function saturate($p){
		$color = $this->_hsl;
		$color[1] = $this->_add($color[1], $p);
		$this->_hsl = $color;
		return $this;
	}
	/**
	*	desaturates color by the given value p
	*	@return Color
	*/
	public function desaturate($p){
		$color = $this->_hsl;
		$color[1] = $this->_subtract($color[1], $p);
		$this->_hsl = $color;
		return $this;
	}

	public function mix($color, $mode = null){
		if(!$mode){
			$mode = $this->_mode;
		}
		$rgb = $this->_getRGB($this->_getHSL($color, $mode));
		$self = $this->_getRGB();
		foreach($self as $k => &$s){
			$s = ($s+$rgb[$k]);
			if($s > 255)$s=255;
		}

		$this->_hsl = $this->_getHSL($self, 'rgb');
		return $this;
	}

	public function remove($color, $mode = null){
		if(!$mode){
			$mode = $this->_mode;
		}
		$rgb = $this->_getRGB($this->_getHSL($color, $mode));
		$self = $this->_getRGB();
		foreach($self as $k => &$s){
			$s = ($s-$rgb[$k]);
			if($s < 0)$s=0;
		}

		$this->_hsl = $this->_getHSL($self, 'rgb');
		return $this;
	}

	private function _add($number, $p){
		if($p > 1)$p/=100;

		$number *= 1+$p;
		return ($number > 1) ? 1 : round($number,2);
	}

	private function _subtract($number, $p){
		if($p > 1)$p/=100;
		$number *= 1-$p;
		return ($number < 0) ? 0 : round($number,2);
	}

	/**
	*	Returns the string/array representation of the color
	*	@return String/Array color
	*/
	public function __toString(){
		return $this->__get($this->_mode);
	}

	/**
	* Returns the Color object
	* @return Color
	*/
	public static function set($color, $mode = null){
		return new Color($color, $mode);
	}

	/**
	* Magic Function to output color in the correct mode
	* @return Array/String color
	**/
	public function __get($t){
		switch(strtolower($t)){
			case 'hsl' : return $this->_hsl;
			break;
			case 'rgb' : return $this->_getRGB();
			break;
			case 'hex' : return $this->_getHex();
		}
	}

	public function __construct($color = null, $mode = null){

		if($mode)$this->_mode = substr($mode,0,3);
		else $this->_mode = $this->_getMode($color);
		if($color){
			$this->_color = $color;
			$this->_hsl = $this->_getHSL($this->_color, $this->_mode);
		}

	}

	/**
	* @return Array HSL
	**/
	private function _getHSL($color, $mode){
		switch(strtolower($mode)){
			case 'rgb' :
			return $this->_rgb2hsl($color);
			break;

			case 'hex' :
			return $this->_hex2hsl($color);
			break;

			case 'hsl' :
			return $color;
			break;
		}
	}


	/**
	* @return String mode
	**/
	private function _getMode($color){
		if(is_array($color))
			return 'rgb';
		else
			return 'hex';
	}

	/**
	* @return Array RGB
	**/
	private function _getRGB($hsl = null){
		if(!$hsl)$hsl = $this->_hsl;
		return $this->_hsl2rgb($hsl);
	}

	/**
	* @return String Hex
	**/
	private function _getHex($hsl = null){
		if(!$hsl)$hsl = $this->_hsl;
		return '#'.$this->_hsl2hex($this->_hsl);
	}

	/**
	* @array color - rgb array
	* @return String  - hex representation of color
	**/
	private function _rgb2hex($rgb){
		if(is_array($rgb) && count($rgb) == 3){
			$output = '';
			foreach($rgb as $c){
				$s = dechex($c);
				if(strlen($s)<2)$s = '0'.$s;
				$output .= $s;
			}
		}
		return $output;
	}

	/**
	* @string hex - hex representation of color
	* @return Array rgb
	**/
	private function _hex2rgb($hex){
		if(substr($hex, 0,1) == '#')$hex = substr($hex,1);
		if(strlen($hex) == 3){
			$hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
		}
		if(strlen($hex) == 6 && ($dec = hexdec($hex))!== false){
			$out = array();
			for($x = 0; $x<3 ; $x ++){
				array_unshift($out, $dec % 256);
				$dec /= 256;
			}
			return $out;
		}else{
			throw new Exception('Invalid Hex color');
		}
	}


	/**
	* @string rgb - rgb array
	* @return Array hsl
	**/
	private function _rgb2hsl($rgb){
		// Where RGB values = 0 ÷ 255.
		$var_R = $rgb[0] / 255;
		$var_G = $rgb[1] / 255;
		$var_B = $rgb[2] / 255;

        // Min. value of RGB
		$var_Min = min($var_R, $var_G, $var_B);
        // Max. value of RGB
		$var_Max = max($var_R, $var_G, $var_B);
        // Delta RGB value
		$del_Max = $var_Max - $var_Min;

		$L = ($var_Max + $var_Min) / 2;

		if ( $del_Max == 0 ) {
            // This is a gray, no chroma...
            // HSL results = 0 ÷ 1
			$H = 0;
			$S = 0;
		} else {
            // Chromatic data...
			if ($L < 0.5) {
				$S = $del_Max / ($var_Max + $var_Min);
			} else {
				$S = $del_Max / ( 2 - $var_Max - $var_Min );
			}

			$del_R = ((($var_Max - $var_R) / 6) + ($del_Max / 2)) / $del_Max;
			$del_G = ((($var_Max - $var_G) / 6) + ($del_Max / 2)) / $del_Max;
			$del_B = ((($var_Max - $var_B) / 6) + ($del_Max / 2)) / $del_Max;

			if ($var_R == $var_Max) {
				$H = $del_B - $del_G;
			} else if ($var_G == $var_Max) {
				$H = ( 1 / 3 ) + $del_R - $del_B;
			} else if ($var_B == $var_Max) {
				$H = ( 2 / 3 ) + $del_G - $del_R;
			}

			if ($H < 0) {
				$H += 1;
			}
			if ($H > 1) {
				$H -= 1;
			}
		}

		return array(round($H*360), round($S,2), round($L,2));
	}
	/**
	* @string hsl - hsl array
	* @return Array rgb
	**/

	private function _hsl2rgb($hsl)
	{

		if(is_array($hsl) && count($hsl) == 3)list($h, $s, $l) = $hsl;
		else {
			throw new Exception('Not a valid HSL');
		}
		if($h>0)$h /= 360;
		if($s == 0)
		{
			$r = $l;
			$g = $l;
			$b = $l;
		}
		else
		{
			if($l < .5)
			{
				$t2 = $l * (1.0 + $s);
			}
			else
			{
				$t2 = ($l + $s) - ($l * $s);
			}
			$t1 = 2.0 * $l - $t2;

			$rt3 = $h + 1.0/3.0;
			$gt3 = $h;
			$bt3 = $h - 1.0/3.0;

			if($rt3 < 0) $rt3 += 1.0;
			if($rt3 > 1) $rt3 -= 1.0;
			if($gt3 < 0) $gt3 += 1.0;
			if($gt3 > 1) $gt3 -= 1.0;
			if($bt3 < 0) $bt3 += 1.0;
			if($bt3 > 1) $bt3 -= 1.0;

			if(6.0 * $rt3 < 1) $r = $t1 + ($t2 - $t1) * 6.0 * $rt3;
			elseif(2.0 * $rt3 < 1) $r = $t2;
			elseif(3.0 * $rt3 < 2) $r = $t1 + ($t2 - $t1) * ((2.0/3.0) - $rt3) * 6.0;
			else $r = $t1;

			if(6.0 * $gt3 < 1) $g = $t1 + ($t2 - $t1) * 6.0 * $gt3;
			elseif(2.0 * $gt3 < 1) $g = $t2;
			elseif(3.0 * $gt3 < 2) $g = $t1 + ($t2 - $t1) * ((2.0/3.0) - $gt3) * 6.0;
			else $g = $t1;

			if(6.0 * $bt3 < 1) $b = $t1 + ($t2 - $t1) * 6.0 * $bt3;
			elseif(2.0 * $bt3 < 1) $b = $t2;
			elseif(3.0 * $bt3 < 2) $b = $t1 + ($t2 - $t1) * ((2.0/3.0) - $bt3) * 6.0;
			else $b = $t1;
		}

		$r = (int)round(255.0 * $r);
		$g = (int)round(255.0 * $g);
		$b = (int)round(255.0 * $b);

		return array($r, $g, $b);
	}

	/**
	*	@return Array hsl
	*/
	private function _hex2hsl($hex){
		return $this->_rgb2hsl($this->_hex2rgb($hex));
	}

	/**
	*	@return Array hex
	*/
	private function _hsl2hex($hsl){
		return $this->_rgb2hex($this->_hsl2rgb($hsl));
	}

}
?>