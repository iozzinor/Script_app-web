<?php
    class Language
    {
		/**
		 * The suppported languages.
		 */
		private static $supported_languages_ = array(
			"fr" => "fr_FR",
			"en" => "en_US"
		);

		/**
		 * Whether the languages folder have been loaded.
		 */
		private static $initialized_ = false;

		/**
		 * The current language name.
		 */
		private static $language_name_;
		
		/**
		 * @return bool Whether language has been updated.
		 */
		public static function set_language($short_name)
		{
			if (!self::$initialized_)
			{
				self::initialize();
			}

			$directory = __DIR__ . '/Locale';
			$locale = 'fr_FR';

			/*putenv('LANG=' . $locale);
			setlocale(LC_ALL, $locale);
			bindtextdomain('messages', $directory);
			bindtextdomain('common', $directory);
			textdomain('messages');

			print(_('first_test') . '<br />');
			print(dgettext('common', 'welcome') . '<br />');*/
		}

		public static function get_text($first, $second = null)
		{
			if ($second != null && is_string($first) && is_string($second))
			{
				return dgettext($first, $second);
			}
			else if (is_string($first))
			{
				return _($first);
			}
			return $first;
		}
	}

?>