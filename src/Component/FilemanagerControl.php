<?php


namespace Kevas\Filemanager\Component;

use Kevas\Filemanager\BasePath;
use Kevas\Filemanager\Configuration;
use Kevas\Filemanager\Exception\FilemanagerException;
use Nette\Application\UI\Control;
use Nette\Http\FileUpload;
use Nette\IOException;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use Nette\Utils\Html;
use Nette\Utils\Image;
use Nette\Utils\ImageException;
use Nette\Utils\UnknownImageFileException;
use SplFileInfo;
use Nette;

class FilemanagerControl extends Control {

    /** @persistent */
    public ?string $path = null;

    /** @persistent */
    public ?string $selectedFile = null;

    /** @persistent */
    public ?string $idFile = null;

    /** @persistent */
    public ?string $selectedFolder = null;

    /** @persistent */
    public ?string $idFolder = null;

    /** @persistent */
    public ?string $ckeditor = null;

    /** @persistent */
    public ?string $load = null;

    /** @persistent */
    public ?string $CKEditorFuncNum = null;

    private BasePath $basePath;

    private ?Nette\Http\IRequest $request = null;

    private string $uploadDir;

    private string $lang;

    private array $messages;

    private ?string $initPath = null;

    private string $thumbDir = '__thumb__';

    private string $nameRootUploadedDir = 'user_uploads';

    private Configuration $configuration;

    /**
     * FilemanagerControl constructor.
     * @param BasePath $basePath
     * @param Configuration $configuration
     */
    public function __construct(BasePath $basePath, Configuration $configuration)
    {
        $this->basePath = $basePath;
        $this->configuration = $configuration;

        $this->monitor(Nette\Application\UI\Presenter::class, function () {

            $presenter = $this->getPresenter();

            if(!$presenter) {
                $this->error();
            }

            $this->request = $presenter->getHttpRequest();
        });
    }

    /**
     * @param array $params
     * @throws Nette\Application\BadRequestException
     */
    public function loadState(array $params): void
    {
        $init = $this->request->getQuery('init');
        $initCkeditor = $this->request->getQuery('initCkeditor');

        if(!is_null($initCkeditor)) {
            $params['path'] = $this->initPath;
            $params['CKEditorFuncNum'] = $this->request->getQuery('CKEditorFuncNum');
        }

        if(!is_null($init)) {
            $params['path'] = $this->initPath;

            foreach(['selectedFile', 'selectedFolder'] as $nameParam) {

                if(isset($params[$nameParam])) {

                    $dir = pathinfo($params[$nameParam], PATHINFO_DIRNAME);

                    if (!empty($dir)) {
                        $dir = str_replace($this->uploadDir, '', $dir);
                        $params['path'] = ltrim($this->removeMultipleSlashes($dir), '/');
                    }
                }
            }

        }

        if(isset($params['selectedFolder']) && !empty($params['selectedFolder']) &&
            strpos($params['selectedFolder'], $this->nameRootUploadedDir) === false) {
            $params['selectedFolder'] = '';
            $params['path'] = null;
        }

        parent::loadState($params);
    }

    /**
     * @param string $uploadDir
     * @return FilemanagerControl
     */
    public function setUploadDir(string $uploadDir): FilemanagerControl
    {
        $this->uploadDir = $uploadDir;
        return $this;
    }

    /**
     * @param string $lang
     * @return FilemanagerControl
     */
    public function setLang(string $lang): FilemanagerControl {
        $this->lang = $lang;
        return $this;
    }

    /**
     * @param string|null $initPath
     * @return FilemanagerControl
     */
    public function setInitPath(?string $initPath): FilemanagerControl
    {

        if(!empty($initPath) && is_dir($this->removeMultipleSlashes($this->getSearchInDir() . '/' . $initPath))) {
            $this->initPath = $initPath;
        }

        return $this;
    }

    /**
     * @Handle
     * @param string $dirName
     * @throws Nette\Application\AbortException
     */
    public function handleChildDir(string $dirName): void
    {
        $this->path = (!empty($this->path) ? $this->path . '/' : '') . $dirName;
        $this->redirect('this');
    }

    /**
     * @Handle
     * @throws Nette\Application\AbortException
     */
    public function handleParentDir(): void
    {
        $path = $this->path;
        $pathArr = array_filter(explode('/', $path));
        $countPath = count($pathArr);

        if($countPath < 1) {
            $path = '';
        } else {
            array_pop($pathArr);
            $path = ($countPath > 1 ? implode('/', $pathArr) : '');
        }

        $this->path = $path;

        $this->redirect('this');
    }

    /**
     * @Handle
     * @param int $i
     * @throws Nette\Application\AbortException
     */
    public function handleBreadCrumb(int $i): void
    {
        if($i === 0) {
            $this->path = null;
            $this->redirect('this');
        }

        $pathArr = explode('/', $this->path);
        $pathArrSlice = array_slice($pathArr, 0, ($i*-1));

        if(!empty($pathArrSlice)) {
            $this->path = implode('/', $pathArrSlice);
        } else {
            $this->path = null;
        }

        $this->redirect('this');
    }

    public function handleDropzone(): void
    {
        $presenter = $this->getPresenter();

        if($presenter !== null && $presenter->getRequest() !== null) {
            $files = $presenter->getRequest()->getFiles();

            if(isset($files['file'])) {

                /** @var FileUpload $file */
                $file = $files['file'];

                if(!$file->getError()) {
                    $dir = $this->removeMultipleSlashes($this->getSearchInDir() . '/' . $this->path . '/');
                    $file->move($dir . $file->getUntrustedName());
                }

            }
        }

        exit;
    }

    /**
     * @throws FilemanagerException
     * @throws Nette\Application\AbortException
     */
    public function handleCreateDir(): void
    {
        $name = $this->request->getPost('folderName');
        $dir = $this->removeMultipleSlashes($this->getSearchInDir() . '/' . $this->path . '/');

        if(!empty($name)) {

            try {
                FileSystem::createDir($dir . $name);
            } catch (IOException $e) {
                throw new FilemanagerException('Failed to create directory');
            }

        }

        $this->redirect('this');

    }

    /**
     * @throws FilemanagerException
     * @throws Nette\Application\AbortException
     */
    public function handleEditDir(): void
    {
        $oldName = $this->request->getPost('oldEditName');
        $newName = $this->request->getPost('editName');
        $ext = $this->request->getPost('ext');

        $dir = $this->removeMultipleSlashes($this->getSearchInDir() . '/' . $this->path . '/');

        if(!empty($oldName) && !empty($newName)) {

            $ext = (!empty($ext) ? '.' . $ext : '');

            $oldFilename = $dir . $oldName . $ext;
            $newFilename = $dir . $newName . $ext;

            try {
                FileSystem::rename($oldFilename, $newFilename);
            } catch (IOException $e) {
                throw new FilemanagerException($e->getMessage());
            }

        }

        $this->redirect('this');
    }

    /**
     * @throws FilemanagerException
     * @throws Nette\Application\AbortException
     */
    public function handleRemove(): void
    {
        $name = $this->request->getPost('removeName');

        if(!empty($name)) {

            $dir = $this->removeMultipleSlashes($this->getSearchInDir() . '/' . $this->path . '/');

            try {
                FileSystem::delete($dir . $name);
            } catch (IOException $e) {
                throw new FilemanagerException($e->getMessage());
            }
        }

        $this->redirect('this');
    }

    public function render(): void {
        $this->addLatteFunction();

        $langMessageFile = __DIR__ . '/../messages/' . $this->lang . '.json';

        if(!file_exists($langMessageFile)) {
            $langMessageFile = __DIR__ . '/../messages/en.json';
        }

        try {
            $this->messages = (array) Nette\Utils\Json::decode(FileSystem::read($langMessageFile));
        } catch (Nette\Utils\JsonException $e) {
        }

        $searchInDir = $this->getSearchInDir() . '/' . $this->path;

        $dirs = Finder::findDirectories('*')->in($searchInDir)->exclude($this->thumbDir);
        $files = Finder::findFiles('*')->exclude('.*')->in($searchInDir);

        $dirItems = $this->sortItems($dirs);
        $fileItems = $this->sortItems($files);

        $this->template->render(__DIR__ . '/../template/index.latte', [
            'dirItems' => $dirItems,
            'fileItems' => $fileItems,
            'canInsertFile' => (!empty($this->idFile) || !empty($this->ckeditor)),
            'canInsertDir' => (!empty($this->idFolder)),
            'paths' => array_filter(explode('/', $this->path)),
            'conf' => $this->configuration->getConf(),
            'searchInDir' => $searchInDir
        ]);
    }

    private function addLatteFunction(): void
    {
        $latte = $this->template->getLatte();

        $latte->addFunction('getMessage', function ($text) {
            return $this->messages[$text] ?? $text;
        });

        $latte->addFunction('getDataDir', function (SplFileInfo $dir) {
            return $this->getDataDir($dir);
        });

        $latte->addFunction('canDisplayParentDir', function () {
            return $this->canDisplayParentDir();
        });

        $latte->addFunction('getClassNameActiveRow', function (SplFileInfo $file, string $nameParam) {
            return $this->getClassNameActiveRow($file, $nameParam);
        });

        $latte->addFunction('getDataFile', function (SplFileInfo $file) {
            return $this->getDataFile($file);
        });

        $latte->addFunction('getFileIcon', function (SplFileInfo $file) {
            return $this->getFileIcon($file);
        });

        $latte->addFunction('getLink', function (SplFileInfo $file) {
            return $this->getLink($file);
        });

    }

    /**
     * @param $items
     * @return array
     */
    private function sortItems($items): array
    {
        /** @var SplFileInfo $item */
        $newItems = [];

        foreach($items as $item) {
            $newItems[$item->getFilename()] = $item;
        }

        ksort($newItems,  SORT_NATURAL);

        return $newItems;
    }

    /**
     * @param SplFileInfo $folder
     * @return string
     */
    private function getDataDir(SplFileInfo $folder): string {
        $dataDir = '';

        try {
            $dataDir = Nette\Utils\Json::encode([
                 'path' => str_replace(
                     $this->basePath->getWwwDir(),
                     '',
                     $this->basePath->changeBackSlashes($folder->getRealPath())
                 )
            ]);
        } catch (Nette\Utils\JsonException $e) {
        }

        return $dataDir;
    }

    /**
     * @return bool
     */
    private function canDisplayParentDir(): bool {
        $pathArr = array_filter(explode('/', $this->path));
        return (count($pathArr) > 0);
    }

    /**
     * @param SplFileInfo $file
     * @param string $nameParam
     * @return string
     */
    private function getClassNameActiveRow(SplFileInfo $file, string $nameParam): string {
        $filename = str_replace(
            $this->basePath->getWwwDir(), '', $this->basePath->changeBackSlashes($file->getRealPath())
        );

        return (
            isset($this->$nameParam) &&
            !empty($this->$nameParam) &&
            $this->$nameParam === $filename ? 'active' : '');
    }

    /**
     * @param SplFileInfo $file
     * @return string
     */
    private function getDataFile(SplFileInfo $file): string {
        $dataFile = '';
        $realPath = $this->basePath->changeBackSlashes($file->getRealPath());

        try {
            $dataFile = Nette\Utils\Json::encode([
                'filename' => $file->getFilename(),
                'pathFilename' => str_replace($this->basePath->getWwwDir(), '', $realPath),
                'extension' => $file->getExtension()
            ]);
        } catch (Nette\Utils\JsonException $e) {
        }

        return $dataFile;
    }

    /**
     * @param SplFileInfo $file
     * @return Html|string
     * @throws UnknownImageFileException
     * @throws ImageException
     */
    private function getFileIcon(SplFileInfo $file) {
        $extension = strtolower($file->getExtension());

        if($this->isFileImage($file)) {
            $fileIcon = $this->getImageThumb($file);
        } elseif($extension === 'xls' || $extension === 'xlsx') {
            $fileIcon = '<i class="far fa-file-excel"></i>';
        } elseif($extension === 'doc') {
            $fileIcon = '<i class="far fa-file-word"></i>';
        } elseif($extension === 'pdf') {
            $fileIcon = '<i class="far fa-file-pdf"></i>';
        } elseif($extension === 'zip') {
            $fileIcon = '<i class="far fa-file-archive"></i>';
        } elseif($extension === 'txt') {
            $fileIcon = '<i class="far fa-file-alt"></i>';
        } else {
            $fileIcon = '<i class="far fa-file"></i>';
        }

        return $fileIcon;

    }

    /**
     * @param SplFileInfo $file
     * @return bool
     */
    private function isFileImage(SplFileInfo $file): bool {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bnp', 'webp', 'bmp'];
        return (in_array(strtolower($file->getExtension()), $imageExtensions));
    }

    /**
     * @param SplFileInfo $file
     * @return Html
     * @throws ImageException
     * @throws UnknownImageFileException
     */
    private function getImageThumb(SplFileInfo $file): Html {
        $width = 45;
        $height = 45;

        $thumbDir = rtrim($this->thumbDir) . '/';
        $ext = $file->getExtension();

        $thumbFilename =  md5($file->getFilename() . $file->getPath() . $width . $height . $ext) . '.' . $ext;
        $thumbImage = $this->removeMultipleSlashes($this->getSearchInDir() . '/' . $thumbDir . $thumbFilename);

        FileSystem::createDir($this->getSearchInDir() . '/' . $thumbDir);

        if(!file_exists($thumbImage)) {
            $srcImage = $this->removeMultipleSlashes($file->getPath() . '/' . $file->getFilename());

            $image = Image::fromFile($srcImage);
            $image->resize($width, $height, Image::SHRINK_ONLY | Image::EXACT);
            $image->save($thumbImage, 80, Image::PNG);
        }

        $wwwThumbImage = str_replace($this->basePath->getWwwDir(), '', $thumbImage);

        return Html::el('img')->setAttribute('src', $wwwThumbImage);
    }

    /**
     * @param SplFileInfo $file
     * @return string
     * @throws ImageException
     * @throws UnknownImageFileException
     */
    private function getLink(SplFileInfo $file): string {
        $realPath = $this->basePath->changeBackSlashes($file->getRealPath());

        $link = str_replace($this->basePath->getWwwDir(), '', $realPath);

        if($this->isFileImage($file)) {
            $filename = Html::el('a')->addAttributes([
                'onclick' => 'window.open(\'' . $link . '\', \'_blank\', \'width=800,height=500\');',
                'href' => '#'
            ]);
        } else {
            $filename = Html::el('a')->addAttributes([
                'href' => $link,
                'target' => '_blank'
            ]);
        }

        $icon = $this->getFileIcon($file);

        return $filename->setHtml($icon)->render();
    }

    /**
     * @return string
     */
    private function getSearchInDir(): string {
        return $this->removeMultipleSlashes($this->basePath->getWwwDir() . '/' . $this->uploadDir);
    }

    /**
     * @param string $filename
     * @return string
     */
    private function removeMultipleSlashes(string $filename): string {
        return preg_replace('#/+#', '/', $filename);
    }

}