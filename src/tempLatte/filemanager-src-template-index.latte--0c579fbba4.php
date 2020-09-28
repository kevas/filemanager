<?php
// source: /var/www/html/crud/src/filemanager-src/template/index.latte

use Latte\Runtime as LR;

final class Template0c579fbba4 extends Latte\Runtime\Template
{

	public function main(): array
	{
		extract($this->params);
?>
<div class="container-filemanager">

    <div class="top-bar">

        <div class="logo">File Manager</div>

        <div class="breadcrumbs">
            <a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($homeBreadCrumbParams)) /* line 8 */ ?>"><i class="fas fa-home"></i></a>
<?php
		if (count($breadCrumbs) > 0) {
?>
                <span class="sep">/</span>
<?php
			$iterations = 0;
			foreach ($iterator = $this->global->its[] = new LR\CachingIterator($breadCrumbs) as $breadCrumb) {
				?>                    <?php echo ($this->global->fn->getBreadCrumbItem)($breadCrumb) /* line 12 */ ?>

                    <?php echo !$iterator->isLast() ? '<span class="sep">/</span>' : '' /* line 13 */ ?>

<?php
				$iterations++;
			}
			array_pop($this->global->its);
			$iterator = end($this->global->its);
		}
?>
        </div>

        <div class="upload"><i class="fas fa-upload"></i>Upload files</div>
        <div class="new-folder"><i class="far fa-folder-open"></i>New folder</div>
    </div>

    <div class="filemanager<?php echo LR\Filters::escapeHtmlAttr($isInsertFileParam ? ' insertFile' : '') /* line 22 */ ?>" data-path="<?php
		echo LR\Filters::escapeHtmlAttr(($this->global->fn->getPathUrl)()) /* line 22 */ ?>">

        <div class="row header">
            <span class="wrapper-value icon"><span class="inner-wrapper-value"></span></span>
            <span class="wrapper-value filename"><span class="inner-wrapper-value">Name</span></span>
            <span class="wrapper-value size"><span class="inner-wrapper-value">Size</span></span>
            <span class="wrapper-value time"><span class="inner-wrapper-value">Modified</span></span>
        </div>

<?php
		if (($this->global->fn->displayParentDir)()) {
?>
            <div class="row">
                <a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getParamsParentUrlDir)())) /* line 33 */ ?>" class="folder parent">
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

            <div class="row folder">
                <a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getParamsUrlDir)($dir))) /* line 42 */ ?>" class="folder">
                    <span class="wrapper-value icon"><span class="inner-wrapper-value"><i class="far fa-folder"></i></span></span>
                    <span class="wrapper-value filename">
                        <span class="inner-wrapper-value">
                            <span class="name" data-name="<?php echo LR\Filters::escapeHtmlAttr($dir->getBasename()) /* line 46 */ ?>">
                                <?php echo LR\Filters::escapeHtmlText($dir->getFilename()) /* line 47 */ ?>

                            </span>
                            <span class="action">
                                <i class="far fa-edit edit"></i>
                                <i class="far fa-trash-alt remove"></i>
                            </span>
                        </span>
                    </span>
                    <span class="wrapper-value size"><span class="inner-wrapper-value">Folder</span></span>
                    <span class="wrapper-value time"><span class="inner-wrapper-value"><?php echo LR\Filters::escapeHtmlText(date('d.m.Y H:i', $dir->getMTime())) /* line 56 */ ?></span></span>
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

            <div class="row file" data-file='<?php echo ($this->global->fn->getDataFile)($file) /* line 64 */ ?>'>
                <span class="wrapper-value icon"><span class="inner-wrapper-value"><?php echo ($this->global->fn->getFileIcon)($file) /* line 65 */ ?></span></span>
                <span class="wrapper-value filename">
                    <span class="inner-wrapper-value">
                        <span class="name" data-name="<?php echo LR\Filters::escapeHtmlAttr($file->getBasename('.' . $file->getExtension())) /* line 68 */ ?>" data-ext="<?php
			echo LR\Filters::escapeHtmlAttr($file->getExtension()) /* line 68 */ ?>">
                            <?php echo ($this->global->fn->getFilename)($file) /* line 69 */ ?>

                        </span>
                        <span class="action">
                            <i class="far fa-edit edit"></i>
                            <i class="far fa-trash-alt remove"></i>
                        </span>
                    </span>
                </span>
                <span class="wrapper-value size"><span class="inner-wrapper-value"><?php echo LR\Filters::escapeHtmlText(($this->global->fn->getHumanFileSize)($file)) /* line 77 */ ?></span></span>
                <span class="wrapper-value time"><span class="inner-wrapper-value"><?php echo LR\Filters::escapeHtmlText(date('d.m.Y H:i', $file->getMTime())) /* line 78 */ ?></span></span>
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
                    <button class="btn upload"><i class="fas fa-upload"></i>Upload files</button>
                    <button class="btn new-folder"><i class="far fa-folder-open"></i>New folder</button>
                </div>
            </div>
<?php
		}
?>

    </div>

    <div class="overlay"></div>

    <form action="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getDropzoneUrl)())) /* line 96 */ ?>" class="dropzone" id="dropzoneBox">
        <i class="fas fa-times"></i>
        <div class="dz-message needsclick">
            <button type="button" class="dz-button">Drop files here or click to upload.</button>
        </div>
    </form>

    <div class="new-folder-box action-box">
        <form action="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getPathUrl)())) /* line 104 */ ?>" method="post">
            <i class="fas fa-times"></i>
            <input type="text" name="folderName" class="form-control" placeholder="Name of folder">
            <input type="submit" value="Create" class="btn">
        </form>
    </div>

    <div class="edit-box action-box">
        <form action="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getPathUrl)())) /* line 112 */ ?>" method="post">
            <i class="fas fa-times"></i>
            <input type="text" name="editName" class="form-control">
            <input type="hidden" name="oldEditName">
            <input type="hidden" name="ext">
            <input type="submit" value="Edit" class="btn">
        </form>
    </div>

</div>

<?php
		return get_defined_vars();
	}


	public function prepare(): void
	{
		extract($this->params);
		if (!$this->getReferringTemplate() || $this->getReferenceType() === "extends") {
			foreach (array_intersect_key(['breadCrumb' => '11', 'dir' => '39', 'file' => '62'], $this->params) as $_v => $_l) {
				trigger_error("Variable \$$_v overwritten in foreach on line $_l");
			}
		}
		
	}

}