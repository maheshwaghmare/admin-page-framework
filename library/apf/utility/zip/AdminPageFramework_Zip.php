<?php 
/**
	Admin Page Framework v3.7.10b08 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2016, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_Zip {
    public $sSource;
    public $sDestination;
    public $aCallbacks = array('file_name' => null, 'file_contents' => null, 'directory_name' => null,);
    public $aOptions = array('include_directory' => false, 'additional_source_directories' => array(),);
    public function __construct($sSource, $sDestination, $abOptions = false, array $aCallbacks = array()) {
        $this->sSource = $sSource;
        $this->sDestination = $sDestination;
        $this->aOptions = $this->_getFormattedOptions($abOptions);
        $this->aCallbacks = $aCallbacks + $this->aCallbacks;
    }
    private function _getFormattedOptions($abOptions) {
        $_aOptions = is_array($abOptions) ? $abOptions : array('include_directory' => $abOptions,);
        return $_aOptions + $this->aOptions;
    }
    public function compress() {
        if (!$this->isFeasible($this->sSource)) {
            return false;
        }
        if (file_exists($this->sDestination)) {
            unlink($this->sDestination);
        }
        $_oZip = new ZipArchive();
        if (!$_oZip->open($this->sDestination, ZIPARCHIVE::CREATE)) {
            return false;
        }
        $this->sSource = $this->_getSanitizedSourcePath($this->sSource);
        $_aMethods = array('unknown' => '_replyToReturnFalse', 'directory' => '_replyToCompressDirectory', 'file' => '_replyToCompressFile',);
        $_sMethodName = $_aMethods[$this->_getSourceType($this->sSource) ];
        return call_user_func_array(array($this, $_sMethodName), array($_oZip, $this->sSource, $this->aCallbacks, $this->aOptions['include_directory'], $this->aOptions['additional_source_directories'],));
    }
    private function _getSanitizedSourcePath($sPath) {
        return str_replace('\\', '/', realpath($sPath));
    }
    public function _replyToCompressDirectory(ZipArchive $oZip, $sSourceDirPath, array $aCallbacks = array(), $bIncludeDir = false, array $aAdditionalSourceDirs = array()) {
        $_sArchiveRootDirName = '';
        if ($bIncludeDir) {
            $_sArchiveRootDirName = $this->_getMainDirectoryName($sSourceDirPath);
            $this->_addEmptyDir($oZip, $_sArchiveRootDirName, $aCallbacks['directory_name']);
        }
        array_unshift($aAdditionalSourceDirs, $sSourceDirPath);
        $_aSourceDirPaths = array_unique($aAdditionalSourceDirs);
        $this->_addArchiveItems($oZip, $_aSourceDirPaths, $aCallbacks, $_sArchiveRootDirName);
        return $oZip->close();
    }
    private function _addArchiveItems($oZip, $aSourceDirPaths, $aCallbacks, $sRootDirName = '') {
        $sRootDirName = $sRootDirName ? rtrim($sRootDirName, '/') . '/' : '';
        foreach ($aSourceDirPaths as $_isIndexOrRelativeDirPath => $_sSourceDirPath) {
            $_sSourceDirPath = $this->_getSanitizedSourcePath($_sSourceDirPath);
            $_sInsideDirPrefix = is_integer($_isIndexOrRelativeDirPath) ? '' : $_isIndexOrRelativeDirPath;
            if ($_sInsideDirPrefix) {
                $this->_addRelativeDir($oZip, $_sInsideDirPrefix, $aCallbacks['directory_name']);
            }
            $_oFilesIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($_sSourceDirPath), RecursiveIteratorIterator::SELF_FIRST);
            foreach ($_oFilesIterator as $_sIterationItem) {
                $this->_addArchiveItem($oZip, $_sSourceDirPath, $_sIterationItem, $aCallbacks, $sRootDirName . $_sInsideDirPrefix);
            }
        }
    }
    private function _addRelativeDir($oZip, $sRelativeDirPath, $oCallable) {
        $sRelativeDirPath = str_replace('\\', '/', $sRelativeDirPath);
        $_aPathPartsParse = array_filter(explode('/', $sRelativeDirPath));
        $_aDirPath = array();
        foreach ($_aPathPartsParse as $_sDirName) {
            $_aDirPath[] = $_sDirName;
            $this->_addEmptyDir($oZip, implode('/', $_aDirPath), $oCallable);
        }
    }
    private function _addArchiveItem(ZipArchive $oZip, $sSource, $_sIterationItem, array $aCallbacks, $sInsidePathPrefix = '') {
        $_sIterationItem = str_replace('\\', '/', $_sIterationItem);
        $sInsidePathPrefix = rtrim($sInsidePathPrefix, '/') . '/';
        if (in_array(substr($_sIterationItem, strrpos($_sIterationItem, '/') + 1), array('.', '..'))) {
            return;
        }
        $_sIterationItem = realpath($_sIterationItem);
        $_sIterationItem = str_replace('\\', '/', $_sIterationItem);
        if (true === is_dir($_sIterationItem)) {
            $this->_addEmptyDir($oZip, $sInsidePathPrefix . str_replace($sSource . '/', '', $_sIterationItem . '/'), $aCallbacks['directory_name']);
        } else if (true === is_file($_sIterationItem)) {
            $this->_addFromString($oZip, $sInsidePathPrefix . str_replace($sSource . '/', '', $_sIterationItem), file_get_contents($_sIterationItem), $aCallbacks);
        }
    }
    private function _getMainDirectoryName($sSource) {
        $_aPathParts = explode("/", $sSource);
        return $_aPathParts[count($_aPathParts) - 1];
    }
    public function _replyToCompressFile(ZipArchive $oZip, $sSourceFilePath, $aCallbacks = null) {
        $this->_addFromString($oZip, basename($sSourceFilePath), file_get_contents($sSourceFilePath), $aCallbacks);
        return $oZip->close();
    }
    private function _getSourceType($sSource) {
        if (true === is_dir($sSource)) {
            return 'directory';
        }
        if (true === is_file($sSource)) {
            return 'file';
        }
        return 'unknown';
    }
    private function isFeasible($sSource) {
        if (!extension_loaded('zip')) {
            return false;
        }
        return file_exists($sSource);
    }
    public function _replyToReturnFalse() {
        return false;
    }
    private function _addEmptyDir(ZipArchive $oZip, $sInsidePath, $oCallable) {
        $sInsidePath = $this->_getFilteredArchivePath($sInsidePath, $oCallable);
        if (!strlen($sInsidePath)) {
            return;
        }
        $oZip->addEmptyDir($sInsidePath);
    }
    private function _addFromString(ZipArchive $oZip, $sInsidePath, $sSourceContents = '', array $aCallbacks = array()) {
        $sInsidePath = $this->_getFilteredArchivePath($sInsidePath, $aCallbacks['file_name']);
        if (!strlen($sInsidePath)) {
            return;
        }
        $oZip->addFromString($sInsidePath, is_callable($aCallbacks['file_contents']) ? call_user_func_array($aCallbacks['file_contents'], array($sSourceContents, $sInsidePath)) : $sSourceContents);
    }
    private function _getFilteredArchivePath($sArchivePath, $oCallable) {
        return is_callable($oCallable) ? call_user_func_array($oCallable, array($sArchivePath,)) : $sArchivePath;
    }
}