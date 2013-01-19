<?php
namespace Weasel\Annotation\TestResources {

    use /** @noinspection PhpUndefinedNamespaceInspection */
        /** @noinspection PhpUndefinedClassInspection */
        Weasel\Annotation\TestResources\TestA;
    use /** @noinspection PhpUndefinedClassInspection */
        /** @noinspection PhpUndefinedNamespaceInspection */
        \Weasel\Annotation\TestResources\TestB;
    use /** @noinspection PhpUndefinedNamespaceInspection */
        /** @noinspection PhpUndefinedClassInspection */
        Weasel\Annotation\TestResources\BlahFoo as Test;

    class NamespacedFirst
    {

    }
}

namespace Weasel\Annotation\TestResourcesToo {

    use /** @noinspection PhpUndefinedNamespaceInspection */
        /** @noinspection PhpUndefinedClassInspection */
        \Weasel\Annotation\TestResources\TestC;

    class NamespacedSecond
    {

    }
}
