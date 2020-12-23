<?php


namespace Kevas\Filemanager\Component;


interface IFilemanagerControlFactory {

    public function create(): FilemanagerControl;

}
