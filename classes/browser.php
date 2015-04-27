<?php
namespace Locomo;
/**
 * ブラウザをゆるく判別
 * thx https://gist.github.com/takahashi-yuki/4667353
 * @version 1.2.2
 */

class Browser
{

	/**
	 * IEのバージョンを整数値で取得する
	 *
	 * @return int IEならば1以上の整数値、そうでなければ0
	 */
	public static function getIEVersion()
	{
		if(isset($_SERVER['HTTP_USER_AGENT']) and stristr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
			preg_match('/MSIE\s([\d.]+)/i', $_SERVER['HTTP_USER_AGENT'], $ver);
			$ver = @floor($ver[1]);
		} elseif (isset($_SERVER['HTTP_USER_AGENT']) and stristr($_SERVER['HTTP_USER_AGENT'], "Trident")){
			preg_match('/rv\:([\d.]+)/i', $_SERVER['HTTP_USER_AGENT'], $ver);
			$ver = $ver[1];
		} else {
			$ver = 0;
		}
		return (int) $ver;
	}

	/**
	 * ブラウザのタイプを取得する
	 *
	 * @return string
	 * @link http://developer.wordpress.org/reference/functions/wp_is_mobile/
	 */
	public static function getBrowserType()
	{
		$type = 'legacy';

		if(isset($_SERVER['HTTP_USER_AGENT']))
		{
			if(self::getIEVersion() >= 10)
			{
				$type = 'modern';
			} else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false)
			{
				$type = 'mobile';
			} else if(strpos($_SERVER['HTTP_USER_AGENT'], 'bot') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'spider') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'archiver') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'Google') !== false
			       || strpos($_SERVER['HTTP_USER_AGENT'], 'Yahoo') !== false)
			{
				$type = 'robot';
			} else if(isset($_SERVER['HTTP_ACCEPT'])) {
				if(
						strpos($_SERVER['HTTP_ACCEPT'], 'application/xml') !== false
						|| strpos($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') !== false)
				{
					$type = 'modern';
				}
			}
		}

		return (string) $type;
	}

	/**
	 * モダンブラウザかどうかの判定
	 *
	 * @return bool モダンブラウザならtrue、そうでなければfalse
	 */
	public static function isModernBrowser()
	{
		return (bool) (self::getBrowserType() === 'modern');
	}

	/**
	 * レガシーブラウザかどうかの判定
	 *
	 * @return bool レガシーブラウザならtrue、そうでなければfalse
	 */
	public static function isLegacyBrowser()
	{
		return (bool) (self::getBrowserType() === 'legacy');
	}

	/**
	 * スマートフォンやタブレットなどのモバイル端末かどうかを判定する
	 *
	 * @return bool モバイル端末ならばtrue、そうでなければfalse
	 */
	public static function isMobile()
	{
		return (bool) (self::getBrowserType() === 'mobile');
	}

	/**
	 * ボットかどうかを判定する
	 *
	 * @return bool ボットならばtrue、そうでなければfalse
	 */
	public static function isBot()
	{
		return (bool) (self::getBrowserType() === 'robot');
	}

}

?>
