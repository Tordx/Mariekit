<?php

use Symfony\Component\Translation\MessageCatalogue;

$catalogue = new MessageCatalogue('fr-CA', array (
));

$catalogueFr = new MessageCatalogue('fr', array (
));
$catalogue->addFallbackCatalogue($catalogueFr);

return $catalogue;
