<?php

namespace Kevas\Filemanager;

use Kevas\Filemanager\Exception\FilemanagerException;
use Nette\Utils\Finder;
use Nette\IOException;
use Nette\Utils\Html;
use Nette\Utils\ImageException;
use Nette\Utils\UnknownImageFileException;
use SplFileInfo;
use Latte;
use Nette\Utils\Image;
use Nette\Utils\FileSystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Filemanager {

    /**
     * @var Latte\Engine
     */
    private $latte;

    /**
     * @var string
     */
    private $uploadDir;

    /**
     * @var string
     */
    private $documentRootDir;

    /**
     * @var string
     */
    private $uploadDirFullPath;

    public function __construct() {
        $this->latte = new Latte\Engine;
        $this->latte->setTempDirectory($this->getTempDirLatte());
        $this->latte->setAutoRefresh(false);
    }

    /**
     * @return string
     */
    public function getUploadDir(): ?string {
        return $this->uploadDir;
    }

    /**
     * @param string $documentRootDir
     * @return Filemanager
     */
    public function setDocumentRootDir(string $documentRootDir): Filemanager {
        $this->documentRootDir = $documentRootDir;
        return $this;
    }

    /**
     * @param string $uploadDir
     * @return Filemanager
     */
    public function setUploadDir(string $uploadDir): Filemanager {
        $this->uploadDir = $uploadDir;
        return $this;
    }

    /**
     * @return string
     * @throws FilemanagerException
     */
    public function render() {
        $this->init();
        $this->createThumbDir();

        $request = Request::createFromGlobals();
        $pathRequest = $request->get('path', '');
        $fileRequest = $request->get('file', '');
        $insertFileRequest = $request->get('insertFile', '');

        if(!empty($fileRequest) && !empty($insertFileRequest)) {
            $fileRequestDirname = pathinfo($fileRequest)['dirname'];
            $pathRequest = trim($this->removeMultipleSlashes(str_replace($this->uploadDir, '', $fileRequestDirname)), '/');
        }

        $this->filesUploadAction($request);

        $searchInDir = $this->uploadDirFullPath . '/' . $pathRequest;

        $this->createNewDirAction($searchInDir, $pathRequest, $request);
        $this->renameAction($searchInDir, $pathRequest, $request);
        $this->removeAction($searchInDir, $pathRequest, $request);

        if(!is_dir($searchInDir)) {
            $searchInDir = $this->uploadDirFullPath;
            $pathRequest = '';
        }

        $breadCrumbs = array_filter(explode('/', $pathRequest));

        $this->addLatteFunction($pathRequest, $request, $breadCrumbs);

        $dirs = Finder::findDirectories('*')->in($searchInDir)->exclude($this->getNameThumbDir());
        $files = Finder::findFiles('*')->in($searchInDir);

        return $this->latte->renderToString($this->getTemplateDir() . '/index.latte', [
            'dirs' => $dirs,
            'files' => $files,
            'breadCrumbs' => $breadCrumbs,
            'homeBreadCrumbParams' => '?path=' .  $this->getInsertFileUriParam($request),
            'isInsertFileParam' => (!empty($request->get('insertFile', ''))),
            'searchInDir' => $searchInDir,
        ]);
    }

    /**
     * @param string $pathRequest
     * @param Request $request
     * @param array $breadCrumbs
     */
    protected function addLatteFunction(string $pathRequest, Request $request, array $breadCrumbs) {
        $this->latte->addFunction('getParamsUrlDir', function (SplFileInfo $file) use ($pathRequest, $request) {
            return $this->getParamsUrlDir($file, $pathRequest, $request);
        });

        $this->latte->addFunction('getFileIcon', function (SplFileInfo $file) {
            return $this->getFileIcon($file);
        });

        $this->latte->addFunction('getHumanFileSize', function (SplFileInfo $file) {
            return $this->getHumanFileSize($file->getMTime());
        });

        $this->latte->addFunction('displayParentDir', function () use ($pathRequest) {
            return $this->displayParentDir($pathRequest);
        });

        $this->latte->addFunction('getParamsParentUrlDir', function () use ($pathRequest, $request) {
            return $this->getParamsParentUrlDir($pathRequest, $request);
        });

        $this->latte->addFunction('getDropzoneUrl', function () use ($request, $pathRequest) {
            return $request->getPathInfo() . '?fileupload=1&path=' . $pathRequest;
        });

        $this->latte->addFunction('getPathUrl', function () use ($request, $pathRequest) {
            return $request->getPathInfo() . '?path=' . $pathRequest;
        });

        $this->latte->addFunction('getBreadCrumbItem', function ($breadCrumbValue) use ($breadCrumbs, $request) {
            return $this->getBreadCrumbItem($breadCrumbValue, $breadCrumbs, $request);
        });

        $this->latte->addFunction('getFilename', function (SplFileInfo $file) {
            return $this->getFilename($file);
        });

        $this->latte->addFunction('getDataFile', function (SplFileInfo $file) {
            return $this->getDataFile($file);
        });
    }

    /**
     * @param SplFileInfo $file
     * @return string
     */
    protected function getDataFile(SplFileInfo $file): string {
        return json_encode([
            'filename' => $file->getFilename(),
            'absoluteFilename' => $file->getRealPath(),
            'pathFilename' => str_replace($this->documentRootDir, '', $file->getRealPath()),
            'extension' => $file->getExtension()
        ]);
    }

    /**
     * @param SplFileInfo $file
     * @param string $pathRequest
     * @param Request $request
     * @return string
     */
    protected function getParamsUrlDir(SplFileInfo $file, string $pathRequest, Request $request): string {
        $path = urlencode((!empty($pathRequest) ? $pathRequest . '/' : '') . $file->getFilename());
        return '?path=' . $path . $this->getInsertFileUriParam($request);
    }

    /**
     * @param string $path
     * @param Request $request
     * @return string
     */
    protected function getParamsParentUrlDir(string $path, Request $request): string {
        $pathArr = array_filter(explode('/', $path));
        $countPath = count($pathArr);

        if($countPath < 1) {
            $path = '';
        } else {
            array_pop($pathArr);
            $path = ($countPath > 1 ? implode('/', $pathArr) : '');
        }

        return '?path=' . $path . $this->getInsertFileUriParam($request);
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function getInsertFileUriParam(Request $request): string {
        $insertFileParam = $request->get('insertFile', '');
        return (!empty($insertFileParam) ? '&insertFile=' . $insertFileParam: '');
    }

    /**
     * @param SplFileInfo $file
     * @return string
     */
    protected function getFilename(SplFileInfo $file): string {

        $link = str_replace($this->documentRootDir, '', $file->getPathname());

        if($this->isImageFileType($file)) {
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

        return $filename->setText($file->getFilename())->render();
    }

    /**
     * @param string $breadCrumbValue
     * @param array $breadCrumbs
     * @param Request $request
     * @return string
     */
    protected function getBreadCrumbItem(string $breadCrumbValue, array $breadCrumbs, Request $request): string {

        if(empty($breadCrumbs)) {
            return '';
        }

        $lastItem = end($breadCrumbs);

        if($lastItem == $breadCrumbValue) {
            return Html::el('span')->setText($breadCrumbValue)->render();
        }

        $path = '';

        foreach($breadCrumbs as $breadCrumb) {

            $path .= $breadCrumb . '/';
            if($breadCrumb == $breadCrumbValue) {
                break;
            }
        }

        $path = rtrim($path, '/');
        return Html::el('a')
            ->setAttribute('href', '?path=' . $path . $this->getInsertFileUriParam($request))
            ->setText($breadCrumbValue)->render();
    }

    /**
     * @param SplFileInfo $file
     * @return Html|string
     * @throws UnknownImageFileException
     * @throws ImageException
     */
    protected function getFileIcon(SplFileInfo $file) {
        $extension = $file->getExtension();

        if($this->isImageFileType($file)) {
            $fileIcon = $this->getImageThumb($file);
        } else if($extension == 'xls' || $extension == 'xlsx') {
            $fileIcon = '<i class="far fa-file-excel"></i>';
        } else if($extension == 'doc') {
            $fileIcon = '<i class="far fa-file-word"></i>';
        } else if($extension == 'pdf') {
            $fileIcon = '<i class="far fa-file-pdf"></i>';
        } else if($extension == 'zip') {
            $fileIcon = '<i class="far fa-file-archive"></i>';
        } else if($extension == 'txt') {
            $fileIcon = '<i class="far fa-file-alt"></i>';
        } else {
            $fileIcon = '<i class="far fa-file"></i>';
        }

        return $fileIcon;

    }

    /**
     * @param SplFileInfo $file
     * @return Html
     * @throws ImageException
     * @throws UnknownImageFileException
     */
    protected function getImageThumb(SplFileInfo $file) {

        $width = 45;
        $height = 45;

        $thumbFilename =  md5($file->getFilename() . $file->getPath() . $width . $height) . '.' . $file->getExtension();
        $thumbImage = $this->getThumbDir() . '/' . $thumbFilename;

        if(!file_exists($thumbImage)) {
            $srcImage = $this->removeMultipleSlashes($file->getPath() . '/' . $file->getFilename());

            $image = Image::fromFile($srcImage);
            $image->resize($width, $height, Image::SHRINK_ONLY | Image::STRETCH);
            $image->save($thumbImage, 80, Image::JPEG);
        }

        return Html::el('img')->setAttribute('src', $this->getThumbDirUrl() . '/' . $thumbFilename);
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function displayParentDir(string $path): bool {
        $pathArr = array_filter(explode('/', $path));
        return (count($pathArr) > 0);
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function removeMultipleSlashes(string $filename): string {
        return preg_replace('#/+#', '/', '/' . $filename);
    }

    /**
     * @return string
     */
    protected function getThumbDir(): string {
        return $this->uploadDirFullPath . '/' . $this->getNameThumbDir();
    }

    /**
     * @return string
     */
    protected function getThumbDirUrl(): string {
        return $this->getUploadDir() . '/' . $this->getNameThumbDir();
    }

    /**
     * @return string
     */
    protected function getNameThumbDir(): string {
        return '__thumb__';
    }

    /**
     * @param $size
     * @param string $unit
     * @return string
     */
    protected function getHumanFileSize($size, $unit=''): string {
        if( (!$unit && $size >= 1<<30) || $unit == "GB")
            return number_format($size/(1<<30),2)." GB";
        if( (!$unit && $size >= 1<<20) || $unit == "MB")
            return number_format($size/(1<<20),2)." MB";
        if( (!$unit && $size >= 1<<10) || $unit == "KB")
            return number_format($size/(1<<10),2)." KB";

        return number_format($size)." bytes";
    }

    /**
     * @param SplFileInfo $file
     * @return bool
     */
    protected function isImageFileType(SplFileInfo $file): bool {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bnp'];
        return (in_array($file->getExtension(), $imageExtensions));
    }

    /**
     * @param Request $request
     */
    protected function filesUploadAction(Request $request) {
        $fileupload = $request->get('fileupload', '');

        if(!empty($fileupload)) {

            $uploadDir = $this->removeMultipleSlashes($this->uploadDirFullPath . '/'. $request->get('path', '') . '/');

            /** @var UploadedFile $file */
            foreach($request->files->all() as $file) {
                $file->move($uploadDir, $file->getClientOriginalName());
            }

            exit;

        }
    }

    /**
     * @param string $dir
     * @param string $pathRequest
     * @param Request $request
     * @throws FilemanagerException
     */
    protected function createNewDirAction(string $dir, string $pathRequest, Request $request) {
        $newDirName = $request->request->get('folderName', '');

        if(!empty($newDirName)) {

            try {
                FileSystem::createDir($this->removeMultipleSlashes($dir . '/' . $newDirName));
            } catch (IOException $e) {
                throw new FilemanagerException('Failed to create directory');
            }

            $redirect = RedirectResponse::create($request->getPathInfo() . '?path=' . $pathRequest);
            $redirect->send();

            exit;
        }
    }

    /**
     * @param string $dir
     * @param string $pathRequest
     * @param Request $request
     * @throws FilemanagerException
     */
    protected function renameAction(string $dir, string $pathRequest, Request $request) {
        $originName = $request->request->get('oldEditName', '');
        $targetName = $request->request->get('editName', '');
        $ext = $request->request->get('ext', '');

        if(!empty($originName) && !empty($targetName)) {

            $ext = (!empty($ext) ? '.' . $ext : '');

            $origin = $dir . '/' . $originName . $ext;
            $target = $dir . '/' . $targetName . $ext;

            try {
                FileSystem::rename($this->removeMultipleSlashes($origin), $this->removeMultipleSlashes($target));
            } catch (IOException $e) {
                throw new FilemanagerException($e->getMessage());
            }

            $redirect = RedirectResponse::create($request->getPathInfo() . '?path=' . $pathRequest);
            $redirect->send();

            exit;
        }
    }

    protected function removeAction(string $dir, string $pathRequest, Request $request) {
        $name = $request->request->get('removeName', '');

        if(!empty($name)) {

            try {
                FileSystem::delete($this->removeMultipleSlashes($dir . '/' . $name));
            } catch (IOException $e) {
                throw new FilemanagerException($e->getMessage());
            }

            $redirect = RedirectResponse::create($request->getPathInfo() . '?path=' . $pathRequest);
            $redirect->send();

            exit;
        }
    }

    /**
     * @throws FilemanagerException
     */
    protected function createThumbDir() {
        $thumbDir = $this->getThumbDir();

        if(!is_dir($thumbDir)) {

            try {
                FileSystem::createDir($thumbDir);
            } catch (IOException $e) {
                throw new FilemanagerException('Failed to create thumb directory');
            }

        }
    }

    /**
     * @throws FilemanagerException
     */
    protected function init() {
        $uploadDir = $this->getUploadDir();

        if(empty($this->documentRootDir)) {
            $this->documentRootDir = $_SERVER['DOCUMENT_ROOT'];
        }

        $this->uploadDirFullPath = $this->removeMultipleSlashes(rtrim($this->documentRootDir . '/' . $uploadDir, '/'));

        if (empty($uploadDir) || !is_writable($this->uploadDirFullPath)) {
            throw new FilemanagerException('Upload dir must exists and must be writable');
        }

        $this->uploadDir = $this->removeMultipleSlashes('/' . rtrim($uploadDir, '/'));
    }

    /**
     * @return string
     */
    protected function getTempDirLatte(): string {
        return __DIR__ . '/tempLatte';
    }

    /**
     * @return string
     */
    protected function getTemplateDir(): string {
        return __DIR__ . '/template';
    }

}