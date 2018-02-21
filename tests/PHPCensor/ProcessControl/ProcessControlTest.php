<?php
namespace Tests\PHPCensor\ProcessControl;

/**
 * Some helpers to
 */
abstract class ProcessControlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var resource
     */
    protected $process;

    /**
     * @var array
     */
    protected $pipes;

    /**
     * @var \PHPCensor\ProcessControl\ProcessControlInterface
     */
    protected $object;

    /** Starts a process.
     *
     * @return int The PID of the process.
     */
    protected function startProcess()
    {
        $desc = [["pipe", "r"], ["pipe", "w"], ["pipe", "w"]];
        $this->pipes = [];

        $this->process = proc_open($this->getTestCommand(), $desc, $this->pipes);
        sleep(1);

        self::assertTrue(is_resource($this->process));
        self::assertTrue($this->isRunning());

        $status = proc_get_status($this->process);
        return (integer)$status['pid'];
    }

    /** End the running process.
     *
     * @return int
     */
    protected function endProcess()
    {
        if (!is_resource($this->process)) {
            return;
        }
        array_map('fclose', $this->pipes);
        $exitCode = proc_close($this->process);
        self::assertFalse($this->isRunning());
        $this->process = null;
        return $exitCode;
    }

    /**
     * @return bool
     */
    protected function isRunning()
    {
        if (!is_resource($this->process)) {
            return false;
        }
        $status = proc_get_status($this->process);
        return (boolean)$status['running'];
    }

    public function testIsRunning()
    {
        if (!$this->object->isAvailable()) {
            $this->markTestSkipped();
        }

        $pid = $this->startProcess();

        self::assertTrue($this->object->isRunning($pid));

        fwrite($this->pipes[0], PHP_EOL);

        $exitCode = $this->endProcess();

        self::assertEquals(0, $exitCode);
        self::assertFalse($this->object->isRunning($pid));
    }

    public function testSoftKill()
    {
        if (!$this->object->isAvailable()) {
            $this->markTestSkipped();
        }

        $pid = $this->startProcess();

        self::assertTrue($this->object->kill($pid));
        sleep(1);

        self::assertFalse($this->isRunning());
    }

    public function testForcefullyKill()
    {
        if (!$this->object->isAvailable()) {
            $this->markTestSkipped();
        }

        $pid = $this->startProcess();

        $this->object->kill($pid, true);
        sleep(1);

        self::assertFalse($this->isRunning());
    }

    abstract public function testIsAvailable();

    abstract public function getTestCommand();

    protected function tearDown()
    {
        parent::tearDown();
        $this->endProcess();
    }
}
