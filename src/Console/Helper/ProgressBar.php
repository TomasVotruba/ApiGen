<?php declare(strict_types=1);

namespace ApiGen\Console\Helper;

use Symfony\Component\Console\Helper\ProgressBar as ProgressBarHelper;
use Symfony\Component\Console\Output\OutputInterface;

final class ProgressBar
{
    /**
     * @var string
     */
    private const BAR_FORMAT = 'debug';

    /**
     * @var ProgressBarHelper
     */
    private $progressBarHelper;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function init(int $maximum = 1): void
    {
        $this->progressBarHelper = new ProgressBarHelper($this->output, $maximum);
        $this->progressBarHelper->setFormat(self::BAR_FORMAT);
        $this->progressBarHelper->start();
    }

    public function increment(int $increment = 1): void
    {
        if ($this->progressBarHelper === null) {
            return;
        }

        $this->progressBarHelper->advance($increment);
        if ($this->progressBarHelper->getProgress() === $this->progressBarHelper->getMaxSteps()) {
            $this->output->writeln('. <info>done!</info>');
        }
    }
}
