<?php

namespace App\BarBundle\Tests\Functional;


use App\ChainCommandBundle\Exceptions\CantBeExecutedException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ChainCommandTest extends KernelTestCase
{
    private Application $application;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->application = new Application($kernel);
    }

    public function testChainWorks(): void
    {
        $output = new BufferedOutput();
        $this->application->doRun((new ArrayInput(['command' => 'foo:hello'])), $output);

        $outputContent = $output->fetch();

        $this->assertStringContainsString('Hello from Foo!', $outputContent);
        $this->assertStringContainsString('Hi from Bar!', $outputContent);
    }

    public function testShouldNotRunChainMember(): void
    {
        $this->expectException(CantBeExecutedException::class);
        $this->expectExceptionMessage('Error: bar:hi command is a member of foo:hello command chain and cannot be executed on its own.');

        $this->application->doRun((new ArrayInput(['command' => 'bar:hi'])), (new BufferedOutput()));
    }
}
