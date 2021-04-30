<?php


namespace Kevas\Filemanager;


class Configuration {

    private array $conf;

    public function __construct(array $conf) {
        $this->conf = $conf;
    }

    /**
     * @return array
     */
    public function getConf(): array {
        return $this->conf;
    }

}