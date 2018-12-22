<?php

namespace Tests\PHPCensor\Plugin;

use PHPCensor\Plugin\Phar as PharPlugin;
use Phar as PHPPhar;

class PharTest extends \PHPUnit\Framework\TestCase
{
    protected $directory;

    protected function tearDown()
    {
        $this->cleanSource();
    }

    protected function getPlugin(array $options = [])
    {
        $build = $this
            ->getMockBuilder('PHPCensor\Model\Build')
            ->disableOriginalConstructor()
            ->getMock();

        $builder = $this
            ->getMockBuilder('PHPCensor\Builder')
            ->disableOriginalConstructor()
            ->getMock();

        $builder
            ->expects($this->any())
            ->method('interpolate')
            ->willReturnArgument(0);

        $buildPath          = $this->buildSource();
        $builder->buildPath = $buildPath;
        $builder->directory = $buildPath;

        return new PharPlugin($builder, $build, $options);
    }

    protected function buildTemp()
    {
        $directory = tempnam(ROOT_DIR . 'tests/runtime/', 'source');
        @unlink($directory);

        return $directory . '/';
    }

    protected function buildSource()
    {
        $directory = $this->buildTemp();
        mkdir($directory);
        file_put_contents($directory . 'one.php', '<?= "one";');
        file_put_contents($directory . 'two.php', '<?= "two";');
        mkdir($directory . 'config');
        file_put_contents($directory . 'config/config.ini', '[config]');
        mkdir($directory . 'views');
        file_put_contents($directory . 'views/index.phtml', '<?= "hello";');
        $this->directory = $directory;

        return $this->directory;
    }

    protected function cleanSource()
    {
        if ($this->directory) {
            $filenames = [
                'build.phar',
                'stub.php',
                'views/index.phtml',
                'views',
                'config/config.ini',
                'config',
                'two.php',
                'one.php',
            ];
            foreach ($filenames as $filename) {
                if (is_dir($this->directory . $filename)) {
                    rmdir($this->directory . $filename);
                } else if (is_file($this->directory . $filename)) {
                    unlink($this->directory . $filename);
                }
            }
            rmdir($this->directory);
            $this->directory = null;
        }
    }

    protected function checkReadonly()
    {
        if (ini_get('phar.readonly')) {
            $this->markTestSkipped('Test skipped because phar writing disabled in php.ini.');
        }
    }

    public function testPlugin()
    {
        $plugin = $this->getPlugin();
        self::assertInstanceOf('PHPCensor\Plugin', $plugin);
        self::assertInstanceOf('PHPCensor\Model\Build', $plugin->getBuild());
        self::assertInstanceOf('PHPCensor\Builder', $plugin->getBuilder());
    }

    public function testFilename()
    {
        $plugin = $this->getPlugin();
        self::assertEquals('build.phar', $plugin->getFilename());

        $plugin = $this->getPlugin(['filename' => 'another.phar']);
        self::assertEquals('another.phar', $plugin->getFilename());
    }

    public function testRegExp()
    {
        $plugin = $this->getPlugin();
        self::assertEquals('/\.php$/', $plugin->getRegExp());

        $plugin = $this->getPlugin(['regexp' => '/\.(php|phtml)$/']);
        self::assertEquals('/\.(php|phtml)$/', $plugin->getRegExp());
    }

    public function testStub()
    {
        $plugin = $this->getPlugin();
        self::assertNull($plugin->getStub());

        $plugin = $this->getPlugin(['stub' => 'stub.php']);
        self::assertEquals('stub.php', $plugin->getStub());
    }

    public function testExecute()
    {
        $this->checkReadonly();

        $plugin = $this->getPlugin();

        self::assertTrue($plugin->execute());

        self::assertFileExists($plugin->getBuilder()->buildPath . 'build.phar');
        PHPPhar::loadPhar($plugin->getBuilder()->buildPath . 'build.phar');
        self::assertFileEquals($plugin->getBuilder()->buildPath . 'one.php', 'phar://build.phar/one.php');
        self::assertFileEquals($plugin->getBuilder()->buildPath . 'two.php', 'phar://build.phar/two.php');
        self::assertFileNotExists('phar://build.phar/config/config.ini');
        self::assertFileNotExists('phar://build.phar/views/index.phtml');
    }

    public function testExecuteRegExp()
    {
        $this->checkReadonly();

        $plugin = $this->getPlugin(['regexp' => '/\.(php|phtml)$/']);

        self::assertTrue($plugin->execute());

        self::assertFileExists($plugin->getBuilder()->buildPath . 'build.phar');
        PHPPhar::loadPhar($plugin->getBuilder()->buildPath . 'build.phar');
        self::assertFileEquals($plugin->getBuilder()->buildPath . 'one.php', 'phar://build.phar/one.php');
        self::assertFileEquals($plugin->getBuilder()->buildPath . 'two.php', 'phar://build.phar/two.php');
        self::assertFileNotExists('phar://build.phar/config/config.ini');
        self::assertFileEquals(
            $plugin->getBuilder()->buildPath . 'views/index.phtml',
            'phar://build.phar/views/index.phtml'
        );
    }

    public function testExecuteStub()
    {
        $this->checkReadonly();

        $content = <<<STUB
<?php
Phar::mapPhar();
__HALT_COMPILER(); ?>
STUB;

        $plugin = $this->getPlugin(['stub' => 'stub.php']);

        file_put_contents($plugin->getBuilder()->buildPath . 'stub.php', $content);

        self::assertTrue($plugin->execute());

        self::assertFileExists($plugin->getBuilder()->buildPath . 'build.phar');
        $phar = new PHPPhar($plugin->getBuilder()->buildPath . 'build.phar');
        self::assertEquals($content, trim($phar->getStub())); // + trim because PHP adds newline char
    }

    public function testExecuteUnknownDirectory()
    {
        $this->checkReadonly();

        $directory = $this->buildTemp();

        $plugin = $this->getPlugin(['directory' => $directory]);

        self::assertFalse($plugin->execute());
    }
}
