<?php

declare(strict_types=1);

namespace Danilocgsilva\Database\Discover;

use PDO;
use Danilocgsilva\Database\Discover;
use Danilocgsilva\Database\Table;
use Danilocgsilva\Database\TableNotFoundException;
use Psr\Log\LoggerInterface;

class Relatory
{
    private Discover $databaseDiscover;

    private int $databaseSize = 0;

    private ?LoggerInterface $logger = null;

    private ?string $alias = null;

    public function __construct(PDO $pdo)
    {
        $this->databaseDiscover = new Discover($pdo);
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;
        return $this;
    }

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;
        return $this;
    }

    public function cliTables(): void
    {
        $tableCount = 0;
        foreach ($this->databaseDiscover->getTables() as $table) {
            $tableCount++;

            $tableData = (new TableData())
                ->setTableName($table->getName())
                ->setTableSize($this->getTableSize($table->getName()))
                ->setRegistersLength($this->databaseDiscover->getRegistersCount($table->getName()));

            print(
                $this->getStringTableData($tableData)
            );
        }

        print(sprintf("\nThe total count of tables is %s.\n",  $tableCount));
        print(sprintf(
            "The size of database is %s.\n",
            $this->formatSizeFromBytes($this->databaseSize)
        ));
    }

    public function cliTablesAndOrderBySize(): void
    {
        $tableCount = 0;
        print("Fetching table data...\n\n");

        $fetchedDataTable = [];

        foreach ($this->databaseDiscover->getTables() as $table) {
            $tableCount++;

            $tableData = (new TableData())
                ->setTableName($table->getName())
                ->setTableSize($this->getTableSize($table->getName()))
                ->setRegistersLength($this->databaseDiscover->getRegistersCount($table->getName()));

            print($this->getStringTableData($tableData));
            $fetchedDataTable[] = $tableData;
        }

        usort($fetchedDataTable, fn ($first, $second) => $first->getTableSize() < $second->getTableSize());

        array_map(function($entry) {
            print($this->getStringTableData($entry));
        }, $fetchedDataTable);

        print(sprintf("\nThe total count of tables is %s.\n", $tableCount));
        print(sprintf(
            "The size of database is %s.\n",
            $this->formatSizeFromBytes($this->databaseSize)
        ));
    }

    /**
     * Prints an html table based on table data
     *
     * @return void
     */
    public function htmlTables(): void
    {
        $tableCount = 0;
        $databaseSize = 0;

        $stringHtml = "<table>";

        $stringHtml .= "<tr> <th>Name</th> <th>Size in disk</th> <th>Count</th> </tr>";

        foreach ($this->databaseDiscover->getTables() as $table) {
            $stringHtml .= $this->generateTr($table);
            $databaseSize += $tableSize = $this->getTableSize($table->getName());
            if ($this->logger) {
                $this->makeLoggerData($table, $tableSize, $tableCount);
            }
            $tableCount++;
        }

        $stringHtml .= "</table>";

        $stringHtml .= "<br /><br /><p>The table size is {$this->formatSizeFromBytes($databaseSize)}</p>";

        print($stringHtml);
    }

    /**
     * Prints an html table based on table data, ordered by table size
     *
     * @return void
     */
    public function orderedHtmlTable(): void
    {
        $tables = iterator_to_array($this->databaseDiscover->getTablesWithSize());
        usort($tables, function ($first, $second) {
            return $first->getSize() <=> $second->getSize();
        });

        $stringHtml = "<table>";

        $stringHtml .= "<tr> <th>Name</th> <th>Size in disk</th> <th>Count</th> </tr>";
        foreach ($tables as $table) {
            $stringHtml .= $this->generateTr($table);
        }

        $stringHtml .= "</table>";

        print($stringHtml);
    }

    private function makeLoggerData($table, $tableSize, $tableCount)
    {
        $loggerMessageString = $table->getName() . ", table number {$tableCount}.";
        $loggerMessageString .= " Table size: " . $this->formatSizeFromBytes($tableSize);
        if ($this->alias) {
            $loggerMessageString .= ", alias: " . $this->alias . ".";
        }
        $this->logger->info($loggerMessageString);
    }

    private function generateTr(Table $rawTable)
    {
        $tableData = (new TableData())
            ->setTableName($rawTable->getName())
            ->setTableSize($this->getTableSize($rawTable->getName()))
            ->setRegistersLength($this->databaseDiscover->getRegistersCount($rawTable->getName()));

        $stringHtml = "<tr>";
        $stringHtml .= "<td>{$tableData->getTableName()}</td>";
        $stringHtml .= "<td>{$this->formatSizeFromBytes($tableData->getTableSize())}</td>";
        $stringHtml .= "<td>" . number_format(
            $tableData->getRegistersLength(),
            0,
            ",",
            "."
        ) . "</td>";
        $stringHtml .= "</tr>";

        return $stringHtml;
    }

    private function getTableSize(string $tableName): int
    {
        try {
            return $this->databaseDiscover->getTableSize($tableName);
        } catch (TableNotFoundException $e) {
            return 0;
        }
    }

    /**
     * Return a friendly string to quickly see the table size um Mbs
     *
     * @param integer $bytes
     * @return string
     */
    private function formatSizeFromBytes(int $bytes): string
    {
        $this->databaseSize += $bytes;
        $tableSizeMb = $bytes / 1024 / 1024;
        return number_format($tableSizeMb, 2, ",", ".") . " Mb";
    }

    private function getStringTableData(TableData $tableData): string
    {
        return sprintf(
            "%s, %s, %s registers.\n",
            $tableData->getTableName(),
            $this->formatSizeFromBytes($tableData->getTableSize()),
            number_format(
                $tableData->getRegistersLength(),
                0,
                ",",
                "."
            )
        );
    }
}
