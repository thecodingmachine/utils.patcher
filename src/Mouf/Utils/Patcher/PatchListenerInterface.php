<?php


namespace Mouf\Utils\Patcher;

/**
 * Listens to events triggered by the PatchService
 */
interface PatchListenerInterface
{
    /**
     * Triggered when the 'reset()' method is called on the PatchService
     */
    public function onReset(): void;

    /**
     * Triggered when one or many patches have been applied.
     *
     * @param PatchInterface[] $patches
     */
    //public function onPatchesApplied(array $patches): void;

}