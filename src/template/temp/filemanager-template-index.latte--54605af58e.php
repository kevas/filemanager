<?php
// source: /var/www/html/ea3/src/filemanager/template/index.latte

use Latte\Runtime as LR;

final class Template54605af58e extends Latte\Runtime\Template
{

	public function main(): array
	{
		extract($this->params);
?>
<div class="container-filemanager">

    <div class="top-bar">
        <div class="top-bar-inner">

            <div class="logo"><?php echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('File Manager')) /* line 6 */ ?></div>

            <div class="breadcrumbs">
                <a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($homeBreadCrumbParams)) /* line 9 */ ?>"><i class="fas fa-home"></i></a>
<?php
		if (count($breadCrumbs) > 0) {
?>
                    <span class="sep">/</span>
<?php
			$iterations = 0;
			foreach ($iterator = $this->global->its[] = new LR\CachingIterator($breadCrumbs) as $breadCrumb) {
				?>                        <?php echo ($this->global->fn->getBreadCrumbItem)($breadCrumb) /* line 13 */ ?>

                        <?php echo !$iterator->isLast() ? '<span class="sep">/</span>' : '' /* line 14 */ ?>

<?php
				$iterations++;
			}
			array_pop($this->global->its);
			$iterator = end($this->global->its);
		}
?>
            </div>

            <div class="upload"><i class="fas fa-upload"></i><?php echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('Upload files')) /* line 19 */ ?></div>
            <div class="new-folder"><i class="far fa-folder-open"></i><?php echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('New folder')) /* line 20 */ ?></div>
        </div>
    </div>

    <div class="filemanager<?php
		echo LR\Filters::escapeHtmlAttr($isInsertFileParam ? ' insertFile' : '') /* line 24 */;
		echo LR\Filters::escapeHtmlAttr($isInsertFolderParam ? ' insertFolder' : '') /* line 24 */ ?>" data-path="<?php
		echo LR\Filters::escapeHtmlAttr(($this->global->fn->getPathUrl)()) /* line 24 */ ?>">

        <div class="row header">
            <span class="wrapper-value icon"><span class="inner-wrapper-value"></span></span>
            <span class="wrapper-value filename"><span class="inner-wrapper-value"><?php echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('Name')) /* line 28 */ ?></span></span>
            <span class="wrapper-value size"><span class="inner-wrapper-value"><?php echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('Size')) /* line 29 */ ?></span></span>
            <span class="wrapper-value time"><span class="inner-wrapper-value"><?php echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('Modified')) /* line 30 */ ?></span></span>
        </div>

<?php
		if (($this->global->fn->displayParentDir)()) {
?>
            <div class="row">
                <a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getUrlParamsParentDir)())) /* line 35 */ ?>" class="folder parent">
                    <span class="wrapper-value icon"><span class="inner-wrapper-value"><i class="fas fa-level-up-alt"></i></span></span>
                </a>
            </div>
<?php
		}
?>

<?php
		$iterations = 0;
		foreach ($dirs as $dir) {
?>

            <div class="row folder <?php echo LR\Filters::escapeHtmlAttr(($this->global->fn->getClassNameActiveRow)($dir, 'selectedFolder')) /* line 43 */ ?>" data-folder='<?php
			echo ($this->global->fn->getDataDir)($dir) /* line 43 */ ?>'>
                <a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getDirUrl)($dir))) /* line 44 */ ?>" class="folder">
                    <span class="wrapper-value icon"><span class="inner-wrapper-value"><i class="far fa-folder"></i></span></span>
                    <span class="wrapper-value filename">
                        <span class="inner-wrapper-value">
                            <span class="name" data-name="<?php echo LR\Filters::escapeHtmlAttr($dir->getBasename()) /* line 48 */ ?>">
                                <?php echo LR\Filters::escapeHtmlText($dir->getFilename()) /* line 49 */ ?>

                            </span>
                            <span class="action">
                                <span class="choose choose-folder"><?php echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('choose folder')) /* line 52 */ ?></span>
                                <i class="far fa-edit edit"></i>
                                <i class="far fa-trash-alt remove"></i>
                            </span>
                        </span>
                    </span>
                    <span class="wrapper-value size"><span class="inner-wrapper-value"><?php echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('Folder')) /* line 58 */ ?></span></span>
                    <span class="wrapper-value time"><span class="inner-wrapper-value"><?php echo LR\Filters::escapeHtmlText(date('d.m.Y H:i', $dir->getMTime())) /* line 59 */ ?></span></span>
                </a>
            </div>

<?php
			$iterations++;
		}
?>

<?php
		$iterations = 0;
		foreach ($files as $file) {
?>

            <div class="row file <?php echo LR\Filters::escapeHtmlAttr(($this->global->fn->getClassNameActiveRow)($file, 'selectedFile')) /* line 67 */ ?>" data-file='<?php
			echo ($this->global->fn->getDataFile)($file) /* line 67 */ ?>'>
                <span class="wrapper-value icon"><span class="inner-wrapper-value"><?php echo ($this->global->fn->getFileIcon)($file) /* line 68 */ ?></span></span>
                <span class="wrapper-value filename">
                    <span class="inner-wrapper-value">
                        <span class="name" data-name="<?php echo LR\Filters::escapeHtmlAttr($file->getBasename('.' . $file->getExtension())) /* line 71 */ ?>" data-ext="<?php
			echo LR\Filters::escapeHtmlAttr($file->getExtension()) /* line 71 */ ?>">
                            <?php echo ($this->global->fn->getFilename)($file) /* line 72 */ ?>

                        </span>
                        <span class="action">
                            <span class="choose choose-file"><?php echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('choose file')) /* line 75 */ ?></span>
                            <i class="far fa-edit edit"></i>
                            <i class="far fa-trash-alt remove"></i>
                        </span>
                    </span>
                </span>
                <span class="wrapper-value size"><span class="inner-wrapper-value"><?php echo LR\Filters::escapeHtmlText(($this->global->fn->getHumanFileSize)($file)) /* line 81 */ ?></span></span>
                <span class="wrapper-value time"><span class="inner-wrapper-value"><?php echo LR\Filters::escapeHtmlText(date('d.m.Y H:i', $file->getMTime())) /* line 82 */ ?></span></span>
            </div>

<?php
			$iterations++;
		}
?>

<?php
		if ($dirs->count() == 0 && $files->count() == 0) {
?>
            <div class="empty-content">
                <div class="empty-content-inner">
                    <button class="btn upload"><i class="fas fa-upload"></i><?php echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('Upload files')) /* line 90 */ ?></button>
                    <button class="btn new-folder"><i class="far fa-folder-open"></i><?php echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('New folder')) /* line 91 */ ?></button>
                </div>
            </div>
<?php
		}
?>

    </div>

    <div class="overlay"></div>

    <form action="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getDropzoneUrl)())) /* line 100 */ ?>" class="dropzone" id="dropzoneBox">
        <i class="fas fa-times"></i>
        <div class="dz-message needsclick">
            <button type="button" class="dz-button"><?php echo LR\Filters::escapeHtmlText(($this->global->fn->getMessage)('Drop files here or click to upload.')) /* line 103 */ ?></button>
        </div>
    </form>

    <div class="new-folder-box action-box">
        <form action="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getPathUrl)())) /* line 108 */ ?>" method="post">
            <i class="fas fa-times"></i>
            <input type="text" name="folderName" class="form-control" placeholder="<?php echo LR\Filters::escapeHtmlAttr(($this->global->fn->getMessage)('Name of folder')) /* line 110 */ ?>">
            <input type="submit" value="<?php echo LR\Filters::escapeHtmlAttr(($this->global->fn->getMessage)('Create')) /* line 111 */ ?>" class="btn">
        </form>
    </div>

    <div class="edit-box action-box">
        <form action="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getPathUrl)())) /* line 116 */ ?>" method="post">
            <i class="fas fa-times"></i>
            <input type="text" name="editName" class="form-control">
            <input type="hidden" name="oldEditName">
            <input type="hidden" name="ext">
            <input type="submit" value="<?php echo LR\Filters::escapeHtmlAttr(($this->global->fn->getMessage)('Edit')) /* line 121 */ ?>" class="btn">
        </form>
    </div>

</div>

<script>
    __filemanagerMessages = {};
    __filemanagerMessages.notEmpty = <?php echo LR\Filters::escapeJs(($this->global->fn->getMessage)('Name cannot be empty')) /* line 129 */ ?>;
    __filemanagerMessages.illegalCharacters = <?php echo LR\Filters::escapeJs(($this->global->fn->getMessage)('The name contains illegal characters')) /* line 130 */ ?>;
    __filemanagerMessages.sure = <?php echo LR\Filters::escapeJs(($this->global->fn->getMessage)('Are you sure?')) /* line 131 */ ?>;
</script>

<?php
		return get_defined_vars();
	}


	public function prepare(): void
	{
		extract($this->params);
		if (!$this->getReferringTemplate() || $this->getReferenceType() === "extends") {
			foreach (array_intersect_key(['breadCrumb' => '12', 'dir' => '41', 'file' => '65'], $this->params) as $_v => $_l) {
				trigger_error("Variable \$$_v overwritten in foreach on line $_l");
			}
		}
		
	}

}