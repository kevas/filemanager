<?php


namespace Kevas\Filemanager;


class BasePath {

    private string $appDir;

    private string $wwwDir;

    private string $tempDir;

    private string $vendorDir;

    /**
     * Parameter constructor.
     * @param string $appDir
     * @param string $wwwDir
     * @param string $tempDir
     * @param string $vendorDir
     */
    public function __construct(string $appDir, string $wwwDir, string $tempDir, string $vendorDir) {
        $this->appDir = $this->changeBackSlashes($appDir);
        $this->wwwDir = $this->changeBackSlashes($wwwDir);
        $this->tempDir = $this->changeBackSlashes($tempDir);
        $this->vendorDir = $this->changeBackSlashes($vendorDir);
    }

    /**
     * @return string
     */
    public function getAppDir(): string {
        return $this->appDir;
    }

    /**
     * @return string
     */
    public function getWwwDir(): string {
        return $this->wwwDir;
    }

    /**
     * @return string
     */
    public function getTempDir(): string {
        return $this->tempDir;
    }

    /**
     * @return string
     */
    public function getVendorDir(): string {
        return $this->vendorDir;
    }

    /**
     * @param string $path
     * @return string
     */
    public function changeBackSlashes(string $path): string {
        return str_replace('\\', '/', $path);
    }
}