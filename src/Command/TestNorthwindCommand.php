<?php

namespace App\Command;

use App\Repository\Northwind\EmployeesRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-northwind',
    description: 'Test Northwind database connection and repository',
)]
class TestNorthwindCommand extends Command
{
    public function __construct(
        private readonly EmployeesRepository $employeesRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $io->title('Testing Northwind Database Connection');

            $io->section('Fetching all employees...');
            $employees = $this->employeesRepository->findAll();

            $io->success(sprintf('Found %d employees', count($employees)));

            $io->table(
                ['ID', 'First Name', 'Last Name', 'Title', 'City'],
                array_map(fn($emp) => [
                    $emp->getId(),
                    $emp->getFirstName(),
                    $emp->getLastName(),
                    $emp->getTitle() ?? '-',
                    $emp->getCity() ?? '-',
                ], $employees)
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error: ' . $e->getMessage());
            $io->text('Stack trace:');
            $io->text($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
