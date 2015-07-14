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

	/**
	 * Cette classe permet de créer un parser proche du bb_code, en lui fournissant une liste de balises à passer au format HTML
	 */
	class BBCodeParser
	{
		/* Les tags autorisés */
		private $allowedTags;

		/**
		 * Fonction de construction
		 * @param array $allowedTags : Un tableau contenant les tags BBCodeTag qui devront être validés (par défaut, pas de tag)
		 */
		public function __construct ($allowedTags = [])
		{
			$this->setAllowedTags($allowedTags);
		}

		/**
		 * Permet de récupérer le tableau des tags alloués
		 */
		public function getAllowedTags ()
		{
			return $this->allowedTags;
		}

		public function setAllowedTags ($allowedTags)
		{
			$this->allowedTags = $allowedTags;
			return $this;
		}

		/**
		 * Permet d'ajouter un tag aux tags alloués
		 * @param BBCodeTag $tag : Le tag à ajouter aux tags alloués
		 * @return BBCodeParser : L'objet lui même, afin de pouvoir chainer facilement
		 */
		public function addAllowedTag ($tag)
		{
			$allowedTags = $this->getAllowedTags();
			$allowedTags[] = $tag;

			$alreadyFoundTags = [];
			foreach ($allowedTags as $key => $allowedTag)
			{
				if (in_array($allowedTag->getName(), $alreadyFoundTags))
				{
					unset($allowedTags[$key]);
					continue;
				}

				$alreadyFoundTags[] = $allowedTag->getName();
			}

			$this->setAllowedTags($allowedTags);
			return $this;
		}

		/**
		 * Permet de retirer un tag des tags alloués
		 * @param string $tagName : Le nom du tag à retirer
		 * @return BBCodeParser : L'objet lui même, afin de pouvoir chainer facilement
		 */
		public function removeAllowedTag ($tagName)
		{
			$allowedTags = $this->getAllowedTags();

			foreach ($allowedTags as $key => $allowedTag)
			{
				if ($allowedTag->getName() == $tagName)
				{
					unset($allowedTags[$key]);
				}
			}

			$this->setAllowedTags($allowedTags);

			return $this;
		}

		/**
		 * Permet de vérifier si un tag est alloué
		 * @param string $tagName : Le nom du tag à vérifier
		 * @return boolean : true si il est alloué, false sinon
		 */
		public function isAllowed ($tagName)
		{
			$allowedTags = $this->getAllowedTags();

			foreach ($allowedTags as $key => $allowedTag)
			{
				if ($allowedTag->getName() == $tagName)
				{
					return true;
				}
			}

			return false;
		}

		/**
		 * Cette fonction permet de parser une chaine avec du BBcode pour la récupérer au format HTML. Il faut déjà avoir sécurisé tout le reste du HTML !
		 * @param string $string : La chaine à parser
		 * @return string : La chaine une fois parsée
		 */
		public function parse ($string)
		{
			//On va remplacer chaque tag autorisé
			$allowedTags = $this->getAllowedTags();
			foreach ($allowedTags as $key => $allowedTag)
			{
				$matches = array();
				
				//Cas des balises standard (no auto-fermantes)
				if (!$allowedTag->getAutoClosing())
				{
					//On forge le pattern qui va nous permettre de récupérer la balise
					$pattern = '#(?<!\\?<!\\\)\[' . preg_quote($allowedTag->getName(), '#') . ' (.*)(?<!\\?<!\\\)\](.*)(?<!\\?<!\\\)\[/' . preg_quote($allowedTag->getName(), '#') . '(?<!\\?<!\\\)\]#iUu';
					preg_match_all($pattern, $string, $matches, PREG_SET_ORDER);

					//On va faire le remplacement de chaque chaine
					$offset = 0;
					foreach ($matches as $key => $matche)
					{
						//On va récupérer la position du début de la chaine, sa taille et déterminer l'offset de la suivante
						$pos = mb_strpos($string, $matche[0], $offset);

						//On va analyser les attributs du tag pour garder seulement ceux valides

						//On regarde avec des '"'
						$pattern = '#([^ ]+)="([^"]+)"#iu';
						$matches2 = [];
						preg_match_all($pattern, $matche[1], $matches2, PREG_SET_ORDER);

						//On va vérifier s'il s'agit d'un attribut autorisé, et on va reconstruire les attributs du tag selon cette nouvelle version
						$newAttributs = [];
						foreach ($matches2 as $key2 => $matche2)
						{
							if (in_array($matche2[1], $allowedTag->getAllowedAttributs()))
							{
								$newAttributs[] = $matche2[1] . '="' . $matche2[2] . '"';
							}
						}
						
						//On regerde sans les '"'
						$pattern = '#([^ ]+)=([^" ]+)#iu';
						$matches2 = [];
						preg_match_all($pattern, $matche[1], $matches2, PREG_SET_ORDER);

						//On va vérifier s'il s'agit d'un attribut autorisé, et on va reconstruire les attributs du tag selon cette nouvelle version
						foreach ($matches2 as $key2 => $matche2)
						{
							if (in_array($matche2[1], $allowedTag->getAllowedAttributs()))
							{
								$newAttributs[] = $matche2[1] . '="' . $matche2[2] . '"';
							}
						}

						$newTag = '<' . $allowedTag->getName() . ' ' . implode(' ', $newAttributs) . '>' . $matche[2] . '</' . $allowedTag->getName() . '>';

						//On va reforger la chaine globale
						$stringFirstPart = mb_strcut($string, 0, $pos);
						$stringLastPart = mb_strcut($string, $pos + mb_strlen($matche[0]));
						$string = $stringFirstPart . $newTag . $stringLastPart;

						//On recalcul l'offset
						$offset = $pos + mb_strlen($newTag);
					}

				}
			}
			
			return $string;
		}
	}