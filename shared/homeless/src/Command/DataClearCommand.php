<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Command;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(name: 'homeless:data:clear')]
class DataClearCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly KernelInterface $kernel,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Удаление всех данных приложения')
            ->addOption('delete', null, InputOption::VALUE_NONE, 'Будет использовано DELETE FROM вместо TRUNCATE TABLE')
        ;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $text = '<error>ВНИМАНИЕ!<error> <question>Все данные приложения будут безвозвратно удалены! Вы уверены? (y/n)</question>';
        $question = new ConfirmationQuestion($text, false);

        if (!$input->getOption('no-interaction') && !$helper->ask($input, $output, $question)) {
            return;
        }

        $conn = $this->entityManager->getConnection();

        $command = $input->getOption('delete') ? 'DELETE FROM ' : 'TRUNCATE TABLE ';

        $queries = $conn
            ->executeQuery("
                SELECT Concat('{$command}',table_schema,'.',TABLE_NAME, ';')
                FROM INFORMATION_SCHEMA.TABLES
                WHERE table_schema = 'homeless';
            ")
            ->fetchFirstColumn()
        ;

        $conn->executeQuery('SET FOREIGN_KEY_CHECKS=0');

        foreach ($queries as $query) {
            echo $query."\r\n";
            $conn->executeQuery($query);
        }

        $conn->executeQuery('SET FOREIGN_KEY_CHECKS=1');

        $fs = new Filesystem();
        $projectDir = $this->kernel->getProjectDir();

        echo "Удаление загруженных файлов\r\n";
        $fs->remove($projectDir.'/web/uploads');

        $output->writeln('<info>Удаление данных завершено</info>');
    }
}
