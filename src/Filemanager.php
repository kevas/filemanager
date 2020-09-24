<?php

namespace Kevas\Filemanager;

use Kevas\Filemanager\Exception\FilemanagerException;
use Nette\Utils\Finder;
use Nette\IOException;
use Nette\Utils\Html;
use Nette\Utils\ImageException;
use Nette\Utils\UnknownImageFileException;
use SplFileInfo;
use Nette\Utils\Image;
use Nette\Utils\FileSystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Filemanager {

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
     * @throws ImageException
     * @throws UnknownImageFileException
     */
    public function render() {
        $this->init();

        $this->createThumbDir();

        $request = Request::createFromGlobals();
        $pathRequest = $request->get('path', '');
        $fileupload = $request->get('fileupload', '');

        if(!empty($fileupload)) {
            $this->filesUpload($request);
            exit;
        }

        $searchDir = $this->uploadDirFullPath . '/' . $pathRequest;

        $this->createNewFolder($searchDir, $pathRequest, $request);

        if(!is_dir($searchDir)) {
            $searchDir = $this->uploadDirFullPath;
            $pathRequest = '';
        }

        $headerContent = $this->getHeader();
        $parentDirContent = $this->getParentDir($pathRequest);

        $content = '';
        foreach (Finder::findDirectories('*')->in($searchDir)->exclude('__thumb__') as $file) {
            $content .= $this->getRowFolder($file);
        }

        foreach (Finder::findFiles('*')->in($searchDir) as $file) {
            $content .= $this->getRowFile($file);
        }

        if(empty($content)) {
            $content = $this->getEmptyContent();
        }

        $filemanagerList = Html::el('div')->setAttribute('class', 'filemanager')->setHtml($headerContent . $parentDirContent . $content);

        return  Html::el('div')->setAttribute('class', 'container-filemanager')->setHtml($this->getTopBar() .
            $filemanagerList . $this->getDropzoneBlock($request, $pathRequest) . $this->getNewFolderBlock($request, $pathRequest))->render();

    }

    /**
     * @return string
     */
    protected function getEmptyContent(): string {
        return Html::el('div')->setAttribute('class', 'empty-content')->setHtml('
            <div class="empty-content-inner">
                <button class="btn upload"><i class="fas fa-upload"></i>Upload files</button>
                <button class="btn new-folder"><i class="far fa-plus-square"></i>New folder</button>
            </div>
        ')->render();
    }

    /**
     * @param SplFileInfo $file
     * @return Html
     */
    protected function getRowFolder(SplFileInfo $file): Html {
        $icon = $this->getFolderIcon();
        $folderName = $this->getFileName($file);
        $fileSize = $this->getFileSize($file, 'Folder');
        $fileTime = $this->getModifiedTime($file);

        $pathEncoded = urlencode((!empty($pathRequest) ? $pathRequest . '/' : '') . $file->getFilename());

        $row = Html::el('a')->addAttributes(['href' => '?path=' . $pathEncoded, 'class' => 'folder'])->setHtml(
            $icon . $folderName . $fileSize . $fileTime);

        return Html::el('div')->setAttribute('class', 'row folder')->setHtml($row);
    }

    /**
     * @param SplFileInfo $file
     * @return Html
     * @throws ImageException
     * @throws UnknownImageFileException
     */
    protected function getRowFile(SplFileInfo $file): Html {
        $icon = $this->getFileIcon($file);
        $fileName = $this->getFileName($file);
        $fileSize = $this->getFileSize($file);
        $fileTime = $this->getModifiedTime($file);

        $row = $icon . $fileName . $fileSize . $fileTime;

        $classFileType = ($this->isImageFileType($file) ? 'image' : 'file');

        return Html::el('div')->setAttribute('class', 'row ' . $classFileType)->setHtml($row);
    }

    /**
     * @return string
     */
    protected function getFolderIcon(): string {
        $value = $this->getInnerWrapperValue('<i class="far fa-folder"></i>');
        return $this->getWrapperValue($value, 'icon');
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
        } else if($extension == 'xsl' || $extension == 'xslx') {
            $fileIcon = '<i class="far fa-file-excel"></i>';
        } else if($extension == 'doc') {
            $fileIcon = '<i class="far fa-file-word"></i>';
        } else if($extension == 'pdf') {
            $fileIcon = '<i class="far fa-file-pdf"></i>';
        } else {
            $fileIcon = '<i class="far fa-file"></i>';
        }

        $value = $this->getInnerWrapperValue($fileIcon);

        return $this->getWrapperValue($value, 'icon');

    }

    /**
     * @param SplFileInfo $file
     * @param string $altTextSize
     * @return string
     */
    protected function getFileSize(SplFileInfo $file, string $altTextSize = ''): string {

        if(empty($altTextSize)) {
            $size = $this->getHumanFileSize($file->getSize());
        } else {
            $size = $altTextSize;
        }

        $value = $this->getInnerWrapperValue($size);
        return $this->getWrapperValue($value, 'size');
    }

    /**
     * @param SplFileInfo $file
     * @return string
     */
    protected function getModifiedTime(SplFileInfo $file): string {
        $value = $this->getInnerWrapperValue(date('d.m.Y H:i', $file->getMTime()));
        return $this->getWrapperValue($value, 'time');
    }

    /**
     * @param SplFileInfo $file
     * @return string
     */
    protected function getFileName(SplFileInfo $file): string {
        $value = $this->getInnerWrapperValue($file->getFilename());
        return $this->getWrapperValue($value, 'filename');
    }

    /**
     * @param string $value
     * @param string $class
     * @return string
     */
    protected function getWrapperValue(string $value, string $class): string {
        return Html::el('span')->setAttribute('class', 'wrapper-value ' . $class)->setHtml($value)->render();
    }

    /**
     * @param string $value
     * @return string
     */
    protected function getInnerWrapperValue(string $value): string {
        return Html::el('span')->setAttribute('class', 'inner-wrapper-value')->setHtml($value)->render();
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
            $srcImage = $this->removeMultipleSlash($file->getPath() . '/' . $file->getFilename());

            $image = Image::fromFile($srcImage);
            $image->resize($width, $height, Image::SHRINK_ONLY | Image::STRETCH);
            $image->save($thumbImage, 80, Image::JPEG);
        }

        return Html::el('img')->setAttribute('src', $this->getThumbDirUrl() . '/' . $thumbFilename);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getParentDir(string $path): string {
        $pathArr = array_filter(explode('/', $path));

        $countPath = count($pathArr);

        if($countPath < 1) {
            return '';
        }

        array_pop($pathArr);

        $href = ($countPath > 1 ? '?path=' . implode('/', $pathArr) : '?path=');

        $icon =  '<i class="fas fa-arrow-up"></i>';

        $value = $this->getInnerWrapperValue($icon);

        $parentDir = Html::el('a')->addAttributes(['href' => $href, 'class' => 'folder parent'])->setHtml($this->getWrapperValue($value, 'icon'));

        return Html::el('div')->setAttribute('class', 'row')->setHtml($parentDir);
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function removeMultipleSlash(string $filename): string {
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
     * @return string
     */
    protected function getHeader(): string {

        $items = $this->getWrapperValue($this->getInnerWrapperValue(''), 'icon');
        $items .= $this->getWrapperValue($this->getInnerWrapperValue('Name'), 'filename');
        $items .= $this->getWrapperValue($this->getInnerWrapperValue('Size'), 'size');
        $items .= $this->getWrapperValue($this->getInnerWrapperValue('Modified'), 'modified');

        return Html::el('div')->setAttribute('class', 'row header')->setHtml($items)->render();
    }

    /**
     * @return string
     */
    protected function getTopBar(): string {
        return Html::el('div')->setAttribute('class', 'top-bar')->setHtml(
            Html::el('div')->setAttribute('class', 'logo')->setHtml('File Manager') .
            Html::el('div')->setAttribute('class', 'upload')->setHtml('<i class="fas fa-upload"></i>Upload files') .
            Html::el('div')->setAttribute('class', 'new-folder')->setHtml('<i class="far fa-plus-square"></i>New folder')
        )->render();
    }

    /**
     * @param Request $request
     * @param string $pathRequest
     * @return string
     */
    protected function getDropzoneBlock(Request $request, string $pathRequest): string {
        $dropzoneBg =  Html::el('div')->addAttributes([
            'class' => 'dropzone-bg'
        ])->render();

        $dropzoneBlock =  Html::el('form')->addAttributes([
            'action' => $request->getPathInfo() . '?fileupload=1&path=' . $pathRequest,
            'class' => 'dropzone-box'
        ])->addHtml('<i class="fas fa-times"></i><div class="dz-message needsclick"><button type="button" class="dz-button">Drop files here or click to upload.</button></div>')->render();

        return $dropzoneBg . $dropzoneBlock;
    }

    /**
     * @param Request $request
     */
    protected function filesUpload(Request $request) {

        $uploadDir = $this->removeMultipleSlash($this->uploadDirFullPath . '/'. $request->get('path', '') . '/');

        /** @var UploadedFile $file */
        foreach($request->files->all() as $file) {
            $file->move($uploadDir, $file->getClientOriginalName());
        }
    }

    /**
     * @param Request $request
     * @param string $pathRequest
     * @return string
     */
    protected function getNewFolderBlock(Request $request, string $pathRequest): string {
        $newFolderBg =  Html::el('div')->addAttributes([
            'class' => 'new-folder-bg'
        ])->render();

        $newFolderBlock = Html::el('div')->addAttributes([
            'class' => 'new-folder-box'
        ])->setHtml($this->getFormNewFolder($request, $pathRequest));

        return $newFolderBg . $newFolderBlock;
    }

    /**
     * @param Request $request
     * @param string $pathRequest
     * @return string
     */
    protected function getFormNewFolder(Request $request, string $pathRequest): string {
        return Html::el('form')->addAttributes([
            'action' => $request->getPathInfo() . '?path=' . $pathRequest,
            'method' => 'post'
        ])->setHtml('
            <i class="fas fa-times"></i>
            <input type="text" name="folderName" class="form-control" placeholder="Name of folder" />
            <input type="submit" value="Create" class="btn">'
        )->render();
    }

    /**
     * @param string $searchDir
     * @param string $pathRequest
     * @param Request $request
     * @throws FilemanagerException
     */
    protected function createNewFolder(string $searchDir, string $pathRequest, Request $request) {
        $newFolderName = $request->request->get('folderName', '');

        if(!empty($newFolderName)) {

            try {
                FileSystem::createDir($this->removeMultipleSlash($searchDir . '/' . $newFolderName));
            } catch (IOException $e) {
                throw new FilemanagerException('Failed to create directory');
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

        $this->documentRootDir = $_SERVER['DOCUMENT_ROOT'];

        $this->uploadDirFullPath = $this->removeMultipleSlash(rtrim($this->documentRootDir . '/' . $uploadDir, '/'));

        if (empty($this->getUploadDir()) || !is_writable($this->uploadDirFullPath)) {
            throw new FilemanagerException('Upload dir must exists and must be writable');
        }

        $this->uploadDir = $this->removeMultipleSlash('/' . rtrim($uploadDir, '/'));
    }

}