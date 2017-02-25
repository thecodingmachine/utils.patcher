<?php
declare(strict_types=1);

namespace Mouf\Utils\Patcher;


use Mouf\MoufManager;

class PatchType implements \JsonSerializable
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
     * @Important
     * @param string $name The name of the patch type. Should not contain special characters or spaces. Note: the default type is an empty string.
     * @param string $description The description of the patch type
     * @throws PatchException
     */
    public function __construct(string $name, string $description)
    {
        if (!preg_match('/^[a-z_\-0-9]*$/i', $name)) {
            throw new PatchException('A patch name can only contain alphanumeric characters and underscore. Name passed: "'.$name.'"');
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

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'instanceName' => MoufManager::getMoufManager()->findInstanceName($this)
        ];
    }
}
