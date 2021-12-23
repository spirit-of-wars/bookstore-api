<?php

namespace App\Util\Common;

/**
 * Trait MessageKeeperTrait
 * @package App\Util\Common
 */
trait MessageKeeperTrait
{
    /** @var array */
    protected $messages;

    /** @var bool */
    protected $hasErrors;

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return $this->hasErrors;
    }

    /**
     * @param string $row
     */
    protected function addTitle($row)
    {
        $this->messages[] = '<options=bold>' . $row . '</>';
    }

    /**
     * @param string $row
     */
    protected function addTextRow($row)
    {
        if (!preg_match('/^>>>/', $row)) {
            $row = '>>> ' . $row;
        }
        $this->messages[] = $row;
    }

    /**
     * @param string $row
     */
    protected function addErrorRow($row)
    {
        $this->hasErrors = true;
        $this->addTextRow('<fg=red>' . $row . '</>');
    }

    /**
     * @param array $rows
     */
    protected function addTextRows($rows)
    {
        foreach ($rows as $row) {
            $this->addTextRow($row);
        }
    }

    /**
     * @param array $rows
     */
    protected function addErrorRows($rows)
    {
        foreach ($rows as $row) {
            $this->addErrorRow($row);
        }
    }
}
