<?php

declare(strict_types=1);

namespace Danilocgsilva\DatabaseDiscoverRelatory;

use PDO;
use Danilocgsilva\DatabaseDiscover\DatabaseDiscover;

class DatabaseDiscoverRelatory
{
    private DatabaseDiscover $databaseDiscover;

    private int $databaseSize = 0;

    public function __construct(PDO $pdo)
    {
        $this->databaseDiscover = new DatabaseDiscover($pdo);
    }

    public function cliTables(): void
    {
        $tableCount = 0;
        foreach ($this->databaseDiscover->getTables() as $table) {
            $tableCount++;

            print(
                $this->getStringTableData(
                    (new TableData())
                        ->setTableName($table->getName())
                        ->setTableSize($this->getTableSize($table->getName()))
                        ->setRegistersLength($this->databaseDiscover->getRegistersCount($table->getName()))
                )
            );
        }

        print(sprintf("\nThe total count of tables is %s.\n",  $tableCount));
        print(sprintf(
            "The size of database is %s.\n",
            $this->formatSizeFromBytes($this->databaseSize)
        ));
    }

    public function cliTablesAndOrderBySize()
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

    private function getTableSize(string $tableName): int
    {
        return $this->databaseDiscover->getTableSize($tableName);
    }

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
