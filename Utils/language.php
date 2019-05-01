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

		/**
		 * The locale directory path.
		 */
		private static $locale_path_;

		/**
		 * List of domains.
		 */
		private static $domains_ = array(
			'messages'
		);

		/**
		 * Whether the languages folder have been loaded.
		 */
		private static $initialized_ = false;

		/**
		 * The current language name, short.
		 */
		private static $language_name_;

		/**
		 * The current locale.
		 */
		private static $locale_;

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
			self::$locale_ = $locale;
			putenv('LANG=' . $locale);
			setlocale(LC_ALL, $locale);
		}

		/**
		 * @return string The current locale.
		 */
		public static function locale()
		{
			return self::$locale_;
		}

		/**
		 * Get the list of domains.
		 */
		public static function get_domains()
		{
			return self::$domains_;
		}

		public static function load_domains($domains)
		{
			self::$domains_ = $domains;
			self::initialize_();
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
				self::$language_name_ = $short_name;
				$locale_name = self::$supported_languages_[$short_name];
				self::update_language_($locale_name);
				return $short_name;
			}

			self::$language_name_ = self::$default_language_short_name_;
			self::update_language_(self::$supported_languages_[self::$default_language_short_name_]);
			return self::$default_language_short_name_;
		}

		public static function get_lang()
		{
			return self::$language_name_;
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

	/**
	 * Convenience method to replace newlines with <br /> tags.
	 */
	function l($message_id)
	{
		return nl2br(htmlspecialchars(_($message_id), ENT_COMPAT | EN_HTML_401), false);
	}

	function _d($domain, $message_id)
	{
		return dgettext($domain, $message_id);
	}

	function _n($message_id_singular, $message_id_plural, $count)
	{
		return ngettext($message_id_singular, $message_id_plural, $count);
	}
?>