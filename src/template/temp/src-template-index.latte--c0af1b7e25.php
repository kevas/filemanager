<?php
// source: /var/www/html/ea3/filemanager/src/template/index.latte

use Latte\Runtime as LR;

final class Templatec0af1b7e25 extends Latte\Runtime\Template
{

	public function main(): array
	{
		extract($this->params);
		echo '<div class="container-filemanager">

    <div class="top-bar">
        <div class="top-bar-inner">

            <div class="logo">';
		echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('File Manager')) /* line 6 */;
		echo '</div>

            <div class="breadcrumbs">
                <a href="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($homeBreadCrumbParams)) /* line 9 */;
		echo '"><i class="fas fa-home"></i></a>
';
		if (count($breadCrumbs) > 0) {
			echo '                    <span class="sep">/</span>
';
			$iterations = 0;
			foreach ($iterator = $__it = new LR\CachingIterator($breadCrumbs, $__it ?? null) as $breadCrumb) {
				echo '                        ';
				echo ($this->global->fn->getBreadCrumbItem)($breadCrumb) /* line 13 */;
				echo '
                        ';
				echo !$iterator->isLast() ? '<span class="sep">/</span>' : '' /* line 14 */;
				echo "\n";
				$iterations++;
			}
			$iterator = $__it = $__it->getParent();
		}
		echo '            </div>

            <div class="upload"><i class="fas fa-upload"></i>';
		echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('Upload files')) /* line 19 */;
		echo '</div>
            <div class="new-folder"><i class="far fa-folder-open"></i>';
		echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('New folder')) /* line 20 */;
		echo '</div>
        </div>
    </div>

    <div class="filemanager';
		echo LR\Filters::escapeHtmlAttr($isInsertFileParam ? ' insertFile' : '') /* line 24 */;
		echo LR\Filters::escapeHtmlAttr($isInsertFolderParam ? ' insertFolder' : '') /* line 24 */;
		echo '" data-path="';
		echo LR\Filters::escapeHtmlAttr(($this->global->fn->getPathUrl)()) /* line 24 */;
		echo '">

        <div class="row header">
            <span class="wrapper-value icon"><span class="inner-wrapper-value"></span></span>
            <span class="wrapper-value filename"><span class="inner-wrapper-value">';
		echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('Name')) /* line 28 */;
		echo '</span></span>
            <span class="wrapper-value size"><span class="inner-wrapper-value">';
		echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('Size')) /* line 29 */;
		echo '</span></span>
            <span class="wrapper-value time"><span class="inner-wrapper-value">';
		echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('Modified')) /* line 30 */;
		echo '</span></span>
        </div>

';
		if (($this->global->fn->displayParentDir)()) {
			echo '            <div class="row">
                <a href="';
			echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getUrlParamsParentDir)())) /* line 35 */;
			echo '" class="folder parent">
                    <span class="wrapper-value icon"><span class="inner-wrapper-value"><i class="fas fa-level-up-alt"></i></span></span>
                </a>
            </div>
';
		}
		echo "\n";
		$iterations = 0;
		foreach ($dirs as $dir) {
			echo '
            <div class="row folder ';
			echo LR\Filters::escapeHtmlAttr(($this->global->fn->getClassNameActiveRow)($dir, 'selectedFolder')) /* line 43 */;
			echo '" data-folder=\'';
			echo ($this->global->fn->getDataDir)($dir) /* line 43 */;
			echo '\'>
                <a href="';
			echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getDirUrl)($dir))) /* line 44 */;
			echo '" class="folder">
                    <span class="wrapper-value icon"><span class="inner-wrapper-value"><i class="far fa-folder"></i></span></span>
                    <span class="wrapper-value filename">
                        <span class="inner-wrapper-value">
                            <span class="name" data-name="';
			echo LR\Filters::escapeHtmlAttr($dir->getBasename()) /* line 48 */;
			echo '">
                                ';
			echo LR\Filters::escapeHtmlText($dir->getFilename()) /* line 49 */;
			echo '
                            </span>
                            <span class="action">
                                <span class="choose choose-folder">';
			echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('choose folder')) /* line 52 */;
			echo '</span>
                                <i class="far fa-edit edit"></i>
                                <i class="far fa-trash-alt remove"></i>
                            </span>
                        </span>
                    </span>
                    <span class="wrapper-value size"><span class="inner-wrapper-value">';
			echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('Folder')) /* line 58 */;
			echo '</span></span>
                    <span class="wrapper-value time"><span class="inner-wrapper-value">';
			echo LR\Filters::escapeHtmlText(date('d.m.Y H:i', $dir->getMTime())) /* line 59 */;
			echo '</span></span>
                </a>
            </div>

';
			$iterations++;
		}
		echo "\n";
		$iterations = 0;
		foreach ($files as $file) {
			echo '
            <div class="row file ';
			echo LR\Filters::escapeHtmlAttr(($this->global->fn->getClassNameActiveRow)($file, 'selectedFile')) /* line 67 */;
			echo '" data-file=\'';
			echo ($this->global->fn->getDataFile)($file) /* line 67 */;
			echo '\'>
                <span class="wrapper-value icon"><span class="inner-wrapper-value">';
			echo ($this->global->fn->getFileIcon)($file) /* line 68 */;
			echo '</span></span>
                <span class="wrapper-value filename">
                    <span class="inner-wrapper-value">
                        <span class="name" data-name="';
			echo LR\Filters::escapeHtmlAttr($file->getBasename('.' . $file->getExtension())) /* line 71 */;
			echo '" data-ext="';
			echo LR\Filters::escapeHtmlAttr($file->getExtension()) /* line 71 */;
			echo '">
                            ';
			echo ($this->global->fn->getFilename)($file) /* line 72 */;
			echo '
                        </span>
                        <span class="action">
                            <span class="choose choose-file">';
			echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('choose file')) /* line 75 */;
			echo '</span>
                            <i class="far fa-edit edit"></i>
                            <i class="far fa-trash-alt remove"></i>
                        </span>
                    </span>
                </span>
                <span class="wrapper-value size"><span class="inner-wrapper-value">';
			echo LR\Filters::escapeHtmlText(($this->global->fn->getHumanFileSize)($file)) /* line 81 */;
			echo '</span></span>
                <span class="wrapper-value time"><span class="inner-wrapper-value">';
			echo LR\Filters::escapeHtmlText(date('d.m.Y H:i', $file->getMTime())) /* line 82 */;
			echo '</span></span>
            </div>

';
			$iterations++;
		}
		echo "\n";
		if ($dirs->count() == 0 && $files->count() == 0) {
			echo '            <div class="empty-content">
                <div class="empty-content-inner">
                    <button class="btn upload"><i class="fas fa-upload"></i>';
			echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('Upload files')) /* line 90 */;
			echo '</button>
                    <button class="btn new-folder"><i class="far fa-folder-open"></i>';
			echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('New folder')) /* line 91 */;
			echo '</button>
                </div>
            </div>
';
		}
		echo '
    </div>

    <div class="overlay"></div>

    <form action="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getDropzoneUrl)())) /* line 100 */;
		echo '" class="dropzone" id="dropzoneBox">
        <i class="fas fa-times"></i>
        <div class="dz-message needsclick">
            <button type="button" class="dz-button">';
		echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('Drop files here or click to upload.')) /* line 103 */;
		echo '</button>
        </div>
    </form>

    <div class="new-folder-box action-box">
        <form action="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getPathUrl)())) /* line 108 */;
		echo '" method="post">
            <i class="fas fa-times"></i>
            <input type="text" name="folderName" class="form-control" placeholder="';
		echo LR\Filters::escapeHtmlAttr(($this->global->fn->getMessage)('Name of folder')) /* line 110 */;
		echo '">
            <input type="submit" value="';
		echo LR\Filters::escapeHtmlAttr(($this->global->fn->getMessage)('Create')) /* line 111 */;
		echo '" class="btn">
        </form>
    </div>

    <div class="edit-box action-box">
        <form action="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getPathUrl)())) /* line 116 */;
		echo '" method="post">
            <i class="fas fa-times"></i>
            <input type="text" name="editName" class="form-control">
            <input type="hidden" name="oldEditName">
            <input type="hidden" name="ext">
            <input type="submit" value="';
		echo LR\Filters::escapeHtmlAttr(($this->global->fn->getMessage)('Edit')) /* line 121 */;
		echo '" class="btn">
        </form>
    </div>

</div>

<script>
    __filemanagerMessages = {};
    __filemanagerMessages.notEmpty = ';
		echo LR\Filters::escapeJs(($this->global->fn->getMessage)('Name cannot be empty')) /* line 129 */;
		echo ';
    __filemanagerMessages.illegalCharacters = ';
		echo LR\Filters::escapeJs(($this->global->fn->getMessage)('The name contains illegal characters')) /* line 130 */;
		echo ';
    __filemanagerMessages.sure = ';
		echo LR\Filters::escapeJs(($this->global->fn->getMessage)('Are you sure?')) /* line 131 */;
		echo ';
</script>

';
		return get_defined_vars();
	}


	public function prepare(): void
	{
		extract($this->params);
		if (!$this->getReferringTemplate() || $this->getReferenceType() === "extends") {
			foreach (array_intersect_key(['breadCrumb' => '12', 'dir' => '41', 'file' => '65'], $this->params) as $__v => $__l) {
				trigger_error("Variable \$$__v overwritten in foreach on line $__l");
			}
		}
		
	}

}
