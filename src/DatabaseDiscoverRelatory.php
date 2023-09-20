<?php

declare(strict_types=1);

namespace Danilocgsilva\DatabaseDiscoverRelatory;

use PDO;
use Danilocgsilva\DatabaseDiscover\DatabaseDiscover;

class DatabaseDiscoverRelatory
{
    private DatabaseDiscover $databaseDiscover;

    private int $tablesCount = 0;

    private int $databaseSize = 0;

    public function __construct(PDO $pdo) {
        $this->databaseDiscover = new DatabaseDiscover($pdo);
    }

    public function cliTables(): void
    {
        foreach ($this->databaseDiscover->getTables() as $table) {
            $this->tablesCount++;
            print(sprintf(
                "%s, %s, %s registers.\n",
                ($tableName = $table->getName()),
                $this->formatTableSize($tableName),
                $this->databaseDiscover->getRegistersCount($tableName)
            ));
        }

        print(sprintf("\nThe total count of tables is %s.\n", $this->tablesCount));
        print(sprintf(
            "The size of database is %s.\n", 
            $this->formatSizeFromBytes($this->databaseSize)
        ));
    }

    private function formatTableSize(string $tableName): string
    {
        $bytes = $this->databaseDiscover->getTableSize($tableName);
        return $this->formatSizeFromBytes($bytes);
    }

    private function formatSizeFromBytes(int $bytes): string
    {
        $this->databaseSize += $bytes;
        $tableSizeMb = $bytes / 1024 / 1024;
        return number_format($tableSizeMb, 2, ",", ".") . " Mb";
    }
}
