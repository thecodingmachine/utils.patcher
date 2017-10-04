<?php


namespace Mouf\Utils\Patcher\Dumper;


interface DumperInterface
{
    public function dumpPatch(string $code): void;
}
