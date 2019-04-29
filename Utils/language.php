<?php
    class Language
    {
		/**
		 * The suppported languages.
		 */
		private static $supported_languages_ = array(
			"fr" => "fr_FR.utf8",
			"en" => "en_US"
		);

		private static $default_language_short_name_ = 'en';

		private static $locale_path_;

		/**
		 * List of domains.
		 */
		private static $domains_ = array(
			'messages',
			'common'
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
		 * The current domain.
		 */
		private static $domain_;

		private static function initialize_()
		{
			self::$locale_path_ = Router::get_base_path() . '/Locale';
			// bind the domains
			foreach (self::$domains_ as $domain)
			{
				bindtextdomain($domain, self::$locale_path_);
			}

			if (count(self::$domains_) > 0)
			{
				self::set_domain(self::$domains_[0]);
			}
		}

		private static function update_language_($locale)
		{
			putenv('LANG=' . $locale);
			setlocale(LC_ALL, $locale);
		}

		/**
		 * Get the list of domains.
		 */
		public static function get_domains()
		{
			return self::$domains_;
		}

		/**
		 * Set the default domain.
		 */
		public static function set_domain($domain)
		{
			textdomain($domain);
		}
		
		/**
		 * @return string The new language if it could have been set, of the default one.
		 */
		public static function set_language($short_name)
		{
			if (!self::$initialized_)
			{
				self::initialize_();
			}

			if (array_key_exists($short_name, self::$supported_languages_))
			{
				$locale_name = self::$supported_languages_[$short_name];
				self::update_language_($locale_name);
				return $short_name;
			}

			self::update_language_(self::$supported_languages_[self::$default_language_short_name_]);
			return self::$default_language_short_name_;
		}

		/**
		 * Convenienve method to get translated text.
		 */
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