<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation;

use RuntimeException;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;

class PhpParser implements LoggerAwareInterface
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        if (isset($logger)) {
            $this->setLogger($logger);
        }
    }

    public function parseClass(\ReflectionClass $class)
    {
        if (isset($this->logger)) {
            $this->logger->debug("Parsing file " . $class->getFileName() . " for " . $class->getName());
        }
        return $this->_parse($class);
    }

    protected function _parse(\ReflectionClass $class)
    {
        // Read PHP file up to the point the class is defined
        $data = $this->_readPrologue($class);

        $tokens = token_get_all('<?php ' . $data);

        // LAME HACK!
        // This fixes issue #41 : by calling token_get_all() on a faked up empty docblock comment we ensure that the
        // doc_comment Zend compiler global contains something "harmless." Without doing this the compiler global may
        // contain a docblock comment from $data, and that might be picked up by the Zend compiler when the next file
        // to be included gets compiled. That would be really, really bad.
        token_get_all("<?php\n/**\n *\n */\n");

        $namespaces = array();
        $curNamespace = '\\';
        while ($token = array_shift($tokens)) {
            if (!is_array($token)) {
                continue;
            }
            $name = $token[0];
            if ($name === T_NAMESPACE) {
                $curNamespace = $this->_Namespace($tokens);
                $namespaces = array();
                continue;
            }

            if ($name === T_USE) {
                $namespaces = array_merge($namespaces, $this->_Use($tokens));
            }

            if ($name === T_CLASS) {
                $foundClass = $this->_Class($tokens);
                if (empty($foundClass)) {
                    // Class name is not on the same line as the class keyword. We're good to assume that we've read the right thing.
                    break;
                }
                if ($foundClass[0] != '\\') {
                    $foundClass = $curNamespace . '\\' . $foundClass;
                }
                if ($foundClass === $class->getName()) {
                    if ($class->getNamespaceName() !== $curNamespace) {
                        throw new \RuntimeException("Parsing error: Thought $foundClass was in {$class->getNamespaceName()} but it isn't.");
                    }
                    break;
                }
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

    protected function _Use($tokens)
    {

        $namespaces = array();

        $aliasPart = false;
        $namespace = "";
        $alias = null;
        $lastSegment = null;

        while ($token = array_shift($tokens)) {
            if (!is_array($token)) {
                $namespace = ltrim($namespace, '\\');
                if ($token === ',') {
                    if (!isset($alias)) {
                        $alias = $lastSegment;
                    }
                    $namespaces[$alias] = $namespace;
                    $alias = null;
                    $lastSegment = null;
                    $aliasPart = false;
                    $namespace = "";
                    continue;
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
            } elseif ($token[0] === T_WHITESPACE || $token[0] === T_COMMENT || $token[0] === T_DOC_COMMENT) {
                // Meh
            } elseif ($aliasPart && ($token[0] === T_STRING || $token[0] === T_NS_SEPARATOR)) {
                $alias .= $token[1];
            } else {
                break;
            }
        }
        return $namespaces;

    }


    protected function _readPrologue(\ReflectionClass $class)
    {
        $file = $class->getFileName();
        $line = $class->getStartLine();

        $buffer = "";
        $handle = @fopen($file, "r");
        if ($handle) {
            for ($curLine = 0; $curLine < $line; $curLine++) {
                $data = fgets($handle);
                if ($data === false) {
                    break;
                }
                $buffer .= $data;
            }

            fclose($handle);
        }

        return $buffer;

    }

    private function _Namespace($tokens)
    {
        $namespace = "";

        while ($token = array_shift($tokens)) {
            if (!is_array($token)) {
                switch ($token) {
                    case ";":
                    case "{":
                        return $namespace;
                    default:
                        throw new RuntimeException("Parse error: got $token expected ;");
                }
            } else {
                switch ($token[0]) {
                    case T_STRING:
                    case T_NS_SEPARATOR:
                        $namespace .= $token[1];
                        break;
                    case T_WHITESPACE:
                        if (empty($namespace)) {
                            break;
                        } else {
                            return $namespace;
                        }
                    case T_COMMENT:
                    case T_DOC_COMMENT:
                        break;
                    default:
                        throw new RuntimeException("Parse error: got {$token[1]} expected part of namespace");
                }
            }
        }
        return $namespace;
    }

    private function _Class($tokens)
    {
        $class = "";

        while ($token = array_shift($tokens)) {
            if (!is_array($token)) {
                switch ($token) {
                    case "{":
                        return $class;
                    default:
                        throw new RuntimeException("Parse error: got $token expected {");
                }
            } else {
                switch ($token[0]) {
                    case T_STRING:
                    case T_NS_SEPARATOR:
                        $class .= $token[1];
                        break;
                    case T_WHITESPACE:
                        if (empty($class)) {
                            break;
                        } else {
                            return $class;
                        }
                    case T_COMMENT:
                    case T_DOC_COMMENT:
                        break;
                    default:
                        throw new RuntimeException("Parse error: got {$token[1]} expected part of class name");
                }
            }
        }
        return $class;
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
