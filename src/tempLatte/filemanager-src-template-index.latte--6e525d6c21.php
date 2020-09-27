<?php
// source: /var/www/html/crud/src/filemanager-src/template/index.latte

use Latte\Runtime as LR;

final class Template6e525d6c21 extends Latte\Runtime\Template
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

    <div class="filemanager<?php echo LR\Filters::escapeHtmlAttr($isInsertFileParam ? ' insertFile' : '') /* line 22 */ ?>">

<?php
		if (($this->global->fn->displayParentDir)()) {
?>
            <div class="row">
                <a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getParamsParentUrlDir)())) /* line 26 */ ?>" class="folder parent">
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
                <a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getParamsUrlDir)($dir))) /* line 35 */ ?>" class="folder">
                    <span class="wrapper-value icon"><span class="inner-wrapper-value"><i class="far fa-folder"></i></span></span>
                    <span class="wrapper-value filename"><span class="inner-wrapper-value"><?php echo LR\Filters::escapeHtmlText($dir->getFilename()) /* line 37 */ ?></span></span>
                    <span class="wrapper-value size"><span class="inner-wrapper-value">Folder</span></span>
                    <span class="wrapper-value time"><span class="inner-wrapper-value"><?php echo LR\Filters::escapeHtmlText(date('d.m.Y H:i', $dir->getMTime())) /* line 39 */ ?></span></span>
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

            <div class="row file" data-file='<?php echo ($this->global->fn->getDataFile)($file) /* line 47 */ ?>'>
                <span class="wrapper-value icon"><span class="inner-wrapper-value"><?php echo ($this->global->fn->getFileIcon)($file) /* line 48 */ ?></span></span>
                <span class="wrapper-value filename"><span class="inner-wrapper-value"><?php echo ($this->global->fn->getFilename)($file) /* line 49 */ ?></span></span>
                <span class="wrapper-value size"><span class="inner-wrapper-value"><?php echo LR\Filters::escapeHtmlText(($this->global->fn->getHumanFileSize)($file)) /* line 50 */ ?></span></span>
                <span class="wrapper-value time"><span class="inner-wrapper-value"><?php echo LR\Filters::escapeHtmlText(date('d.m.Y H:i', $file->getMTime())) /* line 51 */ ?></span></span>
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

    <div class="dropzone-bg"></div>
    <form action="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getDropzoneUrl)())) /* line 68 */ ?>" class="dropzone-box">
        <i class="fas fa-times"></i>
        <div class="dz-message needsclick">
            <button type="button" class="dz-button">Drop files here or click to upload.</button>
        </div>
    </form>

    <div class="new-folder-bg"></div>
    <div class="new-folder-box">
        <form action="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl(($this->global->fn->getNewDirUrl)())) /* line 77 */ ?>" method="post">
            <i class="fas fa-times"></i>
            <input type="text" name="folderName" class="form-control" placeholder="Name of folder">
            <input type="submit" value="Create" class="btn">
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
			foreach (array_intersect_key(['breadCrumb' => '11', 'dir' => '32', 'file' => '45'], $this->params) as $_v => $_l) {
				trigger_error("Variable \$$_v overwritten in foreach on line $_l");
			}
		}
		
	}

}
