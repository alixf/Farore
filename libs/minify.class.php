<?php

class Minify
{
	public static function apply($input, $type)
	{
		switch ($type)
		{
			case self::HTML :
				return self::minifyHTML($input);
			case self::CSS :
				return self::minifyCSS($input);
			case self::JS :
				return self::minifyJS($input);
			default :
				return $input;
		}
	}

	/**
	 * Minify an HTML string
	 *
	 * @param string $input An HTML string.
	 */
	public static function minifyHTML($input)
	{
		/*		 * ***** La balise <style> ***** */
		$style = array();
		$style_min = array();
		$style_code = array();
		$style_exist = false;

		if (preg_match_all('#<style[^>]*>(.*)</style[^>]>#isU', $input, $style))
		{
			$style_exist = true;
			$style_code = $style[1];
			for ($i = 0; $i < count($style_code); $i++)
				$style_code[$i] = self::keygen('style', $i);

			$input = str_replace($style[1], $style_code, $input);

			for ($i = 0; $i < count($style_code); $i++)
				$style_min[] = self::minifyCSS($style[1][$i]); //FIXME test if key already exists in document
		}


		/*		 * ***** La balise <script> ***** */
		$script = array();
		$script_min = array();
		$script_code = array();
		$script_exist = false;

		if (preg_match_all('#<script[^>]*>(.*)</script[^>]>#isU', $input, $script))
		{
			$script_exist = true;
			$script_code = $script[1];
			for ($i = 0; $i < count($script_code); $i++)
				$script_code[$i] = self::keygen('script', $i);
			$input = str_replace($script[1], $script_code, $input); //FIXME test if key already exists in document

			for ($i = 0; $i < count($script_code); $i++)
				$script_min[] = self::minifyJS($script[1][$i]);
		}


		/*		 * ***** La balise <pre> ***** */
		$pre = array();
		$pre_min = array();
		$pre_code = array();
		$pre_exist = false;

		if (preg_match_all('#<pre[^>]*>(.*)</pre[^>]*>#isU', $input, $pre))
		{
			$pre_exist = true;
			$pre_code = $pre[1];
			for ($i = 0; $i < count($pre_code); $i++)
				$pre_code[$i] = self::keygen('pre', $i);

			$input = str_replace($pre[1], $pre_code, $input); //FIXME test if key already exists in document

			for ($i = 0; $i < count($pre_code); $i++)
				$pre_min[] = $pre[1][$i];
		}

		/*		 * ***** La balise <textarea> ***** */
		$textarea = array();
		$textarea_min = array();
		$textarea_code = array();
		$textarea_exist = false;

		if (preg_match_all('#<textarea[^>]*>(.*)</textarea[^>]*>#isU', $input, $textarea))
		{
			$textarea_exist = true;
			$textarea_code = $textarea[1];
			for ($i = 0; $i < count($textarea_code); $i++)
				$textarea_code[$i] = self::keygen('textarea', $i);

			$input = str_replace($textarea[1], $textarea_code, $input); //FIXME test if key already exists in document

			for ($i = 0; $i < count($textarea_code); $i++)
				$textarea_min[] = $textarea[1][$i];
		}


		/*		 * ***** Les autres balises ***** */
		$tags = array();
		$tags_code = array();
		$tags_min = array();

		if (preg_match_all('#(<[^>]+>)#isU', $input, $tags))
		{
			$tags_code = $tags[1];
			for ($i = 0; $i < count($tags_code); $i++)
				$tags_code[$i] = self::keygen('tag', $i);

			$input = str_replace($tags[1], $tags_code, $input); //FIXME test if key already exists in document

			for ($i = 0; $i < count($tags_code); $i++)
				$tags_min[] = self::minifyHTMLTag($tags[1][$i]);
		}


		/*		 * ***** Traitement du code HTML restant ***** */
		$input = self::minifyHTMLString($input);

		/*		 * ***** Remplacements des clés ***** */
		$input = str_replace($tags_code, $tags_min, $input);
		if ($style_exist)
			$input = str_replace($style_code, $style_min, $input);
		if ($script_exist)
			$input = str_replace($script_code, $script_min, $input);
		if ($pre_exist)
			$input = str_replace($pre_code, $pre_min, $input);
		if ($textarea_exist)
			$input = str_replace($textarea_code, $textarea_min, $input);
		return $input;
	}

	/**
	 * Minify a CSS string
	 *
	 * @param string $input A CSS string.
	 */
	public static function minifyCSS($input)
	{
		$input = preg_replace('#@charset "UTF-8";#isU', '', $input);
		$input = str_replace(array("\r", "\n"), '', $input);
		$input = preg_replace('#([^*/])\/\*([^*]|[*](?!/)){5,}\*\/([^*/])#Us', '$1$3', $input);
		$input = preg_replace('#\s*({|}|,|:|;)\s*#', '$1', $input);
		$input = str_replace(';}', '}', $input);
		$input = preg_replace('#(?=|})[^{}]+{}#', '', $input);
		$input = preg_replace('#[\s]+#', ' ', $input);
		$input = preg_replace('#/\*(.*)\*/#isU', '', $input);
		return $input;
	}

	/**
	 * Minify a JS string
	 *
	 * @param string $input A JS string.
	 */
	public static function minifyJS($input)
	{
		$output = '';
		// Supression des commentaires entre : /**/
		$input = preg_replace('#/\*.*\*/#isU', '', $input);
		$inQuotes = array();
		$noSpacesAround = '{}()[]<>|&!?:;,+-*/="\'';
		$input = preg_replace("#(\r\n|\r)#", "\n", $input);
		$inputs = str_split($input);
		$inputs_count = count($inputs);
		$prevChr = null;
		for ($i = 0; $i < $inputs_count; $i++)
		{
			$chr = $inputs[$i];
			$nextChr = ($i + 1 < $inputs_count) ? $inputs[$i + 1] : null;
			switch ($chr)
			{
				case '/':
					if (!count($inQuotes) && $nextChr == '*' && $inputs[$i + 2] != '@')
					{
						$i = 1 + strpos($input, '*/', $i);
						continue 2;
					}
					elseif (!count($inQuotes) && $nextChr == '/')
					{
						$i = strpos($input, "\n", $i);
						continue 2;
					}
					elseif (!count($inQuotes))
					{
						// C'est peut-être le début d'une RegExp
						$eolPos = strpos($input, "\n", $i);
						if ($eolPos === false)
							$eolPos = $inputs_count;
						$eol = substr($input, $i, $eolPos - $i);
						$matches = array();
						if (!preg_match('#^(/.+(?<=\\\/)/(?!/)[gim]*)[^gim]#U', $eol, $matches))
							preg_match('#^(/.+(?<!/)/(?!/)[gim]*)[^gim]#U', $eol, $matches);
						if (isset($matches[1]))
						{
							// C'est bien une RegExp, on la retourne telle quelle
							$output .= $matches[1];
							$i += strlen($matches[1]) - 1;
							continue 2;
						}
					}
					break;
				case "'":
				case '"':
					if ($prevChr != '\\' || ($prevChr == '\\' && $inputs[$i - 2] == '\\'))
					{
						if (end($inQuotes) == $chr)
							array_pop($inQuotes);
						elseif (!count($inQuotes))
							$inQuotes[] = $chr;
					}
					break;
				case ' ':
				case "\t":
				case "\n":
					if (!count($inQuotes))
					{
						if (strstr("{$noSpacesAround} \t\n", $nextChr) || strstr("{$noSpacesAround} \t\n", $prevChr))
							continue 2;
						$chr = ' ';
					}
					break;
				default:
					break;
			}
			$output .= $chr;
			$prevChr = $chr;
		}
		$output = trim($output);
		$output = str_replace(';}', '}', $output);
		//Pour éviter le bug des "this"
		$output = str_replace('}this.', '};this.', $output);
		return $output;
	}

	/**
	 * Minify an HTML tag
	 *
	 * @param string $input An HTML tag.
	 */
	public static function minifyHTMLTag($input)
	{
		$quotes_raw = array();
		$quotes_mod = array();
		preg_match_all('#("[^"]+")#isU', $input, $quotes_raw);
		$input = preg_replace('#\s#is', ' ', $input);
		$input = preg_replace('# {2,}#is', ' ', $input);
		$input = str_replace(' />', '/>', $input);
		$input = str_replace(' >', '>', $input);
		$input = preg_replace('# ?= ?#', '=', $input);
		preg_match_all('#("[^"]+")#isU', $input, $quotes_mod);
		$input = str_replace($quotes_mod[1], $quotes_raw[1], $input);
		$style_raw = array();
		if (preg_match_all('#style="([^"]*)"#isU', $input, $style_raw))
		{
			$style_min = self::minifyCSS($style_raw[1][0]);
			$input = str_replace($style_raw[1][0], $style_min, $input);
		}
		return $input;
	}

	/**
	 * Minify an HTML-related string
	 *
	 * @param string $input An HTML-related string.
	 */
	public static function minifyHTMLString($input)
	{
		$input = preg_replace('#\r#is', '', $input);
		$input = preg_replace('#\t#is', '', $input);
		$input = preg_replace('#\n#is', '', $input);
		$input = preg_replace('# {2,}#is', ' ', $input);
		return $input;
	}

	/**
	 * Generate a random code
	 *
	 * @param string $type The type.
	 * @param int $type The rank.
	 */
	public static function keygen($type, $rank)
	{
		return '#@' . strtoupper($type) . '@' . rand() % 100 . '@' . $rank . '#';
	}

	// Minify types
	const NONE = 0;
	const HTML = 1;
	const CSS = 2;
	const JS = 3;

}

?>