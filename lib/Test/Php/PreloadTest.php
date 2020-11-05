<?php

namespace RmpUp\WpDi\Test\Php;

use Exception;
use RmpUp\WpDi\Compiler\AlreadyExistsException;
use RmpUp\WpDi\Provider\InvalidActionDefinitionException;
use RmpUp\WpDi\Provider\MissingServiceDefinitionException;
use RmpUp\WpDi\Test\AbstractTestCase;
use RuntimeException;
use Symfony\Component\Finder\Finder;

/**
 * Preloading
 *
 * The "preload.php" loads some of the often used classes
 * and can be used for PHP 7.4 pre-loading feature.
 * It should increase the overall performance a little bit
 * because loading all files/classes could take round about
 * 0.002 seconds.
 *
 * @package RmpUp\WpDi\Test\Php
 *
 * @runInSeparateProcess
 */
class PreloadTest extends AbstractTestCase
{
    /**
     * @var string[]
     */
    protected $classList;

    /**
     * @var string
     */
    private $preloadFilePath;

    public function getScenarios()
    {
        $scenarios = [];
        foreach ($this->getClasses() as $item) {
            $scenarios[$item] = [$item];
        }

        return $scenarios;
    }

    public function assertClassesNotLoaded()
    {
        foreach ($this->getClasses() as $className) {
            static::assertFalse(
                class_exists($className, false),
                'Class has been loaded: ' . $className
            );
        }
    }

    protected function setUp()
    {
        $this->preloadFilePath = realpath(WPDI_BASE_DIR) . '/preload.php';

        $this->getClasses();

        require_once WPDI_BASE_DIR . 'preload.php';
    }

    /**
     * @dataProvider getScenarios
     * @param string $className
     */
    public function testClassesHaveBeenPreloaded(string $className)
    {
//        foreach ($this->classList as $classPath => $className)
        {
            static::assertTrue(
                class_exists($className)
                || interface_exists($className)
                || trait_exists($className),
                false
            );
        }
    }

    public function testGeneratedPreloaderIsComplete()
    {
        $content = '<?php' . PHP_EOL;

        foreach ($this->getClasses() as $classPath => $className) {
            static::assertTrue(
                class_exists($className) || interface_exists($className) || trait_exists($className),
                'Unknown thing: ' . $className
            );

            if ($this->isClassIgnored($className)) {
                continue;
            }

            $content .= PHP_EOL . "require_once '" . addslashes($classPath) . "';";
        }

        static::assertSame($content, file_get_contents($this->preloadFilePath));

        // file_put_contents($this->preloadFilePath, $content);
    }

    private function isClassIgnored(string $className)
    {
        $classReallyNotExists = class_exists($className)
            || interface_exists($className)
            || trait_exists($className);

        if (false === $classReallyNotExists) {
            throw new RuntimeException('Unknown: ' . $className);
        }

        // Ignore the following
        $ignored = [
            \RmpUp\WpDi\Exception::class,
            \RmpUp\WpDi\Compiler\Exception::class,
            AlreadyExistsException::class,
            InvalidActionDefinitionException::class,
            MissingServiceDefinitionException::class,
        ];

        return in_array($className, $ignored, true)
            || $className instanceof Exception;
    }

    /**
     * @return array
     */
    protected function getClasses(): array
    {
        if (null === $this->classList) {
            // Build class list
            $finder = new Finder();
            $finder->in(WPDI_LIB_DIR . 'Compiler');
            $finder->in(WPDI_LIB_DIR . 'Provider');
            $finder->name('*.php');

            $classList = array_map(
                'realpath',
                array_map('strval', iterator_to_array($finder->files()))
            );

            $libDir = realpath(WPDI_LIB_DIR);
            $baseDir = realpath(WPDI_BASE_DIR);
            foreach ($classList as $key => $classPath) {
                $className = str_replace($libDir, '', $classPath);
                $className = str_replace('/', '\\', dirname($className))
                    . '\\' . basename($className, '.php');
                $className = 'RmpUp\\WpDi\\' . ltrim($className, '\\');

                $classPath = str_replace($baseDir, '', $classPath);
                $classPath = ltrim($classPath, '/');

                $this->classList[$classPath] = $className;
            }

            ksort($this->classList);
        }

        return $this->classList;
    }
}