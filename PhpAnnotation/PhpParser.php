<?php
namespace PhpAnnotation;

class PhpParser
{

    public function __construct() {
    }

    public function parseClass(\ReflectionClass $class) {
        return $this->_parse($class);
    }

    protected function _parse(\ReflectionClass $class) {
        // Read PHP file up to the point the class is defined
        $data = $this->_readPrologue($class);

        $tokens = token_get_all('<?php ' . $data);
        $namespaces = array();
        while ($token = array_shift($tokens)) {
            if (!is_array($token)) {
                continue;
            }
            $name = $token[0];
            if ($name === T_NAMESPACE) {
                $namespaces = array();
                continue;
            }

            if ($name === T_USE) {
                $namespaces = array_merge($namespaces, $this->_Use($tokens));
            }

        }

        $classNS = $class->getNamespaceName();
        if (!empty($classNS)) {
            $namespaces[""] = $classNS;
        } else {
            $namespaces[""] = "";
        }

        return $namespaces;
    }

    protected function _Use($tokens) {

        $namespaces = array();

        $aliasPart = false;
        $namespace = "";
        $alias = null;
        $lastSegment = null;

        while ($token = array_shift($tokens)) {
            if (!is_array($token)) {
                if ($token === ',') {
                    $namespaces[$alias] = $namespace;
                    $alias = "";
                } elseif ($token === ';') {
                    if (!isset($alias)) {
                        $alias = $lastSegment;
                    }
                    $namespaces[$alias] = $namespace;
                    break;
                } else {
                    // Who knows.
                    break;
                }
            }
            if (!$aliasPart && $token[0] === T_STRING) {
                $namespace .= $token[1];
                $lastSegment = $token[1];
            } elseif (!$aliasPart && $token[0] === T_NS_SEPARATOR) {
                $namespace .= $token[1];
            } elseif ($token[0] === T_AS) {
                $aliasPart = true;
                $alias = "";
            } elseif ($token[0] === T_WHITESPACE) {
                // Meh
            } elseif ($aliasPart && ($token[0] === T_STRING || $token[0] === T_NS_SEPARATOR)) {
                $alias .= $token[1];
            } else {
                break;
            }
        }
        return $namespaces;

    }


    protected function _readPrologue(\ReflectionClass $class) {
        $file = $class->getFileName();
        $line = $class->getStartLine();

        return file_get_contents($file, null, null, 0, $line);
    }

}
