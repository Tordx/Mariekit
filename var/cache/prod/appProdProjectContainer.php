<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerOvaoywx\appProdProjectContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerOvaoywx/appProdProjectContainer.php') {
    touch(__DIR__.'/ContainerOvaoywx.legacy');

    return;
}

if (!\class_exists(appProdProjectContainer::class, false)) {
    \class_alias(\ContainerOvaoywx\appProdProjectContainer::class, appProdProjectContainer::class, false);
}

return new \ContainerOvaoywx\appProdProjectContainer([
    'container.build_hash' => 'Ovaoywx',
    'container.build_id' => 'c004dd25',
    'container.build_time' => 1665683753,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerOvaoywx');
