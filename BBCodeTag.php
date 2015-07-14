<?php
	/**
	 * Cette classe représente un tag BBCodeParser
	 */
	class BBCodeTag
	{
		/* Le nom du tag (ex : a, img, iframe) */
		private $name;

		/* Le attributs autorisés pour ce tag sous forme de tableau (ex : ['src', 'class']) */
		private $allowedAttributs;

		/* Un booléean, vrai si le tag est autofermant (ex : Les balises <img />) */
		private $autoClosing;

		/**
		 * Fonction de construction
		 * @param string $name : Le nom du tag
		 * @param array $allowedAttributs : Les attributs autorisés (ex : ['src', 'class']). Par défaut []
		 * @param boolean $autoClosing : Si la balise est auto-fermante. Par défaut false;
		 * @return BBCodeTag : Le tag lui même
		 */
		public function __construct ($name, $allowedAttributs = [], $autoClosing = false)
		{
			$this->setName($name);
			$this->setAllowedAttributs($allowedAttributs);
			$this->setAutoClosing($autoClosing);
		}

		public function setName ($name)
		{
			$this->name = $name;
			return $this;
		}

		public function getName ()
		{
			return $this->name;
		}

		public function setAllowedAttributs ($allowedAttributs)
		{
			$this->allowedAttributs = $allowedAttributs;
			return $this;
		}

		public function getAllowedAttributs ()
		{
			return $this->allowedAttributs;
		}

		public function setAutoClosing ($autoClosing)
		{
			$this->autoClosing = $autoClosing;
			return $this;
		}

		public function getAutoClosing ()
		{
			return $this->autoClosing;
		}

		/**
		 * Cette fonction permet de vérifier si un attribut est autorisé sur le tag
		 * @param string $attribut : L'attribut à vérifier
		 * @return boolean : Vrai si l'attribut est autorisé, faux sinon
		 */
		public function isAllowed ($attribut)
		{
			return in_array($attribut, $this->getAllowedAttributs());
		}

		/**
		 * Cette fonction permet d'ajouter un attribut autorisé au tag
		 * @param string $attribut : L'attribut à ajouter
		 * @return BBCodeTag : Le tag lui même
		 */
		public function addAllowedAttribut ($attribut)
		{
			$attributs = $this->getAllowedAttributs();
			$attributs[] = $attribut;
			$this->setAllowedAttributs(array_unique($attributs));
			return $this;
		}

		/**
		 * Cette fonction permet de retirer un attribut autorisé du tag
		 * @param string $attribut : L'attribut à retirer
		 * @return BBCodeTag : Le tag lui même
		 */
		public function removeAllowedAttribut ($attribut)
		{
			$allowedAttributs = $this->getAllowedAttributs();
			if ($key = array_search($tag, $allowedAttributs) !== false)
			{
				unset($allowedAttributs[$key]);
				$this->setAllowedAttributs($allowedAttributs);
			}

			return $this;
		}
	}
