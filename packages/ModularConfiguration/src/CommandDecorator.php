<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration;

use ApiGen\ModularConfiguration\Contract\Option\CommandArgumentInterface;
use ApiGen\ModularConfiguration\Contract\Option\CommandBoundInterface;
use ApiGen\ModularConfiguration\Contract\Option\CommandOptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

final class CommandDecorator
{
    /**
     * @var CommandBoundInterface[]
     */
    private $options = [];

    public function addOption(CommandBoundInterface $commandBound): void
    {
        $this->options[$commandBound->getName()] = $commandBound;
    }

    public function decorateCommand(Command $command): void
    {
        foreach ($this->options as $option) {
            if (! $this->isCommandCandidate($option, $command)) {
                continue;
            }

            if ($option instanceof CommandArgumentInterface) {
                $this->addCommandArgument($command, $option);
            }

            if ($option instanceof CommandOptionInterface) {
                $this->addCommandOption($command, $option);
            }
        }
    }

    private function isCommandCandidate(CommandBoundInterface $commandBound, Command $command): bool
    {
        return is_a($command, $commandBound->getCommand());
    }

    private function addCommandArgument(Command $command, CommandArgumentInterface $commandArgument): void
    {
        $command->addArgument(
            $commandArgument->getName(),
            $this->getCommandArgumentMode($commandArgument),
            $commandArgument->getDescription()
        );
    }

    private function getCommandArgumentMode(CommandArgumentInterface $commandArgument): int
    {
        $mode = 0;
        if ($commandArgument->isValueRequired()) {
            $mode |= InputArgument::REQUIRED;
        }

        if ($commandArgument->isArray()) {
            $mode |= InputArgument::IS_ARRAY;
        }

        return $mode;
    }

    private function addCommandOption(Command $command, CommandOptionInterface $commandOption): void
    {
        $command->addOption(
            $commandOption->getName(),
            null,
            $this->getCommandOptionMode($commandOption),
            $commandOption->getDescription(),
            $commandOption->getDefaultValue()
        );
    }

    private function getCommandOptionMode(CommandOptionInterface $commandOption): int
    {
        $mode = 0;
        if ($commandOption->isValueRequired()) {
            $mode |= InputOption::VALUE_REQUIRED;
        }

        return $mode;
    }
}
