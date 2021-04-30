<?php


namespace Kevas\Filemanager\DI;

use Kevas\Filemanager\Configuration;
use Nette;
use Easyadmin;
use Nette\Schema\Expect;

class FilemanagerExtension extends Nette\DI\CompilerExtension {

    public function getConfigSchema(): Nette\Schema\Schema {
        return Expect::structure([
            'maxFilesize' => Expect::int()->default(2),
        ]);
    }

    public function loadConfiguration() {
        $this->compiler->loadDefinitionsFromConfig(
            $this->loadFromFile(__DIR__ . '/../conf/filemanager.neon')['services']
		);

        $builder = $this->getContainerBuilder();
        $builder->addDefinition($this->prefix('filemanager.configuration'))
            ->setFactory(Configuration::class, [
                (array) $this->config
            ]);
    }

}