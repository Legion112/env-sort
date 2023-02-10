<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'env-group')]
final class EnvGroupCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('base', InputArgument::REQUIRED, 'File with right sort');
    }

    public function execute(InputInterface $input, OutputInterface $output):int
    {
        $base = new DotEnv(file_get_contents($input->getArgument('base')));
        $grouped = $base->group();
        file_put_contents($input->getArgument('base'), $grouped->toString());
        return self::SUCCESS;
    }
}