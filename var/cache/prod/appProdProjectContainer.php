<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerQal6hcv\appProdProjectContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerQal6hcv/appProdProjectContainer.php') {
    touch(__DIR__.'/ContainerQal6hcv.legacy');

    return;
}

if (!\class_exists(appProdProjectContainer::class, false)) {
    \class_alias(\ContainerQal6hcv\appProdProjectContainer::class, appProdProjectContainer::class, false);
}

return new \ContainerQal6hcv\appProdProjectContainer([
    'container.build_hash' => 'Qal6hcv',
    'container.build_id' => 'eaac290a',
    'container.build_time' => 1663151240,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerQal6hcv');
