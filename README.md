# BBCodeParser
BBCodeParser est un parser PHP pour un code proche du BBCode mais reprenant entierrement la syntaxe du HTML.
Le but de BBCodeParser est de vous permettre de gérer facilement une syntaxe offrant toutes les possibilitées du HTML mais en gérant les balises disponibles et les attributs autorisés de celles-ci.

#BBTags
Afin de gérer les balises utilisables dans un texte, BBCodeParser emploie l'objet BBTags, qui permet de définir un tag et ses différents réglages.

Voici un exemple illustrant un lien dans lequel vous pourrez utiliser les attributs "href", "rel" et "target" uniquement !
```PHP
$tag = new BBTags('a', ['href', 'rel', 'target']);
```

#Parser un texte
Le but de BBCodeParser est de vous permettre de facilement parser une chaine contenant du BBCode pour en faire du HTML.
Voici un exemple vous permettant de parser une chaine BBCode en reconnaissant :
* les liens, dans lesquel vous pourrez renseigner un attribut "href" uniquement !
* les images, dans lesquel vous pourrez renseigner un attribut "src" et "alt" !
```PHP
$tags = array(
  new BBTags('a', ['href']),
  new BBTags('img', ['src', 'alt']),
);

$parser = new BBCodeParser($tags);
```
