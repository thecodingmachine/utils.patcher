<?php


namespace Mouf\Utils\Patcher\Dumper;

/**
 * Patches implementing this interface can dump their content (probably SQL) to the output.
 */
interface DumpableInterface
{
    public function setDumper(DumperInterface $dumper);
}