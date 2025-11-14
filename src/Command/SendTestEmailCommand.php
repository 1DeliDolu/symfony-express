<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(name: 'app:send-test-email', description: 'Send a synchronous test email using the configured transport')]
final class SendTestEmailCommand extends Command
{
    public function __construct(private readonly TransportInterface $transport)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('no-reply@localhost')
            ->to('test@example.com')
            ->subject('Test email from app:send-test-email')
            ->text('This is a test message to verify mailer transport.');

        try {
            $sent = $this->transport->send($email);
            $output->writeln('Email sent synchronously via transport.');
            if (method_exists($sent, 'getMessageId')) {
                $output->writeln('Message ID: ' . $sent->getMessageId());
            }
        } catch (\Throwable $e) {
            $output->writeln('<error>Failed to send test email: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
