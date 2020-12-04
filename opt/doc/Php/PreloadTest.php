<?php

namespace RmpUp\WpDi\Test\Php;

use ReflectionClass;
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
     * Set this to TRUE to regenerate the preload.php
     *
     * @var bool
     */
    private $regenerate = false;

    /**
     * @var string[]
     */
    protected $classList;

    /**
     * @var string[]
     */
    private $excludeFromPreload = [
        'RmpUp\\WpDi\\Compat\\',
        'RmpUp\\WpDi\\Compiler\\Yaml\\',

        \RmpUp\WpDi\Exception::class,
        \RmpUp\WpDi\Yaml::class,

        \RmpUp\WpDi\Compiler\Exception::class,
        \RmpUp\WpDi\Compiler\AlreadyExistsException::class,

        \RmpUp\WpDi\Helper\Deprecated::class,
        'RmpUp\\WpDi\\Helper\\Yaml\\',

        \RmpUp\WpDi\Provider\InvalidActionDefinitionException::class,
        \RmpUp\WpDi\Provider\MissingServiceDefinitionException::class,
    ];

    /**
     * Include in pre-load
     *
     * @var string[]
     */
    private $includeInPreload = [
        'RmpUp\\WpDi\\',
    ];

    /**
     * @var string
     */
    private $preloadFilePath;

    private function findInList(string $className, array $list): bool
    {
        foreach ($list as $item) {
            if ('\\' === substr($item, -1) && 0 === strpos($className, $item)) {
                // Namespace match
                return true;
            }

            if ($item === $className) {
                // FQN match
                return true;
            }
        }

        return false;
    }

    public function getScenarios()
    {
        $scenarios = [];
        foreach ($this->getClassesAndDependencies() as $item) {
            $scenarios[$item] = [$item];
        }

        return $scenarios;
    }

    public function assertClassesNotLoaded()
    {
        foreach ($this->getClassesAndDependencies() as $className) {
            static::assertFalse(
                class_exists($className, false),
                'Class has been loaded: ' . $className
            );
        }
    }

    private function isWhitelisted(string $className): bool
    {
        return $this->findInList($className, $this->includeInPreload);
    }

    /**
     * @param string $class
     *
     * @return ReflectionClass[]
     * @throws \ReflectionException
     */
    private function resolveDependencies($class)
    {
        $dependencies = [];

        if (false === $class instanceof \ReflectionClass) {
            $class = new ReflectionClass($class);
        }

        // Interfaces
        if ($class->isInterface()) {
            return [$class];
        }

        foreach ($class->getInterfaces() as $interface) {
            $dependencies[] = $this->resolveDependencies($interface);
        }

        // Parents
        $parentClass = $class->getParentClass();
        if ($parentClass) {
            $dependencies[] = $this->resolveDependencies($parentClass);
        }

        // Traits
        foreach ($class->getTraits() as $trait) {
            $dependencies[] = $this->resolveDependencies($trait);
        }

        $dependencies[] = [$class];

        return array_merge(...$dependencies);
    }

    protected function setUp()
    {
        $this->preloadFilePath = realpath(WPDI_BASE_DIR) . '/preload.php';

        require_once WPDI_BASE_DIR . 'preload.php';
    }

    /**
     * @dataProvider getScenarios
     * @param string $className
     */
    public function testClassesHaveBeenPreloaded(string $className)
    {
        static::assertTrue(
            class_exists($className)
            || interface_exists($className)
            || trait_exists($className),
            false
        );
    }

    public function testGeneratedPreloaderIsComplete()
    {
        $content = '<?php' . PHP_EOL;

        foreach ($this->getClassesAndDependencies() as $classPath => $className) {
            static::assertTrue(
                class_exists($className) || interface_exists($className) || trait_exists($className),
                'Unknown thing: ' . $className
            );

            $content .= PHP_EOL . "require_once '" . addslashes($classPath) . "';";
        }

        if ($this->regenerate) {
            file_put_contents($this->preloadFilePath, $content);
            $this->markTestSkipped('Flag to regenerate preload.php is set');
        }

        static::assertSame($content, file_get_contents($this->preloadFilePath));
    }

    private function isBlacklisted(string $className): bool
    {
        if ($this->findInList($className, $this->excludeFromPreload)) {
            return true;
        }

        $classReallyNotExists = class_exists($className)
            || interface_exists($className)
            || trait_exists($className);

        if (false === $classReallyNotExists) {
            throw new RuntimeException('Unknown: ' . $className);
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getAllClasses(): array
    {
        if (null === $this->classList) {
            // Build class list
            $finder = new Finder();
            $finder->in(WPDI_LIB_DIR);
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

    /**
     * @return array<string, string>
     * @throws \ReflectionException
     */
    public function getClassesAndDependencies()
    {
        $wpDiBaseDir = realpath(WPDI_BASE_DIR);
        $dependencies = [];
        foreach ($this->getAllClasses() as $classPath => $class) {
            if (
                false === $this->isWhitelisted($class)
                || $this->isBlacklisted($class)
                || in_array($class, $dependencies, true)
            ) {
                // Not our scope, excluded or already part of the pre-loading.
                continue;
            }

            foreach ($this->resolveDependencies($class) as $item) {
                $dependencyName = $item->getName();
                if (false === $this->isWhitelisted($dependencyName)) {
                    // Not our scope.
                    continue;
                }

                if ($this->isBlacklisted($dependencyName)) {
                    // In our scope but excluded.
                    // Either load all dependencies or ignore the actual class.
                    // Otherwise pre-loading (without autoloader) makes no sense.
                    throw new \RuntimeException(
                        sprintf(
                            '"%s" is excluded from pre-loading but needed by "%s"',
                            $dependencyName,
                            $class
                        )
                    );
                }

                $dependencyPath = ltrim(str_replace($wpDiBaseDir, '', $item->getFileName()), '/');
                $dependencies[] = [$dependencyPath => $dependencyName];
            }

            $dependencies[] = [$classPath => $class];
        }

        if (empty($dependencies)) {
            return [];
        }

        return array_merge(...$dependencies);
    }
}