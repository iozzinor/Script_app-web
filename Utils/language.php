<?php
    class Language
    {
		/**
		 * The suppported languages.
		 */
		private static $supported_languages_ = array(
			"fr" => array('long' => 'fr_FR.utf8', 'medium' => 'fr_FR'),
			"en" => array('long' => 'en_US.utf8', 'medium' => 'en_US')
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
		 * The current locale, long.
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

		private static function update_language_($short_name)
		{
			self::$language_name_ = $short_name;
			self::$locale_ = self::$supported_languages_[$short_name]['long'];
			putenv('LANG=' . self::$locale_);
			setlocale(LC_ALL, self::$locale_);
		}

		/**
		 * @return string The current locale, long format.
		 */
		public static function locale()
		{
			return self::$locale_;
		}

		/**
		 * @return string The current locale, medium format.
		 */
		public static function locale_medium()
		{
			return self::$supported_languages_[self::$language_name_]['medium'];
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
				self::update_language_($short_name);
				return $short_name;
			}

			self::update_language_(self::$default_language_short_name_);
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
	function l($domain, $message_id)
	{
		return nl2br(htmlspecialchars(_d($domain, $message_id), ENT_COMPAT | EN_HTML_401), false);
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