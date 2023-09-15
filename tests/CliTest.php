<?php


namespace Phespro\Phespro\Tests;


use Phespro\Phespro\Kernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Console\Tester\CommandTester;

class CliTest extends TestCase
{
    /**
     * @covers \Phespro\Phespro\Kernel
     */
    public function test()
    {
        $check = false;

        $kernel = new Kernel([]);

        $kernel->add(
            'testplugin',
            fn() => new class extends Command
            {
                protected function configure()
                {
                    $this->setName('testcommand');
                }

                protected function execute(InputInterface $input, OutputInterface $output)
                {
                    $output->write('Hello World');

                    return 0;
                }

            },
            ['cli_command'],
        );

        $this->assertTrue(true);
    }
}