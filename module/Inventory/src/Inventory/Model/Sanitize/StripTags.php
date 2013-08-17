<?php

namespace Inventory\Model\Sanitize;

class StripTags
{
    public function doClean($obj)
    {
        if ((is_array($obj)) || (is_object($obj))) {
            foreach($obj as $key => $value) {
                if ((is_array($obj)) || (is_object($obj))) {
                    $obj[$key] = $this->cleaner($value);
                }
                $obj[$key] = $this->cleaner($value);
            }
        } else {
            $obj = $this->cleaner($obj);
        }
        return $obj;
    }

	private function cleaner($str)
	{
		return $this->strip_word_html($this->html2txt(strip_tags($str)));
	}

	private function html2txt($document){
		$search = array('@<script[^>]*?>.*?</script>@si',
						'@<[\/\!]*?[^<>]*?>@si',
						'@<style[^>]*?>.*?</style>@siU',
						'@<![\s\S]*?--[ \t\n\r]*>@');
		return preg_replace($search, '', $document);
	}

	private function strip_word_html($text, $allowed_tags = '')
    {
        mb_regex_encoding('UTF-8');

        $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
        $replace = array('\'', '\'', '"', '"', '-');
        $text = preg_replace($search, $replace, $text);

        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        if(mb_stripos($text, '/*') !== FALSE){
            $text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
        }

        $text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text);
        $text = strip_tags($text, $allowed_tags);

        $text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text);

        $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
        $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
        $text = preg_replace($search, $replace, $text);

        $num_matches = preg_match_all("/\<!--/u", $text, $matches);
        if($num_matches){
              $text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
        }
        return $text;
    }
}
