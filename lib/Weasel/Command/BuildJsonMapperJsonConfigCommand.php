<?php
namespace Weasel\Command;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Weasel\JsonMarshaller\Config\AnnotationDriver;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\WeaselDefaultAnnotationDrivenFactory;
use Weasel\WeaselDoctrineAnnotationDrivenFactory;

class BuildJsonMapperJsonConfigCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('json')
            ->setDescription('Build a config for the JSON mapper')
            ->addArgument(
                'in',
                InputArgument::REQUIRED,
                'What directory/file should I scan?'
            )
            ->addArgument(
                'out',
                InputArgument::OPTIONAL,
                'What file should I write to? Default is stdout'
            )
            ->addOption("builtinannot",
                null,
                InputOption::VALUE_OPTIONAL,
                "Use the built in annotations rather than Doctrine",
                false);
    }

    /**
     * @var AnnotationDriver
     */
    protected $driver;

    /**
     * @var JsonMapper
     */
    protected $mapper;

    protected function setupDriver($useBuiltin)
    {
        if ($useBuiltin) {
            $factory = new WeaselDefaultAnnotationDrivenFactory();
            $driver = $factory->getJsonDriverInstance();
        } else {
            $factory = new WeaselDoctrineAnnotationDrivenFactory();
            $driver = $factory->getJsonDriverInstance();
        }

        $this->mapper = $factory->getJsonMapperInstance();
        $this->driver = $driver;

    }

    protected function generateConfigs($target)
    {
        $before = get_declared_classes();
        $flags = FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($target, $flags)) as $fileInfo) {
            /**
             * @var \SplFileInfo $fileInfo
             */
            if ($fileInfo->isFile() && preg_match('/\.(php[0-9.]*|inc)$/', $fileInfo->getFilename())) {
                include_once($fileInfo->getPathname());
            }
        }
        $after = get_declared_classes();
        $new = array_diff($after, $before);

        $config = array();
        foreach ($new as $class) {
            $config[$class] = $this->driver->getConfig($class);
        }
        return $config;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $in = $input->getArgument('in');
        if (!is_readable($in)) {
            throw new \InvalidArgumentException("in must be a readable file or directory");
        }
        $this->setupDriver($input->getOption("builtinannot"));
        $config = $this->generateConfigs($in);
        $json = $this->mapper->writeString($config, '\Weasel\JsonMarshaller\Config\ClassMarshaller[]');
        if ($out = $input->getArgument("out")) {
            file_put_contents($out, $json);
        } else {
            print $json;
        }
    }
}