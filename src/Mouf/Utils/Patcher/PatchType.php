<?php
declare(strict_types=1);

namespace Mouf\Utils\Patcher;


class PatchType
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $description;

    /**
     * @param string $name The name of the patch type. Should not contain special characters or spaces. Note: the default type is an empty string.
     * @param string $description The description of the patch type
     * @throws PatchException
     */
    public function __construct(string $name, string $description)
    {
        if (!preg_match('/[^a-z_\-0-9]/i', $name)) {
            throw new PatchException('A patch name can only contain alphanumeric characters and underscore.');
        }

        $this->name = $name;
        $this->description = $description;
    }

    /**
     * The name of the patch type. Should not contain special characters or spaces.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * The description of the patch type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
