<?php

declare(strict_types=1);

namespace Danilocgsilva\DatabaseDiscoverRelatory;

use PDO;
use Danilocgsilva\DatabaseDiscover\DatabaseDiscover;

class Compare
{
    private array $databaseDiscoveries = [];

    public function __construct(PDO $pdo1, PDO $pdo2) {
        $this->databaseDiscoveries[] = new DatabaseDiscover($pdo1);
        $this->databaseDiscoveries[] = new DatabaseDiscover($pdo2);
    }

    public function getCliData()
    {
        $index = 1;
        $totalIndexes = count($this->databaseDiscoveries);
        foreach ($this->databaseDiscoveries as $databaseDiscover) {
            print("Fetching data from index {$index} of {$totalIndexes}");

            foreach ($databaseDiscover->getTables() as $table) {

                $tableSize = $databaseDiscover->getTableSize(
                    $table->getName()
                );

                $registersCount = $databaseDiscover->getRegistersCount(
                    $table->getName()
                );
                
                $tableData = (new TableData())
                    ->setTableName($table->getName())
                    ->setTableSize($tableSize)
                    ->setTableSize($registersCount);
            }

            $index++;
        }
    }
}
