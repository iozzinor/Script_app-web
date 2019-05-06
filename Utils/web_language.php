<?php
    class WebLanguage
    {
		/**
		 * The suppported languages.
		 */
		private static $supported_languages_ = array();

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
		 * The current language.
		 */
		private static $current_language_;

		private static function initialize_()
		{
			if (self::$initialized_)
			{
				return;
			}
			self::$initialized_ = true;

			self::$locale_path_ = Router::get_base_path() . '/Locale';
			// bind the domains
			array_push(self::$supported_languages_, new WebLanguage('fr', 'fr_FR', 'fr_FR.utf8', 'Français'));
			array_push(self::$supported_languages_, new WebLanguage('en', 'en_US', 'en_US.utf8', 'English'));
			
			foreach (self::$domains_ as $domain)
			{
				bindtextdomain($domain, self::$locale_path_);
			}

			if (count(self::$domains_) > 0)
			{
				self::set_domain(self::$domains_[0]);
			}
		}

		private static function update_language_(string $short_name)
		{
			foreach (self::$supported_languages_ as $supported_language)
			{
				if ($supported_language->short_name_ == $short_name)
				{
					self::$current_language_ = $supported_language;
					putenv('LANG=' . $supported_language->long_name_);
					setlocale(LC_ALL, $supported_language->long_name_);
				}
			}
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

		public static function get_current_language()
		{
			return self::$current_language_;
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
			self::initialize_();

			foreach (self::$supported_languages_ as $supported_language)
			{
				if ($supported_language->short_name_ == $short_name)
				{
					self::update_language_($short_name);
					return $short_name;	
				}
			}

			self::update_language_(self::$default_language_short_name_);
			return self::$default_language_short_name_;
		}

		public static function get_supported_languages()
		{
			return self::$supported_languages_;
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

		protected $short_name_;
		protected $medium_name_;
		protected $long_name_;
		protected $full_name_;
		/**
		 * @param string $short_name The language short name that should appear in the URL.
		 * @param string $medium_name The language medium name.
		 * @param string $long_name The language long name that is used when calling setlocale.
		 * @param string $full_name The language full name.
		 */
		public function __construct($short_name, $medium_name, $long_name, $full_name)
		{
			$this->short_name_ 	= $short_name;
			$this->medium_name_	= $medium_name;
			$this->long_name_ 	= $long_name;
			$this->full_name_ 	= $full_name;
		}

		public function get_short_name()
		{
			return $this->short_name_;
		}

		public function get_medium_name()
		{
			return $this->medium_name_;
		}

		public function get_long_name()
		{
			return $this->long_name_;
		}

		public function get_full_name()
		{
			return $this->full_name_;
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

	function _dn($domain, $message_id_singular, $message_id_plural, $count)
	{
		return dngettext($domain, $message_id_singular, $message_id_plural, $count);
	}
?>