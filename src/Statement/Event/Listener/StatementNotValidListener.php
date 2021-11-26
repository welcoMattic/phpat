<?php

declare(strict_types=1);

namespace PhpAT\Statement\Event\Listener;

use PhpAT\App\ErrorStorage;
use PhpAT\Rule\Baseline;
use PHPAT\EventDispatcher\EventInterface;
use PHPAT\EventDispatcher\EventListenerInterface;
use PhpAT\Rule\RuleContext;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAT\Statement\Event\StatementNotValidEvent;

class StatementNotValidListener implements EventListenerInterface
{
    private OutputInterface $output;
    private Baseline $baseliner;

    public function __construct(OutputInterface $output, Baseline $baseliner)
    {
        $this->output = $output;
        $this->baseliner = $baseliner;
    }

    /**
     * @psalm-suppress MoreSpecificImplementedParamType
     * @param StatementNotValidEvent $event
     */
    public function __invoke(EventInterface $event)
    {
        if ($this->baseliner->compensateError(RuleContext::ruleName(), $event->getMessage())) {
            return;
        }

        $this->output->write('X', false, OutputInterface::VERBOSITY_VERBOSE);
        $this->output->writeln(' ' . $event->getMessage(), OutputInterface::VERBOSITY_VERY_VERBOSE);
        ErrorStorage::addRuleError($event->getMessage());
    }
}
