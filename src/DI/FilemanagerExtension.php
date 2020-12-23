<?php


namespace Kevas\Filemanager\DI;

use Nette;
use Easyadmin;
use Nette\Application\IPresenterFactory;
use Nette\Schema\Expect;

class FilemanagerExtension extends Nette\DI\CompilerExtension {

    public function loadConfiguration() {
        $this->compiler->loadDefinitionsFromConfig(
            $this->loadFromFile(__DIR__ . '/../conf/filemanager.neon')['services']
		);
    }

}