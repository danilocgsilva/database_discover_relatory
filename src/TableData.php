<?php

declare(strict_types=1);

namespace Danilocgsilva\DatabaseDiscoverRelatory;

class TableData
{
    private string $tableName;
    private int $tableSize;
    private int $registersLength;


    /**
     * Get the value of registersLength
     */ 
    public function getRegistersLength(): int
    {
        return $this->registersLength;
    }

    /**
     * Set the value of registersLength
     *
     * @return  self
     */ 
    public function setRegistersLength(int $registersLength): self
    {
        $this->registersLength = $registersLength;

        return $this;
    }

    /**
     * Get the value of tableSize
     */ 
    public function getTableSize(): int
    {
        return $this->tableSize;
    }

    /**
     * Set the value of tableSize
     *
     * @return  self
     */ 
    public function setTableSize(int $tableSize): self
    {
        $this->tableSize = $tableSize;

        return $this;
    }

    /**
     * Get the value of tableName
     */ 
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Set the value of tableName
     *
     * @return  self
     */ 
    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }
}
